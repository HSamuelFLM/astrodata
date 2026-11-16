<?php
/**
 * Gera segredos locais do AstroData.
 * Execute no terminal: php tools/gerar_credenciais.php
 * O arquivo criado é ignorado pelo Git.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Acesso restrito ao terminal.');
}
$destino = __DIR__ . '/../config/seguranca.local.php';
if (is_file($destino) && !in_array('--force', $argv, true)) {
    fwrite(STDERR, "O arquivo já existe. Use --force somente se deseja trocar senha e chave.\n");
    exit(1);
}
$senha = rtrim(strtr(base64_encode(random_bytes(24)), '+/', '-_'), '=');
$chave = base64_encode(random_bytes(32));
$config = "<?php\nreturn " . var_export([
    'admin_password_hash' => password_hash($senha, PASSWORD_DEFAULT),
    'encryption_key' => $chave,
], true) . ";\n";
if (file_put_contents($destino, $config, LOCK_EX) === false) {
    fwrite(STDERR, "Não foi possível gravar config/seguranca.local.php.\n");
    exit(1);
}
if (function_exists('chmod')) @chmod($destino, 0600);
echo "Arquivo criado: config/seguranca.local.php\n";
echo "Senha administrativa gerada (guarde-a agora):\n{$senha}\n";
echo "A chave de criptografia foi armazenada somente no arquivo local.\n";
echo "Faça backup seguro desse arquivo. Não o envie ao GitHub.\n";
