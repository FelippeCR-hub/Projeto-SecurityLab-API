<?php

class Router
{
    private array $routes = [];

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $path): void
    {
        $handler = $this->routes[$method][$path] ?? null;

        if ($handler) {
            call_user_func($handler);
            return;
        }

        http_response_code(404);

        echo json_encode(
            ['error' => 'Não encontrei essa rota, verifique o método e o caminho'],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}