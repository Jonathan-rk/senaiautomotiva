// ficha_detalhes.js - Script para gerenciar a exibição detalhada da ficha técnica

// Obter o ID do veículo da URL
function obterIdVeiculoDaURL() {
    const params = new URLSearchParams(window.location.search);
    return params.get('id');
}

// Carregar dados do veículo
function carregarDadosVeiculo() {
    const id = obterIdVeiculoDaURL();
    
    if (!id) {
        alert('ID do veículo não especificado. Redirecionando para a lista de fichas.');
        window.location.href = 'index.html#fichas';
        return;
    }
    
    // Carregar dados do localStorage ou usar os dados padrão
    let veiculos = JSON.parse(localStorage.getItem('veiculos')) || carData;
    
    // Encontrar o veículo pelo ID
    const veiculo = veiculos.find(v => v.id == id);
    
    if (!veiculo) {
        alert('Veículo não encontrado. Redirecionando para a lista de fichas.');
        window.location.href = 'index.html#fichas';
        return;
    }
    
    // Preencher os dados na página
    preencherDadosVeiculo(veiculo);
    
    // Verificar se o usuário é professor para mostrar botões de edição/exclusão
    verificarPermissoesProfessor();
    
    // Configurar botões de ação
    configurarBotoes(veiculo);
}

// Preencher os dados do veículo na página
function preencherDadosVeiculo(veiculo) {
    // Título e imagem
    document.getElementById('tituloVeiculo').textContent = `${veiculo.marca} ${veiculo.modelo}`;
    document.getElementById('versaoVeiculo').textContent = veiculo.versao || '';
    document.getElementById('anoVeiculo').textContent = veiculo.ano || '';
    
    if (veiculo.imagem) {
        document.getElementById('imagemVeiculo').src = veiculo.imagem;
        document.getElementById('imagemVeiculo').alt = `${veiculo.marca} ${veiculo.modelo}`;
    }
    
    // Preencher todos os campos disponíveis
    const campos = [
        'marca', 'modelo', 'ano', 'versao', 'codigo_motor', 'combustivel',
        'tipo_motor', 'cilindrada_cm3', 'potencia_cv', 'torque_kgfm', 'valvulas', 'injecao_eletronica',
        'cambio', 'marchas',
        'suspensao_dianteira', 'suspensao_traseira', 'freios', 'abs_ebd',
        'direcao', 'pneus_originais',
        'comprimento_mm', 'largura_mm', 'altura_mm', 'entre_eixos_mm', 'altura_solo_mm', 'peso_kg',
        'velocidade_max_kmh', 'aceleracao_0_100_s', 'consumo_urbano_kmL', 'consumo_rodoviario_kmL', 'tanque_l',
        'porta_malas_l', 'carga_util_kg', 'ocupantes',
        'sistema_injecao', 'sonda_lambda', 'sensor_fase_rotacao', 'sistema_ignicao', 'ecu'
    ];
    
    campos.forEach(campo => {
        const elemento = document.getElementById(campo);
        if (elemento && veiculo[campo]) {
            elemento.textContent = veiculo[campo];
        }
    });
    
    // Verificar se há dados de sistemas eletrônicos para mostrar a seção
    const camposSistemas = ['sistema_injecao', 'sonda_lambda', 'sensor_fase_rotacao', 'sistema_ignicao', 'ecu'];
    const temDadosSistemas = camposSistemas.some(campo => veiculo[campo]);
    
    if (temDadosSistemas) {
        document.getElementById('secaoSistemas').classList.remove('hidden');
    }
}

