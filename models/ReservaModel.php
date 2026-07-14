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

    // Usado em pedidos.php: se o cliente ja tem uma reserva
    // confirmada para hoje, a mesa dele aparece pre-selecionada no
    // formulario de pedido, em vez de ele ter de a escolher outra
    // vez do zero. Se houver mais de uma reserva no mesmo dia
    // (raro), fica com a mais proxima da hora atual.
    public function buscarConfirmadaHojeParaCliente(int $clienteId): array|false
    {
        $sql = "SELECT r.*, m.numero AS mesa_numero, m.capacidade AS mesa_capacidade
                FROM reservas r
                JOIN mesas m ON m.id = r.mesa_id
                WHERE r.cliente_id = ?
                AND r.data = CURDATE()
                AND r.estado = 'Confirmada'
                ORDER BY ABS(TIME_TO_SEC(TIMEDIFF(r.hora, CURTIME())))
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$clienteId]);

        return $stmt->fetch();
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

    // Um cliente so pode ter uma reserva por dia, independente da
    // mesa ou da hora escolhida. Quem tem conta e identificado pelo
    // cliente_id (mais fiavel); quem reserva sem conta e identificado
    // pelo telefone, que e o unico dado estavel que temos dele.
    // Reservas canceladas nao contam, porque libertam esse dia.
    public function existeReservaNoDiaPara(?int $clienteId, string $telefone, string $data): bool
    {
        if ($clienteId) {
            $sql = "SELECT id FROM reservas WHERE cliente_id = ? AND data = ? AND estado != 'Cancelada'";
            $params = [$clienteId, $data];
        } else {
            $sql = "SELECT id FROM reservas WHERE cliente_id IS NULL AND telefone = ? AND data = ? AND estado != 'Cancelada'";
            $params = [$telefone, $data];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch() !== false;
    }
}
