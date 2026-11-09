<?php
/**
 * Credenciais e chave de criptografia da área restrita.
 *
 * IMPORTANTE:
 * - Este arquivo guarda segredos do projeto. Não o publique em repositórios
 *   públicos e restrinja o acesso ao servidor.
 * - Para trocar a senha ou gerar uma nova chave, use:
 *     php tools/gerar_credenciais.php "nova-senha"
 *     php tools/gerar_credenciais.php --chave
 * - Trocar a chave de criptografia torna ilegíveis os registros já gravados.
 */

// Hash da senha de acesso à área restrita (gerado com password_hash).
// Senha padrão do projeto: AstroData@2026
define('ADMIN_SENHA_HASH', '$2y$12$HAERb0MrNVZLLXjssNeIGuJHppxScnUQCWBDaxfDJdFoxE0ew.1ti');

// Chave AES-256 (32 bytes em base64) usada para criptografar as consultas.
define('APP_CHAVE_CRIPTOGRAFIA', 'R9a0QeaBIMUVuTn8TPWNjMMcsWjnCwgPHrPlYkZ3E9o=');

// Tempo máximo de inatividade da sessão administrativa (em segundos).
define('ADMIN_SESSAO_TIMEOUT', 900);

// Tentativas de login permitidas antes do bloqueio temporário.
define('ADMIN_MAX_TENTATIVAS', 5);
define('ADMIN_BLOQUEIO_SEGUNDOS', 60);
