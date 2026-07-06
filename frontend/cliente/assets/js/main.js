// ============================================
// BANCO DE USUARIOS (SIMULADO)
// ============================================
var usuarios = [
    {
        id: 1,
        email: 'cliente@email.com',
        senha: '123456',
        tipo: 'cliente',
        nome: 'Joao Silva',
        telefone: '923456789'
    },
    {
        id: 2,
        email: 'admin@saboralma.ao',
        senha: 'admin123',
        tipo: 'admin',
        nome: 'Administrador',
        telefone: '900000000'
    }
];

// ============================================
// FUNCAO PRINCIPAL DE LOGIN
// ============================================
function fazerLogin(event) {
    event.preventDefault();
    
    var email = document.getElementById('emailLogin').value.trim();
    var senha = document.getElementById('senhaLogin').value.trim();
    var mensagemErro = document.getElementById('mensagemErro');
    var textoErro = document.getElementById('textoErro');
    
    // Esconder erro anterior
    mensagemErro.classList.add('d-none');
    
    // Validar campos
    if (!email || !senha) {
        textoErro.textContent = 'Por favor, preencha todos os campos!';
        mensagemErro.classList.remove('d-none');
        return false;
    }
    
    // Procurar usuario
    var user = null;
    for (var i = 0; i < usuarios.length; i++) {
        if (usuarios[i].email === email && usuarios[i].senha === senha) {
            user = usuarios[i];
            break;
        }
    }
    
    if (!user) {
        textoErro.textContent = 'Email ou senha invalidos!';
        mensagemErro.classList.remove('d-none');
        return false;
    }
    
    // Gerar certificado
    var certificado = gerarCertificado(user);
    
    // Salvar na sessao
    localStorage.setItem('user', JSON.stringify(user));
    localStorage.setItem('certificado', JSON.stringify(certificado));
    
    // Mostrar certificado
    mostrarCertificado(user, certificado);
    
    return false;
}

