// script.js

// --- Page Navigation ---
function showPage(pageId) {
    document.querySelectorAll('.page').forEach(page => {
        page.classList.remove('active');
        page.classList.add('hidden'); // Use hidden for better control
    });
    const targetPage = document.getElementById(pageId + 'Page');
    if (targetPage) {
        targetPage.classList.add('active');
        targetPage.classList.remove('hidden');
    } else {
        console.error(`Page with id ${pageId}Page not found.`);
        // Show home page as fallback
        document.getElementById('homePage').classList.add('active');
        document.getElementById('homePage').classList.remove('hidden');
    }
    // Close mobile menu if open
    const mobileMenuContainer = document.getElementById('mobileMenuContainer');
    if (mobileMenuContainer && mobileMenuContainer.__x) {
        mobileMenuContainer.__x.data.mobileMenuOpen = false;
    }
}

function toggleMobileMenu() {
     const mobileMenuContainer = document.getElementById('mobileMenuContainer');
    if (mobileMenuContainer && mobileMenuContainer.__x) {
        mobileMenuContainer.__x.data.mobileMenuOpen = !mobileMenuContainer.__x.data.mobileMenuOpen;
    }
}

// --- Modal Handling ---
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex'); // Use flex for centering
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
     // Clear potential error messages
    const errorMessage = document.getElementById(`${modalId}ErrorMessage`); // Assuming convention
    if (errorMessage) {
        errorMessage.classList.add('hidden');
        errorMessage.textContent = '';
    }
}

// --- Initial Setup ---
document.addEventListener('DOMContentLoaded', () => {
    showPage('home'); // Show home page by default
    checkLoginStatus(); // Check login status from auth.js
    loadCategories(); // Load categories from localStorage
    populateStaticFilters(); // Populate filters with predefined/dynamic options
    loadCars(); // Load initial car list

    // Add event listener for category form
    const addCategoryForm = document.getElementById('addCategoryForm');
    if (addCategoryForm) {
        addCategoryForm.addEventListener('submit', handleAddCategory);
    }

    // Add event listener for search input (optional: trigger search on Enter)
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault(); // Prevent form submission if it's inside a form
                handleSearch();
            }
        });
    }
});

// --- Category Management ---
const CATEGORY_STORAGE_KEY = 'vehicleCategories';
let categories = [];

function loadCategories() {
    const storedCategories = localStorage.getItem(CATEGORY_STORAGE_KEY);
    // Define default categories if none are stored
    const defaultCategories = ['Hatch', 'Sedan', 'SUV', 'Picape', 'Esportivo', 'Utilitário'];
    categories = storedCategories ? JSON.parse(storedCategories) : [...defaultCategories];
    // Ensure default categories are saved if storage was empty
    if (!storedCategories) {
        saveCategories();
    }
    updateCategoryUI();
}

function saveCategories() {
    localStorage.setItem(CATEGORY_STORAGE_KEY, JSON.stringify(categories));
}

