<?php

require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $user;
    
    public function __construct() {
        $this->user = new User();
    }
    
    public function login($email, $senha) {
        $user = $this->user->authenticate($email, $senha);
        
        if ($user) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->nome;
            $_SESSION['user_email'] = $user->email;
            $_SESSION['user_type'] = $user->tipo;
            
            return true;
        }
        
        return false;
    }
    
    public function logout() {
        session_destroy();
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function getUserType() {
        return $_SESSION['user_type'] ?? '';
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: /index.php');
            exit;
        }
    }
    
    public function requireTeacher() {
        $this->requireLogin();
        
        if ($this->getUserType() !== 'professor') {
            header('Location: /index.php');
            exit;
        }
    }
    
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return $this->user->findById($_SESSION['user_id']);
    }
}
