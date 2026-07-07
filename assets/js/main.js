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
// UTILIZADOR LOGADO
// window.usuarioLogado e preenchido pelo PHP nas paginas protegidas
// (ver views/cliente/perfil-cliente.php). O acesso em si ja e
// controlado no servidor, isto aqui e so para mostrar os dados na tela.
// ============================================
function verificarLogin() {
    return window.usuarioLogado || null;
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
}

// ============================================
// SAIR
// ============================================
function logout() {
    if (confirm('Deseja realmente sair?')) {
        window.location.href = '/index.php?rota=logout';
    }
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
        html += '<div><span class="fw-semibold">' + escaparHtml(item.nome) + '</span>';
        html += '<small class="d-block text-muted">' + item.quantidade + ' x Kz ' + item.preco.toFixed(2) + '</small></div>';
        html += '<div><span class="fw-bold" style="color: #c9a84c;">Kz ' + subtotal.toFixed(2) + '</span>';
        html += '<button class="btn btn-sm btn-outline-danger ms-2" onclick="removerDoCarrinho(' + item.id + ')"><i class="fas fa-times"></i></button></div>';
        html += '</div>';
    }
    
    container.innerHTML = html;
    if (totalEl) totalEl.textContent = 'Kz ' + total.toFixed(2);
}

// Envia o carrinho a serio para o servidor (formPedidoCliente, em
// pedidos.php), em vez de so simular com um alert. Os itens vao como
// campos escondidos produto_id[]/quantidade[], o mesmo esquema que o
// painel do operador ja usava.
function finalizarPedido() {
    if (carrinho.length === 0) {
        alert('Seu carrinho esta vazio!');
        return;
    }

    var form = document.getElementById('formPedidoCliente');
    if (!form) return;

    var mesaSelect = form.querySelector('select[name="mesa_id"]');
    if (!mesaSelect || !mesaSelect.value) {
        alert('Escolhe a tua mesa antes de finalizar o pedido.');
        return;
    }

    var total = 0;
    for (var i = 0; i < carrinho.length; i++) {
        total += carrinho[i].preco * carrinho[i].quantidade;
    }

    if (!confirm('Confirmar pedido no valor total de Kz ' + total.toFixed(2) + '?')) {
        return;
    }

    var escondidos = document.getElementById('itensPedidoClienteEscondidos');
    escondidos.innerHTML = '';
    for (var i = 0; i < carrinho.length; i++) {
        escondidos.innerHTML +=
            '<input type="hidden" name="produto_id[]" value="' + carrinho[i].id + '">' +
            '<input type="hidden" name="quantidade[]" value="' + carrinho[i].quantidade + '">';
    }

    form.requestSubmit();
}

// ============================================
// RENDERIZAR MENU
// ============================================

// O nome e a descricao dos produtos agora vem da base de dados
// (menu.php ja injeta os dados reais). Escapamos antes de meter no
// innerHTML para nao correr o risco de um produto com um nome
// malicioso injetar HTML/JS na pagina.
function escaparHtml(texto) {
    var div = document.createElement('div');
    div.textContent = texto == null ? '' : String(texto);
    return div.innerHTML;
}

// Chamada pelos botoes de filtro do cardapio. Alem de filtrar,
// marca visualmente qual o botao ativo. So existe no menu.php
// publico, por isso usa sempre o modo "redirecionar" (ver
// renderizarMenu): quem nao esta na pagina de pedidos precisa de ser
// levado para la antes de o produto poder ir para um carrinho de verdade.
function filtrarMenu(filtro, botao) {
    renderizarMenu('listaMenu', filtro, 'redirecionar');

    if (botao) {
        var botoes = botao.parentElement.querySelectorAll('.btn');
        for (var i = 0; i < botoes.length; i++) {
            botoes[i].classList.remove('active');
        }
        botao.classList.add('active');
    }
}

// modo "carrinho" (padrao, usado em pedidos.php): o botao Adicionar
// mete o produto direto no carrinho da propria pagina.
// modo "redirecionar" (usado em menu.php): a pagina do menu nao tem
// carrinho nenhum, entao o botao manda o cliente para pedidos.php
// com o produto ja identificado na URL, para la ele entrar sozinho
// no carrinho (ver o tratamento de "?adicionar=" no DOMContentLoaded).
function renderizarMenu(containerId, filtro, modo) {
    var container = document.getElementById(containerId);
    if (!container) return;

    if (filtro === undefined) filtro = 'todos';
    if (modo === undefined) modo = 'carrinho';

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

        var imagemHtml = produto.imagem
            ? '<img src="' + escaparHtml(produto.imagem) + '" alt="" style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%; margin: 0 auto; display: block;">'
            : '<div style="width: 70px; height: 70px; background: #f5f0e8; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 28px; color: #c9a84c;"><i class="fas fa-utensils"></i></div>';

        var onclickBotao = modo === 'redirecionar'
            ? "window.location.href='pedidos.php?adicionar=" + produto.id + "'"
            : 'adicionarAoCarrinho(' + produto.id + ')';

        col.innerHTML =
            '<div class="card h-100 border-0 shadow-sm">' +
                '<div class="card-body text-center">' +
                    imagemHtml +
                    '<h6 class="fw-bold mt-3">' + escaparHtml(produto.nome) + '</h6>' +
                    '<p class="text-muted small">' + escaparHtml(produto.descricao) + '</p>' +
                    '<p class="fw-bold" style="color: #c9a84c;">Kz ' + produto.preco.toFixed(2) + '</p>' +
                    '<button class="btn btn-sm w-100" style="background: #c9a84c; color: #1a3c2a;" onclick="' + onclickBotao + '">' +
                        '<i class="fas fa-plus me-1"></i> Adicionar' +
                    '</button>' +
                '</div>' +
            '</div>';
        container.appendChild(col);
    }
}

// ============================================
// INICIALIZACAO
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    var path = window.location.pathname;
    var page = path.split('/').pop();

    // Verificar login
    verificarLogin();

    if (page === 'menu.php') {
        renderizarMenu('listaMenu', 'todos', 'redirecionar');
    }

    if (page === 'pedidos.php') {
        renderizarMenu('listaPedidos', 'todos');
        atualizarCarrinho();

        // Se o cliente veio do menu.php com "Adicionar" num produto,
        // o id chega aqui na URL (?adicionar=5). Metemos logo no
        // carrinho para ele nao ter de procurar o produto outra vez.
        var idParaAdicionar = new URLSearchParams(window.location.search).get('adicionar');
        if (idParaAdicionar) {
            adicionarAoCarrinho(parseInt(idParaAdicionar, 10));
        }
    }

    if (page === 'perfil-cliente.php') {
        carregarPerfil();
    }
    
    if (page === 'reservas.php') {
        var dataInput = document.getElementById('dataReserva');
        if (dataInput) {
            var hoje = new Date().toISOString().split('T')[0];
            dataInput.min = hoje;
            dataInput.value = hoje;
        }
    }
});