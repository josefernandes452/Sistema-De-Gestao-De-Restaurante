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
var mesas = [
    { id: 1, numero: 1, capacidade: 4, localizacao: 'Salao Principal', status: 'Livre', ocupadoPor: '-' },
    { id: 2, numero: 2, capacidade: 2, localizacao: 'Salao Principal', status: 'Ocupada', ocupadoPor: 'Joao Silva' },
    { id: 3, numero: 3, capacidade: 6, localizacao: 'Salao Principal', status: 'Livre', ocupadoPor: '-' },
    { id: 4, numero: 4, capacidade: 4, localizacao: 'Varanda', status: 'Reservada', ocupadoPor: 'Maria Santos' }
];
var proximoIdMesa = 5;

var clientes = [
    { id: 1, nome: 'Joao Silva', email: 'joao@email.com', telefone: '923456789', nif: '123456789', totalPedidos: 5 },
    { id: 2, nome: 'Maria Santos', email: 'maria@email.com', telefone: '934567890', nif: '987654321', totalPedidos: 3 },
    { id: 3, nome: 'Pedro Costa', email: 'pedro@email.com', telefone: '945678901', nif: '', totalPedidos: 8 },
    { id: 4, nome: 'Ana Pereira', email: 'ana@email.com', telefone: '956789012', nif: '456789123', totalPedidos: 2 }
];
var proximoIdCliente = 5;

var pedidos = [
    { id: 1, cliente: 'Joao Silva', mesa: 'Mesa 1', total: 2450, status: 'Entregue', data: '2026-06-30 14:30' },
    { id: 2, cliente: 'Maria Santos', mesa: 'Mesa 4', total: 1200, status: 'Em Preparacao', data: '2026-06-30 13:15' },
    { id: 3, cliente: 'Pedro Costa', mesa: 'Mesa 2', total: 1800, status: 'Pendente', data: '2026-06-30 12:00' },
    { id: 4, cliente: 'Ana Pereira', mesa: 'Mesa 3', total: 1550, status: 'Pronto', data: '2026-06-29 20:45' }
];
var proximoIdPedido = 5;

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
function carregarMesas() {
    var tbody = document.getElementById('tabelaMesas');
    var total = document.getElementById('totalMesas');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    for (var i = 0; i < mesas.length; i++) {
        var mesa = mesas[i];
        var statusClass = mesa.status === 'Livre' ? 'success' : mesa.status === 'Ocupada' ? 'danger' : mesa.status === 'Reservada' ? 'warning' : 'secondary';
        var tr = document.createElement('tr');
        tr.innerHTML = 
            '<td>' + (i + 1) + '</td>' +
            '<td><strong>Mesa ' + mesa.numero + '</strong></td>' +
            '<td>' + mesa.capacidade + ' pessoas</td>' +
            '<td>' + mesa.localizacao + '</td>' +
            '<td><span class="badge bg-' + statusClass + '">' + mesa.status + '</span></td>' +
            '<td>' + mesa.ocupadoPor + '</td>' +
            '<td class="text-center">' +
                '<button class="btn btn-sm btn-outline-success me-1" onclick="editarMesa(' + mesa.id + ')"><i class="fas fa-edit"></i></button>' +
                '<button class="btn btn-sm btn-outline-danger" onclick="eliminarMesa(' + mesa.id + ')"><i class="fas fa-trash"></i></button>' +
            '</td>';
        tbody.appendChild(tr);
    }
    if (total) total.textContent = 'Total: ' + mesas.length + ' mesas';
}

function abrirModalMesa() {
    document.getElementById('mesaId').value = '';
    document.getElementById('modalMesaTitulo').innerHTML = '<i class="fas fa-chair me-2"></i> Nova Mesa';
    document.getElementById('btnSalvarMesa').textContent = 'Salvar';
    document.getElementById('formMesa').reset();
    var modal = new bootstrap.Modal(document.getElementById('modalMesa'));
    modal.show();
}

function editarMesa(id) {
    var mesa = null;
    for (var i = 0; i < mesas.length; i++) {
        if (mesas[i].id === id) {
            mesa = mesas[i];
            break;
        }
    }
    if (!mesa) return;
    
    document.getElementById('mesaId').value = mesa.id;
    document.getElementById('modalMesaTitulo').innerHTML = '<i class="fas fa-edit me-2"></i> Editar Mesa';
    document.getElementById('btnSalvarMesa').textContent = 'Atualizar';
    document.getElementById('numeroMesa').value = mesa.numero;
    document.getElementById('capacidadeMesa').value = mesa.capacidade;
    document.getElementById('localizacaoMesa').value = mesa.localizacao;
    document.getElementById('statusMesa').value = mesa.status;
    var modal = new bootstrap.Modal(document.getElementById('modalMesa'));
    modal.show();
}

