<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusColumnsToUsuarioTable extends Migration
{
    public function up()
    {
        $fields = [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['ativo', 'suspenso', 'banido'],
                'default'    => 'ativo',
                'after'      => 'dtCadastro',
            ],
            'motivo_bloqueio' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'status',
            ],
            'bloqueado_ate' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'after'      => 'motivo_bloqueio',
            ],
        ];

        $this->forge->addColumn('usuario', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('usuario', ['status', 'motivo_bloqueio', 'bloqueado_ate']);
    }
}