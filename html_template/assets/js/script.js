// Arquivo JavaScript para funcionalidades do sistema
document.addEventListener('DOMContentLoaded', function() {
    // Funções para navegação entre páginas
    window.showPage = function(pageId) {
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
    window.toggleMobileMenu = function() {
        const mobileMenu = document.getElementById('mobileMenu');
        if (mobileMenu.classList.contains('hidden')) {
            mobileMenu.classList.remove('hidden');
        } else {
            mobileMenu.classList.add('hidden');
        }
    }
    
    // Função para filtrar carros por marca
    window.filterCars = function(marca) {
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
    window.filterByBrand = function(marca) {
        showPage('fichas');
        filterCars(marca);
    }
    
    // Função para resetar filtros
    window.resetFilters = function() {
        document.getElementById('searchInput').value = '';
        if (document.getElementById('potenciaFilter')) {
            document.getElementById('potenciaFilter').value = '';
        }
        if (document.getElementById('combustivelFilter')) {
            document.getElementById('combustivelFilter').value = 'todos';
        }
        if (document.getElementById('transmissaoFilter')) {
            document.getElementById('transmissaoFilter').value = 'todos';
        }
        if (document.getElementById('carroceriaFilter')) {
            document.getElementById('carroceriaFilter').value = 'todos';
        }
        
        filterCars('all');
    }
    
    // Função para mostrar detalhes do carro
    window.showCarDetails = function(id) {
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
    
    // Configurar eventos para o modal
    if (document.getElementById('closeModal')) {
        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('detailsModal').classList.add('hidden');
        });
    }
    
    if (document.getElementById('detailsModal')) {
        document.getElementById('detailsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
    }
    
    // Configurar busca
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const term = this.value.toLowerCase();
            const cards = document.querySelectorAll('.car-card');
            
            cards.forEach(card => {
                const marca = card.dataset.marca.toLowerCase();
                const modelo = card.querySelector('h3').textContent.toLowerCase();
                
                if (marca.includes(term) || modelo.includes(term)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
});
