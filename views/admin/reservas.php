<?php
require_once __DIR__ . "/../../inicializar.php";
$utilizadorLogado = Sessao::exigirPerfil("Administrador", "Operador");

$reservaModel = new ReservaModel();
$lista = $reservaModel->todosComDetalhes();
$flash = Sessao::consumirFlash();

$corEstado = [
    'Confirmada' => 'success',
    'Cancelada' => 'danger',
    'Concluida' => 'secondary',
];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas - Sabor Alma Admin</title>

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
            <?php if ($utilizadorLogado['perfil'] === 'Administrador'): ?>
            <a href="utilizadores.php" class="menu-item"><i class="fas fa-users"></i> Utilizadores</a>
            <a href="categorias.php" class="menu-item"><i class="fas fa-tags"></i> Categorias</a>
            <a href="produtos.php" class="menu-item"><i class="fas fa-box"></i> Produtos</a>
            <?php endif; ?>
            <a href="mesas.php" class="menu-item"><i class="fas fa-chair"></i> Mesas</a>
            <a href="clientes.php" class="menu-item"><i class="fas fa-user-friends"></i> Clientes</a>
            <a href="pedidos.php" class="menu-item"><i class="fas fa-clipboard-list"></i> Pedidos</a>
            <a href="reservas.php" class="menu-item active"><i class="fas fa-calendar-check"></i> Reservas</a>
            <a href="pagamentos.php" class="menu-item"><i class="fas fa-credit-card"></i> Pagamentos</a>
            <?php if ($utilizadorLogado['perfil'] === 'Administrador'): ?>
            <a href="relatorios.php" class="menu-item"><i class="fas fa-chart-bar"></i> Relatorios</a>
            <?php endif; ?>
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
                <span class="fw-semibold ms-2">Gestao de Reservas</span>
            </div>
            <div class="user-info">
                <?php include __DIR__ . '/_notificacoes-bell.php'; ?>
                <span class="text-muted small d-none d-md-inline">
                    <i class="fas fa-clock me-1"></i> <span id="relogio"></span>
                </span>
                <div class="avatar"><?= htmlspecialchars(strtoupper(substr($utilizadorLogado['nome'], 0, 1))) ?></div>
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

        <div class="card card-dashboard p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>Mesa</th>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Pessoas</th>
                            <th>Status</th>
                            <th class="text-center">Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($lista)): ?>
                            <tr><td colspan="9" class="text-muted text-center py-3">Ainda nao ha reservas.</td></tr>
                        <?php else: ?>
                            <?php foreach ($lista as $r): ?>
                                <tr>
                                    <td>#<?= $r['id'] ?></td>
                                    <td><?= htmlspecialchars($r['nome']) ?><?= $r['cliente_nome'] ? ' <span class="text-muted small">(conta: ' . htmlspecialchars($r['cliente_nome']) . ')</span>' : '' ?></td>
                                    <td><?= htmlspecialchars($r['telefone']) ?></td>
                                    <td>Mesa <?= $r['mesa_numero'] ?></td>
                                    <td><?= htmlspecialchars($r['data']) ?></td>
                                    <td><?= htmlspecialchars(substr($r['hora'], 0, 5)) ?></td>
                                    <td><?= (int) $r['pessoas'] ?></td>
                                    <td><span class="badge bg-<?= $corEstado[$r['estado']] ?? 'secondary' ?>"><?= htmlspecialchars($r['estado']) ?></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-success me-1"
                                            onclick="editarEstadoReserva(this)"
                                            data-id="<?= $r['id'] ?>"
                                            data-estado="<?= htmlspecialchars($r['estado']) ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarReserva(<?= $r['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between">
                <span class="text-muted small">Total: <?= count($lista) ?> reservas</span>
            </div>
        </div>

        <footer class="text-center text-muted small mt-4">&copy; 2026 Sabor Alma - Sistema de Gestao</footer>
    </div>

    <!-- MODAL MUDAR ESTADO -->
    <div class="modal fade" id="modalEstadoReserva" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="/index.php?rota=reservas.estado">
                    <?= Csrf::campo() ?>
                    <input type="hidden" name="id" id="estadoReservaId">
                    <div class="modal-header" style="background: #1a3c2a; color: white;">
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i> Mudar Estado da Reserva</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label fw-semibold">Estado</label>
                        <select class="form-select" name="estado" id="novoEstadoReserva">
                            <option value="Confirmada">Confirmada</option>
                            <option value="Cancelada">Cancelada</option>
                            <option value="Concluida">Concluida</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn" style="background: #c9a84c; color: #1a3c2a;">Atualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="formEliminarReserva" method="post" action="/index.php?rota=reservas.eliminar" style="display: none;">
        <?= Csrf::campo() ?>
        <input type="hidden" name="id" id="eliminarReservaId">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/admin.js"></script>
    <script>
        function editarEstadoReserva(botao) {
            document.getElementById('estadoReservaId').value = botao.dataset.id;
            document.getElementById('novoEstadoReserva').value = botao.dataset.estado;
            new bootstrap.Modal(document.getElementById('modalEstadoReserva')).show();
        }

        function eliminarReserva(id) {
            confirmarAcao('Eliminar esta reserva?').then(function (ok) {
                if (!ok) return;
                document.getElementById('eliminarReservaId').value = id;
                document.getElementById('formEliminarReserva').requestSubmit();
            });
        }
    </script>
</body>
</html>
