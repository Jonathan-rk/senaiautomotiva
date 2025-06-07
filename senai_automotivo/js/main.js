// SENAI Automotivo - Sistema de Fichas Técnicas
// Enhanced functionality with professor area and category management

// Global Variables
let carsData = [
    {
        id: 1,
        model: 'Onix',
        brand: 'Chevrolet',
        year: 2023,
        category: 'Hatch',
        image: 'https://via.placeholder.com/400x250/254AA5/ffffff?text=Chevrolet+Onix',
        specs: {
            motor: '1.0 Turbo',
            potencia: '116 cv',
            torque: '16,8 kgfm',
            consumo: '13,7 km/l (cidade) / 17,1 km/l (estrada)',
            cambio: 'Manual de 6 marchas',
            combustivel: 'Flex',
            tracao: 'Dianteira',
            portaMalas: '275 litros',
            portas: '4',
            peso: '1.050 kg',
            carroceria: 'Hatch'
        }
    },
    {
        id: 2,
        model: 'Golf GTI',
        brand: 'Volkswagen',
        year: 2023,
        category: 'Hatch',
        image: 'https://via.placeholder.com/400x250/254AA5/ffffff?text=VW+Golf+GTI',
        specs: {
            motor: '2.0 TSI',
            potencia: '230 cv',
            torque: '35,7 kgfm',
            consumo: '9,8 km/l (cidade) / 12,3 km/l (estrada)',
            cambio: 'Automático DSG de 7 marchas',
            combustivel: 'Gasolina',
            tracao: 'Dianteira',
            portaMalas: '380 litros',
            portas: '5',
            peso: '1.420 kg',
            carroceria: 'Hatch'
        }
    },
    {
        id: 3,
        model: 'Civic',
        brand: 'Honda',
        year: 2023,
        category: 'Sedan',
        image: 'https://via.placeholder.com/400x250/254AA5/ffffff?text=Honda+Civic',
        specs: {
            motor: '2.0 i-VTEC',
            potencia: '155 cv',
            torque: '19,4 kgfm',
            consumo: '12,1 km/l (cidade) / 16,8 km/l (estrada)',
            cambio: 'CVT',
            combustivel: 'Flex',
            tracao: 'Dianteira',
            portaMalas: '519 litros',
            portas: '4',
            peso: '1.350 kg',
            carroceria: 'Sedan'
        }
    },
    {
        id: 4,
        model: 'Corolla',
        brand: 'Toyota',
        year: 2023,
        category: 'Sedan',
        image: 'https://via.placeholder.com/400x250/254AA5/ffffff?text=Toyota+Corolla',
        specs: {
            motor: '2.0 Flex',
            potencia: '177 cv',
            torque: '21,4 kgfm',
            consumo: '11,2 km/l (cidade) / 15,1 km/l (estrada)',
            cambio: 'CVT',
            combustivel: 'Flex',
            tracao: 'Dianteira',
            portaMalas: '470 litros',
            portas: '4',
            peso: '1.395 kg',
            carroceria: 'Sedan'
        }
    }
];

// Session Management
let currentUser = null;
let userType = null;
let isLoggedIn = false;

// Category Management - integrated with CategoryManager

// Initialize Application
document.addEventListener('DOMContentLoaded', function() {
    console.log('SENAI Automotivo system initializing...');
    initializeApp();
});

function initializeApp() {
    console.log('Loading application components...');
    
    // Load stored session
    loadStoredSession();
    
    // Initialize category management
    if (typeof categoryManager !== 'undefined') {
        categoryManager.initialize();
    }
    
    // Load cars
    loadCars();
    
    // Load professor cars if logged in
    if (userType === 'professor') {
        loadProfessorCars();
        updateNavigation();
        updateCarCount();
    }
    
    // Setup search functionality
    setupSearchInput();
    
    console.log('Application initialized successfully');
}

// Session Management Functions
function loadStoredSession() {
    console.log('Checking for stored session...');
    const storedUser = localStorage.getItem('senai_currentUser');
    const storedUserType = localStorage.getItem('senai_userType');
    
    if (storedUser && storedUserType) {
        currentUser = storedUser;
        userType = storedUserType;
        isLoggedIn = true;
        console.log('Session restored:', currentUser, userType);
        updateNavigation();
    }
}

