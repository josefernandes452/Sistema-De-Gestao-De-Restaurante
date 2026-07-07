<?php

require_once __DIR__ . '/Model.php';

class ReservaModel extends Model
{
    protected string $tabela = 'reservas';

    // Lista para o painel do operador, ja com o numero da mesa e o
    // nome do cliente com conta (se a reserva tiver vindo de um).
    public function todosComDetalhes(): array
    {
        $sql = 'SELECT r.*, m.numero AS mesa_numero, c.nome AS cliente_nome
                FROM reservas r
                JOIN mesas m ON m.id = r.mesa_id
                LEFT JOIN clientes c ON c.id = r.cliente_id
                ORDER BY r.data DESC, r.hora DESC';

        return $this->pdo->query($sql)->fetchAll();
    }

    // Verifica se ja existe outra reserva confirmada para a mesma
    // mesa, no mesmo dia e hora. Reservas canceladas nao contam,
    // porque essa mesa/horario voltou a ficar livre.
    public function existeConflito(int $mesaId, string $data, string $hora): bool
    {
        $sql = "SELECT id FROM reservas
                WHERE mesa_id = ?
                AND data = ?
                AND hora = ?
                AND estado != 'Cancelada'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$mesaId, $data, $hora]);

        return $stmt->fetch() !== false;
    }
}
