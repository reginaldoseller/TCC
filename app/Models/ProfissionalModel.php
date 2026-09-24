<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfissionalModel extends Model
{
    protected $table            = 'profissional';
    protected $primaryKey       = 'usuario_id'; // usuario_id como chave primária
    protected $useAutoIncrement = false;        // Desativa auto incremento na PK
    protected $returnType       = 'array';

    // ADICIONADOS: 'observacao_admin' e 'bloqueado_ate'
    protected $allowedFields    = [
        'usuario_id', 
        'descricaoPerfil', 
        'raio_atendimento_km',
        'latitude', 
        'longitude', 
        'status', // ENUM: 'em_analise', 'ativo', 'inativo', 'pendente', 'indisponivel', 'ajustes_solicitados', 'suspenso'
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
     * Busca os profissionais com status 'em_analise' ou 'pendente' unindo dados da tabela usuario
     */
    public function getPendentes()
    {
        return $this->select('profissional.usuario_id as profissional_id, profissional.status, usuario.id as usuario_id, usuario.nome, usuario.email, usuario.dtCadastro')
                    ->join('usuario', 'usuario.id = profissional.usuario_id')
                    ->whereIn('profissional.status', ['em_analise', 'pendente'])
                    ->findAll();
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
}