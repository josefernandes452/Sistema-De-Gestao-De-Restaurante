# 🍽️ Sistema de Gestão de Restaurante

> Projeto académico da disciplina **PHP Completo — Fundamentos, MySQL e PHP Orientado a Objetos** (ISPTEC).

## 📋 Sobre o projeto

Sistema web de gestão de restaurante desenvolvido em **PHP 8** com arquitetura **MVC/POO** e **MySQL (PDO)**. Permite gerir mesas, clientes, cardápio, pedidos e pagamentos, com autenticação por perfis, relatórios e segurança aplicada (prepared statements, hashing de senhas, proteção CSRF/XSS).

## 👥 Equipa

- Clofia Loureiro
- José Fernandes 

## 🛠️ Tecnologias

- PHP 8
- MySQL + PDO
- HTML5 · CSS3 · JavaScript
- Arquitetura MVC

## ⚠️ Depois de fazeres git pull

O backend já liga a base de dados a sério e manda emails de verdade (recuperação de senha), por isso há passos manuais que o Git não faz sozinho. Sempre que fizeres `git pull` e aparecerem novidades no backend, confirma isto:

1. **Instalar as dependências do Composer.** O projeto passou a usar o PHPMailer para enviar emails. Corre `composer install` na pasta do projeto (ou `php composer.phar install`, se não tiveres o Composer instalado globalmente). Sem isto o site dá erro a carregar o `vendor/autoload.php`.
2. **Criar o teu `config/config.php`.** Este ficheiro não vai para o GitHub (tem dados sensíveis: senha da base de dados e senha do email). Copia o `config/config.example.php`, renomeia para `config.php`, e preenche com os teus próprios dados: a tua ligação MySQL local e, se quiseres testar a recuperação de senha, uma conta Gmail com "palavra-passe de aplicação" (gera-se em myaccount.google.com/apppasswords).
3. **Importar a base de dados.** Corre `database/schema.sql` e depois `database/seed.sql` no teu MySQL/phpMyAdmin, se ainda não tiveres as tabelas criadas.

Sem estes 3 passos o site carrega mas nada que dependa da base de dados ou de email vai funcionar.

## 📁 Estrutura do projeto

```
config/
controllers/
models/
views/
assets/
  css/
  js/
  img/
  uploads/
database/
index.php
```

## 🗄️ Modelo de dados

| Tabela | Descrição |
|---|---|
| perfis | Tipos de utilizador (Administrador, Operador, Cliente) |
| utilizadores | Contas do sistema |
| categorias | Categorias do cardápio |
| produtos | Itens do cardápio |
| mesas | Mesas do restaurante e seu estado |
| clientes | Dados dos clientes |
| pedidos | Pedidos feitos por mesa/cliente |
| itens_pedido | Itens de cada pedido |
| pagamentos | Pagamentos associados a pedidos |
| logs | Registo de ações sensíveis |
| reservas *(bónus)* | Reservas de mesa |
| notificacoes *(bónus)* | Avisos para Administrador/Operador (novo pedido, nova reserva) |

## 🌐 API pública utilizada

**Exchange Rate API** — conversão Kwanza ↔ USD/EUR (exibida no dashboard / cardápio).

## ✅ Testes automatizados

O projeto usa PHPUnit. Depois de correres `composer install`, corre:

```
php vendor/bin/phpunit
```

Os testes cobrem o `Validador` (validação central usada em todos os formulários) e o bloqueio de conflito de reservas (`ReservaModel::existeConflito`). Este último usa a base de dados real, mas está protegido dentro de uma transação que é sempre desfeita no fim, então não deixa nenhum dado de teste na tua base.

---

## 🗺️ Roadmap do Projeto

**Prazo de entrega:** 09/07/2026, 20h00

### 🔧 Backend — passo a passo

**Dia 1 (29/06) — Modelagem**
- [ ] Diagrama ER (entidades e relações)
- [ ] Estrutura de pastas MVC
- [ ] Convenções de nomenclatura

**Dia 2 (30/06) — Base de Dados**
- [ ] Script SQL completo (`database/schema.sql`) com PK/FK
- [ ] Dados de seed (categorias, produtos, utilizador admin)
- [ ] Classe `Database` (PDO, Singleton)

**Dia 3 (01/07) — Autenticação**
- [ ] `UsuarioModel` + `AuthController` (login/logout)
- [ ] `password_hash()` / `password_verify()`
- [ ] Sessões PHP + cookie "lembrar-me"
- [ ] Controlo de acesso por perfil
- [ ] Recuperação de senha simulada
- [ ] CRUD de Utilizadores (gestão por Admin)

