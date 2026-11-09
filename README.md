# AstroData – Descubra seu Signo

Projeto web completo desenvolvido para trabalho de curso.  
O usuário informa a data de nascimento e o sistema identifica automaticamente o signo zodiacal, exibindo informações detalhadas carregadas de um banco SQLite.

---

## Tecnologias utilizadas

- **HTML5**
- **CSS3** (arquivo próprio + Bootstrap 5)
- **JavaScript** (validação de formulário)
- **PHP** (sem frameworks)
- **Bootstrap 5** (via CDN)
- **SQLite** (banco de dados local)
- **PDO** com prepared statements
- **OpenSSL** (criptografia AES-256-GCM dos registros)

O projeto roda localmente com **XAMPP** (Apache + PHP). Não utiliza Node.js nem Laravel.

---

## Estrutura de pastas

```
projeto-signos/
├── index.php              # Página inicial (formulário)
├── signo.php              # Página de detalhes do signo
├── config/
│   ├── database.php       # Conexão PDO com SQLite
│   └── seguranca.php      # Hash da senha e chave AES da área restrita
├── database/
│   ├── init.php           # Cria as tabelas e insere os 12 signos
│   └── signos.sqlite      # Banco (criado automaticamente pelo init.php)
├── admin/
│   └── consultas.php      # Área restrita (somente por URL + senha)
├── tools/
│   └── gerar_credenciais.php  # Gera hash de senha / nova chave AES
├── assets/
│   ├── css/
│   │   ├── style.css      # Estilos personalizados (tema zodíaco)
│   │   └── admin.css      # Estilos da área restrita
│   ├── js/
│   │   └── script.js      # Validação e interações
│   └── img/
│       └── logo.png       # Logo do projeto
├── includes/
│   ├── header.php         # Cabeçalho HTML comum
│   ├── footer.php         # Rodapé (scripts)
│   ├── funcoes.php        # Funções de validação e identificação do signo
│   ├── criptografia.php   # Criptografia/descriptografia AES-256-GCM
│   └── consultas.php      # Registro e leitura das consultas
└── README.md              # Este arquivo
```

---

## Como instalar e executar (XAMPP)

### 1. Instalar o XAMPP

1. Acesse o site oficial: https://www.apachefriends.org/
2. Baixe a versão para o seu sistema operacional (Windows, Linux ou macOS).
3. Execute o instalador e siga as instruções (mantenha as opções padrão de Apache e PHP).
4. Após a instalação, abra o **XAMPP Control Panel**.

### 2. Colocar a pasta do projeto

1. Copie a pasta **`projeto-signos`** completa para dentro do diretório `htdocs` do XAMPP:
   - **Windows**: `C:\xampp\htdocs\`
   - **Linux**: `/opt/lampp/htdocs/` ou `/var/www/html/`
   - **macOS**: `/Applications/XAMPP/htdocs/`

2. A estrutura final deve ficar assim:
   ```
   htdocs/
   └── projeto-signos/
       ├── index.php
       ├── signo.php
       ├── config/
       ├── database/
       ├── assets/
       ├── includes/
       └── README.md
   ```

### 3. Iniciar o Apache

1. Abra o **XAMPP Control Panel**.
2. Clique em **Start** ao lado de **Apache**.
3. Aguarde o status ficar verde (Apache rodando).

> **Observação**: Não é necessário iniciar o MySQL, pois usamos SQLite (arquivo local).

### 4. Criar / inicializar o banco SQLite

1. No navegador, acesse:
   ```
   http://localhost/projeto-signos/database/init.php
   ```
2. A página deve exibir a mensagem:  
   **“Banco de dados inicializado com sucesso!”**  
   (12 signos cadastrados)
3. Se o banco já existir, a mensagem informará que já está inicializado.

O arquivo `database/signos.sqlite` será criado automaticamente.

### 5. Acessar o projeto

No navegador, abra:

```
http://localhost/projeto-signos/
```

ou

```
http://localhost/projeto-signos/index.php
```

### 6. Como testar

1. Na página inicial, escolha uma data de nascimento (ex.: 15/09/1990).
2. Clique em **“Descobrir meu signo”**.
3. Você será redirecionado para `signo.php?signo=virgem` (ou o signo correspondente).
4. Verifique se as informações (elemento, planeta, características, etc.) aparecem corretamente.
5. Clique em **“Descobrir outro signo”** para voltar ao formulário.
6. Teste validações:
   - Enviar sem data → alerta de erro.
   - Data futura → rejeitada.
   - Data inválida → rejeitada.
7. Teste URL inválida: `signo.php?signo=xyz` → página de erro amigável.

### 7. Área restrita – registro de consultas

Cada consulta válida gera um registro interno na tabela `consultas`, contendo:

- **data da consulta** (momento em que o formulário foi enviado);
- **data consultada** (data de nascimento informada);
- **signo consultado**.

Esse registro **não aparece em nenhuma página pública** e não existe link para ele no site. O acesso é feito somente pela URL:

```
http://localhost/projeto-signos/admin/consultas.php
```

**Senha padrão:** `AstroData@2026`

Medidas de proteção aplicadas:

- senha armazenada como hash em `config/seguranca.php` (`password_hash` / `password_verify`);
- todos os campos da tabela são gravados criptografados com **AES-256-GCM**; a chave fica em `config/seguranca.php`;
- sessão com expiração após 15 minutos de inatividade;
- token **CSRF** nos formulários de login e logout;
- bloqueio temporário após 5 tentativas de senha incorretas;
- cabeçalhos `noindex` / `no-store` e nenhum link no site público.

> Se o banco já existia antes desta funcionalidade, acesse novamente `database/init.php` para criar a tabela `consultas` (os signos já cadastrados são preservados).

#### Alterar a senha

Gere um novo hash:

```bash
php tools/gerar_credenciais.php "nova-senha"
```

Copie o valor retornado para `ADMIN_SENHA_HASH` em `config/seguranca.php`.

#### Gerar uma nova chave de criptografia

```bash
php tools/gerar_credenciais.php --chave
```

> **Atenção:** trocar a chave torna ilegíveis os registros já gravados, pois eles foram criptografados com a chave anterior.

### 8. Problemas comuns (SQLite / PDO)

| Problema | Solução |
|----------|---------|
| Erro “could not find driver” | No XAMPP, edite `php.ini` e descomente a linha `extension=pdo_sqlite` e `extension=sqlite3`. Reinicie o Apache. |
| Erro de permissão ao criar o `.sqlite` | Dê permissão de escrita na pasta `database/` (no Linux/macOS: `chmod 777 database` temporariamente ou ajuste o dono). |
| Página em branco / erro 500 | Ative a exibição de erros no PHP temporariamente (`display_errors = On` no `php.ini`) ou veja o log do Apache. |
| Banco não encontrado | Execute novamente `http://localhost/projeto-signos/database/init.php`. |
| Signo não aparece | Confirme que o `init.php` rodou com sucesso e que a tabela possui 12 registros. |
| Área restrita sem registros | Execute `database/init.php` novamente para criar a tabela `consultas`. |
| Erro “Chave de criptografia inválida” | Confira `APP_CHAVE_CRIPTOGRAFIA` em `config/seguranca.php`; ela deve ter 32 bytes em base64. |

