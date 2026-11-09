<?php
/**
 * Utilitário de linha de comando para gerar credenciais da área restrita.
 *
 * Uso:
 *   php tools/gerar_credenciais.php "nova-senha"
 *   php tools/gerar_credenciais.php --chave
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Acesso restrito ao terminal.');
}

$argumento = $argv[1] ?? '';

if ($argumento === '') {
    fwrite(STDERR, "Uso:\n");
    fwrite(STDERR, "  php tools/gerar_credenciais.php \"nova-senha\"\n");
    fwrite(STDERR, "  php tools/gerar_credenciais.php --chave\n");
    exit(1);
}

if ($argumento === '--chave') {
    echo "Nova chave de criptografia:\n";
    echo "define('APP_CHAVE_CRIPTOGRAFIA', '" . base64_encode(random_bytes(32)) . "');\n\n";
    echo "ATENÇÃO: ao trocar a chave, os registros antigos ficam ilegíveis.\n";
    exit(0);
}

echo "Novo hash de senha:\n";
echo "define('ADMIN_SENHA_HASH', '" . password_hash($argumento, PASSWORD_DEFAULT) . "');\n";
