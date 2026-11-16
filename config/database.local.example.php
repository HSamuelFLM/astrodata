<?php
/**
 * Copie para config/database.local.php e preencha com os dados do painel.
 * O arquivo real é ignorado pelo Git.
 */
return [
    'host' => 'SEU_HOST_MYSQL',
    'port' => 3306,
    'database' => 'NOME_DO_BANCO',
    'username' => 'USUARIO_DO_BANCO',
    'password' => 'SENHA_DO_BANCO',
    'charset' => 'utf8mb4',
    // Gere um token longo e aleatório; será solicitado ao abrir database/init.php.
    'setup_token' => 'SUBSTITUA_POR_64_CARACTERES_HEXADECIMAIS_ALEATORIOS',
];
