<?php

// Nao estende Model porque um relatorio nao e uma tabela so, e uma
// combinacao de varias. Aqui e so consultas de leitura mesmo.
class RelatorioModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConexao();
    }

    public function produtosMaisVendidos(string $dataInicio, string $dataFim): array
    {
        $sql = 'SELECT pr.nome, SUM(ip.quantidade) AS total_vendido, SUM(ip.subtotal) AS total_faturado
                FROM itens_pedido ip
                JOIN produtos pr ON pr.id = ip.produto_id
                JOIN pedidos p ON p.id = ip.pedido_id
                WHERE DATE(p.criado_em) BETWEEN ? AND ?
                GROUP BY pr.id, pr.nome
                ORDER BY total_vendido DESC
                LIMIT 10';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$dataInicio, $dataFim]);

        return $stmt->fetchAll();
    }

    public function vendasPorPeriodo(string $dataInicio, string $dataFim): array
    {
        $sql = 'SELECT DATE(criado_em) AS dia, COUNT(*) AS total_pedidos, SUM(total) AS total_vendido
                FROM pedidos
                WHERE DATE(criado_em) BETWEEN ? AND ?
                GROUP BY DATE(criado_em)
                ORDER BY dia';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$dataInicio, $dataFim]);

        return $stmt->fetchAll();
    }

    public function desempenhoPorOperador(string $dataInicio, string $dataFim): array
    {
        $sql = "SELECT u.nome AS operador, COUNT(p.id) AS total_pedidos, COALESCE(SUM(p.total), 0) AS total_vendido
                FROM pedidos p
                JOIN utilizadores u ON u.id = p.utilizador_id
                WHERE DATE(p.criado_em) BETWEEN ? AND ?
                GROUP BY u.id, u.nome
                ORDER BY total_vendido DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$dataInicio, $dataFim]);

        return $stmt->fetchAll();
    }
}
