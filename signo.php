<?php
/**
 * Página de exibição das informações do signo
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/funcoes.php';

$slug = isset($_GET['signo']) ? strtolower(trim($_GET['signo'])) : '';

// Validação básica do parâmetro
if (empty($slug) || !in_array($slug, slugsValidos(), true)) {
    $pageTitle = 'Signo não encontrado';
    $bodyClass = 'page-error';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <main class="error-wrapper">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100">
                <div class="col-md-8 col-lg-6 text-center">
                    <div class="card border-0 shadow-lg error-card">
                        <div class="card-body p-5">
                            <div class="display-1 mb-3">✨</div>
                            <h1 class="h2 mb-3">Signo não encontrado</h1>
                            <p class="text-muted mb-4">
                                O signo solicitado não existe ou o parâmetro é inválido.
                            </p>
                            <a href="index.php" class="btn btn-discover">
                                Voltar e descobrir seu signo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

try {
    $pdo = getConnection();
    $signo = buscarSignoPorSlug($pdo, $slug);

    if (!$signo) {
        // Banco existe mas signo não encontrado (improvável se init rodou)
        $pageTitle = 'Signo não encontrado';
        $bodyClass = 'page-error';
        require_once __DIR__ . '/includes/header.php';
        ?>
        <main class="error-wrapper">
            <div class="container">
                <div class="row justify-content-center align-items-center min-vh-100">
                    <div class="col-md-8 col-lg-6 text-center">
                        <div class="card border-0 shadow-lg error-card">
                            <div class="card-body p-5">
                                <div class="display-1 mb-3">✨</div>
                                <h1 class="h2 mb-3">Signo não encontrado</h1>
                                <p class="text-muted mb-4">
                                    Não foi possível carregar as informações deste signo.
                                    Verifique se o banco de dados foi inicializado.
                                </p>
                                <a href="index.php" class="btn btn-discover">Voltar ao início</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php
        require_once __DIR__ . '/includes/footer.php';
        exit;
    }

} catch (PDOException $e) {
    $pageTitle = 'Erro no sistema';
    $bodyClass = 'page-error';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <main class="error-wrapper">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100">
                <div class="col-md-8 col-lg-6 text-center">
                    <div class="card border-0 shadow-lg error-card">
                        <div class="card-body p-5">
                            <div class="display-1 mb-3">⚠️</div>
                            <h1 class="h2 mb-3">Erro de conexão</h1>
                            <p class="text-muted mb-4">
                                Não foi possível acessar o banco de dados.<br>
                                O banco é preparado automaticamente. Atualize a página em alguns instantes ou verifique a configuração do SQLite.
                            </p>
                            <a href="index.php" class="btn btn-discover">Voltar ao início</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $signo['nome'];
$bodyClass = 'page-signo';
require_once __DIR__ . '/includes/header.php';
?>

<main class="signo-wrapper">
    <div class="stars-bg stars-dark"></div>
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-9">
                
                <!-- Cabeçalho do signo -->
                <div class="signo-header text-center mb-5 fade-in">
                    <div class="constellation-circle mx-auto mb-4">
                        <span class="signo-symbol"><?= htmlspecialchars($signo['simbolo']) ?></span>
                    </div>
                    
                    <h1 class="signo-nome"><?= htmlspecialchars($signo['nome']) ?></h1>
                    
                    <div class="badge-datas mb-3">
                        <?= htmlspecialchars($signo['data_inicio']) ?> — <?= htmlspecialchars($signo['data_fim']) ?>
                    </div>
                    
                    <p class="signo-meta">
                        <span>PLANETA // <?= htmlspecialchars(strtoupper($signo['planeta'])) ?></span>
                        <span class="mx-2">|</span>
                        <span>ELEMENTO // <?= htmlspecialchars(strtoupper($signo['elemento'])) ?></span>
                    </p>
                </div>
                
                <!-- Descrição -->
                <div class="card info-card mb-4 fade-in delay-1">
                    <div class="card-body p-4">
                        <h2 class="section-title">Sobre o signo</h2>
                        <p class="mb-0"><?= htmlspecialchars($signo['descricao']) ?></p>
                    </div>
                </div>
                
                <!-- Grid de informações -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4 fade-in delay-2">
                        <div class="card info-card h-100">
                            <div class="card-body p-4">
                                <div class="info-icon">🔥</div>
                                <h3 class="h6 text-uppercase info-label mb-2">Elemento</h3>
                                <p class="fs-5 mb-0 fw-semibold"><?= htmlspecialchars($signo['elemento']) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 fade-in delay-2">
                        <div class="card info-card h-100">
                            <div class="card-body p-4">
                                <div class="info-icon">🌀</div>
                                <h3 class="h6 text-uppercase info-label mb-2">Modalidade</h3>
                                <p class="fs-5 mb-0 fw-semibold"><?= htmlspecialchars($signo['modalidade']) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 fade-in delay-2">
                        <div class="card info-card h-100">
                            <div class="card-body p-4">
                                <div class="info-icon">🪐</div>
                                <h3 class="h6 text-uppercase info-label mb-2">Planeta regente</h3>
                                <p class="fs-5 mb-0 fw-semibold"><?= htmlspecialchars($signo['planeta']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Características -->
                <div class="card info-card mb-4 fade-in delay-3">
                    <div class="card-body p-4">
                        <h2 class="section-title">Características</h2>
                        <p class="mb-0"><?= htmlspecialchars($signo['caracteristicas']) ?></p>
                    </div>
                </div>
                
                <!-- Pontos fortes e fracos -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6 fade-in delay-3">
                        <div class="card info-card h-100 border-success-subtle">
                            <div class="card-body p-4">
                                <h2 class="section-title text-success">Pontos fortes</h2>
                                <p class="mb-0"><?= htmlspecialchars($signo['pontos_fortes']) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 fade-in delay-3">
                        <div class="card info-card h-100 border-danger-subtle">
                            <div class="card-body p-4">
                                <h2 class="section-title text-danger">Pontos fracos</h2>
                                <p class="mb-0"><?= htmlspecialchars($signo['pontos_fracos']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Compatibilidade -->
                <div class="card info-card mb-4 fade-in delay-4">
                    <div class="card-body p-4">
                        <h2 class="section-title">Compatibilidade</h2>
                        <p class="mb-0"><?= htmlspecialchars($signo['compatibilidade']) ?></p>
                    </div>
                </div>
                
                <!-- Curiosidade -->
                <div class="card info-card curiosidade-card mb-5 fade-in delay-4">
                    <div class="card-body p-4">
                        <h2 class="section-title">
                            <span class="me-2">✨</span> Curiosidade
                        </h2>
                        <p class="mb-0 fst-italic"><?= htmlspecialchars($signo['curiosidade']) ?></p>
                    </div>
                </div>
                
                <!-- Botão voltar -->
                <div class="text-center fade-in delay-5">
                    <a href="index.php" class="btn btn-discover btn-lg">
                        Descobrir outro signo
                    </a>
                </div>
                
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
