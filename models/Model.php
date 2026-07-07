<?php

// Classe base para todos os models. Cada model so precisa de dizer
// qual e a sua tabela; o resto (inserir, atualizar, eliminar, buscar)
// e sempre a mesma logica, entao fica aqui uma vez so.
abstract class Model
{
    protected PDO $pdo;
    protected string $tabela;

    public function __construct()
    {
        $this->pdo = Database::getConexao();
    }

    public function todos(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->tabela}");
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->tabela} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function inserir(array $dados): int
    {
        $colunas = array_keys($dados);
        $marcadores = array_map(fn ($c) => ":$c", $colunas);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->tabela,
            implode(', ', $colunas),
            implode(', ', $marcadores)
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($dados);

        return (int) $this->pdo->lastInsertId();
    }

    public function atualizar(int $id, array $dados): bool
    {
        $sets = implode(', ', array_map(fn ($c) => "$c = :$c", array_keys($dados)));
        $dados['id'] = $id;

        $stmt = $this->pdo->prepare("UPDATE {$this->tabela} SET $sets WHERE id = :id");

        return $stmt->execute($dados);
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->tabela} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
