<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuario';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    protected $allowedFields    = [
        'nome', 
        'cpf', 
        'email', 
        'senha', 
        'cidade', 
        'estado', 
        'bairro', 
        'cep',
        'ativo' // Adicionado para permitir o gerenciamento do status do usuário
    ];

    protected $useTimestamps = false;

    // Callbacks acionados antes do insert ou update
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['senha']) && !empty($data['data']['senha'])) {
            // Fallback para '' caso a variável não exista no .env
            $pepper = env('security.passwordPepper', '');
            $senhaComPepper = $data['data']['senha'] . $pepper;

            $options = [
                'memory_cost' => 65536,
                'time_cost'   => 3,
                'threads'     => 4,
            ];

            $data['data']['senha'] = password_hash($senhaComPepper, PASSWORD_ARGON2ID, $options);
        } else {
            // Remove o índice 'senha' do array para não sobrescrever a senha antiga em updates sem alteração
            unset($data['data']['senha']);
        }

        return $data;
    }
}