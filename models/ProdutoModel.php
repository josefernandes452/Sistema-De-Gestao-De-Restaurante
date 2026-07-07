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

    // Pesquisa por nome, codigo (o id do produto) e categoria, cada
    // filtro so entra na consulta se vier preenchido.
    public function pesquisar(?string $nome, ?int $codigo, ?int $categoriaId): array
    {
        $sql = 'SELECT p.*, c.nome AS categoria_nome
                FROM produtos p
                JOIN categorias c ON c.id = p.categoria_id
                WHERE 1 = 1';
        $parametros = [];

        if ($nome) {
            $sql .= ' AND p.nome LIKE ?';
            $parametros[] = "%$nome%";
        }

        if ($codigo) {
            $sql .= ' AND p.id = ?';
            $parametros[] = $codigo;
        }

        if ($categoriaId) {
            $sql .= ' AND p.categoria_id = ?';
            $parametros[] = $categoriaId;
        }

        $sql .= ' ORDER BY p.nome';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parametros);

        return $stmt->fetchAll();
    }
}
