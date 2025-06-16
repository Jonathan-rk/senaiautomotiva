<?php
session_start();
require_once 'config.php';

// Verificar se o usuário está logado e é professor
if (!isLoggedIn() || $_SESSION['user_role'] !== 'professor') {
    header('Location: login.php');
    exit();
}

$pdo = getDBConnection();
$success = '';
$error = '';

// Processar exclusão de ficha técnica
if (isset($_POST['delete_ficha'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM fichas_tecnicas WHERE id = ?");
        $stmt->execute([$_POST['ficha_id']]);
        $success = "Ficha técnica excluída com sucesso!";
    } catch (PDOException $e) {
        $error = "Erro ao excluir ficha técnica: " . $e->getMessage();
    }
}

// Buscar fichas técnicas
$stmt = $pdo->query("
    SELECT f.*, m.nome as montadoras_nome, c.nome as carroceria_nome 
    FROM fichas_tecnicas f 
    LEFT JOIN montadoras m ON f.montadoras_id = m.id 
    LEFT JOIN carrocerias c ON f.carroceria_id = c.id 
    ORDER BY f.id DESC
");
$fichas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar total de fichas
$stmt_count = $pdo->query("SELECT COUNT(*) FROM fichas_tecnicas");
$total_fichas = $stmt_count->fetchColumn();

// Contar total de montadoras
$stmt_montadoras = $pdo->query("SELECT COUNT(*) FROM montadoras");
$total_montadoras = $stmt_montadoras->fetchColumn();

// Contar total de carrocerias
$stmt_carrocerias = $pdo->query("SELECT COUNT(*) FROM carrocerias");
$total_carrocerias = $stmt_carrocerias->fetchColumn();

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Professor - SENAI Automotivo</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <link rel="stylesheet" href="interface_style.css">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <img src="images/wU7KMxCEL6DS.png" alt="SENAI Logo" class="h-10">
                </div>
                
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="text-gray-700 hover:text-blue-600 transition-colors">Início</a>
                    <a href="fichas.php" class="text-gray-700 hover:text-blue-600 transition-colors">Fichas Técnicas</a>
                    <a href="painel_professor.php" class="text-blue-600 font-semibold">
                        <i class="fas fa-cog mr-2"></i>Painel
                    </a>
                    <a href="montadoras.php" class="text-gray-700 hover:text-blue-600 transition-colors">
                        <i class="fas fa-car mr-2"></i>Montadoras
                    </a>
                    <a href="carrocerias.php" class="text-gray-700 hover:text-blue-600 transition-colors">
                        <i class="fas fa-shapes mr-2"></i>Carrocerias
                    </a>
                    <a href="logout.php" class="text-gray-700 hover:text-blue-600 transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>Sair
                    </a>
                </div>
                
                <div class="md:hidden">
                    <button onclick="toggleMobileMenu()" class="text-gray-700">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobileMenu" class="hidden md:hidden pb-4">
                <a href="index.php" class="block py-2 text-gray-700">Início</a>
                <a href="fichas.php" class="block py-2 text-gray-700">Fichas Técnicas</a>
                <a href="painel_professor.php" class="block py-2 text-blue-600 font-semibold">
                    <i class="fas fa-cog mr-2"></i>Painel
                </a>
                <a href="montadoras.php" class="block py-2 text-gray-700">
                    <i class="fas fa-car mr-2"></i>Montadoras
                </a>
                <a href="carrocerias.php" class="block py-2 text-gray-700">
                    <i class="fas fa-shapes mr-2"></i>Carrocerias
                </a>
                <a href="logout.php" class="block py-2 text-gray-700">
                    <i class="fas fa-sign-out-alt mr-2"></i>Sair
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold senai-blue mb-2">Painel do Professor</h1>
            <p class="text-gray-600">Gerencie fichas técnicas, montadoras e carrocerias do sistema</p>
        </div>

        <!-- Messages -->
        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex items-center">
                <div class="p-4 rounded-full bg-blue-100 text-blue-600 mr-4 w-12 h-12 flex items-center justify-center">
                    <i class="fas fa-file-alt text-xl"></i>
                </div>
                    <div>
                        <p class="text-gray-600 text-sm">Total de Fichas</p>
                        <p class="text-2xl font-bold senai-blue"><?php echo $total_fichas; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-car text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Total de Montadoras</p>
                        <p class="text-2xl font-bold senai-blue"><?php echo $total_montadoras; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                        <i class="fas fa-shapes text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Total de Carrocerias</p>
                        <p class="text-2xl font-bold senai-blue"><?php echo $total_carrocerias; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 senai-blue">Ações Rápidas</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="criar_ficha.php" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <i class="fas fa-plus-circle text-blue-600 text-2xl mr-3"></i>
                    <div>
                        <p class="font-semibold text-blue-800">Nova Ficha Técnica</p>
                        <p class="text-sm text-blue-600">Cadastrar novo veículo</p>
                    </div>
                </a>
                
                <a href="montadoras.php" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <i class="fas fa-car text-green-600 text-2xl mr-3"></i>
                    <div>
                        <p class="font-semibold text-green-800">Gerenciar Montadoras</p>
                        <p class="text-sm text-green-600">Adicionar/editar Montadoras</p>
                    </div>
                </a>
                
                <a href="carrocerias.php" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <i class="fas fa-shapes text-purple-600 text-2xl mr-3"></i>
                    <div>
                        <p class="font-semibold text-purple-800">Gerenciar Carrocerias</p>
                        <p class="text-sm text-purple-600">Adicionar/editar carrocerias</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Fichas Técnicas Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold senai-blue">Fichas Técnicas Cadastradas</h2>
            </div>
            
            <?php if (empty($fichas)): ?>
                <div class="p-8 text-center">
                    <i class="fas fa-file-alt text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600 mb-4">Nenhuma ficha técnica cadastrada ainda.</p>
                    <a href="criar_ficha.php" class="px-6 py-3 bg-senai-blue text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Criar Primeira Ficha
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Veículo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montadora</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ano</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($fichas as $ficha): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <?php if ($ficha['imagem_path']): ?>
                                                <img src="<?php echo htmlspecialchars($ficha['imagem_path']); ?>" 
                                                     alt="<?php echo htmlspecialchars($ficha['modelo']); ?>" 
                                                     class="h-10 w-10 rounded object-cover mr-3">
                                            <?php else: ?>
                                                <div class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center mr-3">
                                                    <i class="fas fa-car text-gray-400"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($ficha['modelo']); ?>
                                                </div>
                                                <?php if ($ficha['modelo']): ?>
                                                    <div class="text-sm text-gray-500">
                                                        <?php echo htmlspecialchars($ficha['modelo']); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($ficha['montadoras_nome'] ?? 'N/A'); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($ficha['ano']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="detalhes.php?id=<?php echo $ficha['id']; ?>" 
                                               class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="criar_ficha.php?edit=<?php echo $ficha['id']; ?>" 
                                               class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir esta ficha técnica?')">
                                                <input type="hidden" name="ficha_id" value="<?php echo $ficha['id']; ?>">
                                                <button type="submit" name="delete_ficha" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

<!-- Footer -->
<footer class="bg-senai-blue text-white py-6">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-start items-start">
            <div class="mb-6 md:mb-0">
                <img src="images/wU7KMxCEL6DS.png" alt="SENAI Logo" class="h-10">
                <p class="mt-2 text-sm opacity-80">&copy; <?php echo date("Y"); ?> SENAI Automotivo. Todos os direitos reservados.</p>
            </div>
            <div class="flex space-x-6 ml-0">
                <!-- Social Media Links -->
                <a href="https://www.facebook.com/senaijoinvilleoficial" target="_blank" rel="noopener noreferrer" class="hover:text-gray-300 transition-colors">
                    <i class="fab fa-facebook-f text-xl"></i>
                </a>
                <a href="https://www.instagram.com/senai.joinvillesc/" target="_blank" rel="noopener noreferrer" class="hover:text-gray-300 transition-colors">
                    <i class="fab fa-instagram text-xl"></i>
                </a>
                <a href="https://www.linkedin.com/company/senai-sc" target="_blank" rel="noopener noreferrer" class="hover:text-gray-300 transition-colors">
                    <i class="fab fa-linkedin-in text-xl"></i>
                </a>
                <a href="https://www.youtube.com/@SENAISantaCatarina" target="_blank" rel="noopener noreferrer" class="hover:text-gray-300 transition-colors">
                    <i class="fab fa-youtube text-xl"></i>
                </a>
            </div>
        </div>
        <div class="mt-8 pt-8 border-t border-blue-800 text-sm opacity-80">
            <div class="flex flex-col md:flex-row items-start">
                <div class="mb-4 md:mb-0 text-left">
                    <a href="#" class="hover:text-gray-300 transition-colors mr-4">Política de Privacidade</a>
                    <a href="#" class="hover:text-gray-300 transition-colors mr-4">Termos de Uso</a>
                    <a href="#" class="hover:text-gray-300 transition-colors">Contato</a>
                </div>
                <div class="text-left">
                    <p>SENAI - Serviço Nacional de Aprendizagem Industrial</p>
                </div>
            </div>
        </div>
    </div>
</footer>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html>

