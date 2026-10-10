<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAtivoToUsuarioTable extends Migration
{
    public function up()
    {
        $fields = [
            'ativo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null'       => false,
                'after'      => 'dtCadastro', // Adiciona a coluna logo após a dtCadastro
            ],
        ];

        $this->forge->addColumn('usuario', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('usuario', 'ativo');
    }
}
