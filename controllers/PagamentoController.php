<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/PagamentoModel.php';
require_once __DIR__ . '/../models/PedidoModel.php';
require_once __DIR__ . '/../models/LogModel.php';

class PagamentoController extends Controller
{
    private PagamentoModel $pagamentoModel;
    private PedidoModel $pedidoModel;
    private LogModel $logModel;

    private const ESTADOS_VALIDOS = ['Pago', 'Pendente', 'Cancelado'];

    public function __construct()
    {
        Sessao::exigirPerfil('Administrador', 'Operador');
        $this->pagamentoModel = new PagamentoModel();
        $this->pedidoModel = new PedidoModel();
        $this->logModel = new LogModel();
    }

    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/admin/pagamentos.php');
        }

        $id = Validador::inteiro($_POST['id'] ?? '') ?: null;
        $pedidoId = Validador::inteiro($_POST['pedido_id'] ?? '');
        $valor = Validador::decimal($_POST['valor'] ?? '');
        $metodo = Validador::texto($_POST['metodo'] ?? '');
        $estado = $_POST['estado'] ?? 'Pendente';

        if (!$pedidoId || $valor === false || $valor <= 0 || !Validador::obrigatorio($metodo) || !in_array($estado, self::ESTADOS_VALIDOS, true)) {
            Sessao::flash('erro', 'Preenche o pedido, o valor e o metodo correctamente.');
            $this->redirecionar('/views/admin/pagamentos.php');
        }

        if (!$this->pedidoModel->buscarPorId($pedidoId)) {
            Sessao::flash('erro', 'Esse pedido nao existe.');
            $this->redirecionar('/views/admin/pagamentos.php');
        }

        $dados = [
            'pedido_id' => $pedidoId,
            'valor' => $valor,
            'metodo' => $metodo,
            'estado' => $estado,
        ];

        if ($id) {
            $this->pagamentoModel->atualizar($id, $dados);
            Sessao::flash('sucesso', 'Pagamento atualizado.');
        } else {
            $this->pagamentoModel->inserir($dados);
            $this->logModel->registar(
                Sessao::utilizadorAtual()['id'],
                'Registo de pagamento',
                "Pedido #$pedidoId, valor Kz $valor, metodo $metodo"
            );
            Sessao::flash('sucesso', 'Pagamento registado.');
        }

        $this->redirecionar('/views/admin/pagamentos.php');
    }

    public function eliminar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/admin/pagamentos.php');
        }

        $id = Validador::inteiro($_POST['id'] ?? '');
        $this->pagamentoModel->eliminar($id);
        Sessao::flash('sucesso', 'Pagamento eliminado.');
        $this->redirecionar('/views/admin/pagamentos.php');
    }
}
