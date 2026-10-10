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
        // =========================================================================
        // 1. FILTROS E PARÂMETROS GET
        // =========================================================================

        // Páginas para Paginação
        $pagePendentes     = (int) ($this->request->getGet('page_pendentes') ?? 1);
        $pageUsuarios      = (int) ($this->request->getGet('page_usuarios') ?? 1);
        $pageCategorias    = (int) ($this->request->getGet('page_categorias') ?? 1);
        $pageProfissionais = (int) ($this->request->getGet('page_profissionais') ?? 1);

        // Filtros de Usuários
        $buscaNome   = $this->request->getGet('busca_nome');
        $buscaEmail  = $this->request->getGet('busca_email');
        $buscaStatus = $this->request->getGet('busca_status');

        // Filtros de Categorias
        $buscaCategoria = $this->request->getGet('busca_categoria');

        // Filtros de Profissionais (Aba Geral)
        $buscaProfNome      = $this->request->getGet('busca_prof_nome');
        $buscaProfCategoria = $this->request->getGet('busca_prof_categoria');
        $buscaProfStatus    = $this->request->getGet('busca_prof_status');


        // =========================================================================
        // 2. INSTÂNCIA DOS MODELOS
        // =========================================================================
        $usuarioModel      = new \App\Models\UsuarioModel();
        $categoriaModel    = new \App\Models\CategoriaModel();
        $profissionalModel = new \App\Models\ProfissionalModel();


        // =========================================================================
        // 3. BLOCO: PROFISSIONAIS PENDENTES DE ANÁLISE (PAGINADO)
        // =========================================================================
        $data['profissionaisPendentes'] = $profissionalModel->getPendentesPaginados(5, $pagePendentes);
        $profissionalModel->pager->setPath('admin/dashboard');
        $data['pager_pendentes'] = $profissionalModel->pager;


        // =========================================================================
        // 4. BLOCO: FILTROS E PAGINAÇÃO DE USUÁRIOS
        // =========================================================================
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

        $data['usuarios'] = $usuarioModel->paginate(5, 'usuarios', $pageUsuarios);
        $usuarioModel->pager->setPath('admin/dashboard');
        $data['pager_usuarios'] = $usuarioModel->pager;


        // =========================================================================
        // 5. BLOCO: FILTROS E PAGINAÇÃO DE CATEGORIAS
        // =========================================================================
        if (!empty($buscaCategoria)) {
            $categoriaModel->like('categoria', $buscaCategoria);
        }

        $data['categorias'] = $categoriaModel
            ->orderBy('categoria', 'ASC')
            ->paginate(5, 'categorias', $pageCategorias);

        $categoriaModel->pager->setPath('admin/dashboard');
        $data['pager_categorias'] = $categoriaModel->pager;


        // =========================================================================
        // 6. BLOCO: FILTROS E PAGINAÇÃO DE PROFISSIONAIS (ABA GERAL)
        // =========================================================================

        // Lista de categorias para o <select> do filtro de profissionais
        $data['todas_categorias'] = $categoriaModel->orderBy('categoria', 'ASC')->findAll();

        // Busca os profissionais paginados
        $data['profissionais'] = $profissionalModel->getProfissionaisPaginados(
            $buscaProfNome,
            $buscaProfCategoria,
            $buscaProfStatus,
            5,                  // Limite por página
            $pageProfissionais  // Número da página atual
        );

        // Configura o Pager para os profissionais
        $profissionalModel->pager->setPath('admin/dashboard');
        $data['pager_profissionais'] = $profissionalModel->pager;


        // =========================================================================
        // 7. RETORNO PARA A VIEW
        // =========================================================================
        return view('admin/dashboard', $data);
    }

    // --- MODERAÇÃO DE PROFISSIONAIS ---

    // Aprova o cadastro/perfil do profissional
    public function aprovarProfissional($usuarioId)
    {
        $profissionalModel = new ProfissionalModel();

        // Atualiza o status do profissional para 'ativo' e limpa observações do admin
        $sucesso = $profissionalModel->atualizarStatus($usuarioId, 'ativo', 'Perfil aprovado pelo administrador.');

        if ($sucesso) {
            return redirect()->to(site_url('admin/dashboard#content-profissionais'))
                ->with('sucesso', 'Profissional aprovado com sucesso!');
        }

        return redirect()->to(site_url('admin/dashboard#content-profissionais'))
            ->with('erro', 'Não foi possível aprovar o profissional.');
    }

    // Solicita correções/ajustes de dados ao profissional
    public function solicitarAjustes($id)
    {
        $profissionalModel = new ProfissionalModel();

        $observacao = $this->request->getPost('observacao');

        if (empty($observacao)) {
            return redirect()->back()->with('erro', 'É necessário preencher a orientação para o profissional.');
        }

        $dadosAtualizacao = [
            'status'           => 'ajustes_solicitados',
            'observacao_admin' => $observacao,
        ];

        if ($profissionalModel->update($id, $dadosAtualizacao)) {
            return redirect()->to(site_url('admin/dashboard#content-profissionais'))
                ->with('sucesso', 'Solicitação de ajustes enviada com sucesso!');
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

        return redirect()->to(site_url('admin/dashboard#content-profissionais'))
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

        $bloqueadoAteFormatado = null;
        if (!empty($bloqueadoAte)) {
            $dataLimpa = str_replace('T', ' ', $bloqueadoAte);
            if (strlen($dataLimpa) === 10) {
                $dataLimpa .= ' 23:59:59';
            }
            $timestamp = strtotime($dataLimpa);
            $bloqueadoAteFormatado = $timestamp ? date('Y-m-d H:i:s', $timestamp) : null;
        }

        // 4. Prepara os dados para salvamento na tabela usuario
        $dadosAtualizacao = [
            'status'          => 'suspenso',
            'motivo_bloqueio' => $motivo,
            'bloqueado_ate'   => $bloqueadoAteFormatado,
        ];

        // 5. Executa o update na tabela 'usuario'
        if ($usuarioModel->update($id, $dadosAtualizacao)) {

            // ##### Atualiza o perfil profissional usando a chave correta (usuario_id)
            $profissionalModel = new \App\Models\ProfissionalModel();
            $profissional = $profissionalModel->where('usuario_id', $id)->first();

            if ($profissional) {
                // Em vez de $profissionalModel->update($id, ...), usamos o método seguro com where()
                // para evitar conflitos com a chave primária não autoincrementada do CodeIgniter.
                $profissionalModel->where('usuario_id', $id)->set([
                    'status'           => 'suspenso',
                    'observacao_admin' => !empty($motivo) ? 'Suspenso pelo admin: ' . $motivo : 'O usuário foi suspenso pelo administrador.',
                    'bloqueado_ate'    => $bloqueadoAteFormatado,
                ])->update();
            }
            // ############

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

        return redirect()->to(site_url('admin/dashboard#content-usuarios'))
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
