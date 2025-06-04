// Variável global para armazenar os dados das fichas buscados da API
let globalCarData = [];
let currentUser = null; // Armazena informações do usuário logado

// Função para buscar e carregar os carros da API
async function loadCars(filterParams = {}) {
    const carsGrid = document.getElementById("carsGrid");
    const loadingIndicator = document.getElementById("loading");
    carsGrid.innerHTML = "";
    loadingIndicator.style.display = "block";

    try {
        // Constrói a query string para a API com base nos filtros
        const query = new URLSearchParams(filterParams).toString();
        const response = await fetch(`/api/fichas?${query}`);
        
        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status}`);
        }
        
        globalCarData = await response.json(); // Armazena os dados globalmente
        
        loadingIndicator.style.display = "none";

        if (globalCarData.length === 0) {
            carsGrid.innerHTML = `
                <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-8">
                    <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600">Nenhuma ficha técnica encontrada com os filtros selecionados.</p>
                    <button onclick="resetFilters()" class="mt-4 px-4 py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-800 transition-colors">
                        Limpar Filtros
                    </button>
                </div>
            `;
            return;
        }

        // Exibir carros
        globalCarData.forEach(car => {
            const carCard = document.createElement("div");
            carCard.className = "car-card bg-white rounded-xl shadow-lg overflow-hidden";
            // Usar os campos retornados pela API (ajustados no controller)
            carCard.innerHTML = `
                <div class="relative">
                    <img src="${car.imagem || '/images/placeholder.png'}" alt="${car.marca} ${car.modelo}" class="w-full h-48 object-cover">
                    <div class="absolute top-0 right-0 bg-senai-blue text-white px-3 py-1 m-2 rounded-lg text-sm font-semibold">
                        ${car.ano || 'N/A'}
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">${car.marca} ${car.modelo}</h3>
                    ${car.preco ? `<div class="flex items-center text-gray-600 mb-4">
                        <span class="text-lg font-semibold text-senai-blue">R$ ${car.preco.toLocaleString('pt-BR')}</span>
                    </div>` : ''}
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-gas-pump mr-2 text-senai-blue"></i>
                            ${car.combustivel || 'N/A'}
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-cogs mr-2 text-senai-blue"></i>
                            ${car.motor || 'N/A'}
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-tachometer-alt mr-2 text-senai-blue"></i>
                            ${car.potencia ? car.potencia + ' cv' : 'N/A'}
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-car mr-2 text-senai-blue"></i>
                            ${car.carroceria || 'N/A'} 
                        </div>
                    </div>
                    <button onclick="showCarDetail(${car.id})" class="w-full py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-800 transition-colors">
                        Ver Detalhes
                    </button>
                </div>
            `;
            carsGrid.appendChild(carCard);
        });

    } catch (error) {
        console.error("Erro ao carregar fichas técnicas:", error);
        loadingIndicator.style.display = "none";
        carsGrid.innerHTML = `
            <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-8">
                <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                <p class="text-gray-600">Erro ao carregar dados. Tente novamente mais tarde.</p>
            </div>
        `;
    }
}

// Função para mostrar detalhes do carro buscando da API
async function showCarDetail(carId) {
    const detailContent = document.getElementById("carDetailContent");
    detailContent.innerHTML = `<div class="p-6 text-center"><i class="fas fa-spinner fa-spin text-3xl senai-blue"></i><p class="mt-2">Carregando detalhes...</p></div>`;
    showPage("detail");

    try {
        const response = await fetch(`/api/fichas/${carId}`);
        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status}`);
        }
        const car = await response.json(); // Recebe todos os dados do backend

        // Formata a exibição dos detalhes - Mapear todos os campos do DB para o HTML
        detailContent.innerHTML = `
            <div class="relative">
                <img src="${car.imagem_url || '/images/placeholder.png'}" alt="${car.marca} ${car.modelo}" class="w-full h-64 object-cover">
                <div class="absolute top-0 right-0 bg-senai-blue text-white px-3 py-1 m-4 rounded-lg text-sm font-semibold">
                    ${car.ano_modelo || car.ano_fabricacao || 'N/A'}
                </div>
            </div>
            <div class="p-6">
                <h2 class="text-3xl font-bold mb-1">${car.marca} ${car.modelo}</h2>
                <p class="text-lg text-gray-600 mb-4">${car.versao || ''}</p>
                ${car.preco ? `<div class="flex items-center text-gray-600 mb-6">
                    <span class="text-2xl font-semibold text-senai-blue">R$ ${parseFloat(car.preco).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                </div>` : ''}
                
                ${car.descricao ? `<p class="text-gray-700 mb-6">${car.descricao}</p>` : ''}
                
                <h3 class="text-xl font-bold mb-4 text-senai-blue">Especificações Técnicas</h3>
                
                <!-- Seção Informações Básicas -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold mb-3 border-b pb-1 border-gray-300">Informações Básicas</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2">
                        ${createSpecItem("Marca", car.marca)}
                        ${createSpecItem("Modelo", car.modelo)}
                        ${createSpecItem("Ano Fabricação/Modelo", `${car.ano_fabricacao || 'N/A'} / ${car.ano_modelo || 'N/A'}`)}
                        ${createSpecItem("Versão", car.versao)}
                        ${createSpecItem("Código do Motor", car.codigo_motor)}
                        ${createSpecItem("Combustível", car.tipo_combustivel)}
                    </div>
                </div>

                <!-- Seção Motorização -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold mb-3 border-b pb-1 border-gray-300">Motorização</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2">
                        ${createSpecItem("Tipo", car.tipo_motor)}
                        ${createSpecItem("Cilindrada", car.cilindrada ? `${car.cilindrada} L` : null)}
                        ${createSpecItem("Potência Máxima", car.potencia_maxima ? `${car.potencia_maxima} cv` : null)}
                        ${createSpecItem("Torque Máximo", car.torque_maximo ? `${car.torque_maximo} kgfm` : null)}
                        ${createSpecItem("Nº de Válvulas", car.numero_valvulas)}
                        ${createSpecItem("Tipo de Injeção", car.tipo_injecao)}
                    </div>
                </div>

                <!-- Seção Transmissão -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold mb-3 border-b pb-1 border-gray-300">Transmissão</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2">
                        ${createSpecItem("Tipo de Câmbio", car.tipo_cambio)}
                        ${createSpecItem("Número de Marchas", car.numero_marchas)}
                    </div>
                </div>
                
                <!-- Seção Suspensão e Freios -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold mb-3 border-b pb-1 border-gray-300">Suspensão e Freios</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2">
                        ${createSpecItem("Suspensão Dianteira", car.suspensao_dianteira)}
                        ${createSpecItem("Suspensão Traseira", car.suspensao_traseira)}
                        ${createSpecItem("Freios Dianteiros", car.freios_dianteiros)}
                        ${createSpecItem("Freios Traseiros", car.freios_traseiros)}
                        ${createSpecItem("ABS", car.possui_abs ? 'Sim' : 'Não')}
                        ${createSpecItem("EBD", car.possui_ebd ? 'Sim' : 'Não')}
                    </div>
                </div>

                <!-- Seção Direção e Pneus -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold mb-3 border-b pb-1 border-gray-300">Direção e Pneus</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2">
                        ${createSpecItem("Tipo de Direção", car.tipo_direcao)}
                        ${createSpecItem("Pneus Originais", car.pneus_originais)}
                    </div>
                </div>

                <!-- Seção Dimensões -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold mb-3 border-b pb-1 border-gray-300">Dimensões</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2">
                        ${createSpecItem("Comprimento", car.comprimento_mm ? `${car.comprimento_mm} mm` : null)}
                        ${createSpecItem("Largura", car.largura_mm ? `${car.largura_mm} mm` : null)}
                        ${createSpecItem("Altura", car.altura_mm ? `${car.altura_mm} mm` : null)}
                        ${createSpecItem("Entre-eixos", car.entre_eixos_mm ? `${car.entre_eixos_mm} mm` : null)}
                        ${createSpecItem("Altura do Solo", car.altura_solo_mm ? `${car.altura_solo_mm} mm` : null)}
                        ${createSpecItem("Peso", car.peso_kg ? `${car.peso_kg} kg` : null)}
                    </div>
                </div>

                <!-- Seção Desempenho e Consumo -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold mb-3 border-b pb-1 border-gray-300">Desempenho e Consumo</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2">
                        ${createSpecItem("Velocidade Máxima", car.velocidade_maxima_kmh ? `${car.velocidade_maxima_kmh} km/h` : null)}
                        ${createSpecItem("Aceleração 0-100 km/h", car.aceleracao_0_100_s ? `${car.aceleracao_0_100_s} s` : null)}
                        ${createSpecItem("Consumo Urbano", car.consumo_urbano_km_l ? `${car.consumo_urbano_km_l} km/l` : null)}
                        ${createSpecItem("Consumo Rodoviário", car.consumo_rodoviario_km_l ? `${car.consumo_rodoviario_km_l} km/l` : null)}
                        ${createSpecItem("Capacidade do Tanque", car.capacidade_tanque_l ? `${car.capacidade_tanque_l} L` : null)}
                    </div>
                </div>

                <!-- Seção Capacidades -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold mb-3 border-b pb-1 border-gray-300">Capacidades</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2">
                        ${createSpecItem("Porta-malas", car.porta_malas_l ? `${car.porta_malas_l} L` : null)}
                        ${createSpecItem("Carga Útil", car.carga_util_kg ? `${car.carga_util_kg} kg` : null)}
                        ${createSpecItem("Nº de Ocupantes", car.numero_ocupantes)}
                    </div>
                </div>

                <!-- Seção Sistemas e Eletrônica (Opcional) -->
                ${ (car.sistema_injecao_detalhes || car.sonda_lambda_detalhes || car.ecu_detalhes || car.sensores_detalhes || car.outros_sistemas_eletronicos) ? `
                <div class="mb-6">
                    <h4 class="text-lg font-semibold mb-3 border-b pb-1 border-gray-300">Sistemas e Eletrônica</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2">
                        ${createSpecItem("Injeção (Detalhes)", car.sistema_injecao_detalhes)}
                        ${createSpecItem("Sonda Lambda (Detalhes)", car.sonda_lambda_detalhes)}
                        ${createSpecItem("ECU (Detalhes)", car.ecu_detalhes)}
                        ${createSpecItem("Sensores (Detalhes)", car.sensores_detalhes)}
                        ${createSpecItem("Outros", car.outros_sistemas_eletronicos)}
                    </div>
                </div>` : ''}
                
                <div class="flex justify-center mt-8 space-x-4">
                    <button onclick="showPage('fichas')" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Voltar
                    </button>
                    <button onclick="generatePDF('${car.marca}', '${car.modelo}')" class="px-6 py-3 bg-senai-red text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-file-pdf mr-2"></i>Baixar PDF
                    </button>
                    <!-- Botões de Editar/Excluir para Professor -->
                    ${currentUser && currentUser.role === 'professor' ? `
                    <button onclick="openEditModal(${car.id})" class="px-6 py-3 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                        <i class="fas fa-edit mr-2"></i>Editar
                    </button>
                    <button onclick="confirmDeleteFicha(${car.id})" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>Excluir
                    </button>
                    ` : ''}
                </div>
            </div>
        `;

    } catch (error) {
        console.error("Erro ao buscar detalhes da ficha:", error);
        detailContent.innerHTML = `
            <div class="p-6 text-center">
                <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                <p class="text-gray-600">Erro ao carregar detalhes. Tente novamente mais tarde.</p>
                <button onclick="showPage('fichas')" class="mt-4 px-4 py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-800 transition-colors">
                    Voltar
                </button>
            </div>
        `;
    }
}

