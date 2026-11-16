# AstroData — Descubra seu Signo

Site em PHP puro que identifica o signo zodiacal a partir da data de nascimento e exibe informações detalhadas. Esta versão utiliza **MySQL/MariaDB via PDO**, preparada para hospedagem compartilhada como o InfinityFree.

## Tecnologias

- PHP 8+
- MySQL 8 / MariaDB
- PDO com prepared statements
- HTML5, CSS3, JavaScript e Bootstrap 5
- OpenSSL AES-256-GCM para criptografar o histórico administrativo

## Estrutura

- `index.php`: formulário e processamento da data.
- `signo.php`: exibe as informações do signo.
- `admin/consultas.php`: painel privado de histórico.
- `config/database.php`: conexão PDO MySQL.
- `config/seguranca.php`: carrega os segredos locais.
- `database/init.php`: cria as tabelas e cadastra os 12 signos.
- `includes/`: regras de negócio, registro e criptografia.
- `tools/gerar_credenciais.php`: gera a senha administrativa e a chave AES localmente.

## Preparação local

1. Instale PHP com as extensões `pdo_mysql` e `openssl`.
2. Crie um banco MySQL e um usuário com permissões para criar e alterar tabelas.
3. Copie `config/database.local.example.php` para `config/database.local.php`.
4. Preencha host, porta, nome do banco, usuário e senha fornecidos pelo servidor MySQL.
5. Substitua `setup_token` por um valor aleatório longo (64 caracteres hexadecimais).
6. Na raiz do projeto, execute:

   ```bash
   php tools/gerar_credenciais.php
   ```

   O comando cria `config/seguranca.local.php` e mostra uma senha administrativa aleatória uma única vez. Guarde a senha e faça backup seguro do arquivo local: ele contém a chave necessária para descriptografar os registros. **Não execute novamente com `--force` depois de começar a registrar consultas**, pois isso gera outra chave e torna os registros antigos ilegíveis.

7. Inicialize o banco:
   - Localmente: `php database/init.php`.
   - Em hospedagem sem terminal: abra `/database/init.php`, informe o token configurado e execute a instalação.
8. Acesse `index.php` e teste datas de diferentes signos. A área administrativa fica em `/admin/consultas.php`.

A inicialização é idempotente: se os 12 signos já estiverem cadastrados, ela não apaga nem sobrescreve registros.

## Configuração no InfinityFree

1. Crie uma conta e um site no InfinityFree.
2. No painel, crie um banco MySQL e anote o host, nome, usuário e senha apresentados.
3. Envie os arquivos do projeto para a pasta pública do site.
4. Crie `config/database.local.php` no servidor copiando o modelo e preenchendo os dados do banco. Não envie esse arquivo ao GitHub.
5. Gere `config/seguranca.local.php` localmente com `php tools/gerar_credenciais.php` e envie o arquivo por FTP/gerenciador de arquivos. Guarde uma cópia segura fora do servidor.
6. Abra `https://SEU-SITE/database/init.php`, informe o token de instalação e confirme que os 12 signos foram cadastrados.
7. Teste a página inicial, a navegação para cada signo e o registro de uma consulta.
8. Teste `/admin/consultas.php` com a senha gerada.

O servidor precisa disponibilizar `pdo_mysql` e OpenSSL. Se a conexão falhar, confira o host MySQL no painel da hospedagem — ele pode não ser `localhost`.

Após inicializar o banco, remova o acesso público ao instalador: apague `database/init.php` do servidor ou renomeie-o e mantenha-o fora da pasta pública. O token é uma proteção adicional, não motivo para deixar o instalador exposto permanentemente.

## Segurança

- Credenciais MySQL e segredos administrativos ficam em arquivos locais ignorados pelo Git.
- A pasta `config/` possui regra de bloqueio HTTP.
- O instalador exige token secreto em requisições web.
- O histórico é criptografado com AES-256-GCM antes de ser gravado.
- O painel administrativo usa hash de senha, sessão com expiração, proteção CSRF e bloqueio temporário de tentativas.
- Não há senha administrativa padrão no código.

**Importante:** versões anteriores do repositório continham um hash administrativo e uma chave de criptografia embutidos. Considere-os comprometidos e use os novos segredos gerados por este projeto. Para uma instalação nova, gere os segredos antes do primeiro registro. Se já houver dados criptografados com a chave antiga, faça backup e migre-os antes de substituí-la.

## Testes recomendados

- Data válida de cada um dos 12 signos.
- Data inválida, futura e anterior a 1900.
- Registro de consulta no banco e exibição no painel administrativo.
- Paginação do histórico.
- Login incorreto, bloqueio temporário, logout e expiração da sessão.
- Acesso HTTP direto a arquivos dentro de `config/` (deve ser negado).

## Execução local

O projeto pode ser executado com Apache + PHP (por exemplo, XAMPP) e um servidor MySQL/MariaDB. SQLite não é mais utilizado.
