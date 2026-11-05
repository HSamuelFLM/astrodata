<?php

date_default_timezone_set('America/Sao_Paulo');
/**
 * Conexão SQLite. O banco ativo fica fora da pasta pública do projeto.
 * Na primeira execução, o banco de dados inicial é migrado automaticamente.
 */

$projectRoot = dirname(__DIR__);
$projectParent = dirname($projectRoot);
$documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;

// Se o projeto estiver dentro do document root do Apache, guarda o banco
// um nível acima dele. No servidor embutido, guarda ao lado da pasta do projeto.
if ($documentRoot) {
    $parentReal = realpath($projectParent) ?: $projectParent;
    $docPrefix = rtrim($documentRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    if ($parentReal === $documentRoot || strpos($parentReal . DIRECTORY_SEPARATOR, $docPrefix) === 0) {
        $projectParent = dirname($documentRoot);
    }
}

$projectKey = substr(hash('sha256', realpath($projectRoot) ?: $projectRoot), 0, 12);
$storageDirectory = $projectParent . DIRECTORY_SEPARATOR . 'astrodata_private_' . $projectKey;
if (!is_dir($storageDirectory) && !mkdir($storageDirectory, 0700, true) && !is_dir($storageDirectory)) {
    throw new RuntimeException('Não foi possível criar a pasta privada do banco de dados.');
}
if (DIRECTORY_SEPARATOR !== '\\') {
    @chmod($storageDirectory, 0700);
}

define('DB_PATH', $storageDirectory . DIRECTORY_SEPARATOR . 'signos.db');

function getConnection(): PDO
{
    static $connection = null;
    if ($connection instanceof PDO) {
        return $connection;
    }

    $seedPath = __DIR__ . '/../database/.signos-seed.sqlite';
    if (!file_exists(DB_PATH) && is_file($seedPath)) {
        if (!copy($seedPath, DB_PATH)) {
            throw new RuntimeException('Não foi possível migrar o banco inicial para a pasta privada.');
        }
        @chmod(DB_PATH, 0600);
        @unlink($seedPath);
    }

    try {
        $connection = new PDO('sqlite:' . DB_PATH, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        $connection->exec('PRAGMA busy_timeout = 5000');
        $connection->exec('PRAGMA foreign_keys = ON');

        // Migrações idempotentes: o histórico e o controle de tentativas
        // são criados automaticamente mesmo em bancos já existentes.
        $connection->exec("CREATE TABLE IF NOT EXISTS historico_consultas (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            signo_consultado TEXT NOT NULL,
            data_consultada TEXT NOT NULL,
            horario_consulta TEXT NOT NULL
        )");
        $connection->exec("CREATE TABLE IF NOT EXISTS historico_tentativas_login (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ip_hash TEXT NOT NULL,
            tentativa_em INTEGER NOT NULL
        )");
        $connection->exec('CREATE INDEX IF NOT EXISTS idx_historico_tentativas_ip_data ON historico_tentativas_login (ip_hash, tentativa_em)');

        return $connection;
    } catch (PDOException $e) {
        error_log('Falha ao conectar ao SQLite: ' . $e->getMessage());
        throw new RuntimeException('Não foi possível acessar o banco de dados. Verifique as permissões e a extensão PDO_SQLITE.');
    }
}
