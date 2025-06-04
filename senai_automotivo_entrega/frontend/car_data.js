// Dados dos carros com características para filtro
const carData = [
    {
        id: 1,
        marca: "Chevrolet",
        modelo: "Onix",
        ano: 2023,
        preco: 79990,
        motor: "1.0 Turbo",
        potencia: 116,
        combustivel: "Flex",
        transmissao: "Manual",
        carroceria: "Hatch",
        portas: 4,
        consumo_cidade: 12.2,
        consumo_estrada: 15.1,
        imagem: "/images/Mrbm6Jp1axfg.jpg",
        descricao: "O Chevrolet Onix é um dos carros mais vendidos do Brasil, oferecendo ótimo custo-benefício, economia de combustível e tecnologia embarcada de ponta."
    },
    {
        id: 2,
        marca: "Volkswagen",
        modelo: "Gol",
        ano: 2022,
        preco: 68990,
        motor: "1.0",
        potencia: 84,
        combustivel: "Flex",
        transmissao: "Manual",
        carroceria: "Hatch",
        portas: 4,
        consumo_cidade: 11.9,
        consumo_estrada: 14.3,
        imagem: "/images/RJ6X7nbFwUtu.jpg",
        descricao: "O Volkswagen Gol é um clássico do mercado brasileiro, conhecido por sua robustez, simplicidade e baixo custo de manutenção."
    },
    {
        id: 3,
        marca: "Fiat",
        modelo: "Argo",
        ano: 2023,
        preco: 75990,
        motor: "1.3",
        potencia: 109,
        combustivel: "Flex",
        transmissao: "Manual",
        carroceria: "Hatch",
        portas: 4,
        consumo_cidade: 11.8,
        consumo_estrada: 14.5,
        imagem: "/images/q2oQEFDzjkH1.png",
        descricao: "O Fiat Argo se destaca pelo design moderno, bom espaço interno e conjunto mecânico eficiente, sendo uma excelente opção entre os hatches compactos."
    },
    {
        id: 4,
        marca: "Honda",
        modelo: "Civic",
        ano: 2023,
        preco: 149990,
        motor: "2.0",
        potencia: 155,
        combustivel: "Flex",
        transmissao: "Automática",
        carroceria: "Sedan",
        portas: 4,
        consumo_cidade: 9.5,
        consumo_estrada: 12.2,
        imagem: "/images/M7FGoWXLWn4u.png",
        descricao: "O Honda Civic é referência em seu segmento, oferecendo refinamento, conforto, desempenho e tecnologia de ponta em um pacote completo."
    },
    {
        id: 5,
        marca: "Toyota",
        modelo: "Corolla",
        ano: 2023,
        preco: 159990,
        motor: "2.0",
        potencia: 177,
        combustivel: "Flex",
        transmissao: "Automática",
        carroceria: "Sedan",
        portas: 4,
        consumo_cidade: 10.6,
        consumo_estrada: 13.2,
        imagem: "/images/PqD79BflrjgR.webp",
        descricao: "O Toyota Corolla é sinônimo de confiabilidade e durabilidade, com acabamento refinado e excelente valor de revenda."
    },
    {
        id: 6,
        marca: "Hyundai",
        modelo: "HB20",
        ano: 2023,
        preco: 82990,
        motor: "1.0 Turbo",
        potencia: 120,
        combustivel: "Flex",
        transmissao: "Automática",
        carroceria: "Hatch",
        portas: 4,
        consumo_cidade: 11.6,
        consumo_estrada: 14.5,
        imagem: "/images/FC7E0Uj3FbSI.png",
        descricao: "O Hyundai HB20 se destaca pelo design arrojado, bom pacote de equipamentos e motor turbo eficiente nas versões mais equipadas."
    },
    {
        id: 7,
        marca: "Jeep",
        modelo: "Renegade",
        ano: 2023,
        preco: 129990,
        motor: "1.3 Turbo",
        potencia: 185,
        combustivel: "Flex",
        transmissao: "Automática",
        carroceria: "SUV",
        portas: 5,
        consumo_cidade: 9.8,
        consumo_estrada: 11.9,
        imagem: "/images/duoqypXPYyDh.jpg",
        descricao: "O Jeep Renegade combina robustez, capacidade off-road e conforto urbano em um SUV compacto com personalidade única."
    },
    {
        id: 8,
        marca: "Volkswagen",
        modelo: "T-Cross",
        ano: 2023,
        preco: 138990,
        motor: "1.4 TSI",
        potencia: 150,
        combustivel: "Flex",
        transmissao: "Automática",
        carroceria: "SUV",
        portas: 5,
        consumo_cidade: 10.1,
        consumo_estrada: 12.5,
        imagem: "/images/o2XAImQd0mmV.jpg",
        descricao: "O Volkswagen T-Cross é um SUV versátil com ótimo espaço interno, tecnologia alemã e excelente dirigibilidade."
    },
    {
        id: 9,
        marca: "Chevrolet",
        modelo: "Tracker",
        ano: 2023,
        preco: 134990,
        motor: "1.2 Turbo",
        potencia: 133,
        combustivel: "Flex",
        transmissao: "Automática",
        carroceria: "SUV",
        portas: 5,
        consumo_cidade: 11.2,
        consumo_estrada: 13.8,
        imagem: "/images/Mrbm6Jp1axfg.jpg", // Substituído por uma imagem existente
        descricao: "O Chevrolet Tracker oferece tecnologia embarcada de ponta, bom espaço interno e motor turbo econômico."
    },
    {
        id: 10,
        marca: "Toyota",
        modelo: "Corolla Cross",
        ano: 2023,
        preco: 169990,
        motor: "2.0 Hybrid",
        potencia: 177,
        combustivel: "Híbrido",
        transmissao: "CVT",
        carroceria: "SUV",
        portas: 5,
        consumo_cidade: 16.3,
        consumo_estrada: 18.5,
        imagem: "/images/2UIhIDAHakZG.jpg",
        descricao: "O Toyota Corolla Cross combina a confiabilidade do Corolla com a versatilidade de um SUV, disponível também em versão híbrida de alta eficiência."
    },
    {
        id: 11,
        marca: "Honda",
        modelo: "HR-V",
        ano: 2023,
        preco: 149990,
        motor: "1.5 Turbo",
        potencia: 177,
        combustivel: "Flex",
        transmissao: "CVT",
        carroceria: "SUV",
        portas: 5,
        consumo_cidade: 10.5,
        consumo_estrada: 12.8,
        imagem: "/images/ZRW1V7HSRZ0I.jpg",
        descricao: "O Honda HR-V se destaca pelo design sofisticado, interior espaçoso e versatilidade do sistema de bancos Magic Seat."
    },
    {
        id: 12,
        marca: "Hyundai",
        modelo: "Creta",
        ano: 2023,
        preco: 139990,
        motor: "1.0 Turbo",
        potencia: 120,
        combustivel: "Flex",
        transmissao: "Automática",
        carroceria: "SUV",
        portas: 5,
        consumo_cidade: 11.0,
        consumo_estrada: 13.7,
        imagem: "/images/a8lPDJj1Ep3K.jpg",
        descricao: "O Hyundai Creta oferece design marcante, bom pacote de equipamentos e conforto superior para sua categoria."
    }
];

