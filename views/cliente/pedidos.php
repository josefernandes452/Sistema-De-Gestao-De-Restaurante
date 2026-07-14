<?php
require_once __DIR__ . "/../../inicializar.php";
$utilizadorLogado = Sessao::exigirPerfil("Cliente");

$produtoModel = new ProdutoModel();
$mesaModel = new MesaModel();
$clienteModel = new ClienteModel();
$reservaModel = new ReservaModel();

$produtosMenu = array_map(
    fn (array $p) => [
        'id' => (int) $p['id'],
        'nome' => $p['nome'],
        'categoria' => 'todos',
        'preco' => (float) $p['preco'],
        'descricao' => $p['descricao'],
        'imagem' => $p['imagem'] ? '../../assets/uploads/' . $p['imagem'] : null,
    ],
    $produtoModel->disponiveis()
);

$mesasLivres = $mesaModel->livres();

// Se o cliente ja tem uma reserva confirmada para hoje, a mesa dela
// aparece pre-selecionada, para ele nao ter de a escolher outra vez
// depois de ja a ter marcado. A mesa reservada entra na lista mesmo
// que por algum motivo nao esteja em "livres" neste momento.
$cliente = $clienteModel->buscarPorUtilizadorId($utilizadorLogado['id']);
$reservaHoje = $cliente ? $reservaModel->buscarConfirmadaHojeParaCliente($cliente['id']) : false;

$mesasParaEscolher = $mesasLivres;
if ($reservaHoje && !in_array((int) $reservaHoje['mesa_id'], array_column($mesasLivres, 'id'), true)) {
    array_unshift($mesasParaEscolher, [
        'id' => $reservaHoje['mesa_id'],
        'numero' => $reservaHoje['mesa_numero'],
        'capacidade' => $reservaHoje['mesa_capacidade'],
    ]);
}

$flash = Sessao::consumirFlash();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos - Sabor Alma</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <!-- HEADER -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: var(--verde-escuro);">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="../../assets/img/logo.png" alt="Sabor Alma" height="40">
                <span class="fw-bold ms-2" style="color: var(--dourado);">Sabor Alma</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSite">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSite">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Início</a></li>
                    <li class="nav-item"><a class="nav-link" href="menu.php">Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="reservas.php">Reservas</a></li>
                    <li class="nav-item"><a class="nav-link active" href="pedidos.php">Pedidos</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Entrar</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- PEDIDOS -->
    <section style="padding-top: 100px; padding-bottom: 60px; background: var(--creme);">
        <div class="container">
            <h2 class="text-center fw-bold" style="color: var(--verde-escuro);">
                <i class="fas fa-shopping-bag" style="color: var(--dourado);"></i> Faça seu Pedido
            </h2>
            <p class="text-center text-muted mb-4">Escolha os itens e faça o seu pedido</p>

            <?php if ($flash): ?>
                <div class="row justify-content-center mb-3">
                    <div class="col-lg-8">
                        <div class="alert alert-<?= $flash['tipo'] === 'erro' ? 'danger' : 'success' ?>" role="alert">
                            <?= htmlspecialchars($flash['mensagem']) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form id="formPedidoCliente" method="post" action="/index.php?rota=pedidos.criar-cliente">
                <?= Csrf::campo() ?>
                <div id="itensPedidoClienteEscondidos"></div>

                <div class="row">
                    <!-- Lista de Produtos -->
                    <div class="col-lg-8">
                        <div class="row g-3" id="listaPedidos">
                            <!-- Itens via JavaScript -->
                        </div>
                    </div>

                    <!-- Carrinho -->
                    <div class="col-lg-4">
                        <div class="card shadow-lg border-0 rounded-4 p-3 sticky-top" style="top: 100px;">
                            <h5 class="fw-bold" style="color: var(--verde-escuro);">
                                <i class="fas fa-shopping-cart" style="color: var(--dourado);"></i> Meu Pedido
                            </h5>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small">A tua mesa</label>
                                <?php if ($reservaHoje): ?>
                                    <p class="text-success small mb-2">
                                        <i class="fas fa-circle-check me-1"></i>
                                        Já tens uma reserva hoje às <?= htmlspecialchars(substr($reservaHoje['hora'], 0, 5)) ?> na Mesa <?= htmlspecialchars($reservaHoje['mesa_numero']) ?>. Já a selecionámos por ti.
                                    </p>
                                <?php endif; ?>
                                <?php if (empty($mesasParaEscolher)): ?>
                                    <p class="text-danger small mb-0">Sem mesas livres neste momento. Tenta novamente daqui a pouco.</p>
                                <?php else: ?>
                                    <select class="form-select" name="mesa_id" required>
                                        <option value="">Seleciona a tua mesa</option>
                                        <?php foreach ($mesasParaEscolher as $m): ?>
                                            <option value="<?= $m['id'] ?>" <?= ($reservaHoje && (int) $reservaHoje['mesa_id'] === (int) $m['id']) ? 'selected' : '' ?>>
                                                Mesa <?= $m['numero'] ?> (<?= $m['capacidade'] ?> pessoas)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                            </div>

                            <div id="carrinhoItems" class="mb-3">
                                <p class="text-muted text-center">Seu carrinho está vazio</p>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total:</span>
                                <span id="totalPedido" style="color: var(--dourado);">Kz 0,00</span>
                            </div>
                            <button type="button" class="btn btn-primary w-100 mt-3" onclick="finalizarPedido()" <?= empty($mesasParaEscolher) ? 'disabled' : '' ?>>
                                <i class="fas fa-check me-2"></i> Finalizar Pedido
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script>
        // Mesmo esquema do menu.php: troca os dados de exemplo pelos
        // produtos reais da base de dados.
        menuData = <?= json_encode($produtosMenu, JSON_UNESCAPED_UNICODE) ?>;
    </script>
</body>
</html>