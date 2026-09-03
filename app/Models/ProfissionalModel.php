<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfissionalModel extends Model
{
    protected $table            = 'profissional';
    protected $primaryKey       = 'usuario_id'; // Conforme estrutura, usuario_id é a chave primária
    protected $useAutoIncrement = false;      // Desativa auto incremento na PK já que é FK do usuario
    protected $returnType       = 'array';

    // Lista atualizada de colunas permitidas para inserção/atualização
    protected $allowedFields    = [
        'usuario_id', 
        'descricaoPerfil', 
        'raio_atendimento_km',
        'latitude', 
        'longitude', 
        'ativo'
    ];

    protected $useTimestamps    = false;
}