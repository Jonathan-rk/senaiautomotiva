<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../controllers/FichaController.php';
require_once __DIR__ . '/../controllers/AuthController.php';

// Verificar se o usuário está autenticado como professor
require_professor();

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

$authController = new AuthController();
$user = $authController->getCurrentUser();

// Verificar se o ID do professor na requisição corresponde ao usuário atual
if (!isset($_POST['id_professor']) || $_POST['id_professor'] != $user->id) {
    header('Location: /views/professor/interface.php?error=permission');
    exit;
}

// Validar campos obrigatórios
if (!isset($_POST['marca']) || empty($_POST['marca']) ||
    !isset($_POST['modelo']) || empty($_POST['modelo']) ||
    !isset($_POST['ano']) || empty($_POST['ano'])) {
    header('Location: /views/professor/nova_ficha.php?error=missing_fields');
    exit;
}

// Validar ano
$ano = filter_input(INPUT_POST, 'ano', FILTER_VALIDATE_INT);
if (!$ano || $ano < 1900 || $ano > 2099) {
    header('Location: /views/professor/nova_ficha.php?error=invalid_year');
    exit;
}

// Criar a ficha técnica
$controller = new FichaController();
$result = $controller->createFicha($_POST);

if ($result) {
    header('Location: /views/professor/interface.php?success=create');
} else {
    header('Location: /views/professor/nova_ficha.php?error=database');
}
exit;
