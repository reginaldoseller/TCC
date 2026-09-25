<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddResetPasswordToUsuario extends Migration
{
    public function up()
    {
        $fields = [
            'reset_token' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'default'    => null,
                'after'      => 'bloqueado_ate', // Posiciona após a última coluna da tabela
            ],
            'reset_expires_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'after'   => 'reset_token',
            ],
        ];

        $this->forge->addColumn('usuario', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('usuario', ['reset_token', 'reset_expires_at']);
    }
}