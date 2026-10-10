<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use App\Models\ProfissionalModel;
use App\Models\ContatoLinkModel;

class UsuarioController extends BaseController
{
    // =========================================================================
    // 1. EXIBIÇÃO DA TELA DE LOGIN
    // =========================================================================
    public function login()
    {
        return view('usuarios/login');
    }

    // =========================================================================
    // 2. EXIBIÇÃO DO FORMULÁRIO DE CADASTRO
    // ==========================================
    public function novo()
    {
        return view('usuarios/cadastrar');
    }

    // =========================================================================
    // 3. PROCESSAMENTO DO CADASTRO DE NOVO USUÁRIO
    // =========================================================================
    public function criar()
    {
        // Define as regras de validação para os campos obrigatórios e únicos do cadastro
        $regras = [
            'nome' => [
                'rules'  => 'required|min_length[3]',
                'errors' => [
                    'required'   => 'O nome completo é obrigatório.',
                    'min_length' => 'O nome deve ter pelo menos 3 caracteres.'
                ]
            ],
            'cpf' => [
                'rules'  => 'required|is_unique[usuario.cpf]',
                'errors' => [
                    'required'  => 'O CPF é obrigatório.',
                    'is_unique' => 'Este CPF já está cadastrado.'
                ]
            ],
            'email' => [
                'rules'  => 'required|valid_email|is_unique[usuario.email]',
                'errors' => [
                    'required'    => 'O e-mail é obrigatório.',
                    'valid_email' => 'Informe um e-mail válido.',
                    'is_unique'   => 'Este e-mail já está cadastrado no sistema.'
                ]
            ],
            'senha' => [
                'rules'  => 'required|min_length[6]',
                'errors' => [
                    'required'   => 'A senha é obrigatória.',
                    'min_length' => 'A senha deve ter no mínimo 6 caracteres.'
                ]
            ],
            'confirma_senha' => [
                'rules'  => 'required|matches[senha]',
                'errors' => [
                    'required' => 'A confirmação de senha é obrigatória.',
                    'matches'  => 'As senhas não conferem.'
                ]
            ]
        ];

        // Se a validação falhar, retorna à página anterior mantendo os inputs e exibindo o erro
        if (!$this->validate($regras)) {
            return redirect()->back()->withInput()->with('erro', $this->validator->listErrors());
        }

        $usuarioModel     = new UsuarioModel();
        $contatoLinkModel = new ContatoLinkModel();

        // Higieniza os campos removendo máscaras (deixa apenas números)
        $cpfLimpo      = preg_replace('/[^0-9]/', '', $this->request->getPost('cpf'));
        $cepLimpo      = preg_replace('/[^0-9]/', '', $this->request->getPost('cep'));
        $telefoneLimpo = preg_replace('/[^0-9]/', '', $this->request->getPost('telefone'));

        // Monta o array com os dados; a senha vai em texto puro pois o UsuarioModel aplica o hash no 'beforeInsert'
        $dados = [
            'nome'   => $this->request->getPost('nome'),
            'cpf'    => $cpfLimpo,
            'email'  => $this->request->getPost('email'),
            'senha'  => $this->request->getPost('senha'),
            'cidade' => $this->request->getPost('cidade'),
            'estado' => strtoupper($this->request->getPost('estado')),
            'bairro' => $this->request->getPost('bairro'),
            'cep'    => $cepLimpo,
        ];

        // Utiliza transação de banco de dados para garantir consistência no registro e contatos
        $db = \Config\Database::connect();
        $db->transStart();

        $usuarioModel->insert($dados);
        $novoId = $usuarioModel->getInsertID();

        // Se o telefone foi informado, salva na tabela de vínculos de contatos
        if (!empty($telefoneLimpo)) {
            $contatoLinkModel->salvarContato($novoId, 'telefone', $telefoneLimpo);
        }

        $db->transComplete();

        // Se a transação for bem-sucedida, inicializa a sessão do novo usuário e redireciona ao painel
        if ($db->transStatus() !== false) {
            session()->set([
                'id'           => $novoId,
                'nome'         => $dados['nome'],
                'email'        => $dados['email'],
                'tipo_perfil'  => 'Cliente',
                'perfil_ativo' => 'Cliente',
                'is_admin'     => false,
                'logged_in'    => true,
            ]);

            return redirect()->to('cliente/dashboard')->with('sucesso', 'Bem-vindo! Seu cadastro foi realizado com sucesso.');
        } else {
            return redirect()->back()->withInput()->with('erro', 'Erro ao realizar o cadastro. Verifique os dados e tente novamente.');
        }
    }

