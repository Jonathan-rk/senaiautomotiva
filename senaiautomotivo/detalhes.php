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
        $titleMarca = $ficha['marca_nome'] ?? '';
        $titleModelo = $ficha['modelo'] ?? '';
        $titleAno = $ficha['ano'] ?? '';
        echo htmlspecialchars(trim("$titleMarca $titleModelo $titleAno")) . ' - SENAI Automotivo'; 
        ?>
    </title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <link rel="stylesheet" href="interface_style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<style>
    @media (min-width: 1024px) {
        .lg\:flex-row {
            display: flex;
            flex-direction: row;
        }
        .lg\:w-1\/2 {
            width: 50%;
        }
    }
</style>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <img src="images/wU7KMxCEL6DS.png" alt="SENAI Logo" class="h-10">
                </div>
                
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="text-gray-700 hover:text-blue-600 transition-colors">Início</a>
                    <a href="fichas.php" class="text-blue-600 font-semibold">Fichas Técnicas</a>
                    <?php if ($isLoggedIn && $userRole === 'professor'): ?>
                        <a href="painel_professor.php" class="text-gray-700 hover:text-blue-600 transition-colors">
                            <i class="fas fa-cog mr-2"></i>Painel
                        </a>
                        <a href="montadoras.php" class="text-gray-700 hover:text-blue-600 transition-colors">
                            <i class="fas fa-car mr-2"></i>Montadoras
                        </a>
                        <a href="carrocerias.php" class="text-gray-700 hover:text-blue-600 transition-colors">
                            <i class="fas fa-shapes mr-2"></i>Carrocerias
                        </a>
                        <a href="logout.php" class="text-gray-700 hover:text-blue-600 transition-colors">
                            <i class="fas fa-sign-out-alt mr-2"></i>Sair
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="text-gray-700 hover:text-blue-600 transition-colors">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
                    <?php endif; ?>
                </div>
                
                <div class="md:hidden">
                    <button onclick="toggleMobileMenu()" class="text-gray-700">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobileMenu" class="hidden md:hidden pb-4">
                <a href="index.php" class="block py-2 text-gray-700">Início</a>
                <a href="fichas.php" class="block py-2 text-blue-600 font-semibold">Fichas Técnicas</a>
                <?php if ($isLoggedIn && $userRole === 'professor'): ?>
                    <a href="painel_professor.php" class="block py-2 text-gray-700">
                        <i class="fas fa-cog mr-2"></i>Painel
                    </a>
                    <a href="montadoras.php" class="block py-2 text-gray-700">
                        <i class="fas fa-car mr-2"></i>Montadoras
                    </a>
                    <a href="carrocerias.php" class="block py-2 text-gray-700">
                        <i class="fas fa-shapes mr-2"></i>Carrocerias
                    </a>
                    <a href="logout.php" class="block py-2 text-gray-700">
                        <i class="fas fa-sign-out-alt mr-2"></i>Sair
                    </a>
                <?php else: ?>
                    <a href="login.php" class="block py-2 text-gray-700">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Replace the Breadcrumb section with Back Button -->
    <section class="Breadcrumb">
        <div class="max-w-7xl mx-auto px-4">
            <a href="javascript:history.back()" id="backButton" class="fixed top-24 left-6 bg-senai-blue text-white w-12 h-12 flex items-center justify-center rounded-full shadow-lg hover:bg-blue-700 transition-colors z-50">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </section>



  <!-- Vehicle Header -->
