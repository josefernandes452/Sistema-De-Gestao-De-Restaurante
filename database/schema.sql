-- Sabor Alma - Sistema de Gestao de Restaurante
-- Estrutura da base de dados (ver database/diagrama-er.md para o diagrama)

CREATE DATABASE IF NOT EXISTS sabor_alma CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sabor_alma;

-- ---------------------------------------------------------------
-- PERFIS
-- Tipos de utilizador. Fixo: Administrador, Operador, Cliente.
-- ---------------------------------------------------------------
CREATE TABLE perfis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(30) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- UTILIZADORES
-- Contas do sistema (admin, operadores e clientes com login).
-- ---------------------------------------------------------------
CREATE TABLE utilizadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    perfil_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    estado ENUM('Ativo', 'Inativo') NOT NULL DEFAULT 'Ativo',
    token_recuperacao VARCHAR(100),
    token_recuperacao_expira DATETIME,
    lembrar_selector VARCHAR(20),
    lembrar_validador_hash VARCHAR(64),
    lembrar_expira DATETIME,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (perfil_id) REFERENCES perfis(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- CATEGORIAS
-- ---------------------------------------------------------------
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(60) NOT NULL,
    descricao VARCHAR(255),
    estado ENUM('Ativo', 'Inativo') NOT NULL DEFAULT 'Ativo',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- PRODUTOS
-- ---------------------------------------------------------------
CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10, 2) NOT NULL,
    estoque INT NOT NULL DEFAULT 0,
    imagem VARCHAR(255),
    estado ENUM('Disponivel', 'Esgotado', 'Indisponivel') NOT NULL DEFAULT 'Disponivel',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- MESAS
-- ---------------------------------------------------------------
CREATE TABLE mesas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero INT NOT NULL UNIQUE,
    capacidade INT NOT NULL,
    localizacao VARCHAR(60),
    estado ENUM('Livre', 'Ocupada', 'Reservada') NOT NULL DEFAULT 'Livre'
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- CLIENTES
-- ---------------------------------------------------------------
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT UNIQUE,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150),
    telefone VARCHAR(20) NOT NULL,
    nif VARCHAR(20),
    endereco VARCHAR(255),
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- PEDIDOS
-- cliente_id e utilizador_id podem ser nulos: nem todo cliente tem
-- conta, e nem sempre interessa guardar quem registou o pedido.
-- ---------------------------------------------------------------
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mesa_id INT NOT NULL,
    cliente_id INT,
    utilizador_id INT,
    estado ENUM('Pendente', 'Em Preparacao', 'Pronto', 'Entregue', 'Cancelado') NOT NULL DEFAULT 'Pendente',
    observacoes TEXT,
    total DECIMAL(10, 2) NOT NULL DEFAULT 0,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mesa_id) REFERENCES mesas(id) ON DELETE RESTRICT,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- ITENS_PEDIDO
-- preco_unitario fica guardado aqui porque o preco do produto
-- pode mudar depois, e o pedido antigo tem de manter o valor certo.
-- ---------------------------------------------------------------
CREATE TABLE itens_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL DEFAULT 1,
    preco_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- PAGAMENTOS
-- Um pedido pode ter mais de um pagamento (ex: parte em dinheiro,
-- parte no cartao), por isso a chave estrangeira fica aqui.
-- ---------------------------------------------------------------
CREATE TABLE pagamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    valor DECIMAL(10, 2) NOT NULL,
    metodo VARCHAR(50) NOT NULL,
    estado ENUM('Pago', 'Pendente', 'Cancelado') NOT NULL DEFAULT 'Pendente',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- LOGS
-- Registo de acoes sensiveis (login, edicoes, eliminacoes).
-- ---------------------------------------------------------------
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT,
    acao VARCHAR(100) NOT NULL,
    detalhes TEXT,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- RESERVAS (bonus)
-- cliente_id fica opcional porque a reserva pode vir de alguem
-- sem conta no sistema (so preenche nome e telefone).
-- ---------------------------------------------------------------
CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mesa_id INT NOT NULL,
    cliente_id INT,
    nome VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    data DATE NOT NULL,
    hora TIME NOT NULL,
    pessoas INT NOT NULL,
    estado ENUM('Confirmada', 'Cancelada', 'Concluida') NOT NULL DEFAULT 'Confirmada',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mesa_id) REFERENCES mesas(id) ON DELETE CASCADE,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- NOTIFICACOES (bonus)
-- Avisos para Administrador/Operador quando algo acontece do lado
-- do cliente (novo pedido, nova reserva), para nao terem de andar a
-- verificar as listas manualmente.
-- ---------------------------------------------------------------
CREATE TABLE notificacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT NOT NULL,
    mensagem VARCHAR(255) NOT NULL,
    link VARCHAR(255),
    lida TINYINT(1) NOT NULL DEFAULT 0,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE CASCADE
) ENGINE=InnoDB;