// Helper para criar item de especificação (evita mostrar se valor for null/undefined)
function createSpecItem(label, value) {
    if (value === null || value === undefined || value === '') {
        return ''; // Não renderiza o item se o valor não existir
    }
    return `
        <div class="spec-item py-2 px-3 bg-gray-50 rounded">
            <span class="block text-sm text-gray-500">${label}</span>
            <span class="font-semibold text-gray-800">${value}</span>
        </div>
    `;
}

// Função para aplicar filtros e recarregar carros
function applyFilters() {
    const filterParams = {};
    
    const marcaFilter = document.querySelector('.filter-btn.active')?.getAttribute('data-filter');
    if (marcaFilter && marcaFilter !== 'all') {
        filterParams.marca = marcaFilter;
    }

    const searchInput = document.getElementById('searchInput').value;
    if (searchInput) {
        filterParams.search = searchInput; // Backend precisa implementar busca por texto
    }

    const potenciaFilter = document.getElementById('potenciaFilter').value;
    if (potenciaFilter) {
        filterParams.potencia = potenciaFilter; // Backend precisa tratar range (ex: 0-100)
    }

    const combustivelFilter = document.getElementById('combustivelFilter').value;
    if (combustivelFilter && combustivelFilter !== 'todos') {
        filterParams.combustivel = combustivelFilter;
    }

    const transmissaoFilter = document.getElementById('transmissaoFilter').value;
    if (transmissaoFilter && transmissaoFilter !== 'todos') {
        filterParams.transmissao = transmissaoFilter;
    }

    const carroceriaFilter = document.getElementById('carroceriaFilter').value;
    if (carroceriaFilter && carroceriaFilter !== 'todos') {
        filterParams.carroceria = carroceriaFilter;
    }
    
    // Adicionar filtro de ano (requer backend)
    const anoFilter = document.getElementById('anoFilter')?.value; // Assumindo que existe um select com id="anoFilter"
    if (anoFilter) {
        filterParams.ano = anoFilter;
    }

    loadCars(filterParams);
}

