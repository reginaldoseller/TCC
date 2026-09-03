<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarTabelaProfissionais extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'usuario_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'descricaoPerfil' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'latitude' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'longitude' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'ativo' => [
                'type'    => 'BOOLEAN',
                'default' => true,
            ],
        ]);

        $this->forge->addKey('usuario_id', true);
        
        // Chave Estrangeira ligando usuario_id com a tabela de usuarios
        $this->forge->addForeignKey('usuario_id', 'usuarios', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('profissional');
    }

    public function down()
    {
        $this->forge->dropTable('profissional');
    }
}