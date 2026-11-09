<?php
/**
 * Funções de criptografia simétrica dos registros de consulta.
 *
 * Algoritmo: AES-256-GCM (criptografia autenticada).
 * Formato gravado: "v1:" + base64( IV (12 bytes) + TAG (16 bytes) + CIFRADO )
 */

require_once __DIR__ . '/../config/seguranca.php';

const CRIPTO_ALGORITMO = 'aes-256-gcm';
const CRIPTO_IV_TAMANHO = 12;
const CRIPTO_TAG_TAMANHO = 16;
const CRIPTO_PREFIXO = 'v1:';

/**
 * Decodifica e valida a chave configurada.
 */
function chaveCriptografia(): string
{
    $chave = base64_decode(APP_CHAVE_CRIPTOGRAFIA, true);

    if ($chave === false || strlen($chave) !== 32) {
        throw new RuntimeException('Chave de criptografia inválida. Verifique config/seguranca.php.');
    }

    return $chave;
}

/**
 * Criptografa um texto e devolve o payload pronto para gravação.
 */
function criptografar(string $texto): string
{
    $iv = random_bytes(CRIPTO_IV_TAMANHO);
    $tag = '';

    $cifrado = openssl_encrypt(
        $texto,
        CRIPTO_ALGORITMO,
        chaveCriptografia(),
        OPENSSL_RAW_DATA,
        $iv,
        $tag,
        '',
        CRIPTO_TAG_TAMANHO
    );

    if ($cifrado === false || strlen($tag) !== CRIPTO_TAG_TAMANHO) {
        throw new RuntimeException('Falha ao criptografar os dados.');
    }

    return CRIPTO_PREFIXO . base64_encode($iv . $tag . $cifrado);
}

/**
 * Descriptografa um payload gerado por criptografar().
 * Retorna null quando o conteúdo é inválido ou foi adulterado.
 */
function descriptografar(?string $payload): ?string
{
    if ($payload === null || strpos($payload, CRIPTO_PREFIXO) !== 0) {
        return null;
    }

    $bruto = base64_decode(substr($payload, strlen(CRIPTO_PREFIXO)), true);

    if ($bruto === false || strlen($bruto) <= CRIPTO_IV_TAMANHO + CRIPTO_TAG_TAMANHO) {
        return null;
    }

    $iv = substr($bruto, 0, CRIPTO_IV_TAMANHO);
    $tag = substr($bruto, CRIPTO_IV_TAMANHO, CRIPTO_TAG_TAMANHO);
    $cifrado = substr($bruto, CRIPTO_IV_TAMANHO + CRIPTO_TAG_TAMANHO);

    $texto = openssl_decrypt(
        $cifrado,
        CRIPTO_ALGORITMO,
        chaveCriptografia(),
        OPENSSL_RAW_DATA,
        $iv,
        $tag
    );

    return $texto === false ? null : $texto;
}
