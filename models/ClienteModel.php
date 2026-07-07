<?php

require_once __DIR__ . '/Model.php';

class ClienteModel extends Model
{
    protected string $tabela = 'clientes';

    // Conta os pedidos de cada cliente (o pedido em si ainda vai ser
    // construido no Dia 7, mas a consulta ja fica pronta para quando
    // a tabela pedidos tiver dados).
    public function todosComContagemPedidos(): array
    {
        $sql = 'SELECT c.*, COUNT(p.id) AS total_pedidos
                FROM clientes c
                LEFT JOIN pedidos p ON p.cliente_id = c.id
                GROUP BY c.id
                ORDER BY c.nome';

        return $this->pdo->query($sql)->fetchAll();
    }
}
