<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/security.php';

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $isHttps,
    'httponly' => true,
    'samesite' => 'Strict',
]);
session_start();

$pageTitle = 'Área administrativa | Histórico';
$bodyClass = 'page-signo';
$erro = '';
$bloqueado = false;
$pdo = getConnection();
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$ipHash = hash('sha256', $ip);
$agora = time();
$janela = 15 * 60;
$limite = 5;

// Limpeza oportunista dos registros antigos para manter a tabela enxuta.
$limpar = $pdo->prepare('DELETE FROM historico_tentativas_login WHERE tentativa_em < :limite');
$limpar->execute([':limite' => $agora - 86400]);

$contar = $pdo->prepare('SELECT COUNT(*) FROM historico_tentativas_login WHERE ip_hash = :ip AND tentativa_em >= :inicio');
$contar->execute([':ip' => $ipHash, ':inicio' => $agora - $janela]);
$tentativas = (int) $contar->fetchColumn();
$bloqueado = $tentativas >= $limite;

if (empty($_SESSION['csrf_historico'])) {
    $_SESSION['csrf_historico'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf'] ?? '';
    if (!is_string($csrf) || !hash_equals($_SESSION['csrf_historico'], $csrf)) {
        http_response_code(400);
        $erro = 'Sessão expirada. Atualize a página e tente novamente.';
    } elseif (isset($_POST['sair'])) {
        unset($_SESSION['historico_autenticado']);
        session_regenerate_id(true);
        header('Location: historico.php');
        exit;
    } elseif (!$bloqueado) {
        $senha = $_POST['senha'] ?? '';
        if (is_string($senha) && password_verify($senha, HISTORICO_PASSWORD_HASH)) {
            $del = $pdo->prepare('DELETE FROM historico_tentativas_login WHERE ip_hash = :ip');
            $del->execute([':ip' => $ipHash]);
            session_regenerate_id(true);
            $_SESSION['historico_autenticado'] = true;
            $_SESSION['historico_atividade'] = $agora;
            $_SESSION['csrf_historico'] = bin2hex(random_bytes(32));
            header('Location: historico.php');
            exit;
        }

        $ins = $pdo->prepare('INSERT INTO historico_tentativas_login (ip_hash, tentativa_em) VALUES (:ip, :data)');
        $ins->execute([':ip' => $ipHash, ':data' => $agora]);
        $tentativas++;
        $bloqueado = $tentativas >= $limite;
        $erro = $bloqueado
            ? 'Muitas tentativas. O acesso foi temporariamente bloqueado por 15 minutos.'
            : 'Senha incorreta. Verifique e tente novamente.';
    }
}

$autenticado = !empty($_SESSION['historico_autenticado']);
if ($autenticado) {
    $ultimaAtividade = (int) ($_SESSION['historico_atividade'] ?? $agora);
    if (($agora - $ultimaAtividade) > 1800) {
        unset($_SESSION['historico_autenticado'], $_SESSION['historico_atividade']);
        $autenticado = false;
        $erro = 'Sua sessão expirou. Entre novamente.';
    } else {
        $_SESSION['historico_atividade'] = $agora;
    }
}

$consultas = [];
$total = 0;
if ($autenticado) {
    $total = (int) $pdo->query('SELECT COUNT(*) FROM historico_consultas')->fetchColumn();
    $consultas = $pdo->query('SELECT signo_consultado, data_consultada, horario_consulta FROM historico_consultas ORDER BY id DESC LIMIT 500')->fetchAll();
}

require_once __DIR__ . '/includes/header.php';
?>
<main class="signo-wrapper">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="card info-card">
                    <div class="card-body p-4">
                        <h1 class="section-title mb-4">Área administrativa</h1>
                        <?php if (!$autenticado): ?>
                            <p class="text-muted">Acesso restrito ao histórico de consultas.</p>
                            <?php if ($erro): ?><div class="alert alert-danger"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                            <?php if ($bloqueado): ?>
                                <div class="alert alert-warning">Limite de tentativas atingido. Tente novamente após 15 minutos.</div>
                            <?php else: ?>
                                <form method="POST" action="historico.php" autocomplete="off">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_historico'], ENT_QUOTES, 'UTF-8') ?>">
                                    <div class="mb-3">
                                        <label for="senha" class="form-label">Senha administrativa</label>
                                        <input type="password" class="form-control" id="senha" name="senha" required maxlength="200" autocomplete="current-password">
                                    </div>
                                    <button type="submit" class="btn btn-discover">Acessar histórico</button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                                <p class="mb-0 text-muted">Total de consultas registradas: <strong><?= $total ?></strong>. Exibindo até 500 mais recentes.</p>
                                <form method="POST" action="historico.php">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_historico'], ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit" name="sair" value="1" class="btn btn-outline-secondary">Sair</button>
                                </form>
                            </div>
                            <?php if (!$consultas): ?>
                                <p class="text-muted">Nenhuma consulta foi registrada ainda.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead><tr><th>Signo consultado</th><th>Data</th><th>Horário</th></tr></thead>
                                        <tbody>
                                        <?php foreach ($consultas as $consulta): ?>
                                            <tr>
                                                <td><?= htmlspecialchars(ucfirst($consulta['signo_consultado']), ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars(date('d/m/Y', strtotime($consulta['data_consultada'])), ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars($consulta['horario_consulta'], ENT_QUOTES, 'UTF-8') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <a href="index.php" class="btn btn-discover mt-3">Voltar ao início</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
