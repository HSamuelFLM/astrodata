<?php
/**
 * Página inicial - Formulário para descobrir o signo
 */

session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/funcoes.php';

$pageTitle = 'Descubra seu Signo';
$bodyClass = 'page-home';

// Recupera mensagem de erro da sessão (flash) e limpa em seguida
$erro = '';
$dataAnterior = '';

if (!empty($_SESSION['flash_erro'])) {
    $erro = $_SESSION['flash_erro'];
    unset($_SESSION['flash_erro']);
}

if (!empty($_SESSION['flash_data'])) {
    $dataAnterior = $_SESSION['flash_data'];
    unset($_SESSION['flash_data']);
}

// Processa o formulário (sempre redireciona — padrão Post/Redirect/Get)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dataNascimento = $_POST['data_nascimento'] ?? '';

    $validacao = validarDataNascimento($dataNascimento);

    if (!$validacao['valido']) {
        $_SESSION['flash_erro'] = $validacao['mensagem'];
        $_SESSION['flash_data'] = $dataNascimento; // mantém a data digitada para correção
        header('Location: index.php');
        exit;
    }

    $slug = determinarSigno($validacao['dia'], $validacao['mes']);

    if ($slug === null) {
        $_SESSION['flash_erro'] = 'Não foi possível determinar o signo. Verifique a data informada.';
        $_SESSION['flash_data'] = $dataNascimento;
        header('Location: index.php');
        exit;
    }

    // Sucesso → redireciona para a página do signo
    header('Location: signo.php?signo=' . urlencode($slug));
    exit;
}

require_once __DIR__ . '/includes/header.php';
?>

<main class="home-wrapper">
    <div class="stars-bg"></div>
    
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-12 col-md-10 col-lg-7 col-xl-6">
                
                <div class="card home-card shadow-lg border-0">
                    <div class="card-body p-4 p-md-5 text-center">
                        
                        <!-- Logo -->
                        <div class="logo-wrapper mb-4">
                            <img src="assets/img/logo.png" alt="AstroData Logo" class="logo-img">
                        </div>
                        
                        <h1 class="brand-title mb-2">ASTRODATA</h1>
                        
                        <div class="badge-discover mb-4">
                            DESCOBRIR SIGNO
                        </div>
                        
                        <p class="lead text-muted mb-4">
                            Insira sua data de nascimento e descubra seu signo zodiacal
                        </p>
                        
                        <?php if ($erro): ?>
                            <div class="alert alert-danger alert-dismissible fade show text-start" role="alert">
                                <strong>Atenção:</strong> <?= htmlspecialchars($erro) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="index.php" id="form-signo" novalidate autocomplete="off">
                            <div class="mb-4">
                                <label for="data_nascimento" class="form-label visually-hidden">
                                    Data de Nascimento
                                </label>
                                <input 
                                    type="date" 
                                    class="form-control form-control-lg text-center <?= $erro ? 'is-invalid' : '' ?>" 
                                    id="data_nascimento" 
                                    name="data_nascimento" 
                                    required
                                    max="<?= date('Y-m-d') ?>"
                                    min="1900-01-01"
                                    autocomplete="off"
                                    value="<?= htmlspecialchars($dataAnterior) ?>"
                                >
                                <div class="invalid-feedback">
                                    Informe uma data de nascimento válida.
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-discover btn-lg w-100">
                                Descobrir meu signo
                            </button>
                        </form>
                        
                        <p class="small text-muted mt-4 mb-0">
                            Sistema desenvolvido para trabalho de curso • Dados astrológicos ocidentais
                        </p>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
