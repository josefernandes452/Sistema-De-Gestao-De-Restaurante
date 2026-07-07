<?php

// Singleton: a ligação PDO só é criada uma vez e reaproveitada
// em todos os models, em vez de abrir uma ligação nova a cada consulta.
class Database
{
    private static ?PDO $conexao = null;

    private function __construct()
    {
    }

    public static function getConexao(): PDO
    {
        if (self::$conexao === null) {
            $config = require __DIR__ . '/config.php';

            $dsn = "mysql:host={$config['host']};dbname={$config['nome']};charset={$config['charset']}";

            self::$conexao = new PDO($dsn, $config['utilizador'], $config['senha'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return self::$conexao;
    }
}
