<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarTabelaContatoLinks extends Migration
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
            'tipoContato' => [
                'type'       => 'VARCHAR',
                'constraint' => '50', // Ex: 'WhatsApp', 'Instagram', 'Telefone', 'LinkedIn'
            ],
            'contato' => [
                'type'       => 'VARCHAR',
                'constraint' => '255', // Ex: '(14) 99999-9999' ou 'https://instagram.com/perfil'
            ],
        ]);

        $this->forge->addKey('id', true);

        // Chave estrangeira ligando ao usuario_id
        $this->forge->addForeignKey('usuario_id', 'usuarios', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('contato_links');
    }

    public function down()
    {
        $this->forge->dropTable('contato_links');
    }
}