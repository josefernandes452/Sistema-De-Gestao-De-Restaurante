<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/ProdutoModel.php';

class ProdutoController extends Controller
{
    private ProdutoModel $produtoModel;

    public function __construct()
    {
        Sessao::exigirPerfil('Administrador', 'Operador');
        $this->produtoModel = new ProdutoModel();
    }

    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/admin/produtos.php');
        }

        $id = Validador::inteiro($_POST['id'] ?? '') ?: null;
        $nome = Validador::texto($_POST['nome'] ?? '');
        $categoriaId = Validador::inteiro($_POST['categoria_id'] ?? '');
        $preco = Validador::decimal($_POST['preco'] ?? '');
        $estoque = Validador::inteiro($_POST['estoque'] ?? '');
        $estado = $_POST['estado'] ?? 'Disponivel';
        $descricao = Validador::texto($_POST['descricao'] ?? '');

        if (!Validador::obrigatorio($nome) || !$categoriaId || $preco === false || $estoque === false) {
            Sessao::flash('erro', 'Preenche nome, categoria, preco e stock correctamente.');
            $this->redirecionar('/views/admin/produtos.php');
        }

        $dados = [
            'nome' => $nome,
            'categoria_id' => $categoriaId,
            'preco' => $preco,
            'estoque' => $estoque,
            'estado' => $estado,
            'descricao' => $descricao,
        ];

        if (!empty($_FILES['imagem']['name'])) {
            $nomeImagem = Upload::imagem($_FILES['imagem']);

            if ($nomeImagem === false) {
                Sessao::flash('erro', 'A imagem tem de ser JPG, PNG ou WEBP e ate 2MB.');
                $this->redirecionar('/views/admin/produtos.php');
            }

            $dados['imagem'] = $nomeImagem;
        }

        if ($id) {
            $this->produtoModel->atualizar($id, $dados);
            Sessao::flash('sucesso', 'Produto atualizado.');
        } else {
            $this->produtoModel->inserir($dados);
            Sessao::flash('sucesso', 'Produto criado.');
        }

        $this->redirecionar('/views/admin/produtos.php');
    }

    public function eliminar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/admin/produtos.php');
        }

        $id = Validador::inteiro($_POST['id'] ?? '');
        $this->produtoModel->eliminar($id);
        Sessao::flash('sucesso', 'Produto eliminado.');
        $this->redirecionar('/views/admin/produtos.php');
    }
}
