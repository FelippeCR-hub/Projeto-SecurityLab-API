<?php
class AuthController {
  public function __construct(private PDO $db) {}

  public function register(array $body): array {
    $name = trim($body['name'] ?? '');
    $email = trim($body['email'] ?? '');
    $password = (string)($body['password'] ?? '');

    if ($name === '' || $email === '' || $password === '') {
      throw new Exception('name, email e password são obrigatórios.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      throw new Exception('email inválido.');
    }
    if (strlen($password) < 6) {
      throw new Exception('senha muito curta (min 6).');
    }

    $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;
    $hash = password_hash($password, $algo);

    $stmt = $this->db->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $hash]);

    return ['ok' => true, 'message' => 'Usuário registrado com hash seguro.', 'user_id' => (int)$this->db->lastInsertId()];
  }

  public function login(array $body): array {
    $email = trim($body['email'] ?? '');
    $password = (string)($body['password'] ?? '');

    $stmt = $this->db->prepare("SELECT id, password_hash, name FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
      throw new Exception('Credenciais inválidas.');
    }

    return ['ok' => true, 'message' => 'Login OK', 'user' => ['id' => (int)$user['id'], 'name' => $user['name']]];
  }
}