<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sabor Alma - Restaurante</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <!-- ============================================
    HEADER / NAVBAR
    ============================================ -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: var(--verde-escuro);">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="../../assets/img/logo.png" alt="Sabor Alma" height="50" class="d-inline-block align-text-top">
                <span class="fw-bold ms-2" style="color: var(--dourado);">Sabor Alma</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSite">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarSite">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Início</a></li>
                    <li class="nav-item"><a class="nav-link" href="menu.php">Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="reservas.php">Reservas</a></li>
                    <li class="nav-item"><a class="nav-link" href="pedidos.php">Pedidos</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Entrar</a></li>
                    <li class="nav-item">
                        <a class="btn btn-outline-warning ms-2" href="registo.php">
                            <i class="fas fa-user-plus me-1"></i> Registe-se
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ============================================
    HERO SECTION
    ============================================ -->
    <section class="hero-section" style="background: linear-gradient(135deg, var(--verde-escuro) 0%, var(--verde-claro) 100%); padding-top: 120px; padding-bottom: 80px; min-height: 100vh; display: flex; align-items: center;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 text-white">
                    <h1 class="display-1 fw-bold" style="color: var(--dourado);">Sabor Alma</h1>
                    <p class="lead mb-4">Uma experiência gastronómica única, onde cada prato conta uma história de tradição e paixão.</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="menu.php" class="btn btn-lg" style="background: var(--dourado); color: var(--verde-escuro); font-weight: 700;">
                            <i class="fas fa-utensils me-2"></i> Ver Menu
                        </a>
                        <a href="reservas.php" class="btn btn-lg btn-outline-light">
                            <i class="fas fa-calendar-check me-2"></i> Fazer Reserva
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center mt-5 mt-lg-0">
                    <div class="card bg-transparent border-0">
                        <img src="../../assets/img/logo.png" alt="Sabor Alma" class="img-fluid" style="max-height: 400px; filter: drop-shadow(0 4px 20px rgba(0,0,0,0.3));">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
    CARDS DE SERVIÇOS
    ============================================ -->
    <section class="py-5" style="background: var(--creme);">
        <div class="container">
            <h2 class="text-center fw-bold mb-5" style="color: var(--verde-escuro);">
                <span style="color: var(--dourado);">✦</span> Nossos Serviços <span style="color: var(--dourado);">✦</span>
            </h2>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-utensils" style="font-size: 3rem; color: var(--dourado);"></i>
                            <h5 class="fw-bold mt-3" style="color: var(--verde-escuro);">Menu Digital</h5>
                            <p class="text-muted">Explore o nosso menu completo com descrições e preços.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-calendar-check" style="font-size: 3rem; color: var(--dourado);"></i>
                            <h5 class="fw-bold mt-3" style="color: var(--verde-escuro);">Reservas</h5>
                            <p class="text-muted">Faça a sua reserva online de forma rápida e fácil.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-shopping-bag" style="font-size: 3rem; color: var(--dourado);"></i>
                            <h5 class="fw-bold mt-3" style="color: var(--verde-escuro);">Self-Service</h5>
                            <p class="text-muted">Faça o seu pedido sem sair da mesa.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-truck" style="font-size: 3rem; color: var(--dourado);"></i>
                            <h5 class="fw-bold mt-3" style="color: var(--verde-escuro);">Delivery</h5>
                            <p class="text-muted">Receba os seus pratos favoritos em casa.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
    FOOTER
    ============================================ -->
    <footer style="background: var(--verde-escuro); color: white; padding: 40px 0;">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5 style="color: var(--dourado);">Sabor Alma</h5>
                    <p class="text-muted">Restaurante de comida tradicional com um toque moderno.</p>
                </div>
                <div class="col-md-4">
                    <h5 style="color: var(--dourado);">Horário</h5>
                    <p class="text-muted">Seg-Sex: 12h - 23h<br>Sáb-Dom: 11h - 00h</p>
                </div>
                <div class="col-md-4">
                    <h5 style="color: var(--dourado);">Contacto</h5>
                    <p class="text-muted">
                        <i class="fas fa-phone me-2"></i> +244 900 000 000<br>
                        <i class="fas fa-envelope me-2"></i> info@saboralma.ao
                    </p>
                </div>
            </div>
            <hr style="border-color: rgba(255,255,255,0.1);">
            <p class="text-center text-muted small">&copy; 2026 Sabor Alma - Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>