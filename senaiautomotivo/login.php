<?php
session_start();

$error = '';
$success = '';

// Processar login
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
                    <a href="login.php" class="text-blue-600 font-semibold">Login</a>
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
                <a href="login.php" class="block py-2 text-blue-600 font-semibold">Login</a>
            </div>
        </div>
    </nav>

    <!-- Login Page -->
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
            
            <!-- Error Message -->
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Success Message -->
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
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-900">
                            Lembrar-me
                        </label>
                    </div>
                </div>
                
                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-senai-blue hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Entrar
                    </button>
                </div>
            </form>
            
            <!-- Additional Links -->
            <div class="text-center">
                <a href="index.php" class="text-senai-blue hover:underline">
                    <i class="fas fa-arrow-left mr-2"></i>Voltar ao Início
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
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
        
        // Auto-focus no primeiro campo
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
        });
    </script>
</body>
</html>

