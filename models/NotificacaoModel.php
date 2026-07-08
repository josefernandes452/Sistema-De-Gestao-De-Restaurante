<?php

require_once __DIR__ . '/Model.php';

class NotificacaoModel extends Model
{
    protected string $tabela = 'notificacoes';

    public function recentesPorUtilizador(int $utilizadorId, int $limite = 6): array
    {
        $limite = max(1, $limite);

        $stmt = $this->pdo->prepare(
            "SELECT * FROM notificacoes WHERE utilizador_id = ? ORDER BY criado_em DESC LIMIT $limite"
        );
        $stmt->execute([$utilizadorId]);

        return $stmt->fetchAll();
    }

    public function contarNaoLidas(int $utilizadorId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM notificacoes WHERE utilizador_id = ? AND lida = 0');
        $stmt->execute([$utilizadorId]);

        return (int) $stmt->fetchColumn();
    }

    public function marcarTodasComoLidas(int $utilizadorId): void
    {
        $stmt = $this->pdo->prepare('UPDATE notificacoes SET lida = 1 WHERE utilizador_id = ?');
        $stmt->execute([$utilizadorId]);
    }

    // Cria a mesma notificacao para todos os utilizadores ativos que
    // tenham um dos perfis indicados. Usado para avisar Administrador
    // e Operador de algo que aconteceu do lado do cliente (pedido
    // novo, reserva nova), sem eles terem de andar a verificar as
    // listas manualmente.
    public function criarParaPerfis(array $perfis, string $mensagem, ?string $link = null): void
    {
        $marcadores = implode(',', array_fill(0, count($perfis), '?'));

        $stmt = $this->pdo->prepare(
            "SELECT u.id FROM utilizadores u
             JOIN perfis p ON p.id = u.perfil_id
             WHERE p.nome IN ($marcadores) AND u.estado = 'Ativo'"
        );
        $stmt->execute($perfis);
        $utilizadorIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $inserir = $this->pdo->prepare(
            'INSERT INTO notificacoes (utilizador_id, mensagem, link, lida, criado_em) VALUES (?, ?, ?, 0, NOW())'
        );

        foreach ($utilizadorIds as $utilizadorId) {
            $inserir->execute([$utilizadorId, $mensagem, $link]);
        }
    }
}
