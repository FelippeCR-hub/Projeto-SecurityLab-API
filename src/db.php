<?php
function db(): PDO {
  $host = '127.0.0.1';
  $dbname = 'security_lab';
  $user = 'root';
  $pass = '';
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
  return $pdo;
}