# Certificado digital, versão corrigida (substitui o que já tinhas feito)

Testei o que fizeste e tem um problema: `SSLVerifyClient optional` no `httpd-ssl.conf` significa que o certificado nunca é **obrigatório**, o Apache deixa entrar com ou sem ele. Isto não cumpre o requisito do professor ("é obrigatório"). Corrigi isso aqui, já testado a bloquear a sério, e gerei um certificado só teu (`clofia.pfx`), com o teu nome, ligado à tua conta de Operador.

## Passo 1 — Apagar a tua configuração antiga

No `httpd-ssl.conf`, remove o bloco `<VirtualHost *:443>` que tinhas adicionado (o que tem `SSLVerifyClient optional` e `DocumentRoot "C:/xampp/htdocs"`).

## Passo 2 — Criar a ligação para o projeto dentro do htdocs

Isto evita problemas do Apache com os caracteres especiais do caminho da tua pasta (o "º", "Ç", "Ã"). Abre o PowerShell **como Administrador** e corre (ajusta o caminho para o teu):

```powershell
New-Item -ItemType Junction -Path "C:\xampp\htdocs\saboralma" -Target "CAMINHO_COMPLETO_DA_TUA_PASTA_DO_PROJETO"
```

## Passo 3 — Substituir os ficheiros da CA

Apaga os ficheiros antigos de `C:/xampp/apache/conf/ssl/` (o `ca.crt`, `cliente.p12`, `servidor.crt`, `servidor.key` que tinhas feito) e coloca lá os novos que te enviei:

- `ca.crt`
- `saboralma-servidor.crt`
- `saboralma-servidor.key`

## Passo 4 — Colar a configuração nova

Copia o conteúdo do ficheiro `httpd-vhost-saboralma.conf` (também te enviei) para o final do `httpd-ssl.conf`. Este bloco já:
- Deixa o site público (menu, pedidos do cliente) sem certificado nenhum.
- Exige o certificado **só** dentro de `/views/admin/`.
- Já tem a correção do TLS 1.2 (sem ela, dá um erro "Cannot perform Post-Handshake Authentication" quando o certificado é exigido só numa pasta).

## Passo 5 — Ficheiro hosts (igual ao que já tinhas feito)

```
127.0.0.1 saboralma.local
```

## Passo 6 — Reiniciar o Apache

XAMPP Control Panel → Stop → Start no Apache.

## Passo 7 — Instalar o teu certificado

Clica duas vezes em `clofia.pfx` → Avançar → senha: `saboralma123` → "Pessoal" → Concluir.

(Não precisas de instalar o `ca.crt` manualmente como certificado raiz, isso só é preciso se quiseres tirar o aviso de "ligação não segura" do browser. Para a demonstração, o aviso não é problema, é normal com um certificado autoassinado.)

## Passo 8 — Testar

1. `https://saboralma.local/views/cliente/menu.php` → deve abrir sem pedir certificado nenhum.
2. `https://saboralma.local/views/admin/dashboard.php` sem teres importado o certificado → deve dar erro 403 do Apache.
3. Com o certificado importado, o browser pergunta qual certificado apresentar, escolhe "Clofia Loureiro".
4. Faz login normal com a tua conta de Operador (`clofia@gmail.com`).
5. Se entrares com a password certa mas escolheres um certificado de outra pessoa (o `administrador.pfx`, por exemplo), o sistema bloqueia mesmo assim, porque o nome no certificado tem de bater com o nome da conta que fez login. Essa verificação está no código, em `config/Sessao.php`.
