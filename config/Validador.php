<?php

// Validacao e sanitizacao central. Cada controller usa isto em vez
// de confiar direto no que vem em $_POST.
class Validador
{
    public static function texto(?string $valor): string
    {
        return htmlspecialchars(trim($valor ?? ''), ENT_QUOTES, 'UTF-8');
    }

    public static function email(?string $valor): string|false
    {
        $valor = trim($valor ?? '');
        return filter_var($valor, FILTER_VALIDATE_EMAIL) ? $valor : false;
    }

    public static function inteiro(mixed $valor): int|false
    {
        return filter_var($valor, FILTER_VALIDATE_INT);
    }

    public static function decimal(mixed $valor): float|false
    {
        return filter_var($valor, FILTER_VALIDATE_FLOAT);
    }

    public static function obrigatorio(?string $valor): bool
    {
        return trim($valor ?? '') !== '';
    }
}
