<?php
// Hash da senha do painel de histórico. Não armazene a senha em texto puro.
// Para trocar, gere outro hash com: php -r "echo password_hash('SUA_SENHA', PASSWORD_DEFAULT), PHP_EOL;"
if (!defined('HISTORICO_PASSWORD_HASH')) {
    define('HISTORICO_PASSWORD_HASH', '$2y$12$DSGFt84Iyr8omxIwFbQ0jOTLymhmSgUW8ghpBj.JzfW2syLjTN686');
}
