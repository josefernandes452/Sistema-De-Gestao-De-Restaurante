<?php

require_once __DIR__ . '/Model.php';

class MesaModel extends Model
{
    protected string $tabela = 'mesas';

    public function todos(): array
    {
        return $this->pdo->query('SELECT * FROM mesas ORDER BY numero')->fetchAll();
    }

    // Mesas que um cliente pode escolher ao fazer um pedido pelo site,
    // so as que estao livres neste momento.
    public function livres(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM mesas WHERE estado = 'Livre' ORDER BY numero");
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function numeroEmUso(int $numero, ?int $ignorarId = null): bool
    {
        $sql = 'SELECT id FROM mesas WHERE numero = ?';
        $params = [$numero];

        if ($ignorarId !== null) {
            $sql .= ' AND id != ?';
            $params[] = $ignorarId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch() !== false;
    }
}
