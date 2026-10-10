<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropAtivoFromUsuarioTable extends Migration
{
    public function up()
    {
        // Dropa a coluna 'ativo' que ficou redundante após a inclusão do 'status'
        $this->forge->dropColumn('usuario', 'ativo');
    }

    public function down()
    {
        // Caso precise desfazer o rollback
        $fields = [
            'ativo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'bloqueado_ate',
            ],
        ];

        $this->forge->addColumn('usuario', $fields);
    }
}