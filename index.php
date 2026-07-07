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

    case 'recuperar-senha':
        (new AuthController())->recuperarSenha();
        break;

    case 'redefinir-senha':
        (new AuthController())->redefinirSenha();
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    case 'utilizadores.guardar':
        (new UtilizadorController())->guardar();
        break;

    case 'utilizadores.eliminar':
        (new UtilizadorController())->eliminar();
        break;

    default:
        header('Location: /views/cliente/index.php');
        exit;
}
