<?php

namespace App\Models;

use CodeIgniter\Model;

class ContatoLinkModel extends Model
{
    protected $table            = 'contato_links';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields    = [
        'usuario_id',
        'tipoContato',
        'contato'
    ];

    protected $useTimestamps    = false;

    /**
     * Salva ou atualiza um tipo específico de contato para o usuário
     */
    public function salvarContato(int $usuarioId, string $tipo, string $valor)
    {
        if (empty($valor)) {
            return;
        }

        $existente = $this->where('usuario_id', $usuarioId)
                          ->where('tipoContato', $tipo)
                          ->first();

        if ($existente) {
            $this->update($existente['id'], ['contato' => $valor]);
        } else {
            $this->insert([
                'usuario_id'  => $usuarioId,
                'tipoContato' => $tipo,
                'contato'     => $valor
            ]);
        }
    }
}