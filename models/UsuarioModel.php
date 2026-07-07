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

    public function idDoPerfilCliente(): int
    {
        $stmt = $this->pdo->query("SELECT id FROM perfis WHERE nome = 'Cliente'");
        return (int) $stmt->fetchColumn();
    }

    public function atualizarSenha(int $id, string $novaSenha): bool
    {
        return $this->atualizar($id, [
            'senha' => password_hash($novaSenha, PASSWORD_DEFAULT),
            'token_recuperacao' => null,
        ]);
    }
}
