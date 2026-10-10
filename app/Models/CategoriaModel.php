<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriaModel extends Model
{
    protected $table            = 'categorias';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields    = [
        'categoria',
        'descricao',
        'created_at'
    ];

    protected $useTimestamps    = false;

    /**
     * Retorna as categorias com o alias 'nome' para compatibilidade com as views
     */
    public function getCategoriasDashboard()
    {
        return $this->select('id, categoria, categoria as nome, descricao')->findAll();
    }
}