// Função para carregar os carros na página
function loadCars(filter = 'all') {
    const carsGrid = document.getElementById('carsGrid');
    carsGrid.innerHTML = '';
    
    let filteredCars = carData;
    
    // Filtrar por marca
    if (filter !== 'all') {
        filteredCars = carData.filter(car => car.marca === filter);
    }
    
    // Aplicar filtros adicionais
    const potenciaFilter = document.getElementById('potenciaFilter').value;
    const combustivelFilter = document.getElementById('combustivelFilter').value;
    const transmissaoFilter = document.getElementById('transmissaoFilter').value;
    const carroceriaFilter = document.getElementById('carroceriaFilter').value;
    
    if (potenciaFilter) {
        const [min, max] = potenciaFilter.split('-').map(Number);
        filteredCars = filteredCars.filter(car => car.potencia >= min && car.potencia <= max);
    }
    
    if (combustivelFilter && combustivelFilter !== 'todos') {
        filteredCars = filteredCars.filter(car => car.combustivel === combustivelFilter);
    }
    
    if (transmissaoFilter && transmissaoFilter !== 'todos') {
        filteredCars = filteredCars.filter(car => car.transmissao === transmissaoFilter);
    }
    
    if (carroceriaFilter && carroceriaFilter !== 'todos') {
        filteredCars = filteredCars.filter(car => car.carroceria === carroceriaFilter);
    }
    
    // Verificar se há resultados
    if (filteredCars.length === 0) {
        carsGrid.innerHTML = `
            <div class="col-span-3 text-center py-8">
                <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">Nenhum carro encontrado com os filtros selecionados.</p>
                <button onclick="resetFilters()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Limpar Filtros
                </button>
            </div>
        `;
        return;
    }
    
    // Exibir carros filtrados
    filteredCars.forEach(car => {
        const carCard = document.createElement('div');
        carCard.className = 'car-card bg-white rounded-xl shadow-lg overflow-hidden';
        carCard.innerHTML = `
            <div class="relative">
                <img src="${car.imagem}" alt="${car.marca} ${car.modelo}" class="w-full h-48 object-cover">
                <div class="absolute top-0 right-0 bg-blue-600 text-white px-3 py-1 m-2 rounded-lg text-sm font-semibold">
                    ${car.ano}
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold mb-2">${car.marca} ${car.modelo}</h3>
                <div class="flex items-center text-gray-600 mb-4">
                    <span class="text-lg font-semibold text-blue-600">R$ ${car.preco.toLocaleString('pt-BR')}</span>
                </div>
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-gas-pump mr-2 text-blue-600"></i>
                        ${car.combustivel}
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-cogs mr-2 text-blue-600"></i>
                        ${car.motor}
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-tachometer-alt mr-2 text-blue-600"></i>
                        ${car.potencia} cv
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-car mr-2 text-blue-600"></i>
                        ${car.carroceria}
                    </div>
                </div>
                <button onclick="showCarDetail(${car.id})" class="w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Ver Detalhes
                </button>
            </div>
        `;
        carsGrid.appendChild(carCard);
    });
}