// Verificar se o usuário é professor para mostrar botões de edição/exclusão
function verificarPermissoesProfessor() {
    const isProfessor = localStorage.getItem('professorLoggedIn') === 'true';
    
    const btnEditar = document.getElementById('btnEditarFicha');
    const btnExcluir = document.getElementById('btnExcluirFicha');
    
    if (isProfessor) {
        btnEditar.classList.remove('hidden');
        btnExcluir.classList.remove('hidden');
    } else {
        btnEditar.classList.add('hidden');
        btnExcluir.classList.add('hidden');
    }
    
    // Atualizar links de login/logout
    const loginLink = document.getElementById('loginLink');
    const loginLinkMobile = document.getElementById('loginLinkMobile');
    
    if (isProfessor) {
        if (loginLink) {
            loginLink.innerHTML = '<i class="fas fa-sign-out-alt mr-2"></i>Sair';
            loginLink.setAttribute('onclick', 'logout(); return false;');
        }
        
        if (loginLinkMobile) {
            loginLinkMobile.innerHTML = '<i class="fas fa-sign-out-alt mr-2"></i>Sair';
            loginLinkMobile.setAttribute('onclick', 'logout(); return false;');
        }
        
        // Adicionar link para área do professor
        adicionarLinkProfessor();
    }
}

// Adicionar link para área do professor nos menus
function adicionarLinkProfessor() {
    const menuDesktop = document.querySelector('.md\\:flex.items-center.space-x-6');
    if (menuDesktop && !document.getElementById('professorLinkDesktop')) {
        const link = document.createElement('a');
        link.id = 'professorLinkDesktop';
        link.href = 'index.html#professor';
        link.className = 'text-gray-700 hover:text-blue-600 transition-colors';
        link.innerHTML = '<i class="fas fa-chalkboard-teacher mr-2"></i>Área do Professor';
        menuDesktop.appendChild(link);
    }
    
    const menuMobile = document.getElementById('mobileMenu');
    if (menuMobile && !document.getElementById('professorLinkMobile')) {
        const link = document.createElement('a');
        link.id = 'professorLinkMobile';
        link.href = 'index.html#professor';
        link.className = 'block py-2 text-gray-700';
        link.innerHTML = '<i class="fas fa-chalkboard-teacher mr-2"></i>Área do Professor';
        menuMobile.appendChild(link);
    }
}

// Configurar botões de ação
function configurarBotoes(veiculo) {
    // Botão Gerar PDF
    const btnGerarPDF = document.getElementById('btnGerarPDF');
    if (btnGerarPDF) {
        btnGerarPDF.addEventListener('click', () => {
            gerarPDF(veiculo);
        });
    }
    
    // Botão Editar Ficha (apenas para professor)
    const btnEditar = document.getElementById('btnEditarFicha');
    if (btnEditar) {
        btnEditar.addEventListener('click', () => {
            window.location.href = `index.html#professor?editar=${veiculo.id}`;
        });
    }
    
    // Botão Excluir Ficha (apenas para professor)
    const btnExcluir = document.getElementById('btnExcluirFicha');
    if (btnExcluir) {
        btnExcluir.addEventListener('click', () => {
            if (confirm(`Tem certeza que deseja excluir a ficha do ${veiculo.marca} ${veiculo.modelo}?`)) {
                excluirFicha(veiculo.id);
            }
        });
    }
}

// Função para excluir ficha
function excluirFicha(id) {
    // Carregar veículos do localStorage
    let veiculos = JSON.parse(localStorage.getItem('veiculos')) || carData;
    
    // Filtrar para remover o veículo com o ID especificado
    veiculos = veiculos.filter(v => v.id != id);
    
    // Salvar de volta no localStorage
    localStorage.setItem('veiculos', JSON.stringify(veiculos));
    
    alert('Ficha técnica excluída com sucesso!');
    
    // Redirecionar para a lista de fichas
    window.location.href = 'index.html#fichas';
}

// Função para gerar PDF (será implementada em outro arquivo)
function gerarPDF(veiculo) {
    alert('Funcionalidade de geração de PDF será implementada em breve.');
    // Esta função será implementada no arquivo de geração de PDF
}

// Função de logout (redefinida para compatibilidade)
function logout() {
    localStorage.removeItem('professorLoggedIn');
    window.location.href = 'index.html';
}

// Inicializar quando o documento estiver pronto
document.addEventListener('DOMContentLoaded', carregarDadosVeiculo);
