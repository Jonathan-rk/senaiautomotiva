<?php
// Configuração do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'senai_automotivo');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configurações do sistema
define('SITE_NAME', 'SENAI Automotivo');
define('SITE_URL', 'http://localhost/senai_automotivo');

// Configurações de sessão
define('SESSION_TIMEOUT', 3600); // 1 hora

// Função para conectar ao banco de dados
function getDBConnection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch(PDOException $e) {
        die("Erro na conexão com o banco de dados: " . $e->getMessage());
    }
}

// Função para verificar se o usuário está logado
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Função para verificar se o usuário é professor
function isProfessor() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'professor';
}

// Função para redirecionar se não estiver logado
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Função para redirecionar se não for professor
function requireProfessor() {
    if (!isProfessor()) {
        header('Location: index.php');
        exit;
    }
}

// Função para sanitizar dados de entrada
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Função para gerar URL amigável
function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

// Função para formatar data brasileira
function formatDateBR($date) {
    return date('d/m/Y', strtotime($date));
}

// Função para formatar data e hora brasileira
function formatDateTimeBR($datetime) {
    return date('d/m/Y H:i', strtotime($datetime));
}
?>

