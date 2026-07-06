<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registo - Sabor Alma</title>
    
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
                            <h3 class="fw-bold mt-3" style="color: #1a3c2a;">Criar Conta</h3>
                            <p class="text-muted">Junte-se a familia Sabor Alma</p>
                        </div>

                        <form onsubmit="return registarCliente(event)">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nome Completo</label>
                                <input type="text" class="form-control" id="nomeRegisto" placeholder="Seu nome completo" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" id="emailRegisto" placeholder="exemplo@email.com" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Telefone</label>
                                <input type="tel" class="form-control" id="telefoneRegisto" placeholder="+244 900 000 000" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Senha</label>
                                <input type="password" class="form-control" id="senhaRegisto" placeholder="Minimo 6 caracteres" required minlength="6">
                            </div>
                            <button type="submit" class="btn w-100 py-2" style="background: #c9a84c; color: #1a3c2a; font-weight: 700;">
                                <i class="fas fa-user-plus me-2"></i> Criar Conta
                            </button>
                        </form>

                        <hr class="my-4">

                        <p class="text-center">
                            Ja tem conta? <a href="login.php" class="fw-bold" style="color: #1a3c2a;">Faca login</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>