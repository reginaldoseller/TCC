<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;

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
        // 1. Regras de validação completas para o cadastro
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

        $usuarioModel = new UsuarioModel();

        // Higieniza os campos CPF e CEP deixando apenas dígitos numéricos
        $cpfLimpo = preg_replace('/[^0-9]/', '', $this->request->getPost('cpf'));
        $cepLimpo = preg_replace('/[^0-9]/', '', $this->request->getPost('cep'));

        // Passa a senha limpa em texto puro. O UsuarioModel executará o callback
        // 'hashPassword' com Argon2id + Pepper antes do insert automaticamente.
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

        if ($usuarioModel->save($dados)) {
            // Resgata o ID recém-gerado no banco
            $novoId = $usuarioModel->getInsertID();

            // Inicia a sessão automaticamente para o novo usuário
            session()->set([
                'id'           => $novoId,
                'nome'         => $dados['nome'],
                'email'        => $dados['email'],
                'tipo_perfil'  => 'Cliente',
                'perfil_ativo' => 'Cliente',
                'logged_in'    => true,
            ]);

            // Redireciona para o painel do cliente
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
        $usuario = $usuarioModel->where('email', $email)->first();

        if ($usuario) {
            // Valida a senha hash utilizando Argon2id + Pepper
            $pepper = env('security.passwordPepper', '');
            if (password_verify($senha . $pepper, $usuario['senha'])) {

                // Checa no banco de dados se esse usuário possui registro na tabela profissional
                $db = \Config\Database::connect();
                $ehProfissional = $db->table('profissional')
                    ->where('usuario_id', $usuario['id'])
                    ->countAllResults() > 0;

                $tipoPerfil = $ehProfissional ? 'Profissional' : 'Cliente';

                // Preenche a Session com as informações corretas
                session()->set([
                    'id'           => $usuario['id'],
                    'nome'         => $usuario['nome'],
                    'email'        => $usuario['email'],
                    'logged_in'    => true,
                    'tipo_perfil'  => $tipoPerfil,
                    'perfil_ativo' => $tipoPerfil
                ]);

                // Redireciona para o painel correspondente ao perfil detectado
                if ($ehProfissional) {
                    return redirect()->to('profissional/dashboard')->with('sucesso', 'Login realizado com sucesso!');
                }

                return redirect()->to('cliente/dashboard')->with('sucesso', 'Login realizado com sucesso!');
            }
        }

        return redirect()->back()->withInput()->with('erro', 'E-mail ou senha inválidos.');
    }

    // Carrega a tela do painel do cliente
    public function dashboard()
    {
        // Garante que apenas usuários logados acessem a view
        if (!session()->get('logged_in')) {
            return redirect()->to('login')->with('erro', 'Faça login para acessar o painel.');
        }

        return view('cliente/dashboard');
    }

    // Encerra a sessão do usuário e redireciona para a Home
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
        $db = \Config\Database::connect();
        $ehProfissional = $db->table('profissional')
            ->where('usuario_id', session()->get('id'))
            ->countAllResults() > 0;

        if (!$ehProfissional) {
            return redirect()->to('profissional/ativar-perfil')->with('erro', 'Você precisa ativar seu perfil profissional primeiro.');
        }

        session()->set('perfil_ativo', 'Profissional');
        return redirect()->to('profissional/dashboard');
    }
}