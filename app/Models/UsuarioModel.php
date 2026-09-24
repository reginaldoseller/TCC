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
        'ativo'
    ];

    protected $useTimestamps = false;

    // Callbacks para hash de senha
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];


    protected function hashPassword(array $data)
    {
        if (isset($data['data']['senha']) && !empty($data['data']['senha'])) {
            $pepper = env('security.passwordPepper', '');
            $senhaComPepper = $data['data']['senha'] . $pepper;

            $options = [
                'memory_cost' => 65536,
                'time_cost'   => 3,
                'threads'     => 4,
            ];

            $data['data']['senha'] = password_hash($senhaComPepper, PASSWORD_ARGON2ID, $options);
        } else {
            unset($data['data']['senha']);
        }

        return $data;
    }

   
    /**
     * Valida email e senha com Argon2id + Pepper
     */
    public function verificarCredenciais(string $email, string $senha)
    {
        $usuario = $this->where('email', $email)->first();

        if (!$usuario) {
            return false;
        }

        $pepper = env('security.passwordPepper', '');
        if (password_verify($senha . $pepper, $usuario['senha'])) {
            return $usuario;
        }

        return false;
    }

  
    /**
     * Verifica na tabela administrador se o usuário possui registro
     */
    public function ehAdmin(int $usuarioId): bool
    {
        $db = \Config\Database::connect();
        return $db->table('administrador')
                  ->where('usuario_id', $usuarioId)
                  ->countAllResults() > 0;
    }

    /**
     * Retorna a lista simples de usuários para a visão do administrador
     */
    public function getUsuariosDashboard()
    {
        return $this->select('id, nome, email, dtCadastro')->findAll();
    }
}