**Dia 4 (02/07) — Núcleo MVC**
- [ ] Classe abstrata `Model` (prepared statements genéricos)
- [ ] Classe base `Controller`
- [ ] Router simples (`index.php`)
- [ ] `Validator` / `Sanitizer` central
- [ ] Proteção CSRF

**Dia 5 (03/07) — Cardápio**
- [ ] `CategoriaModel`/`Controller` (CRUD)
- [ ] `ProdutoModel`/`Controller` (CRUD)
- [ ] Upload de imagem do produto

**Dia 6 (04/07) — Mesas & Clientes**
- [ ] `MesaModel`/`Controller` (CRUD, estado livre/ocupada/reservada)
- [ ] `ClienteModel`/`Controller` (CRUD)

**Dia 7 (05/07) — Pedidos (parte 1)**
- [ ] `PedidoModel` + `ItemPedidoModel`
- [ ] Cálculo de subtotal/total
- [ ] Transação SQL (`BEGIN`/`COMMIT`)

**Dia 8 (06/07) — Pedidos (parte 2) & Pagamentos**
- [ ] Transições de estado do pedido
- [ ] `PagamentoModel`/`Controller`
- [ ] Registo em `logs`
- [ ] Queries agregadas para o dashboard

**Dia 9 (07/07) — Pesquisa, Relatórios & API**
- [ ] Pesquisa por nome/código/data/categoria
- [ ] 3 relatórios (produtos mais vendidos, vendas por período, desempenho por operador)
- [ ] Integração com a Exchange Rate API

**Dia 10 (08/07) — Segurança**
- [ ] Revisão XSS (escapar saída) e SQL Injection (100% PDO)
- [ ] Revisão CSRF e hardening de sessão
- [ ] Testes end-to-end

---

### 🎨 Frontend — passo a passo

**Dia 1 (29/06) — Wireframes**
- [ ] Wireframes das telas principais (login, dashboard, mesas, novo pedido, cardápio, relatórios)
- [ ] Paleta de cores e tipografia

**Dia 2 (30/06) — Layout base**
- [ ] Header, menu de navegação, footer (responsivo)
- [ ] Sistema de mensagens flash (sucesso/erro)

**Dia 3 (01/07) — Telas de autenticação**
- [ ] Páginas de login/logout
- [ ] Página "esqueci a senha" + alteração de senha
- [ ] Página de perfil do utilizador
- [ ] Validação client-side (JS)

**Dia 4 (02/07) — Templates reutilizáveis**
- [ ] Templates de view (head, nav, footer, formulário padrão)
- [ ] Ícones e padrão visual de botões

**Dia 5 (03/07) — Telas de Cardápio**
- [ ] Listagem de categorias/produtos
- [ ] Formulário com pré-visualização da imagem
- [ ] Filtro de pesquisa por nome/categoria

**Dia 6 (04/07) — Mesas & Clientes**
- [ ] Painel visual de mesas (grid colorido por estado)
- [ ] Formulário de clientes

**Dia 7 (05/07) — Tela de Novo Pedido**
- [ ] Seleção de mesa/cliente, carrinho de produtos, observações

**Dia 8 (06/07) — Acompanhamento & Dashboard**
- [ ] Tela de acompanhamento de pedidos por estado
- [ ] Tela de fecho de conta/pagamento
- [ ] Dashboard com indicadores

**Dia 9 (07/07) — Relatórios**
- [ ] Telas de relatório com filtros de data
- [ ] Exibição da taxa de câmbio

**Dia 10 (08/07) — Polimento final**
- [ ] Testes responsivos (mobile/tablet/desktop)
- [ ] Revisão de mensagens e acessibilidade básica

---

### 📦 Entrega final — Dia 11 (09/07, manhã)
- [ ] Documento técnico (5–10 páginas)
- [ ] Vídeo de demonstração (5–10 min)
- [ ] Apresentação PowerPoint (10–15 diapositivos)
- [ ] Certificados Cursa de ambos os elementos
- [ ] Exportação final do `.sql`
- [ ] Pasta zipada submetida via classroom antes das 20h00

---

## ✅ Requisitos cumpridos

- [ ] Sistema de Login
- [ ] Base de Dados (≥8 tabelas, PK/FK)
- [ ] CRUD completo (≥5 entidades)
- [ ] Pesquisa (nome/código/data/categoria)
- [ ] Relatórios (≥3)
- [ ] Upload de ficheiros
- [ ] Programação Orientada a Objetos
- [ ] PDO / Prepared Statements
- [ ] Segurança (SQL Injection, XSS, sanitização, password_hash)
- [ ] Sessões e Cookies
- [ ] Consumo de API pública
- [ ] Interface responsiva

## 📄 Licença

Projeto académico — uso educacional.