function saveSession() {
    console.log('Saving session to localStorage...');
    localStorage.setItem('senai_currentUser', currentUser);
    localStorage.setItem('senai_userType', userType);
}

function clearSession() {
    console.log('Clearing session...');
    localStorage.removeItem('senai_currentUser');
    localStorage.removeItem('senai_userType');
    currentUser = null;
    userType = null;
    isLoggedIn = false;
}

// Navigation Functions
function showPage(pageId) {
    console.log('Navigating to page:', pageId);
    
    // Scroll to top when changing pages
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    // Hide all pages
    document.querySelectorAll('.page').forEach(page => {
        page.classList.remove('active');
    });
    
    // Show selected page
    const targetPage = document.getElementById(pageId + 'Page');
    if (targetPage) {
        targetPage.classList.add('active');
        console.log('Page changed to:', pageId);
    }
    
    // Special handling for specific pages
    if (pageId === 'fichas') {
        loadCars();
    } else if (pageId === 'professor' && userType === 'professor') {
        loadProfessorCars();
        if (typeof categoryManager !== 'undefined') {
            categoryManager.updateAllCategorySelects();
            categoryManager.updateCategoryDisplay();
        }
    }
}

function updateNavigation() {
    console.log('Updating navigation for user type:', userType);
    
    const settingsDropdown = document.querySelector('.settings-dropdown');
    if (!settingsDropdown) return;
    
    if (isLoggedIn) {
        // Update login link to logout
        const loginLink = settingsDropdown.querySelector('a[onclick*="login"]');
        if (loginLink && userType === 'professor') {
            loginLink.innerHTML = '<i class="fas fa-sign-out-alt mr-2"></i>Sair';
            loginLink.setAttribute('onclick', 'logout()');
        }
    }
}

function toggleMobileMenu() {
    console.log('Toggling mobile menu...');
    const menu = document.getElementById('mobileMenu');
    if (menu) {
        menu.classList.toggle('hidden');
    }
}

// Authentication Functions
function handleLogin(event) {
    event.preventDefault();
    console.log('Processing login attempt...');
    
    const username = event.target.username.value;
    const password = event.target.password.value;
    
    console.log('Login attempt for user:', username);
    
    // Demo authentication
    if (username === 'prof@senai.com' && password === 'senha123') {
        currentUser = 'Professor SENAI';
        userType = 'professor';
        isLoggedIn = true;
        saveSession();
        updateNavigation();
        showPage('professor');
        showNotification('Login realizado com sucesso! Bem-vindo, Professor.', 'success');
        console.log('Professor login successful');
    } else if (username === 'aluno@senai.com' && password === 'senha123') {
        currentUser = 'Aluno SENAI';
        userType = 'aluno';
        isLoggedIn = true;
        saveSession();
        updateNavigation();
        showPage('fichas');
        showNotification('Login realizado com sucesso! Bem-vindo, Aluno.', 'success');
        console.log('Student login successful');
    } else {
        showNotification('Usuário ou senha inválidos!', 'error');
        console.log('Login failed: invalid credentials');
    }
}

function logout() {
    console.log('User logging out...');
    
    const userDisplayName = currentUser || 'Usuário';
    clearSession();
    updateNavigation();
    showPage('home');
    showNotification(`Logout realizado com sucesso! Até logo, ${userDisplayName}.`, 'success');
    
    console.log('Logout completed');
}

// Car Management Functions
function loadCars(filter = null, search = '') {
    console.log('Loading cars with filter:', filter, 'search:', search);
    
    const grid = document.getElementById('carsGrid');
    const loading = document.getElementById('loading');
    
    if (!grid) {
        console.error('Cars grid element not found');
        return;
    }
    
    if (loading) {
        loading.classList.add('show');
    }
    
    setTimeout(() => {
        let filteredCars = [...carsData];
        
        // Apply basic filters
        if (filter && filter !== 'all') {
            filteredCars = filteredCars.filter(car => car.brand === filter);
            console.log('Filtered by brand:', filter, 'Results:', filteredCars.length);
        }
        
        if (search) {
            const searchLower = search.toLowerCase();
            filteredCars = filteredCars.filter(car => 
                car.model.toLowerCase().includes(searchLower) ||
                car.brand.toLowerCase().includes(searchLower) ||
                car.year.toString().includes(search)
            );
            console.log('Filtered by search:', search, 'Results:', filteredCars.length);
        }
        
        // Apply advanced filters
        filteredCars = applyAdvancedFilters(filteredCars);
        
        // Render results
        if (filteredCars.length === 0) {
            grid.innerHTML = `
                <div class="col-span-full text-center py-8">
                    <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600 mb-4">Nenhum carro encontrado com os filtros selecionados.</p>
                    <button onclick="resetFilters()" class="btn btn-primary">
                        <i class="fas fa-refresh mr-2"></i>Limpar Filtros
                    </button>
                </div>
            `;
        } else {
            grid.innerHTML = filteredCars.map(car => createCarCard(car)).join('');
        }
        
        if (loading) {
            loading.classList.remove('show');
        }
        
        console.log('Cars loaded successfully. Count:', filteredCars.length);
    }, 500);
}

