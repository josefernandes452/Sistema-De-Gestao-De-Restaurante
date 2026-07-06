-- Sabor Alma - dados iniciais para o sistema arrancar com algo dentro
-- Correr depois do schema.sql

USE sabor_alma;

INSERT INTO perfis (nome) VALUES
    ('Administrador'),
    ('Operador'),
    ('Cliente');

-- senha: admin123 (hash gerado com password_hash, PASSWORD_DEFAULT)
INSERT INTO utilizadores (perfil_id, nome, email, senha, telefone, estado) VALUES
    (1, 'Administrador', 'admin@saboralma.ao', '$2y$10$G4PpEYe.CLmr84Xrc4EO9u99l93/tYK2amDHovL0yZrzvVqNmMomu', '900000000', 'Ativo');

INSERT INTO categorias (nome, descricao) VALUES
    ('Entradas', 'Aperitivos e entradas'),
    ('Pratos Principais', 'Pratos principais do menu'),
    ('Bebidas', 'Refrigerantes, sucos e outras bebidas'),
    ('Sobremesas', 'Doces e sobremesas');

INSERT INTO produtos (categoria_id, nome, descricao, preco, estoque) VALUES
    ((SELECT id FROM categorias WHERE nome = 'Pratos Principais'), 'Bife a Casa', 'Bife com batata frita e ovo', 2500.00, 15),
    ((SELECT id FROM categorias WHERE nome = 'Pratos Principais'), 'Frango Grelhado', 'Frango com legumes grelhados', 1800.00, 10),
    ((SELECT id FROM categorias WHERE nome = 'Entradas'), 'Salada Mista', 'Salada com alface, tomate e cebola', 800.00, 20),
    ((SELECT id FROM categorias WHERE nome = 'Entradas'), 'Sopa do Dia', 'Sopa caseira', 600.00, 20),
    ((SELECT id FROM categorias WHERE nome = 'Bebidas'), 'Refrigerante', 'Lata 330ml', 300.00, 50),
    ((SELECT id FROM categorias WHERE nome = 'Bebidas'), 'Suco Natural', 'Suco de fruta natural', 450.00, 30);

INSERT INTO mesas (numero, capacidade, localizacao) VALUES
    (1, 2, 'Salao Principal'),
    (2, 4, 'Salao Principal'),
    (3, 4, 'Salao Principal'),
    (4, 6, 'Varanda'),
    (5, 2, 'Varanda'),
    (6, 8, 'Salao Principal');
