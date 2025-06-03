<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../controllers/FichaController.php';
require_once __DIR__ . '/../controllers/AuthController.php';

// Verificar se o usuário está autenticado como professor
require_professor();

// Verificar se o ID da ficha foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: /views/professor/interface.php?error=no_id');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$controller = new FichaController();

// Verificar se a ficha existe
$ficha = $controller->getFicha($id);
if (!$ficha) {
    header('Location: /views/professor/interface.php?error=not_found');
    exit;
}

$authController = new AuthController();
$user = $authController->getCurrentUser();

// Verificar se a ficha pertence ao professor atual
if ($ficha->id_professor != $user->id) {
    header('Location: /views/professor/interface.php?error=permission');
    exit;
}

// Excluir a ficha técnica
$result = $controller->deleteFicha($id);

if ($result) {
    header('Location: /views/professor/interface.php?success=delete');
} else {
    header('Location: /views/professor/interface.php?error=delete');
}
exit;
