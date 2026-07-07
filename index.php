<?php

// Router simples: cada acao que precisa de logica de servidor (login,
// logout, etc.) passa por aqui. As paginas em si continuam a viver em views/.
require_once __DIR__ . '/inicializar.php';

$rota = $_GET['rota'] ?? '';

switch ($rota) {
    case 'login':
        (new AuthController())->login();
        break;

    case 'registar':
        (new AuthController())->registar();
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    default:
        header('Location: /views/cliente/index.php');
        exit;
}
