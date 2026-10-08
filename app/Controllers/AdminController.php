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
        // Captura os parâmetros dos filtros GET (Usuários)
        $buscaNome    = $this->request->getGet('busca_nome');
        $buscaEmail   = $this->request->getGet('busca_email');
        $buscaStatus  = $this->request->getGet('busca_status');
        $pageUsuarios = (int) ($this->request->getGet('page_usuarios') ?? 1);

        // Captura o parâmetro do filtro GET (Categorias)
        $buscaCategoria  = $this->request->getGet('busca_categoria');
        $pageCategorias = (int) ($this->request->getGet('page_categorias') ?? 1);

        // Instancia os modelos
        $usuarioModel   = new \App\Models\UsuarioModel();
        $categoriaModel = new \App\Models\CategoriaModel();

        // --- FILTROS E PAGINAÇÃO DE USUÁRIOS ---
        if (!empty($buscaNome)) {
            $usuarioModel->like('nome', $buscaNome);
        }

        if (!empty($buscaEmail)) {
            $usuarioModel->like('email', $buscaEmail);
        }

        if (!empty($buscaStatus)) {
            if ($buscaStatus === 'suspenso') {
                $usuarioModel->where('status', 'suspenso');
            } elseif ($buscaStatus === 'ativo') {
                $usuarioModel->where('status', 'ativo');
            }
        }


        // 1. Paginação dos Usuários
        $data['usuarios'] = $usuarioModel->paginate(5, 'usuarios', $pageUsuarios);
        $usuarioModel->pager->setPath('admin/dashboard');
        $data['pager_usuarios'] = $usuarioModel->pager;



        // --- FILTROS E PAGINAÇÃO DE CATEGORIAS ---
        if (!empty($buscaCategoria)) {
            $categoriaModel->like('categoria', $buscaCategoria);
        }

        // 2. Paginação das Categorias
        $data['categorias'] = $categoriaModel
            ->orderBy('categoria', 'ASC')
            ->paginate(5, 'categorias', $pageCategorias);

        $categoriaModel->pager->setPath('admin/dashboard');
        $data['pager_categorias'] = $categoriaModel->pager;

        return view('admin/dashboard', $data);
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
        $usuarioModel = new \App\Models\UsuarioModel();

        // 1. Verifica se o usuário existe
        $usuario = $usuarioModel->find($id);
        if (!$usuario) {
            return redirect()->to(site_url('admin/dashboard#content-usuarios'))
                ->with('erro', 'Usuário não encontrado.');
        }

        // 2. Impede a auto-suspensão
        $idLogado = session()->get('usuario.id') ?? session()->get('id') ?? session()->get('id_usuario');
        if ($id == $idLogado) {
            return redirect()->to(site_url('admin/dashboard#content-usuarios'))
                ->with('erro', 'Você não pode suspender sua própria conta.');
        }

        // 3. Captura os dados do formulário do Modal
        $motivo       = trim($this->request->getPost('motivo_bloqueio'));
        $bloqueadoAte = $this->request->getPost('bloqueado_ate');

        // Formata a data vinda do input datetime-local para o padrão do MySQL
        $bloqueadoAteFormatado = !empty($bloqueadoAte) ? date('Y-m-d H:i:s', strtotime($bloqueadoAte)) : null;

        // 4. Prepara os dados para salvamento
        $dadosAtualizacao = [
            'status'          => 'suspenso',
            'motivo_bloqueio' => $motivo,
            'bloqueado_ate'   => $bloqueadoAteFormatado,
        ];

        // 5. Executa o update na tabela 'usuario'
        if ($usuarioModel->update($id, $dadosAtualizacao)) {
            return redirect()->to(site_url('admin/dashboard#content-usuarios'))
                ->with('sucesso', 'Usuário suspenso com sucesso!');
        }

        return redirect()->to(site_url('admin/dashboard#content-usuarios'))
            ->with('erro', 'Falha ao atualizar o status do usuário no banco de dados.');
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

        return redirect()->to(site_url('admin/dashboard#content-usuarios'))
            ->with('sucesso', 'Conta do usuário reativada com sucesso.');
    }

    // --- GESTÃO DE CATEGORIAS ---

    // --- GESTÃO DE CATEGORIAS ---

    // Método ajustado para bater certo com a rota /admin/cadastrarCategoria
    // --- GESTÃO DE CATEGORIAS ---

    public function cadastrarCategoria()
    {
        $nome      = trim($this->request->getPost('nome'));
        $descricao = trim($this->request->getPost('descricao'));

        if (!empty($nome)) {
            $categoriaModel = new CategoriaModel();

            $categoriaModel->insert([
                'categoria' => $nome,
                'descricao' => !empty($descricao) ? $descricao : null,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            return redirect()->to(site_url('admin/dashboard#content-categorias'))
                ->with('sucesso', 'Categoria cadastrada com sucesso!');
        }

        return redirect()->to(site_url('admin/dashboard#content-categorias'))
            ->with('erro', 'Informe o nome da categoria.');
    }

    public function atualizarCategoria()
    {
        $id        = $this->request->getPost('id');
        $nome      = trim($this->request->getPost('nome'));
        $descricao = trim($this->request->getPost('descricao'));

        if (empty($id) || empty($nome)) {
            return redirect()->to(site_url('admin/dashboard#content-categorias'))
                ->with('erro', 'Dados inválidos para atualização.');
        }

        $categoriaModel = new CategoriaModel();

        $categoriaModel->update($id, [
            'categoria' => $nome,
            'descricao' => !empty($descricao) ? $descricao : null
        ]);

        return redirect()->to(site_url('admin/dashboard#content-categorias'))
            ->with('sucesso', 'Categoria atualizada com sucesso!');
    }
}
