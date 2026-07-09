# Autenticação por certificado digital (mTLS) na área administrativa

## O que é isto

Requisito de segurança pedido pelo professor: a área administrativa (`/views/admin/`) passa a exigir, além da password normal, um **certificado digital** instalado no browser de quem entra. É o mesmo mecanismo do exercício "Tutorial de Segurança-Certificados", aplicado agora ao Sabor Alma.

O site público (cardápio, pedidos e reservas do cliente) **não muda nada**, continua a funcionar só com password, como sempre funcionou.

## Como funciona

1. Existe uma Autoridade Certificadora (CA) própria, que assina o certificado do servidor e os certificados de cada pessoa autorizada a entrar na área administrativa.
2. O Apache (`httpd-ssl.conf`) está configurado para, dentro de `/views/admin/`, **exigir** um certificado válido assinado por essa CA antes de deixar a ligação passar. Sem certificado nenhum, o Apache já bloqueia com erro 403, nem chega a correr o PHP.
3. Do lado do PHP (`config/Sessao.php`), há uma verificação extra: confirma que o **nome no certificado** apresentado é mesmo o nome da conta que fez login com password. Isto impede que um certificado válido, mas de outra pessoa, sirva para entrar na conta de alguém.
4. Esta camada só entra em ação quando o acesso é feito por HTTPS (`https://saboralma.local`). Por HTTP normal (o que já usamos no dia a dia, `php -S` ou XAMPP sem SSL), continua tudo exatamente como estava, sem certificado nenhum. Isto foi de propósito, para não travar o trabalho do dia a dia nem depender de todos terem esta configuração feita.

## Ficheiros neste diretório

- `ca.crt` — certificado público da CA (não é secreto, pode andar à vontade).
- `httpd-vhost-saboralma.conf` — o bloco de configuração exato a colar no Apache de quem quiser reproduzir isto (tem instruções passo a passo em comentário).
- `administrador.pfx` — **NÃO vai para o Git** (está no `.gitignore`, tem uma chave privada). Fica só neste computador. Password para importar: `saboralma123`.

## Como testar / demonstrar (para o vídeo)

1. Importar o `administrador.pfx` no browser (Chrome/Edge no Windows usam o Gestor de Certificados do Windows: `certmgr.msc` → Pessoal → Certificados → Importar; ou Firefox tem o gestor de certificados próprio nas definições).
2. Abrir `https://saboralma.local/views/admin/dashboard.php`.
3. O browser vai pedir para escolher qual certificado apresentar, escolher "Administrador".
4. Fazer login normal com `admin@saboralma.ao` / `admin123`.
5. Para mostrar o bloqueio: tentar o mesmo endereço sem certificado nenhum instalado (ou noutro browser/perfil sem o certificado), dá erro 403 do próprio Apache, antes mesmo da página de login aparecer.

## Se a Clofia ou o professor quiserem reproduzir isto

Precisam de:
1. O `ca.crt` (está aqui).
2. Um certificado `.pfx` novo, gerado com a chave privada da CA (o José tem essa chave, pode gerar mais certificados para outras contas se for preciso).
3. Seguir os passos comentados no `httpd-vhost-saboralma.conf`.

Se não quiserem montar isto tudo, não há problema: o vídeo de demonstração mostra o mecanismo a funcionar neste computador, e o código relevante (`config/Sessao.php`, método `exigirCertificadoSeForAreaAdministrativa`) está no repositório para o professor ver mesmo sem precisar de reproduzir o ambiente.
