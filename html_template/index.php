<?php
session_start();
require_once 'config/database.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/FichaController.php';

// Inicializar o controlador de fichas para exibição pública
$fichaController = new FichaController();
$fichas = $fichaController->getAllFichas();

// Get the requested URL path
$request_uri = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';

// Basic routing
switch ($request_uri) {
    case '/':
    case '':
        // Exibir página inicial com fichas técnicas (acesso público)
        include 'views/public_home.php';
        break;
    
    case '/login':
        include 'views/login.php';
        break;
    
    case '/logout':
        session_destroy();
        header('Location: index.php');
        exit;
        break;
    
    case '/professor':
        $auth = new AuthController();
        if ($auth->isLoggedIn() && $auth->getUserType() === 'professor') {
            header('Location: views/professor/interface.php');
        } else {
            header('Location: index.php/login');
        }
        exit;
        break;
    
    default:
        echo 'Page not found';
        break;
}
