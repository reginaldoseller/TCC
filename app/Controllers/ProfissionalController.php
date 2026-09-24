<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoriaModel;
use App\Models\ProfissionalModel;
use App\Models\UsuarioModel;
use App\Models\ContatoLinkModel;

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
     * Processa o cadastro inicial do profissional (criação de conta + perfil)
     */
    /**
     * Processa o cadastro inicial do profissional (criação de conta + perfil + telefone)
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

        // Instancia as Models necessárias
        $usuarioModel      = new UsuarioModel();
        $profissionalModel = new ProfissionalModel();
        $contatoLinkModel  = new ContatoLinkModel();

        // Sanitização dos dados recebidos do formulário
        $cpfLimpo      = preg_replace('/[^0-9]/', '', $this->request->getPost('cpf'));
        $cepLimpo      = preg_replace('/[^0-9]/', '', $this->request->getPost('cep'));
        $telefone      = $this->request->getPost('telefone');

        // 2. Transação na Base de Dados
        $db = \Config\Database::connect();
        $db->transStart();

        // Insere a conta principal do Usuário
        $dadosUsuario = [
            'nome'   => $this->request->getPost('nome'),
            'cpf'    => $cpfLimpo,
            'email'  => $this->request->getPost('email'),
            'senha'  => $this->request->getPost('senha'), // O Callback/Observer no UsuarioModel trata o hash seguro
            'cep'    => $cepLimpo,
            'bairro' => $this->request->getPost('bairro'),
            'cidade' => $this->request->getPost('cidade'),
            'estado' => strtoupper($this->request->getPost('estado')),
            'ativo'  => 1
        ];

        $usuarioModel->insert($dadosUsuario);
        $usuarioId = $usuarioModel->getInsertID();

        // Grava o telefone na tabela 'contato_links'
        if (!empty($telefone)) {
            $contatoLinkModel->salvarContato($usuarioId, 'telefone', $telefone);
        }

        // Insere os dados específicos do Perfil Profissional
        $dadosProfissional = [
            'usuario_id'          => $usuarioId,
            'descricaoPerfil'     => $this->request->getPost('descricaoPerfil'),
            'raio_atendimento_km' => $this->request->getPost('raio_atendimento_km'),
            'status'              => 'em_analise'
        ];
        $profissionalModel->insert($dadosProfissional);

        // Vincula as categorias selecionadas na tabela pivô
        $categorias = $this->request->getPost('categorias');
        $profissionalModel->salvarCategorias($usuarioId, (array) $categorias);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('erro', 'Ocorreu um erro ao realizar o cadastro. Tente novamente.');
        }

        // 3. Inicialização de Sessão (Login Automático)
        session()->set([
            'id'           => $usuarioId,
            'nome'         => $dadosUsuario['nome'],
            'email'        => $dadosUsuario['email'],
            'tipo_perfil'  => 'Profissional',
            'perfil_ativo' => 'Cliente', // Permanece como Cliente até a aprovação do Admin
            'logged_in'    => true
        ]);

        return redirect()->to('cliente/dashboard')->with('sucesso', 'Cadastro realizado com sucesso! Seu perfil profissional está em análise pela administração.');
    }

    /**
     * Exibe o formulário para o cliente ativar o perfil profissional
     */
    public function ativarPerfil()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('login')->with('erro', 'Faça login para acessar esta página.');
        }

        $categoriaModel = new CategoriaModel();
        $data['categorias'] = $categoriaModel->findAll();

        return view('profissional/ativar_perfil', $data);
    }

    /**
     * Processa a ativação do perfil profissional
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
        $categorias        = $this->request->getPost('categorias');

        $profissionalModel = new ProfissionalModel();

        $db = \Config\Database::connect();
        $db->transStart();

        $dados = [
            'usuario_id'          => $usuarioId,
            'descricaoPerfil'     => $descricaoPerfil,
            'raio_atendimento_km' => $raioAtendimentoKm,
            'status'              => 'em_analise'
        ];

        if ($profissionalModel->find($usuarioId)) {
            $profissionalModel->update($usuarioId, $dados);
        } else {
            $profissionalModel->insert($dados);
        }

        // Salva categorias usando o método da Model
        $profissionalModel->salvarCategorias($usuarioId, (array) $categorias);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('erro', 'Ocorreu um erro ao processar a ativação do perfil.');
        }

        // Mantém o perfil ativo como Cliente enquanto o Admin avalia a solicitação
        session()->set('tipo_perfil', 'Profissional');
        session()->set('perfil_ativo', 'Cliente');

        return redirect()->to('cliente/dashboard')->with('sucesso', 'Solicitação enviada! Seu perfil profissional está em análise pela administração.');
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

        // Se não encontrou registro ativo, bloqueia o acesso
        if (empty($profissional)) {
            return redirect()->to('profissional/ativarPerfil')
                ->with('erro', 'Você precisa ativar seu perfil profissional para acessar este painel.');
        }

        // Se possui registro ativo, garante que o perfil_ativo na sessão seja Profissional
        session()->set('perfil_ativo', 'Profissional');

        return view('profissional/dashboard');
    }
}