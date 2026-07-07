<?php

// Validacao e sanitizacao central. Cada controller usa isto em vez
// de confiar direto no que vem em $_POST.
class Validador
{
    // So limpa espacos, nao escapa HTML aqui. O escape fica para a
    // hora de mostrar o texto na view (htmlspecialchars), que e onde
    // isso realmente importa. Escapar aqui tambem guardava o texto
    // ja convertido na base de dados, e ao mostrar de novo com
    // htmlspecialchars ficava escapado 2 vezes (um "&" virava "&amp;amp;").
    public static function texto(?string $valor): string
    {
        return trim($valor ?? '');
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
