<?php

require_once __DIR__ . '/Model.php';

class CategoriaModel extends Model
{
    protected string $tabela = 'categorias';

    public function todasComContagemProdutos(): array
    {
        $sql = 'SELECT c.*, COUNT(p.id) AS total_produtos
                FROM categorias c
                LEFT JOIN produtos p ON p.categoria_id = c.id
                GROUP BY c.id
                ORDER BY c.nome';

        return $this->pdo->query($sql)->fetchAll();
    }

    public function estaEmUso(int $id): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM produtos WHERE categoria_id = ?');
        $stmt->execute([$id]);

        return (int) $stmt->fetchColumn() > 0;
    }
}
