// Modelo de dados atualizado para fichas técnicas
// Este arquivo substitui o car_data.js original, mantendo compatibilidade

// Array de veículos com todos os campos conforme requisitos
let carData = [
    {
        id: "1",
        // Informações Básicas
        marca: "Chevrolet",
        modelo: "Onix",
        ano: 2023,
        versao: "LTZ Turbo",
        codigo_motor: "CSS Prime",
        combustivel: "Flex",
        // Motorização
        tipo_motor: "Dianteiro, transversal, 3 cil. em linha",
        cilindrada_cm3: 999,
        potencia_cv: "116 (E) / 116 (G)",
        torque_kgfm: "16.8 (E) / 16.3 (G)",
        valvulas: 12,
        injecao_eletronica: "Multiponto",
        // Transmissão
        cambio: "Manual",
        marchas: 6,
        // Suspensão e Freios
        suspensao_dianteira: "Independente, McPherson",
        suspensao_traseira: "Eixo de torção",
        freios: "Disco ventilado (diant.) / Tambor (tras.)",
        abs_ebd: "Sim",
        // Direção e Pneus
        direcao: "Elétrica progressiva",
        pneus_originais: "195/55 R16",
        // Dimensões
        comprimento_mm: 4163,
        largura_mm: 1730,
        altura_mm: 1476,
        entre_eixos_mm: 2551,
        altura_solo_mm: 128,
        peso_kg: 1085,
        // Desempenho e Consumo
        velocidade_max_kmh: 187,
        aceleracao_0_100_s: 10.1,
        consumo_urbano_kmL: "8.3 (E) / 11.9 (G)",
        consumo_rodoviario_kmL: "10.7 (E) / 15.1 (G)",
        tanque_l: 44,
        // Capacidades
        porta_malas_l: 275,
        carga_util_kg: 375,
        ocupantes: 5,
        // Sistemas e Eletrônica (Opcional)
        sistema_injecao: "Bosch ME 17.5.24",
        sonda_lambda: "Pré e Pós Catalisador",
        sensor_fase_rotacao: "Sim",
        sistema_ignicao: "Eletrônica digital",
        ecu: "ACDelco E83",
        // Outros
        imagem: "images/Mrbm6Jp1axfg.jpg",
        carroceria: "Hatch",
        categoria: "Hatch",
        preco: 79990,
        descricao: "O Chevrolet Onix é um dos carros mais vendidos do Brasil, oferecendo ótimo custo-benefício, economia de combustível e tecnologia embarcada de ponta."
    },
    {
        id: "2",
        // Informações Básicas
        marca: "Volkswagen",
        modelo: "Gol",
        ano: 2022,
        versao: "1.0 MPI",
        codigo_motor: "EA211",
        combustivel: "Flex",
        // Motorização
        tipo_motor: "Dianteiro, transversal, 3 cil. em linha",
        cilindrada_cm3: 999,
        potencia_cv: "84 (E) / 75 (G)",
        torque_kgfm: "10.4 (E) / 9.7 (G)",
        valvulas: 12,
        injecao_eletronica: "Multiponto",
        // Transmissão
        cambio: "Manual",
        marchas: 5,
        // Suspensão e Freios
        suspensao_dianteira: "Independente, McPherson",
        suspensao_traseira: "Eixo de torção",
        freios: "Disco sólido (diant.) / Tambor (tras.)",
        abs_ebd: "Sim",
        // Direção e Pneus
        direcao: "Hidráulica",
        pneus_originais: "185/65 R14",
        // Dimensões
        comprimento_mm: 3897,
        largura_mm: 1656,
        altura_mm: 1467,
        entre_eixos_mm: 2466,
        altura_solo_mm: 155,
        peso_kg: 998,
        // Desempenho e Consumo
        velocidade_max_kmh: 167,
        aceleracao_0_100_s: 13.1,
        consumo_urbano_kmL: "9.1 (E) / 13.3 (G)",
        consumo_rodoviario_kmL: "10.1 (E) / 14.4 (G)",
        tanque_l: 55,
        // Capacidades
        porta_malas_l: 285,
        carga_util_kg: 442,
        ocupantes: 5,
        // Sistemas e Eletrônica (Opcional)
        sistema_injecao: "Magneti Marelli IAW 9GV",
        sonda_lambda: "Pré e Pós Catalisador",
        sensor_fase_rotacao: "Sim",
        sistema_ignicao: "Eletrônica digital",
        ecu: "Bosch ME17.5.20",
        // Outros
        imagem: "images/RJ6X7nbFwUtu.jpg",
        carroceria: "Hatch",
        categoria: "Hatch",
        preco: 68990,
        descricao: "O Volkswagen Gol é um clássico do mercado brasileiro, conhecido por sua robustez, simplicidade e baixo custo de manutenção."
    },
    {
        id: "3",
        // Informações Básicas
        marca: "Fiat",
        modelo: "Argo",
        ano: 2023,
        versao: "Drive 1.3",
        codigo_motor: "Firefly 1.3",
        combustivel: "Flex",
        // Motorização
        tipo_motor: "Dianteiro, transversal, 4 cil. em linha",
        cilindrada_cm3: 1332,
        potencia_cv: "109 (E) / 101 (G)",
        torque_kgfm: "14.2 (E) / 13.7 (G)",
        valvulas: 8,
        injecao_eletronica: "Multiponto",
        // Transmissão
        cambio: "Manual",
        marchas: 5,
        // Suspensão e Freios
        suspensao_dianteira: "Independente, McPherson",
        suspensao_traseira: "Eixo de torção",
        freios: "Disco ventilado (diant.) / Tambor (tras.)",
        abs_ebd: "Sim",
        // Direção e Pneus
        direcao: "Elétrica",
        pneus_originais: "185/60 R15",
        // Dimensões
        comprimento_mm: 3998,
        largura_mm: 1724,
        altura_mm: 1501,
        entre_eixos_mm: 2521,
        altura_solo_mm: 150,
        peso_kg: 1120,
        // Desempenho e Consumo
        velocidade_max_kmh: 183,
        aceleracao_0_100_s: 10.9,
        consumo_urbano_kmL: "9.0 (E) / 13.0 (G)",
        consumo_rodoviario_kmL: "10.1 (E) / 14.6 (G)",
        tanque_l: 48,
        // Capacidades
        porta_malas_l: 300,
        carga_util_kg: 400,
        ocupantes: 5,
        // Sistemas e Eletrônica (Opcional)
        sistema_injecao: "Magneti Marelli",
        sonda_lambda: "Sim",
        sensor_fase_rotacao: "Sim",
        sistema_ignicao: "Eletrônica digital",
        ecu: "Magneti Marelli 9GF",
        // Outros
        imagem: "images/q2oQEFDzjkH1.png",
        carroceria: "Hatch",
        categoria: "Hatch",
        preco: 75990,
        descricao: "O Fiat Argo se destaca pelo design moderno, bom espaço interno e conjunto mecânico eficiente, sendo uma excelente opção entre os hatches compactos."
    },
    {
        id: "4",
        // Informações Básicas
        marca: "Honda",
        modelo: "Civic",
        ano: 2023,
        versao: "Touring 1.5 Turbo",
        codigo_motor: "L15B7",
        combustivel: "Gasolina",
        // Motorização
        tipo_motor: "Dianteiro, transversal, 4 cil. em linha, Turbo",
        cilindrada_cm3: 1497,
        potencia_cv: 173,
        torque_kgfm: 22.4,
        valvulas: 16,
        injecao_eletronica: "Direta",
        // Transmissão
        cambio: "CVT",
        marchas: 7,
        // Suspensão e Freios
        suspensao_dianteira: "Independente, McPherson",
        suspensao_traseira: "Independente, Multilink",
        freios: "Disco ventilado (diant.) / Disco sólido (tras.)",
        abs_ebd: "Sim",
        // Direção e Pneus
        direcao: "Elétrica",
        pneus_originais: "215/50 R17",
        // Dimensões
        comprimento_mm: 4674,
        largura_mm: 1802,
        altura_mm: 1415,
        entre_eixos_mm: 2735,
        altura_solo_mm: 134,
        peso_kg: 1368,
        // Desempenho e Consumo
        velocidade_max_kmh: 208,
        aceleracao_0_100_s: 8.6,
        consumo_urbano_kmL: 10.8,
        consumo_rodoviario_kmL: 13.4,
        tanque_l: 47,
        // Capacidades
        porta_malas_l: 495,
        carga_util_kg: 402,
        ocupantes: 5,
        // Sistemas e Eletrônica (Opcional)
        sistema_injecao: "PGM-FI",
        sonda_lambda: "Banda Larga + Convencional",
        sensor_fase_rotacao: "Sim",
        sistema_ignicao: "Eletrônica digital",
        ecu: "Keihin",
        // Outros
        imagem: "images/M7FGoWXLWn4u.png",
        carroceria: "Sedan",
        categoria: "Sedan",
        preco: 149990,
        descricao: "O Honda Civic é referência em seu segmento, oferecendo refinamento, conforto, desempenho e tecnologia de ponta em um pacote completo."
    },
    {
        id: "5",
        // Informações Básicas
        marca: "Toyota",
        modelo: "Corolla",
        ano: 2023,
        versao: "Altis Premium Hybrid",
        codigo_motor: "2ZR-FXE",
        combustivel: "Híbrido",
        // Motorização
        tipo_motor: "Dianteiro, transversal, 4 cil. em linha + Motor elétrico",
        cilindrada_cm3: 1798,
        potencia_cv: "122 (combinada)",
        torque_kgfm: "14.5 (combustão) + 16.6 (elétrico)",
        valvulas: 16,
        injecao_eletronica: "Multiponto",
        // Transmissão
        cambio: "CVT",
        marchas: 0,
        // Suspensão e Freios
        suspensao_dianteira: "Independente, McPherson",
        suspensao_traseira: "Independente, Multilink",
        freios: "Disco ventilado (diant.) / Disco sólido (tras.)",
        abs_ebd: "Sim",
        // Direção e Pneus
        direcao: "Elétrica",
        pneus_originais: "225/45 R17",
        // Dimensões
        comprimento_mm: 4630,
        largura_mm: 1780,
        altura_mm: 1455,
        entre_eixos_mm: 2700,
        altura_solo_mm: 130,
        peso_kg: 1425,
        // Desempenho e Consumo
        velocidade_max_kmh: 180,
        aceleracao_0_100_s: 11.0,
        consumo_urbano_kmL: 17.5,
        consumo_rodoviario_kmL: 16.3,
        tanque_l: 43,
        // Capacidades
        porta_malas_l: 470,
        carga_util_kg: 400,
        ocupantes: 5,
        // Sistemas e Eletrônica (Opcional)
        sistema_injecao: "Toyota EFI",
        sonda_lambda: "Sim",
        sensor_fase_rotacao: "Sim",
        sistema_ignicao: "Eletrônica digital",
        ecu: "Toyota ETCS-i",
        // Outros
        imagem: "images/PqD79BflrjgR.webp",
        carroceria: "Sedan",
        categoria: "Sedan",
        preco: 159990,
        descricao: "O Toyota Corolla é sinônimo de confiabilidade e durabilidade, com acabamento refinado e excelente valor de revenda."
    }
];