function applyAdvancedFilters(cars) {
    console.log('Applying advanced filters...');
    
    const potenciaFilter = document.getElementById('potenciaFilter')?.value;
    const combustivelFilter = document.getElementById('combustivelFilter')?.value;
    const transmissaoFilter = document.getElementById('transmissaoFilter')?.value;
    const carroceriaFilter = document.getElementById('carroceriaFilter')?.value;
    
    let filteredCars = [...cars];
    
    if (potenciaFilter) {
        const [min, max] = potenciaFilter.split('-').map(Number);
        filteredCars = filteredCars.filter(car => {
            const power = parseInt(car.specs.potencia.match(/\\d+/)?.[0] || 0);
            return power >= min && power <= max;
        });
        console.log('Power filter applied:', potenciaFilter, 'Results:', filteredCars.length);
    }
    
    if (combustivelFilter && combustivelFilter !== 'todos') {
        filteredCars = filteredCars.filter(car => 
            car.specs.combustivel === combustivelFilter
        );
        console.log('Fuel filter applied:', combustivelFilter, 'Results:', filteredCars.length);
    }
    
    if (transmissaoFilter && transmissaoFilter !== 'todos') {
        filteredCars = filteredCars.filter(car => 
            car.specs.cambio.toLowerCase().includes(transmissaoFilter.toLowerCase())
        );
        console.log('Transmission filter applied:', transmissaoFilter, 'Results:', filteredCars.length);
    }
    
    if (carroceriaFilter && carroceriaFilter !== 'todos') {
        filteredCars = filteredCars.filter(car => 
            car.specs.carroceria === carroceriaFilter
        );
        console.log('Body filter applied:', carroceriaFilter, 'Results:', filteredCars.length);
    }
    
    return filteredCars;
}

