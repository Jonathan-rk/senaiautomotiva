<?php
session_start();
require_once 'config.php';

// Verificar se o usuário está logado e é professor
requireLogin();
requireProfessor();

$pdo = getDBConnection();

$success = '';
$error = '';
$editMontadoras = null;

// Processar upload de imagem
function uploadImage($file, $uploadDir = 'uploads/montadoras/') {
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Tipo de arquivo não permitido. Use JPEG, PNG, GIF ou WebP.');
    }
    
    if ($file['size'] > $maxSize) {
        throw new Exception('Arquivo muito grande. Tamanho máximo: 5MB.');
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filepath;
    } else {
        throw new Exception('Erro ao fazer upload da imagem.');
    }
}

// Processar adição/edição de montadoras
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitizeInput($_POST['nome']);
    $montadorasId = isset($_POST['id']) ? sanitizeInput($_POST['id']) : null;
    $imagemPath = null;

    if (empty($nome)) {
        $error = 'O nome da montadora é obrigatório.';
    } else {
        try {
            // Processar upload de imagem se fornecida
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $imagemPath = uploadImage($_FILES['imagem']);
            }
            
            if ($montadorasId) {
                // Editar montadoras existente
                if ($imagemPath) {
                    // Buscar imagem antiga para deletar
                    $stmt = $pdo->prepare("SELECT imagem_path FROM montadoras WHERE id = ?");
                    $stmt->execute([$montadorasId]);
                    $oldImage = $stmt->fetchColumn();
                    
                    if ($oldImage && file_exists($oldImage)) {
                        unlink($oldImage);
                    }

                    $stmt = $pdo->prepare("UPDATE montadoras SET nome = ?, imagem_path = ? WHERE id = ?");
                    $stmt->execute([$nome, $imagemPath, $montadorasId]);
                } else {
                    $stmt = $pdo->prepare("UPDATE montadoras SET nome = ? WHERE id = ?");
                    $stmt->execute([$nome, $montadorasId]);
                }
                $success = 'Montadora atualizada com sucesso!';
            } else {
                // Adicionar nova montadora
                $stmt = $pdo->prepare("INSERT INTO montadoras (nome, imagem_path) VALUES (?, ?)");
                $stmt->execute([$nome, $imagemPath]);
                $success = 'Montadora adicionada com sucesso!';
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                $error = 'Esta montadora já existe.';
            } else {
                $error = 'Erro ao salvar montadora: ' . $e->getMessage();
            }
        }
    }
}

// Processar exclusão de montadora
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $montadorasId = sanitizeInput($_GET['delete']);
    try {
        // Buscar imagem para deletar
        $stmt = $pdo->prepare("SELECT imagem_path FROM montadoras WHERE id = ?");
        $stmt->execute([$montadorasId]);
        $imagePath = $stmt->fetchColumn();
        
        if ($imagePath && file_exists($imagePath)) {
            unlink($imagePath);
        }
        
        $stmt = $pdo->prepare("DELETE FROM montadoras WHERE id = ?");
        $stmt->execute([$montadorasId]);
        $success = 'Montadora excluída com sucesso!';
    } catch (PDOException $e) {
        $error = 'Erro ao excluir montadora: ' . $e->getMessage();
    }
}

// Carregar montadora para edição
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $montadorasId = sanitizeInput($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM montadoras WHERE id = ?");
    $stmt->execute([$montadorasId]);
    $editMontadoras = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$editMontadoras) {
        $error = 'Montadora não encontrada para edição.';
    }
}

