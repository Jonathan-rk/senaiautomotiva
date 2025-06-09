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

$success = '';
$error = '';

// Processar criação de categoria
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $nome = trim($_POST['nome']);
    
    if (empty($nome)) {
        $error = 'O nome da categoria é obrigatório.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO categorias (nome) VALUES (?)");
            $stmt->execute([$nome]);
            $success = 'Categoria criada com sucesso!';
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                $error = 'Esta categoria já existe.';
            } else {
                $error = 'Erro ao criar categoria: ' . $e->getMessage();
            }
        }
    }
}

// Processar edição de categoria
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = $_POST['id'];
    $nome = trim($_POST['nome']);
    
    if (empty($nome)) {
        $error = 'O nome da categoria é obrigatório.';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE categorias SET nome = ? WHERE id = ?");
            $stmt->execute([$nome, $id]);
            $success = 'Categoria atualizada com sucesso!';
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                $error = 'Esta categoria já existe.';
            } else {
                $error = 'Erro ao atualizar categoria: ' . $e->getMessage();
            }
        }
    }
}

// Processar exclusão de categoria
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    
    // Verificar se a categoria está sendo usada
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM fichas_tecnicas WHERE categoria_id = ?");
    $stmt->execute([$deleteId]);
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        $error = "Não é possível excluir esta categoria pois ela está sendo usada por $count ficha(s) técnica(s).";
    } else {
        $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
        if ($stmt->execute([$deleteId])) {
            $success = "Categoria excluída com sucesso!";
        } else {
            $error = "Erro ao excluir categoria.";
        }
    }
}

