<?php

require_once __DIR__ . '/Model.php';

class PagamentoModel extends Model
{
    protected string $tabela = 'pagamentos';

    public function todosComDetalhes(): array
    {
        $sql = 'SELECT pg.*, p.total AS pedido_total, m.numero AS mesa_numero, c.nome AS cliente_nome
                FROM pagamentos pg
                JOIN pedidos p ON p.id = pg.pedido_id
                JOIN mesas m ON m.id = p.mesa_id
                LEFT JOIN clientes c ON c.id = p.cliente_id
                ORDER BY pg.criado_em DESC';

        return $this->pdo->query($sql)->fetchAll();
    }

    public function totalPagoDoPedido(int $pedidoId): float
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(valor), 0) FROM pagamentos WHERE pedido_id = ? AND estado = 'Pago'"
        );
        $stmt->execute([$pedidoId]);

        return (float) $stmt->fetchColumn();
    }
}
