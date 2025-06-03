<?php

class DatabaseConnection {
    private static $instance = null;
    private $connection;
    
    private $host = 'localhost';
    private $dbname = 'senai_fichas';
    private $username = 'root';
    private $password = '';
    
    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname}",
                $this->username,
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $this->connection->exec("SET NAMES utf8");
        } catch (PDOException $e) {
            die("Database connection error: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new DatabaseConnection();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function executeQuery($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function executeInsert($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $result = $stmt->execute($params);
        
        if ($result) {
            return $this->connection->lastInsertId();
        }
        
        return false;
    }
    
    public function executeUpdate($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function fetch($sql, $params = []) {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function fetchOne($sql, $params = []) {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function insert($table, $data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->executeQuery($sql, array_values($data));
        
        return $this->connection->lastInsertId();
    }
    
    public function update($table, $data, $condition) {
        $set = [];
        foreach (array_keys($data) as $column) {
            $set[] = "{$column} = ?";
        }
        $set = implode(", ", $set);
        
        $sql = "UPDATE {$table} SET {$set} WHERE {$condition}";
        $this->executeQuery($sql, array_values($data));
        
        return true;
    }
    
    public function delete($table, $condition) {
        $sql = "DELETE FROM {$table} WHERE {$condition}";
        $this->executeQuery($sql);
        
        return true;
    }
}
