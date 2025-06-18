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
    <link rel="stylesheet" href="interface_style.css">
    <style>

        .overflow-hidden{
            height: 150px;
        }
       /* Carousel Container */
.carousel-container {
    position: relative;
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 60px;
}

/* Navigation Buttons */
.carousel-button {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: white;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    z-index: 10;
    cursor: pointer;
    transition: all 0.3s ease;
}

.carousel-button:hover {
    background: #f3f4f6;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.carousel-button.prev {
    left: 0;
}

.carousel-button.next {
    right: 0;
}

/* Carousel Track */
.carousel-track {
    display: flex;
    gap: 24px;
    transition: transform 0.3s ease;
}

/* Carousel Items */
.carousel-item {
    flex: 0 0 auto;
    width: calc(100% / 6);
    min-width: 200px;
}

/* Card Styling */
.brand-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    height: 220px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.brand-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
}

/* Image Container */
.brand-image-container {
    height: 140px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
}

.brand-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

/* Brand Title */
.brand-title {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    text-align: center;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Responsive Adjustments */
@media (max-width: 1024px) {
    .carousel-item {
        width: calc(100% / 4);
    }
}

@media (max-width: 768px) {
    .carousel-item {
        width: calc(100% / 3);
    }
    .carousel-container {
        padding: 0 40px;
    }
    .overflow-hidden {
        height: 150px; 
    }
}

@media (max-width: 640px) {
    .carousel-item {
        width: calc(100% / 2);
    }
    .carousel-container {
        padding: 0 30px;
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
<section class="py-16 bg-gray-50">
  <div class="max-w-7xl mx-auto px-4">
    <h2 class="text-3xl font-bold text-center mb-12 text-senai-blue">Montadoras Cadastradas</h2>

    <?php if (!empty($montadoras)): ?>
      <div class="relative">
        <!-- Botão Esquerda -->
        <button id="prevBtn" class="absolute left-0 top-1/3 -translate-y-1/2 z-10 bg-white border w-12 h-12 rounded-full p-3 shadow-md hover:bg-gray-100 transition-all">
          <i class="fas fa-chevron-left text-xl text-gray-600"></i>
        </button>

        <!-- Botão Direita -->
        <button id="nextBtn" class="absolute right-0 top-1/3 -translate-y-1/2 z-10 bg-white border w-12 h-12 rounded-full flex items-center justify-center shadow-md hover:bg-gray-100 transition-all">
          <i class="fas fa-chevron-right text-xl text-gray-600"></i>
        </button>

        <!-- Carrossel -->
        <div class="overflow-hidden">
          <div id="carouselTrack" class="flex space-x-2 transition-transform duration-300 ease-in-out px-1">
            <?php foreach ($montadoras as $montadora): ?>
              <div class="carousel-item flex-shrink-0 w-[150px] sm:w-1/2 lg:w-1/6">
                <div class="text-center p-4 bg-white rounded-xl shadow hover:shadow-lg transition-all cursor-pointer w-40 sm:w-48"
                  onclick="window.location.href='fichas.php?montadora=<?php echo $montadora['id']; ?>'">
                  <?php if ($montadora['imagem_path']): ?>
                    <img src="<?php echo htmlspecialchars($montadora['imagem_path']); ?>"
                      alt="<?php echo htmlspecialchars($montadora['nome']); ?>"
                      class="object-contain w-full h-20 mx-auto mb-3">
                  <?php else: ?>
                    <div class="w-full h-20 bg-gray-200 rounded flex items-center justify-center mx-auto mb-3">
                      <i class="fas fa-car text-2xl text-gray-400"></i>
                    </div>
                  <?php endif; ?>
                  <h3 class="font-semibold text-gray-800 text-sm truncate"><?php echo htmlspecialchars($montadora['nome']); ?></h3>
                </div>
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
          <a href="montadoras.php"
            class="mt-4 px-6 py-3 bg-senai-blue text-white rounded-lg hover:bg-blue-700 transition-colors inline-block">
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
            <div class="bg-white rounded-xl shadow-lg overflow-hidde">
                <img src="images/carros.esportivo.jpg" alt="Carro Esportivo Azul" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Modelos Esportivos</h3>
                    <p class="text-gray-600 mb-4">Conheça os veículos de alto desempenho com fichas técnicas detalhadas.</p>
                    <a href="categoria.php?tipo=esportivo" class="block w-full py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-800 transition-colors text-center">
                        Explorar
                    </a>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg overflow-hidde">
                <img src="images/chevrolet-onix-sedan-2020-1.jpg.webp" alt="Carro Azul" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Sedans Premium</h3>
                    <p class="text-gray-600 mb-4">Descubra os sedans de luxo com tecnologia de ponta e conforto superior.</p>
                    <a href="categoria.php?tipo=sedan" class="block w-full py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-800 transition-colors text-center">
                        Explorar
                    </a>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg overflow-hidde">
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
        
        document.addEventListener("DOMContentLoaded", function () {
    const track = document.getElementById("carouselTrack");
    const prevBtn = document.getElementById("prevBtn");
    const nextBtn = document.getElementById("nextBtn");
    const items = track.querySelectorAll(".carousel-item");
    
    let scrollIndex = 0;
    let visibleCount = 1;
    let isDragging = false;
    let startPos = 0;
    let currentTranslate = 0;
    let prevTranslate = 0;

    function updateVisibleCount() {
        const width = window.innerWidth;
        if (width >= 1024) visibleCount = 5;
        else if (width >= 640) visibleCount = 3;
        else visibleCount = 2;
    }

    function updateCarousel(translate = null) {
        const itemWidth = items[0].getBoundingClientRect().width + 16;
        const scrollAmount = translate !== null ? translate : scrollIndex * itemWidth;
        track.style.transform = `translateX(-${scrollAmount}px)`;
        prevBtn.style.display = scrollIndex > 0 ? "block" : "none";
        nextBtn.style.display = scrollIndex + visibleCount < items.length ? "block" : "none";
    }

    // Touch events
    track.addEventListener('touchstart', touchStart);
    track.addEventListener('touchmove', touchMove);
    track.addEventListener('touchend', touchEnd);

    // Mouse events
    track.addEventListener('mousedown', touchStart);
    track.addEventListener('mousemove', touchMove);
    track.addEventListener('mouseup', touchEnd);
    track.addEventListener('mouseleave', touchEnd);

    function touchStart(event) {
        isDragging = true;
        startPos = getPositionX(event);
        track.style.cursor = 'grabbing';
        track.style.transition = 'none';
    }

    function touchMove(event) {
        if (!isDragging) return;
        
        const currentPosition = getPositionX(event);
        currentTranslate = prevTranslate + currentPosition - startPos;
        track.style.transform = `translateX(${currentTranslate}px)`;
    }

    function touchEnd() {
        isDragging = false;
        track.style.cursor = 'grab';
        track.style.transition = 'transform 0.3s ease-in-out';

        const itemWidth = items[0].getBoundingClientRect().width + 16;
        const moveBy = currentTranslate - prevTranslate;

        if (Math.abs(moveBy) > itemWidth / 3) {
            if (moveBy < 0 && scrollIndex + visibleCount < items.length) {
                scrollIndex++;
            } else if (moveBy > 0 && scrollIndex > 0) {
                scrollIndex--;
            }
        }

        updateCarousel();
        prevTranslate = scrollIndex * -itemWidth;
        currentTranslate = prevTranslate;
    }

    function getPositionX(event) {
        return event.type.includes('mouse') ? event.pageX : event.touches[0].clientX;
    }

    nextBtn.addEventListener("click", () => {
        if (scrollIndex + visibleCount < items.length) {
            scrollIndex++;
            updateCarousel();
        }
    });

    prevBtn.addEventListener("click", () => {
        if (scrollIndex > 0) {
            scrollIndex--;
            updateCarousel();
        }
    });

    window.addEventListener("resize", () => {
        updateVisibleCount();
        updateCarousel();
    });

    updateVisibleCount();
    updateCarousel();
});
    </script>
</body>
</html>

