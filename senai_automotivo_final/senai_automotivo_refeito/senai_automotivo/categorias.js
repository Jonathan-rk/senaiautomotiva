// Implementação para gerenciamento de categorias de veículos
// Este arquivo será incluído após car_data.js e auth.js

// Chave para armazenamento das categorias no localStorage
const CATEGORIAS_STORAGE_KEY = 'senai_categorias_veiculos';

// Categorias padrão
const CATEGORIAS_PADRAO = [
    'Hatch',
    'Sedan',
    'SUV',
    'Picape',
    'Esportivo',
    'Utilitário'
];

// Função para carregar categorias do localStorage ou usar as padrão
function carregarCategorias() {
    const categoriasArmazenadas = localStorage.getItem(CATEGORIAS_STORAGE_KEY);
    if (categoriasArmazenadas) {
        return JSON.parse(categoriasArmazenadas);
    } else {
        // Se não existirem categorias salvas, usar as padrão e salvar
        salvarCategorias(CATEGORIAS_PADRAO);
        return CATEGORIAS_PADRAO;
    }
}

// Função para salvar categorias no localStorage
function salvarCategorias(categorias) {
    localStorage.setItem(CATEGORIAS_STORAGE_KEY, JSON.stringify(categorias));
}

// Função para adicionar uma nova categoria
function adicionarCategoria(novaCategoria) {
    if (!novaCategoria || novaCategoria.trim() === '') {
        alert('Por favor, insira um nome válido para a categoria.');
        return false;
    }
    
    const categorias = carregarCategorias();
    
    // Verificar se a categoria já existe (ignorando maiúsculas/minúsculas)
    if (categorias.some(cat => cat.toLowerCase() === novaCategoria.toLowerCase())) {
        alert('Esta categoria já existe.');
        return false;
    }
    
    // Adicionar nova categoria e salvar
    categorias.push(novaCategoria);
    salvarCategorias(categorias);
    
    // Atualizar interface
    atualizarInterfaceCategorias();
    return true;
}

// Função para remover uma categoria
function removerCategoria(categoria) {
    if (confirm(`Tem certeza que deseja remover a categoria "${categoria}"?`)) {
        let categorias = carregarCategorias();
        categorias = categorias.filter(cat => cat !== categoria);
        salvarCategorias(categorias);
        
        // Atualizar interface
        atualizarInterfaceCategorias();
    }
}

// Função para atualizar a interface com as categorias atuais
function atualizarInterfaceCategorias() {
    const categorias = carregarCategorias();
    
    // Atualizar o filtro de categorias na página de fichas técnicas
    const filtroCategoria = document.getElementById('categoriaFilter');
    if (filtroCategoria) {
        // Limpar opções existentes
        filtroCategoria.innerHTML = '<option value="todos">Todas</option>';
        
        // Adicionar cada categoria como opção
        categorias.forEach(categoria => {
            const option = document.createElement('option');
            option.value = categoria;
            option.textContent = categoria;
            filtroCategoria.appendChild(option);
        });
    }
    
    // Atualizar a lista de categorias na área do professor
    const listaCategorias = document.getElementById('listaCategorias');
    if (listaCategorias) {
        listaCategorias.innerHTML = '';
        
        categorias.forEach(categoria => {
            const itemCategoria = document.createElement('div');
            itemCategoria.className = 'flex justify-between items-center p-2 border-b';
            
            const nomeCategoria = document.createElement('span');
            nomeCategoria.textContent = categoria;
            
            const btnRemover = document.createElement('button');
            btnRemover.className = 'text-red-500 hover:text-red-700';
            btnRemover.innerHTML = '<i class="fas fa-trash"></i>';
            btnRemover.onclick = () => removerCategoria(categoria);
            
            itemCategoria.appendChild(nomeCategoria);
            itemCategoria.appendChild(btnRemover);
            listaCategorias.appendChild(itemCategoria);
        });
    }
}

// Função para abrir o modal de gerenciamento de categorias
function abrirModalCategorias() {
    const modal = document.getElementById('modalCategorias');
    if (modal) {
        modal.classList.remove('hidden');
        atualizarInterfaceCategorias();
    }
}

// Função para fechar o modal de gerenciamento de categorias
function fecharModalCategorias() {
    const modal = document.getElementById('modalCategorias');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Inicializar categorias quando o documento estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    // Verificar se o elemento de filtro de categoria existe, se não, criar
    if (!document.getElementById('categoriaFilter')) {
        const filtrosContainer = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-4.gap-4');
        if (filtrosContainer) {
            const divCategoria = document.createElement('div');
            divCategoria.innerHTML = `
                <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                <select id="categoriaFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="todos">Todas</option>
                </select>
            `;
            filtrosContainer.appendChild(divCategoria);
        }
    }
    
    // Adicionar botão de gerenciamento de categorias na área do professor
    const acoesProfessor = document.querySelector('#professorPage .flex.flex-wrap.gap-4');
    if (acoesProfessor && !document.getElementById('btnGerenciarCategorias')) {
        const btnCategorias = document.createElement('button');
        btnCategorias.id = 'btnGerenciarCategorias';
        btnCategorias.className = 'px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors';
        btnCategorias.innerHTML = '<i class="fas fa-tags mr-2"></i>Gerenciar Categorias';
        btnCategorias.onclick = abrirModalCategorias;
        acoesProfessor.appendChild(btnCategorias);
    }
    
    // Criar modal de gerenciamento de categorias se não existir
    if (!document.getElementById('modalCategorias')) {
        const modal = document.createElement('div');
        modal.id = 'modalCategorias';
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-xl shadow-lg p-6 max-w-md w-full">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold senai-blue">Gerenciar Categorias</h3>
                    <button onclick="fecharModalCategorias()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="formNovaCategoria" class="mb-4" onsubmit="event.preventDefault(); adicionarCategoria(document.getElementById('inputNovaCategoria').value);">
                    <div class="flex gap-2">
                        <input type="text" id="inputNovaCategoria" placeholder="Nova categoria" 
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <button type="submit" class="px-4 py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-800 transition-colors">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </form>
                
                <div id="listaCategorias" class="max-h-60 overflow-y-auto">
                    <!-- Lista de categorias será carregada aqui -->
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    // Inicializar categorias
    atualizarInterfaceCategorias();
});