// Chave para armazenamento no localStorage
const VEICULOS_STORAGE_KEY = 'veiculos';

// Função para carregar veículos do localStorage ou usar os padrão
function carregarVeiculos() {
    const veiculosArmazenados = localStorage.getItem(VEICULOS_STORAGE_KEY);
    if (veiculosArmazenados) {
        return JSON.parse(veiculosArmazenados);
    } else {
        // Se não existirem veículos salvos, usar os padrão e salvar
        salvarVeiculos(carData);
        return carData;
    }
}

// Função para salvar veículos no localStorage
function salvarVeiculos(veiculos) {
    localStorage.setItem(VEICULOS_STORAGE_KEY, JSON.stringify(veiculos));
}

// Função para adicionar um novo veículo
function adicionarVeiculo(novoVeiculo) {
    if (!novoVeiculo.id) {
        // Gerar ID único baseado em timestamp
        novoVeiculo.id = Date.now().toString();
    }
    
    const veiculos = carregarVeiculos();
    veiculos.push(novoVeiculo);
    salvarVeiculos(veiculos);
    return novoVeiculo.id;
}

// Função para atualizar um veículo existente
function atualizarVeiculo(id, dadosAtualizados) {
    let veiculos = carregarVeiculos();
    const index = veiculos.findIndex(v => v.id == id);
    
    if (index !== -1) {
        // Manter o ID original
        dadosAtualizados.id = id;
        veiculos[index] = dadosAtualizados;
        salvarVeiculos(veiculos);
        return true;
    }
    
    return false;
}

