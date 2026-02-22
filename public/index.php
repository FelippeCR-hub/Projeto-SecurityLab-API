<?php
require __DIR__ . '/../src/db.php';
require __DIR__ . '/../src/Response.php';
require __DIR__ . '/../src/AuthController.php';
require __DIR__ . '/../src/DemoController.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$base = ''; 
if ($base && str_starts_with($path, $base)) $path = substr($path, strlen($base));

$body = json_decode(file_get_contents('php://input'), true) ?? [];

$db = db();
$auth = new AuthController($db);
$demo = new DemoController($db);

try {
  if ($method === 'POST' && $path === '/auth/register') {
    Response::json($auth->register($body));
  }
  if ($method === 'POST' && $path === '/auth/login') {
    Response::json($auth->login($body));
  }
  if ($method === 'POST' && $path === '/demo/base64') {
    Response::json($demo->base64($body));
  }
  if ($method === 'POST' && $path === '/demo/create-weak-hash') {
    Response::json($demo->createWeakHash($body));
  }
  if ($method === 'POST' && $path === '/demo/attack-dictionary') {
    Response::json($demo->attackDictionary($body));
  }
  if ($method === 'POST' && $path === '/demo/attack-bruteforce-pin') {
    Response::json($demo->attackBruteforcePin($body));
  }

  Response::json(['error' => 'Not Found'], 404);
} catch (Throwable $e) {
  Response::json(['error' => $e->getMessage()], 400);
}