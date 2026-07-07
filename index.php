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

    case 'categorias.guardar':
        (new CategoriaController())->guardar();
        break;

    case 'categorias.eliminar':
        (new CategoriaController())->eliminar();
        break;

    case 'produtos.guardar':
        (new ProdutoController())->guardar();
        break;

    case 'produtos.eliminar':
        (new ProdutoController())->eliminar();
        break;

    case 'mesas.guardar':
        (new MesaController())->guardar();
        break;

    case 'mesas.eliminar':
        (new MesaController())->eliminar();
        break;

    case 'clientes.guardar':
        (new ClienteController())->guardar();
        break;

    case 'clientes.eliminar':
        (new ClienteController())->eliminar();
        break;

    case 'pedidos.criar':
        (new PedidoController())->criar();
        break;

    case 'pedidos.estado':
        (new PedidoController())->atualizarEstado();
        break;

    case 'pedidos.eliminar':
        (new PedidoController())->eliminar();
        break;

    default:
        header('Location: /views/cliente/index.php');
        exit;
}
