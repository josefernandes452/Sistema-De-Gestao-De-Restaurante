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
                <div class="avatar">A</div>
                <div class="d-none d-sm-block">
                    <div class="fw-semibold small">Administrador</div>
                    <div class="text-muted small">admin@saboralma.ao</div>
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
                            <h2 class="fw-bold mt-1" style="color: #1a3c2a;">24</h2>
                            <small class="text-success"><i class="fas fa-arrow-up me-1"></i> +12%</small>
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
                            <h2 class="fw-bold mt-1" style="color: #1a3c2a;">89</h2>
                            <small class="text-success"><i class="fas fa-arrow-up me-1"></i> +5%</small>
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
                            <h2 class="fw-bold mt-1" style="color: #1a3c2a;">42</h2>
                            <small class="text-danger"><i class="fas fa-arrow-down me-1"></i> -2%</small>
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
                            <span class="text-muted small">Faturacao</span>
                            <h2 class="fw-bold mt-1" style="color: #c9a84c;">Kz 12.450</h2>
                            <small class="text-success"><i class="fas fa-arrow-up me-1"></i> +18%</small>
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
                        <div class="text-center flex-grow-1">
                            <div class="barra" style="height: 120px; background: #1a3c2a; width: 30px; margin: 0 auto; border-radius: 6px 6px 0 0;"></div>
                            <small class="text-muted">Seg</small>
                        </div>
                        <div class="text-center flex-grow-1">
                            <div class="barra" style="height: 80px; background: #1a3c2a; width: 30px; margin: 0 auto; border-radius: 6px 6px 0 0;"></div>
                            <small class="text-muted">Ter</small>
                        </div>
                        <div class="text-center flex-grow-1">
                            <div class="barra" style="height: 160px; background: #c9a84c; width: 30px; margin: 0 auto; border-radius: 6px 6px 0 0;"></div>
                            <small class="text-muted">Qua</small>
                        </div>
                        <div class="text-center flex-grow-1">
                            <div class="barra" style="height: 200px; background: #c9a84c; width: 30px; margin: 0 auto; border-radius: 6px 6px 0 0;"></div>
                            <small class="text-muted">Qui</small>
                        </div>
                        <div class="text-center flex-grow-1">
                            <div class="barra" style="height: 140px; background: #1a3c2a; width: 30px; margin: 0 auto; border-radius: 6px 6px 0 0;"></div>
                            <small class="text-muted">Sex</small>
                        </div>
                        <div class="text-center flex-grow-1">
                            <div class="barra" style="height: 90px; background: #1a3c2a; width: 30px; margin: 0 auto; border-radius: 6px 6px 0 0;"></div>
                            <small class="text-muted">Sab</small>
                        </div>
                        <div class="text-center flex-grow-1">
                            <div class="barra" style="height: 180px; background: #c9a84c; width: 30px; margin: 0 auto; border-radius: 6px 6px 0 0;"></div>
                            <small class="text-muted">Dom</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card card-dashboard p-4">
                    <h6 class="fw-semibold mb-3">
                        <i class="fas fa-clock" style="color: #c9a84c;"></i> Ultimos Pedidos
                    </h6>
                    <ul class="list-unstyled">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span><span class="badge bg-success">#123</span> Joao Silva</span>
                            <span class="text-muted small">10 min</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span><span class="badge bg-warning text-dark">#122</span> Maria Santos</span>
                            <span class="text-muted small">25 min</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span><span class="badge bg-success">#121</span> Pedro Costa</span>
                            <span class="text-muted small">1h</span>
                        </li>
                        <li class="d-flex justify-content-between py-2">
                            <span><span class="badge bg-danger">#120</span> Ana Pereira</span>
                            <span class="text-muted small">1h 30min</span>
                        </li>
                    </ul>
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