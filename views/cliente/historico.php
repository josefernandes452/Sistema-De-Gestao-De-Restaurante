<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico - Sabor Alma</title>
    
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
                    <li class="nav-item"><a class="nav-link" href="pedidos.php">Pedidos</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Entrar</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- HISTÓRICO -->
    <section style="padding-top: 100px; padding-bottom: 60px; background: var(--creme); min-height: 100vh;">
        <div class="container">
            <h2 class="text-center fw-bold" style="color: var(--verde-escuro);">
                <i class="fas fa-history" style="color: var(--dourado);"></i> Meu Histórico
            </h2>
            <p class="text-center text-muted mb-4">Acompanhe todos os seus pedidos anteriores</p>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#Pedido</th>
                                        <th>Data</th>
                                        <th>Itens</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody id="historicoPedidos">
                                    <tr>
                                        <td>#123</td>
                                        <td>30/06/2026</td>
                                        <td>3 itens</td>
                                        <td>Kz 3.100,00</td>
                                        <td><span class="badge bg-success">Entregue</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="pedirNovamente(123)">
                                                <i class="fas fa-redo me-1"></i> Pedir Novamente
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#122</td>
                                        <td>28/06/2026</td>
                                        <td>2 itens</td>
                                        <td>Kz 1.800,00</td>
                                        <td><span class="badge bg-success">Entregue</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="pedirNovamente(122)">
                                                <i class="fas fa-redo me-1"></i> Pedir Novamente
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#121</td>
                                        <td>25/06/2026</td>
                                        <td>4 itens</td>
                                        <td>Kz 4.200,00</td>
                                        <td><span class="badge bg-warning">Cancelado</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="pedirNovamente(121)">
                                                <i class="fas fa-redo me-1"></i> Pedir Novamente
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>