<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

class UtilizadorController extends Controller
{
    private UsuarioModel $usuarioModel;

    public function __construct()
    {
        Sessao::exigirPerfil('Administrador', 'Operador');
        $this->usuarioModel = new UsuarioModel();
    }

    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/admin/utilizadores.php');
        }

        $id = Validador::inteiro($_POST['id'] ?? '') ?: null;
        $nome = Validador::texto($_POST['nome'] ?? '');
        $email = Validador::email($_POST['email'] ?? '');
        $perfilNome = Validador::texto($_POST['perfil'] ?? '');
        $estado = ($_POST['estado'] ?? '') === 'Inativo' ? 'Inativo' : 'Ativo';
        $senha = $_POST['senha'] ?? '';

        if (!Validador::obrigatorio($nome) || !$email || !Validador::obrigatorio($perfilNome)) {
            Sessao::flash('erro', 'Preenche nome, email e perfil.');
            $this->redirecionar('/views/admin/utilizadores.php');
        }

        $perfilId = $this->usuarioModel->idDoPerfil($perfilNome);

        if (!$perfilId) {
            Sessao::flash('erro', 'Perfil invalido.');
            $this->redirecionar('/views/admin/utilizadores.php');
        }

        $existente = $this->usuarioModel->buscarPorEmailComPerfil($email);

        if ($existente && (int) $existente['id'] !== $id) {
            Sessao::flash('erro', 'Ja existe outro utilizador com este email.');
            $this->redirecionar('/views/admin/utilizadores.php');
        }

        if (!$id && strlen($senha) < 6) {
            Sessao::flash('erro', 'Define uma senha com pelo menos 6 caracteres.');
            $this->redirecionar('/views/admin/utilizadores.php');
        }

        if ($id && $senha !== '' && strlen($senha) < 6) {
            Sessao::flash('erro', 'A senha precisa de pelo menos 6 caracteres.');
            $this->redirecionar('/views/admin/utilizadores.php');
        }

        $dados = [
            'nome' => $nome,
            'email' => $email,
            'perfil_id' => $perfilId,
            'estado' => $estado,
        ];

        if ($senha !== '') {
            $dados['senha'] = password_hash($senha, PASSWORD_DEFAULT);
        }

        if ($id) {
            $this->usuarioModel->atualizar($id, $dados);
            Sessao::flash('sucesso', 'Utilizador atualizado.');
        } else {
            $this->usuarioModel->inserir($dados);
            Sessao::flash('sucesso', 'Utilizador criado.');
        }

        $this->redirecionar('/views/admin/utilizadores.php');
    }

    public function eliminar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/admin/utilizadores.php');
        }

        $id = Validador::inteiro($_POST['id'] ?? '');

        if ($id === Sessao::utilizadorAtual()['id']) {
            Sessao::flash('erro', 'Nao podes eliminar a tua propria conta.');
            $this->redirecionar('/views/admin/utilizadores.php');
        }

        $this->usuarioModel->eliminar($id);
        Sessao::flash('sucesso', 'Utilizador eliminado.');
        $this->redirecionar('/views/admin/utilizadores.php');
    }
}
