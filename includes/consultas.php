<?php
/**
 * Registro e leitura das consultas realizadas no site.
 * Todos os campos são gravados criptografados (AES-256-GCM).
 */

require_once __DIR__ . '/criptografia.php';

/**
 * Grava uma consulta no banco.
 *
 * @param string $dataConsultada Data de nascimento informada (Y-m-d)
 * @param string $signo           Nome do signo identificado
 */
function registrarConsulta(PDO $pdo, string $dataConsultada, string $signo): void
{
    $agora = new DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo'));

    $stmt = $pdo->prepare("
        INSERT INTO consultas (data_consulta, data_consultada, signo_consultado)
        VALUES (:data_consulta, :data_consultada, :signo_consultado)
    ");

    $stmt->execute([
        ':data_consulta' => criptografar($agora->format('Y-m-d H:i:s')),
        ':data_consultada' => criptografar($dataConsultada),
        ':signo_consultado' => criptografar($signo),
    ]);
}

/**
 * Conta o total de consultas registradas.
 */
function contarConsultas(PDO $pdo): int
{
    return (int) $pdo->query("SELECT COUNT(*) FROM consultas")->fetchColumn();
}

/**
 * Lista as consultas mais recentes, já descriptografadas para exibição.
 */
function listarConsultas(PDO $pdo, int $limite = 25, int $offset = 0): array
{
    $limite = max(1, min(100, $limite));
    $offset = max(0, $offset);

    $stmt = $pdo->prepare("
        SELECT id, data_consulta, data_consultada, signo_consultado
        FROM consultas
        ORDER BY id DESC
        LIMIT :limite OFFSET :offset
    ");
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $registros = [];

    foreach ($stmt->fetchAll() as $linha) {
        $registros[] = [
            'id' => (int) $linha['id'],
            'data_consulta' => descriptografar($linha['data_consulta']),
            'data_consultada' => descriptografar($linha['data_consultada']),
            'signo_consultado' => descriptografar($linha['signo_consultado']),
        ];
    }

    return $registros;
}
