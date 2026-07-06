<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Sabor Alma</title>
    
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
                    <li class="nav-item"><a class="nav-link active" href="menu.php">Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="reservas.php">Reservas</a></li>
                    <li class="nav-item"><a class="nav-link" href="pedidos.php">Pedidos</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Entrar</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- MENU -->
    <section style="padding-top: 100px; padding-bottom: 60px; background: var(--creme);">
        <div class="container">
            <h1 class="text-center fw-bold mb-2" style="color: var(--verde-escuro);">
                <span style="color: var(--dourado);">✦</span> Nosso Menu <span style="color: var(--dourado);">✦</span>
            </h1>
            <p class="text-center text-muted mb-5">Escolha o seu prato favorito</p>

            <!-- Filtros -->
            <div class="d-flex flex-wrap justify-content-center gap-2 mb-5">
                <button class="btn btn-outline-primary active" onclick="filtrarMenu('todos')">Todos</button>
                <button class="btn btn-outline-primary" onclick="filtrarMenu('entradas')">Entradas</button>
                <button class="btn btn-outline-primary" onclick="filtrarMenu('principais')">Pratos Principais</button>
                <button class="btn btn-outline-primary" onclick="filtrarMenu('bebidas')">Bebidas</button>
                <button class="btn btn-outline-primary" onclick="filtrarMenu('sobremesas')">Sobremesas</button>
            </div>

            <!-- Lista de Produtos -->
            <div class="row g-4" id="listaMenu">
                <!-- Itens serão inseridos via JavaScript -->
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer style="background: var(--verde-escuro); color: white; padding: 30px 0;">
        <div class="container text-center">
            <p class="text-muted small">&copy; 2026 Sabor Alma - Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>