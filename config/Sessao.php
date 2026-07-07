<?php

// Tudo o que precisa de sessao (login, perfil, mensagens flash) passa por aqui.
class Sessao
{
    public static function iniciar(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function logar(array $utilizador): void
    {
        self::iniciar();
        session_regenerate_id(true);

        $_SESSION['utilizador'] = [
            'id' => $utilizador['id'],
            'nome' => $utilizador['nome'],
            'email' => $utilizador['email'],
            'telefone' => $utilizador['telefone'],
            'perfil' => $utilizador['perfil_nome'],
        ];
    }

    public static function utilizadorAtual(): ?array
    {
        self::iniciar();
        return $_SESSION['utilizador'] ?? null;
    }

    public static function estaLogado(): bool
    {
        return self::utilizadorAtual() !== null;
    }

    // Redireciona para o login se nao houver sessao, ou se o perfil
    // da sessao nao estiver na lista de perfis permitidos.
    public static function exigirPerfil(string ...$perfis): array
    {
        $utilizador = self::utilizadorAtual();

        if ($utilizador === null || (!empty($perfis) && !in_array($utilizador['perfil'], $perfis, true))) {
            header('Location: /views/cliente/login.php');
            exit;
        }

        return $utilizador;
    }

    public static function sair(): void
    {
        self::iniciar();
        $_SESSION = [];
        session_destroy();
    }

    public static function flash(string $tipo, string $mensagem): void
    {
        self::iniciar();
        $_SESSION['flash'] = ['tipo' => $tipo, 'mensagem' => $mensagem];
    }

    // So devolve a mensagem uma vez, depois ela desaparece.
    public static function consumirFlash(): ?array
    {
        self::iniciar();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $flash;
    }
}
