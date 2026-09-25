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
        'status',           // 'ativo', 'suspenso', 'banido'
        'motivo_bloqueio',  // Justificativa do bloqueio/suspensão
        'bloqueado_ate',    // Data e hora de expiração da suspensão
        'reset_token',      // token para redefinição de senha
        'reset_expires_at'  // expiração do token de redefinição de senha
    ];

    protected $useTimestamps = false;

    // Callbacks para hash de senha
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        // Verifica se a chave 'senha' existe no array de dados enviado
        if (isset($data['data']['senha'])) {
            // Se a senha foi informada e não está vazia, gera o hash
            if (!empty($data['data']['senha'])) {
                $pepper = env('security.passwordPepper', '');
                $senhaComPepper = $data['data']['senha'] . $pepper;

                $options = [
                    'memory_cost' => 65536,
                    'time_cost'   => 3,
                    'threads'     => 4,
                ];

                $data['data']['senha'] = password_hash($senhaComPepper, PASSWORD_ARGON2ID, $options);
            } else {
                // Se foi enviada a chave 'senha' mas veio vazia (ex: formulário de edição), removemos do update
                unset($data['data']['senha']);
            }
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
     * Retorna a lista de usuários para a visão do administrador
     * Usa a própria instância da Model para respeitar $table = 'usuario'
     */
    public function getUsuariosDashboard()
    {
        return $this->select('id, nome, email, status, dtCadastro')->findAll();
    }
}
