<?php

// Carrega o mesmo ponto de entrada que o site usa, para os testes
// terem acesso as mesmas classes (Validador, Model, ReservaModel,
// etc.) sem duplicar nenhum require. Corre bem em CLI: sem pedido
// HTTP nao ha sessao logada, entao a verificacao de sessao em
// inicializar.php e ignorada sozinha.
require_once __DIR__ . '/../inicializar.php';
