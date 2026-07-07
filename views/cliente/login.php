<?php
require_once __DIR__ . '/../../inicializar.php';

if (Sessao::estaLogado()) {
    $perfil = Sessao::utilizadorAtual()['perfil'];
    $destino = in_array($perfil, ['Administrador', 'Operador'], true)
        ? '/views/admin/dashboard.php'
        : '/views/cliente/perfil-cliente.php';
    header("Location: $destino");
    exit;
}

$flash = Sessao::consumirFlash();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sabor Alma</title>
    
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
        </div>
    </nav>

    <section style="padding-top: 100px; min-height: 100vh; display: flex; align-items: center; background: #f5f0e8;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card shadow-lg border-0 rounded-4 p-4">
                        <div class="text-center mb-4">
                            <div style="width: 80px; height: 80px; background: #1a3c2a; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 35px; border: 3px solid #c9a84c;">
                                <span style="color: #c9a84c;">S</span>
                            </div>
                            <h3 class="fw-bold mt-3" style="color: #1a3c2a;">Bem-vindo</h3>
                            <p class="text-muted">Acesse sua conta Sabor Alma</p>
                        </div>

                        <form id="formLogin" method="post" action="/index.php?rota=login">
                            <?= Csrf::campo() ?>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control" id="emailLogin" placeholder="Digite seu email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Senha</label>
                                <div class="input-group">
                                    <input type="password" name="senha" class="form-control" id="senhaLogin" placeholder="Digite sua senha" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="mostrarSenha()">
                                        <i class="fas fa-eye" id="iconeOlho"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="lembrar" id="lembrar">
                                    <label class="form-check-label" for="lembrar">Lembrar-me</label>
                                </div>
                                <a href="#" class="text-decoration-none" style="color: #c9a84c;" data-bs-toggle="modal" data-bs-target="#modalRecuperar">Esqueceu a senha?</a>
                            </div>
                            <button type="submit" class="btn w-100 py-2" style="background: #c9a84c; color: #1a3c2a; font-weight: 700;">
                                <i class="fas fa-sign-in-alt me-2"></i> Entrar
                            </button>
                        </form>

                        <?php if ($flash): ?>
                            <div class="alert alert-<?= $flash['tipo'] === 'erro' ? 'danger' : 'success' ?> mt-3" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?= htmlspecialchars($flash['mensagem']) ?>
                            </div>
                        <?php endif; ?>

                        <hr class="my-4">

                        <p class="text-center">
                            Não tem conta? <a href="registo.php" class="fw-bold" style="color: #1a3c2a;">Registe-se</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MODAL RECUPERAR SENHA -->
    <div class="modal fade" id="modalRecuperar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="/index.php?rota=recuperar-senha">
                    <?= Csrf::campo() ?>
                    <div class="modal-header" style="background: #1a3c2a; color: white;">
                        <h5 class="modal-title">Recuperar Senha</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Digite seu email para receber as instruções:</p>
                        <input type="email" name="email" class="form-control" id="emailRecuperar" placeholder="exemplo@email.com" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn" style="background: #c9a84c; color: #1a3c2a;">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>