// Função para filtrar por marca (botões)
function filterCars(filter) {
    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach(btn => {
        btn.classList.remove('bg-senai-blue', 'text-white', 'active');
        btn.classList.add('bg-gray-200', 'text-gray-700');
        btn.removeAttribute('data-filter'); // Limpa o atributo
    });

    const activeButton = document.querySelector(`.filter-btn[onclick="filterCars('${filter}')"]`);
    if (activeButton) {
        activeButton.classList.remove('bg-gray-200', 'text-gray-700');
        activeButton.classList.add('bg-senai-blue', 'text-white', 'active');
        activeButton.setAttribute('data-filter', filter); // Adiciona o atributo com o filtro
    }

    applyFilters(); // Aplica todos os filtros, incluindo o de marca
}

// Função para filtrar por marca vindo da Home
function filterByBrand(brand) {
    showPage('fichas');
    // Simula o clique no botão da marca correspondente para ativar o filtro
    const brandButton = document.querySelector(`.filter-btn[onclick="filterCars('${brand}')"]`);
    if (brandButton) {
        brandButton.click();
    } else {
        // Se o botão não existir (marca não listada), carrega todos
        filterCars('all'); 
    }
}

// Função para resetar filtros
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('potenciaFilter').value = '';
    document.getElementById('combustivelFilter').value = 'todos';
    document.getElementById('transmissaoFilter').value = 'todos';
    document.getElementById('carroceriaFilter').value = 'todos';
    // Resetar filtro de ano se existir
    const anoFilter = document.getElementById('anoFilter');
    if (anoFilter) anoFilter.value = '';
    
    filterCars('all'); // Ativa o botão 'Todas' e recarrega
}

// Função para alternar entre páginas
function showPage(pageId) {
    const pages = document.querySelectorAll('.page');
    pages.forEach(page => {
        page.classList.remove('active');
    });

    const targetPage = document.getElementById(pageId + 'Page');
    if (targetPage) {
        targetPage.classList.add('active');
    } else {
        console.warn(`Página com ID ${pageId}Page não encontrada.`);
        // Volta para home como fallback
        document.getElementById('homePage').classList.add('active');
        return;
    }

    // Carregar carros quando a página de fichas é exibida
    if (pageId === 'fichas') {
        applyFilters(); // Usa applyFilters para considerar filtros já selecionados
    }
    
    // Carregar dados do painel do professor
    if (pageId === 'professor' && currentUser && currentUser.role === 'professor') {
        loadProfessorDashboard();
    }
    
    // Esconder menu mobile ao navegar
    const mobileMenu = document.getElementById('mobileMenu');
    if (!mobileMenu.classList.contains('hidden')) {
        mobileMenu.classList.add('hidden');
    }
}

