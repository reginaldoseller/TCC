<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRaioAtendimentoToProfissional extends Migration
{
    public function up()
    {
        $fields = [
            'raio_atendimento_km' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 15,
                'null'       => false,
                'after'      => 'ativo',
            ],
        ];

        $this->forge->addColumn('profissional', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('profissional', 'raio_atendimento_km');
    }
}