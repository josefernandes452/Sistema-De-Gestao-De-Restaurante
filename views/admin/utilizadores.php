<?php
require_once __DIR__ . "/../../inicializar.php";
$utilizadorLogado = Sessao::exigirPerfil("Administrador");

$usuarioModel = new UsuarioModel();
$lista = $usuarioModel->todosComPerfil();
$perfis = $usuarioModel->todosPerfis();
$flash = Sessao::consumirFlash();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilizadores - Sabor Alma Admin</title>
    
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
            <a href="dashboard.php" class="menu-item"><i class="fas fa-chart-pie"></i> Dashboard</a>
            <a href="utilizadores.php" class="menu-item active"><i class="fas fa-users"></i> Utilizadores</a>
            <a href="categorias.php" class="menu-item"><i class="fas fa-tags"></i> Categorias</a>
            <a href="produtos.php" class="menu-item"><i class="fas fa-box"></i> Produtos</a>
            <a href="mesas.php" class="menu-item"><i class="fas fa-chair"></i> Mesas</a>
            <a href="clientes.php" class="menu-item"><i class="fas fa-user-friends"></i> Clientes</a>
            <a href="pedidos.php" class="menu-item"><i class="fas fa-clipboard-list"></i> Pedidos</a>
            <a href="reservas.php" class="menu-item"><i class="fas fa-calendar-check"></i> Reservas</a>
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

    <!-- CONTEUDO -->
    <div class="main-content">
        <div class="top-navbar">
            <div>
                <button class="btn btn-link text-dark d-lg-none toggle-sidebar" onclick="toggleSidebar()">
                    <i class="fas fa-bars fs-4"></i>
                </button>
                <span class="fw-semibold ms-2">Gestao de Utilizadores</span>
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

        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['tipo'] === 'erro' ? 'danger' : 'success' ?>" role="alert">
                <?= htmlspecialchars($flash['mensagem']) ?>
            </div>
        <?php endif; ?>

        <!-- BARRA DE FERRAMENTAS -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-search" style="color: #c9a84c;"></i></span>
                    <input type="text" class="form-control" id="pesquisaUtilizador" placeholder="Pesquisar utilizador..." onkeyup="filtrarUtilizadores()">
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn" style="background: #c9a84c; color: #1a3c2a;" onclick="abrirModalUtilizador()">
                    <i class="fas fa-plus me-1"></i> Novo Utilizador
                </button>
            </div>
        </div>

        <!-- TABELA -->
        <div class="card card-dashboard p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Perfil</th>
                            <th>Status</th>
                            <th>Data Criacao</th>
                            <th class="text-center">Acoes</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaUtilizadores">
                        <?php foreach ($lista as $i => $u): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($u['nome']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($u['perfil_nome']) ?></span></td>
                                <td><span class="badge bg-<?= $u['estado'] === 'Ativo' ? 'success' : 'warning' ?>"><?= htmlspecialchars($u['estado']) ?></span></td>
                                <td><?= htmlspecialchars($u['criado_em']) ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-success me-1"
                                        onclick="editarUtilizador(this)"
                                        data-id="<?= $u['id'] ?>"
                                        data-nome="<?= htmlspecialchars($u['nome']) ?>"
                                        data-email="<?= htmlspecialchars($u['email']) ?>"
                                        data-perfil="<?= htmlspecialchars($u['perfil_nome']) ?>"
                                        data-estado="<?= htmlspecialchars($u['estado']) ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarUtilizador(<?= $u['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between">
                <span class="text-muted small" id="totalUtilizadores">Total: <?= count($lista) ?> utilizadores</span>
            </div>
        </div>

        <footer class="text-center text-muted small mt-4">&copy; 2026 Sabor Alma - Sistema de Gestao</footer>
    </div>

    <!-- MODAL UTILIZADOR -->
    <div class="modal fade" id="modalUtilizador" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formUtilizador" method="post" action="/index.php?rota=utilizadores.guardar">
                    <?= Csrf::campo() ?>
                    <input type="hidden" name="id" id="utilizadorId">
                    <div class="modal-header" style="background: #1a3c2a; color: white;">
                        <h5 class="modal-title" id="modalUtilizadorTitulo"><i class="fas fa-user-plus me-2"></i> Novo Utilizador</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nome Completo</label>
                                <input type="text" name="nome" class="form-control" id="nomeUtilizador" placeholder="Digite o nome" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control" id="emailUtilizador" placeholder="exemplo@email.com" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Senha</label>
                                <input type="password" name="senha" class="form-control" id="senhaUtilizador" placeholder="Minimo 6 caracteres">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Perfil</label>
                                <select class="form-select" name="perfil" id="perfilUtilizador" required>
                                    <option value="">Selecione</option>
                                    <?php foreach ($perfis as $p): ?>
                                        <option value="<?= htmlspecialchars($p['nome']) ?>"><?= htmlspecialchars($p['nome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" name="estado" id="statusUtilizador">
                                <option value="Ativo">Ativo</option>
                                <option value="Inativo">Inativo</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn" style="background: #c9a84c; color: #1a3c2a;">
                            <i class="fas fa-save me-1"></i> <span id="btnSalvarUtilizador">Salvar</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- FORMULARIO ESCONDIDO, USADO SO PARA ELIMINAR -->
    <form id="formEliminarUtilizador" method="post" action="/index.php?rota=utilizadores.eliminar" style="display: none;">
        <?= Csrf::campo() ?>
        <input type="hidden" name="id" id="eliminarUtilizadorId">
    </form>

    <?php include __DIR__ . '/_alterar-senha-modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/admin.js"></script>
</body>
</html>