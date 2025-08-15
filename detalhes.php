<?php
session_start();
require_once 'config.php';

$pdo = getDBConnection();

// Verificar se o usuário está logado
$isLoggedIn = isLoggedIn();
$userRole = $isLoggedIn ? $_SESSION['user_role'] : null;

// Verificar se foi fornecido um ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: fichas.php');
    exit;
}

$fichaId = sanitizeInput($_GET['id']);

// Buscar a ficha técnica com informações da montadora
$stmt = $pdo->prepare("
    SELECT f.*, m.nome as montadoras_nome, m.imagem_path as montadoras_imagem
    FROM fichas_tecnicas f
    LEFT JOIN montadoras m ON f.montadoras_id = m.id
    WHERE f.id = ?
");
$stmt->execute([$fichaId]);
$ficha = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ficha) {
    header('Location: fichas.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php 
        $titleMontadora = $ficha['montadoras_nome'] ?? '';
        $titleModelo = $ficha['modelo'] ?? '';
        $titleAno = $ficha['ano'] ?? '';
        echo htmlspecialchars(trim("$titleMontadora $titleModelo $titleAno")) . ' - SENAI Automotivo'; 
        ?>
    </title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/interface_style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body class="bg-gray-50">
     <!-- Navegação -->
     <nav class="navbar">
        <div class="container">
            <div class="navbar-header">
                <a href="index.php">
                   <img src="images/wU7KMxCEL6DS.png" alt="SENAI Logo" class="logo">
                </a>
                
                <div class="menu-desktop">
                    <a href="index.php">Início</a>
                    <a href="fichas.php" class="active">Fichas Técnicas</a>
                    
                    <?php if ($isLoggedIn && $userRole === "professor"): ?>
                        <a href="painel_professor.php">
                            <i class="fas fa-cog icon"></i>Painel
                        </a>
                        <a href="montadoras.php">
                            <i class="fas fa-car icon"></i>Montadoras
                        </a>
                        <a href="carrocerias.php">
                            <i class="fas fa-shapes icon"></i>Carrocerias
                        </a>
                        <a href="logout.php">
                            <i class="fas fa-sign-out-alt icon"></i>Sair
                        </a>
                    <?php else: ?>
                        <a href="login.php">
                            <i class="fas fa-sign-in-alt icon"></i>Login
                        </a>
                    <?php endif; ?>
                </div>
                
                <button class="hamburger" id="hamburger" type="button" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobileMenu" class="menu-mobile hidden">
                <a href="index.php">Início</a>
                <a href="fichas.php" class="active">Fichas Técnicas</a>
                
                <?php if ($isLoggedIn && $userRole === "professor"): ?>
                    <a href="painel_professor.php">
                        <i class="fas fa-cog icon"></i>Painel
                    </a>
                    <a href="montadoras.php">
                        <i class="fas fa-car icon"></i>Montadoras
                    </a>
                    <a href="carrocerias.php">
                        <i class="fas fa-shapes icon"></i>Carrocerias
                    </a>
                    <a href="logout.php">
                        <i class="fas fa-sign-out-alt icon"></i>Sair
                    </a>
                <?php else: ?>
                    <a href="login.php">
                        <i class="fas fa-sign-in-alt icon"></i>Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Botão Voltar -->
    <section class="Breadcrumb">
        <div class="max-w-7xl mx-auto px-4">
            <a href="fichas.php" id="backButton" class="fixed top-24 left-6 bg-senai-blue text-white w-12 h-12 flex items-center justify-center rounded-full shadow-lg hover:bg-blue-700 transition-colors z-50">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </section>



  <!-- Cabeçalho do veículo -->
<section class="py-8 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Imagem do veículo (à esquerda no PC) -->
            <div class="w-full lg:w-1/2">
                <div class="relative">
                    <?php
                    $montadoras = $ficha['montadoras_nome'] ?? '';
                    $modelo = $ficha['modelo'] ?? '';
                    $displayName = trim($montadoras . ' ' . $modelo);

                    // Verifique primeiro a imagem do veículo, depois a imagem da marca e, em seguida, retorne ao espaço reservado
                    if (!empty($ficha['imagem_path'])) {
                        $imageUrl = $ficha['imagem_path'];
                    } elseif (!empty($ficha['montadoras_imagem'])) {
                        $imageUrl = $ficha['montadoras_imagem'];
                    } else {
                        $imageUrl = "https://via.placeholder.com/600x400/254AA5/ffffff?text=" . urlencode($displayName ?: 'Veículo');
                    }
                    ?>
                    <img src="<?php echo htmlspecialchars($imageUrl); ?>" 
                         alt="<?php echo htmlspecialchars($displayName ?: 'Veículo'); ?>" 
                         class="w-full h-[450px] object-cover rounded-xl shadow-lg">
                    
                    <!-- Botões de ação -->
                    <div class="absolute top-4 right-4 flex gap-2">
                        <a href="gerar_pdf.php?id=<?php echo $fichaId; ?>" 
                           target="_blank"
                           class="px-4 py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold cursor-pointer">
                            <i class="fas fa-download mr-2"></i>PDF
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Informações básicas (Right on PC) -->
            <div class="w-full lg:w-1/2">
                <div class="mb-6">
                    <h1 class="text-4xl font-bold senai-blue mb-2">
                        <?php echo htmlspecialchars(($ficha['montadoras_nome'] ?? '') . ' ' . ($ficha['modelo'] ?? '')); ?>
                    </h1>
                    
                    <p class="text-xl text-gray-600 mb-4">
                        <?php echo htmlspecialchars($ficha['ano'] ?? ''); ?>
                    </p>
                </div>
                
                <!-- Especificações rápidas -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    
                    <?php if ($ficha['versao'] ?? false): ?>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">Versão</div>
                            <div class="font-bold"><?php echo htmlspecialchars($ficha['versao']); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if ($ficha['identificacaomotor'] ?? false): ?>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">Motor</div>
                            <div class="font-bold"><?php echo htmlspecialchars($ficha['identificacaomotor']); ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($ficha['potencia'] ?? false): ?>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">Potência</div>
                            <div class="font-bold"><?php echo htmlspecialchars($ficha['potencia']); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if ($ficha['numero_marchas'] ?? false): ?>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">Número de Marchas</div>
                            <div class="font-bold"><?php echo htmlspecialchars($ficha['numero_marchas']); ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($ficha['cambio'] ?? false): ?>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">Câmbio</div>
                            <div class="font-bold"><?php echo htmlspecialchars($ficha['cambio']); ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($ficha['combustivel'] ?? false): ?>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">Combustível</div>
                            <div class="font-bold"><?php echo htmlspecialchars($ficha['combustivel']); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="especificacoes-tecnicas">
    <div class="container-info">
        <h2 class="titulo-secao">
            <i class="fas fa-cog"></i>Especificações Técnicas Completas
        </h2>

        <div class="grade-especificacoes">
            <!-- Bloco: Descrição do Veículo -->
            <div class="bloco-especificacao">
                <h3 class="titulo-bloco"><i class="fas fa-info-circle"></i>Descrição do Veículo</h3>
                <div class="conteudo-especificacao">
                    <?php
                    $basicInfo = [
                        'Montadora' => $ficha['montadoras_nome'] ?? '',
                        'Modelo' => $ficha['modelo'] ?? '',
                        'Versão' => $ficha['versao'] ?? '',
                        'Ano' => $ficha['ano'] ?? '',
                        'Carroceria' => $ficha['carroceria_nome'] ?? '',
                        'Lugares' => $ficha['lugares'] ?? '',
                        'Portas' => $ficha['portas'] ?? ''
                    ];
                    foreach ($basicInfo as $label => $value):
                        if (!empty($value)):
                    ?>
                    <div>
                        <span><?php echo $label; ?>:</span>
                        <span><?php echo htmlspecialchars($value); ?></span>
                    </div>
                    <?php endif; endforeach; ?>
                </div>
            </div>

            <!-- Bloco: Motorização -->
            <div class="bloco-especificacao">
                <h3 class="titulo-bloco"><i class="fas fa-cog"></i>Motorização</h3>
                <div class="conteudo-especificacao">
                    <?php
                    $motorInfo = [
                        'Identificação do Motor' => $ficha['identificacaomotor'],
                        'Material de Construção' => $ficha['materialconstrucao'],
                        'Instalação' => $ficha['instalacao'],
                        'Disposição' => $ficha['disposicao'],
                        'Combustível' => $ficha['combustivel'],
                        'Cilindros' => $ficha['cilindros'],
                        'Válvulas por Cilindro e Total' => $ficha['valvulasporcilindro'],
                        'Aspiração/Admissão' => $ficha['aspiracao'],
                        'Alimentação' => $ficha['alimentacao'],
                        'Potência' => $ficha['potencia'],
                        'Cilindrada' => $ficha['cilindrada'],
                        'Torque' => $ficha['torque'],
                        'Rotações Máximas' => $ficha['rotacao']
                    ];
                    foreach ($motorInfo as $label => $value):
                        if ($value):
                    ?>
                    <div>
                        <span><?php echo $label; ?>:</span>
                        <span><?php echo htmlspecialchars($value); ?></span>
                    </div>
                    <?php endif; endforeach; ?>
                </div>
            </div>

            <!-- Os demais blocos seguem o mesmo padrão -->
            <!-- Transmissão -->
            <div class="bloco-especificacao">
                <h3 class="titulo-bloco"><i class="fas fa-gears"></i>Transmissão</h3>
                <div class="conteudo-especificacao">
                    <?php
                    $transmissionInfo = [
                        'Tração' => $ficha['tracao'],
                        'Tipo de Câmbio' => $ficha['cambio'],
                        'Número de Marchas' => $ficha['numero_marchas'],
                        'Embreagem' => $ficha['embreagem'],
                    ];
                    foreach ($transmissionInfo as $label => $value):
                        if ($value):
                    ?>
                    <div>
                        <span><?php echo $label; ?>:</span>
                        <span><?php echo htmlspecialchars($value); ?></span>
                    </div>
                    <?php endif; endforeach; ?>
                </div>
            </div>

            <!-- Suspensão -->
            <div class="bloco-especificacao">
                <h3 class="titulo-bloco"><i class="fas fa-sliders-h"></i>Suspensão</h3>
                <div class="conteudo-especificacao">
                    <?php
                    $suspensionInfo = [
                        'Dianteira' => $ficha['dianteira'],
                        'Traseira' => $ficha['traseira'],
                    ];
                    foreach ($suspensionInfo as $label => $value):
                        if ($value):
                    ?>
                    <div>
                        <span><?php echo $label; ?>:</span>
                        <span><?php echo htmlspecialchars($value); ?></span>
                    </div>
                    <?php endif; endforeach; ?>
                </div>
            </div>

            <!-- Freios -->
            <div class="bloco-especificacao">
                <h3 class="titulo-bloco"><i class="bi bi-disc"></i>Freios</h3>
                <div class="conteudo-especificacao">
                    <?php
                    $freios = [
                        'Freio Dianteiro' => $ficha['dianteirosfreios'],
                        'Freio Traseiro' => $ficha['traseirosfreios']
                    ];
                    foreach ($freios as $label => $value):
                        if ($value):
                    ?>
                    <div>
                        <span><?php echo $label; ?>:</span>
                        <span><?php echo htmlspecialchars($value); ?></span>
                    </div>
                    <?php endif; endforeach; ?>
                </div>
            </div>

            <!-- Direção -->
            <div class="bloco-especificacao">
                <h3 class="titulo-bloco"><i class="fas fa-car-alt"></i>Direção</h3>
                <div class="conteudo-especificacao">
                    <?php
                    $direcao = ['Assistência' => $ficha['assistencia']];
                    foreach ($direcao as $label => $value):
                        if ($value):
                    ?>
                    <div>
                        <span><?php echo $label; ?>:</span>
                        <span><?php echo htmlspecialchars($value); ?></span>
                    </div>
                    <?php endif; endforeach; ?>
                </div>
            </div>

            <!-- Rodas e Pneus -->
            <div class="bloco-especificacao">
                <h3 class="titulo-bloco"><i class="fas fa-dot-circle"></i>Rodas e Pneus</h3>
                <div class="conteudo-especificacao">
                    <?php
                    $rodas = [
                        'Pressão de Enchimento Dianteira' => $ficha['dianteira_pressao_enchimento'],
                        'Pressão de Enchimento Traseira' => $ficha['traseira_pressao_enchimento'],
                        'Dimensão do Estepe / Velocidade' => $ficha['dimensao_estepe'],
                        'Material das Rodas / Dimensão' => $ficha['material_rodas']
                    ];
                    foreach ($rodas as $label => $value):
                        if ($value):
                    ?>
                    <div>
                        <span><?php echo $label; ?>:</span>
                        <span><?php echo htmlspecialchars($value); ?></span>
                    </div>
                    <?php endif; endforeach; ?>
                </div>
            </div>

            <!-- Chassi / Carroceria -->
            <div class="bloco-especificacao">
                <h3 class="titulo-bloco"><i class="fas fa-car-alt"></i>Chassi / Carroceria</h3>
                <div class="conteudo-especificacao">
                    <?php
                    $chassi = [
                        'Comprimento' => $ficha['comprimento'],
                        'Distância Entre-Eixos' => $ficha['distancia_eixos'],
                        'Largura' => $ficha['largura'],
                        'Altura' => $ficha['altura'],
                        'Peso bruto / Em ordem de Marchas' => $ficha['peso_bruto'],
                        'Porta-Malas (Litros)' => $ficha['porta_malas'],
                    ];
                    foreach ($chassi as $label => $value):
                        if ($value):
                    ?>
                    <div>
                        <span><?php echo $label; ?>:</span>
                        <span><?php echo htmlspecialchars($value); ?></span>
                    </div>
                    <?php endif; endforeach; ?>
                </div>
            </div>

            <!-- Desempenho -->
            <div class="bloco-especificacao">
                <h3 class="titulo-bloco"><i class="fas fa-tachometer-alt"></i>Desempenho</h3>
                <div class="conteudo-especificacao">
                    <?php
                    $performance = [
                        'Velocidade Máxima (km/h)' => $ficha['velocidade_maxima'],
                        'Aceleração 0-100 km/h' => $ficha['aceleracao'],
                        'Capacidade do Tanque de Combustível (litros)' => $ficha['capacidade_tanque'],
                        'Consumo Urbano' => $ficha['consumo_urbano'],
                        'Consumo Rodovia' => $ficha['consumo_rodovia'],
                        'Autonomia Urbana' => $ficha['autonomia_urbana'],
                        'Autonomia Rodovia' => $ficha['autonomia_rodovia'],
                    ];
                    foreach ($performance as $label => $value):
                        if ($value):
                    ?>
                    <div>
                        <span><?php echo $label; ?>:</span>
                        <span><?php echo htmlspecialchars($value); ?></span>
                    </div>
                    <?php endif; endforeach; ?>
                </div>
            </div>

            <!-- Fluidos -->
            <div class="bloco-especificacao">
                <h3 class="titulo-bloco"><i class="fas fa-gas-pump"></i>Fluidos</h3>
                <div class="conteudo-especificacao">
                    <?php
                    $fluids = [
                        'Óleo do Motor / Capacidade' => $ficha['oleo_motor'],
                        'Óleo da Transmissão / Capacidade' => $ficha['oleo_transmissao'],
                        'Fluido de Freio / Capacidade' => $ficha['fluido_freio']
                    ];
                    foreach ($fluids as $label => $value):
                        if ($value):
                    ?>
                    <div>
                        <span><?php echo $label; ?>:</span>
                        <span><?php echo htmlspecialchars($value); ?></span>
                    </div>
                    <?php endif; endforeach; ?>
                </div>
            </div>

            <!-- Descrição final -->
            <div class="bloco-especificacao">
                <h3 class="titulo-bloco"><i class="fas fa-clipboard-list"></i>Descrição</h3>
                <div class="conteudo-especificacao">
                    <?php
                    if (!empty($ficha['descricao'])) {
                        echo "<p>" . htmlspecialchars($ficha['descricao']) . "</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- Footer -->
<footer class="footer">
    <div class="footer-container">
        <div class="footer-main-content">
            <div class="footer-logo-section">
                <img src="images/wU7KMxCEL6DS.png" alt="SENAI Logo" class="footer-logo">
                <p class="footer-copyright">SENAI - Serviço Nacional de Aprendizagem Industrial</p>
            </div>
            <div class="footer-social-links">
                <!-- Social Media Links -->
                <a href="https://www.facebook.com/senaijoinvilleoficial" target="_blank" rel="noopener noreferrer" class="footer-social-link">
                    <i class="fab fa-facebook-f footer-social-icon"></i>
                </a>
                <a href="https://www.instagram.com/senai.joinvillesc/" target="_blank" rel="noopener noreferrer" class="footer-social-link">
                    <i class="fab fa-instagram footer-social-icon"></i>
                </a>
                <a href="https://www.linkedin.com/company/senai-sc" target="_blank" rel="noopener noreferrer" class="footer-social-link">
                    <i class="fab fa-linkedin-in footer-social-icon"></i>
                </a>
                <a href="https://www.youtube.com/@senaibrasil" target="_blank" rel="noopener noreferrer" class="footer-social-link">
                    <i class="fab fa-youtube footer-social-icon"></i>
                </a>
            </div>
        </div>
        <div class="footer-bottom-section">
            <div class="footer-bottom-content">
                <div class="footer-links-section">
                    <a href="desenvolvedores.php" class="footer-link margin-right-4">Desenvolvedores</a>
                    <a href="https://fiesc.com.br/pt-br/politica-de-privacidade#:~:text=O%20Sistema%20FIESC%2C%20composto%20pela,e%20sites%20do%20Sistema%20FIESC" class="footer-link margin-right-4">Política de Privacidade</a>
                    <a href="https://ava.sesisenai.org.br/local/faqs/viewall.php" class="footer-link">Termos de Uso</a>
                </div>
                <div class="footer-text-left">
                    <p class="footer-copyright">&copy; <?php echo date("Y"); ?> SENAI Automotivo. Todos os direitos reservados.</p>
                </div>
            </div>
        </div>
    </div>
</footer>

    <script src="js/script.js"></script>
</body>
</html>

