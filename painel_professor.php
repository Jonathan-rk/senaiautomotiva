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
        // Buscar imagem da ficha antes de excluir
        $stmt = $pdo->prepare("SELECT imagem_path FROM fichas_tecnicas WHERE id = ?");
        $stmt->execute([$_POST['ficha_id']]);
        $imagemPath = $stmt->fetchColumn();

        // Excluir imagem do veículo se for local
        if ($imagemPath && file_exists($imagemPath) && strpos($imagemPath, 'uploads/') === 0) {
            unlink($imagemPath);
        }

        // Excluir ficha técnica
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

$params = [];
$whereConditions = [];
$itensPorPagina = 10; // Set default items per page
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaAtual - 1) * $itensPorPagina;

// Contar total de montadoras
$stmt_montadoras = $pdo->query("SELECT COUNT(*) FROM montadoras");
$total_montadoras = $stmt_montadoras->fetchColumn();

// Contar total de carrocerias
$stmt_carrocerias = $pdo->query("SELECT COUNT(*) FROM carrocerias");
$total_carrocerias = $stmt_carrocerias->fetchColumn();

$itensPorPagina = 10;
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = max(0, ($paginaAtual - 1) * $itensPorPagina);

// Contar total de fichas
$stmt_count = $pdo->query("SELECT COUNT(*) FROM fichas_tecnicas");
$total_fichas = $stmt_count->fetchColumn();

// Contar total de montadoras
$stmt_montadoras = $pdo->query("SELECT COUNT(*) FROM montadoras");
$total_montadoras = $stmt_montadoras->fetchColumn();

// Contar total de carrocerias
$stmt_carrocerias = $pdo->query("SELECT COUNT(*) FROM carrocerias");
$total_carrocerias = $stmt_carrocerias->fetchColumn();

// Calcular total de páginas
$totalPaginas = $itensPorPagina > 0 ? ceil($total_fichas / $itensPorPagina) : 1;

// Buscar fichas com paginação
$sql = "
    SELECT f.*, m.nome as montadoras_nome, c.nome as carroceria_nome 
    FROM fichas_tecnicas f 
    LEFT JOIN montadoras m ON f.montadoras_id = m.id 
    LEFT JOIN carrocerias c ON f.carroceria_id = c.id 
    ORDER BY f.id DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $itensPorPagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$fichas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Função para construir URL de paginação
