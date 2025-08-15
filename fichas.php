<?php
session_start();
require_once 'config.php';

$pdo = getDBConnection();

// Verificar se o usuário está logado
$isLoggedIn = isLoggedIn();
$userRole = $isLoggedIn ? $_SESSION['user_role'] : null;

// Configuração da paginação
$itensPorPagina = 9;
$paginaAtual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($paginaAtual - 1) * $itensPorPagina;

// Buscar carrocerias
$carrocerias = [];
$stmt = $pdo->query("SELECT * FROM carrocerias ORDER BY nome");
$carrocerias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar Montadoras para o filtro
$montadoras = [];
$stmt = $pdo->query("SELECT * FROM montadoras ORDER BY nome");
$montadoras = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt_count_by_montadora = $pdo->prepare("
    SELECT m.id, m.nome, COUNT(f.id) as total_veiculos 
    FROM montadoras m 
    LEFT JOIN fichas_tecnicas f ON m.id = f.montadoras_id 
    GROUP BY m.id, m.nome
");
$stmt_count_by_montadora->execute();
$contagem_por_montadora = [];
while ($row = $stmt_count_by_montadora->fetch(PDO::FETCH_ASSOC)) {
    $contagem_por_montadora[$row['id']] = $row['total_veiculos'];
}

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
} elseif (!empty($_GET['ano_inicio'])) {
    $whereConditions[] = "f.ano = ?";
    $params[] = sanitizeInput($_GET['ano_inicio']);
} elseif (!empty($_GET['ano_unico'])) {
    $whereConditions[] = "f.ano = ?";
    $params[] = sanitizeInput($_GET['ano_unico']);
}

if (!empty($_GET['potencia'])) {
    $potencia = sanitizeInput($_GET['potencia']);
    // Extrai apenas o primeiro número da string de potência
    $potenciaExpr = "CAST(REGEXP_SUBSTR(f.potencia, '[0-9]+') AS UNSIGNED)";
    if ($potencia === 'ate_100') {
        $whereConditions[] = "$potenciaExpr <= 100";
    } elseif ($potencia === '100_200') {
        $whereConditions[] = "$potenciaExpr >= 100 AND $potenciaExpr < 200";
    } elseif ($potencia === 'acima_200') {
        $whereConditions[] = "$potenciaExpr >= 200";
    }
}

// Construir SQL base
$sqlBase = "FROM fichas_tecnicas f 
            LEFT JOIN montadoras m ON f.montadoras_id = m.id
            LEFT JOIN carrocerias c ON f.carroceria_id = c.id";

$whereClause = "";
if (!empty($whereConditions)) {
    $whereClause = " WHERE " . implode(" AND ", $whereConditions);
}

// Contar total de registros
$sqlCount = "SELECT COUNT(*) " . $sqlBase . $whereClause;
$stmtCount = $pdo->prepare($sqlCount);
$stmtCount->execute($params);
$totalRegistros = $stmtCount->fetchColumn();

// Calcular total de páginas
$totalPaginas = ceil($totalRegistros / $itensPorPagina);

// Buscar fichas com paginação
$fichas = [];
$sql = "SELECT f.*, m.nome as montadoras_nome, c.nome as carroceria_nome " . 
       $sqlBase . $whereClause . 
       " ORDER BY m.nome, f.modelo, f.ano DESC LIMIT ? OFFSET ?";

$paramsWithPagination = array_merge($params, [$itensPorPagina, $offset]);
$stmt = $pdo->prepare($sql);
$stmt->execute($paramsWithPagination);
$fichas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Função para construir URL com parâmetros preservados
function buildPaginationUrl($pagina) {
    $params = $_GET;
    $params['pagina'] = $pagina;
    return 'fichas.php?' . http_build_query($params);
}

