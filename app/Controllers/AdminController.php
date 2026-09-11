<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class AdminController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function dashboard()
    {
        // Busca profissionais pendentes utilizando dtCadastro
        $profissionaisPendentes = $this->db->table('profissional p')
            ->select('p.usuario_id as profissional_id, p.status, u.id as usuario_id, u.nome, u.email, u.dtCadastro')
            ->join('usuario u', 'u.id = p.usuario_id')
            ->where('p.status', 'em_analise')
            ->get()
            ->getResultArray();

        // Busca lista completa de usuários com dtCadastro
        $usuarios = $this->db->table('usuario')
            ->select('id, nome, email, dtCadastro')
            ->get()
            ->getResultArray();

        // Busca categorias
        $categorias = $this->db->table('categorias')
            ->select('id, categoria, categoria as nome, descricao')
            ->get()
            ->getResultArray();

        $data = [
            'profissionaisPendentes' => $profissionaisPendentes,
            'usuarios'               => $usuarios,
            'categorias'             => $categorias
        ];

        return view('admin/dashboard', $data);
    }

    public function aprovarProfissional($id)
    {
        $this->db->table('profissional')
            ->where('usuario_id', $id)
            ->update(['status' => 'ativo']);

        return redirect()->to(site_url('admin/dashboard'))
            ->with('sucesso', 'Profissional aprovado com sucesso!');
    }

    public function rejeitarProfissional($id)
    {
        $this->db->table('profissional')
            ->where('usuario_id', $id)
            ->update(['status' => 'rejeitado']);

        return redirect()->to(site_url('admin/dashboard'))
            ->with('sucesso', 'Cadastro do profissional rejeitado.');
    }

    public function criarCategoria()
    {
        $nome = $this->request->getPost('nome');

        if (!empty($nome)) {
            // Insere na coluna 'categoria' conforme a estrutura do banco
            $this->db->table('categorias')->insert([
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
