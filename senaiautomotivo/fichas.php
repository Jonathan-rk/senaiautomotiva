<?php
session_start();
require_once 'config.php';

$pdo = getDBConnection();

// Verificar se o usuário está logado
$isLoggedIn = isLoggedIn();
$userRole = $isLoggedIn ? $_SESSION['user_role'] : null;

// Buscar carrocerias
$carrocerias = [];
$stmt = $pdo->query("SELECT * FROM carrocerias ORDER BY nome");
$carrocerias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar Montadoras para o filtro
$montadoras = [];
$stmt = $pdo->query("SELECT * FROM montadoras ORDER BY nome");
$montadoras = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar anos únicos para o filtro
$anosUnicos = [];
$stmt = $pdo->query("SELECT DISTINCT ano FROM fichas_tecnicas ORDER BY ano DESC");
$anosUnicos = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Buscar potências únicas para o filtro
$potenciasUnicas = [];
$stmt = $pdo->query("SELECT DISTINCT potencia FROM fichas_tecnicas WHERE potencia IS NOT NULL AND potencia != '' ORDER BY potencia");
$potenciasUnicas = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Filtros
$whereConditions = [];
$params = [];

if (!empty($_GET['busca'])) {
    $busca = '%' . sanitizeInput($_GET['busca']) . '%';
    $whereConditions[] = "(m.nome LIKE ? OR f.modelo LIKE ? OR f.aspiracao LIKE ?)";
    $params[] = $busca;
    $params[] = $busca;
    $params[] = $busca;
}

if (!empty($_GET['carroceria'])) {
    $whereConditions[] = "f.carroceria_id = ?";
    $params[] = sanitizeInput($_GET['carroceria']);
}

if (!empty($_GET['montadoras'])) {
    $whereConditions[] = "f.montadoras_id = ?";
    $params[] = sanitizeInput($_GET['montadoras']);
}

if (!empty($_GET['combustivel'])) {
    $whereConditions[] = "f.combustivel = ?";
    $params[] = sanitizeInput($_GET['combustivel']);
}

if (!empty($_GET['ano_inicio']) && !empty($_GET['ano_fim'])) {
    $whereConditions[] = "f.ano BETWEEN ? AND ?";
    $params[] = sanitizeInput($_GET['ano_inicio']);
    $params[] = sanitizeInput($_GET['ano_fim']);
} elseif (!empty($_GET['ano_unico'])) {
    $whereConditions[] = "f.ano = ?";
    $params[] = sanitizeInput($_GET['ano_unico']);
}

if (!empty($_GET['potencia'])) {
    $potencia = sanitizeInput($_GET['potencia']);
    if ($potencia === 'ate_100') {
        $whereConditions[] = "CAST(REGEXP_REPLACE(f.potencia, '[^0-9]', '') AS UNSIGNED) <= 100";
    } elseif ($potencia === '100_200') {
        $whereConditions[] = "CAST(REGEXP_REPLACE(f.potencia, '[^0-9]', '') AS UNSIGNED) BETWEEN 100 AND 200";
    } elseif ($potencia === 'acima_200') {
        $whereConditions[] = "CAST(REGEXP_REPLACE(f.potencia, '[^0-9]', '') AS UNSIGNED) > 200";
    }
}

// Buscar fichas
$fichas = [];
$sql = "SELECT f.*, m.nome as montadoras_nome, c.nome as carroceria_nome 
        FROM fichas_tecnicas f 
        LEFT JOIN montadoras m ON f.montadoras_id = m.id
        LEFT JOIN carrocerias c ON f.carroceria_id = c.id";

if (!empty($whereConditions)) {
    $sql .= " WHERE " . implode(" AND ", $whereConditions);
}