// Função para mostrar detalhes do carro
function showCarDetail(carId) {
    const car = carData.find(car => car.id === carId);
    if (!car) return;
    
    const detailContent = document.getElementById('carDetailContent');
    detailContent.innerHTML = `
        <div class="relative">
            <img src="${car.imagem}" alt="${car.marca} ${car.modelo}" class="w-full h-64 object-cover">
            <div class="absolute top-0 right-0 bg-blue-600 text-white px-3 py-1 m-4 rounded-lg text-sm font-semibold">
                ${car.ano}
            </div>
        </div>
        <div class="p-6">
            <h2 class="text-3xl font-bold mb-2">${car.marca} ${car.modelo}</h2>
            <div class="flex items-center text-gray-600 mb-6">
                <span class="text-2xl font-semibold text-blue-600">R$ ${car.preco.toLocaleString('pt-BR')}</span>
            </div>
            
            <p class="text-gray-700 mb-6">${car.descricao}</p>
            
            <h3 class="text-xl font-bold mb-4 text-blue-600">Especificações Técnicas</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="spec-item p-3 pl-4 bg-gray-50">
                    <span class="block text-sm text-gray-500">Motor</span>
                    <span class="font-semibold">${car.motor}</span>
                </div>
                <div class="spec-item p-3 pl-4 bg-gray-50">
                    <span class="block text-sm text-gray-500">Potência</span>
                    <span class="font-semibold">${car.potencia} cv</span>
                </div>
                <div class="spec-item p-3 pl-4 bg-gray-50">
                    <span class="block text-sm text-gray-500">Combustível</span>
                    <span class="font-semibold">${car.combustivel}</span>
                </div>
                <div class="spec-item p-3 pl-4 bg-gray-50">
                    <span class="block text-sm text-gray-500">Transmissão</span>
                    <span class="font-semibold">${car.transmissao}</span>
                </div>
                <div class="spec-item p-3 pl-4 bg-gray-50">
                    <span class="block text-sm text-gray-500">Carroceria</span>
                    <span class="font-semibold">${car.carroceria}</span>
                </div>
                <div class="spec-item p-3 pl-4 bg-gray-50">
                    <span class="block text-sm text-gray-500">Portas</span>
                    <span class="font-semibold">${car.portas}</span>
                </div>
                <div class="spec-item p-3 pl-4 bg-gray-50">
                    <span class="block text-sm text-gray-500">Consumo Urbano</span>
                    <span class="font-semibold">${car.consumo_cidade} km/l</span>
                </div>
                <div class="spec-item p-3 pl-4 bg-gray-50">
                    <span class="block text-sm text-gray-500">Consumo Rodoviário</span>
                    <span class="font-semibold">${car.consumo_estrada} km/l</span>
                </div>
            </div>
            
            <div class="flex justify-center">
                <button onclick="showPage('fichas')" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors mr-4">
                    Voltar
                </button>
                <button class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Solicitar Cotação
                </button>
            </div>
        </div>
    `;
    
    showPage('detail');
}

// Função para filtrar carros
function filterCars(filter) {
    // Atualizar botões de filtro
    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    
    const activeButton = document.querySelector(`.filter-btn[onclick="filterCars('${filter}')"]`);
    if (activeButton) {
        activeButton.classList.remove('bg-gray-200', 'text-gray-700');
        activeButton.classList.add('bg-blue-600', 'text-white');
    }
    
    loadCars(filter);
}

// Função para filtrar por marca na página inicial
function filterByBrand(brand) {
    showPage('fichas');
    filterCars(brand);
}

// Função para resetar filtros
function resetFilters() {
    document.getElementById('potenciaFilter').value = '';
    document.getElementById('combustivelFilter').value = 'todos';
    document.getElementById('transmissaoFilter').value = 'todos';
    document.getElementById('carroceriaFilter').value = 'todos';
    
    filterCars('all');
}

// Função para alternar entre páginas
function showPage(pageId) {
    const pages = document.querySelectorAll('.page');
    pages.forEach(page => {
        page.classList.remove('active');
    });
    
    document.getElementById(pageId + 'Page').classList.add('active');
    
    // Carregar carros quando a página de fichas é exibida
    if (pageId === 'fichas') {
        loadCars();
    }
}

// Função para alternar menu mobile
function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobileMenu');
    mobileMenu.classList.toggle('hidden');
}

// Função de login (simulada)
function handleLogin(event) {
    event.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    // Simulação de login
    if (username === 'prof@senai.com' && password === 'senha123') {
        showPage('professor');
    } else {
        alert('Usuário ou senha incorretos!');
    }
}

// Função de logout
function logout() {
    showPage('home');
}

// Carregar carros quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar a página
    showPage('home');
});
