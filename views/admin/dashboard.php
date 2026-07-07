<?php
require_once __DIR__ . "/../../inicializar.php";
$utilizadorLogado = Sessao::exigirPerfil("Administrador", "Operador");

$pdo = Database::getConexao();

// Cartoes do topo. Comparamos hoje com ontem para mostrar uma
// variacao real, em vez de uma percentagem inventada.
$pedidosHoje = (int) $pdo->query("SELECT COUNT(*) FROM pedidos WHERE DATE(criado_em) = CURDATE()")->fetchColumn();
$pedidosOntem = (int) $pdo->query("SELECT COUNT(*) FROM pedidos WHERE DATE(criado_em) = CURDATE() - INTERVAL 1 DAY")->fetchColumn();
$variacaoPedidos = $pedidosOntem > 0 ? round((($pedidosHoje - $pedidosOntem) / $pedidosOntem) * 100) : null;

$totalClientes = (int) $pdo->query('SELECT COUNT(*) FROM clientes')->fetchColumn();
$totalProdutos = (int) $pdo->query('SELECT COUNT(*) FROM produtos')->fetchColumn();

$faturacaoHoje = (float) $pdo->query("SELECT COALESCE(SUM(valor), 0) FROM pagamentos WHERE estado = 'Pago' AND DATE(criado_em) = CURDATE()")->fetchColumn();
$faturacaoOntem = (float) $pdo->query("SELECT COALESCE(SUM(valor), 0) FROM pagamentos WHERE estado = 'Pago' AND DATE(criado_em) = CURDATE() - INTERVAL 1 DAY")->fetchColumn();
$variacaoFaturacao = $faturacaoOntem > 0 ? round((($faturacaoHoje - $faturacaoOntem) / $faturacaoOntem) * 100) : null;

// Vendas dos ultimos 7 dias, para o grafico de barras.
$diasSemana = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab', 'Dom'];
$vendas7Dias = [];
for ($i = 6; $i >= 0; $i--) {
    $data = date('Y-m-d', strtotime("-$i day"));
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(valor), 0) FROM pagamentos WHERE estado = 'Pago' AND DATE(criado_em) = ?");
    $stmt->execute([$data]);
    $vendas7Dias[] = [
        'label' => $diasSemana[date('N', strtotime($data)) - 1],
        'valor' => (float) $stmt->fetchColumn(),
    ];
}
$maiorVenda = max(array_column($vendas7Dias, 'valor')) ?: 1;

// Ultimos pedidos, com o tempo desde que foram feitos.
$ultimosPedidos = $pdo->query(
    "SELECT p.id, p.estado, p.criado_em, COALESCE(c.nome, 'Cliente avulso') AS cliente_nome
     FROM pedidos p
     LEFT JOIN clientes c ON c.id = p.cliente_id
     ORDER BY p.criado_em DESC
     LIMIT 4"
)->fetchAll();

function tempoDecorrido(string $dataHora): string
{
    $minutos = intdiv(time() - strtotime($dataHora), 60);

    if ($minutos < 1) {
        return 'agora mesmo';
    }

    if ($minutos < 60) {
        return $minutos . ' min';
    }

    $horas = intdiv($minutos, 60);
    $minutosRestantes = $minutos % 60;

    return $minutosRestantes > 0 ? "{$horas}h {$minutosRestantes}min" : "{$horas}h";
}

