<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarTabelaUsuarios extends Migration
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
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'cpf' => [
                'type'       => 'VARCHAR',
                'constraint' => '11',
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'senha' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'cidade' => [
                'type'       => 'VARCHAR',
                'constraint' => '60',
            ],
            'estado' => [
                'type'       => 'VARCHAR',
                'constraint' => '2',
            ],
            'bairro' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'cep' => [
                'type'       => 'VARCHAR',
                'constraint' => '8',
            ],
            'dtCadastro' => [
                'type'    => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        // Chave Primária
        $this->forge->addKey('id', true);

        // Garante que não existirão e-mails ou CPFs duplicados
        $this->forge->addUniqueKey('email');
        $this->forge->addUniqueKey('cpf');

        // Cria a tabela 'usuario'
        $this->forge->createTable('usuario');
    }

    public function down()
    {
        // Remove a tabela caso a migration seja desfeita (rollback)
        $this->forge->dropTable('usuario');
    }
}