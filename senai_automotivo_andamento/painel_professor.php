<?php
session_start();

// Verificar se o usuário está logado e é professor
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'professor') {
    header('Location: login.php');
    exit;
}

// Configuração do banco de dados
$host = 'localhost';
$dbname = 'senai_automotivo';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Processar exclusão de ficha
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM fichas_tecnicas WHERE id = ?");
    if ($stmt->execute([$deleteId])) {
        $success = "Ficha técnica excluída com sucesso!";
    } else {
        $error = "Erro ao excluir ficha técnica.";
    }
}

// Buscar todas as fichas técnicas
$stmt = $pdo->query("SELECT f.*, c.nome as categoria_nome FROM fichas_tecnicas f 
                     LEFT JOIN categorias c ON f.categoria_id = c.id 
                     ORDER BY f.marca, f.modelo, f.ano DESC");
$fichas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar estatísticas
$stats = [
    'total_fichas' => $pdo->query("SELECT COUNT(*) FROM fichas_tecnicas")->fetchColumn(),
    'total_categorias' => $pdo->query("SELECT COUNT(*) FROM categorias")->fetchColumn(),
    'marcas_distintas' => $pdo->query("SELECT COUNT(DISTINCT marca) FROM fichas_tecnicas")->fetchColumn()
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Professor - SENAI Automotivo</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container mx-auto px-4">
            <nav class="flex justify-between items-center py-4">
                <a href="index.php" class="logo">
                    <i class="fas fa-car mr-2"></i>SENAI Automotivo
                </a>
                
                <div class="hidden md:flex space-x-4">
                    <a href="index.php" class="nav-link">
                        <i class="fas fa-home mr-2"></i>Início
                    </a>
                    <a href="fichas.php" class="nav-link">
                        <i class="fas fa-file-alt mr-2"></i>Fichas Técnicas
                    </a>
                    <a href="painel_professor.php" class="nav-link active">
                        <i class="fas fa-cog mr-2"></i>Painel
                    </a>
                    <a href="categorias.php" class="nav-link">
                        <i class="fas fa-tags mr-2"></i>Categorias
                    </a>
                    <a href="logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt mr-2"></i>Sair
                    </a>
                </div>
                
                <!-- Mobile menu button -->
                <button class="md:hidden text-white" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </nav>
            
            <!-- Mobile menu -->
            <div id="mobileMenu" class="hidden md:hidden pb-4">
                <a href="index.php" class="nav-link block">
                    <i class="fas fa-home mr-2"></i>Início
                </a>
                <a href="fichas.php" class="nav-link block">
                    <i class="fas fa-file-alt mr-2"></i>Fichas Técnicas
                </a>
                <a href="painel_professor.php" class="nav-link block active">
                    <i class="fas fa-cog mr-2"></i>Painel
                </a>
                <a href="categorias.php" class="nav-link block">
                    <i class="fas fa-tags mr-2"></i>Categorias
                </a>
                <a href="logout.php" class="nav-link block">
                    <i class="fas fa-sign-out-alt mr-2"></i>Sair
                </a>
            </div>
        </div>
    </header>

    <!-- Page Header -->
    <section class="bg-senai-blue text-white py-8">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-2">
                        <i class="fas fa-cog mr-3"></i>Painel do Professor
                    </h1>
                    <p class="text-lg opacity-90">
                        Bem-vindo, <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="criar_ficha.php" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i>Nova Ficha Técnica
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics -->
    <section class="py-8 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-xl shadow text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file-alt text-2xl text-senai-blue"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900"><?php echo $stats['total_fichas']; ?></h3>
                    <p class="text-gray-600">Fichas Técnicas</p>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tags text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900"><?php echo $stats['total_categorias']; ?></h3>
                    <p class="text-gray-600">Categorias</p>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-car text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900"><?php echo $stats['marcas_distintas']; ?></h3>
                    <p class="text-gray-600">Marcas Diferentes</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Actions -->
    <section class="py-8">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold senai-blue mb-6">
                <i class="fas fa-bolt mr-2"></i>Ações Rápidas
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="criar_ficha.php" class="bg-white p-6 rounded-xl shadow hover-lift text-center">
                    <i class="fas fa-plus text-3xl text-senai-blue mb-3"></i>
                    <h3 class="font-bold mb-2">Nova Ficha</h3>
                    <p class="text-gray-600 text-sm">Criar nova ficha técnica</p>
                </a>
                
                <a href="categorias.php" class="bg-white p-6 rounded-xl shadow hover-lift text-center">
                    <i class="fas fa-tags text-3xl text-green-600 mb-3"></i>
                    <h3 class="font-bold mb-2">Categorias</h3>
                    <p class="text-gray-600 text-sm">Gerenciar categorias</p>
                </a>
                
                <a href="fichas.php" class="bg-white p-6 rounded-xl shadow hover-lift text-center">
                    <i class="fas fa-eye text-3xl text-purple-600 mb-3"></i>
                    <h3 class="font-bold mb-2">Visualizar</h3>
                    <p class="text-gray-600 text-sm">Ver fichas públicas</p>
                </a>
                
                <a href="#backup" onclick="exportData()" class="bg-white p-6 rounded-xl shadow hover-lift text-center">
                    <i class="fas fa-download text-3xl text-orange-600 mb-3"></i>
                    <h3 class="font-bold mb-2">Exportar</h3>
                    <p class="text-gray-600 text-sm">Backup dos dados</p>
                </a>
            </div>
        </div>
    </section>

    <!-- Fichas Management -->
    <section class="py-8 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold senai-blue">
                    <i class="fas fa-list mr-2"></i>Gerenciar Fichas Técnicas
                </h2>
                <div class="flex gap-2">
                    <input type="text" 
                           id="searchFichas" 
                           placeholder="Buscar fichas..." 
                           class="form-input"
                           onkeyup="filterFichas()">
                </div>
            </div>

            <?php if (isset($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($fichas)): ?>
                <div class="bg-white rounded-xl shadow p-8 text-center">
                    <i class="fas fa-file-alt text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-600 mb-2">Nenhuma ficha cadastrada</h3>
                    <p class="text-gray-500 mb-4">
                        Comece criando sua primeira ficha técnica
                    </p>
                    <a href="criar_ficha.php" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i>Criar Primeira Ficha
                    </a>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Veículo
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Categoria
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Motor
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="fichasTable">
                                <?php foreach ($fichas as $ficha): ?>
                                    <tr class="hover:bg-gray-50 ficha-row">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-12 w-12">
                                                    <img class="h-12 w-12 rounded-lg object-cover" 
                                                         src="https://via.placeholder.com/48x48/254AA5/ffffff?text=<?php echo urlencode(substr($ficha['marca'], 0, 2)); ?>" 
                                                         alt="<?php echo htmlspecialchars($ficha['marca']); ?>">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 ficha-name">
                                                        <?php echo htmlspecialchars($ficha['marca'] . ' ' . $ficha['modelo']); ?>
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        <?php echo htmlspecialchars($ficha['ano']); ?>
                                                        <?php if ($ficha['versao']): ?>
                                                            - <?php echo htmlspecialchars($ficha['versao']); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if ($ficha['categoria_nome']): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <?php echo htmlspecialchars($ficha['categoria_nome']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-gray-400">Sem categoria</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($ficha['tipo_motor'] ?: 'N/A'); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="detalhes.php?id=<?php echo $ficha['id']; ?>" 
                                                   class="text-blue-600 hover:text-blue-900" 
                                                   title="Visualizar">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="criar_ficha.php?edit=<?php echo $ficha['id']; ?>" 
                                                   class="text-green-600 hover:text-green-900" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="confirmDelete(<?php echo $ficha['id']; ?>, '<?php echo addslashes($ficha['marca'] . ' ' . $ficha['modelo']); ?>')" 
                                                        class="text-red-600 hover:text-red-900" 
                                                        title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> SENAI Automotivo. Todos os direitos reservados.</p>
                <p class="mt-2 text-sm opacity-75">
                    Painel administrativo - Acesso restrito a professores
                </p>
            </div>
        </div>
    </footer>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md mx-4">
            <div class="flex items-center mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl mr-3"></i>
                <h3 class="text-lg font-bold">Confirmar Exclusão</h3>
            </div>
            <p class="text-gray-600 mb-6">
                Tem certeza que deseja excluir a ficha técnica "<span id="deleteVehicleName"></span>"?
                Esta ação não pode ser desfeita.
            </p>
            <div class="flex justify-end space-x-3">
                <button onclick="closeDeleteModal()" class="btn btn-secondary">
                    Cancelar
                </button>
                <button id="confirmDeleteBtn" class="btn btn-danger">
                    <i class="fas fa-trash mr-2"></i>Excluir
                </button>
            </div>
        </div>
    </div>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
        
        function filterFichas() {
            const searchTerm = document.getElementById('searchFichas').value.toLowerCase();
            const rows = document.querySelectorAll('.ficha-row');
            
            rows.forEach(row => {
                const name = row.querySelector('.ficha-name').textContent.toLowerCase();
                if (name.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        function confirmDelete(id, vehicleName) {
            document.getElementById('deleteVehicleName').textContent = vehicleName;
            document.getElementById('confirmDeleteBtn').onclick = function() {
                window.location.href = `painel_professor.php?delete=${id}`;
            };
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('deleteModal').classList.remove('flex');
        }
        
        function exportData() {
            // Implementar exportação de dados
            alert('Funcionalidade de exportação será implementada em breve.');
        }
        
        // Fechar modal ao clicar fora
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
</body>
</html>