function salvarMesa() {
    var id = document.getElementById('mesaId').value;
    var numero = parseInt(document.getElementById('numeroMesa').value);
    var capacidade = parseInt(document.getElementById('capacidadeMesa').value);
    var localizacao = document.getElementById('localizacaoMesa').value.trim();
    var status = document.getElementById('statusMesa').value;
    
    if (!numero || !capacidade) {
        alert('Preencha todos os campos obrigatorios!');
        return;
    }
    
    if (id) {
        for (var i = 0; i < mesas.length; i++) {
            if (mesas[i].id === parseInt(id)) {
                mesas[i].numero = numero;
                mesas[i].capacidade = capacidade;
                mesas[i].localizacao = localizacao;
                mesas[i].status = status;
                mesas[i].ocupadoPor = status === 'Ocupada' ? 'Cliente' : '-';
                break;
            }
        }
    } else {
        mesas.push({
            id: proximoIdMesa++,
            numero: numero,
            capacidade: capacidade,
            localizacao: localizacao,
            status: status,
            ocupadoPor: status === 'Ocupada' ? 'Cliente' : '-'
        });
    }
    
    var modal = bootstrap.Modal.getInstance(document.getElementById('modalMesa'));
    modal.hide();
    carregarMesas();
    alert(id ? 'Mesa atualizada com sucesso!' : 'Mesa criada com sucesso!');
}

function eliminarMesa(id) {
    if (confirm('Tem certeza que deseja eliminar esta mesa?')) {
        var novoArray = [];
        for (var i = 0; i < mesas.length; i++) {
            if (mesas[i].id !== id) {
                novoArray.push(mesas[i]);
            }
        }
        mesas = novoArray;
        carregarMesas();
        alert('Mesa eliminada com sucesso!');
    }
}

function filtrarMesas() {
    var termo = document.getElementById('pesquisaMesa').value.toLowerCase();
    var tbody = document.getElementById('tabelaMesas');
    var total = document.getElementById('totalMesas');
    if (!tbody) return;
    
    var filtrados = [];
    for (var i = 0; i < mesas.length; i++) {
        if (mesas[i].numero.toString().includes(termo) || mesas[i].localizacao.toLowerCase().includes(termo)) {
            filtrados.push(mesas[i]);
        }
    }
    
    tbody.innerHTML = '';
    for (var i = 0; i < filtrados.length; i++) {
        var mesa = filtrados[i];
        var statusClass = mesa.status === 'Livre' ? 'success' : mesa.status === 'Ocupada' ? 'danger' : mesa.status === 'Reservada' ? 'warning' : 'secondary';
        var tr = document.createElement('tr');
        tr.innerHTML = 
            '<td>' + (i + 1) + '</td>' +
            '<td><strong>Mesa ' + mesa.numero + '</strong></td>' +
            '<td>' + mesa.capacidade + ' pessoas</td>' +
            '<td>' + mesa.localizacao + '</td>' +
            '<td><span class="badge bg-' + statusClass + '">' + mesa.status + '</span></td>' +
            '<td>' + mesa.ocupadoPor + '</td>' +
            '<td class="text-center">' +
                '<button class="btn btn-sm btn-outline-success me-1" onclick="editarMesa(' + mesa.id + ')"><i class="fas fa-edit"></i></button>' +
                '<button class="btn btn-sm btn-outline-danger" onclick="eliminarMesa(' + mesa.id + ')"><i class="fas fa-trash"></i></button>' +
            '</td>';
        tbody.appendChild(tr);
    }
    if (total) total.textContent = 'Total: ' + filtrados.length + ' mesas (filtrados)';
}

// ============================================
// CLIENTES - CRUD
// ============================================
function carregarClientes() {
    var tbody = document.getElementById('tabelaClientes');
    var total = document.getElementById('totalClientes');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    for (var i = 0; i < clientes.length; i++) {
        var cli = clientes[i];
        var tr = document.createElement('tr');
        tr.innerHTML = 
            '<td>' + (i + 1) + '</td>' +
            '<td><i class="fas fa-user-circle" style="color: #c9a84c;"></i> ' + cli.nome + '</td>' +
            '<td>' + (cli.email || '-') + '</td>' +
            '<td>' + cli.telefone + '</td>' +
            '<td>' + (cli.nif || '-') + '</td>' +
            '<td><span class="badge bg-info">' + cli.totalPedidos + '</span></td>' +
            '<td class="text-center">' +
                '<button class="btn btn-sm btn-outline-success me-1" onclick="editarCliente(' + cli.id + ')"><i class="fas fa-edit"></i></button>' +
                '<button class="btn btn-sm btn-outline-danger" onclick="eliminarCliente(' + cli.id + ')"><i class="fas fa-trash"></i></button>' +
            '</td>';
        tbody.appendChild(tr);
    }
    if (total) total.textContent = 'Total: ' + clientes.length + ' clientes';
}

