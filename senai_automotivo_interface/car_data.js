// Sample data - In real implementation, this would come from PHP/MySQL
        let carsData = [
            {
                id: 1,
                model: 'Onix',
                brand: 'Chevrolet',
                year: 2023,
                image: 'https://via.placeholder.com/400x250/254AA5/ffffff?text=Chevrolet+Onix',
                specs: {
                    motor: '1.0 Turbo',
                    potencia: '116 cv',
                    torque: '16,8 kgfm',
                    consumo: '13,7 km/l (cidade) / 17,1 km/l (estrada)',
                    cambio: 'Manual de 6 marchas',
                    tracao: 'Dianteira',
                    portaMalas: '275 litros',
                    portas: '4',
                    peso: '1.050 kg'
                }
            },
            {
                id: 2,
                model: 'Golf GTI',
                brand: 'Volkswagen',
                year: 2023,
                image: 'https://via.placeholder.com/400x250/254AA5/ffffff?text=VW+Golf+GTI',
                specs: {
                    motor: '2.0 TSI',
                    potencia: '230 cv',
                    torque: '35,7 kgfm',
                    consumo: '9,8 km/l (cidade) / 12,3 km/l (estrada)',
                    cambio: 'Automático DSG de 7 marchas',
                    tracao: 'Dianteira',
                    portaMalas: '380 litros',
                    portas: '5',
                    peso: '1.420 kg'
                }
            },
            {
                id: 3,
                model: 'Civic',
                brand: 'Honda',
                year: 2023,
                image: 'https://via.placeholder.com/400x250/254AA5/ffffff?text=Honda+Civic',
                specs: {
                    motor: '2.0 i-VTEC',
                    potencia: '155 cv',
                    torque: '19,4 kgfm',
                    consumo: '12,1 km/l (cidade) / 16,8 km/l (estrada)',
                    cambio: 'CVT',
                    tracao: 'Dianteira',
                    portaMalas: '519 litros',
                    portas: '4',
                    peso: '1.350 kg'
                }
            },
            {
                id: 4,
                model: 'Corolla',
                brand: 'Toyota',
                year: 2023,
                image: 'https://via.placeholder.com/400x250/254AA5/ffffff?text=Toyota+Corolla',
                specs: {
                    motor: '2.0 Flex',
                    potencia: '177 cv',
                    torque: '21,4 kgfm',
                    consumo: '11,2 km/l (cidade) / 15,1 km/l (estrada)',
                    cambio: 'CVT',
                    tracao: 'Dianteira',
                    portaMalas: '470 litros',
                    portas: '4',
                    peso: '1.395 kg'
                }
            }
        ];

        // User session management
        let currentUser = null;
        let userType = null;

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('currentYear').textContent = new Date().getFullYear();
            loadCars();
            loadProfessorCars();
        });

        // Page navigation
        function showPage(pageId) {
            // Hide all pages
            document.querySelectorAll('.page').forEach(page => {
                page.classList.remove('active');
            });
            
            // Show selected page
            document.getElementById(pageId + 'Page').classList.add('active');
            
            // Special handling for fichas page
            if (pageId === 'fichas') {
                loadCars();
            }
        }

        // Mobile menu toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }

        // Função para resetar filtros
function resetFilters() {
    document.getElementById('potenciaFilter').value = '';
    document.getElementById('combustivelFilter').value = 'todos';
    document.getElementById('transmissaoFilter').value = 'todos';
    document.getElementById('carroceriaFilter').value = 'todos';
    
    filterCars('all');
}

