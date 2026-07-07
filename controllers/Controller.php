<?php

// Classe base para todos os controllers, com as duas coisas que
// praticamente todo controller precisa: renderizar uma view e redirecionar.
abstract class Controller
{
    protected function view(string $caminho, array $dados = []): void
    {
        extract($dados);
        require __DIR__ . '/../views/' . $caminho . '.php';
    }

    protected function redirecionar(string $url): void
    {
        header("Location: $url");
        exit;
    }
}
