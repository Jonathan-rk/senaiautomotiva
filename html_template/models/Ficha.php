<?php
class Ficha {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Obtém todas as fichas técnicas
     * @return array Lista de fichas técnicas
     */
    public function getAll() {
        $query = "SELECT * FROM fichas ORDER BY id DESC";
        return $this->db->executeQuery($query);
    }
    
    /**
     * Busca fichas técnicas por termo de pesquisa (marca ou modelo)
     * @param string $term Termo de pesquisa
     * @return array Lista de fichas técnicas filtradas
     */
    public function search($term) {
        $term = "%$term%";
        $query = "SELECT * FROM fichas WHERE marca LIKE ? OR modelo LIKE ? ORDER BY id DESC";
        return $this->db->executeQuery($query, [$term, $term]);
    }
    
    /**
     * Obtém fichas técnicas de um professor específico
     * @param int $professorId ID do professor
     * @return array Lista de fichas técnicas do professor
     */
    public function getByProfessor($professorId) {
        $query = "SELECT * FROM fichas WHERE id_professor = ? ORDER BY id DESC";
        return $this->db->executeQuery($query, [$professorId]);
    }
    
    /**
     * Obtém uma ficha técnica específica
     * @param int $id ID da ficha técnica
     * @return object|null Dados da ficha técnica ou null se não encontrada
     */
    public function getById($id) {
        $query = "SELECT * FROM fichas WHERE id = ? LIMIT 1";
        $result = $this->db->executeQuery($query, [$id]);
        
        return $result ? $result[0] : null;
    }
    
    /**
     * Cria uma nova ficha técnica
     * @param array $data Dados da ficha técnica
     * @return bool|int ID da nova ficha ou false em caso de erro
     */
    public function create($data) {
        $query = "INSERT INTO fichas (
            marca, modelo, ano, id_professor, motor, potencia, torque, 
            consumo, cambio, tracao, porta_malas, portas, peso, imagem
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        
        $params = [
            $data['marca'],
            $data['modelo'],
            $data['ano'],
            $data['id_professor'],
            $data['motor'],
            $data['potencia'],
            $data['torque'],
            $data['consumo'],
            $data['cambio'],
            $data['tracao'],
            $data['porta_malas'],
            $data['portas'],
            $data['peso'],
            $data['imagem']
        ];
        
        return $this->db->executeInsert($query, $params);
    }
    
    /**
     * Atualiza uma ficha técnica existente
     * @param int $id ID da ficha técnica
     * @param array $data Novos dados da ficha técnica
     * @return bool Resultado da operação
     */
    public function update($id, $data) {
        $query = "UPDATE fichas SET 
            marca = ?, 
            modelo = ?, 
            ano = ?, 
            id_professor = ?,
            motor = ?,
            potencia = ?,
            torque = ?,
            consumo = ?,
            cambio = ?,
            tracao = ?,
            porta_malas = ?,
            portas = ?,
            peso = ?,
            imagem = ?,
            updated_at = NOW()
            WHERE id = ?
        ";
        
        $params = [
            $data['marca'],
            $data['modelo'],
            $data['ano'],
            $data['id_professor'],
            $data['motor'],
            $data['potencia'],
            $data['torque'],
            $data['consumo'],
            $data['cambio'],
            $data['tracao'],
            $data['porta_malas'],
            $data['portas'],
            $data['peso'],
            $data['imagem'],
            $id
        ];
        
        return $this->db->executeUpdate($query, $params);
    }
    
    /**
     * Exclui uma ficha técnica
     * @param int $id ID da ficha técnica
     * @return bool Resultado da operação
     */
    public function delete($id) {
        $query = "DELETE FROM fichas WHERE id = ?";
        return $this->db->executeUpdate($query, [$id]);
    }
}