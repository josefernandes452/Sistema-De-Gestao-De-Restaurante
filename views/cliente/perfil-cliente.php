<?php
require_once __DIR__ . "/../../inicializar.php";
$utilizadorLogado = Sessao::exigirPerfil("Cliente");

$clienteModel = new ClienteModel();
$pedidoModel = new PedidoModel();

$cliente = $clienteModel->buscarPorUtilizadorId($utilizadorLogado['id']);
$historicoPedidos = $cliente ? $pedidoModel->porCliente($cliente['id']) : [];

$corEstadoPedido = [
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
    <title>Perfil - Sabor Alma</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: #1a3c2a;">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <span class="fw-bold" style="color: #c9a84c;">Sabor Alma</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSite">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSite">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="menu.php">Menu</a></li>
                    <li class="nav-item"><a class="nav-link active" href="perfil-cliente.php">Perfil</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" onclick="logout()" style="color: #dc3545;">Sair</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section style="padding-top: 100px; padding-bottom: 60px; background: #f5f0e8; min-height: 100vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-lg border-0 rounded-4 p-4">
                        <div class="text-center mb-4">
                            <div id="perfilAvatar" style="width: 100px; height: 100px; border-radius: 50%; background: #c9a84c; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 40px; color: white; font-weight: 700;">J</div>
                            <h4 class="fw-bold mt-3" id="perfilNome" style="color: #1a3c2a;">-</h4>
                            <p class="text-muted" id="perfilEmail">-</p>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nome Completo</label>
                                <input type="text" class="form-control" id="perfilNomeInput" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" id="perfilEmailInput" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Telefone</label>
                                <input type="tel" class="form-control" id="perfilTelefoneInput" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Tipo</label>
                                <input type="text" class="form-control" value="Cliente" disabled>
                            </div>
                        </div>

                        <hr>

                        <div class="text-end">
                            <button class="btn" style="background: #c9a84c; color: #1a3c2a;" onclick="logout()">
                                <i class="fas fa-sign-out-alt me-1"></i> Sair
                            </button>
                        </div>
                    </div>

                    <!-- HISTORICO DE PEDIDOS -->
                    <div class="card shadow-lg border-0 rounded-4 p-4 mt-4">
                        <h5 class="fw-bold mb-3" style="color: #1a3c2a;">
                            <i class="fas fa-receipt me-2"></i> Os Meus Pedidos
                        </h5>
                        <?php if (empty($historicoPedidos)): ?>
                            <p class="text-muted text-center py-3 mb-0">Ainda nao fizeste nenhum pedido.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Mesa</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Data</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($historicoPedidos as $p): ?>
                                            <tr>
                                                <td>#<?= $p['id'] ?></td>
                                                <td>Mesa <?= $p['mesa_numero'] ?></td>
                                                <td><strong>Kz <?= number_format((float) $p['total'], 2) ?></strong></td>
                                                <td><span class="badge bg-<?= $corEstadoPedido[$p['estado']] ?? 'secondary' ?>"><?= htmlspecialchars($p['estado']) ?></span></td>
                                                <td class="text-muted small"><?= htmlspecialchars($p['criado_em']) ?></td>
                                                <td class="text-end">
                                                    <a href="acompanhamento.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>window.usuarioLogado = <?= json_encode($utilizadorLogado) ?>;</script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>