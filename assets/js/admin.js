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
// DADOS FICTICIOS
// ============================================
var pagamentos = [
    { id: 1, pedido: '#123', cliente: 'Joao Silva', valor: 2450, metodo: 'Cartao de Credito', status: 'Pago', data: '2026-06-30 14:35' },
    { id: 2, pedido: '#122', cliente: 'Maria Santos', valor: 1200, metodo: 'Dinheiro', status: 'Pago', data: '2026-06-30 13:20' },
    { id: 3, pedido: '#121', cliente: 'Pedro Costa', valor: 1800, metodo: 'Multicaixa Express', status: 'Pendente', data: '2026-06-30 12:10' },
    { id: 4, pedido: '#120', cliente: 'Ana Pereira', valor: 1550, metodo: 'Cartao de Debito', status: 'Pago', data: '2026-06-29 20:50' }
];
var proximoIdPagamento = 5;

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

function filtrarProdutos() {
    var termo = document.getElementById('pesquisaProduto').value.toLowerCase();
    var linhas = document.querySelectorAll('#tabelaProdutos tr');
    var visiveis = 0;

    linhas.forEach(function (linha) {
        var mostra = linha.textContent.toLowerCase().indexOf(termo) !== -1;
        linha.style.display = mostra ? '' : 'none';
        if (mostra) visiveis++;
    });

    var total = document.getElementById('totalProdutos');
    if (total) total.textContent = 'Total: ' + visiveis + ' produtos';
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
            '<td>' + item.nome + '</td>' +
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

function verPedido(botao) {
    var ped = JSON.parse(botao.dataset.pedido);
    var detalhes = document.getElementById('detalhesPedido');

    var linhasItens = '';
    ped.itens.forEach(function (item) {
        linhasItens +=
            '<li class="d-flex justify-content-between border-bottom py-2">' +
                '<span>' + item.produto_nome + '</span>' +
                '<span>' + item.quantidade + ' x Kz ' + parseFloat(item.preco_unitario).toFixed(2) + '</span>' +
            '</li>';
    });

    detalhes.innerHTML =
        '<div class="row">' +
            '<div class="col-md-6">' +
                '<p><strong>Cliente:</strong> ' + (ped.cliente_nome || 'Cliente avulso') + '</p>' +
                '<p><strong>Mesa:</strong> Mesa ' + ped.mesa_numero + '</p>' +
                '<p><strong>Data:</strong> ' + ped.criado_em + '</p>' +
            '</div>' +
            '<div class="col-md-6">' +
                '<p><strong>Total:</strong> Kz ' + parseFloat(ped.total).toFixed(2) + '</p>' +
                '<p><strong>Status:</strong> ' + ped.estado + '</p>' +
                (ped.observacoes ? '<p><strong>Observacoes:</strong> ' + ped.observacoes + '</p>' : '') +
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

function filtrarPedidos() {
    var termo = document.getElementById('pesquisaPedido').value.toLowerCase();
    var linhas = document.querySelectorAll('#tabelaPedidos tr');
    var visiveis = 0;

    linhas.forEach(function (linha) {
        var mostra = linha.textContent.toLowerCase().indexOf(termo) !== -1;
        linha.style.display = mostra ? '' : 'none';
        if (mostra) visiveis++;
    });

    var total = document.getElementById('totalPedidos');
    if (total) total.textContent = 'Total: ' + visiveis + ' pedidos';
}

// ============================================
// PAGAMENTOS - CRUD
// ============================================
function carregarPagamentos() {
    var tbody = document.getElementById('tabelaPagamentos');
    var total = document.getElementById('totalPagamentos');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    for (var i = 0; i < pagamentos.length; i++) {
        var pag = pagamentos[i];
        var statusClass = pag.status === 'Pago' ? 'success' : pag.status === 'Pendente' ? 'warning' : 'danger';
        var tr = document.createElement('tr');
        tr.innerHTML = 
            '<td>' + (i + 1) + '</td>' +
            '<td>' + pag.pedido + '</td>' +
            '<td>' + pag.cliente + '</td>' +
            '<td><strong>Kz ' + pag.valor.toFixed(2) + '</strong></td>' +
            '<td><span class="badge bg-info">' + pag.metodo + '</span></td>' +
            '<td><span class="badge bg-' + statusClass + '">' + pag.status + '</span></td>' +
            '<td>' + pag.data + '</td>' +
            '<td class="text-center">' +
                '<button class="btn btn-sm btn-outline-success me-1" onclick="editarPagamento(' + pag.id + ')"><i class="fas fa-edit"></i></button>' +
                '<button class="btn btn-sm btn-outline-danger" onclick="eliminarPagamento(' + pag.id + ')"><i class="fas fa-trash"></i></button>' +
            '</td>';
        tbody.appendChild(tr);
    }
    if (total) total.textContent = 'Total: ' + pagamentos.length + ' pagamentos';
}

function abrirModalPagamento() {
    document.getElementById('pagamentoId').value = '';
    document.getElementById('modalPagamentoTitulo').innerHTML = '<i class="fas fa-credit-card me-2"></i> Novo Pagamento';
    document.getElementById('btnSalvarPagamento').textContent = 'Salvar';
    document.getElementById('formPagamento').reset();
    var modal = new bootstrap.Modal(document.getElementById('modalPagamento'));
    modal.show();
}

function editarPagamento(id) {
    var pag = null;
    for (var i = 0; i < pagamentos.length; i++) {
        if (pagamentos[i].id === id) {
            pag = pagamentos[i];
            break;
        }
    }
    if (!pag) return;
    
    document.getElementById('pagamentoId').value = pag.id;
    document.getElementById('modalPagamentoTitulo').innerHTML = '<i class="fas fa-edit me-2"></i> Editar Pagamento';
    document.getElementById('btnSalvarPagamento').textContent = 'Atualizar';
    document.getElementById('pedidoPagamento').value = pag.pedido;
    document.getElementById('valorPagamento').value = pag.valor;
    document.getElementById('metodoPagamento').value = pag.metodo;
    document.getElementById('statusPagamento').value = pag.status;
    var modal = new bootstrap.Modal(document.getElementById('modalPagamento'));
    modal.show();
}

function salvarPagamento() {
    var id = document.getElementById('pagamentoId').value;
    var pedido = document.getElementById('pedidoPagamento').value;
    var valor = parseFloat(document.getElementById('valorPagamento').value);
    var metodo = document.getElementById('metodoPagamento').value;
    var status = document.getElementById('statusPagamento').value;
    
    if (!pedido || isNaN(valor) || !metodo) {
        alert('Preencha todos os campos obrigatorios!');
        return;
    }
    
    var agora = new Date().toLocaleString('pt-PT', {
        year: 'numeric', month: '2-digit', day: '2-digit',
        hour: '2-digit', minute: '2-digit'
    }).replace(/\//g, '-');
    
    var cliente = 'Cliente';
    for (var i = 0; i < pedidos.length; i++) {
        if (pedidos[i].id.toString() === pedido.replace('#', '')) {
            cliente = pedidos[i].cliente;
            break;
        }
    }
    
    if (id) {
        for (var i = 0; i < pagamentos.length; i++) {
            if (pagamentos[i].id === parseInt(id)) {
                pagamentos[i].pedido = pedido;
                pagamentos[i].valor = valor;
                pagamentos[i].metodo = metodo;
                pagamentos[i].status = status;
                break;
            }
        }
    } else {
        pagamentos.push({
            id: proximoIdPagamento++,
            pedido: pedido,
            cliente: cliente,
            valor: valor,
            metodo: metodo,
            status: status,
            data: agora
        });
    }
    
    var modal = bootstrap.Modal.getInstance(document.getElementById('modalPagamento'));
    modal.hide();
    carregarPagamentos();
    alert(id ? 'Pagamento atualizado com sucesso!' : 'Pagamento criado com sucesso!');
}

function eliminarPagamento(id) {
    if (confirm('Tem certeza que deseja eliminar este pagamento?')) {
        var novoArray = [];
        for (var i = 0; i < pagamentos.length; i++) {
            if (pagamentos[i].id !== id) {
                novoArray.push(pagamentos[i]);
            }
        }
        pagamentos = novoArray;
        carregarPagamentos();
        alert('Pagamento eliminado com sucesso!');
    }
}

function filtrarPagamentos() {
    var termo = document.getElementById('pesquisaPagamento').value.toLowerCase();
    var tbody = document.getElementById('tabelaPagamentos');
    var total = document.getElementById('totalPagamentos');
    if (!tbody) return;
    
    var filtrados = [];
    for (var i = 0; i < pagamentos.length; i++) {
        if (pagamentos[i].cliente.toLowerCase().includes(termo) || pagamentos[i].pedido.includes(termo) || pagamentos[i].metodo.toLowerCase().includes(termo)) {
            filtrados.push(pagamentos[i]);
        }
    }
    
    tbody.innerHTML = '';
    for (var i = 0; i < filtrados.length; i++) {
        var pag = filtrados[i];
        var statusClass = pag.status === 'Pago' ? 'success' : pag.status === 'Pendente' ? 'warning' : 'danger';
        var tr = document.createElement('tr');
        tr.innerHTML = 
            '<td>' + (i + 1) + '</td>' +
            '<td>' + pag.pedido + '</td>' +
            '<td>' + pag.cliente + '</td>' +
            '<td><strong>Kz ' + pag.valor.toFixed(2) + '</strong></td>' +
            '<td><span class="badge bg-info">' + pag.metodo + '</span></td>' +
            '<td><span class="badge bg-' + statusClass + '">' + pag.status + '</span></td>' +
            '<td>' + pag.data + '</td>' +
            '<td class="text-center">' +
                '<button class="btn btn-sm btn-outline-success me-1" onclick="editarPagamento(' + pag.id + ')"><i class="fas fa-edit"></i></button>' +
                '<button class="btn btn-sm btn-outline-danger" onclick="eliminarPagamento(' + pag.id + ')"><i class="fas fa-trash"></i></button>' +
            '</td>';
        tbody.appendChild(tr);
    }
    if (total) total.textContent = 'Total: ' + filtrados.length + ' pagamentos (filtrados)';
}

// ============================================
// RELATORIOS
// ============================================
function gerarRelatorio() {
    var tipo = document.getElementById('tipoRelatorio').value;
    var dataInicio = document.getElementById('dataInicio').value;
    var dataFim = document.getElementById('dataFim').value;
    
    if (!dataInicio || !dataFim) {
        alert('Selecione as datas de inicio e fim!');
        return;
    }
    
    alert('Relatorio de ' + tipo + ' gerado de ' + dataInicio + ' ate ' + dataFim);
}

function exportarRelatorio() {
    alert('Relatorio exportado com sucesso!');
}

// ============================================
// INICIALIZAR
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    var page = window.location.pathname.split('/').pop();
    
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
    
    // Carregar dados conforme a pagina
    switch(page) {
        case 'pagamentos.php':
            if (typeof carregarPagamentos === 'function') carregarPagamentos();
            break;
        case 'relatorios.php':
            var hoje = new Date().toISOString().split('T')[0];
            var semana = new Date();
            semana.setDate(semana.getDate() - 7);
            var semanaStr = semana.toISOString().split('T')[0];
            if (document.getElementById('dataInicio')) {
                document.getElementById('dataInicio').value = semanaStr;
                document.getElementById('dataFim').value = hoje;
            }
            break;
    }
});