<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../controllers/FichaController.php';

// Verificar se o usuário está autenticado (aluno ou professor)
require_auth();

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'ID não fornecido']);
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$controller = new FichaController();
$ficha = $controller->getFicha($id);

if (!$ficha) {
    header('HTTP/1.1 404 Not Found');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Ficha técnica não encontrada']);
    exit;
}

// Retornar os dados da ficha em formato JSON
header('Content-Type: application/json');
echo json_encode($ficha);
exit;
