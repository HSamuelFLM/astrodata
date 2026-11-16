<?php
/**
 * Conexão PDO exclusiva com MySQL/MariaDB.
 * Use variáveis de ambiente ou config/database.local.php (não versionado).
 */
$localDatabaseConfig = [];
$localDatabaseFile = __DIR__ . '/database.local.php';
if (is_file($localDatabaseFile)) {
    $loadedConfig = require $localDatabaseFile;
    if (is_array($loadedConfig)) $localDatabaseConfig = $loadedConfig;
}
$dbHost = getenv('DB_HOST') ?: ($localDatabaseConfig['host'] ?? '');
$dbPort = getenv('DB_PORT') ?: ($localDatabaseConfig['port'] ?? 3306);
$dbName = getenv('DB_NAME') ?: ($localDatabaseConfig['database'] ?? '');
$dbUser = getenv('DB_USER') ?: ($localDatabaseConfig['username'] ?? '');
$dbPass = getenv('DB_PASS') ?: ($localDatabaseConfig['password'] ?? '');
$dbCharset = getenv('DB_CHARSET') ?: ($localDatabaseConfig['charset'] ?? 'utf8mb4');

if (!defined('DB_SETUP_TOKEN')) {
    define('DB_SETUP_TOKEN', (string) (getenv('DB_SETUP_TOKEN') ?: ($localDatabaseConfig['setup_token'] ?? '')));
}

function getConnection(): PDO
{
    global $dbHost, $dbPort, $dbName, $dbUser, $dbPass, $dbCharset;
    if ($dbHost === '' || $dbName === '' || $dbUser === '') {
        throw new RuntimeException('Configure host, database e usuário MySQL em config/database.local.php.');
    }
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $dbHost, (int) $dbPort, $dbName, $dbCharset);
    return new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}
