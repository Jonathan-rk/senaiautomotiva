<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/AuthController.php';

// Verificar se o usuário está logado e é professor
$auth = new AuthController();
$auth->requireTeacher();

// Obter usuário atual
$currentUser = $auth->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENAI Automotivo - Painel do Professor</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <style>
        :root {
            --senai-blue: #0A3871;
            --senai-red: #E31E24;
        }
        
        .senai-blue { color: var(--senai-blue); }
        .bg-senai-blue { background-color: var(--senai-blue); }
        .senai-red { color: var(--senai-red); }
        .bg-senai-red { background-color: var(--senai-red); }
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
                
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">
                        <i class="fas fa-user mr-2"></i>
                        <?php echo htmlspecialchars($currentUser->nome); ?>
                    </span>
                    <a href="/api/logout.php" class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-md transition duration-200">
                        <i class="fas fa-sign-out-alt mr-1"></i> Sair
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold senai-blue">Painel do Professor</h1>
                    <p class="text-gray-600 mt-2">Gerencie as fichas técnicas dos veículos</p>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-car text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold">Minhas Fichas</h3>
                        <p class="text-2xl font-bold" id="totalFichas">--</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-calendar text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold">Data</h3>
                        <p class="text-xl font-bold"><?php echo date('d/m/Y'); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-user-shield text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold">Perfil</h3>
                        <p class="text-xl font-bold">Professor</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">Ações Rápidas</h2>
            <div class="flex flex-wrap gap-4">
                <a href="nova_ficha.php" class="px-4 py-2 bg-senai-blue text-white rounded-lg hover:bg-blue-800 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Adicionar Veículo
                </a>
                <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-file-export mr-2"></i>Exportar Dados
                </button>
                <a href="/index.php" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-home mr-2"></i>Página Inicial
                </a>
            </div>
        </div>
        
        <!-- Fichas Técnicas -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Minhas Fichas Técnicas</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modelo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ano</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="fichasTableBody">
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Carregando fichas técnicas...
                            </td>
                        </tr>
                    </tbody>
                </table>
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
            </div>
        </div>
    </footer>

    <script>
        // Carregar fichas técnicas do professor
        document.addEventListener('DOMContentLoaded', function() {
            const professorId = <?php echo $currentUser->id; ?>;
            
            fetch(`/api/get_car.php?professor_id=${professorId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        const fichas = data.data;
                        document.getElementById('totalFichas').textContent = fichas.length;
                        
                        const tableBody = document.getElementById('fichasTableBody');
                        tableBody.innerHTML = '';
                        
                        fichas.forEach(ficha => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="px-6 py-4 whitespace-nowrap">${ficha.id}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${ficha.marca}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${ficha.modelo}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${ficha.ano}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="editar_ficha.php?id=${ficha.id}" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="#" onclick="deleteFicha(${ficha.id})" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i> Excluir
                                    </a>
                                </td>
                            `;
                            tableBody.appendChild(row);
                        });
                    } else {
                        document.getElementById('totalFichas').textContent = '0';
                        document.getElementById('fichasTableBody').innerHTML = `
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    Nenhuma ficha técnica encontrada.
                                </td>
                            </tr>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar fichas:', error);
                    document.getElementById('fichasTableBody').innerHTML = `
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-red-500">
                                Erro ao carregar fichas técnicas.
                            </td>
                        </tr>
                    `;
                });
        });
        
        // Função para excluir ficha
        function deleteFicha(id) {
            if (confirm('Tem certeza que deseja excluir esta ficha técnica?')) {
                fetch(`/api/delete_car.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Ficha técnica excluída com sucesso!');
                        location.reload();
                    } else {
                        alert('Erro ao excluir ficha técnica.');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao excluir ficha técnica.');
                });
            }
        }
    </script>
</body>
</html>
