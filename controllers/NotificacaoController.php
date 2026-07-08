<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/NotificacaoModel.php';

class NotificacaoController extends Controller
{
    private NotificacaoModel $notificacaoModel;

    public function __construct()
    {
        Sessao::exigirPerfil('Administrador', 'Operador');
        $this->notificacaoModel = new NotificacaoModel();
    }

    public function marcarTodas(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/admin/dashboard.php');
        }

        $this->notificacaoModel->marcarTodasComoLidas(Sessao::utilizadorAtual()['id']);

        // Volta para a pagina de onde veio o clique, para nao tirar
        // o operador do que estava a fazer.
        $voltar = $_SERVER['HTTP_REFERER'] ?? '/views/admin/dashboard.php';
        $this->redirecionar($voltar);
    }
}
