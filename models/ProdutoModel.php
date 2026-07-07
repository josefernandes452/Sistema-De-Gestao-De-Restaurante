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
}
