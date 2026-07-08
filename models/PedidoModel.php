<?php

require_once __DIR__ . '/Model.php';

class PedidoModel extends Model
{
    protected string $tabela = 'pedidos';

    // Lista os pedidos com o numero da mesa, o nome do cliente (se
    // houver) e os itens de cada um, tudo pronto para a tela de
    // gestao de pedidos. Os filtros (codigo do pedido, intervalo de
    // datas) so entram na consulta se vierem preenchidos. Devolve ja
    // paginado, junto com o total de resultados e de paginas.
    public function todosComDetalhes(?int $codigo = null, ?string $dataInicio = null, ?string $dataFim = null, int $pagina = 1, int $porPagina = 10): array
    {
        $condicoes = 'WHERE 1 = 1';
        $parametros = [];

        if ($codigo) {
            $condicoes .= ' AND p.id = ?';
            $parametros[] = $codigo;
        }

        if ($dataInicio) {
            $condicoes .= ' AND DATE(p.criado_em) >= ?';
            $parametros[] = $dataInicio;
        }

        if ($dataFim) {
            $condicoes .= ' AND DATE(p.criado_em) <= ?';
            $parametros[] = $dataFim;
        }

        $totalStmt = $this->pdo->prepare("SELECT COUNT(*) FROM pedidos p $condicoes");
        $totalStmt->execute($parametros);
        $total = (int) $totalStmt->fetchColumn();

        $pagina = max(1, $pagina);
        $porPagina = max(1, $porPagina);
        $offset = ($pagina - 1) * $porPagina;

        // LIMIT/OFFSET direto na string: os dois ja passaram por
        // (int) em PHP, entao e seguro (ver o mesmo comentario em
        // ProdutoModel::pesquisar).
        $sql = "SELECT p.*, m.numero AS mesa_numero, c.nome AS cliente_nome
                FROM pedidos p
                JOIN mesas m ON m.id = p.mesa_id
                LEFT JOIN clientes c ON c.id = p.cliente_id
                $condicoes
                ORDER BY p.criado_em DESC
                LIMIT $porPagina OFFSET $offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parametros);
        $pedidos = $stmt->fetchAll();

        if ($pedidos) {
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
        }

        return [
            'pedidos' => $pedidos,
            'total' => $total,
            'totalPaginas' => max(1, (int) ceil($total / $porPagina)),
            'paginaAtual' => $pagina,
        ];
    }

    // Usado na tela de acompanhamento do cliente: um pedido so, com
    // o numero da mesa e os itens, para mostrar o estado real.
    public function buscarComItens(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.*, m.numero AS mesa_numero
             FROM pedidos p
             JOIN mesas m ON m.id = p.mesa_id
             WHERE p.id = ?'
        );
        $stmt->execute([$id]);
        $pedido = $stmt->fetch();

        if (!$pedido) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            'SELECT ip.*, pr.nome AS produto_nome
             FROM itens_pedido ip
             JOIN produtos pr ON pr.id = ip.produto_id
             WHERE ip.pedido_id = ?'
        );
        $stmt->execute([$id]);
        $pedido['itens'] = $stmt->fetchAll();

        return $pedido;
    }

    // Historico de pedidos de um cliente, para o perfil dele
    // (views/cliente/perfil-cliente.php). Do mais recente para o mais antigo.
    public function porCliente(int $clienteId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.*, m.numero AS mesa_numero
             FROM pedidos p
             JOIN mesas m ON m.id = p.mesa_id
             WHERE p.cliente_id = ?
             ORDER BY p.criado_em DESC'
        );
        $stmt->execute([$clienteId]);

        return $stmt->fetchAll();
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
