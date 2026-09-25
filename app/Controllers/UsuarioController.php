<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use App\Models\ProfissionalModel;
use App\Models\ContatoLinkModel;

class UsuarioController extends BaseController
{
    // Exibe a tela de login
    public function login()
    {
        return view('usuarios/login');
    }

    // Exibe o formulário de cadastro de usuário/cliente
    public function novo()
    {
        return view('usuarios/cadastrar');
    }

    // Processa o cadastro do novo usuário no banco de dados
    public function criar()
    {
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

        if (!$this->validate($regras)) {
            return redirect()->back()->withInput()->with('erro', $this->validator->listErrors());
        }

        $usuarioModel     = new UsuarioModel();
        $contatoLinkModel = new ContatoLinkModel();

        $cpfLimpo      = preg_replace('/[^0-9]/', '', $this->request->getPost('cpf'));
        $cepLimpo      = preg_replace('/[^0-9]/', '', $this->request->getPost('cep'));
        $telefoneLimpo = preg_replace('/[^0-9]/', '', $this->request->getPost('telefone'));

        $dados = [
            'nome'   => $this->request->getPost('nome'),
            'cpf'    => $cpfLimpo,
            'email'  => $this->request->getPost('email'),
            'senha'  => password_hash($this->request->getPost('senha'), PASSWORD_DEFAULT),
            'cidade' => $this->request->getPost('cidade'),
            'estado' => strtoupper($this->request->getPost('estado')),
            'bairro' => $this->request->getPost('bairro'),
            'cep'    => $cepLimpo,
            // 'status' é definido como 'ativo' por padrão no banco de dados
        ];

        // Transação para assegurar consistência do cadastro
        $db = \Config\Database::connect();
        $db->transStart();

        $usuarioModel->insert($dados);
        $novoId = $usuarioModel->getInsertID();

        // Se o campo de telefone for enviado no formulário, salva em contato_links
        if (!empty($telefoneLimpo)) {
            $contatoLinkModel->salvarContato($novoId, 'telefone', $telefoneLimpo);
        }

        $db->transComplete();

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

    // Processa a validação de e-mail e senha no Login
    public function autenticar()
    {
        $email = $this->request->getPost('email');
        $senha = $this->request->getPost('senha');

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->verificarCredenciais($email, $senha);

        if ($usuario) {
            // 1. Verificação de Banimento Definitivo
            if ($usuario['status'] === 'banido') {
                $mensagemBanido = 'Sua conta foi permanentemente suspensa por descumprimento dos termos de uso.';
                if (!empty($usuario['motivo_bloqueio'])) {
                    $mensagemBanido .= ' Motivo: ' . $usuario['motivo_bloqueio'];
                }
                return redirect()->back()->withInput()->with('erro', $mensagemBanido);
            }

            // 2. Verificação de Suspensão Temporária
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
                    // O tempo de suspensão expirou: reativa a conta automaticamente
                    $usuarioModel->update($usuario['id'], [
                        'status'          => 'ativo',
                        'motivo_bloqueio' => null,
                        'bloqueado_ate'   => null
                    ]);
                    $usuario['status'] = 'ativo';
                }
            }

            // Consulta perfil do profissional via ProfissionalModel
            $profissionalModel = new ProfissionalModel();
            $dadosProfissional = $profissionalModel->where('usuario_id', $usuario['id'])->first();

            $ehProfissional = !empty($dadosProfissional);
            $profissionalAprovado = $ehProfissional && isset($dadosProfissional['status']) && $dadosProfissional['status'] === 'ativo';

            // Consulta perfil de administrador via UsuarioModel
            $ehAdmin = $usuarioModel->ehAdmin($usuario['id']);

            // Define o tipo de perfil inicial
            $tipoPerfil = 'Cliente';
            if ($ehAdmin) {
                $tipoPerfil = 'Administrador';
            } elseif ($profissionalAprovado) {
                $tipoPerfil = 'Profissional';
            }

            session()->set([
                'id'           => $usuario['id'],
                'nome'         => $usuario['nome'],
                'email'        => $usuario['email'],
                'logged_in'    => true,
                'tipo_perfil'  => $tipoPerfil,
                'perfil_ativo' => $tipoPerfil,
                'is_admin'     => $ehAdmin
            ]);

            if ($ehAdmin) {
                return redirect()->to('admin/dashboard')->with('sucesso', 'Login administrativo realizado com sucesso!');
            }

            if ($profissionalAprovado) {
                return redirect()->to('profissional/dashboard')->with('sucesso', 'Login realizado com sucesso!');
            }

            if ($ehProfissional && !$profissionalAprovado) {
                return redirect()->to('cliente/dashboard')->with('aviso', 'Seu perfil profissional está em análise pela administração. Você navegará como cliente até a aprovação.');
            }

            return redirect()->to('cliente/dashboard')->with('sucesso', 'Login realizado com sucesso!');
        }

