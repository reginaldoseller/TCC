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

    public function aprovarProfissional($id)
    {
        $profissionalModel = new ProfissionalModel();
        $profissionalModel->atualizarStatus($id, 'ativo');

        return redirect()->to(site_url('admin/dashboard'))
            ->with('sucesso', 'Profissional aprovado com sucesso!');
    }

    public function rejeitarProfissional($id)
    {
        $profissionalModel = new ProfissionalModel();
        $profissionalModel->atualizarStatus($id, 'rejeitado');

        return redirect()->to(site_url('admin/dashboard'))
            ->with('sucesso', 'Cadastro do profissional rejeitado.');
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