<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdministradorTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'usuario_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'nivel_permissao' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'admin',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('usuario_id', true); // Define usuario_id como Chave Primária
        $this->forge->addForeignKey('usuario_id', 'usuario', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('administrador');
    }

    public function down()
    {
        $this->forge->dropTable('administrador');
    }
}