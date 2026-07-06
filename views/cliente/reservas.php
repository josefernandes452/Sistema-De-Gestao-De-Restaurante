<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas - Sabor Alma</title>
    
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
                    <li class="nav-item"><a class="nav-link active" href="reservas.php">Reservas</a></li>
                    <li class="nav-item"><a class="nav-link" href="pedidos.php">Pedidos</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Entrar</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- RESERVAS -->
    <section style="padding-top: 100px; padding-bottom: 60px; background: var(--creme);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-lg border-0 rounded-4 p-4">
                        <h2 class="text-center fw-bold" style="color: var(--verde-escuro);">
                            <i class="fas fa-calendar-check" style="color: var(--dourado);"></i> Faça sua Reserva
                        </h2>
                        <p class="text-center text-muted">Preencha os dados abaixo para garantir a sua mesa</p>

                        <form onsubmit="return fazerReserva(event)">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Nome Completo</label>
                                    <input type="text" class="form-control" id="nomeReserva" placeholder="Seu nome" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Telefone</label>
                                    <input type="tel" class="form-control" id="telefoneReserva" placeholder="+244 900 000 000" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Data</label>
                                    <input type="date" class="form-control" id="dataReserva" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Hora</label>
                                    <input type="time" class="form-control" id="horaReserva" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Número de Pessoas</label>
                                    <input type="number" class="form-control" id="pessoasReserva" placeholder="2" min="1" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Ocasião</label>
                                    <select class="form-select" id="ocasiaoReserva">
                                        <option value="Jantar">Jantar</option>
                                        <option value="Almoço">Almoço</option>
                                        <option value="Aniversário">Aniversário</option>
                                        <option value="Encontro">Encontro</option>
                                        <option value="Outro">Outro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Observações</label>
                                <textarea class="form-control" id="obsReserva" rows="3" placeholder="Alguma preferência especial?"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="fas fa-check me-2"></i> Confirmar Reserva
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>