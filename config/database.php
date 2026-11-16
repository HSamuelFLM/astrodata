<?php
/**
 * Conexão PDO com SQLite para desenvolvimento local.
 * Não use esta configuração na hospedagem InfinityFree: a branch main usa MySQL.
 */
$localDatabaseConfig = [];
$localDatabaseFile = __DIR__ . '/database.local.php';
if (is_file($localDatabaseFile)) {
    $loadedConfig = require $localDatabaseFile;
    if (is_array($loadedConfig)) $localDatabaseConfig = $loadedConfig;
}
$databasePath = getenv('DB_PATH') ?: ($localDatabaseConfig['database_path'] ?? (__DIR__ . '/../database/signos.sqlite'));
if (!defined('DB_SETUP_TOKEN')) {
    define('DB_SETUP_TOKEN', (string) (getenv('DB_SETUP_TOKEN') ?: ($localDatabaseConfig['setup_token'] ?? '')));
}
if (!defined('DB_PATH')) define('DB_PATH', $databasePath);

function getConnection(): PDO
{
    $directory = dirname(DB_PATH);
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        throw new RuntimeException('Não foi possível criar a pasta do banco SQLite.');
    }
    if (!extension_loaded('pdo_sqlite')) {
        throw new RuntimeException('A extensão PDO_SQLite do PHP não está habilitada.');
    }
    $pdo = new PDO('sqlite:' . DB_PATH, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('PRAGMA busy_timeout = 5000');
    return $pdo;
}
