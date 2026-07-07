<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

class AuthController extends Controller
{
    private UsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionar('/views/cliente/login.php');
        }

        if (!Csrf::validar($_POST['csrf_token'] ?? null)) {
            Sessao::flash('erro', 'A sessao expirou, tenta entrar de novo.');
            $this->redirecionar('/views/cliente/login.php');
        }

        $email = Validador::email($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        $utilizador = $email ? $this->usuarioModel->buscarPorEmailComPerfil($email) : false;

        if (!$utilizador || !password_verify($senha, $utilizador['senha'])) {
            Sessao::flash('erro', 'Email ou senha invalidos.');
            $this->redirecionar('/views/cliente/login.php');
        }

        if ($utilizador['estado'] !== 'Ativo') {
            Sessao::flash('erro', 'Esta conta esta inativa. Fala com o administrador.');
            $this->redirecionar('/views/cliente/login.php');
        }

        Sessao::logar($utilizador);

        if (in_array($utilizador['perfil_nome'], ['Administrador', 'Operador'], true)) {
            $this->redirecionar('/views/admin/dashboard.php');
        }

        $this->redirecionar('/views/cliente/perfil-cliente.php');
    }

    public function registar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/cliente/registo.php');
        }

        $nome = Validador::texto($_POST['nome'] ?? '');
        $email = Validador::email($_POST['email'] ?? '');
        $telefone = Validador::texto($_POST['telefone'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if (!Validador::obrigatorio($nome) || !$email || !Validador::obrigatorio($telefone) || strlen($senha) < 6) {
            Sessao::flash('erro', 'Preenche todos os campos (a senha precisa de pelo menos 6 caracteres).');
            $this->redirecionar('/views/cliente/registo.php');
        }

        if ($this->usuarioModel->emailExiste($email)) {
            Sessao::flash('erro', 'Ja existe uma conta com este email.');
            $this->redirecionar('/views/cliente/registo.php');
        }

        $this->usuarioModel->inserir([
            'perfil_id' => $this->usuarioModel->idDoPerfilCliente(),
            'nome' => $nome,
            'email' => $email,
            'senha' => password_hash($senha, PASSWORD_DEFAULT),
            'telefone' => $telefone,
            'estado' => 'Ativo',
        ]);

        Sessao::flash('sucesso', 'Conta criada com sucesso. Agora e so entrar.');
        $this->redirecionar('/views/cliente/login.php');
    }

    public function logout(): void
    {
        Sessao::sair();
        $this->redirecionar('/views/cliente/login.php');
    }
}
