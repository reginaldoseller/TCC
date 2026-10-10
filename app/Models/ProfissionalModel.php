<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfissionalModel extends Model
{
    protected $table            = 'profissional';
    protected $primaryKey       = 'usuario_id'; // usuario_id como chave primária
    protected $useAutoIncrement = false;        // Desativa auto incremento na PK
    protected $returnType       = 'array';

    // Campos permitidos para atualização
    protected $allowedFields    = [
        'usuario_id', 
        'descricaoPerfil', 
        'raio_atendimento_km',
        'latitude', 
        'longitude', 
        'status', // ENUM: 'em_analise', 'ativo', 'inativo', 'pendente', 'indisponivel', 'ajustes_solicitados', 'suspenso'
        'permite_redes_sociais',
        'observacao_admin',
        'bloqueado_ate'
    ];

    protected $useTimestamps    = false;

    /**
     * Vincula uma lista de IDs de categorias a um profissional
     */
    public function salvarCategorias(int $usuarioId, array $categorias)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('profissional_categorias');

        // Remove vínculos antigos caso existam
        $builder->where('usuario_id', $usuarioId)->delete();

        // Insere as novas categorias selecionadas
        if (!empty($categorias)) {
            $novosVinculos = [];
            foreach ($categorias as $catId) {
                $novosVinculos[] = [
                    'usuario_id'   => $usuarioId,
                    'categoria_id' => $catId
                ];
            }
            $builder->insertBatch($novosVinculos);
        }
    }

    /**
     * Retorna o registro do profissional caso o status seja 'ativo'
     */
    public function getProfissionalAtivo(int $usuarioId)
    {
        return $this->where('usuario_id', $usuarioId)
                    ->where('status', 'ativo')
                    ->first();
    }

    /**
     * Busca os profissionais pendentes de aprovação com paginação
     */
    public function getPendentesPaginados(int $perPage = 5, int $page = 1)
    {
        return $this->select('profissional.*, usuario.id as usuario_id, usuario.nome, usuario.email, usuario.cidade, usuario.estado')
                    ->join('usuario', 'usuario.id = profissional.usuario_id')
                    ->whereIn('profissional.status', ['em_analise', 'pendente', 'ajustes_solicitados'])
                    ->paginate($perPage, 'pendentes', $page);
    }

    /**
     * Atualiza o status e a observação do profissional por ID do usuário
     */
    public function atualizarStatus(int $usuarioId, string $status, ?string $observacao = null): bool
    {
        $dados = ['status' => $status];
        if ($observacao !== null) {
            $dados['observacao_admin'] = $observacao;
        }

        return $this->where('usuario_id', $usuarioId)
                    ->set($dados)
                    ->update();
    }

    /**
     * Busca os profissionais filtrados e paginados para a aba de gestão geral
     */
    public function getProfissionaisPaginados(?string $nome = null, ?int $categoriaId = null, ?string $status = null, int $perPage = 5, int $page = 1)
    {
        $builder = $this->select('
                profissional.*, 
                usuario.nome, 
                usuario.email, 
                usuario.cidade, 
                usuario.estado,
                usuario.status as status_usuario
            ')
            ->join('usuario', 'usuario.id = profissional.usuario_id');

        // Filtro por Nome ou E-mail
        if (!empty($nome)) {
            $builder->groupStart()
                    ->like('usuario.nome', $nome)
                    ->orLike('usuario.email', $nome)
                    ->groupEnd();
        }

        // Filtro por Status do Perfil
        if (!empty($status)) {
            $builder->where('profissional.status', $status);
        }

        // Filtro por Categoria
        if (!empty($categoriaId)) {
            $builder->whereIn('profissional.usuario_id', function($subQuery) use ($categoriaId) {
                return $subQuery->select('usuario_id')
                                ->from('profissional_categorias')
                                ->where('categoria_id', $categoriaId);
            });
        }

        // Executa a paginação do CodeIgniter
        $profissionais = $builder->paginate($perPage, 'profissionais', $page);

        // Anexa as categorias a cada profissional paginado
        if (!empty($profissionais)) {
            $db = \Config\Database::connect();
            foreach ($profissionais as &$prof) {
                $cats = $db->table('profissional_categorias')
                           ->select('categorias.categoria')
                           ->join('categorias', 'categorias.id = profissional_categorias.categoria_id')
                           ->where('profissional_categorias.usuario_id', $prof['usuario_id'])
                           ->get()
                           ->getResultArray();

                $prof['categorias_list'] = array_column($cats, 'categoria');
            }
        }

        return $profissionais;
    }
}