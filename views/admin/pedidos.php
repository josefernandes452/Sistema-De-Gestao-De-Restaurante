<?php
require_once __DIR__ . "/../../inicializar.php";
$utilizadorLogado = Sessao::exigirPerfil("Administrador", "Operador");

$pedidoModel = new PedidoModel();
$mesaModel = new MesaModel();
$clienteModel = new ClienteModel();
$produtoModel = new ProdutoModel();

$pesquisaCodigo = Validador::inteiro($_GET['codigo'] ?? '') ?: null;
$pesquisaDataInicio = Validador::texto($_GET['data_inicio'] ?? '') ?: null;
$pesquisaDataFim = Validador::texto($_GET['data_fim'] ?? '') ?: null;
$emPesquisa = $pesquisaCodigo || $pesquisaDataInicio || $pesquisaDataFim;

$lista = $pedidoModel->todosComDetalhes($pesquisaCodigo, $pesquisaDataInicio, $pesquisaDataFim);
$mesas = $mesaModel->todos();
$clientes = $clienteModel->todos();
$produtos = $produtoModel->todos();
$flash = Sessao::consumirFlash();

$corEstado = [
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
    <title>Pedidos - Sabor Alma Admin</title>
    
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
            <a href="pedidos.php" class="menu-item active"><i class="fas fa-clipboard-list"></i> Pedidos</a>
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
                <span class="fw-semibold ms-2">Gestao de Pedidos</span>
            </div>
            <div class="user-info">
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

        <form method="get" class="row g-2 mb-4 align-items-end">
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Codigo</label>
                <input type="number" name="codigo" class="form-control" placeholder="#" value="<?= htmlspecialchars((string) ($pesquisaCodigo ?? '')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">De</label>
                <input type="date" name="data_inicio" class="form-control" value="<?= htmlspecialchars($pesquisaDataInicio ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Ate</label>
                <input type="date" name="data_fim" class="form-control" value="<?= htmlspecialchars($pesquisaDataFim ?? '') ?>">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn flex-grow-1" style="background: #c9a84c; color: #1a3c2a;">
                    <i class="fas fa-search me-1"></i> Pesquisar
                </button>
                <?php if ($emPesquisa): ?>
                    <a href="pedidos.php" class="btn btn-outline-secondary" title="Limpar pesquisa"><i class="fas fa-times"></i></a>
                <?php endif; ?>
            </div>
        </form>

        <div class="row g-3 mb-4">
            <div class="col-12 text-md-end">
                <button class="btn" style="background: #c9a84c; color: #1a3c2a;" onclick="abrirModalPedido()">
                    <i class="fas fa-plus me-1"></i> Novo Pedido
                </button>
            </div>
        </div>

        <div class="card card-dashboard p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Mesa</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th class="text-center">Acoes</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaPedidos">
                        <?php foreach ($lista as $p): ?>
                            <tr>
                                <td>#<?= $p['id'] ?></td>
                                <td><?= htmlspecialchars($p['cliente_nome'] ?: 'Cliente avulso') ?></td>
                                <td>Mesa <?= $p['mesa_numero'] ?></td>
                                <td><strong>Kz <?= number_format((float) $p['total'], 2) ?></strong></td>
                                <td><span class="badge bg-<?= $corEstado[$p['estado']] ?? 'secondary' ?>"><?= htmlspecialchars($p['estado']) ?></span></td>
                                <td><?= htmlspecialchars($p['criado_em']) ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-info me-1"
                                        onclick="verPedido(this)"
                                        data-pedido="<?= htmlspecialchars(json_encode($p), ENT_QUOTES) ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success me-1"
                                        onclick="editarEstadoPedido(this)"
                                        data-id="<?= $p['id'] ?>"
                                        data-estado="<?= htmlspecialchars($p['estado']) ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarPedido(<?= $p['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between">
                <span class="text-muted small" id="totalPedidos">Total: <?= count($lista) ?> pedidos</span>
            </div>
        </div>

        <footer class="text-center text-muted small mt-4">&copy; 2026 Sabor Alma - Sistema de Gestao</footer>
    </div>

    <!-- MODAL NOVO PEDIDO -->
    <div class="modal fade" id="modalPedido" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formPedido" method="post" action="/index.php?rota=pedidos.criar">
                    <?= Csrf::campo() ?>
                    <div class="modal-header" style="background: #1a3c2a; color: white;">
                        <h5 class="modal-title"><i class="fas fa-clipboard-list me-2"></i> Novo Pedido</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Mesa</label>
                                <select class="form-select" name="mesa_id" id="mesaPedido" required>
                                    <option value="">Selecione</option>
                                    <?php foreach ($mesas as $m): ?>
                                        <option value="<?= $m['id'] ?>">Mesa <?= $m['numero'] ?> (<?= $m['capacidade'] ?> pessoas)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Cliente</label>
                                <select class="form-select" name="cliente_id" id="clientePedido">
                                    <option value="">Cliente avulso (sem conta)</option>
                                    <?php foreach ($clientes as $c): ?>
                                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Adicionar Produto</label>
                            <div class="row g-2">
                                <div class="col-7">
                                    <select class="form-select" id="produtoParaAdicionar">
                                        <?php foreach ($produtos as $p): ?>
                                            <option value="<?= $p['id'] ?>" data-nome="<?= htmlspecialchars($p['nome']) ?>" data-preco="<?= $p['preco'] ?>">
                                                <?= htmlspecialchars($p['nome']) ?> (Kz <?= number_format((float) $p['preco'], 2) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <input type="number" class="form-control" id="quantidadeParaAdicionar" value="1" min="1">
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-outline-success w-100" onclick="adicionarItemPedido()">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <table class="table table-sm">
                            <thead>
                                <tr><th>Produto</th><th>Qtd</th><th>Subtotal</th><th></th></tr>
                            </thead>
                            <tbody id="carrinhoPedidoTabela"></tbody>
                        </table>
                        <div class="text-end fw-bold mb-3">Total: <span id="totalCarrinhoPedido">Kz 0.00</span></div>

                        <div id="itensPedidoEscondidos"></div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Observacoes</label>
                            <textarea name="observacoes" class="form-control" id="observacoesPedido" rows="2" placeholder="Ex: sem cebola, alergia a amendoim..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn" style="background: #c9a84c; color: #1a3c2a;">
                            <i class="fas fa-save me-1"></i> Criar Pedido
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL VISUALIZAR PEDIDO -->
    <div class="modal fade" id="modalVerPedido" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: #1a3c2a; color: white;">
                    <h5 class="modal-title"><i class="fas fa-receipt me-2"></i> Detalhes do Pedido</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detalhesPedido"></div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL MUDAR ESTADO -->
    <div class="modal fade" id="modalEstadoPedido" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="/index.php?rota=pedidos.estado">
                    <?= Csrf::campo() ?>
                    <input type="hidden" name="id" id="estadoPedidoId">
                    <div class="modal-header" style="background: #1a3c2a; color: white;">
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i> Mudar Estado do Pedido</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label fw-semibold">Estado</label>
                        <select class="form-select" name="estado" id="novoEstadoPedido">
                            <option value="Pendente">Pendente</option>
                            <option value="Em Preparacao">Em Preparacao</option>
                            <option value="Pronto">Pronto</option>
                            <option value="Entregue">Entregue</option>
                            <option value="Cancelado">Cancelado</option>
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

    <form id="formEliminarPedido" method="post" action="/index.php?rota=pedidos.eliminar" style="display: none;">
        <?= Csrf::campo() ?>
        <input type="hidden" name="id" id="eliminarPedidoId">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/admin.js"></script>
</body>
</html>