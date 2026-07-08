<?php

require_once __DIR__ . '/Model.php';

class ClienteModel extends Model
{
    protected string $tabela = 'clientes';

    // Conta os pedidos de cada cliente. Devolve ja paginado, junto
    // com o total de clientes e de paginas.
    public function todosComContagemPedidos(int $pagina = 1, int $porPagina = 10): array
    {
        $total = (int) $this->pdo->query('SELECT COUNT(*) FROM clientes')->fetchColumn();

        $pagina = max(1, $pagina);
        $porPagina = max(1, $porPagina);
        $offset = ($pagina - 1) * $porPagina;

        // LIMIT/OFFSET direto na string: os dois ja passaram por
        // (int) em PHP, entao e seguro (ver o mesmo comentario em
        // ProdutoModel::pesquisar).
        $sql = "SELECT c.*, COUNT(p.id) AS total_pedidos
                FROM clientes c
                LEFT JOIN pedidos p ON p.cliente_id = c.id
                GROUP BY c.id
                ORDER BY c.nome
                LIMIT $porPagina OFFSET $offset";

        return [
            'clientes' => $this->pdo->query($sql)->fetchAll(),
            'total' => $total,
            'totalPaginas' => max(1, (int) ceil($total / $porPagina)),
            'paginaAtual' => $pagina,
        ];
    }

    public function buscarPorUtilizadorId(int $utilizadorId): array|false
    {
        $stmt = $this->pdo->prepare('SELECT * FROM clientes WHERE utilizador_id = ?');
        $stmt->execute([$utilizadorId]);

        return $stmt->fetch();
    }

    public function buscarPorEmail(string $email): array|false
    {
        $stmt = $this->pdo->prepare('SELECT * FROM clientes WHERE email = ?');
        $stmt->execute([$email]);

        return $stmt->fetch();
    }

    // Chamado quando um Cliente cria conta em registo.php. Se ja
    // existir uma linha em clientes com o mesmo email (por exemplo,
    // porque um operador ja tinha registado esse cliente manualmente
    // num pedido antigo), liga essa linha a conta nova em vez de
    // duplicar. Caso contrario cria uma linha nova ja ligada.
    public function criarOuLigarAUtilizador(int $utilizadorId, string $nome, string $email, string $telefone): int
    {
        $existente = $this->buscarPorEmail($email);

        if ($existente) {
            $this->atualizar((int) $existente['id'], ['utilizador_id' => $utilizadorId]);

            return (int) $existente['id'];
        }

        return $this->inserir([
            'utilizador_id' => $utilizadorId,
            'nome' => $nome,
            'email' => $email,
            'telefone' => $telefone,
        ]);
    }
}
