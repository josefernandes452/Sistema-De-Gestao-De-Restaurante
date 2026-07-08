<?php

require_once __DIR__ . '/Model.php';

class ProdutoModel extends Model
{
    protected string $tabela = 'produtos';

    public function todosComCategoria(): array
    {
        $sql = 'SELECT p.*, c.nome AS categoria_nome
                FROM produtos p
                JOIN categorias c ON c.id = p.categoria_id
                ORDER BY p.nome';

        return $this->pdo->query($sql)->fetchAll();
    }

    // So os produtos que o cliente pode mesmo pedir, para usar no
    // cardapio publico (views/cliente/menu.php).
    public function disponiveis(): array
    {
        $sql = 'SELECT p.*, c.nome AS categoria_nome
                FROM produtos p
                JOIN categorias c ON c.id = p.categoria_id
                WHERE p.estado = ?
                ORDER BY c.nome, p.nome';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['Disponivel']);

        return $stmt->fetchAll();
    }

    // Pesquisa por nome, codigo (o id do produto) e categoria, cada
    // filtro so entra na consulta se vier preenchido. Chamado tanto
    // no carregamento normal da pagina (sem filtros, pagina 1) como
    // pelo endpoint AJAX de pesquisa em tempo real. Devolve os
    // produtos da pagina pedida mais os dados para desenhar a
    // paginacao (total de resultados e total de paginas).
    public function pesquisar(?string $nome, ?int $codigo, ?int $categoriaId, int $pagina = 1, int $porPagina = 10): array
    {
        $condicoes = 'WHERE 1 = 1';
        $parametros = [];

        if ($nome) {
            $condicoes .= ' AND p.nome LIKE ?';
            $parametros[] = "%$nome%";
        }

        if ($codigo) {
            $condicoes .= ' AND p.id = ?';
            $parametros[] = $codigo;
        }

        if ($categoriaId) {
            $condicoes .= ' AND p.categoria_id = ?';
            $parametros[] = $categoriaId;
        }

        $totalStmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM produtos p JOIN categorias c ON c.id = p.categoria_id $condicoes"
        );
        $totalStmt->execute($parametros);
        $total = (int) $totalStmt->fetchColumn();

        $pagina = max(1, $pagina);
        $porPagina = max(1, $porPagina);
        $offset = ($pagina - 1) * $porPagina;

        // LIMIT/OFFSET nao aceitam bind normal com prepared
        // statements nativos (PDO::ATTR_EMULATE_PREPARES esta
        // desligado). Como os dois valores ja passaram por (int) em
        // PHP, meter direto na string e seguro, nao vem de input cru.
        $sql = "SELECT p.*, c.nome AS categoria_nome
                FROM produtos p
                JOIN categorias c ON c.id = p.categoria_id
                $condicoes
                ORDER BY p.nome
                LIMIT $porPagina OFFSET $offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parametros);

        return [
            'produtos' => $stmt->fetchAll(),
            'total' => $total,
            'totalPaginas' => max(1, (int) ceil($total / $porPagina)),
            'paginaAtual' => $pagina,
        ];
    }
}
