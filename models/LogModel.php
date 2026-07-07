<?php

require_once __DIR__ . '/Model.php';

class LogModel extends Model
{
    protected string $tabela = 'logs';

    // Atalho para nao ter de escrever o array toda vez que uma
    // accao sensivel acontece (login, mudar estado de um pedido, etc).
    public function registar(?int $utilizadorId, string $acao, ?string $detalhes = null): void
    {
        $this->inserir([
            'utilizador_id' => $utilizadorId,
            'acao' => $acao,
            'detalhes' => $detalhes,
        ]);
    }
}
