<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarTabelaProfissionalCategorias extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'usuario_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'categoria_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
        ]);

        // Chave Primária Composta
        $this->forge->addKey(['usuario_id', 'categoria_id'], true);

        // Chaves Estrangeiras relacionando Profissional e Categoria
        $this->forge->addForeignKey('usuario_id', 'profissional', 'usuario_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('categoria_id', 'categorias', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('profissional_categorias');
    }

    public function down()
    {
        $this->forge->dropTable('profissional_categorias');
    }
}