<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoriaModel;
use App\Models\ProfissionalModel;
use App\Models\UsuarioModel;
use App\Models\ContatoLinkModel;
use App\Services\LocalizacaoService;

class ProfissionalController extends BaseController
{
    public function cadastrar()
    {
        $categoriaModel = new CategoriaModel();

        // Busca todas as categorias para preencher o formulário
        $data['categorias'] = $categoriaModel->findAll();

        return view('profissional/cadastrar', $data);
    }

    /**
     * Processa o cadastro inicial do profissional (criação de conta + perfil + whatsapp)
     */
    public function criar()
    {
        // 1. Regras de Validação
        $regras = [
            'nome' => [
                'rules'  => 'required|min_length[3]',
                'errors' => [
                    'required'   => 'O campo Nome Completo é obrigatório.',
                    'min_length' => 'O nome deve ter pelo menos 3 caracteres.'
                ]
            ],
            'cpf' => [
                'rules'  => 'required|is_unique[usuario.cpf]',
                'errors' => [
                    'required'  => 'O campo CPF é obrigatório.',
                    'is_unique' => 'Este CPF já está cadastrado no sistema.'
                ]
            ],
            'email' => [
                'rules'  => 'required|valid_email|is_unique[usuario.email]',
                'errors' => [
                    'required'    => 'O campo E-mail é obrigatório.',
                    'valid_email' => 'Informe um e-mail válido.',
                    'is_unique'   => 'Este e-mail já está cadastrado no sistema.'
                ]
            ],
            'telefone' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'O campo Telefone é obrigatório.'
                ]
            ],
            'senha' => [
                'rules'  => 'required|min_length[6]',
                'errors' => [
                    'required'   => 'O campo Senha é obrigatório.',
                    'min_length' => 'A senha deve ter no mínimo 6 caracteres.'
                ]
            ],
            'confirma_senha' => [
                'rules'  => 'required|matches[senha]',
                'errors' => [
                    'required' => 'A confirmação de senha é obrigatória.',
                    'matches'  => 'As senhas informadas não coincidem.'
                ]
            ],
            'cep' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'O campo CEP é obrigatório.'
                ]
            ],
            'bairro' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'O campo Bairro é obrigatório.'
                ]
            ],
            'cidade' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'O campo Cidade é obrigatório.'
                ]
            ],
            'estado' => [
                'rules'  => 'required|exact_length[2]',
                'errors' => [
                    'required'     => 'O campo UF é obrigatório.',
                    'exact_length' => 'Informe a sigla do estado com 2 letras.'
                ]
            ],
            'descricaoPerfil' => [
                'rules'  => 'required|min_length[20]',
                'errors' => [
                    'required'   => 'A apresentação dos seus serviços é obrigatória.',
                    'min_length' => 'A apresentação deve conter no mínimo 20 caracteres.'
                ]
            ],
            'raio_atendimento_km' => [
                'rules'  => 'required|integer|greater_than[0]',
                'errors' => [
                    'required'     => 'O campo Raio de Atendimento é obrigatório.',
                    'integer'      => 'Informe um valor numérico inteiro para o raio de atendimento.',
                    'greater_than' => 'O raio de atendimento deve ser maior que zero.'
                ]
            ],
            'categorias' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Selecione pelo menos uma categoria de atuação.'
                ]
            ]
        ];

        if (!$this->validate($regras)) {
            return redirect()->back()->withInput()->with('erro', $this->validator->listErrors());
        }

        // Instancia as Models e o Service necessários
        $usuarioModel       = new UsuarioModel();
        $profissionalModel  = new ProfissionalModel();
        $contatoLinkModel   = new ContatoLinkModel();
        $localizacaoService = new LocalizacaoService();

        // Sanitização dos dados recebidos do formulário
        $cpfLimpo           = preg_replace('/[^0-9]/', '', $this->request->getPost('cpf'));
        $cepLimpo           = preg_replace('/[^0-9]/', '', $this->request->getPost('cep'));
        $bairro             = trim($this->request->getPost('bairro'));
        $cidade             = trim($this->request->getPost('cidade'));
        $estado             = strtoupper(trim($this->request->getPost('estado')));
        $telefone           = $this->request->getPost('telefone');
        $apenasMesmaCidade  = $this->request->getPost('apenas_mesma_cidade') ? 1 : 0;

        // 🛡️ SEGURANÇA: Se os campos de endereço vieram vazios, resolvemos via backend pelo ViaCEP
        if (empty($bairro) || empty($cidade) || empty($estado)) {
            $dadosViaCep = $localizacaoService->consultarCep($cepLimpo);
            if ($dadosViaCep) {
                $bairro = empty($bairro) ? ($dadosViaCep['bairro'] ?? '') : $bairro;
                $cidade = empty($cidade) ? ($dadosViaCep['cidade'] ?? '') : $cidade;
                $estado = empty($estado) ? ($dadosViaCep['estado'] ?? '') : $estado;
            }
        }

        // Obtém as coordenadas geográficas (latitude e longitude) pelo OpenStreetMap
        $coordenadas = $localizacaoService->obterCoordenadas($bairro, $cidade, $estado);
        $latitude    = $coordenadas['latitude'] ?? null;
        $longitude   = $coordenadas['longitude'] ?? null;

        // Insere a conta principal do Usuário
        $dadosUsuario = [
            'nome'   => $this->request->getPost('nome'),
            'cpf'    => $cpfLimpo,
            'email'  => $this->request->getPost('email'),
            'senha'  => $this->request->getPost('senha'),
            'cep'    => $cepLimpo,
            'bairro' => $bairro,
            'cidade' => $cidade,
            'estado' => $estado,
            'ativo'  => 1
        ];

        $usuarioId = $usuarioModel->insert($dadosUsuario);

        if (!$usuarioId) {
            return redirect()->back()->withInput()->with('erro', 'Ocorreu um erro ao cadastrar o usuário. Tente novamente.');
        }

        // Grava o WhatsApp/Telefone principal na tabela 'contato_links' como 'whatsapp'
        if (!empty($telefone)) {
            $contatoLinkModel->insert([
                'usuario_id'  => $usuarioId,
                'tipoContato' => 'whatsapp',
                'contato'     => preg_replace('/[^0-9]/', '', $telefone)
            ]);
        }

        // Insere os dados específicos do Perfil Profissional
        $dadosProfissional = [
            'usuario_id'          => $usuarioId,
            'descricaoPerfil'     => $this->request->getPost('descricaoPerfil'),
            'raio_atendimento_km' => $this->request->getPost('raio_atendimento_km'),
            'cep'                 => $cepLimpo,
            'bairro'              => $bairro,
            'cidade'              => $cidade,
            'estado'              => $estado,
            'apenas_mesma_cidade' => $apenasMesmaCidade,
            'latitude'            => $latitude,
            'longitude'           => $longitude,
            'status'              => 'em_analise'
        ];
        $profissionalModel->insert($dadosProfissional);

        // Vincula as categorias selecionadas na tabela pivô
        $categorias = $this->request->getPost('categorias');
        $profissionalModel->salvarCategorias($usuarioId, (array) $categorias);

        // 3. Inicialização de Sessão (Login Automático)
        session()->set([
            'id'           => $usuarioId,
            'nome'         => $dadosUsuario['nome'],
            'email'        => $dadosUsuario['email'],
            'tipo_perfil'  => 'Profissional',
            'perfil_ativo' => 'Cliente',
            'logged_in'    => true
        ]);

        return redirect()->to('cliente/dashboard');
    }

    /**
     * Exibe o formulário para o cliente ativar o perfil profissional
     */
    public function ativarPerfil()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('login')->with('erro', 'Faça login para acessar esta página.');
        }

        $usuarioId = session()->get('id');

        $usuarioModel   = new UsuarioModel();
        $categoriaModel = new CategoriaModel();

        $data = [
            'usuario'    => $usuarioModel->find($usuarioId),
            'categorias' => $categoriaModel->findAll()
        ];

        return view('profissional/ativar_perfil', $data);
    }

    /**
     * Processa a ativação do perfil profissional (Cliente existente virando Profissional)
     */
    public function processarAtivacao()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('login')->with('erro', 'Sua sessão expirou. Faça login novamente.');
        }

        $usuarioId = session()->get('id');

        $regras = [
            'descricaoPerfil' => [
                'rules'  => 'required|min_length[20]',
                'errors' => [
                    'required'   => 'Por favor, informe uma descrição para o seu perfil.',
                    'min_length' => 'A descrição deve conter no mínimo 20 caracteres.'
                ]
            ],
            'raio_atendimento_km' => [
                'rules'  => 'required|integer|greater_than[0]',
                'errors' => [
                    'required'     => 'O campo Raio de Atendimento é obrigatório.',
                    'integer'      => 'Informe um valor numérico inteiro para o raio de atendimento.',
                    'greater_than' => 'O raio de atendimento deve ser maior que zero.'
                ]
            ],
            'cep' => [
                'rules'  => 'required',
                'errors' => ['required' => 'O campo CEP é obrigatório.']
            ],
            'bairro' => [
                'rules'  => 'required',
                'errors' => ['required' => 'O campo Bairro é obrigatório.']
            ],
            'cidade' => [
                'rules'  => 'required',
                'errors' => ['required' => 'O campo Cidade é obrigatório.']
            ],
            'estado' => [
                'rules'  => 'required|exact_length[2]',
                'errors' => ['required' => 'O campo UF é obrigatório.']
            ],
            'categorias' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Selecione pelo menos uma categoria de atuação.'
                ]
            ]
        ];

        if (!$this->validate($regras)) {
            return redirect()->back()->withInput()->with('erro', $this->validator->listErrors());
        }

        $descricaoPerfil   = $this->request->getPost('descricaoPerfil');
        $raioAtendimentoKm = $this->request->getPost('raio_atendimento_km');
        $cepLimpo          = preg_replace('/[^0-9]/', '', $this->request->getPost('cep'));
        $bairro            = trim($this->request->getPost('bairro'));
        $cidade            = trim($this->request->getPost('cidade'));
        $estado            = strtoupper(trim($this->request->getPost('estado')));
        $apenasMesmaCidade = $this->request->getPost('apenas_mesma_cidade') ? 1 : 0;
        $categorias        = $this->request->getPost('categorias');

        $localizacaoService = new LocalizacaoService();

        // 🛡️ SEGURANÇA: Se por acaso os campos vieram vazios, resolvemos via backend pelo ViaCEP
        if (empty($bairro) || empty($cidade) || empty($estado)) {
            $dadosViaCep = $localizacaoService->consultarCep($cepLimpo);
            if ($dadosViaCep) {
                $bairro = empty($bairro) ? ($dadosViaCep['bairro'] ?? '') : $bairro;
                $cidade = empty($cidade) ? ($dadosViaCep['cidade'] ?? '') : $cidade;
                $estado = empty($estado) ? ($dadosViaCep['estado'] ?? '') : $estado;
            }
        }

        // Obtém as coordenadas geográficas (latitude e longitude) pelo OpenStreetMap
        $coordenadas = $localizacaoService->obterCoordenadas($bairro, $cidade, $estado);
        $latitude    = $coordenadas['latitude'] ?? null;
        $longitude   = $coordenadas['longitude'] ?? null;

        $profissionalModel = new ProfissionalModel();
        $usuarioModel      = new UsuarioModel();

        // Atualiza os dados de endereço base na tabela 'usuario'
        $usuarioModel->update($usuarioId, [
            'cep'    => $cepLimpo,
            'bairro' => $bairro,
            'cidade' => $cidade,
            'estado' => $estado
        ]);

        $dadosProfissional = [
            'usuario_id'          => $usuarioId,
            'descricaoPerfil'     => $descricaoPerfil,
            'raio_atendimento_km' => $raioAtendimentoKm,
            'cep'                 => $cepLimpo,
            'bairro'              => $bairro,
            'cidade'              => $cidade,
            'estado'              => $estado,
            'apenas_mesma_cidade' => $apenasMesmaCidade,
            'latitude'            => $latitude,
            'longitude'           => $longitude,
            'status'              => 'em_analise'
        ];

        $profissionalExistente = $profissionalModel->where('usuario_id', $usuarioId)->first();

        if ($profissionalExistente) {
            $profissionalModel->where('usuario_id', $usuarioId)->set($dadosProfissional)->update();
        } else {
            $profissionalModel->insert($dadosProfissional);
        }

        // Salva categorias usando o método da Model
        $profissionalModel->salvarCategorias($usuarioId, (array) $categorias);

        session()->set('tipo_perfil', 'Profissional');
        session()->set('perfil_ativo', 'Cliente');

        return redirect()->to('cliente/dashboard');
    }

    /**
     * Exibe o painel/dashboard do profissional
     */
    public function dashboard()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('login')->with('erro', 'Sua sessão expirou. Faça login novamente.');
        }

        $usuarioId = session()->get('id');

        $profissionalModel = new ProfissionalModel();
        $profissional = $profissionalModel->getProfissionalAtivo($usuarioId);

        if (empty($profissional)) {
            return redirect()->to('profissional/ativarPerfil')
                ->with('erro', 'Você precisa ativar seu perfil profissional para acessar este painel.');
        }

        session()->set('perfil_ativo', 'Profissional');

        return view('profissional/dashboard');
    }

    /**
     * Exibe o formulário de edição do perfil profissional
     */
    public function editarPerfil()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('login');
        }

        $usuarioId = session()->get('id');
        $profissionalModel = new ProfissionalModel();
        $contatoLinkModel   = new ContatoLinkModel();

        $profissional = $profissionalModel->where('usuario_id', $usuarioId)->first();
        $contatosBanco = $contatoLinkModel->where('usuario_id', $usuarioId)->findAll();

        $links = [];
        foreach ($contatosBanco as $contato) {
            $tipo = is_object($contato) 
                ? ($contato->tipoContato ?? $contato->tipo ?? null) 
                : ($contato['tipoContato'] ?? $contato['tipo'] ?? null);

            $valor = is_object($contato) 
                ? ($contato->contato ?? $contato->link_ou_numero ?? null) 
                : ($contato['contato'] ?? $contato['link_ou_numero'] ?? null);

            if ($tipo) {
                // Mapeia o tipo 'telefone' ou 'whatsapp' para a chave 'whatsapp' da view
                if ($tipo === 'telefone' || $tipo === 'whatsapp') {
                    $links['whatsapp'] = $valor;
                }
                $links[$tipo] = $valor;
            }
        }

        $data = [
            'profissional' => $profissional,
            'links'        => $links
        ];

        return view('profissional/editar_perfil', $data);
    }


    /**
     * Processa a atualização do perfil profissional e links
     */
    public function atualizarPerfil()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('login');
        }

        $usuarioId = session()->get('id');
        $profissionalModel = new ProfissionalModel();
        $contatoLinkModel   = new ContatoLinkModel();
        $localizacaoService = new LocalizacaoService();

        // 1. Regras de validação
        $regras = [
            'descricao'    => 'required|min_length[10]',
            'raio_atuacao' => 'required|integer|greater_than[0]',
            'cep'          => 'required',
        ];

        if (!$this->validate($regras)) {
            return redirect()->back()->withInput()->with('erro', $this->validator->listErrors());
        }

        $cepLimpo  = preg_replace('/[^0-9]/', '', $this->request->getPost('cep'));
        $apenasMesmaCidade = $this->request->getPost('apenas_mesma_cidade') ? 1 : 0;

        // 🛡️ ABORDAGEM BLINDADA: Buscamos sempre os dados oficiais do endereço diretamente pelo CEP no backend
        $bairro = trim($this->request->getPost('bairro'));
        $cidade = trim($this->request->getPost('cidade'));
        $estado = strtoupper(trim($this->request->getPost('estado')));

        if (strlen($cepLimpo) === 8) {
            $dadosViaCep = $localizacaoService->consultarCep($cepLimpo);
            if ($dadosViaCep && !isset($dadosViaCep['erro'])) {
                $bairro = !empty($dadosViaCep['bairro']) ? $dadosViaCep['bairro'] : $bairro;
                $cidade = !empty($dadosViaCep['cidade']) ? $dadosViaCep['cidade'] : $cidade;
                $estado = !empty($dadosViaCep['estado']) ? $dadosViaCep['estado'] : $estado;
            }
        }

        // Fallback caso venha vazio
        if (empty($bairro) || empty($cidade) || empty($estado)) {
            $usuarioModelTemp = new UsuarioModel();
            $usuarioTemp = $usuarioModelTemp->find($usuarioId);
            if ($usuarioTemp) {
                $bairro = empty($bairro) ? ($usuarioTemp['bairro'] ?? '') : $bairro;
                $cidade = empty($cidade) ? ($usuarioTemp['cidade'] ?? '') : $cidade;
                $estado = empty($estado) ? ($usuarioTemp['estado'] ?? '') : $estado;
            }
        }

        // Busca o registro atual do profissional para comparar se o CEP mudou
        $profissionalAtual = $profissionalModel->where('usuario_id', $usuarioId)->first();
        $cepSalvoAntigo    = $profissionalAtual['cep'] ?? '';

        // Calcula as coordenadas obrigatoriamente se o CEP mudou, se estiverem vazias ou se for um cadastro novo
        $latitude  = $profissionalAtual['latitude'] ?? null;
        $longitude = $profissionalAtual['longitude'] ?? null;

        if (empty($latitude) || empty($longitude) || $cepLimpo !== $cepSalvoAntigo) {
            $coordenadas = $localizacaoService->obterCoordenadas($bairro, $cidade, $estado);
            
            if (!empty($coordenadas['latitude']) && !empty($coordenadas['longitude'])) {
                $latitude  = $coordenadas['latitude'];
                $longitude = $coordenadas['longitude'];
            }
        }

        // Monta os dados para salvar
        $dadosProfissional = [
            'usuario_id'          => $usuarioId,
            'descricao'           => $this->request->getPost('descricao'),
            'descricaoPerfil'     => $this->request->getPost('descricao'),
            'raio_atuacao'        => $this->request->getPost('raio_atuacao'),
            'raio_atendimento_km' => $this->request->getPost('raio_atuacao'),
            'cep'                 => $cepLimpo,
            'bairro'              => $bairro,
            'cidade'              => $cidade,
            'estado'              => $estado,
            'apenas_mesma_cidade' => $apenasMesmaCidade,
            'latitude'            => $latitude,
            'longitude'           => $longitude,
        ];

        if ($profissionalAtual) {
            $profissionalModel->where('usuario_id', $usuarioId)->set($dadosProfissional)->update();
        } else {
            $dadosProfissional['status'] = 'pendente';
            $profissionalModel->insert($dadosProfissional);
        }

        // Atualiza também na tabela 'usuario'
        $usuarioModel = new UsuarioModel();
        $usuarioModel->update($usuarioId, [
            'cep'    => $cepLimpo,
            'bairro' => $bairro,
            'cidade' => $cidade,
            'estado' => $estado
        ]);

        // 2. Salva os links e redes sociais na 'contato_links'
        $redes = ['whatsapp', 'instagram', 'facebook', 'linkedin'];

        foreach ($redes as $tipo) {
            $valor = trim((string) $this->request->getPost($tipo));
            if (!empty($valor)) {
                if ($tipo === 'whatsapp') {
                    $valor = preg_replace('/[^0-9]/', '', $valor);
                }

                $existe = $contatoLinkModel->where('usuario_id', $usuarioId)->where('tipoContato', $tipo)->first();
                if ($existe) {
                    $contatoLinkModel->where('usuario_id', $usuarioId)
                                     ->where('tipoContato', $tipo)
                                     ->set(['contato' => $valor])->update();
                } else {
                    $contatoLinkModel->insert([
                        'usuario_id'  => $usuarioId,
                        'tipoContato' => $tipo,
                        'contato'     => $valor
                    ]);
                }
            }
        }

        return redirect()->to(site_url('profissional/dashboard'))->with('sucesso', 'Perfil profissional atualizado com sucesso!');
    }

    /**
     * Endpoint API consumido via AJAX pela view para autocompletar o CEP
     */
    public function consultarCepApi()
    {
        $cep = $this->request->getGet('cep');

        if (empty($cep)) {
            return $this->response->setJSON(['erro' => true]);
        }

        $localizacaoService = new LocalizacaoService();
        $resultado = $localizacaoService->consultarCep($cep);

        if (!$resultado) {
            return $this->response->setJSON(['erro' => true]);
        }

        return $this->response->setJSON($resultado);
    }
}