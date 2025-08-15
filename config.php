<?php
// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'senai_automotivo');

// Configurações gerais
define('SITE_NAME', 'SENAI Automotivo');
define('SITE_URL', 'http://localhost:8000');
define('UPLOAD_DIR', __DIR__ . '/uploads');

// Função para conexão com o banco de dados usando PDO
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Erro na conexão com o banco de dados: " . $e->getMessage());
    }
}

// Função para verificar se usuário está logado
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Função para verificar se é professor
function isProfessor() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'professor';
}

// Função para requerer login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Função para requerer permissão de professor
function requireProfessor() {
    if (!isProfessor()) {
        header('Location: index.php');
        exit;
    }
}

// Função para sanitizar inputs
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Função para gerar URL completa
function siteUrl($path = '') {
    return SITE_URL . '/' . ltrim($path, '/');
}

// Função para mostrar mensagens de erro/sucesso
function showMessage($message, $type = 'info') {
    $_SESSION['message'] = [
        'text' => $message,
        'type' => $type
    ];
}

// Função para obter mensagem
function getMessage() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
        return $message;
    }
    return null;
}

// Criar diretório de uploads se não existir
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}