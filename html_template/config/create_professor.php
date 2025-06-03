<?php
// Script para criar um usuário professor padrão no banco de dados
require_once __DIR__ . '/../models/User.php';

// Credenciais padrão do professor
$nome = 'Professor Admin';
$email = 'professor@senai.com';
$senha = 'senai123';
$tipo = 'professor';

// Criar instância do usuário
$professor = new User($nome, $email, $senha, $tipo);

// Verificar se o usuário já existe
$existingUser = $professor->findByEmail($email);

if ($existingUser) {
    echo "Usuário professor já existe no sistema.\n";
} else {
    // Criar o usuário no banco de dados
    if ($professor->create()) {
        echo "Usuário professor criado com sucesso!\n";
    } else {
        echo "Erro ao criar usuário professor.\n";
    }
}