// Função para excluir um veículo
function excluirVeiculo(id) {
    let veiculos = carregarVeiculos();
    const novaLista = veiculos.filter(v => v.id != id);
    
    if (novaLista.length < veiculos.length) {
        salvarVeiculos(novaLista);
        return true;
    }
    
    return false;
}

// Função para obter um veículo pelo ID
function obterVeiculoPorId(id) {
    const veiculos = carregarVeiculos();
    return veiculos.find(v => v.id == id);
}

// Função para filtrar veículos por critérios
function filtrarVeiculos(criterios) {
    let veiculos = carregarVeiculos();
    
    // Aplicar filtros se fornecidos
    if (criterios) {
        if (criterios.marca && criterios.marca !== 'all') {
            veiculos = veiculos.filter(v => v.marca === criterios.marca);
        }
        
        if (criterios.ano) {
            veiculos = veiculos.filter(v => v.ano == criterios.ano);
        }
        
        if (criterios.combustivel && criterios.combustivel !== 'todos') {
            veiculos = veiculos.filter(v => v.combustivel === criterios.combustivel);
        }
        
        if (criterios.transmissao && criterios.transmissao !== 'todos') {
            veiculos = veiculos.filter(v => v.cambio === criterios.transmissao);
        }
        
        if (criterios.carroceria && criterios.carroceria !== 'todos') {
            veiculos = veiculos.filter(v => v.carroceria === criterios.carroceria);
        }
        
        if (criterios.categoria && criterios.categoria !== 'todos') {
            veiculos = veiculos.filter(v => v.categoria === criterios.categoria);
        }
        
        if (criterios.potencia) {
            const [min, max] = criterios.potencia.split('-').map(Number);
            veiculos = veiculos.filter(v => {
                // Extrair valor numérico da potência (pode estar em formato "116 (E) / 116 (G)")
                const potenciaStr = String(v.potencia_cv);
                const potenciaNum = parseInt(potenciaStr);
                
                if (isNaN(potenciaNum)) {
                    // Tentar extrair o primeiro número da string
                    const match = potenciaStr.match(/\d+/);
                    if (match) {
                        const valor = parseInt(match[0]);
                        if (!isNaN(valor)) {
                            if (max) {
                                return valor >= min && valor <= max;
                            } else {
                                return valor >= min;
                            }
                        }
                    }
                    return false;
                } else {
                    if (max) {
                        return potenciaNum >= min && potenciaNum <= max;
                    } else {
                        return potenciaNum >= min;
                    }
                }
            });
        }
        
        // Filtro de busca por texto
        if (criterios.busca) {
            const termoBusca = criterios.busca.toLowerCase();
            veiculos = veiculos.filter(v => 
                v.marca.toLowerCase().includes(termoBusca) || 
                v.modelo.toLowerCase().includes(termoBusca) || 
                String(v.ano).includes(termoBusca) ||
                (v.versao && v.versao.toLowerCase().includes(termoBusca))
            );
        }
    }
    
    return veiculos;
}

