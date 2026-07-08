// ============================================
// ADMIN
// O acesso a esta area ja e controlado no servidor (ver o topo de
// cada view em views/admin/), entao aqui nao precisa de verificar login.
// ============================================

// ============================================
// FUNCOES GERAIS
// ============================================

// RELOGIO
function atualizarRelogio() {
    var agora = new Date();
    var relogio = document.getElementById('relogio');
    if (relogio) {
        relogio.textContent = agora.toLocaleTimeString('pt-PT', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }
}

// TOGGLE SIDEBAR
function toggleSidebar() {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('overlay');
    if (sidebar) {
        sidebar.classList.toggle('open');
        if (overlay) overlay.classList.toggle('active');
    }
}

function fecharSidebar() {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('overlay');
    if (sidebar) {
        sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('active');
    }
}

// SAIR
function logout() {
    if (confirm('Deseja realmente sair?')) {
        window.location.href = '/index.php?rota=logout';
    }
}

// ============================================
// UTILIZADORES - CRUD
// A tabela ja vem pronta do servidor (ver views/admin/utilizadores.php).
// Este JS so cuida do modal: abrir, preencher ao editar, e o filtro de
// pesquisa. Guardar e eliminar sao formularios normais que submetem
// para o servidor a serio.
// ============================================
function abrirModalUtilizador() {
    document.getElementById('utilizadorId').value = '';
    document.getElementById('modalUtilizadorTitulo').innerHTML = '<i class="fas fa-user-plus me-2"></i> Novo Utilizador';
    document.getElementById('btnSalvarUtilizador').textContent = 'Salvar';
    document.getElementById('formUtilizador').reset();
    document.getElementById('senhaUtilizador').placeholder = 'Minimo 6 caracteres';
    var modal = new bootstrap.Modal(document.getElementById('modalUtilizador'));
    modal.show();
}

function editarUtilizador(botao) {
    document.getElementById('utilizadorId').value = botao.dataset.id;
    document.getElementById('modalUtilizadorTitulo').innerHTML = '<i class="fas fa-user-edit me-2"></i> Editar Utilizador';
    document.getElementById('btnSalvarUtilizador').textContent = 'Atualizar';
    document.getElementById('nomeUtilizador').value = botao.dataset.nome;
    document.getElementById('emailUtilizador').value = botao.dataset.email;
    document.getElementById('perfilUtilizador').value = botao.dataset.perfil;
    document.getElementById('statusUtilizador').value = botao.dataset.estado;
    document.getElementById('senhaUtilizador').value = '';
    document.getElementById('senhaUtilizador').placeholder = 'Deixa em branco para manter a senha atual';
    var modal = new bootstrap.Modal(document.getElementById('modalUtilizador'));
    modal.show();
}

function eliminarUtilizador(id) {
    if (confirm('Tem certeza que deseja eliminar este utilizador?')) {
        document.getElementById('eliminarUtilizadorId').value = id;
        document.getElementById('formEliminarUtilizador').submit();
    }
}

function filtrarUtilizadores() {
    var termo = document.getElementById('pesquisaUtilizador').value.toLowerCase();
    var linhas = document.querySelectorAll('#tabelaUtilizadores tr');
    var visiveis = 0;

    linhas.forEach(function (linha) {
        var mostra = linha.textContent.toLowerCase().indexOf(termo) !== -1;
        linha.style.display = mostra ? '' : 'none';
        if (mostra) visiveis++;
    });

    var total = document.getElementById('totalUtilizadores');
    if (total) total.textContent = 'Total: ' + visiveis + ' utilizadores';
}

// ============================================
// CATEGORIAS - CRUD
// Mesmo esquema das Utilizadores: a tabela vem pronta do servidor,
// o JS so trata do modal e do filtro.
// ============================================
function abrirModalCategoria() {
    document.getElementById('categoriaId').value = '';
    document.getElementById('modalCategoriaTitulo').innerHTML = '<i class="fas fa-tag me-2"></i> Nova Categoria';
    document.getElementById('btnSalvarCategoria').textContent = 'Salvar';
    document.getElementById('formCategoria').reset();
    var modal = new bootstrap.Modal(document.getElementById('modalCategoria'));
    modal.show();
}

function editarCategoria(botao) {
    document.getElementById('categoriaId').value = botao.dataset.id;
    document.getElementById('modalCategoriaTitulo').innerHTML = '<i class="fas fa-edit me-2"></i> Editar Categoria';
    document.getElementById('btnSalvarCategoria').textContent = 'Atualizar';
    document.getElementById('nomeCategoria').value = botao.dataset.nome;
    document.getElementById('descricaoCategoria').value = botao.dataset.descricao;
    document.getElementById('statusCategoria').value = botao.dataset.estado;
    var modal = new bootstrap.Modal(document.getElementById('modalCategoria'));
    modal.show();
}

function eliminarCategoria(id) {
    if (confirm('Tem certeza que deseja eliminar esta categoria?')) {
        document.getElementById('eliminarCategoriaId').value = id;
        document.getElementById('formEliminarCategoria').submit();
    }
}

function filtrarCategorias() {
    var termo = document.getElementById('pesquisaCategoria').value.toLowerCase();
    var linhas = document.querySelectorAll('#tabelaCategorias tr');
    var visiveis = 0;

    linhas.forEach(function (linha) {
        var mostra = linha.textContent.toLowerCase().indexOf(termo) !== -1;
        linha.style.display = mostra ? '' : 'none';
        if (mostra) visiveis++;
    });

    var total = document.getElementById('totalCategorias');
    if (total) total.textContent = 'Total: ' + visiveis + ' categorias';
}

// ============================================
// PRODUTOS - CRUD
// ============================================
function abrirModalProduto() {
    document.getElementById('produtoId').value = '';
    document.getElementById('modalProdutoTitulo').innerHTML = '<i class="fas fa-box me-2"></i> Novo Produto';
    document.getElementById('btnSalvarProduto').textContent = 'Salvar';
    document.getElementById('formProduto').reset();
    document.getElementById('previaImagemProduto').style.display = 'none';
    var modal = new bootstrap.Modal(document.getElementById('modalProduto'));
    modal.show();
}

function editarProduto(botao) {
    document.getElementById('produtoId').value = botao.dataset.id;
    document.getElementById('modalProdutoTitulo').innerHTML = '<i class="fas fa-edit me-2"></i> Editar Produto';
    document.getElementById('btnSalvarProduto').textContent = 'Atualizar';
    document.getElementById('nomeProduto').value = botao.dataset.nome;
    document.getElementById('categoriaProduto').value = botao.dataset.categoriaId;
    document.getElementById('descricaoProduto').value = botao.dataset.descricao;
    document.getElementById('precoProduto').value = botao.dataset.preco;
    document.getElementById('stockProduto').value = botao.dataset.estoque;
    document.getElementById('statusProduto').value = botao.dataset.estado;
    document.getElementById('imagemProduto').value = '';
    document.getElementById('previaImagemProduto').style.display = 'none';
    var modal = new bootstrap.Modal(document.getElementById('modalProduto'));
    modal.show();
}

function eliminarProduto(id) {
    if (confirm('Tem certeza que deseja eliminar este produto?')) {
        document.getElementById('eliminarProdutoId').value = id;
        document.getElementById('formEliminarProduto').submit();
    }
}

// Mostra uma previa da imagem escolhida antes de guardar o produto.
function preVisualizarImagem(input) {
    var previa = document.getElementById('previaImagemProduto');
    if (!input.files || !input.files[0]) {
        previa.style.display = 'none';
        return;
    }

    var leitor = new FileReader();
    leitor.onload = function (e) {
        previa.src = e.target.result;
        previa.style.display = 'inline-block';
    };
    leitor.readAsDataURL(input.files[0]);
}

// Pesquisa em tempo real: a cada letra digitada (ou mudanca de
// categoria, ou clique numa pagina), busca so os produtos certos no
// servidor via fetch() e redesenha a tabela, sem recarregar a pagina.
// O debounce evita mandar um pedido a cada tecla, so pesquisa quando
// a pessoa para de escrever por 300ms.
var temporizadorPesquisaProdutos = null;

function pesquisarProdutosAjax(pagina) {
    clearTimeout(temporizadorPesquisaProdutos);

    temporizadorPesquisaProdutos = setTimeout(function () {
        var nome = document.getElementById('pesquisaNomeProduto').value;
        var codigo = document.getElementById('pesquisaCodigoProduto').value;
        var categoriaId = document.getElementById('pesquisaCategoriaProduto').value;

        var parametros = new URLSearchParams({
            rota: 'produtos.pesquisar-ajax',
            nome: nome,
            codigo: codigo,
            categoria_id: categoriaId,
            pagina: pagina || 1
        });

        fetch('/index.php?' + parametros.toString())
            .then(function (resposta) { return resposta.json(); })
            .then(function (dados) {
                renderizarTabelaProdutos(dados.produtos, (dados.paginaAtual - 1) * 10);
                renderizarPaginacaoProdutos(dados.paginaAtual, dados.totalPaginas);
                document.getElementById('totalProdutos').textContent = 'Total: ' + dados.total + ' produtos';
            });
    }, 300);
}

function limparPesquisaProdutos() {
    document.getElementById('pesquisaNomeProduto').value = '';
    document.getElementById('pesquisaCodigoProduto').value = '';
    document.getElementById('pesquisaCategoriaProduto').value = '';
    pesquisarProdutosAjax(1);
}

function renderizarTabelaProdutos(produtos, indiceInicial) {
    var corpo = document.getElementById('tabelaProdutos');
    if (!produtos.length) {
        corpo.innerHTML = '<tr><td colspan="8" class="text-muted text-center py-3">Nenhum produto encontrado.</td></tr>';
        return;
    }

    var html = '';
    produtos.forEach(function (p, i) {
        var imagemHtml = p.imagem
            ? '<img src="../../assets/uploads/' + escaparHtml(p.imagem) + '" alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;">'
            : '<div style="width: 40px; height: 40px; background: #e9ecef; border-radius: 8px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-utensils text-muted"></i></div>';

        var corStatus = p.estado === 'Disponivel' ? 'success' : (p.estado === 'Esgotado' ? 'danger' : 'warning');

        html += '<tr>' +
            '<td>' + (indiceInicial + i + 1) + '</td>' +
            '<td>' + imagemHtml + '</td>' +
            '<td>' + escaparHtml(p.nome) + '</td>' +
            '<td><span class="badge bg-secondary">' + escaparHtml(p.categoria_nome) + '</span></td>' +
            '<td><strong>Kz ' + parseFloat(p.preco).toFixed(2) + '</strong></td>' +
            '<td>' + p.estoque + '</td>' +
            '<td><span class="badge bg-' + corStatus + '">' + escaparHtml(p.estado) + '</span></td>' +
            '<td class="text-center">' +
                '<button type="button" class="btn btn-sm btn-outline-success me-1" onclick="editarProduto(this)"' +
                    ' data-id="' + p.id + '"' +
                    ' data-nome="' + escaparHtml(p.nome) + '"' +
                    ' data-categoria-id="' + p.categoria_id + '"' +
                    ' data-preco="' + p.preco + '"' +
                    ' data-estoque="' + p.estoque + '"' +
                    ' data-estado="' + escaparHtml(p.estado) + '"' +
                    ' data-descricao="' + escaparHtml(p.descricao || '') + '">' +
                    '<i class="fas fa-edit"></i>' +
                '</button>' +
                '<button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarProduto(' + p.id + ')"><i class="fas fa-trash"></i></button>' +
            '</td>' +
        '</tr>';
    });

    corpo.innerHTML = html;
}

function renderizarPaginacaoProdutos(paginaAtual, totalPaginas) {
    var nav = document.getElementById('paginacaoProdutos');
    if (!nav) return;

    if (totalPaginas <= 1) {
        nav.innerHTML = '';
        return;
    }

    var html = '<ul class="pagination pagination-sm mb-0">';
    for (var p = 1; p <= totalPaginas; p++) {
        html += '<li class="page-item' + (p === paginaAtual ? ' active' : '') + '">' +
            '<a class="page-link" href="#" onclick="event.preventDefault(); pesquisarProdutosAjax(' + p + ')">' + p + '</a>' +
        '</li>';
    }
    html += '</ul>';
    nav.innerHTML = html;
}

// ============================================
// MESAS - CRUD
// ============================================
function abrirModalMesa() {
    document.getElementById('mesaId').value = '';
    document.getElementById('modalMesaTitulo').innerHTML = '<i class="fas fa-chair me-2"></i> Nova Mesa';
    document.getElementById('btnSalvarMesa').textContent = 'Salvar';
    document.getElementById('formMesa').reset();
    var modal = new bootstrap.Modal(document.getElementById('modalMesa'));
    modal.show();
}

function editarMesa(botao) {
    document.getElementById('mesaId').value = botao.dataset.id;
    document.getElementById('modalMesaTitulo').innerHTML = '<i class="fas fa-edit me-2"></i> Editar Mesa';
    document.getElementById('btnSalvarMesa').textContent = 'Atualizar';
    document.getElementById('numeroMesa').value = botao.dataset.numero;
    document.getElementById('capacidadeMesa').value = botao.dataset.capacidade;
    document.getElementById('localizacaoMesa').value = botao.dataset.localizacao;
    document.getElementById('statusMesa').value = botao.dataset.estado;
    var modal = new bootstrap.Modal(document.getElementById('modalMesa'));
    modal.show();
}

function eliminarMesa(id) {
    if (confirm('Tem certeza que deseja eliminar esta mesa?')) {
        document.getElementById('eliminarMesaId').value = id;
        document.getElementById('formEliminarMesa').submit();
    }
}

function filtrarMesas() {
    var termo = document.getElementById('pesquisaMesa').value.toLowerCase();
    var linhas = document.querySelectorAll('#tabelaMesas tr');
    var visiveis = 0;

    linhas.forEach(function (linha) {
        var mostra = linha.textContent.toLowerCase().indexOf(termo) !== -1;
        linha.style.display = mostra ? '' : 'none';
        if (mostra) visiveis++;
    });

    var total = document.getElementById('totalMesas');
    if (total) total.textContent = 'Total: ' + visiveis + ' mesas';
}

// ============================================
// CLIENTES - CRUD
// ============================================
function abrirModalCliente() {
    document.getElementById('clienteId').value = '';
    document.getElementById('modalClienteTitulo').innerHTML = '<i class="fas fa-user-plus me-2"></i> Novo Cliente';
    document.getElementById('btnSalvarCliente').textContent = 'Salvar';
    document.getElementById('formCliente').reset();
    var modal = new bootstrap.Modal(document.getElementById('modalCliente'));
    modal.show();
}

function editarCliente(botao) {
    document.getElementById('clienteId').value = botao.dataset.id;
    document.getElementById('modalClienteTitulo').innerHTML = '<i class="fas fa-edit me-2"></i> Editar Cliente';
    document.getElementById('btnSalvarCliente').textContent = 'Atualizar';
    document.getElementById('nomeCliente').value = botao.dataset.nome;
    document.getElementById('emailCliente').value = botao.dataset.email;
    document.getElementById('telefoneCliente').value = botao.dataset.telefone;
    document.getElementById('nifCliente').value = botao.dataset.nif;
    document.getElementById('enderecoCliente').value = botao.dataset.endereco;
    var modal = new bootstrap.Modal(document.getElementById('modalCliente'));
    modal.show();
}

function eliminarCliente(id) {
    if (confirm('Tem certeza que deseja eliminar este cliente?')) {
        document.getElementById('eliminarClienteId').value = id;
        document.getElementById('formEliminarCliente').submit();
    }
}

function filtrarClientes() {
    var termo = document.getElementById('pesquisaCliente').value.toLowerCase();
    var linhas = document.querySelectorAll('#tabelaClientes tr');
    var visiveis = 0;

    linhas.forEach(function (linha) {
        var mostra = linha.textContent.toLowerCase().indexOf(termo) !== -1;
        linha.style.display = mostra ? '' : 'none';
        if (mostra) visiveis++;
    });

    var total = document.getElementById('totalClientes');
    if (total) total.textContent = 'Total: ' + visiveis + ' clientes';
}

// ============================================
// PEDIDOS - CRUD
// A tabela ja vem pronta do servidor. Aqui so fica o carrinho de
// produtos do "Novo Pedido", o modal de mudar estado, o "ver
// detalhes" e o filtro.
// ============================================
var carrinhoPedido = [];

function abrirModalPedido() {
    carrinhoPedido = [];
    renderizarCarrinhoPedido();
    document.getElementById('formPedido').reset();
    var modal = new bootstrap.Modal(document.getElementById('modalPedido'));
    modal.show();
}

function adicionarItemPedido() {
    var select = document.getElementById('produtoParaAdicionar');
    var opcaoEscolhida = select.options[select.selectedIndex];
    var quantidade = parseInt(document.getElementById('quantidadeParaAdicionar').value) || 1;

    if (!opcaoEscolhida || quantidade < 1) return;

    carrinhoPedido.push({
        produtoId: opcaoEscolhida.value,
        nome: opcaoEscolhida.dataset.nome,
        preco: parseFloat(opcaoEscolhida.dataset.preco),
        quantidade: quantidade
    });

    renderizarCarrinhoPedido();
}

function removerItemPedido(indice) {
    carrinhoPedido.splice(indice, 1);
    renderizarCarrinhoPedido();
}

// Desenha a tabela do carrinho e, ao mesmo tempo, cria os campos
// escondidos (produto_id[] e quantidade[]) que vao mesmo no POST.
function renderizarCarrinhoPedido() {
    var tbody = document.getElementById('carrinhoPedidoTabela');
    var escondidos = document.getElementById('itensPedidoEscondidos');
    var total = 0;

    tbody.innerHTML = '';
    escondidos.innerHTML = '';

    carrinhoPedido.forEach(function (item, indice) {
        var subtotal = item.preco * item.quantidade;
        total += subtotal;

        var linha = document.createElement('tr');
        linha.innerHTML =
            '<td>' + escaparHtml(item.nome) + '</td>' +
            '<td>' + item.quantidade + '</td>' +
            '<td>Kz ' + subtotal.toFixed(2) + '</td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removerItemPedido(' + indice + ')"><i class="fas fa-times"></i></button></td>';
        tbody.appendChild(linha);

        escondidos.innerHTML +=
            '<input type="hidden" name="produto_id[]" value="' + item.produtoId + '">' +
            '<input type="hidden" name="quantidade[]" value="' + item.quantidade + '">';
    });

    document.getElementById('totalCarrinhoPedido').textContent = 'Kz ' + total.toFixed(2);
}

// Nome de produto, nome de cliente e observacoes sao texto livre
// escrito por um utilizador, entao antes de meter isso dentro de
// innerHTML tem de se escapar, senao um nome tipo "<img onerror=...>"
// executava assim que alguem abrisse os detalhes do pedido.
function escaparHtml(texto) {
    var div = document.createElement('div');
    div.textContent = texto == null ? '' : String(texto);
    return div.innerHTML;
}

function verPedido(botao) {
    var ped = JSON.parse(botao.dataset.pedido);
    var detalhes = document.getElementById('detalhesPedido');

    var linhasItens = '';
    ped.itens.forEach(function (item) {
        linhasItens +=
            '<li class="d-flex justify-content-between border-bottom py-2">' +
                '<span>' + escaparHtml(item.produto_nome) + '</span>' +
                '<span>' + item.quantidade + ' x Kz ' + parseFloat(item.preco_unitario).toFixed(2) + '</span>' +
            '</li>';
    });

    detalhes.innerHTML =
        '<div class="row">' +
            '<div class="col-md-6">' +
                '<p><strong>Cliente:</strong> ' + escaparHtml(ped.cliente_nome || 'Cliente avulso') + '</p>' +
                '<p><strong>Mesa:</strong> Mesa ' + ped.mesa_numero + '</p>' +
                '<p><strong>Data:</strong> ' + ped.criado_em + '</p>' +
            '</div>' +
            '<div class="col-md-6">' +
                '<p><strong>Total:</strong> Kz ' + parseFloat(ped.total).toFixed(2) + '</p>' +
                '<p><strong>Status:</strong> ' + ped.estado + '</p>' +
                (ped.observacoes ? '<p><strong>Observacoes:</strong> ' + escaparHtml(ped.observacoes) + '</p>' : '') +
            '</div>' +
        '</div>' +
        '<hr>' +
        '<h6 class="fw-semibold">Itens do Pedido</h6>' +
        '<ul class="list-unstyled">' + linhasItens + '</ul>';

    var modal = new bootstrap.Modal(document.getElementById('modalVerPedido'));
    modal.show();
}

function editarEstadoPedido(botao) {
    document.getElementById('estadoPedidoId').value = botao.dataset.id;
    document.getElementById('novoEstadoPedido').value = botao.dataset.estado;
    var modal = new bootstrap.Modal(document.getElementById('modalEstadoPedido'));
    modal.show();
}

function eliminarPedido(id) {
    if (confirm('Tem certeza que deseja eliminar este pedido?')) {
        document.getElementById('eliminarPedidoId').value = id;
        document.getElementById('formEliminarPedido').submit();
    }
}

// ============================================
// PAGAMENTOS - CRUD
// ============================================
function abrirModalPagamento() {
    document.getElementById('formPagamento').reset();
    var modal = new bootstrap.Modal(document.getElementById('modalPagamento'));
    modal.show();
}

// Ao escolher o pedido, sugere logo o valor total dele (o utilizador
// pode mudar, por exemplo se o pagamento for so parcial).
function preencherValorPagamento(select) {
    var opcaoEscolhida = select.options[select.selectedIndex];
    if (opcaoEscolhida && opcaoEscolhida.dataset.total) {
        document.getElementById('valorPagamento').value = opcaoEscolhida.dataset.total;
    }
}

function eliminarPagamento(id) {
    if (confirm('Tem certeza que deseja eliminar este pagamento?')) {
        document.getElementById('eliminarPagamentoId').value = id;
        document.getElementById('formEliminarPagamento').submit();
    }
}

function filtrarPagamentos() {
    var termo = document.getElementById('pesquisaPagamento').value.toLowerCase();
    var linhas = document.querySelectorAll('#tabelaPagamentos tr');
    var visiveis = 0;

    linhas.forEach(function (linha) {
        var mostra = linha.textContent.toLowerCase().indexOf(termo) !== -1;
        linha.style.display = mostra ? '' : 'none';
        if (mostra) visiveis++;
    });

    var total = document.getElementById('totalPagamentos');
    if (total) total.textContent = 'Total: ' + visiveis + ' pagamentos';
}

// ============================================
// INICIALIZAR
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Relogio
    atualizarRelogio();
    setInterval(atualizarRelogio, 1000);

    // Fechar sidebar ao clicar fora (mobile)
    document.addEventListener('click', function(e) {
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('overlay');
        if (window.innerWidth <= 768) {
            if (sidebar && !sidebar.contains(e.target) && !e.target.closest('.toggle-sidebar')) {
                sidebar.classList.remove('open');
                if (overlay) overlay.classList.remove('active');
            }
        }
    });

    // Produtos: a tabela e a paginacao ja vem desenhadas do servidor
    // na primeira visita, mas so a paginacao (nao ha ainda nenhum
    // numero de pagina desenhado no PHP). Buscamos logo a pagina 1
    // via AJAX para a barra de paginas aparecer certa desde o inicio.
    if (document.getElementById('pesquisaNomeProduto')) {
        pesquisarProdutosAjax(1);
    }
});