// ============================================
// GERAR CERTIFICADO
// ============================================
function gerarCertificado(user) {
    var agora = new Date();
    var dataHora = agora.toLocaleString('pt-PT', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    
    // Gerar hash simples
    var hash = user.id + '-' + user.email + '-' + agora.getTime();
    hash = btoa(hash).substring(0, 20);
    
    return {
        usuario: user.nome,
        email: user.email,
        tipo: user.tipo === 'admin' ? 'Administrador' : 'Cliente',
        dataHora: dataHora,
        hash: hash
    };
}

// ============================================
// MOSTRAR CERTIFICADO
// ============================================
function mostrarCertificado(user, certificado) {
    document.getElementById('certUsuario').textContent = certificado.usuario;
    document.getElementById('certEmail').textContent = certificado.email;
    document.getElementById('certTipo').textContent = certificado.tipo;
    document.getElementById('certData').textContent = certificado.dataHora;
    document.getElementById('certHash').textContent = certificado.hash;
    
    var modal = new bootstrap.Modal(document.getElementById('modalCertificado'));
    modal.show();
    
    // Botao continuar
    document.getElementById('continuarLogin').onclick = function() {
        modal.hide();
        redirecionarUsuario(user);
    };
    
    // Fechar com X
    document.getElementById('fecharCertificado').onclick = function() {
        modal.hide();
        redirecionarUsuario(user);
    };
}

// ============================================
// REDIRECIONAR USUARIO
// ============================================
function redirecionarUsuario(user) {
    if (user.tipo === 'admin') {
        window.location.href = '../admin/dashboard.html';
    } else {
        window.location.href = 'perfil-cliente.html';
    }
}

// ============================================
// MOSTRAR/ESCONDER SENHA
// ============================================
function mostrarSenha() {
    var senha = document.getElementById('senhaLogin');
    var icone = document.getElementById('iconeOlho');
    
    if (senha.type === 'password') {
        senha.type = 'text';
        icone.classList.remove('fa-eye');
        icone.classList.add('fa-eye-slash');
    } else {
        senha.type = 'password';
        icone.classList.remove('fa-eye-slash');
        icone.classList.add('fa-eye');
    }
}

// ============================================
// RECUPERAR SENHA
// ============================================
function recuperarSenha() {
    var email = document.getElementById('emailRecuperar').value.trim();
    
    if (!email) {
        alert('Por favor, digite seu email!');
        return;
    }
    
    // Verificar se existe
    var encontrado = false;
    for (var i = 0; i < usuarios.length; i++) {
        if (usuarios[i].email === email) {
            encontrado = true;
            break;
        }
    }
    
    if (!encontrado) {
        alert('Email nao encontrado!');
        return;
    }
    
    alert('Instrucoes de recuperacao enviadas para: ' + email);
    
    var modal = bootstrap.Modal.getInstance(document.getElementById('modalRecuperar'));
    modal.hide();
    document.getElementById('emailRecuperar').value = '';
}

// ============================================
// VERIFICAR SE ESTA LOGADO
// ============================================
function verificarLogin() {
    var user = JSON.parse(localStorage.getItem('user'));
    
    if (!user) {
        var paginasProtegidas = ['perfil-cliente.html', 'pedidos.html', 'historico.html', 'acompanhamento.html'];
        var paginaAtual = window.location.pathname.split('/').pop();
        
        for (var i = 0; i < paginasProtegidas.length; i++) {
            if (paginaAtual === paginasProtegidas[i]) {
                window.location.href = 'login.html';
                return null;
            }
        }
    }
    
    return user;
}

// ============================================
// CARREGAR PERFIL DO CLIENTE
// ============================================
function carregarPerfil() {
    var user = verificarLogin();
    if (!user) return;
    
    var nomeEl = document.getElementById('perfilNome');
    var emailEl = document.getElementById('perfilEmail');
    var avatarEl = document.getElementById('perfilAvatar');
    var telefoneEl = document.getElementById('perfilTelefoneInput');
    var nomeInput = document.getElementById('perfilNomeInput');
    var emailInput = document.getElementById('perfilEmailInput');
    
    if (nomeEl) nomeEl.textContent = user.nome;
    if (emailEl) emailEl.textContent = user.email;
    if (nomeInput) nomeInput.value = user.nome;
    if (emailInput) emailInput.value = user.email;
    if (telefoneEl) telefoneEl.value = user.telefone || 'Nao definido';
    if (avatarEl) avatarEl.textContent = user.nome.charAt(0).toUpperCase();
    
    // Mostrar certificado
    var certificado = JSON.parse(localStorage.getItem('certificado'));
    var certInfo = document.getElementById('certificadoInfo');
    if (certificado && certInfo) {
        certInfo.innerHTML = 
            '<div class="alert alert-success small">' +
                '<i class="fas fa-certificate me-2" style="color: #c9a84c;"></i>' +
                '<strong>Certificado CA:</strong> ' + certificado.hash +
                '<br><small class="text-muted">Emitido em: ' + certificado.dataHora + '</small>' +
            '</div>';
    }
}

// ============================================
// SAIR
// ============================================
function logout() {
    if (confirm('Deseja realmente sair?')) {
        localStorage.removeItem('user');
        localStorage.removeItem('certificado');
        window.location.href = 'login.html';
    }
}

// ============================================
// REGISTO DE CLIENTE
// ============================================
function registarCliente(event) {
    event.preventDefault();
    
    var nome = document.getElementById('nomeRegisto').value.trim();
    var email = document.getElementById('emailRegisto').value.trim();
    var telefone = document.getElementById('telefoneRegisto').value.trim();
    var senha = document.getElementById('senhaRegisto').value;
    
    if (!nome || !email || !telefone || senha.length < 6) {
        alert('Preencha todos os campos! (senha minimo 6 caracteres)');
        return false;
    }
    
    // Verificar se email ja existe
    for (var i = 0; i < usuarios.length; i++) {
        if (usuarios[i].email === email) {
            alert('Este email ja esta registado!');
            return false;
        }
    }
    
    // Adicionar novo cliente
    usuarios.push({
        id: usuarios.length + 1,
        email: email,
        senha: senha,
        tipo: 'cliente',
        nome: nome,
        telefone: telefone
    });
    
    alert('Conta criada com sucesso! Faca login para continuar.');
    window.location.href = 'login.html';
    return false;
}

// ============================================
// DADOS DO MENU
// ============================================
var menuData = [
    { id: 1, nome: 'Bife a Casa', categoria: 'principais', preco: 2500, descricao: 'Bife com batata frita e ovo' },
    { id: 2, nome: 'Frango Grelhado', categoria: 'principais', preco: 1800, descricao: 'Frango com legumes grelhados' },
    { id: 3, nome: 'Salada Mista', categoria: 'entradas', preco: 800, descricao: 'Salada com alface, tomate e cebola' },
    { id: 4, nome: 'Sopa do Dia', categoria: 'entradas', preco: 600, descricao: 'Sopa caseira' },
    { id: 5, nome: 'Refrigerante', categoria: 'bebidas', preco: 300, descricao: 'Lata 330ml' },
    { id: 6, nome: 'Suco Natural', categoria: 'bebidas', preco: 450, descricao: 'Suco de fruta natural' }
];

// ============================================
// CARRINHO
// ============================================
var carrinho = [];

function adicionarAoCarrinho(id) {
    var produto = null;
    for (var i = 0; i < menuData.length; i++) {
        if (menuData[i].id === id) {
            produto = menuData[i];
            break;
        }
    }
    if (!produto) return;
    
    var existente = null;
    for (var i = 0; i < carrinho.length; i++) {
        if (carrinho[i].id === id) {
            existente = carrinho[i];
            break;
        }
    }
    
    if (existente) {
        existente.quantidade++;
    } else {
        carrinho.push({ 
            id: produto.id, 
            nome: produto.nome, 
            preco: produto.preco, 
            quantidade: 1 
        });
    }
    
    atualizarCarrinho();
}

function removerDoCarrinho(id) {
    var novoCarrinho = [];
    for (var i = 0; i < carrinho.length; i++) {
        if (carrinho[i].id !== id) {
            novoCarrinho.push(carrinho[i]);
        }
    }
    carrinho = novoCarrinho;
    atualizarCarrinho();
}

function atualizarCarrinho() {
    var container = document.getElementById('carrinhoItems');
    var totalEl = document.getElementById('totalPedido');
    
    if (!container) return;
    
    if (carrinho.length === 0) {
        container.innerHTML = '<p class="text-center text-muted">Seu carrinho esta vazio</p>';
        if (totalEl) totalEl.textContent = 'Kz 0,00';
        return;
    }
    
    var html = '';
    var total = 0;
    
    for (var i = 0; i < carrinho.length; i++) {
        var item = carrinho[i];
        var subtotal = item.preco * item.quantidade;
        total += subtotal;
        html += '<div class="d-flex justify-content-between border-bottom py-2">';
        html += '<div><span class="fw-semibold">' + item.nome + '</span>';
        html += '<small class="d-block text-muted">' + item.quantidade + ' x Kz ' + item.preco.toFixed(2) + '</small></div>';
        html += '<div><span class="fw-bold" style="color: #c9a84c;">Kz ' + subtotal.toFixed(2) + '</span>';
        html += '<button class="btn btn-sm btn-outline-danger ms-2" onclick="removerDoCarrinho(' + item.id + ')"><i class="fas fa-times"></i></button></div>';
        html += '</div>';
    }
    
    container.innerHTML = html;
    if (totalEl) totalEl.textContent = 'Kz ' + total.toFixed(2);
}

function finalizarPedido() {
    if (carrinho.length === 0) {
        alert('Seu carrinho esta vazio!');
        return;
    }
    
    var total = 0;
    for (var i = 0; i < carrinho.length; i++) {
        total += carrinho[i].preco * carrinho[i].quantidade;
    }
    
    if (confirm('Confirmar pedido no valor total de Kz ' + total.toFixed(2) + '?')) {
        alert('Pedido realizado com sucesso!');
        carrinho = [];
        atualizarCarrinho();
        window.location.href = 'acompanhamento.html';
    }
}

// ============================================
// RENDERIZAR MENU
// ============================================
function renderizarMenu(containerId, filtro) {
    var container = document.getElementById(containerId);
    if (!container) return;
    
    if (filtro === undefined) filtro = 'todos';
    
    var filtered = [];
    if (filtro === 'todos') {
        filtered = menuData;
    } else {
        for (var i = 0; i < menuData.length; i++) {
            if (menuData[i].categoria === filtro) {
                filtered.push(menuData[i]);
            }
        }
    }
    
    container.innerHTML = '';
    
    for (var i = 0; i < filtered.length; i++) {
        var produto = filtered[i];
        var col = document.createElement('div');
        col.className = 'col-md-4 col-lg-3';
        col.innerHTML = 
            '<div class="card h-100 border-0 shadow-sm">' +
                '<div class="card-body text-center">' +
                    '<div style="width: 70px; height: 70px; background: #f5f0e8; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 28px; color: #c9a84c;">' +
                        '<i class="fas fa-utensils"></i>' +
                    '</div>' +
                    '<h6 class="fw-bold mt-3">' + produto.nome + '</h6>' +
                    '<p class="text-muted small">' + produto.descricao + '</p>' +
                    '<p class="fw-bold" style="color: #c9a84c;">Kz ' + produto.preco.toFixed(2) + '</p>' +
                    '<button class="btn btn-sm w-100" style="background: #c9a84c; color: #1a3c2a;" onclick="adicionarAoCarrinho(' + produto.id + ')">' +
                        '<i class="fas fa-plus me-1"></i> Adicionar' +
                    '</button>' +
                '</div>' +
            '</div>';
        container.appendChild(col);
    }
}

// ============================================
// RESERVAS
// ============================================
function fazerReserva(event) {
    event.preventDefault();
    
    var nome = document.getElementById('nomeReserva').value.trim();
    var telefone = document.getElementById('telefoneReserva').value.trim();
    var data = document.getElementById('dataReserva').value;
    var hora = document.getElementById('horaReserva').value;
    var pessoas = document.getElementById('pessoasReserva').value;
    
    if (!nome || !telefone || !data || !hora || !pessoas) {
        alert('Preencha todos os campos obrigatorios!');
        return false;
    }
    
    alert('Reserva confirmada!\nNome: ' + nome + '\nData: ' + data + ' ' + hora + '\nPessoas: ' + pessoas);
    return false;
}

// ============================================
// ACOMPANHAMENTO
// ============================================
var statusPedido = 0;
var statusLista = ['Recebido', 'Preparando', 'Pronto', 'Entregue'];

function atualizarStatus() {
    var steps = document.querySelectorAll('.step-status');
    var progressBar = document.querySelector('.progress-bar');
    
    if (!steps.length) return;
    
    for (var i = 0; i < steps.length; i++) {
        var icon = steps[i].querySelector('i');
        if (i <= statusPedido) {
            steps[i].classList.remove('text-muted');
            steps[i].classList.add('text-success');
            if (icon) icon.className = 'fas fa-check-circle fs-3';
        } else {
            steps[i].classList.remove('text-success');
            steps[i].classList.add('text-muted');
            if (icon) icon.className = 'fas fa-circle fs-3';
        }
    }
    
    if (progressBar) {
        var progress = ((statusPedido + 1) / statusLista.length) * 100;
        progressBar.style.width = progress + '%';
    }
}

function iniciarAcompanhamento() {
    statusPedido = 0;
    atualizarStatus();
    
    if (window.intervalStatus) clearInterval(window.intervalStatus);
    window.intervalStatus = setInterval(function() {
        if (statusPedido < statusLista.length - 1) {
            statusPedido++;
            atualizarStatus();
        } else {
            clearInterval(window.intervalStatus);
        }
    }, 5000);
}

// ============================================
// INICIALIZACAO
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    var path = window.location.pathname;
    var page = path.split('/').pop();
    
    // Verificar login
    verificarLogin();
    
    if (page === 'menu.html') {
        renderizarMenu('listaMenu', 'todos');
    }
    
    if (page === 'pedidos.html') {
        renderizarMenu('listaPedidos', 'todos');
        atualizarCarrinho();
    }
    
    if (page === 'acompanhamento.html') {
        iniciarAcompanhamento();
    }
    
    if (page === 'perfil-cliente.html') {
        carregarPerfil();
    }
    
    if (page === 'reservas.html') {
        var dataInput = document.getElementById('dataReserva');
        if (dataInput) {
            var hoje = new Date().toISOString().split('T')[0];
            dataInput.min = hoje;
            dataInput.value = hoje;
        }
    }
});