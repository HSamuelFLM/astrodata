<?php
/**
 * Configuração opcional para desenvolvimento local.
 * Copie para config/database.local.php. Esse arquivo é ignorado pelo Git.
 */
return [
    // Por padrão, o banco fica em database/signos.sqlite.
    'database_path' => __DIR__ . '/../database/signos.sqlite',
    // Gere com: php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
    // Necessário somente para inicialização pelo navegador; CLI não exige token.
    'setup_token' => 'SUBSTITUA_POR_64_CARACTERES_HEXADECIMAIS_ALEATORIOS',
];
