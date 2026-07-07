<?php

// Nunca mostrar erros do PHP directo no browser: um erro pode
// revelar caminhos do servidor, nomes de tabelas ou outros detalhes
// internos a quem esta a visitar o site. Fica tudo registado num
// ficheiro em vez disso, para continuarmos a conseguir depurar.
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/assets/cache/php-erros.log');

// Ponto unico que carrega as pecas base do backend. Tanto o index.php
// como as views protegidas comecam por incluir este ficheiro.
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/Sessao.php';
require_once __DIR__ . '/config/Csrf.php';
require_once __DIR__ . '/config/Validador.php';
require_once __DIR__ . '/config/Mailer.php';
require_once __DIR__ . '/config/Upload.php';
require_once __DIR__ . '/config/ExchangeRate.php';
require_once __DIR__ . '/models/Model.php';
require_once __DIR__ . '/models/UsuarioModel.php';
require_once __DIR__ . '/models/RelatorioModel.php';
require_once __DIR__ . '/controllers/Controller.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/UtilizadorController.php';
require_once __DIR__ . '/controllers/CategoriaController.php';
require_once __DIR__ . '/controllers/ProdutoController.php';
require_once __DIR__ . '/controllers/MesaController.php';
require_once __DIR__ . '/controllers/ClienteController.php';
require_once __DIR__ . '/controllers/PedidoController.php';
require_once __DIR__ . '/controllers/PagamentoController.php';

Sessao::iniciar();