        return redirect()->back()->withInput()->with('erro', 'E-mail ou senha inválidos.');
    }

    // Carrega a tela do painel do cliente
    public function dashboard()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('login')->with('erro', 'Faça login para acessar o painel.');
        }

        return view('cliente/dashboard');
    }

    // Encerra a sessão do usuário
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/')->with('sucesso', 'Você saiu da sua conta com sucesso.');
    }

    // Alterna o modo ativo de visualização para Cliente
    public function mudarParaCliente()
    {
        session()->set('perfil_ativo', 'Cliente');
        return redirect()->to('cliente/dashboard');
    }

    // Alterna o modo ativo de visualização para Profissional
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

    // Alterna o modo ativo de visualização para Administrador
    public function mudarParaAdmin()
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('cliente/dashboard')->with('erro', 'Você não possui permissão de administrador.');
        }

        session()->set('perfil_ativo', 'Administrador');
        return redirect()->to('admin/dashboard');
    }

    // Exibe o formulário de solicitação de recuperação de senha
    public function esqueciSenha()
    {
        return view('usuarios/esqueci_senha');
    }

    // Processa a solicitação e gera o token de recuperação
    public function processarEsqueciSenha()
    {
        $email = $this->request->getPost('email');

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->where('email', $email)->first();

        if (!$usuario) {
            // Por segurança, exibe mensagemGenérica para não expor e-mails cadastrados
            return redirect()->back()->with('sucesso', 'Se o e-mail estiver cadastrado, você receberá o link para redefinição em instantes.');
        }

        // Gera token aleatório de 32 bytes (64 caracteres hexadecimais)
        $token = bin2hex(random_bytes(32));
        $expiracao = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $usuarioModel->update($usuario['id'], [
            'reset_token'      => $token,
            'reset_expires_at' => $expiracao,
        ]);

        // Cria o link que será enviado ao utilizador
        $linkRedefinicao = base_url("redefinir-senha/{$token}");

        // TODO: Enviar e-mail utilizando a classe \Config\Services::email() do CodeIgniter
        // Exemplo simples para teste em ambiente local (desenvolvimento):
        log_message('info', "Link de recuperação para {$email}: {$linkRedefinicao}");

        return redirect()->back()->with('sucesso', 'Se o e-mail estiver cadastrado, você receberá o link para redefinição em instantes.');
    }

    // Exibe o formulário para digitar a nova senha via token
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

        // Verifica se o token já expirou
        $agora = date('Y-m-d H:i:s');
        if ($usuario['reset_expires_at'] < $agora) {
            return redirect()->to('esqueci-senha')->with('erro', 'Este link de redefinição expirou. Solicite um novo.');
        }

        return view('usuarios/redefinir_senha', ['token' => $token]);
    }

    // Processa a gravação da nova senha
    public function salvarNovaSenha()
    {
        $token           = $this->request->getPost('token');
        $senha           = $this->request->getPost('senha');
        $confirmarSenha  = $this->request->getPost('confirmar_senha');

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

        // Atualiza a nova senha (o hashPassword callback da model trata da criptografia Argon2id)
        $usuarioModel->update($usuario['id'], [
            'senha'            => $senha,
            'reset_token'      => null,
            'reset_expires_at' => null,
        ]);

        return redirect()->to('login')->with('sucesso', 'Sua senha foi alterada com sucesso! Faça login com as novas credenciais.');
    }

    // Exibe a tela de edição do perfil
    // Exibe a tela de edição do perfil
    public function meuPerfil()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('login')->with('erro', 'Faça login para acessar o seu perfil.');
        }

        $usuarioId = session()->get('id');
        $usuarioModel = new UsuarioModel();
        $contatoLinkModel = new ContatoLinkModel();

        $usuario = $usuarioModel->find($usuarioId);

        // Busca o telefone cadastrado na contato_links usando a coluna correta: tipoContato
        $contatoTelefone = $contatoLinkModel->where('usuario_id', $usuarioId)
                                            ->where('tipoContato', 'telefone')
                                            ->first();

        // Trata objeto ou array e lê o valor da coluna 'contato'
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

    // Processa a atualização dos dados pessoais
    public function atualizarPerfil()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('login');
        }

        $usuarioId = session()->get('id');
        $usuarioModel = new UsuarioModel();
        $contatoLinkModel = new ContatoLinkModel();

        // Validação simples dos dados
        $regras = [
            'nome'  => 'required|min_length[3]',
            'email' => "required|valid_email|is_unique[usuario.email,id,{$usuarioId}]",
        ];

        if (!$this->validate($regras)) {
            return redirect()->back()->withInput()->with('erro', $this->validator->listErrors());
        }

        $cepLimpo      = preg_replace('/[^0-9]/', '', $this->request->getPost('cep'));
        $telefoneLimpo = preg_replace('/[^0-9]/', '', $this->request->getPost('telefone'));

        $dadosAtualizacao = [
            'nome'   => $this->request->getPost('nome'),
            'email'  => $this->request->getPost('email'),
            'cidade' => $this->request->getPost('cidade'),
            'estado' => strtoupper($this->request->getPost('estado')),
            'bairro' => $this->request->getPost('bairro'),
            'cep'    => $cepLimpo,
        ];

        // Lógica para Troca de Senha (se preenchida)
        $senhaAtual       = $this->request->getPost('senha_atual');
        $novaSenha        = $this->request->getPost('nova_senha');
        $confirmaNova     = $this->request->getPost('confirma_nova_senha');

        if (!empty($novaSenha)) {
            $usuarioLogado = $usuarioModel->find($usuarioId);

            if (!password_verify($senhaAtual, $usuarioLogado['senha'])) {
                return redirect()->back()->withInput()->with('erro', 'A senha atual informada está incorreta.');
            }

            if ($novaSenha !== $confirmaNova) {
                return redirect()->back()->withInput()->with('erro', 'A nova senha e a confirmação não conferem.');
            }

            $dadosAtualizacao['senha'] = password_hash($novaSenha, PASSWORD_DEFAULT);
        }

        // Atualiza a tabela usuario
        $usuarioModel->update($usuarioId, $dadosAtualizacao);

        // Atualiza ou salva o telefone na contato_links
        if (!empty($telefoneLimpo)) {
            $contatoLinkModel->salvarContato($usuarioId, 'telefone', $telefoneLimpo);
        }

        // Atualiza a sessão ativa com o novo nome/e-mail
        session()->set([
            'nome'  => $dadosAtualizacao['nome'],
            'email' => $dadosAtualizacao['email']
        ]);

        return redirect()->back()->with('sucesso', 'Perfil atualizado com sucesso!');
    }
}