<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/MesaModel.php';

class MesaController extends Controller
{
    private MesaModel $mesaModel;

    public function __construct()
    {
        Sessao::exigirPerfil('Administrador', 'Operador');
        $this->mesaModel = new MesaModel();
    }

    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/admin/mesas.php');
        }

        $id = Validador::inteiro($_POST['id'] ?? '') ?: null;
        $numero = Validador::inteiro($_POST['numero'] ?? '');
        $capacidade = Validador::inteiro($_POST['capacidade'] ?? '');
        $localizacao = Validador::texto($_POST['localizacao'] ?? '');
        $estado = $_POST['estado'] ?? 'Livre';

        if (!$numero || !$capacidade) {
            Sessao::flash('erro', 'Preenche o numero e a capacidade da mesa.');
            $this->redirecionar('/views/admin/mesas.php');
        }

        if ($this->mesaModel->numeroEmUso($numero, $id)) {
            Sessao::flash('erro', 'Ja existe uma mesa com esse numero.');
            $this->redirecionar('/views/admin/mesas.php');
        }

        $dados = [
            'numero' => $numero,
            'capacidade' => $capacidade,
            'localizacao' => $localizacao,
            'estado' => $estado,
        ];

        if ($id) {
            $this->mesaModel->atualizar($id, $dados);
            Sessao::flash('sucesso', 'Mesa atualizada.');
        } else {
            $this->mesaModel->inserir($dados);
            Sessao::flash('sucesso', 'Mesa criada.');
        }

        $this->redirecionar('/views/admin/mesas.php');
    }

    public function eliminar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/admin/mesas.php');
        }

        $id = Validador::inteiro($_POST['id'] ?? '');

        try {
            $this->mesaModel->eliminar($id);
            Sessao::flash('sucesso', 'Mesa eliminada.');
        } catch (PDOException) {
            Sessao::flash('erro', 'Esta mesa tem pedidos ou reservas associados, nao pode ser eliminada.');
        }

        $this->redirecionar('/views/admin/mesas.php');
    }
}
