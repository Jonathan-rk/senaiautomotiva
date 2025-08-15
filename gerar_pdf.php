<?php
include('config.php');

// Obter conexão usando a função do config.php
$pdo = getDBConnection();

// Verificar se foi fornecido um ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('HTTP/1.0 404 Not Found');
    exit('Ficha não encontrada');
}

$fichaId = $_GET['id'];

// Buscar a ficha técnica com JOIN para trazer nome da montadora e carroceria
$stmt = $pdo->prepare("
    SELECT f.*, m.nome as montadoras_nome, c.nome as carroceria_nome
    FROM fichas_tecnicas f
    LEFT JOIN montadoras m ON f.montadoras_id = m.id
    LEFT JOIN carrocerias c ON f.carroceria_id = c.id
    WHERE f.id = ?
");
$stmt->execute([$fichaId]);
$ficha = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ficha) {
    header('HTTP/1.0 404 Not Found');
    exit('Ficha não encontrada');
}

// Lógica para imagem do veículo (gera base64 se for local, ou URL se for externa)
$imageTag = '';
if (!empty($ficha['imagem_path']) && file_exists($ficha['imagem_path'])) {
    $imgData = file_get_contents($ficha['imagem_path']);
    $imgType = pathinfo($ficha['imagem_path'], PATHINFO_EXTENSION);
    $base64 = 'data:image/' . $imgType . ';base64,' . base64_encode($imgData);
    $imageTag = '<img src="' . $base64 . '" alt="Imagem do veículo">';
} elseif (!empty($ficha['montadoras_imagem']) && file_exists($ficha['montadoras_imagem'])) {
    $imgData = file_get_contents($ficha['montadoras_imagem']);
    $imgType = pathinfo($ficha['montadoras_imagem'], PATHINFO_EXTENSION);
    $base64 = 'data:image/' . $imgType . ';base64,' . base64_encode($imgData);
    $imageTag = '<img src="' . $base64 . '" alt="Imagem do veículo">';
} else {
    $displayName = trim(($ficha['montadoras_nome'] ?? '') . ' ' . ($ficha['modelo'] ?? ''));
    $imageTag = '<img src="https://via.placeholder.com/120x80/254AA5/ffffff?text=' . urlencode($displayName ?: 'Veículo') . '" alt="Imagem do veículo">';
}

// Gerar conteúdo HTML para conversão em PDF
$html = '
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ficha Técnica - ' . htmlspecialchars($ficha["montadoras_nome"] ?? "") . ' ' . htmlspecialchars($ficha["modelo"] ?? "") . '</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            font-size: 10.5px;
            margin: 0;
            padding: 0;
            color: #000;
        }
        .container {
            padding: 20px 30px;
            max-width: 100%;
            box-sizing: border-box;
        }

        /* Cabeçalho com imagem à esquerda e texto alinhado à direita */
        .title-box {
            display: flex;
            justify-content: space-between;
            align-items: flex-end; /* Alinha a base da imagem com o texto */
            border-bottom: 2px solid #0a3871;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .title-box .image {
            flex-shrink: 0;
            margin-bottom: 4px;
        }
        .title-box .image img {
            max-width: 350px;        /* limite máximo de largura */
            max-height: 200px;       /* limite máximo de altura */
            height: auto;            /* altura automática conforme proporção */
            width: auto;             /* largura automática conforme proporção */
            border-radius: 4px;
            margin-bottom: -50px;
            object-fit: contain;     /* mantém a imagem proporcional sem cortar */
            background-color: transparent; /* fundo totalmente transparente */
            display: block;
        }
        .title-box .text {
            flex-grow: 1;
            text-align: right;
        }
        .title-box .text h1 {
            margin: 0;
            font-size: 22px;
            color: #0a3871;
        }
        .title-box .text h2 {
            margin: 4px 0 0;
            font-size: 13px;
            color: #444;
        }

        .section-title {
            background: #0a3871;
            color: #fff;
            padding: 4px 10px;
            font-weight: bold;
            margin-top: 16px;
            font-size: 11px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            margin-bottom: 10px;
        }
        .table td {
            border: 1px solid #0a3871;
            padding: 4px 6px;
        }
        .table td.label {
            background: #e4e4e4;
            font-weight: bold;
            width: 40%;
        }

        .content-section {
            margin: 10px 0;
            padding: 5px 10px;
        }

        .description-text {
            white-space: pre-wrap;
            word-wrap: break-word;
            font-family: Arial, sans-serif;
            line-height: 1.4;
            text-align: justify;
        }

        .footer {
            text-align: center;
            font-size: 9px;
            margin-top: 20px;
            border-top: 1px solid #ccc;
            padding-top: 8px;
            color: #666;
        }
    </style>
