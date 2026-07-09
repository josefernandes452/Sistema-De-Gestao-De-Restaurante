<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../models/LogModel.php';
require_once __DIR__ . '/../models/ClienteModel.php';

class AuthController extends Controller
{
    private UsuarioModel $usuarioModel;
    private LogModel $logModel;
    private ClienteModel $clienteModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->logModel = new LogModel();
        $this->clienteModel = new ClienteModel();
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionar('/views/cliente/login.php');
        }

        if (!Csrf::validar($_POST['csrf_token'] ?? null)) {
            Sessao::flash('erro', 'A sessão expirou, tenta entrar de novo.');
            $this->redirecionar('/views/cliente/login.php');
        }

        $email = Validador::email($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        $utilizador = $email ? $this->usuarioModel->buscarPorEmailComPerfil($email) : false;

        if (!$utilizador || !password_verify($senha, $utilizador['senha'])) {
            $this->logModel->registar(
                is_array($utilizador) ? $utilizador['id'] : null,
                'Tentativa de login falhada',
                "Email usado: $email"
            );
            Sessao::flash('erro', 'Email ou senha inválidos.');
            $this->redirecionar('/views/cliente/login.php');
        }

        if ($utilizador['estado'] !== 'Ativo') {
            Sessao::flash('erro', 'Esta conta esta inativa. Fala com o administrador.');
            $this->redirecionar('/views/cliente/login.php');
        }

        Sessao::logar($utilizador);
        $this->logModel->registar($utilizador['id'], 'Login', null);

        if (!empty($_POST['lembrar'])) {
            LembrarMe::criar((int) $utilizador['id']);
        }

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
            Sessao::flash('erro', 'Já existe uma conta com este email.');
            $this->redirecionar('/views/cliente/registo.php');
        }

        $utilizadorId = $this->usuarioModel->inserir([
            'perfil_id' => $this->usuarioModel->idDoPerfilCliente(),
            'nome' => $nome,
            'email' => $email,
            'senha' => password_hash($senha, PASSWORD_DEFAULT),
            'telefone' => $telefone,
            'estado' => 'Ativo',
        ]);

        // Toda conta de Cliente precisa de uma linha correspondente em
        // "clientes", que e a tabela usada nos pedidos. Sem isto o
        // cliente conseguia entrar no site mas nunca teria como fazer
        // um pedido de verdade.
        $this->clienteModel->criarOuLigarAUtilizador($utilizadorId, $nome, $email, $telefone);

        Sessao::flash('sucesso', 'Conta criada com sucesso. Agora é só entrar.');
        $this->redirecionar('/views/cliente/login.php');
    }

    public function recuperarSenha(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/cliente/login.php');
        }

        $email = Validador::email($_POST['email'] ?? '');
        $utilizador = $email ? $this->usuarioModel->buscarPorEmailComPerfil($email) : false;

        if (!$utilizador) {
            Sessao::flash('erro', 'Não encontramos nenhuma conta com este email.');
            $this->redirecionar('/views/cliente/login.php');
        }

        $token = bin2hex(random_bytes(16));
        $this->usuarioModel->definirTokenRecuperacao($utilizador['id'], $token);

        // O link tem de usar o mesmo esquema do pedido atual. Antes estava
        // fixo em "http://", e por isso quem pedia a recuperacao pelo
        // saboralma.local (HTTPS, porta 443) recebia um link para a porta
        // 80, que nao tem nenhum vhost apontado para o projeto e dava 404.
        $esquema = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $link = $esquema . '://' . $_SERVER['HTTP_HOST'] . '/views/cliente/redefinir-senha.php?token=' . $token;
        $corpo = '<p>Ola ' . htmlspecialchars($utilizador['nome']) . ',</p>'
            . '<p>Pediste para redefinir a senha da tua conta no restaurante Sabor Alma. Clica no link abaixo para escolheres uma nova senha:</p>'
            . '<p><a href="' . $link . '">' . $link . '</a></p>'
            . '<p>Este link e válido por 1 hora.</p>';

        $enviado = Mailer::enviar($utilizador['email'], $utilizador['nome'], 'Recuperação de senha - Sabor Alma', $corpo);

        if ($enviado) {
            Sessao::flash('sucesso', 'Enviamos um email para ' . htmlspecialchars($email) . ' com as instruções.');
        } else {
            Sessao::flash('erro', 'Não foi possivel enviar o email agora. Tenta novamente daqui a pouco.');
        }

        $this->redirecionar('/views/cliente/login.php');
    }

    public function redefinirSenha(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->redirecionar('/views/cliente/login.php');
        }

        $token = $_POST['token'] ?? '';
        $senha = $_POST['senha'] ?? '';
        $confirmar = $_POST['confirmar_senha'] ?? '';

        $utilizador = $token ? $this->usuarioModel->buscarPorToken($token) : false;

        if (!$utilizador) {
            Sessao::flash('erro', 'Este link já não é válido. Pede uma nova recuperação.');
            $this->redirecionar('/views/cliente/login.php');
        }

        if (strlen($senha) < 6 || $senha !== $confirmar) {
            Sessao::flash('erro', 'As senhas tem de ser iguais e terem pelo menos 6 caracteres.');
            $this->redirecionar('/views/cliente/redefinir-senha.php?token=' . urlencode($token));
        }

        $this->usuarioModel->atualizarSenha($utilizador['id'], $senha);

        Sessao::flash('sucesso', 'Senha alterada com sucesso. Agora é só entrar.');
        $this->redirecionar('/views/cliente/login.php');
    }

    // Diferente da recuperarSenha/redefinirSenha (para quem esqueceu a
    // senha e nao tem sessao ativa), este e o "trocar a minha senha"
    // normal, usado por quem ja esta logado e sabe a senha atual.
    // Serve para os 3 perfis: Administrador, Operador e Cliente.
    public function alterarSenha(): void
    {
        $voltarPara = $_POST['voltar_para'] ?? '/views/cliente/login.php';

        if (!Sessao::estaLogado()) {
            $this->redirecionar('/views/cliente/login.php');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validar($_POST['csrf_token'] ?? null)) {
            Sessao::flash('erro', 'A sessão expirou, tenta outra vez.');
            $this->redirecionar($voltarPara);
        }

        $utilizadorId = Sessao::utilizadorAtual()['id'];
        $utilizador = $this->usuarioModel->buscarPorId($utilizadorId);

        $senhaAtual = $_POST['senha_atual'] ?? '';
        $novaSenha = $_POST['nova_senha'] ?? '';
        $confirmar = $_POST['confirmar_nova_senha'] ?? '';

        if (!$utilizador || !password_verify($senhaAtual, $utilizador['senha'])) {
            Sessao::flash('erro', 'A senha atual está incorreta.');
            $this->redirecionar($voltarPara);
        }

        if (strlen($novaSenha) < 6 || $novaSenha !== $confirmar) {
            Sessao::flash('erro', 'A nova senha e a confirmação têm de ser iguais e ter pelo menos 6 caracteres.');
            $this->redirecionar($voltarPara);
        }

        $this->usuarioModel->atualizarSenha($utilizadorId, $novaSenha);
        $this->logModel->registar($utilizadorId, 'Alterou a propria senha', null);

        Sessao::flash('sucesso', 'Senha alterada com sucesso.');
        $this->redirecionar($voltarPara);
    }

    public function logout(): void
    {
        $utilizador = Sessao::utilizadorAtual();
        LembrarMe::esquecer($utilizador['id'] ?? null);
        Sessao::sair();
        $this->redirecionar('/views/cliente/login.php');
    }
}