// Buscar todas as categorias com contagem de fichas
$stmt = $pdo->query("SELECT c.*, COUNT(f.id) as total_fichas 
                     FROM categorias c 
                     LEFT JOIN fichas_tecnicas f ON c.id = f.categoria_id 
                     GROUP BY c.id 
                     ORDER BY c.nome");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar categoria para edição
$editCategoria = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editCategoria = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Categorias - SENAI Automotivo</title>
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
                    <a href="painel_professor.php" class="nav-link">
                        <i class="fas fa-cog mr-2"></i>Painel
                    </a>
                    <a href="categorias.php" class="nav-link active">
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
                <a href="painel_professor.php" class="nav-link block">
                    <i class="fas fa-cog mr-2"></i>Painel
                </a>
                <a href="categorias.php" class="nav-link block active">
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
                        <i class="fas fa-tags mr-3"></i>Gerenciar Categorias
                    </h1>
                    <p class="text-lg opacity-90">
                        Organize as fichas técnicas por categorias
                    </p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="painel_professor.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Voltar ao Painel
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Form Section -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-xl font-bold senai-blue mb-6">
                            <i class="fas fa-plus mr-2"></i>
                            <?php echo $editCategoria ? 'Editar Categoria' : 'Nova Categoria'; ?>
                        </h2>

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

                        <form method="POST">
                            <input type="hidden" name="action" value="<?php echo $editCategoria ? 'edit' : 'create'; ?>">
                            <?php if ($editCategoria): ?>
                                <input type="hidden" name="id" value="<?php echo $editCategoria['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <label for="nome" class="form-label">
                                    <i class="fas fa-tag mr-2"></i>Nome da Categoria
                                </label>
                                <input type="text" 
                                       id="nome" 
                                       name="nome" 
                                       class="form-input" 
                                       placeholder="Ex: Sedans, SUVs, Hatchbacks..."
                                       value="<?php echo htmlspecialchars($editCategoria['nome'] ?? ''); ?>"
                                       required>
                                <small class="text-gray-500">
                                    Use nomes descritivos e únicos para cada categoria
                                </small>
                            </div>

                            <div class="flex gap-3">
                                <button type="submit" class="btn btn-primary flex-1">
                                    <i class="fas fa-save mr-2"></i>
                                    <?php echo $editCategoria ? 'Atualizar' : 'Criar'; ?>
                                </button>
                                
                                <?php if ($editCategoria): ?>
                                    <a href="categorias.php" class="btn btn-secondary">
                                        <i class="fas fa-times mr-2"></i>Cancelar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>

                        <!-- Quick Stats -->
                        <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-bold text-gray-800 mb-2">
                                <i class="fas fa-chart-bar mr-2"></i>Estatísticas
                            </h3>
                            <div class="text-sm text-gray-600">
                                <p>Total de categorias: <strong><?php echo count($categorias); ?></strong></p>
                                <p>Fichas cadastradas: <strong><?php echo array_sum(array_column($categorias, 'total_fichas')); ?></strong></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Categories List -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-bold senai-blue">
                                    <i class="fas fa-list mr-2"></i>Categorias Cadastradas
                                </h2>
                                <div class="text-sm text-gray-500">
                                    <?php echo count($categorias); ?> categoria(s)
                                </div>
                            </div>
                        </div>

                        <?php if (empty($categorias)): ?>
                            <div class="p-8 text-center">
                                <i class="fas fa-tags text-6xl text-gray-300 mb-4"></i>
                                <h3 class="text-xl font-bold text-gray-600 mb-2">Nenhuma categoria cadastrada</h3>
                                <p class="text-gray-500">
                                    Crie sua primeira categoria para organizar as fichas técnicas
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="divide-y divide-gray-200">
                                <?php foreach ($categorias as $categoria): ?>
                                    <div class="p-6 hover:bg-gray-50 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="w-12 h-12 bg-senai-blue rounded-lg flex items-center justify-center mr-4">
                                                    <i class="fas fa-tag text-white text-lg"></i>
                                                </div>
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900">
                                                        <?php echo htmlspecialchars($categoria['nome']); ?>
                                                    </h3>
                                                    <p class="text-sm text-gray-500">
                                                        <?php echo $categoria['total_fichas']; ?> ficha(s) técnica(s)
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-center space-x-3">
                                                <?php if ($categoria['total_fichas'] > 0): ?>
                                                    <a href="fichas.php?categoria=<?php echo $categoria['id']; ?>" 
                                                       class="text-blue-600 hover:text-blue-800" 
                                                       title="Ver fichas desta categoria">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <a href="categorias.php?edit=<?php echo $categoria['id']; ?>" 
                                                   class="text-green-600 hover:text-green-800" 
                                                   title="Editar categoria">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <button onclick="confirmDelete(<?php echo $categoria['id']; ?>, '<?php echo addslashes($categoria['nome']); ?>', <?php echo $categoria['total_fichas']; ?>)" 
                                                        class="text-red-600 hover:text-red-800" 
                                                        title="Excluir categoria">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> SENAI Automotivo. Todos os direitos reservados.</p>
                <p class="mt-2 text-sm opacity-75">
                    Gerenciamento de categorias - Acesso restrito a professores
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
            <div id="deleteMessage" class="text-gray-600 mb-6"></div>
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
        
        function confirmDelete(id, categoryName, totalFichas) {
            let message;
            if (totalFichas > 0) {
                message = `Não é possível excluir a categoria "${categoryName}" pois ela está sendo usada por ${totalFichas} ficha(s) técnica(s). Remova ou altere a categoria das fichas antes de excluí-la.`;
                document.getElementById('confirmDeleteBtn').style.display = 'none';
            } else {
                message = `Tem certeza que deseja excluir a categoria "${categoryName}"? Esta ação não pode ser desfeita.`;
                document.getElementById('confirmDeleteBtn').style.display = 'inline-flex';
                document.getElementById('confirmDeleteBtn').onclick = function() {
                    window.location.href = `categorias.php?delete=${id}`;
                };
            }
            
            document.getElementById('deleteMessage').innerHTML = message;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('deleteModal').classList.remove('flex');
        }
        
        // Fechar modal ao clicar fora
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
        
        // Auto-focus no campo nome
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('nome').focus();
        });
    </script>
</body>
</html>