function abrirModalCliente() {
    document.getElementById('clienteId').value = '';
    document.getElementById('modalClienteTitulo').innerHTML = '<i class="fas fa-user-plus me-2"></i> Novo Cliente';
    document.getElementById('btnSalvarCliente').textContent = 'Salvar';
    document.getElementById('formCliente').reset();
    var modal = new bootstrap.Modal(document.getElementById('modalCliente'));
    modal.show();
}

function editarCliente(id) {
    var cli = null;
    for (var i = 0; i < clientes.length; i++) {
        if (clientes[i].id === id) {
            cli = clientes[i];
            break;
        }
    }
    if (!cli) return;
    
    document.getElementById('clienteId').value = cli.id;
    document.getElementById('modalClienteTitulo').innerHTML = '<i class="fas fa-edit me-2"></i> Editar Cliente';
    document.getElementById('btnSalvarCliente').textContent = 'Atualizar';
    document.getElementById('nomeCliente').value = cli.nome;
    document.getElementById('emailCliente').value = cli.email;
    document.getElementById('telefoneCliente').value = cli.telefone;
    document.getElementById('nifCliente').value = cli.nif;
    document.getElementById('enderecoCliente').value = cli.endereco || '';
    var modal = new bootstrap.Modal(document.getElementById('modalCliente'));
    modal.show();
}

function salvarCliente() {
    var id = document.getElementById('clienteId').value;
    var nome = document.getElementById('nomeCliente').value.trim();
    var email = document.getElementById('emailCliente').value.trim();
    var telefone = document.getElementById('telefoneCliente').value.trim();
    var nif = document.getElementById('nifCliente').value.trim();
    var endereco = document.getElementById('enderecoCliente').value.trim();
    
    if (!nome || !telefone) {
        alert('Preencha nome e telefone!');
        return;
    }
    
    if (id) {
        for (var i = 0; i < clientes.length; i++) {
            if (clientes[i].id === parseInt(id)) {
                clientes[i].nome = nome;
                clientes[i].email = email;
                clientes[i].telefone = telefone;
                clientes[i].nif = nif;
                clientes[i].endereco = endereco;
                break;
            }
        }
    } else {
        clientes.push({
            id: proximoIdCliente++,
            nome: nome,
            email: email,
            telefone: telefone,
            nif: nif,
            endereco: endereco,
            totalPedidos: 0
        });
    }
    
    var modal = bootstrap.Modal.getInstance(document.getElementById('modalCliente'));
    modal.hide();
    carregarClientes();
    alert(id ? 'Cliente atualizado com sucesso!' : 'Cliente criado com sucesso!');
}

function eliminarCliente(id) {
    if (confirm('Tem certeza que deseja eliminar este cliente?')) {
        var novoArray = [];
        for (var i = 0; i < clientes.length; i++) {
            if (clientes[i].id !== id) {
                novoArray.push(clientes[i]);
            }
        }
        clientes = novoArray;
        carregarClientes();
        alert('Cliente eliminado com sucesso!');
    }
}

function filtrarClientes() {
    var termo = document.getElementById('pesquisaCliente').value.toLowerCase();
    var tbody = document.getElementById('tabelaClientes');
    var total = document.getElementById('totalClientes');
    if (!tbody) return;
    
    var filtrados = [];
    for (var i = 0; i < clientes.length; i++) {
        if (clientes[i].nome.toLowerCase().includes(termo) || (clientes[i].email && clientes[i].email.toLowerCase().includes(termo))) {
            filtrados.push(clientes[i]);
        }
    }
    
    tbody.innerHTML = '';
    for (var i = 0; i < filtrados.length; i++) {
        var cli = filtrados[i];
        var tr = document.createElement('tr');
        tr.innerHTML = 
            '<td>' + (i + 1) + '</td>' +
            '<td>' + cli.nome + '</td>' +
            '<td>' + (cli.email || '-') + '</td>' +
            '<td>' + cli.telefone + '</td>' +
            '<td>' + (cli.nif || '-') + '</td>' +
            '<td><span class="badge bg-info">' + cli.totalPedidos + '</span></td>' +
            '<td class="text-center">' +
                '<button class="btn btn-sm btn-outline-success me-1" onclick="editarCliente(' + cli.id + ')"><i class="fas fa-edit"></i></button>' +
                '<button class="btn btn-sm btn-outline-danger" onclick="eliminarCliente(' + cli.id + ')"><i class="fas fa-trash"></i></button>' +
            '</td>';
        tbody.appendChild(tr);
    }
    if (total) total.textContent = 'Total: ' + filtrados.length + ' clientes (filtrados)';
}

