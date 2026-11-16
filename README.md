# AstroData — ambiente local com SQLite

Versão de desenvolvimento local do AstroData. O site identifica o signo zodiacal a partir da data de nascimento e apresenta informações detalhadas. Esta branch utiliza **SQLite via PDO**, sem exigir um servidor MySQL.

> **Importante:** esta branch é destinada ao desenvolvimento local. Para publicar no InfinityFree, use a branch `main`, que utiliza MySQL/MariaDB. Não misture os arquivos de conexão das duas branches.

## Tecnologias

- PHP 8+
- SQLite por PDO (`pdo_sqlite`)
- HTML5, CSS3, JavaScript e Bootstrap 5
- OpenSSL AES-256-GCM para criptografar o histórico administrativo

## Estrutura principal

- `index.php`: formulário de data de nascimento.
- `signo.php`: informações do signo identificado.
- `admin/consultas.php`: painel privado do histórico.
- `config/database.php`: conexão PDO SQLite.
- `config/seguranca.php`: carrega os segredos locais.
- `database/init.php`: cria as tabelas e cadastra os 12 signos.
- `tools/gerar_credenciais.php`: gera senha administrativa e chave AES localmente.
- `database/signos.sqlite`: banco criado automaticamente (não versionado).

## Requisitos

- PHP 8 ou superior.
- Extensões PHP `pdo_sqlite` e `openssl`.
- Apache/XAMPP ou servidor PHP embutido para desenvolvimento.

## Preparação inicial

1. Baixe esta branch:
   `local/sqlite-development`.
2. Confira se as extensões estão habilitadas:
   `php -m`
   e confirme que aparecem `pdo_sqlite` e `openssl`.
3. Copie `config/database.local.example.php` para `config/database.local.php`.
4. No arquivo local, mantenha o caminho padrão ou informe outro caminho gravável em `database_path`.
5. Gere um token aleatório para a inicialização web:
   `php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"`
   e coloque o resultado em `setup_token` no arquivo local.
6. Gere os segredos administrativos:
   `php tools/gerar_credenciais.php`
   O comando cria `config/seguranca.local.php` e mostra uma senha administrativa aleatória uma única vez. Guarde a senha e faça backup seguro desse arquivo: ele contém a chave para descriptografar os registros.
7. Inicialize o banco pelo terminal:
   `php database/init.php`
   Também é possível abrir `/database/init.php` no navegador e informar o token configurado.
8. Inicie o servidor e acesse a página inicial.

## Executar com PHP embutido

Na raiz do projeto, execute:

```bash
php -S localhost:8000
```

Abra http://localhost:8000.

Com XAMPP, coloque a pasta do projeto em `htdocs`, inicie o Apache e acesse a URL local correspondente.

## Banco SQLite

- O banco é criado automaticamente em `database/signos.sqlite`.
- A pasta `database/` precisa ter permissão de escrita para o processo PHP.
- O arquivo do banco é ignorado pelo Git e não deve ser enviado ao repositório.
- O arquivo `database/.htaccess` bloqueia acesso HTTP direto aos arquivos de banco.
- A inicialização preserva os dados se já houver signos cadastrados; não apaga nem sobrescreve registros existentes.
- Para um banco novo, a inicialização cadastra os 12 signos e cria a tabela de consultas.

## Área administrativa e criptografia

Acesse `/admin/consultas.php` e use a senha gerada por `tools/gerar_credenciais.php`.

O histórico é criptografado com AES-256-GCM. Guarde uma cópia segura de `config/seguranca.local.php`. **Não gere uma nova chave com `--force` depois de começar a registrar consultas**, pois isso torna os registros antigos ilegíveis.

## Segurança

- `config/database.local.php` e `config/seguranca.local.php` são ignorados pelo Git.
- O arquivo SQLite é ignorado pelo Git e bloqueado contra acesso HTTP direto pelo `.htaccess` em `database/`.
- A inicialização pelo navegador exige token secreto; prefira executar via CLI quando possível.
- Depois de inicializar pelo navegador, remova `database/init.php` do diretório público ou restrinja seu acesso.
- Não há senha administrativa padrão embutida no código.

## Testes recomendados

- Testar datas correspondentes aos 12 signos.
- Testar data inválida e data futura.
- Confirmar registro de consulta no SQLite.
- Confirmar login, logout, expiração de sessão e bloqueio temporário.
- Confirmar que `database/signos.sqlite` não pode ser baixado pelo navegador.

## Hospedagem

Esta branch é exclusivamente para desenvolvimento local com SQLite. Para o site InfinityFree, faça checkout de `main`, que contém a configuração MySQL preparada para a hospedagem.