function updateCategoryUI() {
    const categoryFilter = document.getElementById('categoriaFilter');
    const categoryList = document.getElementById('categoryList');
    const categoryFormFields = document.getElementById('categoriaFormField'); // Assuming a select field in the ficha form

    // Populate Filter Dropdown
    if (categoryFilter) {
        categoryFilter.innerHTML = '<option value="">Todas</option>'; // Reset options
        categories.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat;
            option.textContent = cat;
            categoryFilter.appendChild(option);
        });
    }

    // Populate Management Modal List
    if (categoryList) {
        categoryList.innerHTML = ''; // Clear list
        if (categories.length === 0) {
            categoryList.innerHTML = '<p class="text-sm text-gray-500">Nenhuma categoria cadastrada.</p>';
        }
        categories.forEach(cat => {
            const div = document.createElement('div');
            div.className = 'flex justify-between items-center bg-gray-100 p-2 rounded';
            const span = document.createElement('span');
            span.textContent = cat;
            span.className = 'text-sm';
            const deleteButton = document.createElement('button');
            deleteButton.innerHTML = '<i class="fas fa-trash text-red-500 hover:text-red-700"></i>';
            deleteButton.className = 'px-2 py-1';
            deleteButton.onclick = () => handleDeleteCategory(cat);
            div.appendChild(span);
            div.appendChild(deleteButton);
            categoryList.appendChild(div);
        });
    }

    // Populate Category Select in Ficha Form (if it exists)
    if (categoryFormFields) {
         const categorySelect = categoryFormFields.querySelector('select'); // Find the select within the field container
         if(categorySelect){
            categorySelect.innerHTML = '<option value="">Selecione...</option>'; // Reset options
            categories.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat;
                option.textContent = cat;
                categorySelect.appendChild(option);
            });
         }
    }

     // Update Home Page Category Grid (Example - Adapt if needed)
    // This part assumes you want categories on the home page grid.
    // If the home grid is for brands, this needs adjustment or removal.
    const categoryGridHome = document.getElementById('categoryGridHome');
    if (categoryGridHome) {
        // Clear existing brand items IF replacing with categories
        // categoryGridHome.innerHTML = '';
        // categories.slice(0, 5).forEach(cat => { // Show first 5 categories
        //     const div = document.createElement('div');
        //     div.className = 'text-center p-4 bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow cursor-pointer';
        //     // Add onclick to filter by category if desired
        //     // div.onclick = () => filterByCategory(cat);
        //     // Add image placeholder or logic if categories have associated images
        //     div.innerHTML = `<img src="images/placeholder.png" alt="${cat}" class="h-16 mx-auto mb-2 object-contain"><h3 class="font-semibold text-sm md:text-base">${cat}</h3>`;
        //     categoryGridHome.appendChild(div);
        // });
    }
}

function handleAddCategory(event) {
    event.preventDefault();
    const input = document.getElementById('newCategoryName');
    const errorMessage = document.getElementById('categoryErrorMessage');
    const newCategory = input.value.trim();

    errorMessage.classList.add('hidden');

    if (!newCategory) {
        errorMessage.textContent = 'Por favor, insira um nome para a categoria.';
        errorMessage.classList.remove('hidden');
        return;
    }

    if (categories.some(cat => cat.toLowerCase() === newCategory.toLowerCase())) {
         errorMessage.textContent = 'Esta categoria já existe.';
         errorMessage.classList.remove('hidden');
        return;
    }

    categories.push(newCategory);
    categories.sort((a, b) => a.localeCompare(b)); // Keep sorted
    saveCategories();
    updateCategoryUI();
    input.value = ''; // Clear input
}

function handleDeleteCategory(categoryNameToDelete) {
    if (!confirm(`Tem certeza que deseja excluir a categoria "${categoryNameToDelete}"?`)) {
        return;
    }

    categories = categories.filter(cat => cat !== categoryNameToDelete);
    saveCategories();
    updateCategoryUI();
}

function openAddCategoryModal() {
    // Ensure the list is up-to-date when opening
    updateCategoryUI();
    openModal('categoryModal');
}

// --- Filter Population ---
function populateStaticFilters() {
    // Populate based on car_data.js or predefined lists
    const uniqueValues = (key) => [...new Set(carData.map(car => car[key]).filter(Boolean))].sort((a, b) => String(a).localeCompare(String(b)));

    const populateSelect = (selectId, values) => {
        const select = document.getElementById(selectId);
        if (select) {
            // Keep the first option (e.g., "Todos")
            const firstOption = select.options[0];
            select.innerHTML = '';
            if (firstOption) {
                select.appendChild(firstOption);
            }
            values.forEach(value => {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = value;
                select.appendChild(option);
            });
        }
    };

    populateSelect('marcaFilter', uniqueValues('marca'));
    populateSelect('anoFilter', uniqueValues('ano'));
    populateSelect('combustivelFilter', uniqueValues('combustivel'));
    populateSelect('transmissaoFilter', uniqueValues('transmissao'));
    populateSelect('carroceriaFilter', uniqueValues('carroceria'));
    // Potencia needs range mapping, handled in filtering logic
}


