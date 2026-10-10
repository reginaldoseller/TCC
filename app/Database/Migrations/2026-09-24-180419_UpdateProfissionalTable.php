<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateProfissionalTable extends Migration
{
    public function up()
    {
        // 1. Altera a coluna 'status' para aceitar os novos valores do ciclo de vida
        $fieldsStatus = [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['em_analise', 'ativo', 'pendente', 'indisponivel', 'inativo'],
                'default'    => 'em_analise',
                'null'       => false,
            ],
        ];
        $this->forge->modifyColumn('profissional', $fieldsStatus);

        // 2. Adiciona as colunas 'observacao_admin' e 'bloqueado_ate' após 'raio_atendimento_km'
        $fieldsNovos = [
            'observacao_admin' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'raio_atendimento_km',
            ],
            'bloqueado_ate' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'after'      => 'observacao_admin',
            ],
        ];
        $this->forge->addColumn('profissional', $fieldsNovos);
    }

    public function down()
    {
        // Reverte a adição das novas colunas
        $this->forge->dropColumn('profissional', ['observacao_admin', 'bloqueado_ate']);

        // Reverte a coluna 'status' para o formato original (se necessário)
        $fieldsStatusOriginal = [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['em_analise', 'ativo', 'rejeitado'],
                'default'    => 'em_analise',
                'null'       => false,
            ],
        ];
        $this->forge->modifyColumn('profissional', $fieldsStatusOriginal);
    }
}
