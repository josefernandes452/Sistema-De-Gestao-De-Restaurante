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
}
