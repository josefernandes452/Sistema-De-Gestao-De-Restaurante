<?php
require_once __DIR__ . "/../../inicializar.php";
$utilizadorLogado = Sessao::exigirPerfil("Administrador");

$dataInicio = Validador::texto($_GET['data_inicio'] ?? '') ?: date('Y-m-d', strtotime('-7 day'));
$dataFim = Validador::texto($_GET['data_fim'] ?? '') ?: date('Y-m-d');

$relatorioModel = new RelatorioModel();
$produtosMaisVendidos = $relatorioModel->produtosMaisVendidos($dataInicio, $dataFim);
$vendasPorPeriodo = $relatorioModel->vendasPorPeriodo($dataInicio, $dataFim);
$desempenhoPorOperador = $relatorioModel->desempenhoPorOperador($dataInicio, $dataFim);

$totalPedidosPeriodo = array_sum(array_column($vendasPorPeriodo, 'total_pedidos'));
$totalVendidoPeriodo = array_sum(array_column($vendasPorPeriodo, 'total_vendido'));
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatorios - Sabor Alma Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>

    <div class="sidebar" id="sidebar">
        <div class="logo">
            <h5 style="color: #c9a84c;">Sabor Alma</h5>
            <small class="text-muted">Painel Administrativo</small>
        </div>
        <nav>
            <a href="dashboard.php" class="menu-item"><i class="fas fa-chart-pie"></i> Dashboard</a>
            <a href="utilizadores.php" class="menu-item"><i class="fas fa-users"></i> Utilizadores</a>
            <a href="categorias.php" class="menu-item"><i class="fas fa-tags"></i> Categorias</a>
            <a href="produtos.php" class="menu-item"><i class="fas fa-box"></i> Produtos</a>
            <a href="mesas.php" class="menu-item"><i class="fas fa-chair"></i> Mesas</a>
            <a href="clientes.php" class="menu-item"><i class="fas fa-user-friends"></i> Clientes</a>
            <a href="pedidos.php" class="menu-item"><i class="fas fa-clipboard-list"></i> Pedidos</a>
            <a href="reservas.php" class="menu-item"><i class="fas fa-calendar-check"></i> Reservas</a>
            <a href="pagamentos.php" class="menu-item"><i class="fas fa-credit-card"></i> Pagamentos</a>
            <a href="relatorios.php" class="menu-item active"><i class="fas fa-chart-bar"></i> Relatorios</a>
        </nav>
        <div style="border-top: 1px solid rgba(255,255,255,0.1); padding: 15px 25px;">
            <a href="../cliente/login.php" class="menu-item text-danger" onclick="logout()">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </div>

    <div class="overlay" id="overlay" onclick="fecharSidebar()"></div>

    <div class="main-content">
        <div class="top-navbar">
            <div>
                <button class="btn btn-link text-dark d-lg-none toggle-sidebar" onclick="toggleSidebar()">
                    <i class="fas fa-bars fs-4"></i>
                </button>
                <span class="fw-semibold ms-2">Relatorios</span>
            </div>
            <div class="user-info">
                <?php include __DIR__ . '/_notificacoes-bell.php'; ?>
                <span class="text-muted small d-none d-md-inline">
                    <i class="fas fa-clock me-1"></i> <span id="relogio"></span>
                </span>
                <div class="avatar" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalAlterarSenha" title="Alterar senha"><?= htmlspecialchars(strtoupper(substr($utilizadorLogado['nome'], 0, 1))) ?></div>
                <div class="d-none d-sm-block">
                    <div class="fw-semibold small"><?= htmlspecialchars($utilizadorLogado['nome']) ?></div>
                    <div class="text-muted small"><?= htmlspecialchars($utilizadorLogado['email']) ?></div>
                </div>
            </div>
        </div>

        <!-- FILTRO DE PERIODO -->
        <div class="card card-dashboard p-4 mb-4">
            <h6 class="fw-semibold mb-3"><i class="fas fa-filter" style="color: #c9a84c;"></i> Periodo</h6>
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Data Inicio</label>
                    <input type="date" name="data_inicio" class="form-control" value="<?= htmlspecialchars($dataInicio) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Data Fim</label>
                    <input type="date" name="data_fim" class="form-control" value="<?= htmlspecialchars($dataFim) ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn w-100" style="background: #c9a84c; color: #1a3c2a;">
                        <i class="fas fa-search me-1"></i> Atualizar Relatorios
                    </button>
                </div>
            </form>
            <div class="d-flex gap-2 mt-3">
                <a class="btn btn-outline-secondary btn-sm" href="/index.php?rota=relatorios.csv&data_inicio=<?= urlencode($dataInicio) ?>&data_fim=<?= urlencode($dataFim) ?>">
                    <i class="fas fa-file-csv me-1"></i> Descarregar CSV
                </a>
                <a class="btn btn-outline-secondary btn-sm" href="/index.php?rota=relatorios.pdf&data_inicio=<?= urlencode($dataInicio) ?>&data_fim=<?= urlencode($dataFim) ?>">
                    <i class="fas fa-file-pdf me-1"></i> Descarregar PDF
                </a>
            </div>
        </div>

        <!-- CARDS RESUMO -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card card-dashboard p-3 text-center">
                    <h6 class="text-muted">Total de Pedidos</h6>
                    <h2 class="fw-bold" style="color: #1a3c2a;"><?= $totalPedidosPeriodo ?></h2>
                    <small class="text-muted">No periodo escolhido</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-dashboard p-3 text-center">
                    <h6 class="text-muted">Faturacao</h6>
                    <h2 class="fw-bold" style="color: #c9a84c;">Kz <?= number_format($totalVendidoPeriodo, 2) ?></h2>
                    <small class="text-muted">No periodo escolhido</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-dashboard p-3 text-center">
                    <h6 class="text-muted">Produtos Diferentes Vendidos</h6>
                    <h2 class="fw-bold" style="color: #1a3c2a;"><?= count($produtosMaisVendidos) ?></h2>
                    <small class="text-muted">No periodo escolhido</small>
                </div>
            </div>
        </div>

        <!-- RELATORIO 1: PRODUTOS MAIS VENDIDOS -->
        <div class="card card-dashboard p-0 mb-4">
            <div class="card-header bg-white">
                <h6 class="fw-semibold mb-0"><i class="fas fa-utensils" style="color: #c9a84c;"></i> Produtos Mais Vendidos</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Produto</th>
                            <th>Quantidade Vendida</th>
                            <th>Total Faturado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($produtosMaisVendidos)): ?>
                            <tr><td colspan="3" class="text-muted text-center py-3">Sem vendas neste periodo.</td></tr>
                        <?php else: ?>
                            <?php foreach ($produtosMaisVendidos as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['nome']) ?></td>
                                    <td><?= (int) $p['total_vendido'] ?></td>
                                    <td><strong>Kz <?= number_format((float) $p['total_faturado'], 2) ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RELATORIO 2: VENDAS POR PERIODO -->
        <div class="card card-dashboard p-0 mb-4">
            <div class="card-header bg-white">
                <h6 class="fw-semibold mb-0"><i class="fas fa-chart-line" style="color: #c9a84c;"></i> Vendas por Periodo</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Dia</th>
                            <th>Pedidos</th>
                            <th>Total Vendido</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($vendasPorPeriodo)): ?>
                            <tr><td colspan="3" class="text-muted text-center py-3">Sem pedidos neste periodo.</td></tr>
                        <?php else: ?>
                            <?php foreach ($vendasPorPeriodo as $dia): ?>
                                <tr>
                                    <td><?= htmlspecialchars($dia['dia']) ?></td>
                                    <td><?= (int) $dia['total_pedidos'] ?></td>
                                    <td><strong>Kz <?= number_format((float) $dia['total_vendido'], 2) ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RELATORIO 3: DESEMPENHO POR OPERADOR -->
        <div class="card card-dashboard p-0 mb-4">
            <div class="card-header bg-white">
                <h6 class="fw-semibold mb-0"><i class="fas fa-user-tie" style="color: #c9a84c;"></i> Desempenho por Operador</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Operador</th>
                            <th>Pedidos Registados</th>
                            <th>Total Vendido</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($desempenhoPorOperador)): ?>
                            <tr><td colspan="3" class="text-muted text-center py-3">Sem pedidos neste periodo.</td></tr>
                        <?php else: ?>
                            <?php foreach ($desempenhoPorOperador as $op): ?>
                                <tr>
                                    <td><?= htmlspecialchars($op['operador']) ?></td>
                                    <td><?= (int) $op['total_pedidos'] ?></td>
                                    <td><strong>Kz <?= number_format((float) $op['total_vendido'], 2) ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <footer class="text-center text-muted small mt-4">&copy; 2026 Sabor Alma - Sistema de Gestao</footer>
    </div>

    <?php include __DIR__ . '/_alterar-senha-modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/admin.js"></script>
</body>
</html>