</head>
<body>
<div class="container">

    <div class="title-box">
        <div class="image">
            ' . $imageTag . '
        </div>
        <div class="text">
            <h1>' . htmlspecialchars($ficha["montadoras_nome"] ?? "") . ' ' . htmlspecialchars($ficha["modelo"] ?? "") . '</h1>
            <h2>' . htmlspecialchars($ficha["ano"] ?? "") . '</h2>
        </div>
    </div>

    <div class="section-title">Descrição do Veículo</div>
    <table class="table">
        <tr><td class="label">Montadora:</td><td>' . htmlspecialchars($ficha["montadoras_nome"] ?? "") . '</td></tr>
        <tr><td class="label">Modelo:</td><td>' . htmlspecialchars($ficha["modelo"] ?? "") . '</td></tr>
        <tr><td class="label">Versão:</td><td>' . htmlspecialchars($ficha["versao"] ?? "") . '</td></tr>
        <tr><td class="label">Ano:</td><td>' . htmlspecialchars($ficha["ano"] ?? "") . '</td></tr>
        <tr><td class="label">Configuração:</td><td>' . htmlspecialchars($ficha["carroceria_nome"] ?? "") . '</td></tr>
        <tr><td class="label">Lugares:</td><td>' . htmlspecialchars($ficha["lugares"] ?? "") . '</td></tr>
        <tr><td class="label">Portas:</td><td>' . htmlspecialchars($ficha["portas"] ?? "") . '</td></tr>
    </table>

    <div class="section-title">Motorização</div>
    <table class="table">
        <tr><td class="label">Identificação do Motor:</td><td>' . htmlspecialchars($ficha["identificacaomotor"] ?? "") . '</td></tr>
        <tr><td class="label">Material de Construção:</td><td>' . htmlspecialchars($ficha["materialconstrucao"] ?? "") . '</td></tr>
        <tr><td class="label">Instalação:</td><td>' . htmlspecialchars($ficha["instalacao"] ?? "") . '</td></tr>
        <tr><td class="label">Disposição:</td><td>' . htmlspecialchars($ficha["disposicao"] ?? "") . '</td></tr>
        <tr><td class="label">Combustível:</td><td>' . htmlspecialchars($ficha["combustivel"] ?? "") . '</td></tr>
        <tr><td class="label">Cilindros:</td><td>' . htmlspecialchars($ficha["cilindros"] ?? "") . '</td></tr>
        <tr><td class="label">Válvulas por Cilindro e Total:</td><td>' . htmlspecialchars($ficha["valvulasporcilindro"] ?? "") . '</td></tr>
        <tr><td class="label">Aspiração/Admissão:</td><td>' . htmlspecialchars($ficha["aspiracao"] ?? "") . '</td></tr>
        <tr><td class="label">Alimentação:</td><td>' . htmlspecialchars($ficha["alimentacao"] ?? "") . '</td></tr>
        <tr><td class="label">Potência:</td><td>' . htmlspecialchars($ficha["potencia"] ?? "") . '</td></tr>
        <tr><td class="label">Cilindrada:</td><td>' . htmlspecialchars($ficha["cilindrada"] ?? "") . '</td></tr>
        <tr><td class="label">Torque:</td><td>' . htmlspecialchars($ficha["torque"] ?? "") . '</td></tr>
        <tr><td class="label">Rotações Máximas:</td><td>' . htmlspecialchars($ficha["rotacao"] ?? "") . '</td></tr>
    </table>

    <div class="section-title">Transmissão</div>
    <table class="table">
        <tr><td class="label">Tração:</td><td>' . htmlspecialchars($ficha["tracao"] ?? "") . '</td></tr>
        <tr><td class="label">Câmbio:</td><td>' . htmlspecialchars($ficha["cambio"] ?? "") . '</td></tr>
        <tr><td class="label">Número de Marchas:</td><td>' . htmlspecialchars($ficha["numero_marchas"] ?? "") . '</td></tr>
        <tr><td class="label">Embreagem:</td><td>' . htmlspecialchars($ficha["embreagem"] ?? "") . '</td></tr>
    </table>

    <div class="section-title">Suspensão</div>
    <table class="table">
        <tr><td class="label">Dianteira:</td><td>' . htmlspecialchars($ficha["dianteira"] ?? "") . '</td></tr>
        <tr><td class="label">Traseira:</td><td>' . htmlspecialchars($ficha["traseira"] ?? "") . '</td></tr>
    </table>

    <div class="section-title">Freios</div>
    <table class="table">
        <tr><td class="label">Dianteiros:</td><td>' . htmlspecialchars($ficha["dianteirosfreios"] ?? "") . '</td></tr>
        <tr><td class="label">Traseiros:</td><td>' . htmlspecialchars($ficha["traseirosfreios"] ?? "") . '</td></tr>
    </table>

    <div class="section-title">Direção</div>
    <table class="table">
        <tr><td class="label">Assistência:</td><td>' . htmlspecialchars($ficha["assistencia"] ?? "") . '</td></tr>
    </table>

    <div class="section-title">Rodas e Pneus</div>
    <table class="table">
        <tr><td class="label">Dianteiros / Pressão:</td><td>' . htmlspecialchars($ficha["dianteira_pressao_enchimento"] ?? "") . '</td></tr>
        <tr><td class="label">Traseiros / Pressão:</td><td>' . htmlspecialchars($ficha["traseira_pressao_enchimento"] ?? "") . '</td></tr>
        <tr><td class="label">Estepe / Velocidade:</td><td>' . htmlspecialchars($ficha["dimensao_estepe"] ?? "") . '</td></tr>
        <tr><td class="label">Material das Rodas / Dimensão:</td><td>' . htmlspecialchars($ficha["material_rodas"] ?? "") . '</td></tr>
    </table>

    <div class="section-title">Chassi / Carroceria</div>
    <table class="table">
        <tr><td class="label">Comprimento:</td><td>' . htmlspecialchars($ficha["comprimento"] ?? "") . '</td></tr>
        <tr><td class="label">Distância entre-Eixos:</td><td>' . htmlspecialchars($ficha["distancia_eixos"] ?? "") . '</td></tr>
        <tr><td class="label">Largura:</td><td>' . htmlspecialchars($ficha["largura"] ?? "") . '</td></tr>
        <tr><td class="label">Altura:</td><td>' . htmlspecialchars($ficha["altura"] ?? "") . '</td></tr>
        <tr><td class="label">Peso Bruto / Em Ordem de Marcha:</td><td>' . htmlspecialchars($ficha["peso_bruto"] ?? "") . '</td></tr>
        <tr><td class="label">Porta-Malas:</td><td>' . htmlspecialchars($ficha["porta_malas"] ?? "") . '</td></tr>
    </table>

    <div class="section-title">Desempenho</div>
    <table class="table">
        <tr><td class="label">Velocidade Máxima:</td><td>' . htmlspecialchars($ficha["velocidade_maxima"] ?? "") . '</td></tr>
        <tr><td class="label">Aceleração 0-100 km/h:</td><td>' . htmlspecialchars($ficha["aceleracao"] ?? "") . '</td></tr>
        <tr><td class="label">Capacidade do Tanque:</td><td>' . htmlspecialchars($ficha["capacidade_tanque"] ?? "") . '</td></tr>
        <tr><td class="label">Consumo Urbano:</td><td>' . htmlspecialchars($ficha["consumo_urbano"] ?? "") . '</td></tr>
        <tr><td class="label">Consumo Rodovia:</td><td>' . htmlspecialchars($ficha["consumo_rodovia"] ?? "") . '</td></tr>
        <tr><td class="label">Autonomia Urbana:</td><td>' . htmlspecialchars($ficha["autonomia_urbana"] ?? "") . '</td></tr>
        <tr><td class="label">Autonomia Rodovia:</td><td>' . htmlspecialchars($ficha["autonomia_rodovia"] ?? "") . '</td></tr>
    </table>

    <div class="section-title">Fluidos</div>
    <table class="table">
        <tr><td class="label">Óleo do Motor / Capacidade:</td><td>' . htmlspecialchars($ficha["oleo_motor"] ?? "") . '</td></tr>
        <tr><td class="label">Óleo da Transmissão / Capacidade:</td><td>' . htmlspecialchars($ficha["oleo_transmissao"] ?? "") . '</td></tr>
        <tr><td class="label">Fluido de Freio:</td><td>' . htmlspecialchars($ficha["fluido_freio"] ?? "") . '</td></tr>
    </table>

    <div class="section-title">Descrição</div>
    <div class="content-section">
        <div class="description-text">
            ' . nl2br(htmlspecialchars($ficha["descricao"] ?? "")) . '
        </div>
    </div>

    <div class="footer">
        SENAI Automotiva - Ficha gerada em ' . date("d/m/Y") . '<br>
        Dados sujeitos a alterações sem aviso prévio.
    </div>
</div>
</body>
</html>';

// Gerar e enviar o PDF corretamente
require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Enviar o PDF para download
$filename = ($ficha['marca'] ?? 'Ficha') . '_' . ($ficha['modelo'] ?? 'modelo') . '_' . ($ficha['versao'] ?? 'versao') . '.pdf';
$dompdf->stream($filename, ['Attachment' => true]);
?>