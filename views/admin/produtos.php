<?php
require_once __DIR__ . "/../../inicializar.php";
$utilizadorLogado = Sessao::exigirPerfil("Administrador", "Operador");
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - Sabor Alma Admin</title>
    
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
            <a href="produtos.php" class="menu-item active"><i class="fas fa-box"></i> Produtos</a>
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

    <div class="main-content">
        <div class="top-navbar">
            <div>
                <button class="btn btn-link text-dark d-lg-none toggle-sidebar" onclick="toggleSidebar()">
                    <i class="fas fa-bars fs-4"></i>
                </button>
                <span class="fw-semibold ms-2">Gestao de Produtos</span>
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

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-search" style="color: #c9a84c;"></i></span>
                    <input type="text" class="form-control" id="pesquisaProduto" placeholder="Pesquisar produto..." onkeyup="filtrarProdutos()">
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn" style="background: #c9a84c; color: #1a3c2a;" onclick="abrirModalProduto()">
                    <i class="fas fa-plus me-1"></i> Novo Produto
                </button>
            </div>
        </div>

        <div class="card card-dashboard p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Imagem</th>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>Preco</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th class="text-center">Acoes</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaProdutos"></tbody>
                </table>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between">
                <span class="text-muted small" id="totalProdutos">Total: 0 produtos</span>
            </div>
        </div>

        <footer class="text-center text-muted small mt-4">&copy; 2026 Sabor Alma - Sistema de Gestao</footer>
    </div>

    <!-- MODAL PRODUTO -->
    <div class="modal fade" id="modalProduto" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: #1a3c2a; color: white;">
                    <h5 class="modal-title" id="modalProdutoTitulo"><i class="fas fa-box me-2"></i> Novo Produto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formProduto">
                        <input type="hidden" id="produtoId">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nome</label>
                                <input type="text" class="form-control" id="nomeProduto" placeholder="Ex: Bife a Casa" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Categoria</label>
                                <select class="form-select" id="categoriaProduto" required>
                                    <option value="">Selecione</option>
                                    <option value="Bebidas">Bebidas</option>
                                    <option value="Entradas">Entradas</option>
                                    <option value="Pratos Principais">Pratos Principais</option>
                                    <option value="Sobremesas">Sobremesas</option>
                                    <option value="Acompanhamentos">Acompanhamentos</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Preco (Kz)</label>
                                <input type="number" class="form-control" id="precoProduto" placeholder="0.00" step="0.01" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Stock</label>
                                <input type="number" class="form-control" id="stockProduto" placeholder="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Status</label>
                                <select class="form-select" id="statusProduto">
                                    <option value="Disponivel">Disponivel</option>
                                    <option value="Indisponivel">Indisponivel</option>
                                    <option value="Esgotado">Esgotado</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn" style="background: #c9a84c; color: #1a3c2a;" onclick="salvarProduto()">
                        <i class="fas fa-save me-1"></i> <span id="btnSalvarProduto">Salvar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/admin.js"></script>
</body>
</html>