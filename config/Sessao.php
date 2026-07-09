<?php

// Tudo o que precisa de sessao (login, perfil, mensagens flash) passa por aqui.
class Sessao
{
    public static function iniciar(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // HttpOnly tira o cookie de sessao do alcance do JavaScript
            // (mesmo que algum dia escape um XSS por ali, o cookie fica
            // protegido). SameSite=Lax ajuda contra CSRF vindo de outros
            // sites. O "secure" fica de fora porque em desenvolvimento
            // corremos em HTTP simples, mas devia ligar-se numa hospedagem
            // a serio com HTTPS.
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
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

        self::exigirCertificadoSeForAreaAdministrativa($utilizador, $perfis);

        return $utilizador;
    }

    // Camada extra so para a area administrativa: quando o acesso e
    // feito por HTTPS (https://saboralma.local, configurado no Apache
    // com SSLVerifyClient require em /views/admin), confirmamos que o
    // certificado apresentado pertence mesmo a pessoa que fez login,
    // nao so um certificado qualquer emitido pela mesma CA. Isto fica
    // parado (nao faz nada) quando se acede por HTTP normal, para nao
    // bloquear o desenvolvimento/testes do dia a dia, que continuam a
    // funcionar so com password como sempre funcionaram.
    private static function exigirCertificadoSeForAreaAdministrativa(array $utilizador, array $perfis): void
    {
        $ehAreaAdministrativa = in_array('Administrador', $perfis, true) || in_array('Operador', $perfis, true);
        $ligacaoSegura = ($_SERVER['HTTPS'] ?? 'off') === 'on';

        if (!$ehAreaAdministrativa || !$ligacaoSegura) {
            return;
        }

        $certificadoValido = ($_SERVER['SSL_CLIENT_VERIFY'] ?? '') === 'SUCCESS';
        $nomeNoCertificado = $_SERVER['SSL_CLIENT_S_DN_CN'] ?? null;

        if (!$certificadoValido || $nomeNoCertificado !== $utilizador['nome']) {
            self::sair();
            self::flash('erro', 'A area administrativa exige um certificado digital valido, correspondente a tua conta.');
            header('Location: /views/cliente/login.php');
            exit;
        }
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
