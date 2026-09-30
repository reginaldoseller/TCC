<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRespostaOrcamentoTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'solicitacao_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'profissional_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'dataResposta' => [
                'type' => 'DATETIME',
            ],
            'ordemDeInteresse' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('solicitacao_id', 'solicitacao_servico', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('profissional_id', 'profissional', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('resposta_orcamento');
    }

    public function down()
    {
        $this->forge->dropTable('resposta_orcamento');
    }
}