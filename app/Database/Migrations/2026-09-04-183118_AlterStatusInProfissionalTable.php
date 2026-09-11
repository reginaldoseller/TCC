<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterStatusInProfissionalTable extends Migration
{
    public function up()
    {
        $fields = [
            'status' => [
                'name'       => 'status', // Altera o nome de "ativo" para "status" se desejar, ou mantém a coluna
                'type'       => 'ENUM',
                'constraint' => ['em_analise', 'ativo', 'inativo', 'rejeitado'],
                'default'    => 'em_analise',
                'null'       => false,
            ],
        ];

        // Modifica a coluna 'ativo' existente para o novo tipo ENUM com nome 'status'
        $this->forge->modifyColumn('profissional', [
            'ativo' => [
                'name'       => 'status',
                'type'       => 'ENUM',
                'constraint' => ['em_analise', 'ativo', 'inativo', 'rejeitado'],
                'default'    => 'em_analise',
                'null'       => false,
            ]
        ]);
    }

    public function down()
    {
        // Reverte para TINYINT se necessário
        $this->forge->modifyColumn('profissional', [
            'status' => [
                'name'       => 'ativo',
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null'       => false,
            ]
        ]);
    }
}