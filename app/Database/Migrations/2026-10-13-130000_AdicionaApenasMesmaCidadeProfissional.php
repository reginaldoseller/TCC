<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AdicionaApenasMesmaCidadeProfissional extends Migration
{
    public function up()
    {
        $this->forge->addColumn('profissional', [
            'apenas_mesma_cidade' => [
                'type'       => 'TINYINT',
                'constraint' => '1',
                'default'    => 0,
                'null'       => false,
                'after'      => 'estado',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('profissional', 'apenas_mesma_cidade');
    }
}