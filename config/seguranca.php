<?php
/**
 * Carrega segredos locais que nunca devem ser enviados ao GitHub.
 * Gere o arquivo com: php tools/gerar_credenciais.php
 */
$localSecurityFile = __DIR__ . '/seguranca.local.php';
if (!is_file($localSecurityFile)) {
    throw new RuntimeException('Credenciais ausentes. Execute php tools/gerar_credenciais.php e envie o arquivo local gerado.');
}
$localSecurity = require $localSecurityFile;
if (!is_array($localSecurity)) {
    throw new RuntimeException('O arquivo config/seguranca.local.php está inválido.');
}
$adminHash = getenv('ADMIN_SENHA_HASH') ?: ($localSecurity['admin_password_hash'] ?? '');
$encryptionKey = getenv('APP_CHAVE_CRIPTOGRAFIA') ?: ($localSecurity['encryption_key'] ?? '');
if (!is_string($adminHash) || $adminHash === '' || !is_string($encryptionKey) || $encryptionKey === '') {
    throw new RuntimeException('Hash administrativo ou chave de criptografia não configurados.');
}
define('ADMIN_SENHA_HASH', $adminHash);
define('APP_CHAVE_CRIPTOGRAFIA', $encryptionKey);
define('ADMIN_SESSAO_TIMEOUT', 900);
define('ADMIN_MAX_TENTATIVAS', 5);
define('ADMIN_BLOQUEIO_SEGUNDOS', 60);
