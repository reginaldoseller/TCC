<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AdicionaCamposEnderecoProfissional extends Migration
{
    public function up()
    {
        $fields = [
            'cep' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'null'       => true,
                'after'      => 'raio_atendimento_km',
            ],
            'bairro' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'cep',
            ],
            'cidade' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'bairro',
            ],
            'estado' => [
                'type'       => 'CHAR',
                'constraint' => '2',
                'null'       => true,
                'after'      => 'cidade',
            ],
        ];

        $this->forge->addColumn('profissional', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('profissional', ['cep', 'bairro', 'cidade', 'estado']);
    }
}