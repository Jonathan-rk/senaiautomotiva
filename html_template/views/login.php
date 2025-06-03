<?php
session_start();
require_once __DIR__ . '/../controllers/AuthController.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENAI Automotivo - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <style>
        :root {
            --senai-blue: #0A3871;
            --senai-red: #E31E24;
        }
        
        .senai-blue { color: var(--senai-blue); }
        .bg-senai-blue { background-color: var(--senai-blue); }
        .senai-red { color: var(--senai-red); }
        .bg-senai-red { background-color: var(--senai-red); }
    </style>
</head>
<body class="bg-gray-50">
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
            
            <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <p>Email ou senha inválidos. Tente novamente.</p>
            </div>
            <?php endif; ?>
            
            <form class="mt-8 space-y-6" action="/api/login.php" method="post">
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" name="email" type="email" required 
                               class="mt-1 relative block w-full px-3 py-3 border border-gray-300 rounded-lg placeholder-gray-500 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Digite seu email">
                    </div>
                    <div>
                        <label for="senha" class="block text-sm font-medium text-gray-700">Senha</label>
                        <input id="senha" name="senha" type="password" required 
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
                
                <!-- Credenciais do Professor -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <h4 class="text-sm font-medium text-blue-800 mb-2">Credenciais do Professor:</h4>
                    <div class="text-xs text-blue-700">
                        <p><strong>Email:</strong> professor@senai.com</p>
                        <p><strong>Senha:</strong> senai123</p>
                    </div>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <a href="/index.php" class="text-sm text-blue-600 hover:text-blue-800">
                    <i class="fas fa-arrow-left mr-1"></i> Voltar para página inicial
                </a>
            </div>
        </div>
    </div>
</body>
</html>