// ============================================
// PEDIDOS - CRUD
// ============================================
function carregarPedidos() {
    var tbody = document.getElementById('tabelaPedidos');
    var total = document.getElementById('totalPedidos');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    for (var i = 0; i < pedidos.length; i++) {
        var ped = pedidos[i];
        var statusClass = ped.status === 'Entregue' ? 'success' : ped.status === 'Em Preparacao' ? 'warning' : ped.status === 'Pronto' ? 'info' : ped.status === 'Cancelado' ? 'danger' : 'secondary';
        var tr = document.createElement('tr');
        tr.innerHTML = 
            '<td>#' + ped.id + '</td>' +
            '<td>' + ped.cliente + '</td>' +
            '<td>' + ped.mesa + '</td>' +
            '<td><strong>Kz ' + ped.total.toFixed(2) + '</strong></td>' +
            '<td><span class="badge bg-' + statusClass + '">' + ped.status + '</span></td>' +
            '<td>' + ped.data + '</td>' +
            '<td class="text-center">' +
                '<button class="btn btn-sm btn-outline-info me-1" onclick="verPedido(' + ped.id + ')"><i class="fas fa-eye"></i></button>' +
                '<button class="btn btn-sm btn-outline-success me-1" onclick="editarPedido(' + ped.id + ')"><i class="fas fa-edit"></i></button>' +
                '<button class="btn btn-sm btn-outline-danger" onclick="eliminarPedido(' + ped.id + ')"><i class="fas fa-trash"></i></button>' +
            '</td>';
        tbody.appendChild(tr);
    }
    if (total) total.textContent = 'Total: ' + pedidos.length + ' pedidos';
}

function abrirModalPedido() {
    document.getElementById('pedidoId').value = '';
    document.getElementById('modalPedidoTitulo').innerHTML = '<i class="fas fa-clipboard-list me-2"></i> Novo Pedido';
    document.getElementById('btnSalvarPedido').textContent = 'Salvar';
    document.getElementById('formPedido').reset();
    var modal = new bootstrap.Modal(document.getElementById('modalPedido'));
    modal.show();
}

function editarPedido(id) {
    var ped = null;
    for (var i = 0; i < pedidos.length; i++) {
        if (pedidos[i].id === id) {
            ped = pedidos[i];
            break;
        }
    }
    if (!ped) return;
    
    document.getElementById('pedidoId').value = ped.id;
    document.getElementById('modalPedidoTitulo').innerHTML = '<i class="fas fa-edit me-2"></i> Editar Pedido';
    document.getElementById('btnSalvarPedido').textContent = 'Atualizar';
    document.getElementById('clientePedido').value = ped.cliente;
    document.getElementById('mesaPedido').value = ped.mesa;
    document.getElementById('statusPedido').value = ped.status;
    document.getElementById('totalPedido').value = ped.total.toFixed(2);
    var modal = new bootstrap.Modal(document.getElementById('modalPedido'));
    modal.show();
}

