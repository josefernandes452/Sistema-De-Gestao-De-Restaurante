<?php
require_once __DIR__ . "/../../inicializar.php";
$utilizadorLogado = Sessao::exigirPerfil("Cliente");

$clienteModel = new ClienteModel();
$pedidoModel = new PedidoModel();

$cliente = $clienteModel->buscarPorUtilizadorId($utilizadorLogado['id']);
$id = Validador::inteiro($_GET['id'] ?? '');
$pedido = $id ? $pedidoModel->buscarComItens($id) : false;

// So mostra o pedido se ele for mesmo do cliente logado. Sem isto,
// bastava mudar o numero na URL para veres o pedido de outra pessoa.
if (!$pedido || !$cliente || (int) $pedido['cliente_id'] !== (int) $cliente['id']) {
    Sessao::flash('erro', 'Nao encontramos esse pedido.');
    header('Location: /views/cliente/pedidos.php');
    exit;
}

// Os passos visiveis no ecra sao so 4 (Recebido, Preparando, Pronto,
// Entregue). "Cancelado" nao entra nessa barra, mostra um aviso a parte.
$mapaPasso = ['Pendente' => 0, 'Em Preparacao' => 1, 'Pronto' => 2, 'Entregue' => 3];
$passoAtual = $mapaPasso[$pedido['estado']] ?? null;
$rotulos = ['Recebido', 'Preparando', 'Pronto', 'Entregue'];

$total = 0;
foreach ($pedido['itens'] as $item) {
    $total += (float) $item['subtotal'];
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acompanhamento - Sabor Alma</title>

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

    <!-- ACOMPANHAMENTO -->
    <section style="padding-top: 100px; padding-bottom: 60px; background: var(--creme); min-height: 100vh;">
        <div class="container">
            <h2 class="text-center fw-bold" style="color: var(--verde-escuro);">
                <i class="fas fa-clock" style="color: var(--dourado);"></i> Acompanhamento do Pedido
            </h2>
            <p class="text-center text-muted mb-4">Acompanhe o status do seu pedido em tempo real</p>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-lg border-0 rounded-4 p-4" id="cardPedido" data-pedido-id="<?= $pedido['id'] ?>" data-estado="<?= htmlspecialchars($pedido['estado']) ?>">
                        <div class="text-center mb-4">
                            <h4 class="fw-bold">Pedido #<?= $pedido['id'] ?> &middot; Mesa <?= $pedido['mesa_numero'] ?></h4>
                            <p class="text-muted">Atualiza sozinho a cada poucos segundos</p>
                        </div>

                        <?php if ($pedido['estado'] === 'Cancelado'): ?>
                            <div class="alert alert-danger text-center">
                                <i class="fas fa-times-circle me-2"></i> Este pedido foi cancelado.
                            </div>
                        <?php else: ?>
                            <!-- Status -->
                            <div class="position-relative">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <?php foreach ($rotulos as $i => $rotulo): ?>
                                        <?php
                                            $concluido = $i <= $passoAtual;
                                            $proximo = $i === $passoAtual + 1;
                                            $corCirculo = $concluido ? 'bg-success' : 'bg-secondary';
                                            $icone = $concluido ? 'fa-check' : ($proximo ? 'fa-hourglass-half' : 'fa-clock');
                                        ?>
                                        <div class="text-center">
                                            <div class="rounded-circle <?= $corCirculo ?> p-3" style="width: 60px; height: 60px; margin: 0 auto;">
                                                <i class="fas <?= $icone ?> text-white fs-4"></i>
                                            </div>
                                            <small class="d-block mt-2 fw-bold <?= $concluido ? 'text-success' : 'text-muted' ?>"><?= $rotulo ?></small>
                                        </div>
                                        <?php if ($i < count($rotulos) - 1): ?>
                                            <?php
                                                $largura = $i < $passoAtual ? '100' : ($i === $passoAtual ? '50' : '0');
                                                $corBarra = $i < $passoAtual ? 'bg-success' : ($i === $passoAtual ? 'bg-warning' : 'bg-secondary');
                                            ?>
                                            <div class="flex-grow-1 mx-2">
                                                <div class="progress" style="height: 4px;">
                                                    <div class="progress-bar <?= $corBarra ?>" role="progressbar" style="width: <?= $largura ?>%;"></div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Itens do Pedido -->
                        <hr>
                        <h6 class="fw-bold">Itens do Pedido</h6>
                        <ul class="list-unstyled">
                            <?php foreach ($pedido['itens'] as $item): ?>
                                <li class="d-flex justify-content-between border-bottom py-2">
                                    <span><?= htmlspecialchars($item['produto_nome']) ?></span>
                                    <span><?= (int) $item['quantidade'] ?> x Kz <?= number_format((float) $item['preco_unitario'], 2) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total:</span>
                            <span style="color: var(--dourado);">Kz <?= number_format($total, 2) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script>
        // Verifica de tempos a tempos se o estado do pedido mudou na
        // base de dados. Se mudou, recarrega a pagina para mostrar o
        // passo novo, em vez de o cliente ter de andar a atualizar
        // manualmente.
        (function () {
            var card = document.getElementById('cardPedido');
            var pedidoId = card.dataset.pedidoId;
            var estadoAtual = card.dataset.estado;

            // Se o pedido deixar de existir ou de ser nosso (por
            // exemplo, foi eliminado), para de perguntar ao servidor
            // em vez de continuar a martelar o endpoint para sempre.
            var intervalo = setInterval(function () {
                fetch('/index.php?rota=pedidos.estado-json&id=' + pedidoId)
                    .then(function (resposta) {
                        if (!resposta.ok) {
                            clearInterval(intervalo);
                            return null;
                        }
                        return resposta.json();
                    })
                    .then(function (dados) {
                        if (dados && dados.estado && dados.estado !== estadoAtual) {
                            window.location.reload();
                        }
                    })
                    .catch(function () { /* tenta outra vez na proxima ronda */ });
            }, 8000);

            // Tambem para quando o cliente sai da pagina, para nao
            // deixar o temporizador a correr numa aba esquecida.
            window.addEventListener('pagehide', function () {
                clearInterval(intervalo);
            });
        })();
    </script>
</body>
</html>
