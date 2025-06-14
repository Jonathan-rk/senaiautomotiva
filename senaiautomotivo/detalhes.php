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

// Buscar a ficha técnica com informações da marca
$stmt = $pdo->prepare("
    SELECT f.*, m.nome as marca_nome, m.imagem_path as marca_imagem
    FROM fichas_tecnicas f
    LEFT JOIN marcas m ON f.marca_id = m.id
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <link rel="stylesheet" href="interface_style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
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
                        <a href="categorias.php" class="text-gray-700 hover:text-blue-600 transition-colors">
                            <i class="fas fa-tags mr-2"></i>Categorias
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
                    <a href="categorias.php" class="block py-2 text-gray-700">
                        <i class="fas fa-tags mr-2"></i>Categorias
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

    <!-- Breadcrumb -->
    <section class="bg-gray-100 py-4">
        <div class="max-w-7xl mx-auto px-4">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="index.php" class="text-senai-blue hover:underline">Início</a>
                <i class="fas fa-chevron-right text-gray-400"></i>
                <a href="fichas.php" class="text-senai-blue hover:underline">Fichas Técnicas</a>
                <i class="fas fa-chevron-right text-gray-400"></i>
                <span class="text-gray-600">
                    <?php 
                    $marca = $ficha['marca'] ?? '';
                    $modelo = $ficha['modelo'] ?? '';
                    echo htmlspecialchars(trim($marca . ' ' . $modelo)); 
                    ?>
                </span>
            </nav>
        </div>
    </section>

    <!-- Vehicle Header -->
    <section class="py-8 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Vehicle Image -->
                <div class="lg:w-1/2">
                    <div class="relative">
                        <?php
                        $marca = $ficha['marca_nome'] ?? '';
                        $modelo = $ficha['modelo'] ?? '';
                        $displayName = trim($marca . ' ' . $modelo);
                        
                        // Check for vehicle image first, then brand image, then fallback to placeholder
                        if (!empty($ficha['imagem_path'])) {
                            $imageUrl = $ficha['imagem_path'];
                        } elseif (!empty($ficha['marca_imagem'])) {
                            $imageUrl = $ficha['marca_imagem'];
                        } else {
                            $imageUrl = "https://via.placeholder.com/600x400/254AA5/ffffff?text=" . urlencode($displayName ?: 'Veículo');
                        }
                        ?>
                        <img src="<?php echo htmlspecialchars($imageUrl); ?>" 
                             alt="<?php echo htmlspecialchars($displayName ?: 'Veículo'); ?>" 
                             class="w-full h-80 object-cover rounded-xl shadow-lg">
                        
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
                
                <!-- Vehicle Info -->
                <div class="lg:w-1/2">
                    <div class="mb-6">
                        <?php /* Removed categoria_nome section since table doesn't exist yet */ ?>
                        
                        <h1 class="text-4xl font-bold senai-blue mb-2">
                            <?php echo htmlspecialchars(($ficha['marca_nome'] ?? '') . ' ' . ($ficha['modelo'] ?? '')); ?>
                        </h1>
                        
                        <p class="text-xl text-gray-600 mb-4">
                            <?php echo htmlspecialchars($ficha['ano'] ?? ''); ?>
                            <?php if ($ficha['versao'] ?? false): ?>
                                - <?php echo htmlspecialchars($ficha['versao']); ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <!-- Quick Specs -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <?php if ($ficha['tipo_motor'] ?? false): ?>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm text-gray-600">Motor</div>
                                <div class="font-bold"><?php echo htmlspecialchars($ficha['tipo_motor']); ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($ficha['potencia_maxima'] ?? false): ?>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm text-gray-600">Potência</div>
                                <div class="font-bold"><?php echo htmlspecialchars($ficha['potencia_maxima']); ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($ficha['tipo_cambio'] ?? false): ?>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm text-gray-600">Câmbio</div>
                                <div class="font-bold"><?php echo htmlspecialchars($ficha['tipo_cambio']); ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($ficha['tipo_combustivel'] ?? false): ?>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm text-gray-600">Combustível</div>
                                <div class="font-bold"><?php echo htmlspecialchars($ficha['tipo_combustivel']); ?></div>
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
                <!-- Informações Básicas -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold senai-blue mb-6">
                        <i class="fas fa-info-circle mr-2"></i>Informações Básicas
                    </h3>
                    <div class="space-y-3">
                        <?php
                        $basicInfo = [
                            'Marca' => $ficha['marca_nome'] ?? '',
                            'Modelo' => $ficha['modelo'] ?? '',
                            'Ano' => $ficha['ano'] ?? '',
                            'Versão' => $ficha['versao'] ?? '',
                            'Código do Motor' => $ficha['codigo_motor'] ?? '',
                            'Tipo de Combustível' => $ficha['tipo_combustivel'] ?? ''
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
                        <i class="fas fa-engine mr-2"></i>Motorização
                    </h3>
                    <div class="space-y-3">
                        <?php
                        $motorInfo = [
                            'Tipo de Motor' => $ficha['tipo_motor'],
                            'Cilindrada' => $ficha['cilindrada'],
                            'Potência Máxima' => $ficha['potencia_maxima'],
                            'Torque Máximo' => $ficha['torque_maximo'],
                            'Número de Válvulas' => $ficha['numero_valvulas'],
                            'Injeção Eletrônica' => $ficha['injecao_eletronica']
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
                        <i class="fas fa-cogs mr-2"></i>Transmissão
                    </h3>
                    <div class="space-y-3">
                        <?php
                        $transmissionInfo = [
                            'Tipo de Câmbio' => $ficha['tipo_cambio'],
                            'Número de Marchas' => $ficha['numero_marchas']
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
                
                <!-- Chassi -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold senai-blue mb-6">
                        <i class="fas fa-car-alt mr-2"></i>Chassi
                    </h3>
                    <div class="space-y-3">
                        <?php
                        $chassiInfo = [
                            'Suspensões' => $ficha['suspensoes'],
                            'Freios' => $ficha['freios'],
                            'ABS/EBD' => $ficha['abs_ebd'],
                            'Tipo de Direção' => $ficha['tipo_direcao'],
                            'Pneus Originais' => $ficha['pneus_originais']
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
                
                <!-- Dimensões e Capacidades -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold senai-blue mb-6">
                        <i class="fas fa-ruler-combined mr-2"></i>Dimensões e Capacidades
                    </h3>
                    <div class="space-y-3">
                        <?php
                        $dimensionsInfo = [
                            'Comprimento (mm)' => $ficha['comprimento'],
                            'Largura (mm)' => $ficha['largura'],
                            'Altura (mm)' => $ficha['altura'],
                            'Entre Eixos (mm)' => $ficha['entre_eixos'],
                            'Altura Livre do Solo (mm)' => $ficha['altura_livre_solo'],
                            'Peso (kg)' => $ficha['peso'],
                            'Tanque (litros)' => $ficha['tanque'],
                            'Porta-Malas (litros)' => $ficha['porta_malas'],
                            'Carga Útil (kg)' => $ficha['carga_util'],
                            'Ocupantes' => $ficha['ocupantes']
                        ];
                        
                        foreach ($dimensionsInfo as $label => $value):
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
                            'Aceleração 0-100 km/h (s)' => $ficha['aceleracao']
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
                
                <!-- Consumo -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold senai-blue mb-6">
                        <i class="fas fa-gas-pump mr-2"></i>Consumo
                    </h3>
                    <div class="space-y-3">
                        <?php
                        $consumptionInfo = [
                            'Consumo Urbano (km/l)' => $ficha['consumo_urbano'],
                            'Consumo Rodoviário (km/l)' => $ficha['consumo_rodoviario']
                        ];
                        
                        foreach ($consumptionInfo as $label => $value):
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
                
                <!-- Sistema Eletrônico -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold senai-blue mb-6">
                        <i class="fas fa-microchip mr-2"></i>Sistema Eletrônico
                    </h3>
                    <div class="space-y-3">
                        <?php
                        $electronicInfo = [
                            'Sistema de Injeção' => $ficha['sistema_injecao'],
                            'Sonda Lambda' => $ficha['sonda_lambda'],
                            'Sensor de Fase' => $ficha['sensor_fase'],
                            'Sistema de Ignição' => $ficha['sistema_ignicao'],
                            'Tipo de ECU' => $ficha['tipo_ecu']
                        ];
                        
                        foreach ($electronicInfo as $label => $value):
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
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html>

