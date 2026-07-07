<?php

// Cookie "lembrar-me": mantem o cliente logado depois de fechar o
// navegador. Nunca guardamos a password nem so o id do utilizador no
// cookie, porque isso seria facil de forjar. Em vez disso o cookie
// tem duas partes: um selector (serve so para encontrar a linha na
// base de dados) e um validador (o segredo em si). Na base de dados
// so fica guardado o hash do validador, tal como fazemos com a
// password, para que uma fuga de dados nao entregue os cookies validos.
class LembrarMe
{
    private const COOKIE = 'lembrar_me';
    private const DIAS_VALIDADE = 30;

    public static function criar(int $utilizadorId): void
    {
        $selector = bin2hex(random_bytes(9));
        $validador = bin2hex(random_bytes(32));

        (new UsuarioModel())->definirLembrarMe($utilizadorId, $selector, hash('sha256', $validador));

        self::gravarCookie($selector, $validador);
    }

    // Corre no arranque de cada pedido, antes de haver sessao. Se
    // vier um cookie valido, faz login automatico. O validador e
    // sempre trocado por um novo depois de usado, para que um cookie
    // copiado por alguem so sirva uma vez.
    public static function autoLogar(): void
    {
        if (Sessao::estaLogado() || empty($_COOKIE[self::COOKIE])) {
            return;
        }

        $partes = explode(':', $_COOKIE[self::COOKIE], 2);

        if (count($partes) !== 2) {
            self::apagarCookie();
            return;
        }

        [$selector, $validador] = $partes;
        $usuarioModel = new UsuarioModel();
        $utilizador = $usuarioModel->buscarPorSelectorLembrarMe($selector);

        if (!$utilizador || $utilizador['estado'] !== 'Ativo') {
            self::apagarCookie();
            return;
        }

        if (!hash_equals($utilizador['lembrar_validador_hash'], hash('sha256', $validador))) {
            // O validador nao bate certo com o que esta guardado: o
            // cookie pode ter sido roubado e ja usado por outra pessoa.
            // Por seguranca invalidamos o lembrar-me todo e a pessoa
            // tem de entrar com a senha outra vez.
            $usuarioModel->limparLembrarMe((int) $utilizador['id']);
            self::apagarCookie();
            return;
        }

        Sessao::logar($utilizador);
        self::criar((int) $utilizador['id']);
    }

    public static function esquecer(?int $utilizadorId): void
    {
        if ($utilizadorId) {
            (new UsuarioModel())->limparLembrarMe($utilizadorId);
        }

        self::apagarCookie();
    }

    private static function gravarCookie(string $selector, string $validador): void
    {
        setcookie(self::COOKIE, "$selector:$validador", [
            'expires' => time() + self::DIAS_VALIDADE * 24 * 3600,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    private static function apagarCookie(): void
    {
        setcookie(self::COOKIE, '', [
            'expires' => time() - 3600,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}
