<?php
session_start();
require_once __DIR__ . '/../controllers/AuthController.php';

$controller = new AuthController();
$controller->logout();

header('Location: /index.php');
exit;