// Contar total de fichas no sistema (independente de filtros)
$stmtTotal = $pdo->query("SELECT COUNT(*) FROM fichas_tecnicas");
$totalFichasSistema = $stmtTotal->fetchColumn();

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fichas Técnicas - SENAI Automotivo</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/interface_style.css">
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
                    
                    <?php if ($isLoggedIn && $userRole === 'professor'): ?>
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
                
                <?php if ($isLoggedIn && $userRole === 'professor'): ?>
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

    <!-- Conteúdo principal -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Pesquisa e filtros -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold mb-6 senai-blue">Fichas Técnicas</h2>
            
            <form method="GET" class="space-y-4">
                <div class="search-bar">
                  <div class="search-input-wrapper">
                    <input type="text" name="busca" id="searchInput" placeholder="Pesquisar por modelo, montadora ou versão..." value="<?php echo htmlspecialchars($_GET['busca'] ?? ''); ?>" class="search-input">
                  </div>
                  <button type="submit" class="search-button">
                    <i class="fas fa-search"></i>
                    <span>Buscar</span>
                  </button>
                </div>

                
                
                <!-- Botão para exibir/esconder os filtros -->
                <button type="button" onclick="toggleFiltros()" class="mb-4 px-4 py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-sliders-h mr-2"></i>Filtros Avançados
                </button>
                
                <!-- Filtros Avançados (escondidos por padrão) -->
                <div id="filtrosAvancados" class="mb-6 hidden">
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ano</label>
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Potência</label>
                            <select name="potencia" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Qualquer</option>
                                <option value="ate_100" <?php echo (isset($_GET['potencia']) && $_GET['potencia'] == 'ate_100') ? 'selected' : ''; ?>>Até 100cv</option>
                                <option value="100_200" <?php echo (isset($_GET['potencia']) && $_GET['potencia'] == '100_200') ? 'selected' : ''; ?>>100-200cv</option>
                                <option value="acima_200" <?php echo (isset($_GET['potencia']) && $_GET['potencia'] == 'acima_200') ? 'selected' : ''; ?>>Acima de 200cv</option>
                            </select>
                        </div>
                    </div>
                
                    <!-- Botões de ação -->
                    <div class="mt-4 flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-filter mr-2"></i>Aplicar Filtros
                        </button>
                        <a href="fichas.php" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            <i class="fas fa-times mr-2"></i>Limpar Filtros
                        </a>
                    </div>
                </div>

                <!-- Filtros de Montadoras -->
                <div class="flex flex-wrap gap-2 mb-4">
                    <a href="fichas.php" class="filter-btn px-4 py-2 rounded-full bg-gray-200 text-gray-700 text-sm hover:bg-gray-300 transition-colors <?php echo empty($_GET['montadoras']) ? 'active bg-senai-blue text-white' : ''; ?>">
                        Todas (<?php echo $totalFichasSistema; ?>)
                    </a>
                    <?php foreach ($montadoras as $montadora): ?>
                        <a href="fichas.php?montadoras=<?php echo $montadora['id']; ?>" 
                           class="filter-btn px-4 py-2 rounded-full bg-gray-200 text-gray-700 text-sm hover:bg-gray-300 transition-colors <?php echo (isset($_GET['montadoras']) && $_GET['montadoras'] == $montadora['id']) ? 'active bg-senai-blue text-white' : ''; ?>">
                            <?php 
                                echo htmlspecialchars($montadora['nome']); 
                                echo ' (' . ($contagem_por_montadora[$montadora['id']] ?? 0) . ')';
                            ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </form>
        </div>
        
        <!-- Cars Grid -->
        <div class="cars-grid custom-mobile-grid">
            <?php if (empty($fichas)): ?>
                <div class="empty-state">
                    <i class="fas fa-search empty-state-icon"></i>
                    <p class="empty-state-text">Nenhum carro encontrado com os filtros selecionados.</p>
                    <a href="fichas.php" class="clear-filters-btn">
                        Limpar Filtros
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($fichas as $ficha): ?>
                    <div class="car-card">
                        <?php if ($ficha['imagem_path']): ?>
                            <div class="car-image-container">
                                <img src="<?php echo htmlspecialchars($ficha['imagem_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($ficha['montadoras_nome'] . ' ' . $ficha['modelo']); ?>" 
                                     class="car-image">
                            </div>
                        <?php else: ?>
                            <div class="car-image-placeholder">
                                <i class="fas fa-car car-image-placeholder-icon"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="car-card-content">
                            <div class="car-header">
                                <div>
                                    <h3 class="car-title">
                                        <?php echo htmlspecialchars($ficha['montadoras_nome'] . ' ' . $ficha['modelo']); ?>
                                    </h3>
                                    <p class="car-subtitle">
                                        <?php echo htmlspecialchars($ficha['ano']); ?>
                                    </p>
                                </div>
                                <?php if ($ficha['carroceria_nome']): ?>
                                    <span class="car-badge">
                                        <?php echo htmlspecialchars($ficha['carroceria_nome']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="car-details">

                                <?php if ($ficha['versao']): ?>
                                    <div class="car-detail-row">
                                        <span class="car-detail-label">Versão:</span>
                                        <span class="car-detail-value"><?php echo htmlspecialchars($ficha['versao']); ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if ($ficha['identificacaomotor']): ?>
                                    <div class="car-detail-row">
                                        <span class="car-detail-label">Motor:</span>
                                        <span class="car-detail-value"><?php echo htmlspecialchars($ficha['identificacaomotor']); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($ficha['potencia']): ?>
                                    <div class="car-detail-row">
                                        <span class="car-detail-label">Potência:</span>
                                        <span class="car-detail-value"><?php echo htmlspecialchars($ficha['potencia']); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($ficha['cambio']): ?>
                                    <div class="car-detail-row">
                                        <span class="car-detail-label">Câmbio:</span>
                                        <span class="car-detail-value"><?php echo htmlspecialchars($ficha['cambio']); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($ficha['combustivel']): ?>
                                    <div class="car-detail-row">
                                        <span class="car-detail-label">Combustível:</span>
                                        <span class="car-detail-value"><?php echo htmlspecialchars($ficha['combustivel']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <a href="detalhes.php?id=<?php echo $ficha['id']; ?>" class="details-btn">
                                Ver Detalhes
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Paginação -->
        <?php if ($totalPaginas > 1): ?>
                <div class="pagination">
                    <!-- Botão Anterior -->
                    <?php if ($paginaAtual > 1): ?>
                        <a href="<?php echo buildPaginationUrl($paginaAtual - 1); ?>" class="pagination-btn">
                            <i class="fas fa-chevron-left"></i>
                            <span class="pagination-text ml-1">Anterior</span>
                        </a>
                    <?php else: ?>
                        <span class="pagination-btn" style="opacity: 0.5; cursor: not-allowed;">
                            <i class="fas fa-chevron-left"></i>
                            <span class="pagination-text ml-1">Anterior</span>
                        </span>
                    <?php endif; ?>

                    <!-- Números das páginas -->
                    <?php
                    $inicio = max(1, $paginaAtual - 2);
                    $fim = min($totalPaginas, $paginaAtual + 2);
                    
                    // Ajustar para sempre mostrar 5 páginas quando possível
                    if ($fim - $inicio < 4) {
                        if ($inicio == 1) {
                            $fim = min($totalPaginas, $inicio + 4);
                        } else {
                            $inicio = max(1, $fim - 4);
                        }
                    }
                    
                    // Primeira página se não estiver no range
                    if ($inicio > 1): ?>
                        <a href="<?php echo buildPaginationUrl(1); ?>" class="pagination-btn">1</a>
                        <?php if ($inicio > 2): ?>
                            <span class="pagination-btn" style="cursor: default;">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Páginas do range -->
                    <?php for ($i = $inicio; $i <= $fim; $i++): ?>
                        <?php if ($i == $paginaAtual): ?>
                            <span class="pagination-btn active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="<?php echo buildPaginationUrl($i); ?>" class="pagination-btn"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <!-- Última página se não estiver no range -->
                    <?php if ($fim < $totalPaginas): ?>
                        <?php if ($fim < $totalPaginas - 1): ?>
                            <span class="pagination-btn" style="cursor: default;">...</span>
                        <?php endif; ?>
                        <a href="<?php echo buildPaginationUrl($totalPaginas); ?>" class="pagination-btn"><?php echo $totalPaginas; ?></a>
                    <?php endif; ?>

                    <!-- Botão Próximo -->
                    <?php if ($paginaAtual < $totalPaginas): ?>
                        <a href="<?php echo buildPaginationUrl($paginaAtual + 1); ?>" class="pagination-btn">
                            <span class="pagination-text mr-1">Próximo</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php else: ?>
                        <span class="pagination-btn" style="opacity: 0.5; cursor: not-allowed;">
                            <span class="pagination-text mr-1">Próximo</span>
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
    </div>

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

