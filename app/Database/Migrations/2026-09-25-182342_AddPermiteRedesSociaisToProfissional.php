<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPermiteRedesSociaisToProfissional extends Migration
{
    public function up()
    {
        $fields = [
            'permite_redes_sociais' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 0, // Por padrão, redes sociais ficam ocultas até o admin liberar
                'null'       => false,
                'after'      => 'status', // Posiciona o campo após a coluna status
            ],
        ];

        $this->forge->addColumn('profissional', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('profissional', 'permite_redes_sociais');
    }
}