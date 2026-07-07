<?php

require_once __DIR__ . '/Model.php';

class UsuarioModel extends Model
{
    protected string $tabela = 'utilizadores';

    // Traz o utilizador junto com o nome do perfil (join com perfis),
    // porque o login precisa de saber se e Administrador, Operador ou Cliente.
    public function buscarPorEmailComPerfil(string $email): array|false
    {
        $sql = 'SELECT u.*, p.nome AS perfil_nome
                FROM utilizadores u
                JOIN perfis p ON p.id = u.perfil_id
                WHERE u.email = ?';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);

        return $stmt->fetch();
    }

    public function emailExiste(string $email): bool
    {
        $stmt = $this->pdo->prepare('SELECT id FROM utilizadores WHERE email = ?');
        $stmt->execute([$email]);

        return $stmt->fetch() !== false;
    }

    public function idDoPerfil(string $nomePerfil): ?int
    {
        $stmt = $this->pdo->prepare('SELECT id FROM perfis WHERE nome = ?');
        $stmt->execute([$nomePerfil]);
        $id = $stmt->fetchColumn();

        return $id !== false ? (int) $id : null;
    }

    public function idDoPerfilCliente(): int
    {
        return $this->idDoPerfil('Cliente');
    }

    public function atualizarSenha(int $id, string $novaSenha): bool
    {
        return $this->atualizar($id, [
            'senha' => password_hash($novaSenha, PASSWORD_DEFAULT),
            'token_recuperacao' => null,
            'token_recuperacao_expira' => null,
        ]);
    }

    public function definirTokenRecuperacao(int $id, string $token): bool
    {
        return $this->atualizar($id, [
            'token_recuperacao' => $token,
            // 1 hora de validade. Depois disso o link do email deixa de funcionar.
            'token_recuperacao_expira' => date('Y-m-d H:i:s', time() + 3600),
        ]);
    }

    // So devolve o utilizador se o token existir e ainda nao tiver expirado.
    public function buscarPorToken(string $token): array|false
    {
        $sql = 'SELECT * FROM utilizadores
                WHERE token_recuperacao = ?
                AND token_recuperacao_expira > NOW()';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$token]);

        return $stmt->fetch();
    }

    // Traz o utilizador pelo selector do cookie "lembrar-me". So devolve
    // se ainda estiver dentro do prazo, o resto da validacao (o validador
    // em si) fica a cargo da classe LembrarMe.
    public function buscarPorSelectorLembrarMe(string $selector): array|false
    {
        $sql = 'SELECT u.*, p.nome AS perfil_nome
                FROM utilizadores u
                JOIN perfis p ON p.id = u.perfil_id
                WHERE u.lembrar_selector = ?
                AND u.lembrar_expira > NOW()';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$selector]);

        return $stmt->fetch();
    }

    public function definirLembrarMe(int $id, string $selector, string $validadorHash): bool
    {
        return $this->atualizar($id, [
            'lembrar_selector' => $selector,
            'lembrar_validador_hash' => $validadorHash,
            // 30 dias de validade para o login automatico.
            'lembrar_expira' => date('Y-m-d H:i:s', time() + 30 * 24 * 3600),
        ]);
    }

    public function limparLembrarMe(int $id): bool
    {
        return $this->atualizar($id, [
            'lembrar_selector' => null,
            'lembrar_validador_hash' => null,
            'lembrar_expira' => null,
        ]);
    }

    // Lista para a tela de gestao de utilizadores, ja com o nome do perfil.
    public function todosComPerfil(): array
    {
        $sql = 'SELECT u.*, p.nome AS perfil_nome
                FROM utilizadores u
                JOIN perfis p ON p.id = u.perfil_id
                ORDER BY u.nome';

        return $this->pdo->query($sql)->fetchAll();
    }

    public function todosPerfis(): array
    {
        return $this->pdo->query('SELECT id, nome FROM perfis ORDER BY id')->fetchAll();
    }
}
