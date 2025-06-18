<?php
session_start();
require_once 'config.php';

// Verificar se o usuário está logado e é professor
requireLogin();
requireProfessor();

$pdo = getDBConnection();

$success = '';
$error = '';
$editCarroceria = null;

// Processar adição/edição de carroceria
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitizeInput($_POST['nome']);
    $carroceriaId = isset($_POST['id']) ? sanitizeInput($_POST['id']) : null;

    if (empty($nome)) {
        $error = 'O nome da carroceria é obrigatório.';
    } else {
        try {
            if ($carroceriaId) {
                // Editar carroceria existente
                $stmt = $pdo->prepare("UPDATE carrocerias SET nome = ? WHERE id = ?");
                $stmt->execute([$nome, $carroceriaId]);
                $success = 'Carroceria atualizada com sucesso!';
            } else {
                // Adicionar nova carroceria
                $stmt = $pdo->prepare("INSERT INTO carrocerias (nome) VALUES (?)");
                $stmt->execute([$nome]);
                $success = 'Carroceria adicionada com sucesso!';
            }
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                $error = 'Esta carroceria já existe.';
            } else {
                $error = 'Erro ao salvar carroceria: ' . $e->getMessage();
            }
        }
    }
}

// Processar exclusão de carroceria
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $carroceriaId = sanitizeInput($_GET['delete']);
    try {
        $stmt = $pdo->prepare("DELETE FROM carrocerias WHERE id = ?");
        $stmt->execute([$carroceriaId]);
        $success = 'Carroceria excluída com sucesso!';
    } catch (PDOException $e) {
        $error = 'Erro ao excluir carroceria: ' . $e->getMessage();
    }
}

// Carregar carroceria para edição
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $carroceriaId = sanitizeInput($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM carrocerias WHERE id = ?");
    $stmt->execute([$carroceriaId]);
    $editCarroceria = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$editCarroceria) {
        $error = 'Carroceria não encontrada para edição.';
    }
}

// Buscar todas as carrocerias
$stmt = $pdo->query("SELECT * FROM carrocerias ORDER BY nome");
$carrocerias = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Carrocerias - SENAI Automotivo</title>
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
                    <a href="painel_professor.php" class="text-gray-700 hover:text-blue-600 transition-colors">
                        <i class="fas fa-cog mr-2"></i>Painel
                    </a>
                    <a href="montadoras.php" class="text-gray-700 hover:text-blue-600 transition-colors">
                        <i class="fas fa-car mr-2"></i>Montadoras
                    </a>
                    <a href="carrocerias.php" class="text-blue-600 font-semibold">
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
                <a href="painel_professor.php" class="block py-2 text-gray-700">
                    <i class="fas fa-cog mr-2"></i>Painel
                </a>
                <a href="montadoras.php" class="block py-2 text-gray-700">
                    <i class="fas fa-car mr-2"></i>Montadoras
                </a>
                <a href="carrocerias.php" class="block py-2 text-blue-600 font-semibold">
                    <i class="fas fa-shapes mr-2"></i>Carrocerias
                </a>
                <a href="logout.php" class="block py-2 text-gray-700">
                    <i class="fas fa-sign-out-alt mr-2"></i>Sair
                </a>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="bg-senai-blue text-white py-8">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-between md:items-center">
            <!-- Texto à esquerda -->
            <div class="md:text-left text-left">
                <h1 class="text-3xl font-bold mb-2 flex justify-start md:justify-center items-center"> 
                    <i class="fas fa-shapes mr-3"></i>Gerenciar Carrocerias
                </h1>
                <p class="text-lg opacity-90">
                    Adicione, edite ou exclua tipos de carroceria de veículos
                </p>
            </div>

            <!-- Botão à esquerda no celular, à direita no desktop -->
            <div class="mt-4 md:mt-0 md:text-right text-left">
                <a href="painel_professor.php" class="inline-block">
                    <i class="fas fa-arrow-left mr-2"></i>Voltar ao Painel
                </a>
            </div>
        </div>
    </div>
</section>


    <!-- Main Content -->
    <section class="py-8">
        <div class="max-w-7xl mx-auto px-4">
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

           <!-- Add/Edit Carroceria Form -->
           <div class="grid grid-cols-1 gap-6">
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-2xl font-bold senai-blue mb-6 text-left">
            <i class="fas fa-<?php echo $editCarroceria ? 'edit' : 'plus'; ?>-circle mr-2"></i>
            <?php echo $editCarroceria ? 'Editar Carroceria' : 'Adicionar Nova Carroceria'; ?>
        </h2>
        <form method="POST" action="carrocerias.php" class="flex flex-col space-y-4">
            <?php if ($editCarroceria): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($editCarroceria['id']); ?>">
            <?php endif; ?>
            <div class="flex-grow">
                <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome da Carroceria *</label>
                <input type="text" id="nome" name="nome" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                       value="<?php echo htmlspecialchars($editCarroceria['nome'] ?? ''); ?>" required>
            </div>
            <div class="flex gap-4">
                <button type="submit" class="px-6 py-3 bg-senai-blue text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                    <i class="fas fa-save mr-2"></i><?php echo $editCarroceria ? 'Atualizar' : 'Adicionar'; ?> Carroceria
                </button>
                <?php if ($editCarroceria): ?>
                    <a href="carrocerias.php" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-semibold">
                        Cancelar
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

            <!-- Carrocerias List -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold senai-blue mb-6">
                    <i class="fas fa-list mr-2"></i>Carrocerias Cadastradas
                </h2>
                <?php if (empty($carrocerias)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-shapes text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">Nenhuma carroceria cadastrada</h3>
                        <p class="text-gray-500">Adicione sua primeira carroceria acima.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome da Carroceria</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($carrocerias as $carroceria): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($carroceria['nome']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <a href="carrocerias.php?edit=<?php echo $carroceria['id']; ?>" class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <a href="carrocerias.php?delete=<?php echo $carroceria['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir esta carroceria? Isso também removerá fichas técnicas associadas a ela.');" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i> Excluir
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

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

