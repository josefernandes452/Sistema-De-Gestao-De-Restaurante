<?php

// Protecao CSRF simples: gera um token por sessao e confere se o
// formulario submetido trouxe o mesmo token de volta.
class Csrf
{
    public static function token(): string
    {
        Sessao::iniciar();

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public static function campo(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::token(), ENT_QUOTES) . '">';
    }

    public static function validar(?string $tokenRecebido): bool
    {
        Sessao::iniciar();

        return !empty($tokenRecebido)
            && !empty($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $tokenRecebido);
    }
}
