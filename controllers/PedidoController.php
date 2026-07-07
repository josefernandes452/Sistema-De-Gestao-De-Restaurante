<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/PedidoModel.php';
require_once __DIR__ . '/../models/ProdutoModel.php';
require_once __DIR__ . '/../models/MesaModel.php';
require_once __DIR__ . '/../models/LogModel.php';
require_once __DIR__ . '/../models/ClienteModel.php';

class PedidoController extends Controller
{
    private PedidoModel $pedidoModel;
    private ProdutoModel $produtoModel;
    private MesaModel $mesaModel;
    private LogModel $logModel;
    private ClienteModel $clienteModel;

    private const ESTADOS_VALIDOS = ['Pendente', 'Em Preparacao', 'Pronto', 'Entregue', 'Cancelado'];

    // Quando o pedido chega a um destes estados, a mesa fica livre outra vez.
    private const ESTADOS_QUE_LIBERTAM_MESA = ['Entregue', 'Cancelado'];

    // Nao ha exigirPerfil() aqui no construtor porque este controller
    // agora serve dois publicos diferentes: o operador (criar, mudar
    // estado, eliminar) e o proprio cliente (criarComoCliente,
    // estadoJson). Cada metodo verifica o perfil que precisa.
    public function __construct()
    {
        $this->pedidoModel = new PedidoModel();
        $this->produtoModel = new ProdutoModel();
        $this->mesaModel = new MesaModel();
        $this->logModel = new LogModel();
        $this->clienteModel = new ClienteModel();
    }

    public function criar(): void
    {
        Sessao::exigirPerfil('Administrador', 'Operador');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/admin/pedidos.php');
        }

        $mesaId = Validador::inteiro($_POST['mesa_id'] ?? '');
        $clienteId = Validador::inteiro($_POST['cliente_id'] ?? '') ?: null;
        $observacoes = Validador::texto($_POST['observacoes'] ?? '');
        $produtoIds = $_POST['produto_id'] ?? [];
        $quantidades = $_POST['quantidade'] ?? [];

        if (!$mesaId || empty($produtoIds)) {
            Sessao::flash('erro', 'Escolhe a mesa e pelo menos um produto.');
            $this->redirecionar('/views/admin/pedidos.php');
        }

        // O preco de cada produto vem sempre da base de dados, nunca
        // do que o formulario mandou, para ninguem conseguir mudar o
        // preco de um pedido só editando o HTML da pagina.
        $itens = [];
        foreach ($produtoIds as $indice => $produtoId) {
            $produto = $this->produtoModel->buscarPorId((int) $produtoId);
            $quantidade = (int) ($quantidades[$indice] ?? 0);

            if (!$produto || $quantidade < 1) {
                continue;
            }

            $itens[] = [
                'produto_id' => $produto['id'],
                'quantidade' => $quantidade,
                'preco_unitario' => $produto['preco'],
            ];
        }

        if (empty($itens)) {
            Sessao::flash('erro', 'Escolhe pelo menos um produto valido.');
            $this->redirecionar('/views/admin/pedidos.php');
        }

        $dadosPedido = [
            'mesa_id' => $mesaId,
            'cliente_id' => $clienteId,
            'utilizador_id' => Sessao::utilizadorAtual()['id'],
            'observacoes' => $observacoes ?: null,
            'estado' => 'Pendente',
        ];

        $pedidoId = $this->pedidoModel->criarComItens($dadosPedido, $itens);
        $this->mesaModel->atualizar($mesaId, ['estado' => 'Ocupada']);

        $this->logModel->registar(
            Sessao::utilizadorAtual()['id'],
            'Criacao de pedido',
            "Pedido #$pedidoId, mesa #$mesaId"
        );