function buildPaginationUrl($page) {
    $query = $_GET;
    $query['pagina'] = $page;
    return '?' . http_build_query($query);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Professor - SENAI Automotivo</title>
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
    
          <!-- Menu desktop agora dentro do header -->
          <div class="menu-desktop">
            <a href="index.php">Início</a>
            <a href="fichas.php">Fichas Técnicas</a>
            <a href="painel_professor.php" class="active"><i class="fas fa-cog icon"></i>Painel</a>
            <a href="montadoras.php"><i class="fas fa-car icon"></i>Montadoras</a>
            <a href="carrocerias.php"><i class="fas fa-shapes icon"></i>Carrocerias</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt icon"></i>Sair</a>
          </div>
    
          <button class="hamburger" id="hamburger" onclick="toggleMobileMenu()">
            <i class="fas fa-bars"></i>
          </button>
        </div>

        <!-- Manter o mobile menu fora do header se for necessário -->
        <div id="mobileMenu" class="menu-mobile hidden">
          <a href="index.php">Início</a>
          <a href="fichas.php">Fichas Técnicas</a>
          <a href="painel_professor.php" class="active"><i class="fas fa-cog icon"></i>Painel</a>
          <a href="montadoras.php"><i class="fas fa-car icon"></i>Montadoras</a>
          <a href="carrocerias.php"><i class="fas fa-shapes icon"></i>Carrocerias</a>
          <a href="logout.php"><i class="fas fa-sign-out-alt icon"></i>Sair</a>
        </div>
      </div>
    </nav>

    <!-- Conteúdo principal -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold senai-blue mb-2">Painel do Professor</h1>
            <p class="text-gray-600">Gerencie fichas técnicas, montadoras e carrocerias do sistema</p>
        </div>

        <!-- Mensagens -->
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

        <!-- Cartões de estatísticas -->
               <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex items-center">
                <div class="p-4 rounded-full bg-blue-100 text-blue-600 mr-4 w-12 h-12 flex items-center justify-center">
                    <i class="fas fa-file-alt text-xl"></i>
                </div>
                    <div>
                        <p class="text-gray-600 text-sm">Fichas</p>
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
                        <p class="text-gray-600 text-sm">Montadoras</p>
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
                        <p class="text-gray-600 text-sm">Carrocerias</p>
                        <p class="text-2xl font-bold senai-blue"><?php echo $total_carrocerias; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ações rápidas -->
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
       <div class="fichas-container">
           <div class="fichas-header">
               <h2 class="fichas-title">Fichas Técnicas Cadastradas</h2>
           </div>

           <?php if (empty($fichas)): ?>
               <div class="fichas-empty">
                   <i class="fas fa-file-alt fichas-empty-icon"></i>
                   <p class="fichas-empty-text">Nenhuma ficha técnica cadastrada ainda.</p>
                   <a href="criar_ficha.php" class="btn-primary-pf">
                       Criar Primeira Ficha
                   </a>
               </div>
           <?php else: ?>
               <div class="fichas-table-wrapper">
                   <table class="fichas-table">
                       <thead>
                           <tr>
                               <th>Veículo</th>
                               <th></th>
                           </tr>
                       </thead>
                       <tbody>
                           <?php foreach ($fichas as $ficha): ?>
                               <tr>
                                   <td data-label="Veículo">
                                       <div class="ficha-veiculo">
                                           <?php if ($ficha['imagem_path']): ?>
                                               <img src="<?php echo htmlspecialchars($ficha['imagem_path']); ?>" 
                                                    alt="<?php echo htmlspecialchars($ficha['modelo']); ?>" 
                                                    class="ficha-imagem">
                                           <?php else: ?>
                                               <div class="ficha-imagem-placeholder">
                                                   <i class="fas fa-car"></i>
                                               </div>
                                           <?php endif; ?>
                                           <div>
                                               <div class="ficha-modelo">
                                                   <?php echo htmlspecialchars($ficha['modelo']); ?>
                                               </div>
                                               <?php if ($ficha['modelo']): ?>
                                                   <div class="ficha-submodelo">
                                                       <?php echo htmlspecialchars($ficha['modelo']); ?>
                                                       <?php if ($ficha['versao']): ?>
                                                           <?php echo htmlspecialchars($ficha['versao']); ?>
                                                       <?php endif; ?>
                                                   </div>
                                               <?php endif; ?>
                                           </div>
                                       </div>
                                   </td>
                                   <td data-label="Ações">
                                       <div class="ficha-acoes">
                                           <a href="detalhes.php?id=<?php echo $ficha['id']; ?>" class="acao-visualizar" title="Visualizar">
                                               <i class="fas fa-eye"></i>
                                           </a>
                                           <a href="criar_ficha.php?edit=<?php echo $ficha['id']; ?>" class="acao-editar" title="Editar">
                                               <i class="fas fa-edit"></i>
                                           </a>
                                           <button type="button"
                                               class="acao-excluir"
                                               title="Excluir"
                                               onclick="showDeleteModalFicha(<?php echo $ficha['id']; ?>, '<?php echo htmlspecialchars(addslashes($ficha['modelo'])); ?>')">
                                               <i class="fas fa-trash"></i>
                                           </button>
                                       </div>
                                   </td>
                               </tr>
                           <?php endforeach; ?>
                       </tbody>
                   </table>
               </div>
    <?php endif; ?>
     <!-- Paginação -->
     <?php if ($totalPaginas > 1): ?>
                <div class="pagination mt-8">
                    <!-- Botão Anterior -->
                    <?php if ($paginaAtual > 1): ?>
                        <a href="<?php echo buildPaginationUrl($paginaAtual - 1); ?>" class="pagination-btn">
                            <i class="fas fa-chevron-left"></i>
                            <span class="ml-1 hidden md:inline">Anterior</span>
                        </a>
                    <?php else: ?>
                        <span class="pagination-btn opacity-50 cursor-not-allowed">
                            <i class="fas fa-chevron-left"></i>
                            <span class="ml-1 hidden md:inline">Anterior</span>
                        </span>
                    <?php endif; ?>
 
                    <!-- Números das páginas -->
                    <?php
                    $inicio = max(1, $paginaAtual - 2);
                    $fim = min($totalPaginas, $paginaAtual + 2);
                    
                    // Ajustar para sempre mostrar 5 páginas quando possível
                    if ($fim - $inicio < 4) {
                        if ($inicio == 1) {
                            $fim = min($totalPaginas, $inicio + 4);
                        } else {
                            $inicio = max(1, $fim - 4);
                        }
                    }
                    
                    // Primeira página se não estiver no range
                    if ($inicio > 1): ?>
                        <a href="<?php echo buildPaginationUrl(1); ?>" class="pagination-btn">1</a>
                        <?php if ($inicio > 2): ?>
                            <span class="pagination-btn cursor-default">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Páginas do range -->
                    <?php for ($i = $inicio; $i <= $fim; $i++): ?>
                        <?php if ($i == $paginaAtual): ?>
                            <span class="pagination-btn active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="<?php echo buildPaginationUrl($i); ?>" class="pagination-btn"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <!-- Última página se não estiver no range -->
                    <?php if ($fim < $totalPaginas): ?>
                        <?php if ($fim < $totalPaginas - 1): ?>
                            <span class="pagination-btn cursor-default">...</span>
                        <?php endif; ?>
                        <a href="<?php echo buildPaginationUrl($totalPaginas); ?>" class="pagination-btn"><?php echo $totalPaginas; ?></a>
                    <?php endif; ?>

                    <!-- Botão Próximo -->
                    <?php if ($paginaAtual < $totalPaginas): ?>
                        <a href="<?php echo buildPaginationUrl($paginaAtual + 1); ?>" class="pagination-btn">
                            <span class="mr-1 hidden md:inline">Próximo</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php else: ?>
                        <span class="pagination-btn opacity-50 cursor-not-allowed">
                            <span class="mr-1 hidden md:inline">Próximo</span>
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

<!-- Modal de confirmação de exclusão de ficha técnica -->
<div id="deleteModalFicha" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <form method="POST" action="painel_professor.php" class="bg-white rounded-lg shadow-lg p-8 max-w-sm w-full">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Confirmar exclusão</h3>
        <p class="mb-6 text-gray-700">Tem certeza que deseja excluir a ficha técnica do veículo <span id="modalFichaNome" class="font-semibold"></span>?<br>
        Esta ação não poderá ser desfeita.</p>
        <input type="hidden" name="ficha_id" id="modalFichaId" value="">
        <div class="flex justify-end gap-3">
            <button type="button" onclick="hideDeleteModalFicha()" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 font-semibold">Cancelar</button>
            <button type="submit" name="delete_ficha" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 font-semibold">Excluir</button>
        </div>
    </form>
</div>

<script>
function showDeleteModalFicha(id, nome) {
    document.getElementById('modalFichaNome').textContent = nome;
    document.getElementById('modalFichaId').value = id;
    document.getElementById('deleteModalFicha').classList.remove('hidden');
}
function hideDeleteModalFicha() {
    document.getElementById('deleteModalFicha').classList.add('hidden');
}
// Fechar modal ao clicar fora do conteúdo
document.addEventListener('click', function(e) {
    var modal = document.getElementById('deleteModalFicha');
    if (!modal.classList.contains('hidden') && e.target === modal) {
        hideDeleteModalFicha();
    }
});
</script>
    </div>

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


    <script src="js/script.js"></script>
</body>
</html>

