<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGeolocalizacaoSolicitacaoServico extends Migration
{
    public function up()
    {
        $fields = [
            'cep' => [
                'type'       => 'VARCHAR',
                'constraint' => '9',
                'null'       => true,
                'after'      => 'status', // Posiciona após o campo status
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
                'type'       => 'VARCHAR',
                'constraint' => '2',
                'null'       => true,
                'after'      => 'cidade',
            ],
            'latitude' => [
                'type'       => 'DOUBLE',
                'null'       => true,
                'after'      => 'estado',
            ],
            'longitude' => [
                'type'       => 'DOUBLE',
                'null'       => true,
                'after'      => 'latitude',
            ],
        ];

        $this->forge->addColumn('solicitacao_servico', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('solicitacao_servico', ['cep', 'bairro', 'cidade', 'estado', 'latitude', 'longitude']);
    }
}