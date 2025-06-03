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

// Verificar se o ID da ficha foi fornecido
if (!isset($_POST['id']) || empty($_POST['id'])) {
    header('Location: /views/professor/interface.php?error=no_id');
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
    header('Location: /views/professor/editar_ficha.php?id=' . $_POST['id'] . '&error=missing_fields');
    exit;
}

// Validar ano
$ano = filter_input(INPUT_POST, 'ano', FILTER_VALIDATE_INT);
if (!$ano || $ano < 1900 || $ano > 2099) {
    header('Location: /views/professor/editar_ficha.php?id=' . $_POST['id'] . '&error=invalid_year');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$controller = new FichaController();

// Verificar se a ficha existe e pertence ao professor atual
$ficha = $controller->getFicha($id);
if (!$ficha || $ficha->id_professor != $user->id) {
    header('Location: /views/professor/interface.php?error=not_found');
    exit;
}

// Atualizar a ficha técnica
$result = $controller->updateFicha($id, $_POST);

if ($result) {
    header('Location: /views/professor/interface.php?success=update');
} else {
    header('Location: /views/professor/editar_ficha.php?id=' . $id . '&error=database');
}
exit;
