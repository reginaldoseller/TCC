<?php

namespace App\Controllers;

use App\Models\ProfissionalModel;
use App\Models\CategoriaModel;

class BuscaController extends BaseController
{
    public function index()
    {
        $profissionalModel = new ProfissionalModel();
        $categoriaModel = new CategoriaModel();

        // Recupera os filtros passados via GET
        $categoriaId = $this->request->getGet('categoria');
        $termo = $this->request->getGet('termo');

        // Carrega as categorias e garante que possuem tanto 'nome' quanto 'categoria'
        $categoriasRaw = $categoriaModel->asArray()->findAll();
        $categorias = [];
        foreach ($categoriasRaw as $cat) {
            $cat['categoria'] = $cat['nome'] ?? '';
            $categorias[] = $cat;
        }
        $data['categorias'] = $categorias;

        // Constrói a query para os profissionais
        $builder = $profissionalModel->asArray()
            ->select('profissional.*, usuario.nome, usuario.cidade, usuario.bairro, categorias.nome as categoria')
            ->join('usuario', 'usuario.id = profissional.usuario_id', 'left')
            ->join('profissional_categorias', 'profissional_categorias.usuario_id = profissional.usuario_id', 'left')
            ->join('categorias', 'categorias.id = profissional_categorias.categoria_id', 'left')
            ->where('profissional.status', 'ativo');

        // Aplica o filtro por Categoria, se selecionado
        if (!empty($categoriaId)) {
            $builder->where('profissional_categorias.categoria_id', $categoriaId);
        }

        // Aplica o filtro por Termo de busca, se digitado
        if (!empty($termo)) {
            $builder->groupStart()
                ->like('usuario.nome', $termo)
                ->orLike('categorias.nome', $termo)
                ->groupEnd();
        }

        $profissionaisRaw = $builder->groupBy('profissional.id')->findAll();

        // Normaliza os dados dos profissionais para evitar qualquer chave indefinida na view
        $profissionais = [];
        foreach ($profissionaisRaw as $prof) {
            $prof['nome']      = $prof['nome'] ?? 'Desconhecido';
            $prof['cidade']    = $prof['cidade'] ?? '';
            $prof['bairro']    = $prof['bairro'] ?? '';
            $prof['categoria'] = $prof['categoria'] ?? 'Sem categoria';
            $profissionais[]   = $prof;
        }

        $data['profissionais'] = $profissionais;

        // Carrega a view de listagem/busca
        return view('site/busca', $data);
    }
}