    // =========================================================================
    // 4. AUTENTICAÇÃO E LOGIN DE USUÁRIOS
    // =========================================================================
    public function autenticar()
    {
        $email = $this->request->getPost('email');
        $senha = $this->request->getPost('senha');

        $usuarioModel = new UsuarioModel();
        // Utiliza o método do model que lida com a verificação de credenciais e hash
        $usuario = $usuarioModel->verificarCredenciais($email, $senha);

        if ($usuario) {
            // Validação 4.1: Verifica se a conta está banida permanentemente
            if ($usuario['status'] === 'banido') {
                $mensagemBanido = 'Sua conta foi permanentemente suspensa por descumprimento dos termos de uso.';
                if (!empty($usuario['motivo_bloqueio'])) {
                    $mensagemBanido .= ' Motivo: ' . $usuario['motivo_bloqueio'];
                }
                return redirect()->back()->withInput()->with('erro', $mensagemBanido);
            }

            // Validação 4.2: Verifica se a conta está suspensa temporariamente
            if ($usuario['status'] === 'suspenso') {
                $agora = date('Y-m-d H:i:s');
                if (!empty($usuario['bloqueado_ate']) && $usuario['bloqueado_ate'] > $agora) {
                    $dataLiberacao = date('d/m/Y \à\s H:i', strtotime($usuario['bloqueado_ate']));
                    $mensagemSuspenso = "Sua conta está suspensa temporariamente até {$dataLiberacao}.";
                    if (!empty($usuario['motivo_bloqueio'])) {
                        $mensagemSuspenso .= " Motivo: " . $usuario['motivo_bloqueio'];
                    }
                    return redirect()->back()->withInput()->with('erro', $mensagemSuspenso);
                } else {
                    // Se o tempo expirou, reativa a conta automaticamente
                    $usuarioModel->update($usuario['id'], [
                        'status'          => 'ativo',
                        'motivo_bloqueio' => null,
                        'bloqueado_ate'   => null
                    ]);
                    $usuario['status'] = 'ativo';
                }
            }

            // Verifica se possui perfil profissional cadastrado e aprovado
            $profissionalModel = new ProfissionalModel();
            $dadosProfissional = $profissionalModel->where('usuario_id', $usuario['id'])->first();

            $ehProfissional = !empty($dadosProfissional);
            $profissionalAprovado = $ehProfissional && isset($dadosProfissional['status']) && $dadosProfissional['status'] === 'ativo';

            // Verifica se possui privilégios de administrador
            $ehAdmin = $usuarioModel->ehAdmin($usuario['id']);

            // Define o tipo de perfil inicial com base nas permissões
            $tipoPerfil = 'Cliente';
            if ($ehAdmin) {
                $tipoPerfil = 'Administrador';
            } elseif ($profissionalAprovado) {
                $tipoPerfil = 'Profissional';
            }

            // Grava os dados essenciais na sessão
            session()->set([
                'id'           => $usuario['id'],
                'nome'         => $usuario['nome'],
                'email'        => $usuario['email'],
                'logged_in'    => true,
                'tipo_perfil'  => $tipoPerfil,
                'perfil_ativo' => $tipoPerfil,
                'is_admin'     => $ehAdmin
            ]);

            // Redireciona de acordo com o perfil principal do usuário
            if ($ehAdmin) {
                return redirect()->to('admin/dashboard')->with('sucesso', 'Login administrativo realizado com sucesso!');
            }

            if ($profissionalAprovado) {
                return redirect()->to('profissional/dashboard');
            }

            if ($ehProfissional && !$profissionalAprovado) {
                return redirect()->to('cliente/dashboard');
            }

            return redirect()->to('cliente/dashboard');
        }

        return redirect()->back()->withInput()->with('erro', 'E-mail ou senha inválidos.');
    }