// Buscar todas as montadoras
$stmt = $pdo->query("SELECT * FROM montadoras ORDER BY nome");
$montadoras = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Montadoras - SENAI Automotivo</title>
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
                    <a href="montadoras.php" class="text-blue-600 font-semibold">
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
                <a href="painel_professor.php" class="block py-2 text-gray-700">
                    <i class="fas fa-cog mr-2"></i>Painel
                </a>
                <a href="montadoras.php" class="block py-2 text-blue-600 font-semibold">
                    <i class="fas fa-car mr-2"></i>Montadoras
                </a>
                <a href="carrocerias.php" class="text-gray-700 hover:text-blue-600 transition-colors">
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
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <div>
                <h1 class="text-3xl font-bold mb-2 text-center flex justify-center items-center">
                    <i class="fas fa-car mr-3"></i>Gerenciar Montadoras
                </h1>
                    <p class="text-lg opacity-90">
                        Adicione, edite ou exclua montadoras de veículos
                    </p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="painel_professor.php" class="">
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

            <!-- Add/Edit montadoras Form -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-2xl font-bold senai-blue mb-6">
                    <i class="fas fa-<?php echo $editMontadoras ? 'edit' : 'plus'; ?>-circle mr-2"></i>
                    <?php echo $editMontadoras ? 'Editar Montadora' : 'Adicionar Nova Montadora'; ?>
                </h2>
                <form method="POST" action="montadoras.php" enctype="multipart/form-data" class="space-y-6">
                    <?php if ($editMontadoras): ?>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($editMontadoras['id']); ?>">
                    <?php endif; ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome da Montadora *</label>
                            <input type="text" id="nome" name="nome" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($editMontadoras['nome'] ?? ''); ?>" required>
                        </div>
                        
                        <div>
                            <label for="imagem" class="block text-sm font-medium text-gray-700 mb-1">Imagem da Montadora</label>
                            <input type="file" id="imagem" name="imagem" accept="image/*" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm">
                            <p class="text-xs text-gray-500 mt-1">Formatos aceitos: JPEG, PNG, GIF, WebP. Tamanho máximo: 5MB</p>
                            
                            <?php if ($editMontadoras && $editMontadoras['imagem_path']): ?>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-600">Imagem atual:</p>
                                    <img src="<?php echo htmlspecialchars($editMontadoras['imagem_path']); ?>" alt="<?php echo htmlspecialchars($editMontadoras['nome']); ?>" class="w-20 h-20 object-contain border rounded">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="flex gap-4">
                        <button type="submit" class="px-6 py-3 bg-senai-blue text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                            <i class="fas fa-save mr-2"></i><?php echo $editMontadoras ? 'Atualizar' : 'Adicionar'; ?> Montadora
                        </button>
                        <?php if ($editMontadoras): ?>
                            <a href="montadoras.php" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-semibold">
                                Cancelar
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Montadoras List -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold senai-blue mb-6">
                    <i class="fas fa-list mr-2"></i>Montadoras Cadastradas
                </h2>
                <?php if (empty($montadoras)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-car text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">Nenhuma montadora cadastrada</h3>
                        <p class="text-gray-500">Adicione sua primeira montadora acima.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <?php foreach ($montadoras as $montadoras): ?>
                            <div class="bg-gray-50 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="text-center">
                                    <?php if ($montadoras['imagem_path']): ?>
                                        <img src="<?php echo htmlspecialchars($montadoras['imagem_path']); ?>" 
                                             alt="<?php echo htmlspecialchars($montadoras['nome']); ?>" 
                                             class="w-20 h-20 object-contain mx-auto mb-3 rounded">
                                    <?php else: ?>
                                        <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center mx-auto mb-3">
                                            <i class="fas fa-car text-2xl text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>

                                    <h3 class="font-semibold text-gray-900 mb-3"><?php echo htmlspecialchars($montadoras['nome']); ?></h3>

                                    <div class="flex justify-center space-x-2">
                                        <a href="montadoras.php?edit=<?php echo $montadoras['id']; ?>" class="px-3 py-1 bg-green-500 text-white text-sm rounded hover:bg-green-600 transition-colors">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <a href="montadoras.php?delete=<?php echo $montadoras['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir esta montadora? Isso também removerá fichas técnicas associadas a ela.');" class="px-3 py-1 bg-red-500 text-white text-sm rounded hover:bg-red-600 transition-colors">
                                            <i class="fas fa-trash"></i> Excluir
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
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

