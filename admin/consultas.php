<?php
/**
 * Área restrita – registro de consultas.
 *
 * Esta página não é linkada em nenhum lugar do site. O acesso ocorre apenas
 * pela URL direta e exige senha. Os registros são lidos do SQLite e
 * descriptografados somente em memória, durante a exibição.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/seguranca.php';
require_once __DIR__ . '/../includes/consultas.php';

// Cabeçalhos de segurança
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

// Sessão segura
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
]);
session_start();

$erro = '';
$aviso = '';
$autenticado = false;

if (empty($_SESSION['admin_csrf'])) {
    $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
}

function csrfValido(): bool
{
    return isset($_POST['csrf'])
        && is_string($_POST['csrf'])
        && hash_equals($_SESSION['admin_csrf'] ?? '', $_POST['csrf']);
}

function formatarDataConsulta(?string $valor): string
{
    if ($valor === null || $valor === '') {
        return '—';
    }

    $data = DateTime::createFromFormat('Y-m-d H:i:s', $valor);
    return $data ? $data->format('d/m/Y H:i:s') : $valor;
}

function formatarData(?string $valor): string
{
    if ($valor === null || $valor === '') {
        return '—';
    }

    $data = DateTime::createFromFormat('Y-m-d', $valor);
    return $data ? $data->format('d/m/Y') : $valor;
}

// Verifica sessão ativa e expira por inatividade
if (!empty($_SESSION['admin_autenticado'])) {
    $ultimoAcesso = (int) ($_SESSION['admin_ultimo_acesso'] ?? 0);

    if ($ultimoAcesso > 0 && (time() - $ultimoAcesso) > ADMIN_SESSAO_TIMEOUT) {
        $_SESSION['admin_autenticado'] = false;
        $_SESSION['admin_ultimo_acesso'] = 0;
        $aviso = 'Sessão expirada por inatividade. Informe a senha novamente.';
    } else {
        $autenticado = true;
        $_SESSION['admin_ultimo_acesso'] = time();
    }
}

// Logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'sair') {
    if (csrfValido()) {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        header('Location: consultas.php');
        exit;
    }

    $erro = 'Sessão inválida. Recarregue a página e tente novamente.';
}

// Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'login') {
    if (!csrfValido()) {
        $erro = 'Sessão inválida. Recarregue a página e tente novamente.';
    } else {
        $bloqueadoAte = (int) ($_SESSION['admin_bloqueado_ate'] ?? 0);

        if ($bloqueadoAte > time()) {
            $erro = 'Muitas tentativas incorretas. Aguarde ' . ($bloqueadoAte - time()) . ' segundos.';
        } else {
            $senha = isset($_POST['senha']) && is_string($_POST['senha']) ? $_POST['senha'] : '';

            if ($senha !== '' && password_verify($senha, ADMIN_SENHA_HASH)) {
                session_regenerate_id(true);
                $_SESSION['admin_autenticado'] = true;
                $_SESSION['admin_ultimo_acesso'] = time();
                $_SESSION['admin_tentativas'] = 0;
                $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
                unset($_SESSION['admin_bloqueado_ate']);

                $autenticado = true;
                $aviso = '';
            } else {
                $tentativas = (int) ($_SESSION['admin_tentativas'] ?? 0) + 1;
                $_SESSION['admin_tentativas'] = $tentativas;

                if ($tentativas >= ADMIN_MAX_TENTATIVAS) {
                    $_SESSION['admin_bloqueado_ate'] = time() + ADMIN_BLOQUEIO_SEGUNDOS;
                    $_SESSION['admin_tentativas'] = 0;
                    $erro = 'Muitas tentativas incorretas. Acesso bloqueado por '
                        . ADMIN_BLOQUEIO_SEGUNDOS . ' segundos.';
                } else {
                    $erro = 'Senha incorreta.';
                }
            }
        }
    }
}

// Carrega os registros somente após autenticação
$consultas = [];
$total = 0;
$paginaParam = $_GET['pagina'] ?? 1;
$pagina = max(1, is_scalar($paginaParam) ? (int) $paginaParam : 1);
$porPagina = 25;
$totalPaginas = 1;

if ($autenticado) {
    try {
        $pdo = getConnection();
        $total = contarConsultas($pdo);
        $totalPaginas = max(1, (int) ceil($total / $porPagina));
        $pagina = min($pagina, $totalPaginas);
        $consultas = listarConsultas($pdo, $porPagina, ($pagina - 1) * $porPagina);
    } catch (Throwable $e) {
        error_log('AstroData: falha ao ler consultas: ' . $e->getMessage());
        $erro = 'Não foi possível carregar os registros. '
            . 'Verifique se o banco foi inicializado em database/init.php.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Área restrita | AstroData</title>

    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="stars" aria-hidden="true"></div>

<?php if (!$autenticado): ?>
    <main class="auth-wrap">
        <section class="auth-card">
            <img src="../assets/img/logo.png" alt="AstroData" class="logo">
            <p class="eyebrow">Área restrita</p>
            <h1>Registro de consultas</h1>
            <p class="subtitle">
                Acesso exclusivo para administração. Informe a senha para visualizar os dados.
            </p>

            <?php if ($erro !== ''): ?>
                <div class="alert alert-error" role="alert"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <?php if ($aviso !== ''): ?>
                <div class="alert alert-info" role="status"><?= htmlspecialchars($aviso) ?></div>
            <?php endif; ?>

            <form method="post" action="consultas.php" autocomplete="off">
                <input type="hidden" name="acao" value="login">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['admin_csrf']) ?>">

                <label for="senha">Senha de acesso</label>
                <div class="password-row">
                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        required
                        autofocus
                        autocomplete="current-password"
                        placeholder="••••••••••••"
                    >
                    <button type="submit" class="btn-primary">Entrar</button>
                </div>
            </form>

            <p class="footnote">
                Dados criptografados com AES-256-GCM · Sessão expira em <?= (int) (ADMIN_SESSAO_TIMEOUT / 60) ?> minutos
            </p>
        </section>
    </main>
<?php else: ?>
    <header class="topbar">
        <div class="brand">
            <img src="../assets/img/logo.png" alt="AstroData" class="brand-logo">
            <div>
                <strong>ASTRODATA</strong>
                <span>Área restrita</span>
            </div>
        </div>

        <form method="post" action="consultas.php">
            <input type="hidden" name="acao" value="sair">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['admin_csrf']) ?>">
            <button type="submit" class="btn-ghost">Sair</button>
        </form>
    </header>

    <main class="admin-main">
        <div class="page-head">
            <div>
                <p class="eyebrow">Auditoria</p>
                <h1>Consultas registradas</h1>
                <p class="subtitle">
                    Registro interno das datas consultadas e signos identificados.
                    Informações sensíveis são descriptografadas apenas nesta página.
                </p>
            </div>

            <div class="stat" aria-label="Total de consultas">
                <span class="stat-number"><?= number_format($total, 0, ',', '.') ?></span>
                <span class="stat-label"><?= $total === 1 ? 'registro' : 'registros' ?></span>
            </div>
        </div>

        <?php if ($erro !== ''): ?>
            <div class="alert alert-error" role="alert"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <section class="panel">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th class="col-id">#</th>
                            <th>Data da consulta</th>
                            <th>Data consultada</th>
                            <th>Signo consultado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$consultas): ?>
                            <tr>
                                <td colspan="4" class="empty">
                                    <?= $erro !== '' ? 'Não foi possível exibir os registros.' : 'Nenhuma consulta registrada até o momento.' ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($consultas as $consulta): ?>
                                <tr>
                                    <td class="col-id"><?= (int) $consulta['id'] ?></td>
                                    <td><?= htmlspecialchars(formatarDataConsulta($consulta['data_consulta'])) ?></td>
                                    <td><?= htmlspecialchars(formatarData($consulta['data_consultada'])) ?></td>
                                    <td>
                                        <span class="signo-badge">
                                            <?= htmlspecialchars($consulta['signo_consultado'] ?? '—') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPaginas > 1): ?>
                <nav class="pagination" aria-label="Paginação dos registros">
                    <?php if ($pagina > 1): ?>
                        <a href="consultas.php?pagina=<?= $pagina - 1 ?>">← Anterior</a>
                    <?php else: ?>
                        <span class="disabled">← Anterior</span>
                    <?php endif; ?>

                    <span class="page-info">Página <?= $pagina ?> de <?= $totalPaginas ?></span>

                    <?php if ($pagina < $totalPaginas): ?>
                        <a href="consultas.php?pagina=<?= $pagina + 1 ?>">Próxima →</a>
                    <?php else: ?>
                        <span class="disabled">Próxima →</span>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </section>

        <p class="footnote footnote-main">
            Criptografia AES-256-GCM · Os dados nunca são exibidos no site público
        </p>
    </main>
<?php endif; ?>
</body>
</html>
