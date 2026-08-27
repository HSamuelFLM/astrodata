<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AstroData - Descubra seu signo do zodíaco de forma rápida e elegante">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | AstroData' : 'AstroData - Descubra seu Signo' ?></title>
    
    <!-- Favicon (logo) -->
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    <link rel="apple-touch-icon" href="assets/img/logo.png">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- CSS próprio -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="<?= isset($bodyClass) ? htmlspecialchars($bodyClass) : 'page-home' ?>">