<section class="py-8 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Vehicle Image (Left on PC) -->
            <div class="w-full lg:w-1/2">
                <div class="relative">
                    <?php
                    $montadoras = $ficha['montadoras_nome'] ?? '';
                    $modelo = $ficha['modelo'] ?? '';
                    $displayName = trim($montadoras . ' ' . $modelo);

                    // Check for vehicle image first, then brand image, then fallback to placeholder
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
                    
                    <!-- Action Buttons -->
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
                        <?php if ($ficha['modelo'] ?? false): ?>
                             <?php echo htmlspecialchars($ficha['modelo']); ?>
                        <?php endif; ?>
                         - <?php echo htmlspecialchars($ficha['ano'] ?? ''); ?>
                    </p>
                </div>
                
                <!-- Quick Specs -->
                <div class="grid grid-cols-2 gap-4 mb-6">
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

                    <?php if ($ficha['potencia'] ?? false): ?>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">Potência</div>
                            <div class="font-bold"><?php echo htmlspecialchars($ficha['potencia']); ?></div>
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


    <!-- Technical Specifications -->
    <section class="py-8 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold senai-blue mb-8 text-center">
                <i class="fas fa-cog mr-3"></i>Especificações Técnicas Completas
            </h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Descrição do Veículo -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold senai-blue mb-6">
                        <i class="fas fa-info-circle mr-2"></i>Descrição do Veículo
                    </h3>
                    <div class="space-y-3">
                        <?php
                        $basicInfo = [
                            'Montadora' => $ficha['montadoras_nome'] ?? '',
                            'Modelo' => $ficha['modelo'] ?? '',
                            'Ano' => $ficha['ano'] ?? '',
                            'Carroceria' => $ficha['carroceria_nome'] ?? '',
                            'Lugares' => $ficha['lugares'] ?? '',
                            'Portas' => $ficha['portas'] ?? ''
                        ];
                        
                        foreach ($basicInfo as $label => $value):
                            if (!empty($value)):
                        ?>
                            <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                <span class="font-medium text-gray-700"><?php echo $label; ?>:</span>
                                <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($value); ?></span>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
                
                <!-- Motorização -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold senai-blue mb-6">
                        <i class="fas fa-cog mr-2"></i>Motorização
                    </h3>
                    <div class="space-y-3">
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
                            <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                <span class="font-medium text-gray-700"><?php echo $label; ?>:</span>
                                <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($value); ?></span>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
                
                <!-- Transmissão -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold senai-blue mb-6">
                        <i class="fas fa-gears mr-2"></i>Transmissão
                    </h3>
                    <div class="space-y-3">
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
                            <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                <span class="font-medium text-gray-700"><?php echo $label; ?>:</span>
                                <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($value); ?></span>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>

                <!-- Suspensão -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold senai-blue mb-6">
                        <i class="fas fa-sliders-h mr-2"></i>Suspensão
                    </h3>
                    <div class="space-y-3">
                        <?php
                        $suspensionInfo = [
                            'Dianteira' => $ficha['dianteira'],
                            'Traseira' => $ficha['traseira'],
                        ];

                        foreach ($suspensionInfo as $label => $value):
                            if ($value):
                        ?>
                            <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                <span class="font-medium text-gray-700"><?php echo $label; ?>:</span>
                                <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($value); ?></span>
                            </div>
                        <?php
                            endif;
                        endforeach;
                        ?>
                    </div>
                </div>

                <!-- Freios -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold senai-blue mb-6">
                        <i class="bi bi-disc mr-2"></i>Freios
                    </h3>
                    <div class="space-y-3">
                        <?php
                        $chassiInfo = [
                            'Freio Dianteiro' => $ficha['dianteirosfreios'],
                            'Freio Traseiro' => $ficha['traseirosfreios']
                        ];
                        
                        foreach ($chassiInfo as $label => $value):
                            if ($value):
                        ?>
                            <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                <span class="font-medium text-gray-700"><?php echo $label; ?>:</span>
                                <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($value); ?></span>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>

                <!-- Direção -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold senai-blue mb-6">
                        <i class="fas fa-car-alt mr-2"></i>Direção
                    </h3>
                    <div class="space-y-3">
                        <?php
                        $chassiInfo = [
                            'Assistência' => $ficha['assistencia']
                        ];
                        
                        foreach ($chassiInfo as $label => $value):
                            if ($value):
                        ?>
                            <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                <span class="font-medium text-gray-700"><?php echo $label; ?>:</span>
                                <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($value); ?></span>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>

                <!-- Rodas e Pneus -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold senai-blue mb-6">
                        <i class="fas fa-dot-circle mr-2"></i>Rodas e Pneus
                    </h3>
                    <div class="space-y-3">
                        <?php
                        $chassiInfo = [
                            'Pressão de Enchimento Dianteira' => $ficha['dianteira_pressao_enchimento'],
                            'Pressão de Enchimento Traseira' => $ficha['traseira_pressao_enchimento'],
                            'Dimensão do Estepe / Velocidade' => $ficha['dimensao_estepe'],
                            'Material das Rodas / Dimensão' => $ficha['material_rodas']
                        ];
                        
                        foreach ($chassiInfo as $label => $value):
                            if ($value):
                        ?>
                            <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                <span class="font-medium text-gray-700"><?php echo $label; ?>:</span>
                                <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($value); ?></span>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
                
                <!-- Chassi / Carroceria -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold senai-blue mb-6">
                        <i class="fas fa-car-alt mr-2"></i>Chassi / Carroceria
                    </h3>
                    <div class="space-y-3">
                        <?php
                        $chassiInfo = [
                            'Comprimento' => $ficha['comprimento'],
                            'Distância Entre-Eixos' => $ficha['distancia_eixos'],
                            'Largura' => $ficha['largura'],
                            'Altura' => $ficha['altura'],
                            'Peso bruto / Em ordem de Marchas' => $ficha['peso_bruto'],
                            'Porta-Malas (Litros)' => $ficha['porta_malas'],
                        ];
                        
                        foreach ($chassiInfo as $label => $value):
                            if ($value):
                        ?>
                            <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                <span class="font-medium text-gray-700"><?php echo $label; ?>:</span>
                                <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($value); ?></span>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
                
                <!-- Desempenho -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold senai-blue mb-6">
                        <i class="fas fa-tachometer-alt mr-2"></i>Desempenho
                    </h3>
                    <div class="space-y-3">
                        <?php
                        $performanceInfo = [
                            'Velocidade Máxima (km/h)' => $ficha['velocidade_maxima'],
                            'Aceleração 0-100 km/h' => $ficha['aceleracao'],
                            'Capacidade do Tanque de Combustível (litros)' => $ficha['capacidade_tanque'],
                            'Consumo Urbano' => $ficha['consumo_urbano'],
                            'Consumo Rodovia' => $ficha['consumo_rodovia'],
                            'Autonomia Urbana' => $ficha['autonomia_urbana'],
                            'Autonomia Rodovia' => $ficha['autonomia_rodovia'],
                        ];
                        
                        foreach ($performanceInfo as $label => $value):
                            if ($value):
                        ?>
                            <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                <span class="font-medium text-gray-700"><?php echo $label; ?>:</span>
                                <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($value); ?></span>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
                
                <!-- Fluidos -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold senai-blue mb-6">
                        <i class="fas fa-gas-pump mr-2"></i>Fluidos
                    </h3>
                    <div class="space-y-3">
                        <?php
                        $fluidsInfo = [
                            'Óleo do Motor' => $ficha['oleo_motor'],
                            'Óleo da Transmissão' => $ficha['oleo_transmissao'],
                            'Fluido de Freio' => $ficha['fluido_freio']
                        ];

                        foreach ($fluidsInfo as $label => $value):
                            if ($value):
                        ?>
                            <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                <span class="font-medium text-gray-700"><?php echo $label; ?>:</span>
                                <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($value); ?></span>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>     
            </div>
        </div>
    </section>

    <!-- Footer -->
