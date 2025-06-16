<?php
session_start();

// Configuração do banco de dados
$host = 'localhost';
$dbname = 'senai_automotivo';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Verificar se foi fornecido um ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('HTTP/1.0 404 Not Found');
    exit('Ficha não encontrada');
}

$fichaId = $_GET['id'];

// Buscar a ficha técnica
$stmt = $pdo->prepare("SELECT f.*, c.nome as categoria_nome FROM fichas_tecnicas f 
                       LEFT JOIN categorias c ON f.categoria_id = c.id 
                       WHERE f.id = ?");
$stmt->execute([$fichaId]);
$ficha = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ficha) {
    header('HTTP/1.0 404 Not Found');
    exit('Ficha não encontrada');
}

// Configurar headers para PDF
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $ficha['marca'] . '_' . $ficha['modelo'] . '_' . $ficha['ano'] . '.pdf"');

// Gerar conteúdo HTML para conversão em PDF
$html = '
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ficha Técnica - ' . htmlspecialchars($ficha['marca'] . ' ' . $ficha['modelo']) . '</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0A3871;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #0A3871;
            margin: 0;
            font-size: 24px;
        }
        .header h2 {
            color: #666;
            margin: 5px 0 0 0;
            font-size: 18px;
            font-weight: normal;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background-color: #0A3871;
            color: white;
            padding: 8px 15px;
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: bold;
        }
        .specs-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .spec-item {
            border-left: 3px solid #0A3871;
            padding: 8px 12px;
            background-color: #f8f9fa;
            margin-bottom: 5px;
        }
        .spec-label {
            font-weight: bold;
            color: #555;
        }
        .spec-value {
            color: #333;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        @media print {
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>' . htmlspecialchars($ficha['marca'] . ' ' . $ficha['modelo']) . '</h1>
        <h2>' . htmlspecialchars($ficha['ano']) . 
        ($ficha['versao'] ? ' - ' . htmlspecialchars($ficha['versao']) : '') . '</h2>
        ' . ($ficha['categoria_nome'] ? '<p>Categoria: ' . htmlspecialchars($ficha['categoria_nome']) . '</p>' : '') . '
    </div>';

// Informações Básicas
$html .= '<div class="section">
    <div class="section-title">Informações Básicas</div>
    <div class="specs-grid">';

$basicInfo = [
    'Marca' => $ficha['marca'],
    'Modelo' => $ficha['modelo'],
    'Ano' => $ficha['ano'],
    'Versão' => $ficha['versao'],
    'Código do Motor' => $ficha['codigo_motor'],
    'Tipo de Combustível' => $ficha['tipo_combustivel']
];

foreach ($basicInfo as $label => $value) {
    if ($value) {
        $html .= '<div class="spec-item">
            <div class="spec-label">' . $label . ':</div>
            <div class="spec-value">' . htmlspecialchars($value) . '</div>
        </div>';
    }
}

$html .= '</div></div>';

// Motorização
$html .= '<div class="section">
    <div class="section-title">Motorização</div>
    <div class="specs-grid">';

$motorInfo = [
    'Tipo de Motor' => $ficha['tipo_motor'],
    'Cilindrada' => $ficha['cilindrada'],
    'Potência Máxima' => $ficha['potencia_maxima'],
    'Torque Máximo' => $ficha['torque_maximo'],
    'Número de Válvulas' => $ficha['numero_valvulas'],
    'Injeção Eletrônica' => $ficha['injecao_eletronica']
];

foreach ($motorInfo as $label => $value) {
    if ($value) {
        $html .= '<div class="spec-item">
            <div class="spec-label">' . $label . ':</div>
            <div class="spec-value">' . htmlspecialchars($value) . '</div>
        </div>';
    }
}

$html .= '</div></div>';

// Transmissão
$html .= '<div class="section">
    <div class="section-title">Transmissão</div>
    <div class="specs-grid">';

$transmissionInfo = [
    'Tipo de Câmbio' => $ficha['tipo_cambio'],
    'Número de Marchas' => $ficha['numero_marchas']
];

foreach ($transmissionInfo as $label => $value) {
    if ($value) {
        $html .= '<div class="spec-item">
            <div class="spec-label">' . $label . ':</div>
            <div class="spec-value">' . htmlspecialchars($value) . '</div>
        </div>';
    }
}

$html .= '</div></div>';

// Suspensão e Freios
$html .= '<div class="section">
    <div class="section-title">Suspensão e Freios</div>
    <div class="specs-grid">';

$suspensionInfo = [
    'Suspensões' => $ficha['suspensoes'],
    'Freios' => $ficha['freios'],
    'ABS/EBD' => $ficha['abs_ebd']
];

foreach ($suspensionInfo as $label => $value) {
    if ($value) {
        $html .= '<div class="spec-item">
            <div class="spec-label">' . $label . ':</div>
            <div class="spec-value">' . htmlspecialchars($value) . '</div>
        </div>';
    }
}

$html .= '</div></div>';

// Direção e Pneus
$html .= '<div class="section">
    <div class="section-title">Direção e Pneus</div>
    <div class="specs-grid">';

$steeringInfo = [
    'Tipo de Direção' => $ficha['tipo_direcao'],
    'Pneus Originais' => $ficha['pneus_originais']
];

foreach ($steeringInfo as $label => $value) {
    if ($value) {
        $html .= '<div class="spec-item">
            <div class="spec-label">' . $label . ':</div>
            <div class="spec-value">' . htmlspecialchars($value) . '</div>
        </div>';
    }
}

$html .= '</div></div>';

// Dimensões
$html .= '<div class="section">
    <div class="section-title">Dimensões</div>
    <div class="specs-grid">';

$dimensionsInfo = [
    'Comprimento' => $ficha['comprimento'],
    'Largura' => $ficha['largura'],
    'Altura' => $ficha['altura'],
    'Entre-eixos' => $ficha['entre_eixos'],
    'Altura Livre do Solo' => $ficha['altura_livre_solo'],
    'Peso' => $ficha['peso']
];

foreach ($dimensionsInfo as $label => $value) {
    if ($value) {
        $html .= '<div class="spec-item">
            <div class="spec-label">' . $label . ':</div>
            <div class="spec-value">' . htmlspecialchars($value) . '</div>
        </div>';
    }
}

$html .= '</div></div>';

// Desempenho e Consumo
$html .= '<div class="section">
    <div class="section-title">Desempenho e Consumo</div>
    <div class="specs-grid">';

$performanceInfo = [
    'Velocidade Máxima' => $ficha['velocidade_maxima'],
    'Aceleração (0-100 km/h)' => $ficha['aceleracao'],
    'Consumo Urbano' => $ficha['consumo_urbano'],
    'Consumo Rodoviário' => $ficha['consumo_rodoviario'],
    'Capacidade do Tanque' => $ficha['tanque']
];

foreach ($performanceInfo as $label => $value) {
    if ($value) {
        $html .= '<div class="spec-item">
            <div class="spec-label">' . $label . ':</div>
            <div class="spec-value">' . htmlspecialchars($value) . '</div>
        </div>';
    }
}

$html .= '</div></div>';

// Capacidades
$html .= '<div class="section">
    <div class="section-title">Capacidades</div>
    <div class="specs-grid">';

$capacityInfo = [
    'Porta-malas' => $ficha['porta_malas'],
    'Carga Útil' => $ficha['carga_util'],
    'Ocupantes' => $ficha['ocupantes']
];

foreach ($capacityInfo as $label => $value) {
    if ($value) {
        $html .= '<div class="spec-item">
            <div class="spec-label">' . $label . ':</div>
            <div class="spec-value">' . htmlspecialchars($value) . '</div>
        </div>';
    }
}

$html .= '</div></div>';

// Sistemas e Eletrônica (se houver dados)
if ($ficha['sistema_injecao'] || $ficha['sonda_lambda'] || $ficha['sensor_fase'] || $ficha['sistema_ignicao'] || $ficha['tipo_ecu']) {
    $html .= '<div class="section">
        <div class="section-title">Sistemas e Eletrônica</div>
        <div class="specs-grid">';

    $electronicsInfo = [
        'Sistema de Injeção' => $ficha['sistema_injecao'],
        'Sonda Lambda' => $ficha['sonda_lambda'],
        'Sensor de Fase' => $ficha['sensor_fase'],
        'Sistema de Ignição' => $ficha['sistema_ignicao'],
        'Tipo de ECU' => $ficha['tipo_ecu']
    ];

    foreach ($electronicsInfo as $label => $value) {
        if ($value) {
            $html .= '<div class="spec-item">
                <div class="spec-label">' . $label . ':</div>
                <div class="spec-value">' . htmlspecialchars($value) . '</div>
            </div>';
        }
    }

    $html .= '</div></div>';
}

$html .= '
    <div class="footer">
        <p><strong>SENAI Automotivo</strong></p>
        <p>Ficha técnica gerada em ' . date('d/m/Y H:i') . '</p>
        <p>Dados sujeitos a alterações sem aviso prévio</p>
    </div>
</body>
</html>';

// Usar a biblioteca mPDF ou similar para gerar PDF
// Como não temos mPDF instalado, vamos usar uma abordagem alternativa
// Retornar HTML que pode ser convertido em PDF pelo navegador

header('Content-Type: text/html; charset=utf-8');
header('Content-Disposition: inline; filename="' . $ficha['marca'] . '_' . $ficha['modelo'] . '_' . $ficha['ano'] . '.html"');

echo $html;

// Adicionar JavaScript para imprimir automaticamente
echo '<script>
window.onload = function() {
    window.print();
};
</script>';
?>

