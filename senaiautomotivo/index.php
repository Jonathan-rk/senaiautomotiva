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
    <link rel="stylesheet" href="interface_style.css">
    <style>
        .carousel-container {
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .carousel-container::-webkit-scrollbar {
            display: none;
        }
        .carousel-track {
            display: flex;
            gap: 1rem;
            padding: 0.5rem 0;
        }
        .carousel-item {
            flex: 0 0 auto;
            width: 200px;
        }
        
        @media (max-width: 768px) {
            .carousel-item {
                width: 150px;
            }
        }
        
        .featured-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .featured-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
        }
        
        .featured-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .featured-card .content {
            padding: 1.5rem;
        }
        
        .featured-card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        
        .featured-card p {
            color: #6b7280;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            line-height: 1.5;
        }
        
        .featured-card .btn {
            background: #1e40af;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
            display: inline-block;
            width: 100%;
            text-align: center;
        }
        
        .featured-card .btn:hover {
            background: #1d4ed8;
        }
    </style>
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
                    <a href="index.php" class="text-blue-600 font-semibold">Início</a>
                    <a href="fichas.php" class="text-gray-700 hover:text-blue-600 transition-colors">Fichas Técnicas</a>
                    
                    <?php if ($isLoggedIn && $userRole === "professor"): ?>
                        <a href="painel_professor.php" class="text-gray-700 hover:text-blue-600 transition-colors">
                            <i class="fas fa-cog mr-2"></i>Painel
                        </a>
                        <a href="montadoras.php" class="text-gray-700 hover:text-blue-600 transition-colors">
                            <i class="fas fa-car mr-2"></i>Montadoras
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
                <a href="index.php" class="block py-2 text-blue-600 font-semibold">Início</a>
                <a href="fichas.php" class="block py-2 text-gray-700">Fichas Técnicas</a>
                
                <?php if ($isLoggedIn && $userRole === "professor"): ?>
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

    <!-- Hero Section -->
    <section class="search-container text-white py-32">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Sistema de Fichas Técnicas Automotivas
            </h1>
            <p class="text-xl mb-8 opacity-90">
                Explore o mundo da tecnologia automotiva com o SENAI
            </p>
            <a href="fichas.php" class="bg-white text-blue-600 px-8 py-4 rounded-xl font-semibold text-lg shadow-md hover:bg-blue-700 hover:text-white hover:scale-105 transition-all duration-300 ease-in-out">
                Ver Fichas Técnicas
            </a>
        </div>
    </section>

    <!-- Brands Carousel Section -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12 senai-blue">Montadoras Cadastradas</h2>

            <?php if (!empty($montadoras)): ?>
                <div class="carousel-container">
                    <div class="carousel-track">
                        <?php foreach ($montadoras as $montadora): ?>
                            <div class="carousel-item">
                                <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all cursor-pointer"
                                     onclick="window.location.href='fichas.php?montadora=<?php echo $montadora['id']; ?>'">
                                    <?php if ($montadora['imagem_path']): ?>
                                        <img src="<?php echo htmlspecialchars($montadora['imagem_path']); ?>"
                                             alt="<?php echo htmlspecialchars($montadora['nome']); ?>"
                                             class="object-contain w-full h-20 mx-auto mb-4">
                                    <?php else: ?>
                                        <div class="w-full h-20 bg-gray-200 rounded flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-car text-2xl text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>
                                    <h3 class="font-semibold text-gray-800 text-sm"><?php echo htmlspecialchars($montadora['nome']); ?></h3>
                                </div>
                            </div>
                        <?php endforeach; ?>
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
    
    <!-- Featured Cars Section -->
    <section class="py-16 bg-gray-100">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12 senai-blue">Veículos em Destaque</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <img src="images/carros.esportivo.jpg" alt="Carro Esportivo Azul" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Modelos Esportivos</h3>
                    <p class="text-gray-600 mb-4">Conheça os veículos de alto desempenho com fichas técnicas detalhadas.</p>
                    <a href="categoria.php?tipo=esportivo" class="block w-full py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-800 transition-colors text-center">
                        Explorar
                    </a>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <img src="images/chevrolet-onix-sedan-2020-1.jpg.webp" alt="Carro Azul" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Sedans Premium</h3>
                    <p class="text-gray-600 mb-4">Descubra os sedans de luxo com tecnologia de ponta e conforto superior.</p>
                    <a href="categoria.php?tipo=sedan" class="block w-full py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-800 transition-colors text-center">
                        Explorar
                    </a>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <img src="images/2f0b2b_64065baf27a2434b90c787b711045175~mv2.avif" alt="Carro com Luzes Neon" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Tecnologia Avançada</h3>
                    <p class="text-gray-600 mb-4">Veículos com as mais recentes inovações tecnológicas do mercado automotivo.</p>
                    <a href="categoria.php?tipo=tecnologico" class="block w-full py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-800 transition-colors text-center">
                        Explorar
                    </a>
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
            const menu = document.getElementById("mobileMenu");
            menu.classList.toggle("hidden");
        }
        
        // Auto-scroll carousel
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.querySelector('.carousel-container');
            if (carousel) {
                let isScrolling = false;
                let scrollDirection = 1;
                
                function autoScroll() {
                    if (!isScrolling) {
                        const maxScroll = carousel.scrollWidth - carousel.clientWidth;
                        
                        if (carousel.scrollLeft >= maxScroll) {
                            scrollDirection = -1;
                        } else if (carousel.scrollLeft <= 0) {
                            scrollDirection = 1;
                        }
                        
                        carousel.scrollBy({
                            left: scrollDirection * 2,
                            behavior: 'smooth'
                        });
                    }
                }
                
                // Auto-scroll every 50ms for smooth movement
                const scrollInterval = setInterval(autoScroll, 50);
                
                // Pause auto-scroll on hover
                carousel.addEventListener('mouseenter', () => {
                    isScrolling = true;
                });
                
                carousel.addEventListener('mouseleave', () => {
                    isScrolling = false;
                });
                
                // Pause auto-scroll on manual scroll
                carousel.addEventListener('scroll', () => {
                    isScrolling = true;
                    setTimeout(() => {
                        isScrolling = false;
                    }, 2000);
                });
            }
        });
    </script>
</body>
</html>

