<?php
session_start();
require_once __DIR__ . '/../controllers/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);
    
    $authController = new AuthController();
    $login_successful = $authController->login($email, $senha);
    
    if ($login_successful) {
        if ($_SESSION['user_type'] === 'professor') {
            header('Location: /views/professor/interface.php');
            exit;
        } else {
            // Redirecionamento para a página inicial caso não seja professor
            header('Location: /index.php');
            exit;
        }
    } else {
        header('Location: /index.php/login?error=1');
        exit;
    }
} else {
    header('Location: /index.php');
    exit;
}