// Função para alternar menu mobile
function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobileMenu');
    mobileMenu.classList.toggle('hidden');
}

// --- Funções de Autenticação ---

// Função de login
async function handleLogin(event) {
    event.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const loginButton = event.target.querySelector('button[type="submit"]');
    const originalButtonText = loginButton.innerHTML;
    loginButton.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>Entrando...`;
    loginButton.disabled = true;

    try {
        const response = await fetch('/api/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ username, password }),
        });

        const data = await response.json();

        if (response.ok) {
            // Login bem-sucedido
            alert(data.message); // Ou usar um modal/notificação mais elegante
            currentUser = data.user; // Armazena dados do usuário
            currentUser.role = 'professor'; // Assume que login só retorna professor
            updateUIForLoggedInUser();
            showPage('professor'); // Redireciona para o painel do professor
        } else {
            // Erro no login
            alert(`Erro: ${data.message}`); // Exibe mensagem de erro da API
        }
    } catch (error) {
        console.error("Erro ao fazer login:", error);
        alert("Erro de conexão ao tentar fazer login. Tente novamente.");
    } finally {
        loginButton.innerHTML = originalButtonText;
        loginButton.disabled = false;
        // Limpar campos? Opcional
        // document.getElementById('username').value = '';
        // document.getElementById('password').value = '';
    }
}

// Função de logout
async function logout() {
    try {
        const response = await fetch('/api/auth/logout', {
            method: 'POST',
        });
        const data = await response.json();
        if (response.ok) {
            alert(data.message);
            currentUser = null;
            updateUIForLoggedOutUser();
            showPage('home'); // Redireciona para a home
        } else {
            alert(`Erro ao sair: ${data.message}`);
        }
    } catch (error) {
        console.error("Erro ao fazer logout:", error);
        alert("Erro de conexão ao tentar fazer logout.");
    }
}

// Função para verificar a sessão ao carregar a página
async function checkSession() {
    try {
        const response = await fetch('/api/auth/check-session');
        if (!response.ok) {
            // Se a resposta não for OK, trata como não logado
            updateUIForLoggedOutUser();
            return;
        }
        const data = await response.json();
        if (data.isLoggedIn && data.user.role === 'professor') {
            currentUser = data.user;
            updateUIForLoggedInUser();
        } else {
            updateUIForLoggedOutUser();
        }
    } catch (error) {
        console.error("Erro ao verificar sessão:", error);
        updateUIForLoggedOutUser(); // Assume não logado em caso de erro
    }
}

// Atualiza a UI para usuário logado (professor)
function updateUIForLoggedInUser() {
    // Altera o botão Login para Sair na navegação principal
    const navLinks = document.querySelector('nav .hidden.md\:flex');
    const mobileNavLinks = document.getElementById('mobileMenu');
    
    if (navLinks) {
        navLinks.innerHTML = `
            <a href="#" onclick="showPage('home')" class="text-gray-700 hover:text-blue-600 transition-colors">Início</a>
            <a href="#" onclick="showPage('fichas')" class="text-gray-700 hover:text-blue-600 transition-colors">Fichas Técnicas</a>
            <a href="#" onclick="showPage('professor')" class="text-gray-700 hover:text-blue-600 transition-colors">Painel Professor</a>
            <button onclick="logout()" class="ml-4 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm">
                <i class="fas fa-sign-out-alt mr-1"></i>Sair
            </button>
        `;
    }
    if (mobileNavLinks) {
         mobileNavLinks.innerHTML = `
            <a href="#" onclick="showPage('home'); toggleMobileMenu();" class="block py-2 text-gray-700">Início</a>
            <a href="#" onclick="showPage('fichas'); toggleMobileMenu();" class="block py-2 text-gray-700">Fichas Técnicas</a>
            <a href="#" onclick="showPage('professor'); toggleMobileMenu();" class="block py-2 text-gray-700">Painel Professor</a>
            <button onclick="logout(); toggleMobileMenu();" class="mt-2 w-full text-left px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors text-sm">
                <i class="fas fa-sign-out-alt mr-1"></i>Sair
            </button>
        `;
    }
    
    // Esconde a página de login se estiver ativa
    if (document.getElementById('loginPage').classList.contains('active')) {
        showPage('home');
    }
}

// Atualiza a UI para usuário deslogado
function updateUIForLoggedOutUser() {
    const navLinks = document.querySelector('nav .hidden.md\:flex');
    const mobileNavLinks = document.getElementById('mobileMenu');

    if (navLinks) {
        navLinks.innerHTML = `
            <a href="#" onclick="showPage('home')" class="text-gray-700 hover:text-blue-600 transition-colors">Início</a>
            <a href="#" onclick="showPage('fichas')" class="text-gray-700 hover:text-blue-600 transition-colors">Fichas Técnicas</a>
            <a href="#" onclick="showPage('login')" class="ml-4 px-4 py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-800 transition-colors text-sm">
                <i class="fas fa-sign-in-alt mr-1"></i>Login
            </a>
        `;
    }
     if (mobileNavLinks) {
         mobileNavLinks.innerHTML = `
            <a href="#" onclick="showPage('home'); toggleMobileMenu();" class="block py-2 text-gray-700">Início</a>
            <a href="#" onclick="showPage('fichas'); toggleMobileMenu();" class="block py-2 text-gray-700">Fichas Técnicas</a>
            <a href="#" onclick="showPage('login'); toggleMobileMenu();" class="block py-2 text-gray-700">Login</a>
        `;
    }
    
    // Se o usuário estava no painel do professor, redireciona para home
    if (document.getElementById('professorPage').classList.contains('active')) {
        showPage('home');
    }
}

// --- Funções do Painel do Professor ---

// Carrega dados e monta o painel do professor
async function loadProfessorDashboard() {
    const dashboardContent = document.getElementById('professorDashboardContent'); // Elemento container no HTML
    const fichasList = document.getElementById('professorFichasList'); // Tabela/Lista no HTML
    const statsVeiculos = document.getElementById('statTotalVeiculos'); // Span para total
    
    if (!dashboardContent || !fichasList || !statsVeiculos) {
        console.error("Elementos do painel do professor não encontrados no HTML.");
        // Tenta recriar a estrutura básica se não existir
        const professorPage = document.getElementById('professorPage');
        if (professorPage) {
             professorPage.innerHTML = `
                <div class="max-w-7xl mx-auto px-4 py-8">
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                        <div class="flex justify-between items-center">
                            <div>
                                <h1 class="text-3xl font-bold senai-blue">Painel do Professor</h1>
                                <p class="text-gray-600 mt-1">Gerencie as fichas técnicas dos veículos</p>
                            </div>
                            <button onclick="logout()" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                <i class="fas fa-sign-out-alt mr-2"></i>Sair
                            </button>
                        </div>
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                         <div class="bg-white p-6 rounded-xl shadow-lg">
                            <div class="flex items-center">
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <i class="fas fa-car text-senai-blue text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-700">Total de Fichas</h3>
                                    <p id="statTotalVeiculos" class="text-2xl font-bold senai-blue">-</p>
                                </div>
                            </div>
                        </div>
                        <!-- Outros stats podem ser adicionados aqui se necessário -->
                    </div>

                    <!-- Ações -->
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                        <h2 class="text-xl font-bold mb-4 text-gray-800">Ações</h2>
                        <button onclick="openAddModal()" class="px-4 py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-800 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Adicionar Nova Ficha
                        </button>
                    </div>

                    <!-- Lista de Fichas -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-bold mb-4 text-gray-800">Fichas Cadastradas</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modelo</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ano</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="professorFichasList" class="bg-white divide-y divide-gray-200">
                                    <!-- Linhas serão inseridas aqui -->
                                    <tr><td colspan="4" class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Carregando...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Add/Edit (inicialmente oculto) -->
                <div id="fichaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                    <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
                        <div class="flex justify-between items-center border-b pb-3 mb-5">
                            <h3 id="modalTitle" class="text-2xl font-bold text-senai-blue">Adicionar Nova Ficha</h3>
                            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
                        </div>
                        <form id="fichaForm" class="space-y-4">
                            <input type="hidden" id="fichaId" name="id">
                            <!-- Campos do formulário serão gerados aqui dinamicamente ou estaticamente -->
                            <p class="text-center text-gray-500">(Campos do formulário para todas as seções da ficha técnica serão adicionados aqui)</p>
                            
                            <!-- Exemplo de campo -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="marca" class="block text-sm font-medium text-gray-700">Marca*</label>
                                    <input type="text" id="marca" name="marca" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm">
                                </div>
                                <div>
                                    <label for="modelo" class="block text-sm font-medium text-gray-700">Modelo*</label>
                                    <input type="text" id="modelo" name="modelo" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm">
                                </div>
                                <div>
                                    <label for="ano_modelo" class="block text-sm font-medium text-gray-700">Ano Modelo</label>
                                    <input type="number" id="ano_modelo" name="ano_modelo" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm">
                                </div>
                            </div>
                            
                            <!-- Adicionar todos os outros campos aqui, organizados por seções -->
                            
                            <div class="flex justify-end pt-4 space-x-2">
                                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Cancelar</button>
                                <button type="submit" id="modalSubmitButton" class="px-4 py-2 bg-senai-blue text-white rounded-md hover:bg-blue-800">Salvar Ficha</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            // Tenta buscar os elementos novamente após recriar a estrutura
            loadProfessorDashboard(); 
            return; 
        }
        return;
    }

    fichasList.innerHTML = `<tr><td colspan="4" class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Carregando fichas...</td></tr>`;
    statsVeiculos.textContent = '-';

    try {
        const response = await fetch('/api/fichas'); // Usa a mesma rota GET pública
        if (!response.ok) throw new Error('Erro ao buscar fichas');
        const fichas = await response.json();

        statsVeiculos.textContent = fichas.length;
        fichasList.innerHTML = ''; // Limpa o loading

        if (fichas.length === 0) {
            fichasList.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-gray-500">Nenhuma ficha cadastrada ainda.</td></tr>`;
        } else {
            fichas.forEach(ficha => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${ficha.marca}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${ficha.modelo}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${ficha.ano || 'N/A'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <button onclick="showCarDetail(${ficha.id})" class="text-blue-600 hover:text-blue-900" title="Ver Detalhes"><i class="fas fa-eye"></i></button>
                        <button onclick="openEditModal(${ficha.id})" class="text-yellow-600 hover:text-yellow-900" title="Editar"><i class="fas fa-edit"></i></button>
                        <button onclick="confirmDeleteFicha(${ficha.id})" class="text-red-600 hover:text-red-900" title="Excluir"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                fichasList.appendChild(row);
            });
        }

    } catch (error) {
        console.error("Erro ao carregar painel do professor:", error);
        fichasList.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-red-500">Erro ao carregar fichas.</td></tr>`;
        statsVeiculos.textContent = 'Erro';
    }
}

