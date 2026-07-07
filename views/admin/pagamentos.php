<?php
require_once __DIR__ . "/../../inicializar.php";
$utilizadorLogado = Sessao::exigirPerfil("Administrador", "Operador");

$pagamentoModel = new PagamentoModel();
$pedidoModel = new PedidoModel();

$lista = $pagamentoModel->todosComDetalhes();
$pedidos = $pedidoModel->todosComDetalhes();
$flash = Sessao::consumirFlash();

$corEstadoPagamento = ['Pago' => 'success', 'Pendente' => 'warning', 'Cancelado' => 'danger'];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamentos - Sabor Alma Admin</title>
    
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
            <a href="pagamentos.php" class="menu-item active"><i class="fas fa-credit-card"></i> Pagamentos</a>
            <a href="relatorios.php" class="menu-item"><i class="fas fa-chart-bar"></i> Relatorios</a>
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
                <span class="fw-semibold ms-2">Gestao de Pagamentos</span>
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

        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['tipo'] === 'erro' ? 'danger' : 'success' ?>" role="alert">
                <?= htmlspecialchars($flash['mensagem']) ?>
            </div>
        <?php endif; ?>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-search" style="color: #c9a84c;"></i></span>
                    <input type="text" class="form-control" id="pesquisaPagamento" placeholder="Pesquisar pagamento..." onkeyup="filtrarPagamentos()">
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn" style="background: #c9a84c; color: #1a3c2a;" onclick="abrirModalPagamento()">
                    <i class="fas fa-plus me-1"></i> Novo Pagamento
                </button>
            </div>
        </div>

        <div class="card card-dashboard p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Pedido</th>
                            <th>Cliente</th>
                            <th>Valor</th>
                            <th>Metodo</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th class="text-center">Acoes</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaPagamentos">
                        <?php foreach ($lista as $pg): ?>
                            <tr>
                                <td><?= $pg['id'] ?></td>
                                <td>#<?= $pg['pedido_id'] ?></td>
                                <td><?= htmlspecialchars($pg['cliente_nome'] ?: 'Cliente avulso') ?></td>
                                <td><strong>Kz <?= number_format((float) $pg['valor'], 2) ?></strong></td>
                                <td><span class="badge bg-info"><?= htmlspecialchars($pg['metodo']) ?></span></td>
                                <td><span class="badge bg-<?= $corEstadoPagamento[$pg['estado']] ?? 'secondary' ?>"><?= htmlspecialchars($pg['estado']) ?></span></td>
                                <td><?= htmlspecialchars($pg['criado_em']) ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarPagamento(<?= $pg['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between">
                <span class="text-muted small" id="totalPagamentos">Total: <?= count($lista) ?> pagamentos</span>
            </div>
        </div>

        <footer class="text-center text-muted small mt-4">&copy; 2026 Sabor Alma - Sistema de Gestao</footer>
    </div>

    <!-- MODAL PAGAMENTO -->
    <div class="modal fade" id="modalPagamento" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="/index.php?rota=pagamentos.guardar">
                    <?= Csrf::campo() ?>
                    <div class="modal-header" style="background: #1a3c2a; color: white;">
                        <h5 class="modal-title"><i class="fas fa-credit-card me-2"></i> Novo Pagamento</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pedido</label>
                            <select class="form-select" name="pedido_id" id="pedidoPagamento" required onchange="preencherValorPagamento(this)">
                                <option value="">Selecione</option>
                                <?php foreach ($pedidos as $p): ?>
                                    <option value="<?= $p['id'] ?>" data-total="<?= $p['total'] ?>">
                                        #<?= $p['id'] ?> - <?= htmlspecialchars($p['cliente_nome'] ?: 'Cliente avulso') ?> (Kz <?= number_format((float) $p['total'], 2) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Valor</label>
                            <input type="number" name="valor" class="form-control" id="valorPagamento" placeholder="0.00" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Metodo</label>
                            <select class="form-select" name="metodo" id="metodoPagamento" required>
                                <option value="">Selecione</option>
                                <option value="Dinheiro">Dinheiro</option>
                                <option value="Cartao de Credito">Cartao de Credito</option>
                                <option value="Cartao de Debito">Cartao de Debito</option>
                                <option value="Multicaixa Express">Multicaixa Express</option>
                                <option value="Transferencia">Transferencia</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" name="estado" id="statusPagamento">
                                <option value="Pendente">Pendente</option>
                                <option value="Pago">Pago</option>
                                <option value="Cancelado">Cancelado</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn" style="background: #c9a84c; color: #1a3c2a;">
                            <i class="fas fa-save me-1"></i> Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="formEliminarPagamento" method="post" action="/index.php?rota=pagamentos.eliminar" style="display: none;">
        <?= Csrf::campo() ?>
        <input type="hidden" name="id" id="eliminarPagamentoId">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/admin.js"></script>
</body>
</html>