// Atualizar função loadCars para incluir filtros avançados
function loadCars(filter = null, search = '') {
    const grid = document.getElementById('carsGrid');
    const loading = document.getElementById('loading');
    
    loading.style.display = 'block';
    
    setTimeout(() => {
        let filteredCars = carsData;
        
        // Aplicar filtros básicos
        if (filter && filter !== 'all') {
            filteredCars = filteredCars.filter(car => car.brand === filter);
        }
        
        if (search) {
            filteredCars = filteredCars.filter(car => 
                car.model.toLowerCase().includes(search.toLowerCase()) ||
                car.brand.toLowerCase().includes(search.toLowerCase()) ||
                car.year.toString().includes(search)
            );
        }
        
        // Aplicar filtros avançados
        const potenciaFilter = document.getElementById('potenciaFilter').value;
        const combustivelFilter = document.getElementById('combustivelFilter').value;
        const transmissaoFilter = document.getElementById('transmissaoFilter').value;
        const carroceriaFilter = document.getElementById('carroceriaFilter').value;
        
        if (potenciaFilter) {
            const [min, max] = potenciaFilter.split('-').map(Number);
            filteredCars = filteredCars.filter(car => 
                car.specs.potencia >= min && car.specs.potencia <= max
            );
        }
        
        if (combustivelFilter && combustivelFilter !== 'todos') {
            filteredCars = filteredCars.filter(car => 
                car.specs.combustivel === combustivelFilter
            );
        }
        
        if (transmissaoFilter && transmissaoFilter !== 'todos') {
            filteredCars = filteredCars.filter(car => 
                car.specs.cambio.includes(transmissaoFilter)
            );
        }
        
        if (carroceriaFilter && carroceriaFilter !== 'todos') {
            filteredCars = filteredCars.filter(car => 
                car.specs.carroceria === carroceriaFilter
            );
        }
        
        // Verificar se há resultados
        if (filteredCars.length === 0) {
            grid.innerHTML = `
                <div class="col-span-3 text-center py-8">
                    <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600">Nenhum carro encontrado com os filtros selecionados.</p>
                    <button onclick="resetFilters()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Limpar Filtros
                    </button>
                </div>
            `;
            loading.style.display = 'none';
            return;
        }
        
        grid.innerHTML = filteredCars.map(car => `
            <div class="car-card bg-white rounded-xl shadow-lg overflow-hidden">
                <img src="${car.image}" alt="${car.brand} ${car.model}" class="w-full h-48 object-cover">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">${car.brand} ${car.model}</h3>
                            <p class="text-gray-600">${car.year}</p>
                        </div>
                        <span class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">${car.specs.motor}</span>
                    </div>
                    
                    <div class="space-y-2 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Potência:</span>
                            <span class="font-medium">${car.specs.potencia}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Torque:</span>
                            <span class="font-medium">${car.specs.torque}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Câmbio:</span>
                            <span class="font-medium">${car.specs.cambio}</span>
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        <button onclick="showCarDetail(${car.id})" class="flex-1 bg-senai-blue text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                            <i class="fas fa-eye mr-2"></i>Ver Detalhes
                        </button>
                        <button onclick="downloadPDF(${car.id})" class="bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 transition-colors text-sm">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
        
        loading.style.display = 'none';
    }, 500);
}

// Show car detail page
function showCarDetail(carId) {
    const car = carsData.find(c => c.id === carId);
    if (!car) return;
    
    const content = document.getElementById('carDetailContent');
    content.innerHTML = `
        <div class="relative">
            <img src="${car.image}" alt="${car.brand} ${car.model}" class="w-full h-64 md:h-80 object-cover">
            <div class="absolute top-4 right-4">
                <button onclick="downloadPDF(${car.id})" class="bg-white text-gray-800 px-4 py-2 rounded-lg shadow-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-download mr-2"></i>Download PDF
                </button>
            </div>
        </div>
        
        <div class="p-8">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold senai-blue mb-2">${car.brand} ${car.model}</h1>
                <p class="text-xl text-gray-600">${car.year}</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="detail-section rounded-xl p-6">
                    <h2 class="text-2xl font-bold mb-6 senai-blue">
                        <i class="fas fa-cog mr-2"></i>Especificações Técnicas
                    </h2>
                    <div class="space-y-4">
                        ${Object.entries(car.specs).map(([key, value]) => `
                            <div class="spec-item bg-white p-4 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700 capitalize">${key.replace(/([A-Z])/g, ' $1').trim()}:</span>
                                    <span class="text-gray-900 font-semibold">${value}</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
                
                <div class="detail-section rounded-xl p-6">
                    <h2 class="text-2xl font-bold mb-6 senai-blue">
                        <i class="fas fa-info-circle mr-2"></i>Informações Adicionais
                    </h2>
                    <div class="space-y-4">
                        <div class="bg-white p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-800 mb-2">Categoria</h3>
                            <p class="text-gray-600">Veículo de passeio</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-800 mb-2">Combustível</h3>
                            <p class="text-gray-600">Flex (Etanol/Gasolina)</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-800 mb-2">Última Atualização</h3>
                            <p class="text-gray-600">${new Date().toLocaleDateString('pt-BR')}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 text-center">
                <p class="text-gray-500 text-sm">
                    Ficha técnica fornecida pelo SENAI - Dados sujeitos a alterações sem aviso prévio
                </p>
            </div>
        </div>
    `;
    
    showPage('detail');
}

// Filter cars by brand
function filterCars(brand) {
    // Update active filter button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-senai-blue', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    
    event.target.classList.add('active', 'bg-senai-blue', 'text-white');
    event.target.classList.remove('bg-gray-200', 'text-gray-700');
    
    // Load filtered cars
    const searchTerm = document.getElementById('searchInput').value;
    loadCars(brand === 'all' ? null : brand, searchTerm);
}

// Filter by brand from home page
function filterByBrand(brand) {
    showPage('fichas');
    setTimeout(() => {
        filterCars(brand);
    }, 100);
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const activeFilter = document.querySelector('.filter-btn.active');
            const brand = activeFilter ? activeFilter.textContent.trim() : 'all';
            loadCars(brand === 'Todas' ? null : brand, this.value);
        });
    }
});

// Login handling
function handleLogin(event) {
    event.preventDefault();
    
    const username = event.target.username.value;
    const password = event.target.password.value;
    
    // Demo authentication
    if (username === 'prof@senai.com' && password === 'senha123') {
        currentUser = 'Professor SENAI';
        userType = 'professor';
        showPage('professor');
        showNotification('Login realizado com sucesso!', 'success');
    } else if (username === 'aluno@senai.com' && password === 'senha123') {
        currentUser = 'Aluno SENAI';
        userType = 'aluno';
        showPage('fichas');
        showNotification('Login realizado com sucesso!', 'success');
    } else {
        showNotification('Usuário ou senha inválidos!', 'error');
    }
}

// Logout
function logout() {
    currentUser = null;
    userType = null;
    showPage('home');
    showNotification('Logout realizado com sucesso!', 'success');
}

// Add new car (Professor)
function handleAddCar(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const newCar = {
        id: carsData.length + 1,
        model: formData.get('model'),
        brand: formData.get('brand'),
        year: parseInt(formData.get('year')),
        image: 'https://via.placeholder.com/400x250/254AA5/ffffff?text=' + encodeURIComponent(formData.get('brand') + '+' + formData.get('model')),
        specs: {
            motor: formData.get('engine'),
            potencia: formData.get('power'),
            torque: formData.get('torque'),
            consumo: formData.get('consumption'),
            cambio: formData.get('transmission'),
            tracao: 'Dianteira',
            portaMalas: '300 litros',
            portas: '4',
            peso: '1.200 kg'
        }
    };
    
    carsData.push(newCar);
    event.target.reset();
    loadProfessorCars();
    showNotification('Ficha técnica adicionada com sucesso!', 'success');
}

// Load professor's cars
function loadProfessorCars() {
    const tbody = document.getElementById('professorsCarsList');
    if (!tbody) return;
    
    tbody.innerHTML = carsData.map(car => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <img class="h-10 w-10 rounded-full object-cover mr-4" src="${car.image}" alt="${car.brand} ${car.model}">
                    <div class="text-sm font-medium text-gray-900">${car.model}</div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${car.brand}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${car.year}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${car.specs.motor}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                <button onclick="editCar(${car.id})" class="text-blue-600 hover:text-blue-900">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button onclick="deleteCar(${car.id})" class="text-red-600 hover:text-red-900">
                    <i class="fas fa-trash"></i> Excluir
                </button>
            </td>
        </tr>
    `).join('');
}

// Edit car
function editCar(carId) {
    showNotification('Funcionalidade de edição em desenvolvimento', 'info');
}

// Delete car
function deleteCar(carId) {
    if (confirm('Tem certeza que deseja excluir esta ficha técnica?')) {
        carsData = carsData.filter(car => car.id !== carId);
        loadProfessorCars();
        showNotification('Ficha técnica excluída com sucesso!', 'success');
    }
}

// Download PDF
function downloadPDF(carId) {
    showNotification('Gerando PDF... Download iniciado!', 'success');
    // Here you would call the PHP script to generate and download the PDF
}

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transition-all duration-300 transform translate-x-full`;
    
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        info: 'bg-blue-500 text-white',
        warning: 'bg-yellow-500 text-black'
    };
    
    notification.className += ` ${colors[type]}`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle mr-2"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-lg">&times;</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}