// --- Funções CRUD Fichas (Professor) ---

function openAddModal() {
    document.getElementById('fichaForm').reset();
    document.getElementById('fichaId').value = ''; // Garante que ID está vazio para adição
    document.getElementById('modalTitle').textContent = 'Adicionar Nova Ficha';
    document.getElementById('modalSubmitButton').textContent = 'Salvar Ficha';
    // TODO: Popular o formulário no modal com todos os campos necessários
    populateFormFields(); // Cria os campos do formulário dinamicamente
    document.getElementById('fichaModal').classList.remove('hidden');
}

async function openEditModal(id) {
    document.getElementById('fichaForm').reset();
    document.getElementById('modalTitle').textContent = 'Editar Ficha Técnica';
    document.getElementById('modalSubmitButton').textContent = 'Atualizar Ficha';
    populateFormFields(); // Garante que os campos existem
    
    try {
        const response = await fetch(`/api/fichas/${id}`);
        if (!response.ok) throw new Error('Ficha não encontrada');
        const ficha = await response.json();
        
        // Preenche o formulário com os dados da ficha
        document.getElementById('fichaId').value = ficha.id;
        // Preencher todos os campos do formulário com os dados de 'ficha'
        for (const key in ficha) {
            const input = document.getElementById(key);
            if (input) {
                if (input.type === 'checkbox') {
                    input.checked = !!ficha[key]; // Converte para booleano
                } else {
                    input.value = ficha[key] !== null && ficha[key] !== undefined ? ficha[key] : '';
                }
            }
        }
        
        document.getElementById('fichaModal').classList.remove('hidden');
        
    } catch (error) {
        console.error("Erro ao carregar dados para edição:", error);
        alert("Erro ao carregar dados da ficha para edição.");
    }
}

