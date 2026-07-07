<?php
require_once __DIR__ . "/../../inicializar.php";
$utilizadorLogado = Sessao::exigirPerfil("Administrador", "Operador");
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

    <script>
        var user = JSON.parse(localStorage.getItem('user'));
        if (!user || user.tipo !== 'admin') {
            window.location.href = '../cliente/login.php';
        }
    </script>

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
                <span class="text-muted small d-none d-md-inline">
                    <i class="fas fa-clock me-1"></i> <span id="relogio"></span>
                </span>
                <div class="avatar">A</div>
                <div class="d-none d-sm-block">
                    <div class="fw-semibold small">Administrador</div>
                    <div class="text-muted small">admin@saboralma.ao</div>
                </div>
            </div>
        </div>

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
                    <tbody id="tabelaUtilizadores"></tbody>
                </table>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between">
                <span class="text-muted small" id="totalUtilizadores">Total: 0 utilizadores</span>
            </div>
        </div>

        <footer class="text-center text-muted small mt-4">&copy; 2026 Sabor Alma - Sistema de Gestao</footer>
    </div>

    <!-- MODAL UTILIZADOR -->
    <div class="modal fade" id="modalUtilizador" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: #1a3c2a; color: white;">
                    <h5 class="modal-title" id="modalUtilizadorTitulo"><i class="fas fa-user-plus me-2"></i> Novo Utilizador</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formUtilizador">
                        <input type="hidden" id="utilizadorId">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nome Completo</label>
                                <input type="text" class="form-control" id="nomeUtilizador" placeholder="Digite o nome" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" id="emailUtilizador" placeholder="exemplo@email.com" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Senha</label>
                                <input type="password" class="form-control" id="senhaUtilizador" placeholder="Minimo 6 caracteres">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Perfil</label>
                                <select class="form-select" id="perfilUtilizador" required>
                                    <option value="">Selecione</option>
                                    <option value="Administrador">Administrador</option>
                                    <option value="Gerente">Gerente</option>
                                    <option value="Funcionario">Funcionario</option>
                                    <option value="Caixa">Caixa</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" id="statusUtilizador">
                                <option value="Ativo">Ativo</option>
                                <option value="Inativo">Inativo</option>
                                <option value="Bloqueado">Bloqueado</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn" style="background: #c9a84c; color: #1a3c2a;" onclick="salvarUtilizador()">
                        <i class="fas fa-save me-1"></i> <span id="btnSalvarUtilizador">Salvar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/admin.js"></script>
</body>
</html>