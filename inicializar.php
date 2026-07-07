<?php

// Ponto unico que carrega as pecas base do backend. Tanto o index.php
// como as views protegidas comecam por incluir este ficheiro.
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/Sessao.php';
require_once __DIR__ . '/config/Csrf.php';
require_once __DIR__ . '/config/Validador.php';
require_once __DIR__ . '/models/Model.php';
require_once __DIR__ . '/models/UsuarioModel.php';
require_once __DIR__ . '/controllers/Controller.php';
require_once __DIR__ . '/controllers/AuthController.php';

Sessao::iniciar();
