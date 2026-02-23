<?php

class AuthController
{
    public function __construct(private PDO $db)
    {
    }

    public function register(array $body): array
    {
        $name     = trim($body['name'] ?? '');
        $email    = trim($body['email'] ?? '');
        $password = (string) ($body['password'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            throw new Exception('Faltou enviar name, email e password no corpo da requisição.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('O e-mail informado parece inválido.');
        }

        if (strlen($password) < 6) {
            throw new Exception('Sua senha está muito curta. Use pelo menos 6 caracteres.');
        }

        $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;
        $hash = password_hash($password, $algo);

        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password_hash)
             VALUES (?, ?, ?)"
        );

        $stmt->execute([$name, $email, $hash]);

        return [
            'ok'      => true,
            'message' => 'Usuário registrado com hash seguro.',
            'user_id' => (int) $this->db->lastInsertId(),
        ];
    }

    public function login(array $body): array
    {
        $email    = trim($body['email'] ?? '');
        $password = (string) ($body['password'] ?? '');

        $stmt = $this->db->prepare("SELECT id, password_hash, name FROM users WHERE email = ?");
        $stmt->execute([$email]);

        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            throw new Exception('E-mail ou senha incorretos.');
        }

        return [
            'ok'      => true,
            'message' => 'Login realizado com sucesso.',
            'user'    => [
                'id'   => (int) $user['id'],
                'name' => $user['name'],
            ],
        ];
    }
}