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

// Verificar se o usuário está logado
$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $isLoggedIn ? $_SESSION['user_role'] : null;

// Verificar se foi fornecido um ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: fichas.php');
    exit;
}

$fichaId = $_GET['id'];

// Buscar a ficha técnica
$stmt = $pdo->prepare("SELECT f.*, c.nome as categoria_nome FROM fichas_tecnicas f 
                       LEFT JOIN categorias c ON f.categoria_id = c.id 
                       WHERE f.id = ?");
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
    <title><?php echo htmlspecialchars($ficha['marca'] . ' ' . $ficha['modelo'] . ' ' . $ficha['ano']); ?> - SENAI Automotivo</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container mx-auto px-4">
            <nav class="flex justify-between items-center py-4">
                <a href="index.php" class="logo">
                    <i class="fas fa-car mr-2"></i>SENAI Automotivo
                </a>
                
                <div class="hidden md:flex space-x-4">
                    <a href="index.php" class="nav-link">
                        <i class="fas fa-home mr-2"></i>Início
                    </a>
                    <a href="fichas.php" class="nav-link">
                        <i class="fas fa-file-alt mr-2"></i>Fichas Técnicas
                    </a>
                    <?php if ($isLoggedIn && $userRole === 'professor'): ?>
                        <a href="painel_professor.php" class="nav-link">
                            <i class="fas fa-cog mr-2"></i>Painel
                        </a>
                        <a href="categorias.php" class="nav-link">
                            <i class="fas fa-tags mr-2"></i>Categorias
                        </a>
                        <a href="logout.php" class="nav-link">
                            <i class="fas fa-sign-out-alt mr-2"></i>Sair
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="nav-link">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
                    <?php endif; ?>
                </div>
                
                <!-- Mobile menu button -->
                <button class="md:hidden text-white" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </nav>
            
            <!-- Mobile menu -->
            <div id="mobileMenu" class="hidden md:hidden pb-4">
                <a href="index.php" class="nav-link block">
                    <i class="fas fa-home mr-2"></i>Início
                </a>
                <a href="fichas.php" class="nav-link block">
                    <i class="fas fa-file-alt mr-2"></i>Fichas Técnicas
                </a>
                <?php if ($isLoggedIn && $userRole === 'professor'): ?>
                    <a href="painel_professor.php" class="nav-link block">
                        <i class="fas fa-cog mr-2"></i>Painel
                    </a>
                    <a href="categorias.php" class="nav-link block">
                        <i class="fas fa-tags mr-2"></i>Categorias
                    </a>
                    <a href="logout.php" class="nav-link block">
                        <i class="fas fa-sign-out-alt mr-2"></i>Sair
                    </a>
                <?php else: ?>
                    <a href="login.php" class="nav-link block">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Breadcrumb -->
    <section class="bg-gray-100 py-4">
        <div class="container mx-auto px-4">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="index.php" class="text-senai-blue hover:underline">Início</a>
                <i class="fas fa-chevron-right text-gray-400"></i>
                <a href="fichas.php" class="text-senai-blue hover:underline">Fichas Técnicas</a>
                <i class="fas fa-chevron-right text-gray-400"></i>
                <span class="text-gray-600"><?php echo htmlspecialchars($ficha['marca'] . ' ' . $ficha['modelo']); ?></span>
            </nav>
        </div>
    </section>

    <!-- Vehicle Header -->
    <section class="py-8 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Vehicle Image -->
                <div class="lg:w-1/2">
                    <div class="relative">
                        <img src="https://via.placeholder.com/600x400/254AA5/ffffff?text=<?php echo urlencode($ficha['marca'] . '+' . $ficha['modelo']); ?>" 
                             alt="<?php echo htmlspecialchars($ficha['marca'] . ' ' . $ficha['modelo']); ?>" 
                             class="w-full h-80 object-cover rounded-xl shadow-lg">
                        
                        <!-- Action Buttons -->
                        <div class="absolute top-4 right-4 flex gap-2">
                            <button onclick="downloadPDF()" class="btn btn-primary">
                                <i class="fas fa-download mr-2"></i>PDF
                            </button>
                            <button onclick="shareVehicle()" class="btn btn-secondary">
                                <i class="fas fa-share mr-2"></i>Compartilhar
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Vehicle Info -->
                <div class="lg:w-1/2">
                    <div class="mb-6">
                        <?php if ($ficha['categoria_nome']): ?>
                            <span class="bg-senai-blue text-white text-sm px-3 py-1 rounded-full mb-3 inline-block">
                                <?php echo htmlspecialchars($ficha['categoria_nome']); ?>
                            </span>
                        <?php endif; ?>
                        
                        <h1 class="text-4xl font-bold senai-blue mb-2">
                            <?php echo htmlspecialchars($ficha['marca'] . ' ' . $ficha['modelo']); ?>
                        </h1>
                        
                        <p class="text-xl text-gray-600 mb-4">
                            <?php echo htmlspecialchars($ficha['ano']); ?>
                            <?php if ($ficha['versao']): ?>
                                - <?php echo htmlspecialchars($ficha['versao']); ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <!-- Quick Specs -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <?php if ($ficha['tipo_motor']): ?>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm text-gray-600">Motor</div>
                                <div class="font-bold"><?php echo htmlspecialchars($ficha['tipo_motor']); ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($ficha['potencia_maxima']): ?>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm text-gray-600">Potência</div>
                                <div class="font-bold"><?php echo htmlspecialchars($ficha['potencia_maxima']); ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($ficha['tipo_cambio']): ?>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm text-gray-600">Câmbio</div>
                                <div class="font-bold"><?php echo htmlspecialchars($ficha['tipo_cambio']); ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($ficha['tipo_combustivel']): ?>
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
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold senai-blue mb-8 text-center">
                <i class="fas fa-cog mr-3"></i>Especificações Técnicas Completas
            </h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Informações Básicas -->
                <div class="detail-section">
                    <h3 class="text-xl font-bold senai-blue mb-6">
                        <i class="fas fa-info-circle mr-2"></i>Informações Básicas
                    </h3>
                    <div class="space-y-3">
                        <?php
                        $basicInfo = [
                            'Marca' => $ficha['marca'],
                            'Modelo' => $ficha['modelo'],
                            'Ano' => $ficha['ano'],
                            'Versão' => $ficha['versao'],
                            'Código do Motor' => $ficha['codigo_motor'],
                            'Tipo de Combustível' => $ficha['tipo_combustivel']
                        ];
                        
                        foreach ($basicInfo as $label => $value):
                            if ($value):
                        ?>
                            <div class="spec-item">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700"><?php echo $label; ?>:</span>
                                    <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($value); ?></span>
                                </div>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
                
                <!-- Motorização -->
                <div class="detail-section">
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
                            <div class="spec-item">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700"><?php echo $label; ?>:</span>
                                    <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($value); ?></span>
                                </div>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
                
                <!-- Transmissão -->
                <div class="detail-section">
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
                            <div class="spec-item">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700"><?php echo $label; ?>:</span>
                                    <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($value); ?></span>
                                </div>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
                
                <!-- Suspensão e Freios -->
                <div class="detail-section">
                    <h3 class="text-xl font-bold senai-blue mb-6">
                        <i class="fas fa-car-side mr-2"></i>Suspensão e Freios
                    </h3>
                    <div class="space-y-3">
                        <?php
                        $suspensionInfo = [
                            'Suspensões' => $ficha['suspensoes'],
                            'Freios' => $ficha['freios'],
                            'ABS/EBD' => $ficha['abs_ebd']
                        ];
                        
                        foreach ($suspensionInfo as $label => $value):
                            if ($value):
                        ?>
                            <div class="spec-item">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700"><?php echo $label; ?>:</span>
                                    <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($value); ?></span>
                                </div>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
                
                <!-- Direção e Pneus -->
                <div class="detail-section">
                    <h3 class="text-xl font-bold senai-blue mb-6">
                        <i class="fas fa-steering-wheel mr-2"></i>Direção e Pneus
                    </h3>
                    <div class="space-y-3">
                        <?php
                        $steeringInfo = [
                            'Tipo de Direção' => $ficha['tipo_direcao'],
                            'Pneus Originais' => $ficha['pneus_originais']
                        ];
                        
                        foreach ($steeringInfo as $label => $value):
                            if ($value):
                        ?>
                            <div class="spec-item">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700"><?php echo $label; ?>:</span>
                                    <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($value); ?></span>
                                </div>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
                
                <!-- Dimensões -->
                <div class="detail-section">
                    <h3 class="text-xl font-bold senai-blue mb-6">
                        <i class="fas fa-ruler mr-2"></i>Dimensões
                    </h3>
                    <div class="space-y-3">
                        <?php
                        $dimensionsInfo = [
                            'Comprimento' => $ficha['comprimento'],
                            'Largura' => $ficha['largura'],
                            'Altura' => $ficha['altura'],
                            'Entre-eixos' => $ficha['entre_eixos'],
                            'Altura Livre do Solo' => $ficha['altura_livre_solo'],
                            'Peso' => $ficha['peso']
                        ];
                        
                        foreach ($dimensionsInfo as $label => $value):
                            if ($value):
                        ?>
                            <div class="spec-item">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700"><?php echo $label; ?>:</span>
                                    <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($value); ?></span>
                                </div>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
                
                <!-- Desempenho e Consumo -->
                <div class="detail-section">
                    <h3 class="text-xl font-bold senai-blue mb-6">
                        <i class="fas fa-tachometer-alt mr-2"></i>Desempenho e Consumo
                    </h3>
                    <div class="space-y-3">
                        <?php
                        $performanceInfo = [
                            'Velocidade Máxima' => $ficha['velocidade_maxima'],
                            'Aceleração' => $ficha['aceleracao'],
                            'Consumo Urbano' => $ficha['consumo_urbano'],
                            'Consumo Rodoviário' => $ficha['consumo_rodoviario'],
                            'Tanque' => $ficha['tanque']
                        ];
                        
                        foreach ($performanceInfo as $label => $value):
                            if ($value):
                        ?>
                            <div class="spec-item">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700"><?php echo $label; ?>:</span>
                                    <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($value); ?></span>
                                </div>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
                
                <!-- Capacidades -->
                <div class="detail-section">
                    <h3 class="text-xl font-bold senai-blue mb-6">
                        <i class="fas fa-suitcase mr-2"></i>Capacidades
                    </h3>
                    <div class="space-y-3">
                        <?php
                        $capacityInfo = [
                            'Porta-malas' => $ficha['porta_malas'],
                            'Carga Útil' => $ficha['carga_util'],
                            'Ocupantes' => $ficha['ocupantes']
                        ];
                        
                        foreach ($capacityInfo as $label => $value):
                            if ($value):
                        ?>
                            <div class="spec-item">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700"><?php echo $label; ?>:</span>
                                    <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($value); ?></span>
                                </div>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
            </div>
            
            <!-- Sistemas e Eletrônica (se houver dados) -->
            <?php if ($ficha['sistema_injecao'] || $ficha['sonda_lambda'] || $ficha['sensor_fase'] || $ficha['sistema_ignicao'] || $ficha['tipo_ecu']): ?>
                <div class="mt-8">
                    <div class="detail-section">
                        <h3 class="text-xl font-bold senai-blue mb-6">
                            <i class="fas fa-microchip mr-2"></i>Sistemas e Eletrônica
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php
                            $electronicsInfo = [
                                'Sistema de Injeção' => $ficha['sistema_injecao'],
                                'Sonda Lambda' => $ficha['sonda_lambda'],
                                'Sensor de Fase' => $ficha['sensor_fase'],
                                'Sistema de Ignição' => $ficha['sistema_ignicao'],
                                'Tipo de ECU' => $ficha['tipo_ecu']
                            ];
                            
                            foreach ($electronicsInfo as $label => $value):
                                if ($value):
                            ?>
                                <div class="spec-item">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium text-gray-700"><?php echo $label; ?>:</span>
                                        <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($value); ?></span>
                                    </div>
                                </div>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Action Buttons -->
    <section class="py-8 bg-white">
        <div class="container mx-auto px-4 text-center">
            <div class="flex flex-col md:flex-row gap-4 justify-center">
                <button onclick="downloadPDF()" class="btn btn-primary">
                    <i class="fas fa-download mr-2"></i>Baixar PDF
                </button>
                <button onclick="shareVehicle()" class="btn btn-secondary">
                    <i class="fas fa-share mr-2"></i>Compartilhar Ficha
                </button>
                <a href="fichas.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Voltar às Fichas
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> SENAI Automotivo. Todos os direitos reservados.</p>
                <p class="mt-2 text-sm opacity-75">
                    Ficha técnica fornecida pelo SENAI - Dados sujeitos a alterações sem aviso prévio
                </p>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
        
        function downloadPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            // Título
            doc.setFontSize(20);
            doc.text('<?php echo addslashes($ficha['marca'] . ' ' . $ficha['modelo'] . ' ' . $ficha['ano']); ?>', 20, 30);
            
            // Informações básicas
            doc.setFontSize(12);
            let y = 50;
            
            <?php
            $allSpecs = [
                'Marca' => $ficha['marca'],
                'Modelo' => $ficha['modelo'],
                'Ano' => $ficha['ano'],
                'Versão' => $ficha['versao'],
                'Motor' => $ficha['tipo_motor'],
                'Potência' => $ficha['potencia_maxima'],
                'Torque' => $ficha['torque_maximo'],
                'Câmbio' => $ficha['tipo_cambio'],
                'Combustível' => $ficha['tipo_combustivel']
            ];
            
            foreach ($allSpecs as $label => $value):
                if ($value):
            ?>
                doc.text('<?php echo addslashes($label); ?>: <?php echo addslashes($value); ?>', 20, y);
                y += 10;
            <?php 
                endif;
            endforeach; 
            ?>
            
            doc.save('<?php echo addslashes($ficha['marca'] . '_' . $ficha['modelo'] . '_' . $ficha['ano']); ?>.pdf');
        }
        
        function shareVehicle() {
            const url = window.location.href;
            const title = '<?php echo addslashes($ficha['marca'] . ' ' . $ficha['modelo'] . ' ' . $ficha['ano']); ?> - SENAI Automotivo';
            
            if (navigator.share) {
                navigator.share({
                    title: title,
                    url: url
                });
            } else {
                // Fallback para copiar URL
                navigator.clipboard.writeText(url).then(() => {
                    alert('Link copiado para a área de transferência!');
                });
            }
        }
        
        // Scroll to top when page loads
        window.addEventListener('load', function() {
            window.scrollTo(0, 0);
        });
    </script>
</body>
</html>

