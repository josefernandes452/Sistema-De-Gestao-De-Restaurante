<?php
require_once __DIR__ . "/../../inicializar.php";
$utilizadorLogado = Sessao::exigirPerfil("Administrador", "Operador");

$produtoModel = new ProdutoModel();
$categoriaModel = new CategoriaModel();
$categorias = $categoriaModel->todos();
$flash = Sessao::consumirFlash();

$pesquisaNome = Validador::texto($_GET['nome'] ?? '');
$pesquisaCodigo = Validador::inteiro($_GET['codigo'] ?? '') ?: null;
$pesquisaCategoria = Validador::inteiro($_GET['categoria_id'] ?? '') ?: null;
$emPesquisa = $pesquisaNome !== '' || $pesquisaCodigo || $pesquisaCategoria;

$lista = $emPesquisa
    ? $produtoModel->pesquisar($pesquisaNome ?: null, $pesquisaCodigo, $pesquisaCategoria)
    : $produtoModel->todosComCategoria();
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
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Nome</label>
                <input type="text" name="nome" class="form-control" placeholder="Pesquisar por nome..." value="<?= htmlspecialchars($pesquisaNome) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Codigo</label>
                <input type="number" name="codigo" class="form-control" placeholder="#" value="<?= htmlspecialchars((string) ($pesquisaCodigo ?? '')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Categoria</label>
                <select name="categoria_id" class="form-select">
                    <option value="">Todas</option>
                    <?php foreach ($categorias as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $pesquisaCategoria === (int) $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn flex-grow-1" style="background: #c9a84c; color: #1a3c2a;">
                    <i class="fas fa-search me-1"></i> Pesquisar
                </button>
                <?php if ($emPesquisa): ?>
                    <a href="produtos.php" class="btn btn-outline-secondary" title="Limpar pesquisa"><i class="fas fa-times"></i></a>
                <?php endif; ?>
            </div>
        </form>

        <div class="text-md-end mb-3">
            <button class="btn" style="background: #c9a84c; color: #1a3c2a;" onclick="abrirModalProduto()">
                <i class="fas fa-plus me-1"></i> Novo Produto
            </button>
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
                    <tbody id="tabelaProdutos">
                        <?php foreach ($lista as $i => $p): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <?php if ($p['imagem']): ?>
                                        <img src="../../assets/uploads/<?= htmlspecialchars($p['imagem']) ?>" alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;">
                                    <?php else: ?>
                                        <div style="width: 40px; height: 40px; background: #e9ecef; border-radius: 8px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-utensils text-muted"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($p['nome']) ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($p['categoria_nome']) ?></span></td>
                                <td><strong>Kz <?= number_format((float) $p['preco'], 2) ?></strong></td>
                                <td><?= (int) $p['estoque'] ?></td>
                                <td><span class="badge bg-<?= $p['estado'] === 'Disponivel' ? 'success' : ($p['estado'] === 'Esgotado' ? 'danger' : 'warning') ?>"><?= htmlspecialchars($p['estado']) ?></span></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-success me-1"
                                        onclick="editarProduto(this)"
                                        data-id="<?= $p['id'] ?>"
                                        data-nome="<?= htmlspecialchars($p['nome']) ?>"
                                        data-categoria-id="<?= $p['categoria_id'] ?>"
                                        data-preco="<?= $p['preco'] ?>"
                                        data-estoque="<?= $p['estoque'] ?>"
                                        data-estado="<?= htmlspecialchars($p['estado']) ?>"
                                        data-descricao="<?= htmlspecialchars($p['descricao'] ?? '') ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarProduto(<?= $p['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between">
                <span class="text-muted small" id="totalProdutos">Total: <?= count($lista) ?> produtos</span>
            </div>
        </div>

        <footer class="text-center text-muted small mt-4">&copy; 2026 Sabor Alma - Sistema de Gestao</footer>
    </div>

    <!-- MODAL PRODUTO -->
    <div class="modal fade" id="modalProduto" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formProduto" method="post" action="/index.php?rota=produtos.guardar" enctype="multipart/form-data">
                    <?= Csrf::campo() ?>
                    <input type="hidden" name="id" id="produtoId">
                    <div class="modal-header" style="background: #1a3c2a; color: white;">
                        <h5 class="modal-title" id="modalProdutoTitulo"><i class="fas fa-box me-2"></i> Novo Produto</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nome</label>
                                <input type="text" name="nome" class="form-control" id="nomeProduto" placeholder="Ex: Bife a Casa" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Categoria</label>
                                <select class="form-select" name="categoria_id" id="categoriaProduto" required>
                                    <option value="">Selecione</option>
                                    <?php foreach ($categorias as $c): ?>
                                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Descricao</label>
                            <textarea name="descricao" class="form-control" id="descricaoProduto" rows="2" placeholder="Descricao (opcional)"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Preco (Kz)</label>
                                <input type="number" name="preco" class="form-control" id="precoProduto" placeholder="0.00" step="0.01" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Stock</label>
                                <input type="number" name="estoque" class="form-control" id="stockProduto" placeholder="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Status</label>
                                <select class="form-select" name="estado" id="statusProduto">
                                    <option value="Disponivel">Disponivel</option>
                                    <option value="Indisponivel">Indisponivel</option>
                                    <option value="Esgotado">Esgotado</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Imagem</label>
                            <input type="file" name="imagem" class="form-control" id="imagemProduto" accept="image/png, image/jpeg, image/webp" onchange="preVisualizarImagem(this)">
                            <div class="form-text">JPG, PNG ou WEBP, ate 2MB. Deixa em branco para manter a imagem actual.</div>
                            <img id="previaImagemProduto" src="" alt="" style="display: none; width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-top: 8px;">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn" style="background: #c9a84c; color: #1a3c2a;">
                            <i class="fas fa-save me-1"></i> <span id="btnSalvarProduto">Salvar</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="formEliminarProduto" method="post" action="/index.php?rota=produtos.eliminar" style="display: none;">
        <?= Csrf::campo() ?>
        <input type="hidden" name="id" id="eliminarProdutoId">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/admin.js"></script>
</body>
</html>