// --- Car Loading, Filtering, and Display ---
let currentCars = []; // Holds the currently displayed cars

function loadCars() {
    // In a real app, fetch from API or localStorage
    // Here, we use the global carData from car_data.js
    currentCars = [...carData]; // Start with all cars
    applyFilters(); // Apply any default/existing filters
}

function applyFilters() {
    const marca = document.getElementById('marcaFilter')?.value || '';
    const ano = document.getElementById('anoFilter')?.value || '';
    const potenciaRange = document.getElementById('potenciaFilter')?.value || '';
    const combustivel = document.getElementById('combustivelFilter')?.value || '';
    const transmissao = document.getElementById('transmissaoFilter')?.value || '';
    const carroceria = document.getElementById('carroceriaFilter')?.value || '';
    const categoria = document.getElementById('categoriaFilter')?.value || ''; // Added category filter
    const searchTerm = document.getElementById('searchInput')?.value.toLowerCase() || '';

    let filteredCars = carData.filter(car => {
        // Text Search (Marca, Modelo, Ano)
        const matchesSearch = searchTerm ? (
            car.marca.toLowerCase().includes(searchTerm) ||
            car.modelo.toLowerCase().includes(searchTerm) ||
            String(car.ano).includes(searchTerm)
        ) : true;

        // Filter Dropdowns
        const matchesMarca = marca ? car.marca === marca : true;
        const matchesAno = ano ? String(car.ano) === ano : true;
        const matchesCombustivel = combustivel ? car.combustivel === combustivel : true;
        const matchesTransmissao = transmissao ? car.transmissao === transmissao : true;
        const matchesCarroceria = carroceria ? car.carroceria === carroceria : true;
        const matchesCategoria = categoria ? car.categoria === categoria : true; // Filter by category

        // Potencia Range Filter
        let matchesPotencia = true;
        if (potenciaRange && car.potencia_cv) {
            const potencia = parseInt(car.potencia_cv);
            if (potenciaRange === '0-100') matchesPotencia = potencia <= 100;
            else if (potenciaRange === '101-150') matchesPotencia = potencia >= 101 && potencia <= 150;
            else if (potenciaRange === '151-200') matchesPotencia = potencia >= 151 && potencia <= 200;
            else if (potenciaRange === '201+') matchesPotencia = potencia >= 201;
        }

        return matchesSearch && matchesMarca && matchesAno && matchesCombustivel && matchesTransmissao && matchesCarroceria && matchesPotencia && matchesCategoria;
    });

    currentCars = filteredCars;
    displayCars(currentCars);
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('marcaFilter').value = '';
    document.getElementById('anoFilter').value = '';
    document.getElementById('potenciaFilter').value = '';
    document.getElementById('combustivelFilter').value = '';
    document.getElementById('transmissaoFilter').value = '';
    document.getElementById('carroceriaFilter').value = '';
    document.getElementById('categoriaFilter').value = ''; // Reset category filter
    loadCars(); // Reload all cars
}

function handleSearch() {
    applyFilters(); // Re-apply filters including the search term
}

function displayCars(carsToDisplay) {
    const carListDiv = document.getElementById('carList');
    const noResultsDiv = document.getElementById('noResults');
    carListDiv.innerHTML = ''; // Clear previous list

    if (carsToDisplay.length === 0) {
        noResultsDiv.classList.remove('hidden');
        carListDiv.classList.add('hidden');
    } else {
        noResultsDiv.classList.add('hidden');
        carListDiv.classList.remove('hidden');
        carsToDisplay.forEach(car => {
            const card = createCarCard(car);
            carListDiv.appendChild(card);
        });
    }
}

