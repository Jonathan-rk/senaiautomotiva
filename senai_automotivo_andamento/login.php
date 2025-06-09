<?php
session_start();

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

$error = '';
$success = '';

// Processar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUsername = $_POST['username'] ?? '';
    $inputPassword = $_POST['password'] ?? '';
    
    // Verificar credenciais fixas ou no banco
    if ($inputUsername === 'prof@senai.com' && $inputPassword === 'senha123') {
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = $inputUsername;
        $_SESSION['user_role'] = 'professor';
        
        header('Location: painel_professor.php');
        exit;
    } else {
        // Verificar no banco de dados
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ? AND password = ?");
        $stmt->execute([$inputUsername, $inputPassword]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            
            if ($user['role'] === 'professor') {
                header('Location: painel_professor.php');
            } else {
                header('Location: fichas.php');
            }
            exit;
        } else {
            $error = 'Usuário ou senha inválidos!';
        }
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
                    <a href="login.php" class="nav-link active">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
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
                <a href="login.php" class="nav-link block active">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
            </div>
        </div>
    </header>

    <!-- Login Section -->
    <section class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full space-y-8">
            <!-- Login Card -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="w-20 h-20 bg-senai-blue rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user text-3xl text-white"></i>
                    </div>
                    <h2 class="text-3xl font-bold senai-blue">
                        Área do Professor
                    </h2>
                    <p class="text-gray-600 mt-2">
                        Faça login para gerenciar as fichas técnicas
                    </p>
                </div>

                <!-- Error Message -->
                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <!-- Success Message -->
                <?php if ($success): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form method="POST" class="space-y-6">
                    <div class="form-group">
                        <label for="username" class="form-label">
                            <i class="fas fa-envelope mr-2"></i>E-mail
                        </label>
                        <input type="email" 
                               id="username" 
                               name="username" 
                               class="form-input" 
                               placeholder="Digite seu e-mail"
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock mr-2"></i>Senha
                        </label>
                        <div class="relative">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-input pr-12" 
                                   placeholder="Digite sua senha"
                                   required>
                            <button type="button" 
                                    onclick="togglePassword()" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <i id="passwordIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Entrar
                    </button>
                </form>

                <!-- Demo Credentials -->
                <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                    <h4 class="font-bold text-senai-blue mb-2">
                        <i class="fas fa-info-circle mr-2"></i>Credenciais de Demonstração
                    </h4>
                    <div class="text-sm text-gray-700 space-y-1">
                        <p><strong>Professor:</strong></p>
                        <p>E-mail: prof@senai.com</p>
                        <p>Senha: senha123</p>
                    </div>
                    <button onclick="fillDemoCredentials()" class="btn btn-secondary mt-3 text-sm">
                        <i class="fas fa-magic mr-2"></i>Preencher Automaticamente
                    </button>
                </div>
            </div>

            <!-- Additional Links -->
            <div class="text-center">
                <a href="index.php" class="text-senai-blue hover:underline">
                    <i class="fas fa-arrow-left mr-2"></i>Voltar ao Início
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> SENAI Automotivo. Todos os direitos reservados.</p>
                <p class="mt-2 text-sm opacity-75">
                    Sistema desenvolvido para fins educacionais
                </p>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
        
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }
        
        function fillDemoCredentials() {
            document.getElementById('username').value = 'prof@senai.com';
            document.getElementById('password').value = 'senha123';
        }
        
        // Auto-focus no primeiro campo
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
        });
    </script>
</body>
</html>

