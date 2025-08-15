<?php
session_start();
require_once "config.php";

$pdo = getDBConnection();

// Verificar se o usuário está logado
$isLoggedIn = isLoggedIn();
$userRole = $isLoggedIn ? $_SESSION["user_role"] : null;

// Buscar montadoras para o carrossel
$stmt_montadoras = $pdo->query("SELECT * FROM montadoras ORDER BY id DESC");
$montadoras = $stmt_montadoras->fetchAll(PDO::FETCH_ASSOC);

// Buscar fichas em destaque (as 6 mais recentes com imagem)
$stmt_destaques = $pdo->query("
    SELECT f.*, m.nome as montadoras_nome 
    FROM fichas_tecnicas f 
    LEFT JOIN montadoras m ON f.montadoraS_id = m.id 
    WHERE f.imagem_path IS NOT NULL 
    ORDER BY f.id DESC 
    LIMIT 6
");
$fichas_destaque = $stmt_destaques->fetchAll(PDO::FETCH_ASSOC);

// Se não houver fichas com imagem, buscar as mais recentes sem imagem
if (empty($fichas_destaque)) {
    $stmt_destaques = $pdo->query("
        SELECT f.*, m.nome as montadoras_nome 
        FROM fichas_tecnicas f 
        LEFT JOIN montadoras m ON f.montadoras_id = m.id 
        ORDER BY f.id DESC 
        LIMIT 6
    ");
    $fichas_destaque = $stmt_destaques->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENAI Automotivo - Fichas Técnicas</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
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
                    <a href="index.php" class="active">Início</a>
                    <a href="fichas.php">Fichas Técnicas</a>
                    
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
                <a href="index.php" class="active">Início</a>
                <a href="fichas.php">Fichas Técnicas</a>
                
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

    <section class="search-container">
  <div class="background-image"></div>
  <div class="content-wrapper">
    <h1 class="title">
      Sistema de Fichas Técnicas Automotivas
    </h1>
    <p class="subtitle">
      Explore o mundo da tecnologia automotiva com o SENAI
    </p>
    <a href="fichas.php" class="btn-primary">
      Ver Fichas Técnicas
    </a>
  </div>
</section>

<!-- Seção Carrossel de Marcas -->
<section class="py-16 bg-gray-50">
  <div class="max-w-7xl mx-auto px-4">
    <h2 class="text-3xl font-bold text-center mb-12 text-senai-blue">Montadoras Cadastradas</h2>

    <?php if (!empty($montadoras)): ?>
      <div class="relative">
        <!-- Botão Esquerda -->
        <button id="prevBtn" class="absolute left-0 top-1/3 -translate-y-1/2 z-10 bg-white border w-9 h-9 rounded-full flex items-center justify-center shadow-md hover:bg-gray-100 transition-all">
          <i class="fas fa-chevron-left text-xl text-gray-600"></i>
        </button>

        <!-- Botão Direita -->
        <button id="nextBtn" class="absolute right-0 top-1/3 -translate-y-1/2 z-10 bg-white border w-9 h-9 rounded-full flex items-center justify-center shadow-md hover:bg-gray-100 transition-all">
          <i class="fas fa-chevron-right text-xl text-gray-600"></i>
        </button>

        <!-- Carrossel -->
        <div class="overflow-hidden">
          <div id="carouselTrack" class="flex space-x-2 transition-transform duration-300 ease-in-out px-1">
            <?php foreach ($montadoras as $montadora): ?>
              <div class="carousel-item flex-shrink-0 w-[150px] sm:w-1/2 lg:w-1/6">
                <a href="fichas.php?montadoras=<?= $montadora['id']; ?>" 
                   class="text-center p-4 bg-white rounded-xl shadow hover:shadow-lg transition-all cursor-pointer w-40 sm:w-48 block">
                  <?php if (!empty($montadora['imagem_path'])): ?>
                    <img
                      src="<?= htmlspecialchars($montadora['imagem_path']); ?>"
                      alt="<?= htmlspecialchars($montadora['nome']); ?>"
                      class="object-contain w-full h-20 mx-auto mb-3">
                  <?php else: ?>
                    <div class="w-full h-20 bg-gray-200 rounded flex items-center justify-center mx-auto mb-3">
                      <i class="fas fa-car text-2xl text-gray-400"></i>
                    </div>
                  <?php endif; ?>
                  <h3 class="font-semibold text-gray-800 text-sm truncate">
                    <?= htmlspecialchars($montadora['nome']); ?>
                  </h3>
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    <?php else: ?>
      <div class="text-center py-12">
        <i class="fas fa-car text-6xl text-gray-300 mb-4"></i>
        <p class="text-gray-600 text-lg">Nenhuma montadora cadastrada ainda.</p>
        <?php if ($isLoggedIn && $userRole === "professor"): ?>
          <a href="montadoras.php" class="mt-4 px-6 py-3 bg-senai-blue text-white rounded-lg hover:bg-blue-700 transition-colors inline-block">
            Cadastrar Primeira Montadora
          </a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</section>


    
    <!-- Seção de carros em destaque -->
    <section class="py-16 bg-gray-100">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12 senai-blue">Blogs Automotivos</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidde">
                <img src="images/blog_automotivo_1.jpg" alt="Carro Esportivo Azul" class="w-full h-48 object-cover rounded-t-2xl">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Notícias Automotivas</h3>
                    <p class="text-gray-600 mb-4">Um dos maiores sites automotivos do Brasil, com notícias atualizadas sobre lançamentos, testes e novidades do setor.</p>
                    <a href="https://www.noticiasautomotivas.com.br/" class="block w-full py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-800 transition-colors text-center">
                       Visitar Blog
                    </a>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg overflow-hidde">
                <img src="images/blog_automotivo_2.jpg" alt="Carro Azul" class="w-full h-48 object-cover rounded-t-2xl">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Motor1 Brasil</h3>
                    <p class="text-gray-600 mb-4">Portal especializado em avaliações, comparativos e notícias exclusivas do mundo automotivo, com foco em tecnologia e inovação.</p>
                    <a href="https://motor1.uol.com.br/" class="block w-full py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-800 transition-colors text-center">
                        Visitar Blog
                    </a>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg overflow-hidde">
                <img src="images/blog_automotivo_3.jpg" alt="Carro com Luzes Neon" class="w-full h-48 object-cover rounded-t-2xl">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">AutoPapo</h3>
                    <p class="text-gray-600 mb-4">Blog especializado em mobilidade sustentável, veículos elétricos e híbridos, com análises técnicas e dicas de manutenção.</p>
                    <a href="https://autopapo.com.br/" class="block w-full py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-800 transition-colors text-center">
                        Visitar Blog
                    </a>
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