    // =========================================================================
    // 5. PAINEL DO CLIENTE E CONTROLE DE SESSÃO
    // =========================================================================
    public function dashboard()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('login')->with('erro', 'Faça login para acessar o painel.');
        }

        return view('cliente/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/')->with('sucesso', 'Você saiu da sua conta com sucesso.');
    }

    // =========================================================================
    // 6. ALTERNÂNCIA DE PERFIS (ADMIN, CLIENTE, PROFISSIONAL)
    // =========================================================================
    public function mudarParaCliente()
    {
        session()->set('perfil_ativo', 'Cliente');
        return redirect()->to('cliente/dashboard');
    }

    public function mudarParaProfissional()
    {
        $usuarioId = session()->get('id');

        $profissionalModel = new ProfissionalModel();
        $profissional = $profissionalModel->where('usuario_id', $usuarioId)->first();

        if (!empty($profissional) && $profissional['status'] === 'ativo') {
            session()->set('perfil_ativo', 'Profissional');
            return redirect()->to('profissional/dashboard');
        }

        if (!empty($profissional) && $profissional['status'] === 'em_analise') {
            return redirect()->back()->with('aviso', 'Sua solicitação ainda está em análise.');
        }

        return redirect()->to('profissional/ativarPerfil')->with('aviso', 'Preencha seus dados para se cadastrar como profissional.');
    }

    public function mudarParaAdmin()
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('cliente/dashboard')->with('erro', 'Você não possui permissão de administrador.');
        }

        session()->set('perfil_ativo', 'Administrador');
        return redirect()->to('admin/dashboard');
    }

    // =========================================================================
    // 7. FLUXO DE RECUPERAÇÃO E REDEFINIÇÃO DE SENHA (ESQUECI A SENHA)
    // =========================================================================
    public function esqueciSenha()
    {
        return view('usuarios/esqueci_senha');
    }

    public function processarEsqueciSenha()
    {
        $email = $this->request->getPost('email');

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->where('email', $email)->first();

        if (!$usuario) {
            return redirect()->back()->with('sucesso', 'Se o e-mail estiver cadastrado, você receberá o link para redefinição em instantes.');
        }

        $token = bin2hex(random_bytes(32));
        $expiracao = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $usuarioModel->update($usuario['id'], [
            'reset_token'      => $token,
            'reset_expires_at' => $expiracao,
        ]);

        $linkRedefinicao = base_url("redefinir-senha/{$token}");
        log_message('info', "Link de recuperação para {$email}: {$linkRedefinicao}");

        return redirect()->back()->with('sucesso', 'Se o e-mail estiver cadastrado, você receberá o link para redefinição em instantes.');
    }

    public function redefinirSenha($token = null)
    {
        if (empty($token)) {
            return redirect()->to('login')->with('erro', 'Token inválido ou ausente.');
        }

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->where('reset_token', $token)->first();

        if (!$usuario) {
            return redirect()->to('login')->with('erro', 'Token de redefinição inválido.');
        }

        $agora = date('Y-m-d H:i:s');
        if ($usuario['reset_expires_at'] < $agora) {
            return redirect()->to('esqueci-senha')->with('erro', 'Este link de redefinição expirou. Solicite um novo.');
        }

        return view('usuarios/redefinir_senha', ['token' => $token]);
    }

    public function salvarNovaSenha()
    {
        $token          = $this->request->getPost('token');
        $senha          = $this->request->getPost('senha');
        $confirmarSenha = $this->request->getPost('confirmar_senha');

        if ($senha !== $confirmarSenha) {
            return redirect()->back()->with('erro', 'As senhas não conferem.');
        }

        if (strlen($senha) < 6) {
            return redirect()->back()->with('erro', 'A nova senha deve ter no mínimo 6 caracteres.');
        }

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->where('reset_token', $token)->first();

        if (!$usuario || $usuario['reset_expires_at'] < date('Y-m-d H:i:s')) {
            return redirect()->to('login')->with('erro', 'Solicitação inválida ou expirada.');
        }

        $usuarioModel->update($usuario['id'], [
            'senha'            => $senha,
            'reset_token'      => null,
            'reset_expires_at' => null,
        ]);

        return redirect()->to('login')->with('sucesso', 'Sua senha foi alterada com sucesso! Faça login com as novas credenciais.');
    }

    // =========================================================================
    // 8. GERENCIAMENTO DE PERFIL DO USUÁRIO LOGADO (EXIBIÇÃO E EDIÇÃO)
    // =========================================================================
    public function meuPerfil()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('login')->with('erro', 'Faça login para acessar o seu perfil.');
        }

        $usuarioId = session()->get('id');
        $usuarioModel = new UsuarioModel();
        $contatoLinkModel = new ContatoLinkModel();

        $usuario = $usuarioModel->find($usuarioId);

        $contatoTelefone = $contatoLinkModel->where('usuario_id', $usuarioId)
                                           ->where('tipoContato', 'telefone')
                                           ->first();

        $telefoneValor = '';
        if ($contatoTelefone) {
            $telefoneValor = is_object($contatoTelefone) 
                ? ($contatoTelefone->contato ?? '') 
                : ($contatoTelefone['contato'] ?? '');
        }

        $data = [
            'usuario'  => $usuario,
            'telefone' => $telefoneValor
        ];

        return view('usuarios/meu_perfil', $data);
    }

    // Processa a atualização dos dados cadastrais e credenciais do usuário
    public function atualizarPerfil()
    {
        // 8.1. Verificação de segurança da sessão ativa
        if (!session()->get('logged_in')) {
            return redirect()->to('login');
        }

        $usuarioId = session()->get('id');
        $usuarioModel = new UsuarioModel();
        $contatoLinkModel = new ContatoLinkModel();

        // 8.2. Validação dos campos cadastrais essenciais
        $regras = [
            'nome'  => 'required|min_length[3]',
            'email' => "required|valid_email|is_unique[usuario.email,id,{$usuarioId}]",
        ];

        if (!$this->validate($regras)) {
            return redirect()->back()->withInput()->with('erro', $this->validator->listErrors());
        }

        // 8.3. Higienização de dados formatados (CEP e Telefone)
        $cepLimpo      = preg_replace('/[^0-9]/', '', $this->request->getPost('cep'));
        $telefoneLimpo = preg_replace('/[^0-9]/', '', $this->request->getPost('telefone'));

        // Monta o array com as informações gerais que serão atualizadas
        $dadosAtualizacao = [
            'nome'   => $this->request->getPost('nome'),
            'email'  => $this->request->getPost('email'),
            'cidade' => $this->request->getPost('cidade'),
            'estado' => strtoupper($this->request->getPost('estado')),
            'bairro' => $this->request->getPost('bairro'),
            'cep'    => $cepLimpo,
        ];

        // 8.4. Lógica de Alteração de Senha (Executada apenas se a nova senha for informada)
        $senhaAtual   = $this->request->getPost('senha_atual');
        $novaSenha    = $this->request->getPost('nova_senha');
        $confirmaNova = $this->request->getPost('confirma_nova_senha');

        if (!empty($novaSenha)) {
            
            // Garante que o usuário digitou a senha atual para confirmar a alteração
            if (empty($senhaAtual)) {
                return redirect()->back()->withInput()->with('erro', 'Para alterar a senha, você deve informar a sua senha atual.');
            }

            // Resgata o usuário logado para obter o e-mail e realizar a checagem correta
            $usuarioLogado = $usuarioModel->find($usuarioId);

            // CORREÇÃO DA SENHA ATUAL: Utiliza o método seguro do model (verificarCredenciais) 
            // que considera o padrão de hash e chaves de criptografia do sistema.
            $chebagemCredencial = $usuarioModel->verificarCredenciais($usuarioLogado['email'], $senhaAtual);

            if (!$chebagemCredencial) {
                return redirect()->back()->withInput()->with('erro', 'A senha atual informada está incorreta.');
            }

            // Confere se a nova senha e a confirmação batem
            if ($novaSenha !== $confirmaNova) {
                return redirect()->back()->withInput()->with('erro', 'A nova senha e a confirmação não conferem.');
            }

            // Valida o tamanho mínimo exigido
            if (strlen($novaSenha) < 6) {
                return redirect()->back()->withInput()->with('erro', 'A nova senha deve ter no mínimo 6 caracteres.');
            }

            // Atribui a nova senha ao array (o beforeUpdate do Model cuidará de gerar o novo hash)
            $dadosAtualizacao['senha'] = $novaSenha;
        }

        // 8.5. Persistência dos dados atualizados na tabela principal
        $usuarioModel->update($usuarioId, $dadosAtualizacao);

        // Atualiza ou cadastra o telefone na tabela de contatos vinculados
        if (!empty($telefoneLimpo)) {
            $contatoLinkModel->salvarContato($usuarioId, 'telefone', $telefoneLimpo);
        }

        // 8.6. Atualiza as informações básicas visíveis na sessão
        session()->set([
            'nome'  => $dadosAtualizacao['nome'],
            'email' => $dadosAtualizacao['email']
        ]);

        return redirect()->back()->with('sucesso', 'Perfil atualizado com sucesso!');
    }
}