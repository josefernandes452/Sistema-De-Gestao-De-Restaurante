<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/PagamentoModel.php';
require_once __DIR__ . '/../models/PedidoModel.php';
require_once __DIR__ . '/../models/LogModel.php';
require_once __DIR__ . '/../models/ClienteModel.php';
require_once __DIR__ . '/../models/MesaModel.php';
require_once __DIR__ . '/../models/NotificacaoModel.php';

class PagamentoController extends Controller
{
    private PagamentoModel $pagamentoModel;
    private PedidoModel $pedidoModel;
    private LogModel $logModel;
    private ClienteModel $clienteModel;
    private MesaModel $mesaModel;

    private const ESTADOS_VALIDOS = ['Pago', 'Pendente', 'Cancelado'];

    // Metodos que o cliente pode escolher quando paga ele mesmo, na
    // pagina de acompanhamento. O operador continua a poder registar
    // Dinheiro/Transferencia a mao, isso nao muda.
    private const METODOS_CLIENTE = ['Cartao de Credito', 'Cartao de Debito', 'Multicaixa Express'];

    // Sem exigirPerfil() no construtor: guardar()/eliminar() sao do
    // operador, pagarComoCliente() e do proprio cliente. Cada metodo
    // verifica o perfil que precisa.
    public function __construct()
    {
        $this->pagamentoModel = new PagamentoModel();
        $this->pedidoModel = new PedidoModel();
        $this->logModel = new LogModel();
        $this->clienteModel = new ClienteModel();
        $this->mesaModel = new MesaModel();
    }

    public function guardar(): void
    {
        Sessao::exigirPerfil('Administrador', 'Operador');

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
        Sessao::exigirPerfil('Administrador', 'Operador');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/admin/pagamentos.php');
        }

        $id = Validador::inteiro($_POST['id'] ?? '');
        $this->pagamentoModel->eliminar($id);
        Sessao::flash('sucesso', 'Pagamento eliminado.');
        $this->redirecionar('/views/admin/pagamentos.php');
    }

    // O cliente so pode pagar um pedido que seja mesmo dele e que ja
    // esteja "Pronto" (a cozinha ja preparou, falta so entregar). Ao
    // confirmar, o pagamento fica logo "Pago" e o pedido passa
    // automaticamente para "Entregue", conforme o fluxo combinado:
    // Pendente -> Em Preparacao -> Pronto -> (cliente paga) -> Entregue.
    public function pagarComoCliente(): void
    {
        Sessao::exigirPerfil('Cliente');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            Sessao::flash('erro', 'A sessao expirou, tenta outra vez.');
            $this->redirecionar('/views/cliente/pedidos.php');
        }

        $pedidoId = Validador::inteiro($_POST['pedido_id'] ?? '');
        $metodo = Validador::texto($_POST['metodo'] ?? '');

        if (!$pedidoId || !in_array($metodo, self::METODOS_CLIENTE, true)) {
            Sessao::flash('erro', 'Escolhe um metodo de pagamento valido.');
            $this->redirecionar('/views/cliente/pedidos.php');
        }

        $utilizadorId = Sessao::utilizadorAtual()['id'];
        $cliente = $this->clienteModel->buscarPorUtilizadorId($utilizadorId);
        $pedido = $cliente ? $this->pedidoModel->buscarPorId($pedidoId) : false;

        // So deixa pagar se o pedido for mesmo deste cliente. Sem
        // isto, bastava adivinhar um numero de pedido na URL para
        // pagar (ou marcar como entregue) o pedido de outra pessoa.
        if (!$pedido || (int) $pedido['cliente_id'] !== (int) $cliente['id']) {
            Sessao::flash('erro', 'Nao encontramos esse pedido.');
            $this->redirecionar('/views/cliente/pedidos.php');
        }

        if ($pedido['estado'] !== 'Pronto') {
            Sessao::flash('erro', 'Este pedido ainda nao esta pronto para pagamento.');
            $this->redirecionar('/views/cliente/acompanhamento.php?id=' . $pedidoId);
        }

        $this->pagamentoModel->inserir([
            'pedido_id' => $pedidoId,
            'valor' => $pedido['total'],
            'metodo' => $metodo,
            'origem' => 'Cliente',
            'estado' => 'Pago',
        ]);

        $this->pedidoModel->atualizar($pedidoId, ['estado' => 'Entregue']);
        $this->mesaModel->atualizar($pedido['mesa_id'], ['estado' => 'Livre']);

        $this->logModel->registar($utilizadorId, 'Pagamento feito pelo cliente', "Pedido #$pedidoId, metodo $metodo");

        (new NotificacaoModel())->criarParaPerfis(
            ['Administrador', 'Operador'],
            "Pagamento recebido do pedido #$pedidoId ($metodo)",
            '/views/admin/pagamentos.php'
        );

        Sessao::flash('sucesso', 'Pagamento confirmado! O teu pedido foi entregue.');
        $this->redirecionar('/views/cliente/acompanhamento.php?id=' . $pedidoId);
    }
}
