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
    $pdo = null;
}

// Buscar categorias
$categorias = [];
if ($pdo) {
    $stmt = $pdo->query("SELECT * FROM categorias ORDER BY nome");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Filtros
$whereConditions = [];
$params = [];

if (!empty($_GET['busca'])) {
    $whereConditions[] = "(marca LIKE ? OR modelo LIKE ? OR versao LIKE ?)";
    $busca = '%' . $_GET['busca'] . '%';
    $params[] = $busca;
    $params[] = $busca;
    $params[] = $busca;
}

if (!empty($_GET['categoria'])) {
    $whereConditions[] = "categoria_id = ?";
    $params[] = $_GET['categoria'];
}

if (!empty($_GET['marca'])) {
    $whereConditions[] = "marca = ?";
    $params[] = $_GET['marca'];
}

if (!empty($_GET['combustivel'])) {
    $whereConditions[] = "tipo_combustivel = ?";
    $params[] = $_GET['combustivel'];
}

// Buscar fichas
$fichas = [];
if ($pdo) {
    $sql = "SELECT f.*, c.nome as categoria_nome FROM fichas_tecnicas f 
            LEFT JOIN categorias c ON f.categoria_id = c.id";
    
    if (!empty($whereConditions)) {
        $sql .= " WHERE " . implode(" AND ", $whereConditions);
    }
    
    $sql .= " ORDER BY f.marca, f.modelo, f.ano DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $fichas = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fichas Técnicas - SENAI Automotivo</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
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
                    <a href="fichas.php" class="text-gray-700 hover:text-blue-600 transition-colors">Fichas Técnicas</a>
                    
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'professor'): ?>
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
                <a href="fichas.php" class="block py-2 text-gray-700">Fichas Técnicas</a>
                
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'professor'): ?>
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

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Search and Filters -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold mb-6 senai-blue">Fichas Técnicas</h2>
            
            <form method="GET" class="space-y-4">
                <div class="flex flex-col md:flex-row gap-4 mb-6">
                    <div class="flex-1">
                        <input type="text" name="busca" id="searchInput" placeholder="Pesquisar por modelo, marca ou ano..." 
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
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                            <select name="categoria" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Todas as categorias</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?php echo $categoria['id']; ?>" 
                                            <?php echo ($_GET['categoria'] ?? '') == $categoria['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($categoria['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                            <select name="marca" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Todas as marcas</option>
                                <option value="Chevrolet" <?php echo ($_GET['marca'] ?? '') == 'Chevrolet' ? 'selected' : ''; ?>>Chevrolet</option>
                                <option value="Volkswagen" <?php echo ($_GET['marca'] ?? '') == 'Volkswagen' ? 'selected' : ''; ?>>Volkswagen</option>
                                <option value="Honda" <?php echo ($_GET['marca'] ?? '') == 'Honda' ? 'selected' : ''; ?>>Honda</option>
                                <option value="Toyota" <?php echo ($_GET['marca'] ?? '') == 'Toyota' ? 'selected' : ''; ?>>Toyota</option>
                                <option value="Jeep" <?php echo ($_GET['marca'] ?? '') == 'Jeep' ? 'selected' : ''; ?>>Jeep</option>
                                <option value="Ford" <?php echo ($_GET['marca'] ?? '') == 'Ford' ? 'selected' : ''; ?>>Ford</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Combustível</label>
                            <select name="combustivel" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Todos</option>
                                <option value="Flex" <?php echo ($_GET['combustivel'] ?? '') == 'Flex' ? 'selected' : ''; ?>>Flex</option>
                                <option value="Gasolina" <?php echo ($_GET['combustivel'] ?? '') == 'Gasolina' ? 'selected' : ''; ?>>Gasolina</option>
                                <option value="Diesel" <?php echo ($_GET['combustivel'] ?? '') == 'Diesel' ? 'selected' : ''; ?>>Diesel</option>
                                <option value="Híbrido" <?php echo ($_GET['combustivel'] ?? '') == 'Híbrido' ? 'selected' : ''; ?>>Híbrido</option>
                                <option value="Elétrico" <?php echo ($_GET['combustivel'] ?? '') == 'Elétrico' ? 'selected' : ''; ?>>Elétrico</option>
                            </select>
                        </div>
                        
                        <div class="flex items-end">
                            <a href="fichas.php" class="w-full px-3 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-center">
                                <i class="fas fa-times mr-2"></i>Limpar Filtros
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Results -->
        <div class="mb-6">
            <p class="text-gray-600">
                <?php echo count($fichas); ?> ficha(s) encontrada(s)
                <?php if (!empty($_GET['busca']) || !empty($_GET['categoria']) || !empty($_GET['marca']) || !empty($_GET['combustivel'])): ?>
                    para os filtros aplicados
                <?php endif; ?>
            </p>
        </div>

        <!-- Cars Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($fichas)): ?>
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-car text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Nenhuma ficha encontrada</h3>
                    <p class="text-gray-500">Tente ajustar os filtros ou fazer uma nova busca.</p>
                </div>
            <?php else: ?>
                <?php foreach ($fichas as $ficha): ?>
                    <div class="car-card bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">
                                        <?php echo htmlspecialchars($ficha['marca'] . ' ' . $ficha['modelo']); ?>
                                    </h3>
                                    <p class="text-gray-600">
                                        <?php echo htmlspecialchars($ficha['ano']); ?>
                                        <?php if ($ficha['versao']): ?>
                                            - <?php echo htmlspecialchars($ficha['versao']); ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <?php if ($ficha['categoria_nome']): ?>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                        <?php echo htmlspecialchars($ficha['categoria_nome']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="space-y-2 mb-4">
                                <?php if ($ficha['tipo_motor']): ?>
                                    <div class="spec-item bg-gray-50 p-3 rounded-lg">
                                        <span class="font-medium text-gray-700">Motor:</span>
                                        <span class="text-gray-900"><?php echo htmlspecialchars($ficha['tipo_motor']); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($ficha['potencia_maxima']): ?>
                                    <div class="spec-item bg-gray-50 p-3 rounded-lg">
                                        <span class="font-medium text-gray-700">Potência:</span>
                                        <span class="text-gray-900"><?php echo htmlspecialchars($ficha['potencia_maxima']); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($ficha['tipo_combustivel']): ?>
                                    <div class="spec-item bg-gray-50 p-3 rounded-lg">
                                        <span class="font-medium text-gray-700">Combustível:</span>
                                        <span class="text-gray-900"><?php echo htmlspecialchars($ficha['tipo_combustivel']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="flex gap-2">
                                <a href="detalhes.php?id=<?php echo $ficha['id']; ?>" 
                                   class="flex-1 bg-senai-blue text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition-colors text-center">
                                    <i class="fas fa-eye mr-2"></i>Ver Detalhes
                                </a>
                                <a href="gerar_pdf.php?id=<?php echo $ficha['id']; ?>" 
                                   class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors" 
                                   target="_blank">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-senai-dark text-white py-16 mt-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> SENAI Automotivo. Todos os direitos reservados.</p>
                <p class="mt-2 text-sm opacity-75">
                    Sistema de fichas técnicas automotivas
                </p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html>

