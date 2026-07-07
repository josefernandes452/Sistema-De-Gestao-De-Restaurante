<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/CategoriaModel.php';

class CategoriaController extends Controller
{
    private CategoriaModel $categoriaModel;

    public function __construct()
    {
        Sessao::exigirPerfil('Administrador', 'Operador');
        $this->categoriaModel = new CategoriaModel();
    }

    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/admin/categorias.php');
        }

        $id = Validador::inteiro($_POST['id'] ?? '') ?: null;
        $nome = Validador::texto($_POST['nome'] ?? '');
        $descricao = Validador::texto($_POST['descricao'] ?? '');
        $estado = ($_POST['estado'] ?? '') === 'Inativo' ? 'Inativo' : 'Ativo';

        if (!Validador::obrigatorio($nome)) {
            Sessao::flash('erro', 'A categoria precisa de um nome.');
            $this->redirecionar('/views/admin/categorias.php');
        }

        $dados = ['nome' => $nome, 'descricao' => $descricao, 'estado' => $estado];

        if ($id) {
            $this->categoriaModel->atualizar($id, $dados);
            Sessao::flash('sucesso', 'Categoria atualizada.');
        } else {
            $this->categoriaModel->inserir($dados);
            Sessao::flash('sucesso', 'Categoria criada.');
        }

        $this->redirecionar('/views/admin/categorias.php');
    }

    public function eliminar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/admin/categorias.php');
        }

        $id = Validador::inteiro($_POST['id'] ?? '');

        if ($this->categoriaModel->estaEmUso($id)) {
            Sessao::flash('erro', 'Esta categoria tem produtos associados, nao pode ser eliminada.');
            $this->redirecionar('/views/admin/categorias.php');
        }

        $this->categoriaModel->eliminar($id);
        Sessao::flash('sucesso', 'Categoria eliminada.');
        $this->redirecionar('/views/admin/categorias.php');
    }
}
