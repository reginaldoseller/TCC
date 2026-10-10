<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateStatusProfissionalTable extends Migration
{
    public function up()
    {
        // Altera o campo 'status' adicionando os novos valores ao ENUM
        $fields = [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['em_analise', 'ativo', 'pendente', 'indisponivel', 'inativo', 'ajustes_solicitados', 'suspenso'],
                'default'    => 'em_analise',
            ],
        ];

        $this->forge->modifyColumn('profissional', $fields);
    }

    public function down()
    {
        // Reverte o campo 'status' para os valores antigos caso seja necessário rollback
        $fields = [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['em_analise', 'ativo', 'pendente', 'indisponivel', 'inativo'],
                'default'    => 'em_analise',
            ],
        ];

        $this->forge->modifyColumn('profissional', $fields);
    }
}