function salvarPedido() {
    var id = document.getElementById('pedidoId').value;
    var cliente = document.getElementById('clientePedido').value;
    var mesa = document.getElementById('mesaPedido').value;
    var status = document.getElementById('statusPedido').value;
    var total = parseFloat(document.getElementById('totalPedido').value) || 0;
    
    if (!cliente || !mesa) {
        alert('Selecione cliente e mesa!');
        return;
    }
    
    var agora = new Date().toLocaleString('pt-PT', {
        year: 'numeric', month: '2-digit', day: '2-digit',
        hour: '2-digit', minute: '2-digit'
    }).replace(/\//g, '-');
    
    if (id) {
        for (var i = 0; i < pedidos.length; i++) {
            if (pedidos[i].id === parseInt(id)) {
                pedidos[i].cliente = cliente;
                pedidos[i].mesa = mesa;
                pedidos[i].status = status;
                pedidos[i].total = total;
                break;
            }
        }
    } else {
        pedidos.push({
            id: proximoIdPedido++,
            cliente: cliente,
            mesa: mesa,
            total: total,
            status: status,
            data: agora
        });
    }
    
    var modal = bootstrap.Modal.getInstance(document.getElementById('modalPedido'));
    modal.hide();
    carregarPedidos();
    alert(id ? 'Pedido atualizado com sucesso!' : 'Pedido criado com sucesso!');
}

function verPedido(id) {
    var ped = null;
    for (var i = 0; i < pedidos.length; i++) {
        if (pedidos[i].id === id) {
            ped = pedidos[i];
            break;
        }
    }
    if (!ped) return;
    
    var detalhes = document.getElementById('detalhesPedido');
    if (detalhes) {
        detalhes.innerHTML = 
            '<div class="row">' +
                '<div class="col-md-6">' +
                    '<p><strong>Cliente:</strong> ' + ped.cliente + '</p>' +
                    '<p><strong>Mesa:</strong> ' + ped.mesa + '</p>' +
                    '<p><strong>Data:</strong> ' + ped.data + '</p>' +
                '</div>' +
                '<div class="col-md-6">' +
                    '<p><strong>Total:</strong> Kz ' + ped.total.toFixed(2) + '</p>' +
                    '<p><strong>Status:</strong> <span class="badge bg-success">' + ped.status + '</span></p>' +
                '</div>' +
            '</div>' +
            '<hr>' +
            '<h6 class="fw-semibold">Itens do Pedido</h6>' +
            '<ul class="list-unstyled">' +
                '<li class="d-flex justify-content-between border-bottom py-2"><span>Bife a Casa</span><span>1 x Kz 2.500,00</span></li>' +
                '<li class="d-flex justify-content-between py-2"><span>Refrigerante</span><span>2 x Kz 300,00</span></li>' +
            '</ul>';
    }
    
    var modal = new bootstrap.Modal(document.getElementById('modalVerPedido'));
    modal.show();
}

function eliminarPedido(id) {
    if (confirm('Tem certeza que deseja eliminar este pedido?')) {
        var novoArray = [];
        for (var i = 0; i < pedidos.length; i++) {
            if (pedidos[i].id !== id) {
                novoArray.push(pedidos[i]);
            }
        }
        pedidos = novoArray;
        carregarPedidos();
        alert('Pedido eliminado com sucesso!');
    }
}

function filtrarPedidos() {
    var termo = document.getElementById('pesquisaPedido').value.toLowerCase();
    var tbody = document.getElementById('tabelaPedidos');
    var total = document.getElementById('totalPedidos');
    if (!tbody) return;
    
    var filtrados = [];
    for (var i = 0; i < pedidos.length; i++) {
        if (pedidos[i].cliente.toLowerCase().includes(termo) || pedidos[i].mesa.toLowerCase().includes(termo) || pedidos[i].id.toString().includes(termo)) {
            filtrados.push(pedidos[i]);
        }
    }
    
    tbody.innerHTML = '';
    for (var i = 0; i < filtrados.length; i++) {
        var ped = filtrados[i];
        var statusClass = ped.status === 'Entregue' ? 'success' : ped.status === 'Em Preparacao' ? 'warning' : ped.status === 'Pronto' ? 'info' : ped.status === 'Cancelado' ? 'danger' : 'secondary';
        var tr = document.createElement('tr');
        tr.innerHTML = 
            '<td>#' + ped.id + '</td>' +
            '<td>' + ped.cliente + '</td>' +
            '<td>' + ped.mesa + '</td>' +
            '<td><strong>Kz ' + ped.total.toFixed(2) + '</strong></td>' +
            '<td><span class="badge bg-' + statusClass + '">' + ped.status + '</span></td>' +
            '<td>' + ped.data + '</td>' +
            '<td class="text-center">' +
                '<button class="btn btn-sm btn-outline-info me-1" onclick="verPedido(' + ped.id + ')"><i class="fas fa-eye"></i></button>' +
                '<button class="btn btn-sm btn-outline-success me-1" onclick="editarPedido(' + ped.id + ')"><i class="fas fa-edit"></i></button>' +
                '<button class="btn btn-sm btn-outline-danger" onclick="eliminarPedido(' + ped.id + ')"><i class="fas fa-trash"></i></button>' +
            '</td>';
        tbody.appendChild(tr);
    }
    if (total) total.textContent = 'Total: ' + filtrados.length + ' pedidos (filtrados)';
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
        case 'mesas.php':
            if (typeof carregarMesas === 'function') carregarMesas();
            break;
        case 'clientes.php':
            if (typeof carregarClientes === 'function') carregarClientes();
            break;
        case 'pedidos.php':
            if (typeof carregarPedidos === 'function') carregarPedidos();
            break;
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