<footer class="bg-senai-blue text-white py-6">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-start items-start">
            <div class="mb-6 md:mb-0">
                <img src="images/wU7KMxCEL6DS.png" alt="SENAI Logo" class="h-10">
                <p class="mt-2 text-sm opacity-80">&copy; <?php echo date("Y"); ?> SENAI Automotivo. Todos os direitos reservados.</p>
            </div>
            <div class="flex space-x-6 ml-0">
                <!-- Social Media Links -->
                <a href="https://www.facebook.com/senaijoinvilleoficial" target="_blank" rel="noopener noreferrer" class="hover:text-gray-300 transition-colors">
                    <i class="fab fa-facebook-f text-xl"></i>
                </a>
                <a href="https://www.instagram.com/senai.joinvillesc/" target="_blank" rel="noopener noreferrer" class="hover:text-gray-300 transition-colors">
                    <i class="fab fa-instagram text-xl"></i>
                </a>
                <a href="https://www.linkedin.com/company/senai-sc" target="_blank" rel="noopener noreferrer" class="hover:text-gray-300 transition-colors">
                    <i class="fab fa-linkedin-in text-xl"></i>
                </a>
                <a href="https://www.youtube.com/@SENAISantaCatarina" target="_blank" rel="noopener noreferrer" class="hover:text-gray-300 transition-colors">
                    <i class="fab fa-youtube text-xl"></i>
                </a>
            </div>
        </div>
        <div class="mt-8 pt-8 border-t border-blue-800 text-sm opacity-80">
            <div class="flex flex-col md:flex-row items-start">
                <div class="mb-4 md:mb-0 text-left">
                    <a href="#" class="hover:text-gray-300 transition-colors mr-4">Política de Privacidade</a>
                    <a href="#" class="hover:text-gray-300 transition-colors mr-4">Termos de Uso</a>
                    <a href="#" class="hover:text-gray-300 transition-colors">Contato</a>
                </div>
                <div class="text-left">
                    <p>SENAI - Serviço Nacional de Aprendizagem Industrial</p>
                </div>
            </div>
        </div>
    </div>
</footer>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const backButton = document.getElementById('backButton');
            menu.classList.toggle('hidden');
            backButton.classList.toggle('hidden');
        }
    </script>
</body>
</html>

