<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use App\Models\ProfissionalModel;
use App\Models\ContatoLinkModel; // Importação adicionada

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

        $cpfLimpo = preg_replace('/[^0-9]/', '', $this->request->getPost('cpf'));
        $cepLimpo = preg_replace('/[^0-9]/', '', $this->request->getPost('cep'));
        $telefone = $this->request->getPost('telefone');

        $dados = [
            'nome'   => $this->request->getPost('nome'),
            'cpf'    => $cpfLimpo,
            'email'  => $this->request->getPost('email'),
            'senha'  => $this->request->getPost('senha'),
            'cidade' => $this->request->getPost('cidade'),
            'estado' => strtoupper($this->request->getPost('estado')),
            'bairro' => $this->request->getPost('bairro'),
            'cep'    => $cepLimpo,
            'ativo'  => 1
        ];

        // Transação para assegurar consistência do cadastro
        $db = \Config\Database::connect();
        $db->transStart();

        $usuarioModel->insert($dados);
        $novoId = $usuarioModel->getInsertID();

        // Se o campo de telefone for enviado no formulário, salva em contato_links
        if (!empty($telefone)) {
            $contatoLinkModel->salvarContato($novoId, 'telefone', $telefone);
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
            // Checa se a conta está ativa
            if (isset($usuario['ativo']) && (int)$usuario['ativo'] !== 1) {
                return redirect()->back()->withInput()->with('erro', 'Sua conta está suspensa. Entre em contato com a administração.');
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
}