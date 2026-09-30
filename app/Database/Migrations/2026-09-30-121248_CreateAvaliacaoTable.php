<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAvaliacaoTable extends Migration
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
            'usuario_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'profissional_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'solicitacao_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'nota' => [
                'type'       => 'INT',
                'constraint' => 2,
            ],
            'comentario' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'data' => [
                'type' => 'DATETIME',
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
        $this->forge->addForeignKey('usuario_id', 'usuario', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('profissional_id', 'profissional', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('solicitacao_id', 'solicitacao_servico', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('avaliacao');
    }

    public function down()
    {
        $this->forge->dropTable('avaliacao');
    }
}