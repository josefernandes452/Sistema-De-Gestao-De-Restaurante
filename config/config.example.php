<?php

// Copia este ficheiro para config.php e ajusta os valores para a tua máquina.
// O config.php não vai para o GitHub (está no .gitignore), por isso cada um
// mantém os seus próprios dados de ligação sem risco de sobrescrever os do outro.

return [
    'host' => 'localhost',
    'nome' => 'sabor_alma',
    'utilizador' => 'root',
    'senha' => '',
    'charset' => 'utf8mb4',

    // Para a recuperacao de senha por email (Gmail com "palavra-passe de
    // aplicacao", gerada em myaccount.google.com/apppasswords).
    'email' => [
        'host' => 'smtp.gmail.com',
        'porta' => 587,
        'utilizador' => 'o-teu-email@gmail.com',
        'senha' => 'a-tua-palavra-passe-de-aplicacao',
        'remetente_nome' => 'Sabor Alma',
    ],
];
