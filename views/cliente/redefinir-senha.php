<?php
require_once __DIR__ . '/../../inicializar.php';

$token = $_GET['token'] ?? '';
$flash = Sessao::consumirFlash();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - Sabor Alma</title>

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
                                <i class="fas fa-key" style="color: #c9a84c;"></i>
                            </div>
                            <h3 class="fw-bold mt-3" style="color: #1a3c2a;">Nova Senha</h3>
                            <p class="text-muted">Escolhe a nova senha da tua conta</p>
                        </div>

                        <form method="post" action="/index.php?rota=redefinir-senha">
                            <?= Csrf::campo() ?>
                            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nova Senha</label>
                                <input type="password" name="senha" class="form-control" placeholder="Minimo 6 caracteres" required minlength="6">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Confirmar Senha</label>
                                <input type="password" name="confirmar_senha" class="form-control" placeholder="Repete a senha" required minlength="6">
                            </div>
                            <button type="submit" class="btn w-100 py-2" style="background: #c9a84c; color: #1a3c2a; font-weight: 700;">
                                <i class="fas fa-save me-2"></i> Guardar Nova Senha
                            </button>
                        </form>

                        <?php if ($flash): ?>
                            <div class="alert alert-<?= $flash['tipo'] === 'erro' ? 'danger' : 'success' ?> mt-3" role="alert">
                                <?= htmlspecialchars($flash['mensagem']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>
</html>
