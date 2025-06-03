<?php

require_once __DIR__ . '/../config/database.php';

class User {
    public $id;
    public $nome;
    public $email;
    public $senha_hash;
    public $tipo;
    public $created_at;
    
    private $db;
    
    public function __construct($nome = null, $email = null, $senha = null, $tipo = 'professor') {
        $this->db = DatabaseConnection::getInstance();
        
        $this->nome = $nome;
        $this->email = $email;
        if ($senha !== null) {
            $this->senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        }
        $this->tipo = $tipo;
    }
    
    public function authenticate($email, $senha) {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($senha, $user->senha_hash)) {
            return $user;
        }
        
        return false;
    }
    
    public function isTeacher() {
        return $this->tipo === 'professor';
    }
    
    public function create() {
        $data = [
            'nome' => $this->nome,
            'email' => $this->email,
            'senha_hash' => $this->senha_hash,
            'tipo' => $this->tipo
        ];
        
        $id = $this->db->insert('usuarios', $data);
        
        if ($id) {
            $this->id = $id;
            return true;
        }
        
        return false;
    }
    
    public function findByEmail($email) {
        $result = $this->db->fetchOne("SELECT * FROM usuarios WHERE email = ?", [$email]);
        
        if ($result) {
            $user = new self();
            $user->id = $result['id'];
            $user->nome = $result['nome'];
            $user->email = $result['email'];
            $user->senha_hash = $result['senha_hash'];
            $user->tipo = $result['tipo'];
            $user->created_at = $result['created_at'];
            
            return $user;
        }
        
        return null;
    }
    
    public function findById($id) {
        $result = $this->db->fetchOne("SELECT * FROM usuarios WHERE id = ?", [$id]);
        
        if ($result) {
            $user = new self();
            $user->id = $result['id'];
            $user->nome = $result['nome'];
            $user->email = $result['email'];
            $user->senha_hash = $result['senha_hash'];
            $user->tipo = $result['tipo'];
            $user->created_at = $result['created_at'];
            
            return $user;
        }
        
        return null;
    }
    
    public function getAll() {
        $results = $this->db->fetch("SELECT * FROM usuarios ORDER BY nome");
        $users = [];
        
        foreach ($results as $result) {
            $user = new self();
            $user->id = $result['id'];
            $user->nome = $result['nome'];
            $user->email = $result['email'];
            $user->senha_hash = $result['senha_hash'];
            $user->tipo = $result['tipo'];
            $user->created_at = $result['created_at'];
            
            $users[] = $user;
        }
        
        return $users;
    }
}