// Inicializar dados no localStorage se não existirem
document.addEventListener('DOMContentLoaded', function() {
    if (!localStorage.getItem(VEICULOS_STORAGE_KEY)) {
        salvarVeiculos(carData);
    }
    
    // Atualizar a variável global carData com os dados do localStorage
    carData = carregarVeiculos();
});

// Funções originais mantidas para compatibilidade
function loadCars(filter = 'all') {
    const carsGrid = document.getElementById('carsGrid');
    if (!carsGrid) return;
    
    carsGrid.innerHTML = '';
    
    // Obter filtros da interface
    const potenciaFilter = document.getElementById('potenciaFilter')?.value || '';
    const combustivelFilter = document.getElementById('combustivelFilter')?.value || '';
    const transmissaoFilter = document.getElementById('transmissaoFilter')?.value || '';
    const carroceriaFilter = document.getElementById('carroceriaFilter')?.value || '';
    const categoriaFilter = document.getElementById('categoriaFilter')?.value || '';
    const anoFilter = document.getElementById('anoFilter')?.value || '';
    const searchInput = document.getElementById('searchInput')?.value || '';
    
    // Construir critérios de filtro
    const criterios = {
        marca: filter !== 'all' ? filter : '',
        potencia: potenciaFilter,
        combustivel: combustivelFilter !== 'todos' ? combustivelFilter : '',
        transmissao: transmissaoFilter !== 'todos' ? transmissaoFilter : '',
        carroceria: carroceriaFilter !== 'todos' ? carroceriaFilter : '',
        categoria: categoriaFilter !== 'todos' ? categoriaFilter : '',
        ano: anoFilter,
        busca: searchInput
    };
    
    // Obter veículos filtrados
    const veiculosFiltrados = filtrarVeiculos(criterios);
    
    // Verificar se há resultados
    if (veiculosFiltrados.length === 0) {
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
    veiculosFiltrados.forEach(car => {
        const carCard = document.createElement('div');
        carCard.className = 'car-card bg-white rounded-xl shadow-lg overflow-hidden';
        carCard.innerHTML = `
            <div class="relative">
                <img src="${car.imagem || 'images/placeholder.png'}" alt="${car.marca} ${car.modelo}" class="w-full h-48 object-cover">
                <div class="absolute top-0 right-0 bg-blue-600 text-white px-3 py-1 m-2 rounded-lg text-sm font-semibold">
                    ${car.ano}
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold mb-2">${car.marca} ${car.modelo}</h3>
                <div class="flex items-center text-gray-600 mb-4">
                    <span class="text-lg font-semibold text-blue-600">R$ ${car.preco ? car.preco.toLocaleString('pt-BR') : 'Consulte'}</span>
                </div>
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-gas-pump mr-2 text-blue-600"></i>
                        ${car.combustivel || 'N/D'}
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-cogs mr-2 text-blue-600"></i>
                        ${car.tipo_motor ? (car.tipo_motor.length > 15 ? car.tipo_motor.substring(0, 15) + '...' : car.tipo_motor) : 'N/D'}
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-tachometer-alt mr-2 text-blue-600"></i>
                        ${car.potencia_cv || 'N/D'} cv
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-car mr-2 text-blue-600"></i>
                        ${car.carroceria || car.categoria || 'N/D'}
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="ficha_tecnica.html?id=${car.id}" class="flex-1 text-center py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Ver Detalhes
                    </a>
                    <button onclick="gerarPDF('${car.id}')" class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>
            </div>
        `;
        carsGrid.appendChild(carCard);
    });
}

// Função para mostrar detalhes do carro (redirecionando para a página separada)
function showCarDetail(carId) {
    window.location.href = `ficha_tecnica.html?id=${carId}`;
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
    // Resetar campos de filtro
    if (document.getElementById('potenciaFilter')) document.getElementById('potenciaFilter').value = '';
    if (document.getElementById('combustivelFilter')) document.getElementById('combustivelFilter').value = 'todos';
    if (document.getElementById('transmissaoFilter')) document.getElementById('transmissaoFilter').value = 'todos';
    if (document.getElementById('carroceriaFilter')) document.getElementById('carroceriaFilter').value = 'todos';
    if (document.getElementById('categoriaFilter')) document.getElementById('categoriaFilter').value = 'todos';
    if (document.getElementById('anoFilter')) document.getElementById('anoFilter').value = '';
    if (document.getElementById('searchInput')) document.getElementById('searchInput').value = '';
    
    // Resetar botões de filtro
    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    
    // Destacar botão "Todas"
    const allButton = document.querySelector('.filter-btn[onclick="filterCars(\'all\')"]');
    if (allButton) {
        allButton.classList.remove('bg-gray-200', 'text-gray-700');
        allButton.classList.add('bg-blue-600', 'text-white');
    }
    
    loadCars('all');
}

// Função para buscar veículos
function buscarVeiculos() {
    loadCars('all'); // Usar 'all' como marca, o filtro de texto será aplicado pelo valor do campo de busca
}

// Função para gerar PDF (placeholder - será implementada em outro arquivo)
function gerarPDF(carId) {
    alert('Funcionalidade de geração de PDF será implementada em breve.');
    // Esta função será implementada no arquivo de geração de PDF
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
        
        // Carregar carros quando a página de fichas é exibida
        if (pageId === 'fichas') {
            loadCars();
        }
        
        // Atualizar URL com hash
        window.location.hash = pageId;
    }
}

