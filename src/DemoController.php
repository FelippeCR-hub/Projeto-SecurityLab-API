<?php
class DemoController {
  public function __construct(private PDO $db) {}

  private function rateLimit(string $endpoint, int $maxPerMinute = 20): void {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    $this->db->prepare("DELETE FROM demo_attempts WHERE created_at < (NOW() - INTERVAL 60 SECOND)")->execute();

    $stmt = $this->db->prepare("SELECT COUNT(*) AS c FROM demo_attempts WHERE ip = ? AND endpoint = ? AND created_at >= (NOW() - INTERVAL 60 SECOND)");
    $stmt->execute([$ip, $endpoint]);
    $count = (int)$stmt->fetch()['c'];

    if ($count >= $maxPerMinute) {
      throw new Exception("Rate limit: muitas tentativas em 1 minuto.");
    }

    $this->db->prepare("INSERT INTO demo_attempts (ip, endpoint) VALUES (?, ?)")->execute([$ip, $endpoint]);
  }

  public function base64(array $body): array {
    $text = (string)($body['text'] ?? '');
    $encoded = base64_encode($text);
    $decoded = base64_decode($encoded, true);

    return [
      'note' => 'Base64 é codificação, não criptografia.',
      'input' => $text,
      'base64' => $encoded,
      'decoded' => $decoded
    ];
  }

  public function createWeakHash(array $body): array {
    $userId = (int)($body['user_id'] ?? 0);
    $password = (string)($body['demo_password'] ?? '');
    $algo = strtolower((string)($body['algo'] ?? 'md5')); 

    if ($userId <= 0 || $password === '') throw new Exception('user_id e demo_password são obrigatórios.');
    if (!in_array($algo, ['md5','sha1'], true)) throw new Exception('algo deve ser md5 ou sha1.');

    $hash = ($algo === 'md5') ? md5($password) : sha1($password);

    $stmt = $this->db->prepare("INSERT INTO demo_hashes (user_id, algo, hash_value, hint) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $algo, $hash, 'Senha demo fraca para simulação.']);

    return [
      'ok' => true,
      'message' => 'Hash fraco criado para demonstração.',
      'demo_hash_id' => (int)$this->db->lastInsertId(),
      'algo' => $algo,
      'hash' => $hash
    ];
  }

  public function attackDictionary(array $body): array {
    $this->rateLimit('/demo/attack-dictionary', 10);

    $demoHashId = (int)($body['demo_hash_id'] ?? 0);
    if ($demoHashId <= 0) throw new Exception('demo_hash_id é obrigatório.');

    $stmt = $this->db->prepare("SELECT algo, hash_value FROM demo_hashes WHERE id = ?");
    $stmt->execute([$demoHashId]);
    $row = $stmt->fetch();
    if (!$row) throw new Exception('demo_hash_id não encontrado.');

    $algo = $row['algo'];
    $target = $row['hash_value'];

    $wordlist = [
    '123456',
    '12345678',
    '123456789',
    '123123',
    '111111',
    '000000',
    '1234',
    '0000',
    'senha',
    'senha123',
    'senh@123',
    'password',
    'admin',
    'admin123',
    'qwerty',
    'qwerty123',
    'abc123',
    'abcdef',
    'brasil',
    'brasil123',
    'teste',
    'teste123',
    'user',
    'root',
    '123qwe',
    'qwe123',
    '654321',
    '987654',
    'letmein',
    'welcome',
    'iloveyou'
];

    $tries = 0;
    foreach ($wordlist as $candidate) {
      $tries++;
      $h = ($algo === 'md5') ? md5($candidate) : sha1($candidate);
      if (hash_equals($target, $h)) {
        return [
          'ok' => true,
          'found' => true,
          'tries' => $tries,
          'password_demo' => $candidate,
          'note' => 'Encontrado via dicionário (demonstração).'
        ];
      }
    }

    return ['ok' => true, 'found' => false, 'tries' => $tries, 'note' => 'Não encontrado na wordlist (demonstração).'];
  }

  public function attackBruteforcePin(array $body): array {
    $this->rateLimit('/demo/attack-bruteforce-pin', 5);

    $demoHashId = (int)($body['demo_hash_id'] ?? 0);
    $max = (int)($body['max_attempts'] ?? 2000); 
    if ($demoHashId <= 0) throw new Exception('demo_hash_id é obrigatório.');
    if ($max < 1 || $max > 10000) throw new Exception('max_attempts deve ser 1..10000.');

    $stmt = $this->db->prepare("SELECT algo, hash_value FROM demo_hashes WHERE id = ?");
    $stmt->execute([$demoHashId]);
    $row = $stmt->fetch();
    if (!$row) throw new Exception('demo_hash_id não encontrado.');

    $algo = $row['algo'];
    $target = $row['hash_value'];

    $tries = 0;
    for ($i = 0; $i < 10000 && $tries < $max; $i++) {
      $tries++;
      $pin = str_pad((string)$i, 4, '0', STR_PAD_LEFT);
      $h = ($algo === 'md5') ? md5($pin) : sha1($pin);
      if (hash_equals($target, $h)) {
        return ['ok' => true, 'found' => true, 'tries' => $tries, 'pin_demo' => $pin];
      }
    }

    return ['ok' => true, 'found' => false, 'tries' => $tries, 'note' => 'Limite atingido ou não encontrado.'];
  }
}