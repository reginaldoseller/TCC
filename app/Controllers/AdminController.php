<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProfissionalModel;
use App\Models\UsuarioModel;
use App\Models\CategoriaModel;

class AdminController extends BaseController
{
    public function dashboard()
    {
        $profissionalModel = new ProfissionalModel();
        $usuarioModel      = new UsuarioModel();
        $categoriaModel    = new CategoriaModel();

        $data = [
            'profissionaisPendentes' => $profissionalModel->getPendentes(),
            'usuarios'               => $usuarioModel->getUsuariosDashboard(),
            'categorias'             => $categoriaModel->getCategoriasDashboard()
        ];

        return view('admin/dashboard', $data);
    }

    // Aprova o profissional ativando seu perfil
    public function aprovarProfissional($id)
    {
        $profissionalModel = new ProfissionalModel();

        // Limpa observações antigas e define como ativo
        $profissionalModel->update($id, [
            'status'           => 'ativo',
            'observacao_admin' => null,
            'bloqueado_ate'    => null
        ]);

        // TODO: Enviar e-mail de aprovação aqui

        return redirect()->to(site_url('admin/dashboard'))
            ->with('sucesso', 'Profissional aprovado com sucesso!');
    }

    // Solicita correções/ajustes de dados ao profissional
    public function solicitarAjustes($id)
    {
        $profissionalModel = new ProfissionalModel();

        // 1. Captura o texto enviado pelo modal no campo name="observacao"
        $observacao = $this->request->getPost('observacao');

        if (empty($observacao)) {
            return redirect()->back()->with('erro', 'É necessário preencher a orientação para o profissional.');
        }

        // 2. Monta os dados de atualização
        $dadosAtualizacao = [
            'status'           => 'ajustes_solicitados',
            'observacao_admin' => $observacao,
        ];

        // 3. Executa a atualização na base de dados
        if ($profissionalModel->update($id, $dadosAtualizacao)) {
            return redirect()->to(site_url('admin/dashboard'))->with('sucesso', 'Solicitação de ajustes enviada com sucesso!');
        }

        return redirect()->back()->with('erro', 'Não foi possível registrar os ajustes.');
    }

    // Suspende a adesão do profissional (indisponibilidade temporária/carência)
    public function suspenderProfissional($id)
    {
        $diasCarencia  = 90;
        $bloqueadoAte = date('Y-m-d H:i:s', strtotime("+{$diasCarencia} days"));

        $profissionalModel = new ProfissionalModel();
        $profissionalModel->update($id, [
            'status'           => 'indisponivel',
            'observacao_admin' => null,
            'bloqueado_ate'    => $bloqueadoAte
        ]);

        // TODO: Enviar e-mail institucional de suspensão temporária de adesões

        return redirect()->to(site_url('admin/dashboard'))
            ->with('sucesso', 'Adesão do profissional suspensa temporariamente.');
    }

    // --- MODERAÇÃO DE USUÁRIOS GERAIS ---

    // Suspende a conta de um usuário temporariamente
    public function suspenderUsuario($id)
    {
        
        // Instancia o Model de Usuários
        $usuarioModel = new \App\Models\UsuarioModel();

        // 1. Verifica se o usuário existe
        $usuario = $usuarioModel->find($id);
        if (!$usuario) {
            return redirect()->back()->with('erro', 'Usuário não encontrado.');
        }

        // 2. Impede a auto-suspensão
        if ($id == session()->get('id')) {
            return redirect()->back()->with('erro', 'Você não pode suspender sua própria conta.');
        }

        // 3. Captura os dados do formulário do Modal
        $motivo = $this->request->getPost('motivo_bloqueio');
        $bloqueadoAte = $this->request->getPost('bloqueado_ate');

        // 4. Prepara o array com os dados para salvar
        $dadosAtualizacao = [
            'status'          => 'suspenso',
            'motivo_bloqueio' => $motivo,
            'bloqueado_ate'   => !empty($bloqueadoAte) ? $bloqueadoAte : null,
        ];

        // 5. Executa a atualização no banco de dados
        if ($usuarioModel->update($id, $dadosAtualizacao)) {
            return redirect()->to(site_url('admin/dashboard'))->with('sucesso', 'Usuário suspenso com sucesso.');
        } else {
            return redirect()->back()->with('erro', 'Falha ao atualizar o status do usuário no banco de dados.');
        }
    }

    // Baniu/Expulsa um usuário definitivamente da plataforma
    public function banirUsuario($id)
    {
        $usuarioModel = new UsuarioModel();

        $motivo = $this->request->getPost('motivo');

        $usuarioModel->update($id, [
            'status'          => 'banido',
            'motivo_bloqueio' => $motivo,
            'bloqueado_ate'   => null
        ]);

        return redirect()->to(site_url('admin/dashboard'))
            ->with('sucesso', 'Usuário banido permanentemente da plataforma.');
    }

    // Reativa a conta de um usuário suspenso ou banido
    public function reativarUsuario($id)
    {
        $usuarioModel = new UsuarioModel();

        $usuarioModel->update($id, [
            'status'          => 'ativo',
            'motivo_bloqueio' => null,
            'bloqueado_ate'   => null
        ]);

        return redirect()->to(site_url('admin/dashboard'))
            ->with('sucesso', 'Conta do usuário reativada com sucesso.');
    }

    // --- GESTÃO DE CATEGORIAS ---

    public function criarCategoria()
    {
        $nome = $this->request->getPost('nome');

        if (!empty($nome)) {
            $categoriaModel = new CategoriaModel();

            $categoriaModel->insert([
                'categoria'  => $nome,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            return redirect()->to(site_url('admin/dashboard'))
                ->with('sucesso', 'Categoria cadastrada com sucesso!');
        }

        return redirect()->to(site_url('admin/dashboard'))
            ->with('erro', 'Informe o nome da categoria.');
    }

}