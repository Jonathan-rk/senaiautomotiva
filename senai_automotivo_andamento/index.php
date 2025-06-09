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
    // Se não conseguir conectar, continua sem erro para não quebrar a página
    $pdo = null;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENAI Automotivo - Fichas Técnicas</title>
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

    <!-- Categories Section -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12 senai-blue">Categorias de Marcas</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow cursor-pointer" onclick="window.location.href='fichas.php?marca=Chevrolet'">
                    <img src="images/chevrolet.jpg" alt="Chevrolet" class="object-contain w-full h-full">
                    <h3 class="font-semibold">Chevrolet</h3>
                </div>
                <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow cursor-pointer" onclick="window.location.href='fichas.php?marca=Volkswagen'">
                    <img src="images/volkswagen.png" alt="Volkswagen" class="object-contain w-full h-full">
                    <h3 class="font-semibold">Volkswagen</h3>
                </div>
                <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow cursor-pointer" onclick="window.location.href='fichas.php?marca=Honda'">
                    <img src="images/honda.webp" alt="Honda" class="object-contain w-full h-full">
                    <h3 class="font-semibold">Honda</h3>
                </div>
                <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow cursor-pointer" onclick="window.location.href='fichas.php?marca=Toyota'">
                    <img src="images/toyota.jpg" alt="Toyota" class="object-contain w-full h-full">
                    <h3 class="font-semibold">Toyota</h3>
                </div>
                <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow cursor-pointer" onclick="window.location.href='fichas.php?marca=Jeep'">
                    <img src="images/jeep.jpg" alt="Jeep" class="object-contain w-full h-full">
                    <h3 class="font-semibold">Jeep</h3>
                </div>
            </div>
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
                        <a href="fichas.php?categoria=Esportivos" class="w-full py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-800 transition-colors block text-center">
                            Explorar
                        </a>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <img src="images/chevrolet-onix-sedan-2020-1.jpg.webp" alt="Carro Azul" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">Sedans Premium</h3>
                        <p class="text-gray-600 mb-4">Descubra os sedans de luxo com tecnologia de ponta e conforto superior.</p>
                        <a href="fichas.php?categoria=Sedans" class="w-full py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-800 transition-colors block text-center">
                            Explorar
                        </a>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <img src="images/2f0b2b_64065baf27a2434b90c787b711045175~mv2.avif" alt="Carro com Luzes Neon" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">Tecnologia Avançada</h3>
                        <p class="text-gray-600 mb-4">Veículos com as mais recentes inovações tecnológicas do mercado automotivo.</p>
                        <a href="fichas.php" class="w-full py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-800 transition-colors block text-center">
                            Explorar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-senai-dark text-white py-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">SENAI Automotivo</h3>
                    <p class="text-gray-300 mb-4">
                        Sistema de fichas técnicas desenvolvido para auxiliar no ensino e aprendizagem da tecnologia automotiva.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-xl font-bold mb-4">Links Úteis</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-300 hover:text-white transition-colors">Início</a></li>
                        <li><a href="fichas.php" class="text-gray-300 hover:text-white transition-colors">Fichas Técnicas</a></li>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'professor'): ?>
                            <li><a href="painel_professor.php" class="text-gray-300 hover:text-white transition-colors">Painel do Professor</a></li>
                            <li><a href="categorias.php" class="text-gray-300 hover:text-white transition-colors">Gerenciar Categorias</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-xl font-bold mb-4">Contato</h3>
                    <div class="space-y-2 text-gray-300">
                        <p><i class="fas fa-envelope mr-2"></i>contato@senai.br</p>
                        <p><i class="fas fa-phone mr-2"></i>(11) 1234-5678</p>
                        <p><i class="fas fa-map-marker-alt mr-2"></i>São Paulo, SP</p>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-600 mt-8 pt-8 text-center text-gray-300">
                <p>&copy; <?php echo date('Y'); ?> SENAI Automotivo. Todos os direitos reservados.</p>
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