        Sessao::flash('sucesso', 'Pedido criado.');
        $this->redirecionar('/views/admin/pedidos.php');
    }

    public function atualizarEstado(): void
    {
        Sessao::exigirPerfil('Administrador', 'Operador');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/admin/pedidos.php');
        }

        $id = Validador::inteiro($_POST['id'] ?? '');
        $estado = $_POST['estado'] ?? '';

        if (!$id || !in_array($estado, self::ESTADOS_VALIDOS, true)) {
            Sessao::flash('erro', 'Estado invalido.');
            $this->redirecionar('/views/admin/pedidos.php');
        }

        $pedido = $this->pedidoModel->buscarPorId($id);

        $this->pedidoModel->atualizar($id, ['estado' => $estado]);

        if ($pedido && in_array($estado, self::ESTADOS_QUE_LIBERTAM_MESA, true)) {
            $this->mesaModel->atualizar($pedido['mesa_id'], ['estado' => 'Livre']);
        }

        $this->logModel->registar(
            Sessao::utilizadorAtual()['id'],
            'Mudanca de estado do pedido',
            "Pedido #$id passou para $estado"
        );

        Sessao::flash('sucesso', 'Estado do pedido atualizado.');
        $this->redirecionar('/views/admin/pedidos.php');
    }

    public function eliminar(): void
    {
        Sessao::exigirPerfil('Administrador', 'Operador');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/admin/pedidos.php');
        }

        $id = Validador::inteiro($_POST['id'] ?? '');
        $pedido = $this->pedidoModel->buscarPorId($id);

        $this->pedidoModel->eliminar($id);

        if ($pedido) {
            $this->mesaModel->atualizar($pedido['mesa_id'], ['estado' => 'Livre']);
        }

        $this->logModel->registar(
            Sessao::utilizadorAtual()['id'],
            'Eliminacao de pedido',
            "Pedido #$id"
        );

        Sessao::flash('sucesso', 'Pedido eliminado.');
        $this->redirecionar('/views/admin/pedidos.php');
    }

    // Versao do "criar" para quando quem esta a fazer o pedido e o
    // proprio cliente, no site publico (views/cliente/pedidos.php).
    // Ao contrario do criar() do operador, aqui nao ha um campo
    // "cliente_id" no formulario: descobrimos o cliente a partir da
    // conta que esta logada.
    public function criarComoCliente(): void
    {
        Sessao::exigirPerfil('Cliente');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            Sessao::flash('erro', 'A sessao expirou, tenta outra vez.');
            $this->redirecionar('/views/cliente/pedidos.php');
        }

        $mesaId = Validador::inteiro($_POST['mesa_id'] ?? '');
        $observacoes = Validador::texto($_POST['observacoes'] ?? '');
        $produtoIds = $_POST['produto_id'] ?? [];
        $quantidades = $_POST['quantidade'] ?? [];

        if (!$mesaId || empty($produtoIds)) {
            Sessao::flash('erro', 'Escolhe a tua mesa e pelo menos um produto.');
            $this->redirecionar('/views/cliente/pedidos.php');
        }

        // Mesmo cuidado do lado do operador: o preco vem sempre da
        // base de dados, nunca do formulario.
        $itens = [];
        foreach ($produtoIds as $indice => $produtoId) {
            $produto = $this->produtoModel->buscarPorId((int) $produtoId);
            $quantidade = (int) ($quantidades[$indice] ?? 0);

            if (!$produto || $produto['estado'] !== 'Disponivel' || $quantidade < 1) {
                continue;
            }

            $itens[] = [
                'produto_id' => $produto['id'],
                'quantidade' => $quantidade,
                'preco_unitario' => $produto['preco'],
            ];
        }

        if (empty($itens)) {
            Sessao::flash('erro', 'Escolhe pelo menos um produto valido.');
            $this->redirecionar('/views/cliente/pedidos.php');
        }

        $utilizadorId = Sessao::utilizadorAtual()['id'];
        $cliente = $this->clienteModel->buscarPorUtilizadorId($utilizadorId);

        $dadosPedido = [
            'mesa_id' => $mesaId,
            'cliente_id' => $cliente ? $cliente['id'] : null,
            'utilizador_id' => null,
            'observacoes' => $observacoes ?: null,
            'estado' => 'Pendente',
        ];

        $pedidoId = $this->pedidoModel->criarComItens($dadosPedido, $itens);
        $this->mesaModel->atualizar($mesaId, ['estado' => 'Ocupada']);

        $this->logModel->registar($utilizadorId, 'Pedido feito pelo cliente', "Pedido #$pedidoId, mesa #$mesaId");

        $this->redirecionar('/views/cliente/acompanhamento.php?id=' . $pedidoId);
    }

    // Endpoint JSON usado por acompanhamento.php para atualizar o
    // estado do pedido sem o cliente ter de recarregar a pagina.
    // So devolve o pedido se ele pertencer mesmo ao cliente logado,
    // para ninguem conseguir espreitar o pedido de outra pessoa so
    // por adivinhar o numero na URL.
    public function estadoJson(): void
    {
        Sessao::exigirPerfil('Cliente');
        header('Content-Type: application/json');

        $id = Validador::inteiro($_GET['id'] ?? '');
        $utilizadorId = Sessao::utilizadorAtual()['id'];
        $cliente = $this->clienteModel->buscarPorUtilizadorId($utilizadorId);

        $pedido = ($id && $cliente) ? $this->pedidoModel->buscarPorId($id) : false;

        if (!$pedido || (int) $pedido['cliente_id'] !== (int) $cliente['id']) {
            http_response_code(404);
            echo json_encode(['erro' => 'Pedido nao encontrado.']);
            exit;
        }

        echo json_encode([
            'estado' => $pedido['estado'],
            'total' => (float) $pedido['total'],
        ]);
        exit;
    }
}
