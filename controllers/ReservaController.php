<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/ReservaModel.php';
require_once __DIR__ . '/../models/ClienteModel.php';
require_once __DIR__ . '/../models/LogModel.php';

class ReservaController extends Controller
{
    private ReservaModel $reservaModel;
    private ClienteModel $clienteModel;
    private LogModel $logModel;

    private const ESTADOS_VALIDOS = ['Confirmada', 'Cancelada', 'Concluida'];

    // Sem exigirPerfil() no construtor: criar() e publico (o schema
    // ja previa reserva sem conta, "cliente_id" e opcional), so
    // atualizarEstado() e eliminar() sao coisa de Administrador/Operador.
    public function __construct()
    {
        $this->reservaModel = new ReservaModel();
        $this->clienteModel = new ClienteModel();
        $this->logModel = new LogModel();
    }

    public function criar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            Sessao::flash('erro', 'A sessao expirou, tenta outra vez.');
            $this->redirecionar('/views/cliente/reservas.php');
        }

        $mesaId = Validador::inteiro($_POST['mesa_id'] ?? '');
        $nome = Validador::texto($_POST['nome'] ?? '');
        $telefone = Validador::texto($_POST['telefone'] ?? '');
        $data = Validador::texto($_POST['data'] ?? '');
        $hora = Validador::texto($_POST['hora'] ?? '');
        $pessoas = Validador::inteiro($_POST['pessoas'] ?? '');
        $observacoes = Validador::texto($_POST['observacoes'] ?? '');

        if (!$mesaId || !Validador::obrigatorio($nome) || !Validador::obrigatorio($telefone) || !$data || !$hora || !$pessoas) {
            Sessao::flash('erro', 'Preenche todos os campos obrigatorios da reserva.');
            $this->redirecionar('/views/cliente/reservas.php');
        }

        // Se quem esta a reservar tiver sessao de Cliente, ligamos a
        // reserva a conta dele. Se nao (visitante sem conta), fica
        // so com nome e telefone, tal como o schema ja previa.
        $clienteId = null;
        if (Sessao::estaLogado() && Sessao::utilizadorAtual()['perfil'] === 'Cliente') {
            $cliente = $this->clienteModel->buscarPorUtilizadorId(Sessao::utilizadorAtual()['id']);
            $clienteId = $cliente ? $cliente['id'] : null;
        }

        $reservaId = $this->reservaModel->inserir([
            'mesa_id' => $mesaId,
            'cliente_id' => $clienteId,
            'nome' => $nome,
            'telefone' => $telefone,
            'data' => $data,
            'hora' => $hora,
            'pessoas' => $pessoas,
            'estado' => 'Confirmada',
        ]);

        $this->logModel->registar(
            Sessao::estaLogado() ? Sessao::utilizadorAtual()['id'] : null,
            'Reserva criada',
            "Reserva #$reservaId, mesa #$mesaId, $data $hora"
        );

        Sessao::flash('sucesso', 'Reserva confirmada! Esperamos por ti.');
        $this->redirecionar('/views/cliente/reservas.php');
    }

    public function atualizarEstado(): void
    {
        Sessao::exigirPerfil('Administrador', 'Operador');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/admin/reservas.php');
        }

        $id = Validador::inteiro($_POST['id'] ?? '');
        $estado = $_POST['estado'] ?? '';

        if (!$id || !in_array($estado, self::ESTADOS_VALIDOS, true)) {
            Sessao::flash('erro', 'Estado invalido.');
            $this->redirecionar('/views/admin/reservas.php');
        }

        $this->reservaModel->atualizar($id, ['estado' => $estado]);

        $this->logModel->registar(
            Sessao::utilizadorAtual()['id'],
            'Mudanca de estado da reserva',
            "Reserva #$id passou para $estado"
        );

        Sessao::flash('sucesso', 'Estado da reserva atualizado.');
        $this->redirecionar('/views/admin/reservas.php');
    }

    public function eliminar(): void
    {
        Sessao::exigirPerfil('Administrador', 'Operador');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/admin/reservas.php');
        }

        $id = Validador::inteiro($_POST['id'] ?? '');
        $this->reservaModel->eliminar($id);

        $this->logModel->registar(Sessao::utilizadorAtual()['id'], 'Reserva eliminada', "Reserva #$id");

        Sessao::flash('sucesso', 'Reserva eliminada.');
        $this->redirecionar('/views/admin/reservas.php');
    }
}
