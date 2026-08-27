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

O projeto roda localmente com **XAMPP** (Apache + PHP) **ou** com o servidor embutido do PHP.  
Não utiliza Node.js nem Laravel.

---

## Estrutura de pastas

```
projeto-signos/
├── index.php              # Página inicial (formulário)
├── signo.php              # Página de detalhes do signo
├── config/
│   └── database.php       # Conexão PDO com SQLite
├── database/
│   ├── init.php           # Script que cria a tabela e insere os 12 signos
│   └── signos.sqlite      # Banco (criado automaticamente pelo init.php)
├── assets/
│   ├── css/
│   │   └── style.css      # Estilos personalizados (tema zodíaco)
│   ├── js/
│   │   └── script.js      # Validação e interações
│   └── img/
│       └── logo.png       # Logo do projeto
├── includes/
│   ├── header.php         # Cabeçalho HTML comum
│   ├── footer.php         # Rodapé (scripts)
│   └── funcoes.php        # Funções de validação e identificação do signo
└── README.md              # Este arquivo
```

---

## 1. Instalar o XAMPP

1. Acesse o site oficial: [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Baixe a versão para o seu sistema:
   - **Windows**: instalador `.exe`
   - **Linux**: arquivo `.run`
   - **macOS**: instalador `.dmg` ou `.pkg`
3. Instale mantendo as opções padrão de **Apache** e **PHP**.
4. Caminhos padrão após a instalação:

| Sistema | Pasta do XAMPP | PHP |
|---------|----------------|-----|
| Windows | `C:\xampp\` | `C:\xampp\php\php.exe` |
| Linux | `/opt/lampp/` | `/opt/lampp/bin/php` |
| macOS | `/Applications/XAMPP/` | `/Applications/XAMPP/bin/php` |

> **Observação:** não é necessário iniciar o MySQL. Este projeto usa SQLite (arquivo local).

---

## 2. Como executar (recomendado)

Esta forma **não exige** copiar o projeto para o `htdocs`.  
Deixe a pasta `projeto-signos` onde quiser (Documentos, Desktop, etc.).

### Windows

1. Extraia o projeto, por exemplo em:
   ```
   C:\Users\SeuUsuario\Documents\projeto-signos
   ```
2. Abra o **Prompt de Comando** (`Win + R` → `cmd`).
3. Entre na pasta do projeto:
   ```cmd
   cd C:\Users\SeuUsuario\Documents\projeto-signos
   ```
4. Inicie o servidor:
   ```cmd
   C:\xampp\php\php.exe -S localhost:8000
   ```
5. Deixe essa janela aberta enquanto estiver testando.

### Linux

1. Extraia o projeto, por exemplo em:
   ```
   ~/Documentos/projeto-signos
   ```
2. Abra o terminal e entre na pasta:
   ```bash
   cd ~/Documentos/projeto-signos
   ```
3. Inicie o servidor:
   ```bash
   /opt/lampp/bin/php -S localhost:8000
   ```
4. Deixe o terminal aberto enquanto estiver testando.

### macOS

1. Extraia o projeto, por exemplo em:
   ```
   ~/Documents/projeto-signos
   ```
2. Abra o **Terminal** e entre na pasta:
   ```bash
   cd ~/Documents/projeto-signos
   ```
3. Inicie o servidor:
   ```bash
   /Applications/XAMPP/bin/php -S localhost:8000
   ```
4. Deixe o terminal aberto enquanto estiver testando.

### Acessar no navegador (Windows, Linux e macOS)

Depois que o servidor estiver rodando:

1. **Primeira vez** (criar o banco e cadastrar os 12 signos):
   ```
   http://localhost:8000/database/init.php
   ```
   A página deve mostrar: **“Banco de dados inicializado com sucesso!”**

2. **Usar o sistema:**
   ```
   http://localhost:8000
   ```

Para parar o servidor, volte ao terminal/cmd e pressione `Ctrl + C`.

---

## 3. Forma alternativa: Apache do XAMPP (`htdocs`)

Use esta opção se preferir o Apache em vez do servidor embutido do PHP.

### Windows

1. Abra o **XAMPP Control Panel** e clique em **Start** no Apache.
2. Copie a pasta `projeto-signos` para:
   ```
   C:\xampp\htdocs\
   ```
3. Acesse:
   - Inicializar banco: `http://localhost/projeto-signos/database/init.php`
   - Sistema: `http://localhost/projeto-signos/`

### Linux

1. Inicie o XAMPP:
   ```bash
   sudo /opt/lampp/lampp start
   ```
2. Copie o projeto:
   ```bash
   sudo cp -r ~/Documentos/projeto-signos /opt/lampp/htdocs/
   ```
3. Permissão de escrita na pasta do banco (necessário para criar o `.sqlite`):
   ```bash
   sudo chmod -R 777 /opt/lampp/htdocs/projeto-signos/database
   ```
4. Acesse:
   - Inicializar banco: `http://localhost/projeto-signos/database/init.php`
   - Sistema: `http://localhost/projeto-signos/`

### macOS

1. Abra o **XAMPP** e inicie o Apache (ou use o manager do XAMPP).
2. Copie a pasta `projeto-signos` para:
   ```
   /Applications/XAMPP/htdocs/
   ```
3. Se aparecer erro de permissão ao criar o banco:
   ```bash
   chmod -R 777 /Applications/XAMPP/htdocs/projeto-signos/database
   ```
4. Acesse:
   - Inicializar banco: `http://localhost/projeto-signos/database/init.php`
   - Sistema: `http://localhost/projeto-signos/`

---

## 4. Como usar o sistema

1. Abra a página inicial.
2. Informe uma data de nascimento (exemplo: `15/09/1990`).
3. Clique em **Descobrir meu signo**.
4. O sistema redireciona para `signo.php?signo=virgem` (ou o signo correspondente).
5. Na página do signo aparecem:
   - período
   - elemento
   - modalidade
   - planeta regente
   - características
   - pontos fortes e fracos
   - compatibilidade
   - curiosidade
6. Clique em **Descobrir outro signo** para voltar ao formulário.

### Testes recomendados

| Teste | O que fazer | Resultado esperado |
|-------|-------------|--------------------|
| Data válida | `15/09/1990` | Redireciona para Virgem |
| Campo vazio | Enviar sem data | Alerta de erro |
| Data futura | Data de amanhã | Mensagem de erro |
| URL inválida | `signo.php?signo=xyz` | Página de erro amigável |
| Recarregar após erro | Enviar data inválida e apertar F5 | Formulário limpo, sem erro antigo |
| Responsividade | Abrir no celular/tablet | Layout se adapta |

---

## 5. Problemas comuns (SQLite / PDO)

| Problema | Solução |
|----------|---------|
| `php` não é reconhecido | Use o caminho completo do PHP do XAMPP (veja a tabela da seção 1). |
| Erro **could not find driver** | No `php.ini` do XAMPP, descomente `extension=pdo_sqlite` e `extension=sqlite3`. Reinicie o Apache ou o servidor PHP. |
| Erro de permissão ao criar o `.sqlite` | Dê permissão de escrita na pasta `database/` (`chmod 777 database` no Linux/macOS). |
| Página em branco / erro 500 | Ative `display_errors = On` no `php.ini` ou veja o log do Apache. |
| Banco não encontrado | Execute novamente `.../database/init.php`. |
| Signo não aparece | Confirme que o `init.php` rodou com sucesso e que a tabela possui 12 registros. |
| Porta 8000 já em uso | Troque a porta, por exemplo: `php -S localhost:8080`. |

### Conferir se o SQLite está ativo

**Windows:**
```cmd
C:\xampp\php\php.exe -m | findstr sqlite
```

**Linux:**
```bash
/opt/lampp/bin/php -m | grep -i sqlite
```

**macOS:**
```bash
/Applications/XAMPP/bin/php -m | grep -i sqlite
```

Deve aparecer `pdo_sqlite` e `sqlite3`.

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

- **index.php**: formulário + processamento POST + redirecionamento.
- **signo.php**: recebe o slug via GET, valida, consulta o banco e renderiza a página.
- **config/database.php**: único ponto de conexão PDO.
- **database/init.php**: cria a tabela e popula os 12 signos (executado apenas uma vez).
- **includes/funcoes.php**: lógica de validação de data e cálculo do signo (independente do banco).
- **assets/**: CSS personalizado (tema claro na home, escuro na página do signo) e JavaScript de validação front-end.
- Dados dos signos ficam **somente no SQLite**, nunca hardcoded no HTML das páginas.

---

## Segurança e boas práticas aplicadas

- Validação de data no servidor (não confiar apenas no HTML5).
- Prepared statements (PDO) para evitar SQL Injection.
- Validação de slug permitido antes de consultar o banco.
- `htmlspecialchars()` em todas as saídas.
- Não exposição de erros sensíveis ao usuário final.
- Separação clara de responsabilidades (config, lógica, apresentação).
- Padrão Post/Redirect/Get para o formulário (recarregar a página não reenvia dados antigos).

---

## Autor

Projeto desenvolvido como trabalho de curso – **AstroData**.

---

## Licença

Uso livre para fins educacionais.
