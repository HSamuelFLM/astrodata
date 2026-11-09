<?php
/**
 * Configuração e conexão com o banco de dados SQLite
 */

define('DB_PATH', __DIR__ . '/../database/signos.sqlite');

function getConnection(): PDO
{
    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
}
