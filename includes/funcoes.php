<?php
/**
 * Funções auxiliares do projeto AstroData
 */

/**
 * Determina o signo com base no dia e mês de nascimento
 * Retorna o slug do signo ou null se inválido
 */
function determinarSigno(int $dia, int $mes): ?string
{
    // Validação básica de ranges
    if ($mes < 1 || $mes > 12 || $dia < 1 || $dia > 31) {
        return null;
    }

    // Mapeamento: [mês_inicio, dia_inicio, mês_fim, dia_fim, slug]
    // Observação: Capricórnio e Aquário/Peixes cruzam o ano
    $periodos = [
        ['mes_ini' => 3,  'dia_ini' => 21, 'mes_fim' => 4,  'dia_fim' => 19, 'slug' => 'aries'],
        ['mes_ini' => 4,  'dia_ini' => 20, 'mes_fim' => 5,  'dia_fim' => 20, 'slug' => 'touro'],
        ['mes_ini' => 5,  'dia_ini' => 21, 'mes_fim' => 6,  'dia_fim' => 20, 'slug' => 'gemeos'],
        ['mes_ini' => 6,  'dia_ini' => 21, 'mes_fim' => 7,  'dia_fim' => 22, 'slug' => 'cancer'],
        ['mes_ini' => 7,  'dia_ini' => 23, 'mes_fim' => 8,  'dia_fim' => 22, 'slug' => 'leao'],
        ['mes_ini' => 8,  'dia_ini' => 23, 'mes_fim' => 9,  'dia_fim' => 22, 'slug' => 'virgem'],
        ['mes_ini' => 9,  'dia_ini' => 23, 'mes_fim' => 10, 'dia_fim' => 22, 'slug' => 'libra'],
        ['mes_ini' => 10, 'dia_ini' => 23, 'mes_fim' => 11, 'dia_fim' => 21, 'slug' => 'escorpiao'],
        ['mes_ini' => 11, 'dia_ini' => 22, 'mes_fim' => 12, 'dia_fim' => 21, 'slug' => 'sagitario'],
        // Capricórnio: 22/12 a 19/01
        ['mes_ini' => 12, 'dia_ini' => 22, 'mes_fim' => 1,  'dia_fim' => 19, 'slug' => 'capricornio'],
        // Aquário: 20/01 a 18/02
        ['mes_ini' => 1,  'dia_ini' => 20, 'mes_fim' => 2,  'dia_fim' => 18, 'slug' => 'aquario'],
        // Peixes: 19/02 a 20/03
        ['mes_ini' => 2,  'dia_ini' => 19, 'mes_fim' => 3,  'dia_fim' => 20, 'slug' => 'peixes'],
    ];

    foreach ($periodos as $p) {
        // Caso normal (não cruza o ano)
        if ($p['mes_ini'] <= $p['mes_fim']) {
            if (
                ($mes === $p['mes_ini'] && $dia >= $p['dia_ini']) ||
                ($mes === $p['mes_fim'] && $dia <= $p['dia_fim']) ||
                ($mes > $p['mes_ini'] && $mes < $p['mes_fim'])
            ) {
                return $p['slug'];
            }
        } else {
            // Cruza o ano (ex: Capricórnio)
            if (
                ($mes === $p['mes_ini'] && $dia >= $p['dia_ini']) ||
                ($mes === $p['mes_fim'] && $dia <= $p['dia_fim'])
            ) {
                return $p['slug'];
            }
        }
    }

    return null;
}

/**
 * Valida se uma data de nascimento é válida e não é futura
 */
function validarDataNascimento(string $data): array
{
    $resultado = [
        'valido' => false,
        'mensagem' => '',
        'dia' => 0,
        'mes' => 0,
        'ano' => 0
    ];

    if (empty(trim($data))) {
        $resultado['mensagem'] = 'Por favor, informe sua data de nascimento.';
        return $resultado;
    }

    // Espera formato YYYY-MM-DD (input type="date")
    $partes = explode('-', $data);
    if (count($partes) !== 3) {
        $resultado['mensagem'] = 'Formato de data inválido.';
        return $resultado;
    }

    $ano = (int)$partes[0];
    $mes = (int)$partes[1];
    $dia = (int)$partes[2];

    if (!checkdate($mes, $dia, $ano)) {
        $resultado['mensagem'] = 'A data informada não é válida.';
        return $resultado;
    }

    $dataObj = DateTime::createFromFormat('Y-m-d', $data);
    $hoje = new DateTime('today');

    if ($dataObj > $hoje) {
        $resultado['mensagem'] = 'A data de nascimento não pode ser no futuro.';
        return $resultado;
    }

    // Limite razoável (ex: não antes de 1900)
    if ($ano < 1900) {
        $resultado['mensagem'] = 'Por favor, informe uma data a partir de 1900.';
        return $resultado;
    }

    $resultado['valido'] = true;
    $resultado['dia'] = $dia;
    $resultado['mes'] = $mes;
    $resultado['ano'] = $ano;

    return $resultado;
}

/**
 * Busca um signo pelo slug no banco
 */
function buscarSignoPorSlug(PDO $pdo, string $slug): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM signos WHERE slug = :slug LIMIT 1");
    $stmt->execute(['slug' => $slug]);
    $signo = $stmt->fetch();
    return $signo ?: null;
}

/**
 * Lista de slugs válidos (para validação de URL)
 */
function slugsValidos(): array
{
    return [
        'aries', 'touro', 'gemeos', 'cancer', 'leao', 'virgem',
        'libra', 'escorpiao', 'sagitario', 'capricornio', 'aquario', 'peixes'
    ];
}
