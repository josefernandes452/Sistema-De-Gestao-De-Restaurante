<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/PedidoModel.php';
require_once __DIR__ . '/../models/ProdutoModel.php';
require_once __DIR__ . '/../models/MesaModel.php';

class PedidoController extends Controller
{
    private PedidoModel $pedidoModel;
    private ProdutoModel $produtoModel;
    private MesaModel $mesaModel;

    private const ESTADOS_VALIDOS = ['Pendente', 'Em Preparacao', 'Pronto', 'Entregue', 'Cancelado'];

    public function __construct()
    {
        Sessao::exigirPerfil('Administrador', 'Operador');
        $this->pedidoModel = new PedidoModel();
        $this->produtoModel = new ProdutoModel();
        $this->mesaModel = new MesaModel();
    }

    public function criar(): void
    {
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

        $this->pedidoModel->criarComItens($dadosPedido, $itens);
        $this->mesaModel->atualizar($mesaId, ['estado' => 'Ocupada']);

        Sessao::flash('sucesso', 'Pedido criado.');
        $this->redirecionar('/views/admin/pedidos.php');
    }

    public function atualizarEstado(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/admin/pedidos.php');
        }

        $id = Validador::inteiro($_POST['id'] ?? '');
        $estado = $_POST['estado'] ?? '';

        if (!$id || !in_array($estado, self::ESTADOS_VALIDOS, true)) {
            Sessao::flash('erro', 'Estado invalido.');
            $this->redirecionar('/views/admin/pedidos.php');
        }

        $this->pedidoModel->atualizar($id, ['estado' => $estado]);
        Sessao::flash('sucesso', 'Estado do pedido atualizado.');
        $this->redirecionar('/views/admin/pedidos.php');
    }

    public function eliminar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/admin/pedidos.php');
        }

        $id = Validador::inteiro($_POST['id'] ?? '');
        $this->pedidoModel->eliminar($id);
        Sessao::flash('sucesso', 'Pedido eliminado.');
        $this->redirecionar('/views/admin/pedidos.php');
    }
}