function createCarCard(car) {
    return `
        <div class="car-card bg-white rounded-xl shadow-lg overflow-hidden fade-in">
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
                    <button onclick="showCarDetail(${car.id})" class="flex-1 btn btn-primary text-sm">
                        <i class="fas fa-eye mr-2"></i>Ver Detalhes
                    </button>
                    <button onclick="downloadPDF(${car.id})" class="btn btn-secondary text-sm">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
}

function showCarDetail(carId) {
    console.log('Showing car detail for ID:', carId);
    
    // Scroll to top immediately when showing details
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    const car = carsData.find(c => c.id === carId);
    if (!car) {
        console.error('Car not found with ID:', carId);
        showNotification('Carro não encontrado!', 'error');
        return;
    }
    
    const content = document.getElementById('carDetailContent');
    if (!content) {
        console.error('Car detail content element not found');
        return;
    }
    
    content.innerHTML = `
        <div class="relative">
            <img src="${car.image}" alt="${car.brand} ${car.model}" class="w-full h-64 md:h-80 object-cover">
            <div class="absolute top-4 right-4">
                <button onclick="downloadPDF(${car.id})" class="btn btn-secondary shadow-lg">
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
                <div class="detail-section">
                    <h2 class="text-2xl font-bold mb-6 senai-blue">
                        <i class="fas fa-cog mr-2"></i>Especificações Técnicas
                    </h2>
                    <div class="space-y-4">
                        ${Object.entries(car.specs).map(([key, value]) => `
                            <div class="spec-item">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700 capitalize">${formatSpecKey(key)}:</span>
                                    <span class="text-gray-900 font-semibold">${value}</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
                
                <div class="detail-section">
                    <h2 class="text-2xl font-bold mb-6 senai-blue">
                        <i class="fas fa-info-circle mr-2"></i>Informações Adicionais
                    </h2>
                    <div class="space-y-4">
                        <div class="bg-white p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-800 mb-2">Categoria</h3>
                            <p class="text-gray-600">${car.category || 'Veículo de passeio'}</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-800 mb-2">Combustível</h3>
                            <p class="text-gray-600">${car.specs.combustivel || 'Flex (Etanol/Gasolina)'}</p>
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
    console.log('Car detail displayed successfully');
}

function formatSpecKey(key) {
    const translations = {
        'motor': 'Motor',
        'potencia': 'Potência',
        'torque': 'Torque',
        'consumo': 'Consumo',
        'cambio': 'Câmbio',
        'combustivel': 'Combustível',
        'tracao': 'Tração',
        'portaMalas': 'Porta-malas',
        'portas': 'Portas',
        'peso': 'Peso',
        'carroceria': 'Carroceria'
    };
    
    return translations[key] || key.charAt(0).toUpperCase() + key.slice(1);
}

// Filter Functions
function filterCars(brand) {
    console.log('Filtering cars by brand:', brand);
    
    // Update active filter button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    if (event && event.target) {
        event.target.classList.add('active');
    }
    
    // Load filtered cars
    const searchTerm = document.getElementById('searchInput')?.value || '';
    loadCars(brand === 'all' ? null : brand, searchTerm);
}

function filterByBrand(brand) {
    console.log('Filtering by brand from home page:', brand);
    showPage('fichas');
    setTimeout(() => {
        filterCars(brand);
    }, 100);
}

function resetFilters() {
    console.log('Resetting all filters...');
    
    // Reset advanced filter selects
    const potenciaFilter = document.getElementById('potenciaFilter');
    const combustivelFilter = document.getElementById('combustivelFilter');
    const transmissaoFilter = document.getElementById('transmissaoFilter');
    const carroceriaFilter = document.getElementById('carroceriaFilter');
    
    if (potenciaFilter) potenciaFilter.value = '';
    if (combustivelFilter) combustivelFilter.value = 'todos';
    if (transmissaoFilter) transmissaoFilter.value = 'todos';
    if (carroceriaFilter) carroceriaFilter.value = 'todos';
    
    // Reset search input
    const searchInput = document.getElementById('searchInput');
    if (searchInput) searchInput.value = '';
    
    // Reset brand filters
    filterCars('all');
    
    showNotification('Filtros limpos com sucesso!', 'info');
}

// Search Functions
function setupSearchInput() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            console.log('Search input changed:', this.value);
            const activeFilter = document.querySelector('.filter-btn.active');
            const brand = activeFilter ? activeFilter.textContent.trim() : 'all';
            loadCars(brand === 'Todas' ? null : brand, this.value);
        });
    }
}

// Professor Functions
function handleAddCar(event) {
    event.preventDefault();
    console.log('Adding new car...');
    
    const formData = new FormData(event.target);
    const newCar = {
        id: carsData.length + 1,
        model: formData.get('model'),
        brand: formData.get('brand'),
        year: parseInt(formData.get('year')),
        category: formData.get('category') || 'Outros',
        image: 'https://via.placeholder.com/400x250/254AA5/ffffff?text=' + 
               encodeURIComponent(formData.get('brand') + '+' + formData.get('model')),
        specs: {
            motor: formData.get('engine'),
            potencia: formData.get('power'),
            torque: formData.get('torque'),
            consumo: formData.get('consumption'),
            cambio: formData.get('transmission'),
            combustivel: formData.get('fuel') || 'Flex',
            tracao: 'Dianteira',
            portaMalas: '300 litros',
            portas: '4',
            peso: '1.200 kg',
            carroceria: formData.get('body') || 'Sedan'
        }
    };
    
    carsData.push(newCar);
    event.target.reset();
    loadProfessorCars();
    updateCarCount();
    
    console.log('New car added:', newCar);
    showNotification('Ficha técnica adicionada com sucesso!', 'success');
}

function loadProfessorCars() {
    console.log('Loading professor cars list...');
    
    const tbody = document.getElementById('professorsCarsList');
    if (!tbody) {
        console.warn('Professor cars list element not found');
        return;
    }
    
    tbody.innerHTML = carsData.map(car => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="car-info">
                    <img class="car-thumbnail" src="${car.image}" alt="${car.brand} ${car.model}">
                    <div class="car-name">${car.model}</div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${car.brand}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${car.year}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${car.specs.motor}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="action-buttons">
                    <button onclick="editCar(${car.id})" class="action-btn edit-btn" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteCar(${car.id})" class="action-btn delete-btn" title="Excluir">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
    
    console.log('Professor cars list updated');
}

function editCar(carId) {
    console.log('Edit car function called for ID:', carId);
    showNotification('Funcionalidade de edição em desenvolvimento', 'info');
}

function deleteCar(carId) {
    console.log('Delete car function called for ID:', carId);
    
    if (confirm('Tem certeza que deseja excluir esta ficha técnica?')) {
        const carIndex = carsData.findIndex(car => car.id === carId);
        if (carIndex !== -1) {
            const deletedCar = carsData[carIndex];
            carsData.splice(carIndex, 1);
            loadProfessorCars();
            updateCarCount();
            console.log('Car deleted:', deletedCar);
            showNotification('Ficha técnica excluída com sucesso!', 'success');
        }
    }
}

// Category Management - Global functions for UI integration
function addCustomCategory() {
    if (typeof categoryManager !== 'undefined') {
        categoryManager.handleAddCategory();
    }
}

function removeCustomCategory(categoryName) {
    if (typeof categoryManager !== 'undefined') {
        categoryManager.handleRemoveCategory(categoryName);
    }
}

// Update car count in dashboard
function updateCarCount() {
    const countElement = document.getElementById('totalCarsCount');
    if (countElement) {
        countElement.textContent = carsData.length;
        console.log('Car count updated to:', carsData.length);
    }
}

// Utility Functions
function downloadPDF(carId) {
    console.log('Download PDF requested for car ID:', carId);
    
    const car = carsData.find(c => c.id === carId);
    if (car) {
        showNotification(`Gerando PDF para ${car.brand} ${car.model}... Download iniciado!`, 'success');
        console.log('PDF generation simulated for:', car.brand, car.model);
    }
}

// Notification System
function showNotification(message, type = 'info', duration = 5000) {
    console.log('Showing notification:', type, message);
    
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    
    const icons = {
        success: 'check-circle',
        error: 'times-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    
    notification.innerHTML = `
        <div class="notification-content">
            <div class="notification-icon">
                <i class="fas fa-${icons[type]}"></i>
            </div>
            <div class="notification-message">${message}</div>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                &times;
            </button>
        </div>
        <div class="notification-progress">
            <div class="notification-progress-bar" style="width: 100%; transition: width ${duration}ms linear;"></div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Start progress bar
    setTimeout(() => {
        const progressBar = notification.querySelector('.notification-progress-bar');
        if (progressBar) {
            progressBar.style.width = '0%';
        }
    }, 200);
    
    // Auto remove
    setTimeout(() => {
        notification.classList.remove('show');
        notification.classList.add('hide');
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 300);
    }, duration);
}

// Global Event Listeners
document.addEventListener('keydown', function(event) {
    // ESC key to close modals or go back
    if (event.key === 'Escape') {
        const activePage = document.querySelector('.page.active');
        if (activePage && activePage.id === 'detailPage') {
            showPage('fichas');
        }
    }
    
    // Ctrl+F to focus search
    if (event.ctrlKey && event.key === 'f') {
        event.preventDefault();
        const searchInput = document.getElementById('searchInput');
        if (searchInput && document.querySelector('.page.active')?.id === 'fichasPage') {
            searchInput.focus();
        }
    }
});

// Handle form submissions
document.addEventListener('submit', function(event) {
    const form = event.target;
    
    if (form.id === 'addCarForm') {
        handleAddCar(event);
    } else if (form.onsubmit && form.onsubmit.toString().includes('handleLogin')) {
        handleLogin(event);
    }
});

// Console welcome message
console.log('%c🚗 SENAI Automotivo System Loaded! 🚗', 'color: #0A3871; font-size: 16px; font-weight: bold;');
console.log('Sistema de Fichas Técnicas Automotivas - Versão 2.0');
console.log('Developed for SENAI - Serviço Nacional de Aprendizagem Industrial');