---

## Arquitetura do projeto (resumo)

```
[Usuário]
    │
    ▼
index.php  ──►  valida data (funcoes.php)
    │               │
    │               ▼
    │         determinarSigno(dia, mês) → slug
    │               │
    │               ▼
    └─────────►  redireciona para signo.php?signo=slug
                      │
                      ▼
                 signo.php
                      │
                      ▼
              config/database.php → PDO SQLite
                      │
                      ▼
              buscarSignoPorSlug() → SELECT prepared
                      │
                      ▼
              Exibe dados do signo (HTML + CSS + Bootstrap)
```

- **index.php**: formulário + processamento POST + registro criptografado da consulta + redirecionamento.
- **signo.php**: recebe o slug via GET, valida, consulta o banco e renderiza a página.
- **admin/consultas.php**: área restrita, acessível apenas por URL direta, protegida por senha; descriptografa os registros somente durante a exibição.
- **config/database.php**: único ponto de conexão PDO.
- **config/seguranca.php**: hash da senha administrativa e chave AES-256-GCM.
- **database/init.php**: cria as tabelas `signos` e `consultas` e popula os 12 signos.
- **includes/funcoes.php**: lógica de validação de data e cálculo do signo (independente do banco).
- **includes/consultas.php**: grava e lê os registros de consulta.
- **includes/criptografia.php**: criptografia autenticada AES-256-GCM.
- **assets/**: CSS personalizado (tema claro na home, escuro no signo/área restrita) e JavaScript de validação front-end.
- Dados dos signos ficam **somente no SQLite**, nunca hardcoded no HTML das páginas.
- O histórico de consultas **não é exibido no site público** e não possui link de acesso.

---

## Segurança e boas práticas aplicadas

- Validação de data no servidor (não confiar apenas no HTML5).
- Prepared statements (PDO) para evitar SQL Injection.
- Validação de slug permitido antes de consultar o banco.
- `htmlspecialchars()` em todas as saídas.
- Não exposição de erros sensíveis ao usuário final.
- Separação clara de responsabilidades (config, lógica, apresentação).
- Área restrita sem link no site, protegida por senha com hash (`password_hash` / `password_verify`).
- Registros de consulta criptografados com AES-256-GCM (IV e tag de autenticação por registro).
- Sessão administrativa com `session_regenerate_id`, expiração por inatividade e cookie `HttpOnly` / `SameSite=Lax`.
- Tokens CSRF nos formulários e bloqueio temporário após tentativas de senha incorretas.
- Cabeçalhos `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy` e `X-Robots-Tag` na área restrita.

---

## Autor

Projeto desenvolvido como trabalho de curso – **AstroData**.

---

## Licença

Uso livre para fins educacionais.