// Função para alternar menu mobile
function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobileMenu');
    if (mobileMenu) {
        if (mobileMenu.classList.contains('hidden')) {
            mobileMenu.classList.remove('hidden');
        } else {
            mobileMenu.classList.add('hidden');
        }
    }
}

// Inicializar quando o documento estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    // Verificar se há um hash na URL para mostrar a página correspondente
    const hash = window.location.hash.substring(1);
    if (hash) {
        showPage(hash);
    }
    
    // Adicionar filtro de ano se não existir
    if (!document.getElementById('anoFilter')) {
        const filtrosContainer = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-4.gap-4');
        if (filtrosContainer) {
            const divAno = document.createElement('div');
            divAno.innerHTML = `
                <label class="block text-sm font-medium text-gray-700 mb-1">Ano</label>
                <select id="anoFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todos</option>
                    ${Array.from(new Set(carregarVeiculos().map(car => car.ano))).sort().map(ano => 
                        `<option value="${ano}">${ano}</option>`
                    ).join('')}
                </select>
            `;
            filtrosContainer.appendChild(divAno);
        }
    }
    
    // Configurar botão de busca
    const searchButton = document.querySelector('button.bg-senai-blue:has(i.fas.fa-search)');
    if (searchButton) {
        searchButton.onclick = buscarVeiculos;
    }
    
    // Configurar campo de busca para buscar ao pressionar Enter
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                buscarVeiculos();
            }
        });
    }
});
