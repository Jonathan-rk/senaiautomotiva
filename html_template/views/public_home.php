<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENAI Automotivo - Fichas Técnicas</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <style>
        :root {
            --senai-blue: #0A3871; /* Azul mais escuro */
            --senai-red: #E31E24;
            --senai-dark: #1D2939;
        }
        
        .senai-blue { color: var(--senai-blue); }
        .bg-senai-blue { background-color: var(--senai-blue); }
        .senai-red { color: var(--senai-red); }
        .bg-senai-red { background-color: var(--senai-red); }
        
        .car-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .car-card:hover {
            transform: translateY(-5px);
            border-color: var(--senai-blue);
            box-shadow: 0 10px 25px rgba(10, 56, 113, 0.2);
        }
        
        .detail-section {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }
        
        .spec-item {
            border-left: 4px solid var(--senai-blue);
            transition: all 0.2s ease;
        }
        
        .spec-item:hover {
            background-color: rgba(10, 56, 113, 0.05);
        }
        
        .page {
            display: none;
        }
        
        .page.active {
            display: block;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .btn-primary {
            background-color: var(--senai-blue);
            border-color: var(--senai-blue);
        }
        
        .btn-primary:hover {
            background-color: #072a56;
            border-color: #072a56;
        }
        
        .loading {
            display: none;
        }
        
        .search-container {
            background: linear-gradient(rgba(10, 56, 113, 0.85), rgba(10, 56, 113, 0.95)), url('/images/l7YUU69EbXSL.jpg');
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.2rem;
            }
            
            .car-card {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <div class="bg-senai-blue text-white p-2 rounded-lg">
                        <i class="fas fa-car text-xl"></i>
                    </div>
                    <span class="navbar-brand senai-blue">SENAI Automotivo</span>
                </div>
                
                <div class="hidden md:flex items-center space-x-6">
                    <a href="#" onclick="showPage('home')" class="text-gray-700 hover:text-blue-600 transition-colors">Início</a>
                    <a href="#" onclick="showPage('fichas')" class="text-gray-700 hover:text-blue-600 transition-colors">Fichas Técnicas</a>
                    <a href="/index.php/login" class="text-gray-700 hover:text-blue-600 transition-colors">Fazer login como professor</a>
                </div>
                
                <div class="md:hidden">
                    <button onclick="toggleMobileMenu()" class="text-gray-700">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobileMenu" class="hidden md:hidden pb-4">
                <a href="#" onclick="showPage('home')" class="block py-2 text-gray-700">Início</a>
                <a href="#" onclick="showPage('fichas')" class="block py-2 text-gray-700">Fichas Técnicas</a>
                <a href="/index.php/login" class="block py-2 text-gray-700">Fazer login como professor</a>
            </div>
        </div>
    </nav>

    <!-- Home Page -->
    <div id="homePage" class="page active">
        <!-- Hero Section -->
        <section class="search-container text-white py-20">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <div class="mb-8">
                    <img src="https://www.sp.senai.br/images/senai-logo-branco.png" alt="SENAI Logo" class="h-16 mx-auto">
                </div>
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    Sistema de Fichas Técnicas Automotivas
                </h1>
                <p class="text-xl mb-8 opacity-90">
                    Explore o mundo da tecnologia automotiva com o SENAI
                </p>
                <button onclick="showPage('fichas')" class="bg-white text-blue-800 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-colors">
                    Ver Fichas Técnicas
                </button>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="py-16">
            <div class="max-w-7xl mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12 senai-blue">Categorias de Marcas</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                    <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow cursor-pointer" onclick="filterByBrand('Chevrolet')">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-car text-2xl senai-blue"></i>
                        </div>
                        <h3 class="font-semibold">Chevrolet</h3>
                    </div>
                    <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow cursor-pointer" onclick="filterByBrand('Volkswagen')">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-car text-2xl senai-blue"></i>
                        </div>
                        <h3 class="font-semibold">Volkswagen</h3>
                    </div>
                    <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow cursor-pointer" onclick="filterByBrand('Honda')">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-car text-2xl senai-blue"></i>
                        </div>
                        <h3 class="font-semibold">Honda</h3>
                    </div>
                    <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow cursor-pointer" onclick="filterByBrand('Toyota')">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-car text-2xl senai-blue"></i>
                        </div>
                        <h3 class="font-semibold">Toyota</h3>
                    </div>
                    <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow cursor-pointer" onclick="filterByBrand('Jeep')">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-car text-2xl senai-blue"></i>
                        </div>
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
                    <?php foreach (array_slice($fichas, 0, 3) as $ficha): ?>
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden car-card">
                        <?php if (!empty($ficha->imagem)): ?>
                            <img src="/uploads/<?php echo htmlspecialchars($ficha->imagem); ?>" alt="<?php echo htmlspecialchars($ficha->marca . ' ' . $ficha->modelo); ?>" class="w-full h-48 object-cover">
                        <?php else: ?>
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-car text-gray-400 text-5xl"></i>
                            </div>
                        <?php endif; ?>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($ficha->marca . ' ' . $ficha->modelo); ?></h3>
                            <p class="text-gray-600 mb-4">Ano: <?php echo htmlspecialchars($ficha->ano); ?></p>
                            <button onclick="showCarDetails(<?php echo $ficha->id; ?>)" class="w-full py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-800 transition-colors">
                                Ver Detalhes
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </div>

    <!-- Fichas Técnicas Page -->
    <div id="fichasPage" class="page">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <!-- Search and Filters -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-2xl font-bold mb-6 senai-blue">Fichas Técnicas</h2>
                
                <div class="flex flex-col md:flex-row gap-4 mb-6">
                    <div class="flex-1">
                        <input type="text" id="searchInput" placeholder="Pesquisar por modelo, marca ou ano..." 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <button class="px-6 py-3 bg-senai-blue text-white rounded-lg hover:bg-blue-800 transition-colors">
                        <i class="fas fa-search mr-2"></i>Buscar
                    </button>
                </div>
                
                <!-- Filtros Avançados -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">Filtros por Características</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Potência</label>
                            <select id="potenciaFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Todas</option>
                                <option value="0-100">Até 100 cv</option>
                                <option value="101-150">101 a 150 cv</option>
                                <option value="151-200">151 a 200 cv</option>
                                <option value="201-1000">Acima de 200 cv</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Combustível</label>
                            <select id="combustivelFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="todos">Todos</option>
                                <option value="Flex">Flex</option>
                                <option value="Gasolina">Gasolina</option>
                                <option value="Diesel">Diesel</option>
                                <option value="Híbrido">Híbrido</option>
                                <option value="Elétrico">Elétrico</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Transmissão</label>
                            <select id="transmissaoFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="todos">Todas</option>
                                <option value="Manual">Manual</option>
                                <option value="Automática">Automática</option>
                                <option value="CVT">CVT</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Carroceria</label>
                            <select id="carroceriaFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="todos">Todas</option>
                                <option value="Hatch">Hatch</option>
                                <option value="Sedan">Sedan</option>
                                <option value="SUV">SUV</option>
                                <option value="Picape">Picape</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button onclick="resetFilters()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors mr-3">
                            Limpar Filtros
                        </button>
                        <button onclick="loadCars()" class="px-4 py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-800 transition-colors">
                            Aplicar Filtros
                        </button>
                    </div>
                </div>
                
                <!-- Brand Filters -->
                <div class="flex flex-wrap gap-2">
                    <button onclick="filterCars('all')" class="filter-btn active px-4 py-2 rounded-full bg-senai-blue text-white text-sm">
                        Todas
                    </button>
                    <button onclick="filterCars('Chevrolet')" class="filter-btn px-4 py-2 rounded-full bg-gray-200 text-gray-700 text-sm hover:bg-gray-300 transition-colors">
                        Chevrolet
                    </button>
                    <button onclick="filterCars('Volkswagen')" class="filter-btn px-4 py-2 rounded-full bg-gray-200 text-gray-700 text-sm hover:bg-gray-300 transition-colors">
                        Volkswagen
                    </button>
                    <button onclick="filterCars('Honda')" class="filter-btn px-4 py-2 rounded-full bg-gray-200 text-gray-700 text-sm hover:bg-gray-300 transition-colors">
                        Honda
                    </button>
                    <button onclick="filterCars('Toyota')" class="filter-btn px-4 py-2 rounded-full bg-gray-200 text-gray-700 text-sm hover:bg-gray-300 transition-colors">
                        Toyota
                    </button>
                    <button onclick="filterCars('Fiat')" class="filter-btn px-4 py-2 rounded-full bg-gray-200 text-gray-700 text-sm hover:bg-gray-300 transition-colors">
                        Fiat
                    </button>
                    <button onclick="filterCars('Hyundai')" class="filter-btn px-4 py-2 rounded-full bg-gray-200 text-gray-700 text-sm hover:bg-gray-300 transition-colors">
                        Hyundai
                    </button>
                    <button onclick="filterCars('Jeep')" class="filter-btn px-4 py-2 rounded-full bg-gray-200 text-gray-700 text-sm hover:bg-gray-300 transition-colors">
                        Jeep
                    </button>
                </div>
            </div>
            
            <!-- Cars Grid -->
            <div id="carsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (empty($fichas)): ?>
                    <div class="col-span-full text-center py-8">
                        <p class="text-gray-500 text-lg">Nenhuma ficha técnica encontrada.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($fichas as $ficha): ?>
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden car-card" data-marca="<?php echo htmlspecialchars($ficha->marca); ?>">
                            <?php if (!empty($ficha->imagem)): ?>
                                <img src="/uploads/<?php echo htmlspecialchars($ficha->imagem); ?>" alt="<?php echo htmlspecialchars($ficha->marca . ' ' . $ficha->modelo); ?>" class="w-full h-48 object-cover">
                            <?php else: ?>
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-car text-gray-400 text-5xl"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($ficha->marca . ' ' . $ficha->modelo); ?></h3>
                                <p class="text-gray-600"><?php echo htmlspecialchars($ficha->ano); ?></p>
                                
                                <div class="mt-3 space-y-1 text-sm">
                                    <?php if (!empty($ficha->motor)): ?>
                                        <p><span class="font-semibold">Motor:</span> <?php echo htmlspecialchars($ficha->motor); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($ficha->potencia)): ?>
                                        <p><span class="font-semibold">Potência:</span> <?php echo htmlspecialchars($ficha->potencia); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($ficha->cambio)): ?>
                                        <p><span class="font-semibold">Câmbio:</span> <?php echo htmlspecialchars($ficha->cambio); ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mt-4">
                                    <button class="view-details w-full bg-senai-blue text-white py-2 px-4 rounded-md hover:bg-blue-800 transition-colors" 
                                            data-id="<?php echo $ficha->id; ?>" onclick="showCarDetails(<?php echo $ficha->id; ?>)">
                                        Ver detalhes
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Loading -->
            <div id="loading" class="loading text-center py-8">
                <i class="fas fa-spinner fa-spin text-3xl senai-blue"></i>
                <p class="mt-2 text-gray-600">Carregando fichas técnicas...</p>
            </div>
        </div>
    </div>

    <!-- Car Detail Page -->
    <div id="detailPage" class="page">
        <div class="max-w-4xl mx-auto px-4 py-8">
            <!-- Back Button -->
            <button onclick="showPage('fichas')" class="mb-6 flex items-center text-blue-800 hover:text-blue-900 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Voltar para listagem
            </button>
            
            <!-- Car Detail Content -->
            <div id="carDetailContent" class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>

    <!-- Modal para detalhes da ficha -->
    <div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-screen overflow-y-auto">
            <div class="p-6" id="modalContent">
                <!-- Conteúdo carregado via AJAX -->
                <div class="animate-pulse">
                    <div class="h-8 bg-gray-200 rounded w-1/3 mb-4"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                    <div class="h-4 bg-gray-200 rounded w-2/3 mb-2"></div>
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-100 flex justify-end rounded-b-lg">
                <button id="closeModal" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-md transition duration-200">
                    Fechar
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-senai-blue text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-6 md:mb-0">
                    <img src="https://www.sp.senai.br/images/senai-logo-branco.png" alt="SENAI Logo" class="h-10">
                    <p class="mt-2 text-sm opacity-80">© <?php echo date('Y'); ?> SENAI Automotivo. Todos os direitos reservados.</p>
                </div>
                <div class="flex space-x-6">
                    <a href="#" class="hover:text-gray-300 transition-colors">
                        <i class="fab fa-facebook-f text-xl"></i>
                    </a>
                    <a href="#" class="hover:text-gray-300 transition-colors">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                    <a href="#" class="hover:text-gray-300 transition-colors">
                        <i class="fab fa-linkedin-in text-xl"></i>
                    </a>
                    <a href="#" class="hover:text-gray-300 transition-colors">
                        <i class="fab fa-youtube text-xl"></i>
                    </a>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-blue-800 text-sm opacity-80">
                <div class="flex flex-col md:flex-row justify-between">
                    <div class="mb-4 md:mb-0">
                        <a href="#" class="hover:text-gray-300 transition-colors mr-4">Política de Privacidade</a>
                        <a href="#" class="hover:text-gray-300 transition-colors mr-4">Termos de Uso</a>
                        <a href="#" class="hover:text-gray-300 transition-colors">Contato</a>
                    </div>
                    <div>
                        <p>SENAI - Serviço Nacional de Aprendizagem Industrial</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Função para alternar entre páginas
        function showPage(pageId) {
            document.querySelectorAll('.page').forEach(page => {
                page.classList.remove('active');
            });
            
            if (pageId === 'home') {
                document.getElementById('homePage').classList.add('active');
            } else if (pageId === 'fichas') {
                document.getElementById('fichasPage').classList.add('active');
            } else if (pageId === 'detail') {
                document.getElementById('detailPage').classList.add('active');
            }
        }
        
        // Função para alternar menu mobile
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            if (mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.remove('hidden');
            } else {
                mobileMenu.classList.add('hidden');
            }
        }
        
        // Função para filtrar carros por marca
        function filterCars(marca) {
            const cards = document.querySelectorAll('.car-card');
            const filterButtons = document.querySelectorAll('.filter-btn');
            
            // Atualizar botões de filtro
            filterButtons.forEach(btn => {
                btn.classList.remove('bg-senai-blue', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700');
            });
            
            event.target.classList.remove('bg-gray-200', 'text-gray-700');
            event.target.classList.add('bg-senai-blue', 'text-white');
            
            // Filtrar cards
            cards.forEach(card => {
                if (marca === 'all' || card.dataset.marca === marca) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        // Função para filtrar por marca na seção de categorias
        function filterByBrand(marca) {
            showPage('fichas');
            filterCars(marca);
        }
        
        // Função para resetar filtros
        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('potenciaFilter').value = '';
            document.getElementById('combustivelFilter').value = 'todos';
            document.getElementById('transmissaoFilter').value = 'todos';
            document.getElementById('carroceriaFilter').value = 'todos';
            
            filterCars('all');
        }
        
        // Função para mostrar detalhes do carro
        function showCarDetails(id) {
            // Mostrar modal com loading
            document.getElementById('detailsModal').classList.remove('hidden');
            
            // Carregar detalhes via AJAX
            fetch(`/api/get_car.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const ficha = data.data;
                        
                        // Construir HTML com detalhes completos
                        let html = `
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">${ficha.marca} ${ficha.modelo}</h2>
                            <p class="text-lg text-gray-600 mb-4">Ano: ${ficha.ano}</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        `;
                        
                        // Adicionar especificações técnicas
                        const specs = [
                            { label: 'Motor', value: ficha.motor },
                            { label: 'Potência', value: ficha.potencia },
                            { label: 'Torque', value: ficha.torque },
                            { label: 'Consumo', value: ficha.consumo },
                            { label: 'Câmbio', value: ficha.cambio },
                            { label: 'Tração', value: ficha.tracao },
                            { label: 'Porta-malas', value: ficha.porta_malas },
                            { label: 'Portas', value: ficha.portas },
                            { label: 'Peso', value: ficha.peso }
                        ];
                        
                        specs.forEach(spec => {
                            if (spec.value) {
                                html += `
                                    <div class="bg-gray-100 p-3 rounded spec-item">
                                        <span class="font-semibold">${spec.label}:</span> ${spec.value}
                                    </div>
                                `;
                            }
                        });
                        
                        html += `</div>`;
                        
                        // Adicionar imagem se disponível
                        if (ficha.imagem) {
                            html += `
                                <div class="mt-4">
                                    <img src="/uploads/${ficha.imagem}" alt="${ficha.marca} ${ficha.modelo}" 
                                        class="w-full max-h-64 object-contain">
                                </div>
                            `;
                        }
                        
                        document.getElementById('modalContent').innerHTML = html;
                    } else {
                        document.getElementById('modalContent').innerHTML = `<p class="text-red-500">Erro ao carregar detalhes.</p>`;
                    }
                })
                .catch(error => {
                    document.getElementById('modalContent').innerHTML = `<p class="text-red-500">Erro ao carregar detalhes.</p>`;
                    console.error('Erro:', error);
                });
        }
        
        // Fechar modal
        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('detailsModal').classList.add('hidden');
        });
        
        // Fechar modal ao clicar fora
        document.getElementById('detailsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
