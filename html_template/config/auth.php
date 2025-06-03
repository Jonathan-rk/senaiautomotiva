<?php

// Authentication related functions
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_professor() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'professor';
}

function is_aluno() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'aluno';
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: /index.php');
        exit;
    }
}

function require_professor() {
    require_login();
    if (!is_professor()) {
        header('Location: /views/aluno/dashboard.php');
        exit;
    }
}

function require_aluno() {
    require_login();
    if (!is_aluno()) {
        header('Location: /views/professor/dashboard.php');
        exit;
    }
}
