<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit('Not Found');
}
/**
 * Script de inicialização do banco de dados SQLite
 * Cria a tabela e insere os 12 signos se o banco ainda não existir ou estiver vazio.
 * Execute uma vez: http://localhost/projeto-signos/database/init.php
 */

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getConnection();

    // Cria a tabela se não existir
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS signos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT NOT NULL UNIQUE,
            slug TEXT NOT NULL UNIQUE,
            data_inicio TEXT NOT NULL,
            data_fim TEXT NOT NULL,
            elemento TEXT NOT NULL,
            modalidade TEXT NOT NULL,
            planeta TEXT NOT NULL,
            descricao TEXT NOT NULL,
            caracteristicas TEXT NOT NULL,
            pontos_fortes TEXT NOT NULL,
            pontos_fracos TEXT NOT NULL,
            compatibilidade TEXT NOT NULL,
            curiosidade TEXT NOT NULL,
            simbolo TEXT NOT NULL
        )
    ");

    // Tabela que registra cada consulta realizada
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS historico_consultas (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            signo_consultado TEXT NOT NULL,
            data_consultada TEXT NOT NULL,
            horario_consulta TEXT NOT NULL
        )
    ");

    // Verifica se já existem dados
    $count = $pdo->query("SELECT COUNT(*) FROM signos")->fetchColumn();

    if ($count > 0) {
        echo "<h1>Banco já inicializado</h1>";
        echo "<p>Existem {$count} signos cadastrados.</p>";
        echo '<p><a href="../index.php">Ir para a página inicial</a></p>';
        exit;
    }

    $signos = [
        [
            'nome' => 'Áries',
            'slug' => 'aries',
            'data_inicio' => '21/03',
            'data_fim' => '19/04',
            'elemento' => 'Fogo',
            'modalidade' => 'Cardinal',
            'planeta' => 'Marte',
            'simbolo' => '♈',
            'descricao' => 'Áries é o primeiro signo do zodíaco, simbolizando o início, a coragem e a impulsividade. Pessoas de Áries são líderes natos, cheios de energia e entusiasmo para conquistar novos desafios.',
            'caracteristicas' => 'Corajoso, determinado, otimista, impulsivo, competitivo, direto e apaixonado. Áries não tem medo de iniciar projetos e enfrentar obstáculos de frente.',
            'pontos_fortes' => 'Liderança, iniciativa, energia, honestidade, coragem, capacidade de motivar os outros e espírito pioneiro.',
            'pontos_fracos' => 'Impaciência, impulsividade, teimosia, tendência a ser agressivo ou dominante, dificuldade em ouvir opiniões contrárias.',
            'compatibilidade' => 'Melhores combinações: Leão, Sagitário e Gêmeos. Boa compatibilidade também com Aquário. Relacionamentos mais desafiadores com Câncer e Capricórnio.',
            'curiosidade' => 'O símbolo de Áries representa os chifres do carneiro. Na mitologia grega, está ligado ao Velo de Ouro e à história de Jasão e os Argonautas.'
        ],
        [
            'nome' => 'Touro',
            'slug' => 'touro',
            'data_inicio' => '20/04',
            'data_fim' => '20/05',
            'elemento' => 'Terra',
            'modalidade' => 'Fixo',
            'planeta' => 'Vênus',
            'simbolo' => '♉',
            'descricao' => 'Touro é o signo da estabilidade, do prazer sensorial e da perseverança. Pessoas de Touro valorizam o conforto, a segurança material e as coisas belas da vida.',
            'caracteristicas' => 'Paciente, confiável, prático, leal, sensual, teimoso e apreciador do luxo e da natureza. Touro constrói bases sólidas em tudo o que faz.',
            'pontos_fortes' => 'Determinação, lealdade, senso estético, capacidade de trabalhar duro, estabilidade emocional e financeira, generosidade.',
            'pontos_fracos' => 'Teimosia excessiva, resistência a mudanças, ciúme, materialismo e tendência a se acomodar demais.',
            'compatibilidade' => 'Melhores combinações: Virgem, Capricórnio e Câncer. Boa sintonia também com Peixes. Mais desafiador com Leão e Aquário.',
            'curiosidade' => 'Touro é regido por Vênus, o planeta do amor e da beleza. Por isso, muitos taurinos têm forte apreciação por arte, música, gastronomia e conforto físico.'
        ],
        [
            'nome' => 'Gêmeos',
            'slug' => 'gemeos',
            'data_inicio' => '21/05',
            'data_fim' => '20/06',
            'elemento' => 'Ar',
            'modalidade' => 'Mutável',
            'planeta' => 'Mercúrio',
            'simbolo' => '♊',
            'descricao' => 'Gêmeos é o signo da comunicação, da curiosidade e da dualidade. Pessoas de Gêmeos são versáteis, inteligentes e adoram trocar ideias e conhecer pessoas novas.',
            'caracteristicas' => 'Comunicativo, curioso, adaptável, intelectualmente agitado, sociável, inquieto e às vezes superficial. Gêmeos tem a mente sempre em movimento.',
            'pontos_fortes' => 'Inteligência rápida, versatilidade, habilidade de comunicação, humor, capacidade de se adaptar e fazer amizades com facilidade.',
            'pontos_fracos' => 'Inconstância, tendência a se dispersar, dificuldade em se aprofundar, nervosismo e dualidade que pode confundir os outros.',
            'compatibilidade' => 'Melhores combinações: Libra, Aquário e Áries. Boa relação também com Leão. Mais desafiador com Virgem e Peixes.',
            'curiosidade' => 'O símbolo de Gêmeos representa os gêmeos Castor e Pólux da mitologia grega. Isso explica a famosa dualidade e a capacidade de “ter duas faces” do signo.'
        ],
        [
            'nome' => 'Câncer',
            'slug' => 'cancer',
            'data_inicio' => '21/06',
            'data_fim' => '22/07',
            'elemento' => 'Água',
            'modalidade' => 'Cardinal',
            'planeta' => 'Lua',
            'simbolo' => '♋',
            'descricao' => 'Câncer é o signo do lar, das emoções profundas e da proteção. Pessoas de Câncer são sensíveis, intuitivas e extremamente ligadas à família e às memórias afetivas.',
            'caracteristicas' => 'Emotivo, protetor, intuitivo, leal, carinhoso, nostalgico e às vezes moody. Câncer sente o mundo de forma intensa.',
            'pontos_fortes' => 'Empatia, lealdade familiar, intuição forte, capacidade de cuidar dos outros, memória afetiva e resiliência emocional.',
            'pontos_fracos' => 'Hipersensibilidade, tendência a guardar mágoas, medo de rejeição, manipulação emocional e dificuldade em soltar o passado.',
            'compatibilidade' => 'Melhores combinações: Escorpião, Peixes e Touro. Boa sintonia também com Virgem. Mais desafiador com Áries e Libra.',
            'curiosidade' => 'A Lua, regente de Câncer, muda de fase constantemente — assim como o humor canceriano. Por isso o signo é tão associado às marés emocionais.'
        ],
        [
            'nome' => 'Leão',
            'slug' => 'leao',
            'data_inicio' => '23/07',
            'data_fim' => '22/08',
            'elemento' => 'Fogo',
            'modalidade' => 'Fixo',
            'planeta' => 'Sol',
            'simbolo' => '♌',
            'descricao' => 'Leão é o signo do brilho, da criatividade e da generosidade. Pessoas de Leão nascem para brilhar, liderar e inspirar os outros com sua presença magnética.',
            'caracteristicas' => 'Confiante, criativo, generoso, dramático, leal, orgulhoso e carismático. Leão ama ser o centro das atenções de forma positiva.',
            'pontos_fortes' => 'Liderança natural, generosidade, criatividade, lealdade, otimismo e capacidade de motivar e proteger quem ama.',
            'pontos_fracos' => 'Orgulho excessivo, necessidade de admiração, arrogância, dificuldade em lidar com críticas e tendência a ser dominador.',
            'compatibilidade' => 'Melhores combinações: Áries, Sagitário e Gêmeos. Boa relação também com Libra. Mais desafiador com Touro e Escorpião.',
            'curiosidade' => 'Leão é o único signo regido pelo Sol. Por isso, leoninos costumam ter uma presença solar — quente, radiante e difícil de ignorar.'
        ],
        [
            'nome' => 'Virgem',
            'slug' => 'virgem',
            'data_inicio' => '23/08',
            'data_fim' => '22/09',
            'elemento' => 'Terra',
            'modalidade' => 'Mutável',
            'planeta' => 'Mercúrio',
            'simbolo' => '♍',
            'descricao' => 'Virgem é o signo da análise, do serviço e da busca pela perfeição. Pessoas de Virgem são práticas, detalhistas e extremamente úteis no dia a dia.',
            'caracteristicas' => 'Analítico, perfeccionista, organizado, humilde, trabalhador, crítico e prestativo. Virgem nota o que os outros não veem.',
            'pontos_fortes' => 'Atenção aos detalhes, praticidade, inteligência analítica, lealdade, capacidade de resolver problemas e senso de dever.',
            'pontos_fracos' => 'Excesso de autocrítica, tendência a julgar os outros, ansiedade, dificuldade em relaxar e perfeccionismo paralisante.',
            'compatibilidade' => 'Melhores combinações: Touro, Capricórnio e Câncer. Boa sintonia também com Escorpião. Mais desafiador com Sagitário e Gêmeos.',
            'curiosidade' => 'Apesar de ser um signo de Terra, Virgem é regido por Mercúrio (planeta do intelecto). Por isso une o lado prático com uma mente extremamente analítica.'
        ],
        [
            'nome' => 'Libra',
            'slug' => 'libra',
            'data_inicio' => '23/09',
            'data_fim' => '22/10',
            'elemento' => 'Ar',
            'modalidade' => 'Cardinal',
            'planeta' => 'Vênus',
            'simbolo' => '♎',
            'descricao' => 'Libra é o signo do equilíbrio, da harmonia e dos relacionamentos. Pessoas de Libra buscam justiça, beleza e parcerias equilibradas em todas as áreas da vida.',
            'caracteristicas' => 'Diplomático, charmoso, justo, indeciso, sociável, esteta e mediador. Libra odeia conflitos e ama a harmonia.',
            'pontos_fortes' => 'Diplomacia, senso de justiça, charme, capacidade de ver os dois lados, elegância e talento para relacionamentos.',
            'pontos_fracos' => 'Indecisão, medo de confrontos, dependência de aprovação, tendência a evitar problemas e superficialidade em alguns casos.',
            'compatibilidade' => 'Melhores combinações: Gêmeos, Aquário e Leão. Boa relação também com Sagitário. Mais desafiador com Câncer e Capricórnio.',
            'curiosidade' => 'Libra é o único signo representado por um objeto inanimado (a balança), e não por um animal ou figura humana. Isso reforça sua busca constante pelo equilíbrio.'
        ],
        [
            'nome' => 'Escorpião',
            'slug' => 'escorpiao',
            'data_inicio' => '23/10',
            'data_fim' => '21/11',
            'elemento' => 'Água',
            'modalidade' => 'Fixo',
            'planeta' => 'Plutão (e tradicionalmente Marte)',
            'simbolo' => '♏',
            'descricao' => 'Escorpião é o signo da transformação, da intensidade e dos mistérios. Pessoas de Escorpião vivem com profundidade emocional e possuem grande poder de regeneração.',
            'caracteristicas' => 'Intenso, misterioso, determinado, apaixonado, intuitivo, leal e às vezes vingativo. Escorpião não faz nada pela metade.',
            'pontos_fortes' => 'Força de vontade, lealdade extrema, intuição profunda, capacidade de transformação, coragem emocional e magnetismo.',
            'pontos_fracos' => 'Ciúme, possessividade, dificuldade em perdoar, tendência a guardar segredos e intensidade excessiva que pode sufocar.',
            'compatibilidade' => 'Melhores combinações: Câncer, Peixes e Virgem. Boa sintonia também com Capricórnio. Mais desafiador com Leão e Aquário.',
            'curiosidade' => 'Escorpião é o único signo que possui três símbolos: o escorpião, a águia e a fênix — representando os diferentes níveis de evolução e transformação do signo.'
        ],
        [
            'nome' => 'Sagitário',
            'slug' => 'sagitario',
            'data_inicio' => '22/11',
            'data_fim' => '21/12',
            'elemento' => 'Fogo',
            'modalidade' => 'Mutável',
            'planeta' => 'Júpiter',
            'simbolo' => '♐',
            'descricao' => 'Sagitário é o signo da expansão, da liberdade e da busca por conhecimento. Pessoas de Sagitário são aventureiras, otimistas e apaixonadas por viajar e aprender.',
            'caracteristicas' => 'Otimista, aventureiro, honesto, filosófico, livre, entusiasta e às vezes imprudente. Sagitário odeia se sentir preso.',
            'pontos_fortes' => 'Otimismo, honestidade, sede de conhecimento, senso de humor, generosidade e capacidade de inspirar os outros a expandir horizontes.',
            'pontos_fracos' => 'Impaciência, falta de tato, tendência a exagerar, dificuldade com compromissos e restlessness constante.',
            'compatibilidade' => 'Melhores combinações: Áries, Leão e Aquário. Boa relação também com Libra. Mais desafiador com Virgem e Peixes.',
            'curiosidade' => 'Sagitário é regido por Júpiter, o maior planeta do sistema solar. Por isso o signo está associado à expansão, sorte e “pensar grande”.'
        ],
        [
            'nome' => 'Capricórnio',
            'slug' => 'capricornio',
            'data_inicio' => '22/12',
            'data_fim' => '19/01',
            'elemento' => 'Terra',
            'modalidade' => 'Cardinal',
            'planeta' => 'Saturno',
            'simbolo' => '♑',
            'descricao' => 'Capricórnio é o signo da ambição, da disciplina e da construção a longo prazo. Pessoas de Capricórnio são responsáveis, persistentes e focadas em alcançar seus objetivos.',
            'caracteristicas' => 'Ambicioso, disciplinado, responsável, paciente, realista, reservado e determinado. Capricórnio sobe a montanha passo a passo.',
            'pontos_fortes' => 'Disciplina, perseverança, senso de responsabilidade, ambição saudável, maturidade e capacidade de planejar o futuro.',
            'pontos_fracos' => 'Rigidez, pessimismo, excesso de trabalho, dificuldade em expressar emoções e tendência a ser excessivamente crítico.',
            'compatibilidade' => 'Melhores combinações: Touro, Virgem e Escorpião. Boa sintonia também com Peixes. Mais desafiador com Áries e Libra.',
            'curiosidade' => 'O símbolo de Capricórnio é a cabra-marinha (metade cabra, metade peixe). Representa a capacidade de escalar montanhas (Terra) e navegar nas profundezas emocionais (Água).'
        ],
        [
            'nome' => 'Aquário',
            'slug' => 'aquario',
            'data_inicio' => '20/01',
            'data_fim' => '18/02',
            'elemento' => 'Ar',
            'modalidade' => 'Fixo',
            'planeta' => 'Urano (e tradicionalmente Saturno)',
            'simbolo' => '♒',
            'descricao' => 'Aquário é o signo da inovação, da liberdade e da humanidade. Pessoas de Aquário são visionárias, independentes e preocupadas com o coletivo e o futuro.',
            'caracteristicas' => 'Independente, original, humanitário, intelectual, rebelde, amigável e às vezes distante emocionalmente. Aquário pensa fora da caixa.',
            'pontos_fortes' => 'Originalidade, visão de futuro, senso de justiça social, independência, inteligência e capacidade de inovar.',
            'pontos_fracos' => 'Distanciamento emocional, teimosia ideológica, dificuldade com intimidade, imprevisibilidade e rebeldia excessiva.',
            'compatibilidade' => 'Melhores combinações: Gêmeos, Libra e Sagitário. Boa relação também com Áries. Mais desafiador com Touro e Escorpião.',
            'curiosidade' => 'Apesar do nome “Aquário” e do símbolo das ondas, o signo é de elemento Ar (não Água). O símbolo representa o derramamento de conhecimento e ideias para a humanidade.'
        ],
        [
            'nome' => 'Peixes',
            'slug' => 'peixes',
            'data_inicio' => '19/02',
            'data_fim' => '20/03',
            'elemento' => 'Água',
            'modalidade' => 'Mutável',
            'planeta' => 'Netuno (e tradicionalmente Júpiter)',
            'simbolo' => '♓',
            'descricao' => 'Peixes é o signo da intuição, da compaixão e do mundo imaginário. Pessoas de Peixes são sonhadoras, empáticas e profundamente conectadas ao inconsciente e à arte.',
            'caracteristicas' => 'Empático, intuitivo, artístico, compassivo, sonhador, sensível e às vezes escapista. Peixes sente o que os outros não conseguem verbalizar.',
            'pontos_fortes' => 'Empatia profunda, criatividade, intuição, compaixão, adaptabilidade e capacidade de se conectar com o espiritual.',
            'pontos_fracos' => 'Tendência a fugir da realidade, excesso de sensibilidade, indecisão, vitimismo e dificuldade em estabelecer limites.',
            'compatibilidade' => 'Melhores combinações: Câncer, Escorpião e Touro. Boa sintonia também com Capricórnio. Mais desafiador com Gêmeos e Sagitário.',
            'curiosidade' => 'Peixes é o último signo do zodíaco e, por isso, carrega um pouco de todos os outros. É o “oceano” que absorve e dissolve as energias dos signos anteriores.'
        ]
    ];

    $stmt = $pdo->prepare("
        INSERT INTO signos (
            nome, slug, data_inicio, data_fim, elemento, modalidade, planeta,
            descricao, caracteristicas, pontos_fortes, pontos_fracos,
            compatibilidade, curiosidade, simbolo
        ) VALUES (
            :nome, :slug, :data_inicio, :data_fim, :elemento, :modalidade, :planeta,
            :descricao, :caracteristicas, :pontos_fortes, :pontos_fracos,
            :compatibilidade, :curiosidade, :simbolo
        )
    ");

    foreach ($signos as $signo) {
        $stmt->execute($signo);
    }

    echo "<h1>Banco de dados inicializado com sucesso!</h1>";
    echo "<p>12 signos cadastrados.</p>";
    echo '<p><a href="../index.php">Ir para a página inicial</a></p>';

} catch (PDOException $e) {
    echo "<h1>Erro ao inicializar o banco</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