function createCarCard(car) {
    const div = document.createElement('div');
    div.className = 'bg-white rounded-xl shadow-lg overflow-hidden flex flex-col hover:shadow-xl transition-shadow duration-300';

    // Basic structure - enhance as needed
    div.innerHTML = `
        <img src="${car.imagem || 'images/placeholder.png'}" alt="${car.marca} ${car.modelo}" class="w-full h-40 object-cover">
        <div class="p-4 flex flex-col flex-grow">
            <h3 class="text-lg font-bold senai-blue mb-1">${car.marca} ${car.modelo}</h3>
            <p class="text-sm text-gray-600 mb-3">${car.versao || ''} - ${car.ano}</p>
            <div class="text-xs text-gray-500 mb-3 space-y-1">
                <span><i class="fas fa-gas-pump mr-1"></i> ${car.combustivel || 'N/D'}</span> | 
                <span><i class="fas fa-cogs mr-1"></i> ${car.transmissao || 'N/D'}</span> | 
                <span><i class="fas fa-car mr-1"></i> ${car.carroceria || 'N/D'}</span>
            </div>
            <div class="mt-auto flex flex-col sm:flex-row gap-2 pt-3 border-t border-gray-100">
                <button onclick="openFichaDetail('${car.id}')" class="flex-1 text-center px-3 py-2 bg-senai-blue text-white rounded-md hover:bg-blue-800 transition-colors text-xs font-semibold">
                    Ver Ficha
                </button>
                <button id="pdfBtnCard_${car.id}" onclick="generatePdfFromCard('${car.id}')" class="flex-1 text-center px-3 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors text-xs font-semibold">
                    <i class="fas fa-file-pdf mr-1"></i>PDF
                </button>
                 <!-- Professor Buttons Card -->
                 <button id="editBtnCard_${car.id}" onclick="openEditFichaModal('${car.id}')" class="hidden professor-btn-card px-3 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition-colors text-xs font-semibold">
                    <i class="fas fa-edit"></i>
                </button>
                <button id="deleteBtnCard_${car.id}" onclick="confirmDeleteFicha('${car.id}')" class="hidden professor-btn-card px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors text-xs font-semibold">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    return div;
}

// --- Ficha Técnica Detail Modal ---
function openFichaDetail(carId) {
    const car = carData.find(c => c.id === carId);
    if (!car) return;

    const contentDiv = document.getElementById('fichaDetailContent');
    contentDiv.innerHTML = generateFichaHtml(car); // Reuse HTML generation

    // Pass carId to modal buttons
    document.getElementById('pdfFichaBtnModal').onclick = () => generatePdfFromDetail(carId);
    document.getElementById('editFichaBtnModal').onclick = () => openEditFichaModalFromDetail(carId);
    document.getElementById('deleteFichaBtnModal').onclick = () => confirmDeleteFichaFromDetail(carId);

    // Show/hide professor buttons in modal based on login status
    updateProfessorButtonsModalVisibility();

    openModal('fichaDetailModal');
}

// --- Ficha Técnica CRUD (Placeholders - To be implemented fully later) ---

const FICHA_STORAGE_KEY = 'vehicleFichas';

// Function to load fichas from localStorage (or use initial carData)
function loadFichasFromStorage() {
    const storedFichas = localStorage.getItem(FICHA_STORAGE_KEY);
    if (storedFichas) {
        carData = JSON.parse(storedFichas);
    } else {
        // If nothing in storage, save the initial data
        saveFichasToStorage();
    }
    // Ensure IDs are consistent if loading from storage
    let maxId = carData.reduce((max, car) => Math.max(max, parseInt(car.id) || 0), 0);
    carData.forEach(car => {
        if (!car.id) {
            car.id = String(++maxId);
        }
    });
}

// Function to save fichas to localStorage
function saveFichasToStorage() {
    localStorage.setItem(FICHA_STORAGE_KEY, JSON.stringify(carData));
}

// Call this on initial load after defining carData
document.addEventListener('DOMContentLoaded', () => {
    loadFichasFromStorage();
    loadCars(); // Reload cars with potentially updated data
});


function openAddFichaModal() {
    document.getElementById('fichaFormTitle').textContent = 'Adicionar Nova Ficha Técnica';
    document.getElementById('fichaForm').reset();
    document.getElementById('editFichaId').value = ''; // Clear edit ID
    populateFichaForm({}); // Populate with empty data
    openModal('fichaFormModal');
}

function openEditFichaModal(carId) {
    const car = carData.find(c => c.id === carId);
    if (!car) return;

    document.getElementById('fichaFormTitle').textContent = 'Editar Ficha Técnica';
    document.getElementById('fichaForm').reset();
    document.getElementById('editFichaId').value = carId;
    populateFichaForm(car);
    openModal('fichaFormModal');
}

function openEditFichaModalFromDetail(carId) {
    closeModal('fichaDetailModal');
    openEditFichaModal(carId);
}

function confirmDeleteFicha(carId) {
    const car = carData.find(c => c.id === carId);
    if (!car) return;
    if (confirm(`Tem certeza que deseja excluir a ficha do ${car.marca} ${car.modelo} ${car.ano}?`)) {
        deleteFicha(carId);
    }
}

function confirmDeleteFichaFromDetail(carId) {
     const car = carData.find(c => c.id === carId);
    if (!car) return;
    if (confirm(`Tem certeza que deseja excluir a ficha do ${car.marca} ${car.modelo} ${car.ano}?`)) {
        closeModal('fichaDetailModal');
        deleteFicha(carId);
    }
}

function deleteFicha(carId) {
    carData = carData.filter(car => car.id !== carId);
    saveFichasToStorage();
    loadCars(); // Refresh the list
    console.log(`Ficha ${carId} excluída.`);
    // Add user feedback (e.g., a toast message)
}

// Handle Ficha Form Submission (Add/Edit)
document.getElementById('fichaForm')?.addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const fichaData = Object.fromEntries(formData.entries());
    const editId = document.getElementById('editFichaId').value;

    // Basic validation example (add more as needed)
    if (!fichaData.marca || !fichaData.modelo || !fichaData.ano) {
        const errorDiv = document.getElementById('fichaFormErrorMessage');
        errorDiv.textContent = 'Marca, Modelo e Ano são obrigatórios.';
        errorDiv.classList.remove('hidden');
        return;
    }

    if (editId) {
        // Editing existing ficha
        const index = carData.findIndex(car => car.id === editId);
        if (index !== -1) {
            carData[index] = { ...carData[index], ...fichaData }; // Merge data
            console.log(`Ficha ${editId} atualizada.`);
        }
    } else {
        // Adding new ficha
        // Generate a simple unique ID (replace with a better method if needed)
        const newId = String(Date.now());
        fichaData.id = newId;
        carData.push(fichaData);
        console.log(`Nova ficha ${newId} adicionada.`);
    }

    saveFichasToStorage();
    loadCars(); // Refresh the list
    closeModal('fichaFormModal');
});


// --- Helper Functions ---

// Generates HTML for Ficha Detail View and PDF
function generateFichaHtml(car) {
    // Define the sections and fields as per requirements
    const sections = {
        'Informações Básicas': ['marca', 'modelo', 'ano', 'versao', 'codigo_motor', 'combustivel'],
        'Motorização': ['tipo_motor', 'cilindrada_cm3', 'potencia_cv', 'torque_kgfm', 'valvulas', 'injecao_eletronica'],
        'Transmissão': ['cambio', 'marchas'],
        'Suspensão e Freios': ['suspensao_dianteira', 'suspensao_traseira', 'freios', 'abs_ebd'],
        'Direção e Pneus': ['direcao', 'pneus_originais'],
        'Dimensões': ['comprimento_mm', 'largura_mm', 'altura_mm', 'entre_eixos_mm', 'altura_solo_mm', 'peso_kg'],
        'Desempenho e Consumo': ['velocidade_max_kmh', 'aceleracao_0_100_s', 'consumo_urbano_kmL', 'consumo_rodoviario_kmL', 'tanque_l'],
        'Capacidades': ['porta_malas_l', 'carga_util_kg', 'ocupantes'],
        'Sistemas e Eletrônica (Opcional)': ['sistema_injecao', 'sonda_lambda', 'sensor_fase_rotacao', 'sistema_ignicao', 'ecu']
    };

    // Helper to format field names (e.g., 'potencia_cv' -> 'Potência (cv)')
    const formatFieldName = (key) => {
        return key.replace(/_/g, ' ').replace(//g, c => c.toUpperCase())
                  .replace('cm3', '(cm³)')
                  .replace('cv', '(cv)')
                  .replace('kgfm', '(kgfm)')
                  .replace('mm', '(mm)')
                  .replace('kg', '(kg)')
                  .replace('kmh', '(km/h)')
                  .replace('0 100 s', '0-100 km/h (s)')
                  .replace('kmL', '(km/L)')
                  .replace('l', '(L)')
                  .replace('abs ebd', 'ABS/EBD')
                  .replace('ecu', 'ECU')
                  .split(' ')
                  .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                  .join(' ');
    };

    let html = `<h2 class="text-2xl font-bold senai-blue mb-4 text-center">${car.marca} ${car.modelo} ${car.ano}</h2>`;
    html += `<img src="${car.imagem || 'images/placeholder.png'}" alt="${car.marca} ${car.modelo}" class="w-full h-48 object-contain rounded-lg mb-6 mx-auto block" style="max-width: 300px;">`; // Contained image

    for (const [sectionTitle, fields] of Object.entries(sections)) {
        // Check if at least one field in the section has data
        const sectionHasData = fields.some(key => car[key] !== undefined && car[key] !== null && car[key] !== '');

        if (sectionHasData) {
            html += `<div class="mb-5">
                        <h4 class="text-lg font-semibold border-b pb-1 mb-3 text-gray-700">${sectionTitle}</h4>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                    `;
            fields.forEach(key => {
                const value = car[key];
                if (value !== undefined && value !== null && value !== '') {
                    html += `<div class="flex justify-between py-1 border-b border-dashed border-gray-200">
                                <dt class="font-medium text-gray-600">${formatFieldName(key)}:</dt>
                                <dd class="text-gray-800 text-right">${value}</dd>
                             </div>`;
                }
            });
            html += `</dl></div>`;
        }
    }

    return html;
}

// Populate Ficha Form with data (for editing) or placeholders
function populateFichaForm(carData) {
    const formFieldsDiv = document.getElementById('fichaFormFields');
    formFieldsDiv.innerHTML = ''; // Clear previous fields

    const sections = {
        'Informações Básicas': ['marca', 'modelo', 'ano', 'versao', 'codigo_motor', 'combustivel', 'categoria'], // Added categoria
        'Motorização': ['tipo_motor', 'cilindrada_cm3', 'potencia_cv', 'torque_kgfm', 'valvulas', 'injecao_eletronica'],
        'Transmissão': ['cambio', 'marchas'],
        'Suspensão e Freios': ['suspensao_dianteira', 'suspensao_traseira', 'freios', 'abs_ebd'],
        'Direção e Pneus': ['direcao', 'pneus_originais'],
        'Dimensões': ['comprimento_mm', 'largura_mm', 'altura_mm', 'entre_eixos_mm', 'altura_solo_mm', 'peso_kg'],
        'Desempenho e Consumo': ['velocidade_max_kmh', 'aceleracao_0_100_s', 'consumo_urbano_kmL', 'consumo_rodoviario_kmL', 'tanque_l'],
        'Capacidades': ['porta_malas_l', 'carga_util_kg', 'ocupantes'],
        'Sistemas e Eletrônica (Opcional)': ['sistema_injecao', 'sonda_lambda', 'sensor_fase_rotacao', 'sistema_ignicao', 'ecu'],
        'Outros': ['imagem'] // Image URL field
    };

     const formatFieldName = (key) => {
        // Simplified formatting for labels
        return key.replace(/_/g, ' ').replace(//g, c => c.toUpperCase())
                  .split(' ')
                  .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                  .join(' ');
    };

    for (const [sectionTitle, fields] of Object.entries(sections)) {
         fields.forEach(key => {
            const value = carData[key] || '';
            const label = formatFieldName(key);
            const inputType = (key === 'ano' || key.includes('mm') || key.includes('kg') || key.includes('_l') || key.includes('_cv') || key.includes('kgfm') || key.includes('_s') || key.includes('cm3') || key === 'marchas' || key === 'valvulas' || key === 'ocupantes') ? 'number' : 'text';

            const fieldDiv = document.createElement('div');
            fieldDiv.className = 'mb-3';
            fieldDiv.id = `${key}FormField`; // Add ID for category update

            let inputHtml;

            // Special case for Categoria - use a select dropdown
            if (key === 'categoria') {
                inputHtml = `<label for="${key}" class="block text-xs font-medium text-gray-700 mb-1">${label}</label>
                             <select id="${key}" name="${key}" class="form-input">
                                 <option value="">Selecione...</option>
                                 ${categories.map(cat => `<option value="${cat}" ${value === cat ? 'selected' : ''}>${cat}</option>`).join('')}
                             </select>`;
            } else if (key === 'imagem') {
                 inputHtml = `<label for="${key}" class="block text-xs font-medium text-gray-700 mb-1">URL da Imagem</label>
                             <input type="url" id="${key}" name="${key}" value="${value}" class="form-input" placeholder="https://...">`;
            } else {
                 inputHtml = `<label for="${key}" class="block text-xs font-medium text-gray-700 mb-1">${label}</label>
                             <input type="${inputType}" id="${key}" name="${key}" value="${value}" class="form-input" ${inputType === 'number' ? 'step="any"' : ''}>`;
            }

            fieldDiv.innerHTML = inputHtml;
            formFieldsDiv.appendChild(fieldDiv);
        });
    }
}

// --- PDF Generation (Placeholder - Requires pdfGenerator.js) ---
function generatePdfFromCard(carId) {
    console.log(`Gerando PDF para card ${carId}...`);
    // Call function from pdfGenerator.js
    if (typeof generatePdfForFicha === 'function') {
        generatePdfForFicha(carId);
    } else {
        alert('Funcionalidade de PDF não está pronta.');
    }
}

function generatePdfFromDetail(carId) {
    console.log(`Gerando PDF para detalhe ${carId}...`);
    // Call function from pdfGenerator.js
     if (typeof generatePdfForFicha === 'function') {
        generatePdfForFicha(carId);
    } else {
        alert('Funcionalidade de PDF não está pronta.');
    }
}

// --- Auth Related UI Updates (Called from auth.js or here) ---
function showProfessorUI() {
    document.querySelectorAll('.professor-btn-card').forEach(btn => btn.classList.remove('hidden'));
    document.getElementById('addFichaBtn')?.classList.remove('hidden');
    document.getElementById('professorSection')?.classList.remove('hidden');
    updateProfessorButtonsModalVisibility(); // Update modal buttons too
}

function hideProfessorUI() {
    document.querySelectorAll('.professor-btn-card').forEach(btn => btn.classList.add('hidden'));
    document.getElementById('addFichaBtn')?.classList.add('hidden');
    document.getElementById('professorSection')?.classList.add('hidden');
    updateProfessorButtonsModalVisibility(); // Update modal buttons too
}

function updateProfessorButtonsModalVisibility() {
    const isLoggedIn = localStorage.getItem("professorLoggedIn") === "true";
    const editBtn = document.getElementById('editFichaBtnModal');
    const deleteBtn = document.getElementById('deleteFichaBtnModal');

    if (isLoggedIn) {
        editBtn?.classList.remove('hidden');
        deleteBtn?.classList.remove('hidden');
    } else {
        editBtn?.classList.add('hidden');
        deleteBtn?.classList.add('hidden');
    }
}

// Modify auth.js functions to call these
/*
In auth.js:

function showProfessorSection() {
    showProfessorUI(); // Call the function in script.js
}

function hideProfessorSection() {
    hideProfessorUI(); // Call the function in script.js
}
*/

