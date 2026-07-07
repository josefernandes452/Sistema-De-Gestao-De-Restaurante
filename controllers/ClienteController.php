<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/ClienteModel.php';

class ClienteController extends Controller
{
    private ClienteModel $clienteModel;

    public function __construct()
    {
        Sessao::exigirPerfil('Administrador', 'Operador');
        $this->clienteModel = new ClienteModel();
    }

    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/admin/clientes.php');
        }

        $id = Validador::inteiro($_POST['id'] ?? '') ?: null;
        $nome = Validador::texto($_POST['nome'] ?? '');
        $telefone = Validador::texto($_POST['telefone'] ?? '');
        $email = Validador::texto($_POST['email'] ?? '');
        $nif = Validador::texto($_POST['nif'] ?? '');
        $endereco = Validador::texto($_POST['endereco'] ?? '');

        if (!Validador::obrigatorio($nome) || !Validador::obrigatorio($telefone)) {
            Sessao::flash('erro', 'Preenche pelo menos o nome e o telefone.');
            $this->redirecionar('/views/admin/clientes.php');
        }

        $dados = [
            'nome' => $nome,
            'telefone' => $telefone,
            'email' => $email ?: null,
            'nif' => $nif ?: null,
            'endereco' => $endereco ?: null,
        ];

        if ($id) {
            $this->clienteModel->atualizar($id, $dados);
            Sessao::flash('sucesso', 'Cliente atualizado.');
        } else {
            $this->clienteModel->inserir($dados);
            Sessao::flash('sucesso', 'Cliente criado.');
        }

        $this->redirecionar('/views/admin/clientes.php');
    }

    public function eliminar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/admin/clientes.php');
        }

        $id = Validador::inteiro($_POST['id'] ?? '');
        $this->clienteModel->eliminar($id);
        Sessao::flash('sucesso', 'Cliente eliminado.');
        $this->redirecionar('/views/admin/clientes.php');
    }
}
