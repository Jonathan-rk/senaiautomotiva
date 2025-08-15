<?php
session_start();

$error = '';
$success = '';

// Login do processador
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUsername = $_POST['username'] ?? '';
    $inputPassword = $_POST['password'] ?? '';
    
    // Verificar credenciais fixas
    if ($inputUsername === 'prof@senai.com' && $inputPassword === 'senha123') {
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = $inputUsername;
        $_SESSION['user_role'] = 'professor';
        
        header('Location: painel_professor.php');
        exit;
    } else {
        $error = 'Usuário ou senha inválidos!';
    }
}

// Verificar se já está logado
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'professor') {
        header('Location: painel_professor.php');
    } else {
        header('Location: fichas.php');
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SENAI Automotivo</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/interface_style.css">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
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
                    <a href="login.php" class="active">
                        <i class="fas fa-sign-in-alt icon"></i>Login
                    </a>
                </div>
                
                <button onclick="toggleMobileMenu()" class="hamburger" id="hamburger" type="button">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobileMenu" class="menu-mobile hidden">
                <a href="index.php">Início</a>
                <a href="fichas.php">Fichas Técnicas</a>
                <a href="login.php" class="active">
                    <i class="fas fa-sign-in-alt icon"></i>Login
                </a>
            </div>
        </div>
    </nav>

    <!-- Página de login -->
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <div class="mx-auto h-12 w-12 bg-senai-blue text-white rounded-lg flex items-center justify-center">
                    <i class="fas fa-user text-xl"></i>
                </div>
                <h2 class="mt-6 text-3xl font-bold text-gray-900">
                    Login SENAI Automotivo
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Acesse sua conta para gerenciar fichas técnicas
                </p>
            </div>
            
            <!-- Mensagem de erro -->
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Mensagem de sucesso -->
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form class="mt-8 space-y-6" method="POST">
                <div class="space-y-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Usuário</label>
                        <input id="username" name="username" type="text" required 
                               class="mt-1 relative block w-full px-3 py-3 border border-gray-300 rounded-lg placeholder-gray-500 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Digite seu usuário"
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                        <input id="password" name="password" type="password" required 
                               class="mt-1 relative block w-full px-3 py-3 border border-gray-300 rounded-lg placeholder-gray-500 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Digite sua senha">
                    </div>
                </div>
                
                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-senai-blue hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Entrar
                    </button>
                </div>
            </form>
            
            <!-- Links Adicionais -->
            <div class="text-center">
                <a href="index.php" class="text-senai-blue hover:underline">
                    <i class="fas fa-arrow-left mr-2"></i>Voltar ao Início
                </a>
            </div>
        </div>
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