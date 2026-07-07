<?php

require_once __DIR__ . '/Model.php';

class PedidoModel extends Model
{
    protected string $tabela = 'pedidos';

    // Lista os pedidos com o numero da mesa, o nome do cliente (se
    // houver) e os itens de cada um, tudo pronto para a tela de
    // gestao de pedidos.
    public function todosComDetalhes(): array
    {
        $sql = 'SELECT p.*, m.numero AS mesa_numero, c.nome AS cliente_nome
                FROM pedidos p
                JOIN mesas m ON m.id = p.mesa_id
                LEFT JOIN clientes c ON c.id = p.cliente_id
                ORDER BY p.criado_em DESC';

        $pedidos = $this->pdo->query($sql)->fetchAll();

        if (!$pedidos) {
            return [];
        }

        $ids = array_column($pedidos, 'id');
        $marcadores = implode(',', array_fill(0, count($ids), '?'));

        $stmt = $this->pdo->prepare(
            "SELECT ip.*, pr.nome AS produto_nome
             FROM itens_pedido ip
             JOIN produtos pr ON pr.id = ip.produto_id
             WHERE ip.pedido_id IN ($marcadores)"
        );
        $stmt->execute($ids);
        $itens = $stmt->fetchAll();

        $itensPorPedido = [];
        foreach ($itens as $item) {
            $itensPorPedido[$item['pedido_id']][] = $item;
        }

        foreach ($pedidos as &$pedido) {
            $pedido['itens'] = $itensPorPedido[$pedido['id']] ?? [];
        }

        return $pedidos;
    }

    // Grava o pedido e os seus itens numa unica transacao: ou fica
    // tudo gravado, ou nada fica (se um item falhar a meio, o
    // pedido nao fica gravado sozinho e sem itens).
    public function criarComItens(array $dadosPedido, array $itens): int
    {
        $total = 0;
        foreach ($itens as $item) {
            $total += $item['preco_unitario'] * $item['quantidade'];
        }
        $dadosPedido['total'] = $total;

        $this->pdo->beginTransaction();

        try {
            $pedidoId = $this->inserir($dadosPedido);

            $stmt = $this->pdo->prepare(
                'INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario, subtotal)
                 VALUES (?, ?, ?, ?, ?)'
            );

            foreach ($itens as $item) {
                $subtotal = $item['preco_unitario'] * $item['quantidade'];
                $stmt->execute([$pedidoId, $item['produto_id'], $item['quantidade'], $item['preco_unitario'], $subtotal]);
            }

            $this->pdo->commit();

            return $pedidoId;
        } catch (Throwable $erro) {
            $this->pdo->rollBack();
            throw $erro;
        }
    }
}
