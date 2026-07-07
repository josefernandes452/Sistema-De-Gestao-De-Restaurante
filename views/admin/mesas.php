<?php
require_once __DIR__ . "/../../inicializar.php";
$utilizadorLogado = Sessao::exigirPerfil("Administrador", "Operador");

$mesaModel = new MesaModel();
$lista = $mesaModel->todos();
$flash = Sessao::consumirFlash();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesas - Sabor Alma Admin</title>
    
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
            <a href="mesas.php" class="menu-item active"><i class="fas fa-chair"></i> Mesas</a>
            <a href="clientes.php" class="menu-item"><i class="fas fa-user-friends"></i> Clientes</a>
            <a href="pedidos.php" class="menu-item"><i class="fas fa-clipboard-list"></i> Pedidos</a>
            <a href="pagamentos.php" class="menu-item"><i class="fas fa-credit-card"></i> Pagamentos</a>
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
                <span class="fw-semibold ms-2">Gestao de Mesas</span>
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
                    <input type="text" class="form-control" id="pesquisaMesa" placeholder="Pesquisar mesa..." onkeyup="filtrarMesas()">
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn" style="background: #c9a84c; color: #1a3c2a;" onclick="abrirModalMesa()">
                    <i class="fas fa-plus me-1"></i> Nova Mesa
                </button>
            </div>
        </div>

        <div class="card card-dashboard p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Numero</th>
                            <th>Capacidade</th>
                            <th>Localizacao</th>
                            <th>Status</th>
                            <th class="text-center">Acoes</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaMesas">
                        <?php foreach ($lista as $i => $m): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><strong>Mesa <?= $m['numero'] ?></strong></td>
                                <td><?= $m['capacidade'] ?> pessoas</td>
                                <td><?= htmlspecialchars($m['localizacao'] ?: '-') ?></td>
                                <td>
                                    <?php $corEstado = ['Livre' => 'success', 'Ocupada' => 'danger', 'Reservada' => 'warning']; ?>
                                    <span class="badge bg-<?= $corEstado[$m['estado']] ?? 'secondary' ?>"><?= htmlspecialchars($m['estado']) ?></span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-success me-1"
                                        onclick="editarMesa(this)"
                                        data-id="<?= $m['id'] ?>"
                                        data-numero="<?= $m['numero'] ?>"
                                        data-capacidade="<?= $m['capacidade'] ?>"
                                        data-localizacao="<?= htmlspecialchars($m['localizacao'] ?? '') ?>"
                                        data-estado="<?= htmlspecialchars($m['estado']) ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarMesa(<?= $m['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between">
                <span class="text-muted small" id="totalMesas">Total: <?= count($lista) ?> mesas</span>
            </div>
        </div>

        <footer class="text-center text-muted small mt-4">&copy; 2026 Sabor Alma - Sistema de Gestao</footer>
    </div>

    <!-- MODAL MESA -->
    <div class="modal fade" id="modalMesa" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formMesa" method="post" action="/index.php?rota=mesas.guardar">
                    <?= Csrf::campo() ?>
                    <input type="hidden" name="id" id="mesaId">
                    <div class="modal-header" style="background: #1a3c2a; color: white;">
                        <h5 class="modal-title" id="modalMesaTitulo"><i class="fas fa-chair me-2"></i> Nova Mesa</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Numero da Mesa</label>
                            <input type="number" name="numero" class="form-control" id="numeroMesa" placeholder="Ex: 1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Capacidade</label>
                            <input type="number" name="capacidade" class="form-control" id="capacidadeMesa" placeholder="Ex: 4" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Localizacao</label>
                            <input type="text" name="localizacao" class="form-control" id="localizacaoMesa" placeholder="Ex: Salao Principal">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" name="estado" id="statusMesa">
                                <option value="Livre">Livre</option>
                                <option value="Ocupada">Ocupada</option>
                                <option value="Reservada">Reservada</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn" style="background: #c9a84c; color: #1a3c2a;">
                            <i class="fas fa-save me-1"></i> <span id="btnSalvarMesa">Salvar</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="formEliminarMesa" method="post" action="/index.php?rota=mesas.eliminar" style="display: none;">
        <?= Csrf::campo() ?>
        <input type="hidden" name="id" id="eliminarMesaId">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/admin.js"></script>
</body>
</html>