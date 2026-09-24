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
        $profissionalModel = new \App\Models\ProfissionalModel();

        // 1. Captura o texto enviado pelo modal no campo name="observacao"
        $observacao = $this->request->getPost('observacao');

        if (empty($observacao)) {
            return redirect()->back()->with('erro', 'É necessário preencher a orientação para o profissional.');
        }

        // 2. Monta os dados de atualização
        $dadosAtualizacao = [
            'status'           => 'ajustes_solicitados', // Altera de 'pendente' para 'ajustes_solicitados'
            'observacao_admin' => $observacao,            // Grava a anotação na coluna da BD
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
        // Define carência de 90 dias (ou leia do POST se for configurável)
        $diasCarecia  = 90;
        $bloqueadoAte = date('Y-m-d H:i:s', strtotime("+{$diasCarecia} days"));

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