$corEstado = [
    'Pendente' => 'secondary',
    'Em Preparacao' => 'warning',
    'Pronto' => 'info',
    'Entregue' => 'success',
    'Cancelado' => 'danger',
];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sabor Alma Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div class="logo">
            <h5 style="color: #c9a84c;">Sabor Alma</h5>
            <small class="text-muted">Painel Administrativo</small>
        </div>
        <nav>
            <a href="dashboard.php" class="menu-item active">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
            <a href="utilizadores.php" class="menu-item">
                <i class="fas fa-users"></i> Utilizadores
            </a>
            <a href="categorias.php" class="menu-item">
                <i class="fas fa-tags"></i> Categorias
            </a>
            <a href="produtos.php" class="menu-item">
                <i class="fas fa-box"></i> Produtos
            </a>
            <a href="mesas.php" class="menu-item">
                <i class="fas fa-chair"></i> Mesas
            </a>
            <a href="clientes.php" class="menu-item">
                <i class="fas fa-user-friends"></i> Clientes
            </a>
            <a href="pedidos.php" class="menu-item">
                <i class="fas fa-clipboard-list"></i> Pedidos
            </a>
            <a href="pagamentos.php" class="menu-item">
                <i class="fas fa-credit-card"></i> Pagamentos
            </a>
            <a href="relatorios.php" class="menu-item">
                <i class="fas fa-chart-bar"></i> Relatorios
            </a>
        </nav>
        <div style="border-top: 1px solid rgba(255,255,255,0.1); padding: 15px 25px;">
            <a href="../cliente/login.php" class="menu-item text-danger" onclick="logout()">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </div>

    <!-- OVERLAY MOBILE -->
    <div class="overlay" id="overlay" onclick="fecharSidebar()"></div>

    <!-- CONTEUDO -->
    <div class="main-content">
        <div class="top-navbar">
            <div>
                <button class="btn btn-link text-dark d-lg-none" onclick="toggleSidebar()">
                    <i class="fas fa-bars fs-4"></i>
                </button>
                <span class="fw-semibold ms-2">Dashboard</span>
            </div>
            <div class="user-info">
                <span class="text-muted small d-none d-md-inline">
                    <i class="fas fa-clock me-1"></i> <span id="relogio"></span>
                </span>
                <div class="avatar"><?= strtoupper(substr($utilizadorLogado['nome'], 0, 1)) ?></div>
                <div class="d-none d-sm-block">
                    <div class="fw-semibold small"><?= htmlspecialchars($utilizadorLogado['nome']) ?></div>
                    <div class="text-muted small"><?= htmlspecialchars($utilizadorLogado['email']) ?></div>
                </div>
            </div>
        </div>

        <!-- CARDS -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card card-dashboard p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="text-muted small">Pedidos Hoje</span>
                            <h2 class="fw-bold mt-1" style="color: #1a3c2a;"><?= $pedidosHoje ?></h2>
                            <?php if ($variacaoPedidos !== null): ?>
                                <small class="text-<?= $variacaoPedidos >= 0 ? 'success' : 'danger' ?>">
                                    <i class="fas fa-arrow-<?= $variacaoPedidos >= 0 ? 'up' : 'down' ?> me-1"></i> <?= $variacaoPedidos ?>% vs ontem
                                </small>
                            <?php endif; ?>
                        </div>
                        <div class="card-icon" style="background: rgba(201, 168, 76, 0.15); color: #c9a84c;">
                            <i class="fas fa-clipboard-list fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card card-dashboard p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="text-muted small">Clientes</span>
                            <h2 class="fw-bold mt-1" style="color: #1a3c2a;"><?= $totalClientes ?></h2>
                        </div>
                        <div class="card-icon" style="background: rgba(26, 60, 42, 0.15); color: #1a3c2a;">
                            <i class="fas fa-user-friends fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card card-dashboard p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="text-muted small">Produtos</span>
                            <h2 class="fw-bold mt-1" style="color: #1a3c2a;"><?= $totalProdutos ?></h2>
                        </div>
                        <div class="card-icon" style="background: rgba(26, 60, 42, 0.15); color: #1a3c2a;">
                            <i class="fas fa-box fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card card-dashboard p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="text-muted small">Faturacao Hoje</span>
                            <h2 class="fw-bold mt-1" style="color: #c9a84c;">Kz <?= number_format($faturacaoHoje, 2) ?></h2>
                            <?php if ($variacaoFaturacao !== null): ?>
                                <small class="text-<?= $variacaoFaturacao >= 0 ? 'success' : 'danger' ?>">
                                    <i class="fas fa-arrow-<?= $variacaoFaturacao >= 0 ? 'up' : 'down' ?> me-1"></i> <?= $variacaoFaturacao ?>% vs ontem
                                </small>
                            <?php endif; ?>
                        </div>
                        <div class="card-icon" style="background: rgba(201, 168, 76, 0.15); color: #c9a84c;">
                            <i class="fas fa-money-bill-wave fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRAFICO -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-dashboard p-4">
                    <h6 class="fw-semibold mb-3">
                        <i class="fas fa-chart-line" style="color: #c9a84c;"></i> Vendas Ultimos 7 Dias
                    </h6>
                    <div class="d-flex justify-content-between align-items-end" style="height: 200px;">
                        <?php foreach ($vendas7Dias as $dia): ?>
                            <div class="text-center flex-grow-1">
                                <div class="barra" style="height: <?= max(4, round(($dia['valor'] / $maiorVenda) * 200)) ?>px; background: #1a3c2a; width: 30px; margin: 0 auto; border-radius: 6px 6px 0 0;" title="Kz <?= number_format($dia['valor'], 2) ?>"></div>
                                <small class="text-muted"><?= $dia['label'] ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card card-dashboard p-4">
                    <h6 class="fw-semibold mb-3">
                        <i class="fas fa-clock" style="color: #c9a84c;"></i> Ultimos Pedidos
                    </h6>
                    <?php if (empty($ultimosPedidos)): ?>
                        <p class="text-muted small">Ainda nao ha pedidos.</p>
                    <?php else: ?>
                        <ul class="list-unstyled">
                            <?php foreach ($ultimosPedidos as $p): ?>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span><span class="badge bg-<?= $corEstado[$p['estado']] ?? 'secondary' ?>">#<?= $p['id'] ?></span> <?= htmlspecialchars($p['cliente_nome']) ?></span>
                                    <span class="text-muted small"><?= tempoDecorrido($p['criado_em']) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <a href="pedidos.php" class="btn btn-sm w-100 mt-2" style="background: #c9a84c; color: #1a3c2a;">
                        <i class="fas fa-eye me-1"></i> Ver todos os pedidos
                    </a>
                </div>
            </div>
        </div>

        <footer class="text-center text-muted small mt-4">
            &copy; 2026 Sabor Alma - Sistema de Gestao
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/admin.js"></script>
</body>
</html>