$sql .= " ORDER BY m.nome, f.modelo, f.ano DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$fichas = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fichas Técnicas - SENAI Automotivo</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <link rel="stylesheet" href="interface_style.css">
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

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Search and Filters -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold mb-6 senai-blue">Fichas Técnicas</h2>
            
            <form method="GET" class="space-y-4">
                <div class="flex flex-col md:flex-row gap-4 mb-6">
                    <div class="flex-1">
                        <input type="text" name="busca" id="searchInput" placeholder="Pesquisar por modelo, montadora ou versão..." 
                               value="<?php echo htmlspecialchars($_GET['busca'] ?? ''); ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <button type="submit" class="px-6 py-3 bg-senai-blue text-white rounded-lg hover:bg-blue-800 transition-colors">
                        <i class="fas fa-search mr-2"></i>Buscar
                    </button>
                </div>
                
                <!-- Filtros Avançados -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">Filtros por Características</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Carroceria</label>
                            <select name="carroceria" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Todas as carrocerias</option>
                                <?php foreach ($carrocerias as $carroceria): ?>
                                    <option value="<?php echo $carroceria['id']; ?>" 
                                            <?php echo (isset($_GET['carroceria']) && $_GET['carroceria'] == $carroceria['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($carroceria['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Montadoras</label>
                            <select name="montadoras" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Todas as montadoras</option>
                                <?php foreach ($montadoras as $montadora): ?>
                                    <option value="<?php echo $montadora['id']; ?>" 
                                            <?php echo (isset($_GET['montadoras']) && $_GET['montadoras'] == $montadora['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($montadora['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Combustível</label>
                            <select name="combustivel" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Todos</option>
                                <option value="Flex" <?php echo (isset($_GET['combustivel']) && $_GET['combustivel'] == 'Flex') ? 'selected' : ''; ?>>Flex</option>
                                <option value="Gasolina" <?php echo (isset($_GET['combustivel']) && $_GET['combustivel'] == 'Gasolina') ? 'selected' : ''; ?>>Gasolina</option>
                                <option value="Diesel" <?php echo (isset($_GET['combustivel']) && $_GET['combustivel'] == 'Diesel') ? 'selected' : ''; ?>>Diesel</option>
                                <option value="Híbrido" <?php echo (isset($_GET['combustivel']) && $_GET['combustivel'] == 'Híbrido') ? 'selected' : ''; ?>>Híbrido</option>
                                <option value="Elétrico" <?php echo (isset($_GET['combustivel']) && $_GET['combustivel'] == 'Elétrico') ? 'selected' : ''; ?>>Elétrico</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ano Inicial</label>
                            <select name="ano_inicio" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Qualquer</option>
                                <?php foreach ($anosUnicos as $ano): ?>
                                    <option value="<?php echo $ano; ?>" 
                                            <?php echo (isset($_GET['ano_inicio']) && $_GET['ano_inicio'] == $ano) ? 'selected' : ''; ?>>
                                        <?php echo $ano; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ano Final</label>
                            <select name="ano_fim" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Qualquer</option>
                                <?php foreach ($anosUnicos as $ano): ?>
                                    <option value="<?php echo $ano; ?>" 
                                            <?php echo (isset($_GET['ano_fim']) && $_GET['ano_fim'] == $ano) ? 'selected' : ''; ?>>
                                        <?php echo $ano; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Potência</label>
                            <select name="potencia" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Qualquer</option>
                                <option value="ate_100" <?php echo (isset($_GET['potencia']) && $_GET['potencia'] == 'ate_100') ? 'selected' : ''; ?>>Até 100cv</option>
                                <option value="100_200" <?php echo (isset($_GET['potencia']) && $_GET['potencia'] == '100_200') ? 'selected' : ''; ?>>100-200cv</option>
                                <option value="acima_200" <?php echo (isset($_GET['potencia']) && $_GET['potencia'] == 'acima_200') ? 'selected' : ''; ?>>Acima de 200cv</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-filter mr-2"></i>Aplicar Filtros
                        </button>
                        <a href="fichas.php" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            <i class="fas fa-times mr-2"></i>Limpar Filtros
                        </a>
                    </div>
                </div>
                
                <!-- Brand Filters -->
                <div class="flex flex-wrap gap-2">
                    <a href="fichas.php" class="filter-btn px-4 py-2 rounded-full bg-gray-200 text-gray-700 text-sm hover:bg-gray-300 transition-colors <?php echo empty($_GET['montadoras']) ? 'active bg-senai-blue text-white' : ''; ?>">
                        Todas
                    </a>                    
                    <?php foreach ($montadoras as $montadora): ?>
                        <a href="fichas.php?montadoras=<?php echo $montadora['id']; ?>" class="filter-btn px-4 py-2 rounded-full bg-gray-200 text-gray-700 text-sm hover:bg-gray-300 transition-colors <?php echo (isset($_GET['montadoras']) && $_GET['montadoras'] == $montadora['id']) ? 'active bg-senai-blue text-white' : ''; ?>">
                            <?php echo htmlspecialchars($montadora['nome']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </form>
        </div>

        <!-- Results -->
        <div class="mb-6">
            <p class="text-gray-600">
                <?php echo count($fichas); ?> ficha(s) encontrada(s)
                <?php if (!empty($_GET['busca']) || !empty($_GET['carroceria']) || !empty($_GET['montadoras']) || !empty($_GET['combustivel']) || !empty($_GET['ano_inicio']) || !empty($_GET['potencia'])): ?>
                    para os filtros aplicados
                <?php endif; ?>
            </p>
        </div>

        <!-- Cars Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($fichas)): ?>
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600">Nenhum carro encontrado com os filtros selecionados.</p>
                    <a href="fichas.php" class="mt-4 px-4 py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-700 transition-colors inline-block">
                        Limpar Filtros
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($fichas as $ficha): ?>
                    <div class="car-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                        <?php if ($ficha['imagem_path']): ?>
                            <div class="h-48 bg-gray-200 overflow-hidden">
                                <img src="<?php echo htmlspecialchars($ficha['imagem_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($ficha['montadoras_nome'] . ' ' . $ficha['modelo']); ?>" 
                                     class="w-full h-full object-cover">
                            </div>
                        <?php else: ?>
                            <div class="h-48 bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-car text-4xl text-gray-400"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">
                                        <?php echo htmlspecialchars($ficha['montadoras_nome'] . ' ' . $ficha['modelo']); ?>
                                    </h3>
                                    <p class="text-gray-600">
                                        <?php echo htmlspecialchars($ficha['ano']); ?>
                                        <?php if ($ficha['modelo']): ?>
                                            - <?php echo htmlspecialchars($ficha['modelo']); ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <?php if ($ficha['carroceria_nome']): ?>
                                    <span class="bg-senai-blue text-white text-sm px-3 py-1 rounded-full">
                                        <?php echo htmlspecialchars($ficha['carroceria_nome']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="space-y-2 mb-6">
                                <?php if ($ficha['identificacaomotor']): ?>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Motor:</span>
                                        <span class="font-medium"><?php echo htmlspecialchars($ficha['identificacaomotor']); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($ficha['potencia']): ?>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Potência:</span>
                                        <span class="font-medium"><?php echo htmlspecialchars($ficha['potencia']); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($ficha['cambio']): ?>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Câmbio:</span>
                                        <span class="font-medium"><?php echo htmlspecialchars($ficha['cambio']); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($ficha['combustivel']): ?>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Combustível:</span>
                                        <span class="font-medium"><?php echo htmlspecialchars($ficha['combustivel']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <a href="detalhes.php?id=<?php echo $ficha['id']; ?>" class="block w-full text-center px-4 py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                                Ver Detalhes
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

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