function closeModal() {
    document.getElementById('fichaModal').classList.add('hidden');
}

// Função para lidar com o submit do formulário (Adicionar/Editar)
document.getElementById('fichaForm')?.addEventListener('submit', async function(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const fichaData = {};
    formData.forEach((value, key) => {
        // Tratar checkboxes (podem não ser enviados se desmarcados)
        const input = form.querySelector(`[name="${key}"]`);
        if (input && input.type === 'checkbox') {
            fichaData[key] = input.checked;
        } else if (value !== '') { // Não envia campos vazios (exceto checkboxes)
             // Tenta converter para número se for campo numérico
             if (input && (input.type === 'number' || input.inputMode === 'decimal')) {
                 const numValue = parseFloat(value);
                 if (!isNaN(numValue)) {
                     fichaData[key] = numValue;
                 } else {
                     fichaData[key] = null; // Ou mantém como string vazia? Depende do backend
                 }
             } else {
                 fichaData[key] = value;
             }
        } else {
             fichaData[key] = null; // Envia null para campos vazios não-checkbox
        }
    });

    const fichaId = document.getElementById('fichaId').value;
    const method = fichaId ? 'PUT' : 'POST';
    const url = fichaId ? `/api/fichas/${fichaId}` : '/api/fichas';

    const submitButton = document.getElementById('modalSubmitButton');
    const originalButtonText = submitButton.textContent;
    submitButton.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>Salvando...`;
    submitButton.disabled = true;

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                // O token é enviado automaticamente pelo cookie
            },
            body: JSON.stringify(fichaData),
        });

        const result = await response.json();

        if (response.ok) {
            alert(result.message);
            closeModal();
            loadProfessorDashboard(); // Recarrega a lista no painel
            // Opcional: recarregar a lista pública se a página de fichas estiver ativa
            if (document.getElementById('fichasPage').classList.contains('active')) {
                applyFilters();
            }
        } else {
            alert(`Erro: ${result.message}`);
        }
    } catch (error) {
        console.error("Erro ao salvar ficha:", error);
        alert("Erro de conexão ao salvar a ficha.");
    } finally {
        submitButton.innerHTML = originalButtonText;
        submitButton.disabled = false;
    }
});

// Função para confirmar e deletar ficha
function confirmDeleteFicha(id) {
    if (confirm("Tem certeza que deseja excluir esta ficha técnica permanentemente?")) {
        deleteFicha(id);
    }
}

async function deleteFicha(id) {
    try {
        const response = await fetch(`/api/fichas/${id}`, {
            method: 'DELETE',
            // Token enviado via cookie
        });

        const result = await response.json();

        if (response.ok) {
            alert(result.message);
            loadProfessorDashboard(); // Recarrega a lista no painel
            // Opcional: recarregar a lista pública
            if (document.getElementById('fichasPage').classList.contains('active')) {
                applyFilters();
            }
            // Se a ficha excluída estava sendo visualizada, volta para a lista
            if (document.getElementById('detailPage').classList.contains('active')) {
                const currentDetailId = document.getElementById('fichaId')?.value; // Pega ID do form se estiver editando
                // Ou precisa de outra forma de saber qual ficha está no detalhe
                // Se for a mesma, volta pra lista
                // if (currentDetailId == id) { // Comparação frouxa intencional
                     showPage('fichas');
                // }
            }
        } else {
            alert(`Erro ao excluir: ${result.message}`);
        }
    } catch (error) {
        console.error("Erro ao excluir ficha:", error);
        alert("Erro de conexão ao excluir a ficha.");
    }
}

// --- Geração de PDF (Frontend) ---
// Depende da biblioteca jsPDF ou outra similar incluída no HTML
// Esta função precisa ser adaptada para buscar os dados completos da API
async function generatePDF(marca, modelo) {
    // Verifica se a biblioteca jsPDF está carregada
    if (typeof window.jspdf === 'undefined') {
        console.error('Biblioteca jsPDF não carregada.');
        alert('Erro: A funcionalidade de gerar PDF não está disponível.');
        return;
    }
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF();

    // Busca os dados completos da ficha que está sendo visualizada
    // Assume que os dados estão no elemento #carDetailContent ou busca pela API
    const detailContentElement = document.getElementById('carDetailContent');
    if (!detailContentElement) {
        alert('Erro ao encontrar os dados da ficha para gerar o PDF.');
        return;
    }

    // Tentar extrair dados do HTML ou buscar da API novamente
    // Exemplo simples extraindo do HTML (pode ser frágil)
    const titulo = detailContentElement.querySelector('h2')?.textContent || `${marca} ${modelo}`;
    const especificacoes = detailContentElement.querySelectorAll('.spec-item');
    
    pdf.setFontSize(18);
    pdf.text(titulo, 10, 20);
    pdf.setFontSize(12);
    let yPos = 30;

    especificacoes.forEach(item => {
        const label = item.querySelector('.text-sm')?.textContent || '';
        const value = item.querySelector('.font-semibold')?.textContent || '';
        if (label && value && yPos < 280) { // Adiciona verificação de limite de página
            pdf.setFontSize(10);
            pdf.setTextColor(100);
            pdf.text(label, 10, yPos);
            pdf.setFontSize(12);
            pdf.setTextColor(0);
            pdf.text(value, 50, yPos);
            yPos += 7;
        } else if (yPos >= 280) {
            pdf.addPage();
            yPos = 20;
            // Repete o item na nova página
             pdf.setFontSize(10);
            pdf.setTextColor(100);
            pdf.text(label, 10, yPos);
            pdf.setFontSize(12);
            pdf.setTextColor(0);
            pdf.text(value, 50, yPos);
            yPos += 7;
        }
    });

    // Adiciona um rodapé simples
    pdf.setFontSize(8);
    pdf.setTextColor(150);
    pdf.text(`Ficha Técnica gerada por SENAI Automotivo - ${new Date().toLocaleDateString()}`, 10, 290);

    // Salva o PDF
    pdf.save(`ficha_tecnica_${marca}_${modelo}.pdf`);
}

// --- Inicialização ---

document.addEventListener('DOMContentLoaded', () => {
    checkSession().then(() => {
        // Após verificar a sessão, mostra a página inicial ou a de fichas
        // A lógica de qual página mostrar pode ser mais complexa (ex: hash na URL)
        const initialPage = 'home'; // Ou 'fichas' se preferir
        showPage(initialPage);
        
        // Adiciona listeners aos botões de filtro que não foram cobertos pelo onclick
        document.getElementById('searchInput')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });
        document.querySelector('button[onclick="loadCars()"]')?.addEventListener('click', applyFilters);
        
        // Adiciona listener para o botão de busca principal
        const searchButton = document.querySelector('#fichasPage .flex button');
        if(searchButton && !searchButton.onclick) { // Evita duplicar listener se já tiver onclick
             searchButton.addEventListener('click', applyFilters);
        }

    });
});

// Função para popular dinamicamente os campos do formulário no modal
// Isso garante que todos os campos do banco de dados sejam representados
function populateFormFields() {
    const form = document.getElementById('fichaForm');
    // Limpa campos existentes exceto o hidden ID
    form.innerHTML = '<input type="hidden" id="fichaId" name="id">'; 

    const fieldGroups = {
        'Informações Básicas': [
            { id: 'marca', label: 'Marca*', type: 'text', required: true },
            { id: 'modelo', label: 'Modelo*', type: 'text', required: true },
            { id: 'ano_fabricacao', label: 'Ano Fabricação', type: 'number' },
            { id: 'ano_modelo', label: 'Ano Modelo', type: 'number' },
            { id: 'versao', label: 'Versão', type: 'text' },
            { id: 'codigo_motor', label: 'Código do Motor', type: 'text' },
            { id: 'tipo_combustivel', label: 'Combustível', type: 'text' }, // Poderia ser select
            { id: 'imagem_url', label: 'URL da Imagem Principal', type: 'url' },
            { id: 'preco', label: 'Preço (R$)', type: 'number', step: '0.01' },
            { id: 'descricao', label: 'Descrição Breve', type: 'textarea' },
        ],
        'Motorização': [
            { id: 'tipo_motor', label: 'Tipo de Motor', type: 'text' },
            { id: 'cilindrada', label: 'Cilindrada (L)', type: 'number', step: '0.1' },
            { id: 'potencia_maxima', label: 'Potência Máxima (cv)', type: 'number' },
            { id: 'torque_maximo', label: 'Torque Máximo (kgfm)', type: 'number', step: '0.1' },
            { id: 'numero_valvulas', label: 'Nº de Válvulas', type: 'number' },
            { id: 'tipo_injecao', label: 'Tipo de Injeção', type: 'text' },
        ],
        'Transmissão': [
            { id: 'tipo_cambio', label: 'Tipo de Câmbio', type: 'text' }, // Poderia ser select
            { id: 'numero_marchas', label: 'Número de Marchas', type: 'number' },
        ],
        'Suspensão e Freios': [
            { id: 'suspensao_dianteira', label: 'Suspensão Dianteira', type: 'text' },
            { id: 'suspensao_traseira', label: 'Suspensão Traseira', type: 'text' },
            { id: 'freios_dianteiros', label: 'Freios Dianteiros', type: 'text' },
            { id: 'freios_traseiros', label: 'Freios Traseiros', type: 'text' },
            { id: 'possui_abs', label: 'Possui ABS', type: 'checkbox' },
            { id: 'possui_ebd', label: 'Possui EBD', type: 'checkbox' },
        ],
        'Direção e Pneus': [
            { id: 'tipo_direcao', label: 'Tipo de Direção', type: 'text' },
            { id: 'pneus_originais', label: 'Pneus Originais', type: 'text' },
        ],
        'Dimensões': [
            { id: 'comprimento_mm', label: 'Comprimento (mm)', type: 'number' },
            { id: 'largura_mm', label: 'Largura (mm)', type: 'number' },
            { id: 'altura_mm', label: 'Altura (mm)', type: 'number' },
            { id: 'entre_eixos_mm', label: 'Entre-eixos (mm)', type: 'number' },
            { id: 'altura_solo_mm', label: 'Altura do Solo (mm)', type: 'number' },
            { id: 'peso_kg', label: 'Peso (kg)', type: 'number' },
        ],
        'Desempenho e Consumo': [
            { id: 'velocidade_maxima_kmh', label: 'Velocidade Máx. (km/h)', type: 'number' },
            { id: 'aceleracao_0_100_s', label: 'Aceleração 0-100 (s)', type: 'number', step: '0.1' },
            { id: 'consumo_urbano_km_l', label: 'Consumo Urbano (km/l)', type: 'number', step: '0.1' },
            { id: 'consumo_rodoviario_km_l', label: 'Consumo Rodoviário (km/l)', type: 'number', step: '0.1' },
            { id: 'capacidade_tanque_l', label: 'Capacidade Tanque (L)', type: 'number' },
        ],
        'Capacidades': [
            { id: 'porta_malas_l', label: 'Porta-malas (L)', type: 'number' },
            { id: 'carga_util_kg', label: 'Carga Útil (kg)', type: 'number' },
            { id: 'numero_ocupantes', label: 'Nº de Ocupantes', type: 'number' },
        ],
        'Sistemas e Eletrônica (Opcional)': [
            { id: 'sistema_injecao_detalhes', label: 'Injeção (Detalhes)', type: 'textarea' },
            { id: 'sonda_lambda_detalhes', label: 'Sonda Lambda (Detalhes)', type: 'textarea' },
            { id: 'ecu_detalhes', label: 'ECU (Detalhes)', type: 'textarea' },
            { id: 'sensores_detalhes', label: 'Sensores (Detalhes)', type: 'textarea' },
            { id: 'outros_sistemas_eletronicos', label: 'Outros Sistemas', type: 'textarea' },
        ]
    };

    for (const groupName in fieldGroups) {
        const groupDiv = document.createElement('div');
        groupDiv.className = 'mb-6 border border-gray-200 p-4 rounded-md';
        groupDiv.innerHTML = `<h4 class="text-lg font-semibold mb-3 border-b pb-1 border-gray-300">${groupName}</h4>`;
        
        const fieldsContainer = document.createElement('div');
        fieldsContainer.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4';
        
        fieldGroups[groupName].forEach(field => {
            const fieldDiv = document.createElement('div');
            const label = document.createElement('label');
            label.htmlFor = field.id;
            label.className = 'block text-sm font-medium text-gray-700';
            label.textContent = field.label;
            fieldDiv.appendChild(label);

            let input;
            if (field.type === 'textarea') {
                input = document.createElement('textarea');
                input.rows = 2;
            } else if (field.type === 'checkbox') {
                input = document.createElement('input');
                input.type = 'checkbox';
                input.className = 'mt-1 h-4 w-4 text-senai-blue focus:ring-senai-blue border-gray-300 rounded';
                // Checkbox alignment might need adjustment
                fieldDiv.classList.add('flex', 'items-center', 'gap-2'); // Adjust layout for checkbox
                label.classList.add('mb-0'); // Remove bottom margin for checkbox label
                fieldDiv.appendChild(input); // Append input before label text for standard layout
                fieldDiv.insertBefore(input, label);
            } else {
                input = document.createElement('input');
                input.type = field.type;
                if (field.step) input.step = field.step;
            }
            
            if (field.type !== 'checkbox') {
                 input.className = 'mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm';
                 fieldDiv.appendChild(input);
            }
            
            input.id = field.id;
            input.name = field.id;
            if (field.required) input.required = true;

            fieldsContainer.appendChild(fieldDiv);
        });
        groupDiv.appendChild(fieldsContainer);
        form.appendChild(groupDiv);
    }
    
    // Adiciona os botões no final
    const buttonDiv = document.createElement('div');
    buttonDiv.className = 'flex justify-end pt-4 space-x-2';
    buttonDiv.innerHTML = `
        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Cancelar</button>
        <button type="submit" id="modalSubmitButton" class="px-4 py-2 bg-senai-blue text-white rounded-md hover:bg-blue-800">Salvar Ficha</button>
    `;
    form.appendChild(buttonDiv);
}

