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
    <link rel="stylesheet" href="css/interface_style.css">
</head>
<body class="bg-gray-50">
    <!-- Navegação -->
    <nav class="navbar">
      <div class="container">
        <div class="navbar-header">
          <a href="index.php">
             <img src="images/wU7KMxCEL6DS.png" alt="SENAI Logo" class="logo">
          </a>
    
          <!-- Menu desktop -->
          <div class="menu-desktop">
            <a href="index.php">Início</a>
            <a href="fichas.php">Fichas Técnicas</a>
            <a href="painel_professor.php"><i class="fas fa-cog icon"></i>Painel</a>
            <a href="montadoras.php"><i class="fas fa-car icon"></i>Montadoras</a>
            <a href="carrocerias.php" class="active"><i class="fas fa-shapes icon"></i>Carrocerias</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt icon"></i>Sair</a>
          </div>
    
          <button class="hamburger" id="hamburger" type="button" onclick="toggleMobileMenu()">
            <i class="fas fa-bars"></i>
          </button>
        </div>

        <!-- Mobile menu -->
        <div id="mobileMenu" class="menu-mobile hidden">
          <a href="index.php">Início</a>
          <a href="fichas.php">Fichas Técnicas</a>
          <a href="painel_professor.php"><i class="fas fa-cog icon"></i>Painel</a>
          <a href="montadoras.php"><i class="fas fa-car icon"></i>Montadoras</a>
          <a href="carrocerias.php" class="active"><i class="fas fa-shapes icon"></i>Carrocerias</a>
          <a href="logout.php"><i class="fas fa-sign-out-alt icon"></i>Sair</a>
        </div>
      </div>
    </nav>

    <!-- Botão Voltar -->
    <section class="Breadcrumb">
        <div class="max-w-7xl mx-auto px-4">
            <a href="painel_professor.php" id="backButton" class="fixed top-24 left-6 bg-senai-blue text-white w-12 h-12 flex items-center justify-center rounded-full shadow-lg hover:bg-blue-700 transition-colors z-50">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </section>

    <!-- Conteúdo principal -->
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

           <!-- Adicionar/Editar Formulário Carroceria -->
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

            <!-- Lista de Carrocerias -->
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
                                            <button type="button"
                                                class="text-red-600 hover:text-red-900"
                                                onclick="showDeleteModal(<?php echo $carroceria['id']; ?>, '<?php echo htmlspecialchars(addslashes($carroceria['nome'])); ?>')">
                                                <i class="fas fa-trash"></i> Excluir
                                            </button>
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
<footer class="footer">
    <div class="footer-container">
        <div class="footer-main-content">
            <div class="footer-logo-section">
                <img src="images/wU7KMxCEL6DS.png" alt="SENAI Logo" class="footer-logo">
                <p class="footer-copyright">SENAI - Serviço Nacional de Aprendizagem Industrial</p>
            </div>
            <div class="footer-social-links">
                <!-- Social Media Links -->
                <a href="https://www.facebook.com/senaijoinvilleoficial" target="_blank" rel="noopener noreferrer" class="footer-social-link">
                    <i class="fab fa-facebook-f footer-social-icon"></i>
                </a>
                <a href="https://www.instagram.com/senai.joinvillesc/" target="_blank" rel="noopener noreferrer" class="footer-social-link">
                    <i class="fab fa-instagram footer-social-icon"></i>
                </a>
                <a href="https://www.linkedin.com/company/senai-sc" target="_blank" rel="noopener noreferrer" class="footer-social-link">
                    <i class="fab fa-linkedin-in footer-social-icon"></i>
                </a>
                <a href="https://www.youtube.com/@senaibrasil" target="_blank" rel="noopener noreferrer" class="footer-social-link">
                    <i class="fab fa-youtube footer-social-icon"></i>
                </a>
            </div>
        </div>
        <div class="footer-bottom-section">
            <div class="footer-bottom-content">
                <div class="footer-links-section">
                    <a href="desenvolvedores.php" class="footer-link margin-right-4">Desenvolvedores</a>
                    <a href="https://fiesc.com.br/pt-br/politica-de-privacidade#:~:text=O%20Sistema%20FIESC%2C%20composto%20pela,e%20sites%20do%20Sistema%20FIESC" class="footer-link margin-right-4">Política de Privacidade</a>
                    <a href="https://ava.sesisenai.org.br/local/faqs/viewall.php" class="footer-link">Termos de Uso</a>
                </div>
                <div class="footer-text-left">
                    <p class="footer-copyright">&copy; <?php echo date("Y"); ?> SENAI Automotivo. Todos os direitos reservados.</p>
                </div>
            </div>
        </div>
    </div>
</footer>

    <!-- Modal de confirmação de exclusão -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
        <form method="GET" action="carrocerias.php" class="bg-white rounded-lg shadow-lg p-8 max-w-sm w-full">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Confirmar exclusão</h3>
            <p class="mb-6 text-gray-700">Tem certeza que deseja excluir a carroceria <span id="modalCarroceriaNome" class="font-semibold"></span>?<br>
            Isso também removerá fichas técnicas associadas a ela.</p>
            <input type="hidden" name="delete" id="modalCarroceriaId" value="">
            <div class="flex justify-end gap-3">
                <button type="button" onclick="hideDeleteModal()" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 font-semibold">Cancelar</button>
                <button type="submit" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 font-semibold">Excluir</button>
            </div>
        </form>
    </div>

    <script>
    function showDeleteModal(id, nome) {
        document.getElementById('modalCarroceriaNome').textContent = nome;
        document.getElementById('modalCarroceriaId').value = id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }
    function hideDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
    // Fechar modal ao clicar fora do conteúdo
    document.addEventListener('click', function(e) {
        var modal = document.getElementById('deleteModal');
        if (!modal.classList.contains('hidden') && e.target === modal) {
            hideDeleteModal();
        }
    });
    </script>
    <script src="js/script.js"></script>
</body>
</html>

