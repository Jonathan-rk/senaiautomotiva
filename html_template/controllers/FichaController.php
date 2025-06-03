<?php
require_once __DIR__ . '/../models/Ficha.php';
require_once __DIR__ . '/../config/database.php';

class FichaController {
    private $model;
    private $db;
    
    public function __construct() {
        $this->db = DatabaseConnection::getInstance();
        $this->model = new Ficha($this->db);
    }
    
    /**
     * Obtém todas as fichas técnicas
     * @return array Lista de fichas técnicas
     */
    public function getAllFichas() {
        return $this->model->getAll();
    }
    
    /**
     * Busca fichas técnicas por termo de pesquisa (marca ou modelo)
     * @param string $term Termo de pesquisa
     * @return array Lista de fichas técnicas filtradas
     */
    public function searchFichas($term) {
        return $this->model->search($term);
    }
    
    /**
     * Obtém fichas técnicas de um professor específico
     * @param int $professorId ID do professor
     * @return array Lista de fichas técnicas do professor
     */
    public function getFichasByProfessor($professorId) {
        return $this->model->getByProfessor($professorId);
    }
    
    /**
     * Obtém uma ficha técnica específica
     * @param int $id ID da ficha técnica
     * @return object|null Dados da ficha técnica ou null se não encontrada
     */
    public function getFicha($id) {
        return $this->model->getById($id);
    }
    
    /**
     * Cria uma nova ficha técnica
     * @param array $data Dados da ficha técnica
     * @return bool Resultado da operação
     */
    public function createFicha($data) {
        // Processar upload de imagem se houver
        $imagemPath = null;
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $imagemPath = $this->handleImageUpload($_FILES['imagem']);
            if (!$imagemPath) {
                return false; // Erro no upload
            }
        }
        
        // Preparar dados para o modelo
        $fichaData = [
            'marca' => $data['marca'],
            'modelo' => $data['modelo'],
            'ano' => $data['ano'],
            'id_professor' => $data['id_professor'],
            'motor' => $data['motor'] ?? null,
            'potencia' => $data['potencia'] ?? null,
            'torque' => $data['torque'] ?? null,
            'consumo' => $data['consumo'] ?? null,
            'cambio' => $data['cambio'] ?? null,
            'tracao' => $data['tracao'] ?? null,
            'porta_malas' => $data['porta_malas'] ?? null,
            'portas' => $data['portas'] ?? null,
            'peso' => $data['peso'] ?? null,
            'imagem' => $imagemPath
        ];
        
        return $this->model->create($fichaData);
    }
    
    /**
     * Atualiza uma ficha técnica existente
     * @param int $id ID da ficha técnica
     * @param array $data Novos dados da ficha técnica
     * @return bool Resultado da operação
     */
    public function updateFicha($id, $data) {
        // Verificar se a ficha existe
        $ficha = $this->model->getById($id);
        if (!$ficha) {
            return false;
        }
        
        // Processar upload de imagem se houver
        $imagemPath = $ficha->imagem; // Manter a imagem atual por padrão
        
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            // Upload de nova imagem
            $novaImagem = $this->handleImageUpload($_FILES['imagem']);
            if (!$novaImagem) {
                return false; // Erro no upload
            }
            
            // Excluir imagem antiga se existir
            if (!empty($ficha->imagem) && file_exists(__DIR__ . '/../uploads/' . $ficha->imagem)) {
                unlink(__DIR__ . '/../uploads/' . $ficha->imagem);
            }
            
            $imagemPath = $novaImagem;
        }
        
        // Preparar dados para o modelo
        $fichaData = [
            'marca' => $data['marca'],
            'modelo' => $data['modelo'],
            'ano' => $data['ano'],
            'id_professor' => $data['id_professor'],
            'motor' => $data['motor'] ?? null,
            'potencia' => $data['potencia'] ?? null,
            'torque' => $data['torque'] ?? null,
            'consumo' => $data['consumo'] ?? null,
            'cambio' => $data['cambio'] ?? null,
            'tracao' => $data['tracao'] ?? null,
            'porta_malas' => $data['porta_malas'] ?? null,
            'portas' => $data['portas'] ?? null,
            'peso' => $data['peso'] ?? null,
            'imagem' => $imagemPath
        ];
        
        return $this->model->update($id, $fichaData);
    }
    
    /**
     * Exclui uma ficha técnica
     * @param int $id ID da ficha técnica
     * @return bool Resultado da operação
     */
    public function deleteFicha($id) {
        // Verificar se a ficha existe e obter dados da imagem
        $ficha = $this->model->getById($id);
        if (!$ficha) {
            return false;
        }
        
        // Excluir imagem associada, se houver
        if (!empty($ficha->imagem) && file_exists(__DIR__ . '/../uploads/' . $ficha->imagem)) {
            unlink(__DIR__ . '/../uploads/' . $ficha->imagem);
        }
        
        return $this->model->delete($id);
    }
    
    /**
     * Processa o upload de uma imagem
     * @param array $file Dados do arquivo enviado ($_FILES['campo'])
     * @return string|false Nome do arquivo salvo ou false em caso de erro
     */
    private function handleImageUpload($file) {
        // Verificar se o diretório de uploads existe, se não, criar
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Verificar tipo de arquivo
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }
        
        // Verificar tamanho (5MB máximo)
        if ($file['size'] > 5 * 1024 * 1024) {
            return false;
        }
        
        // Gerar nome de arquivo único
        $fileName = uniqid() . '_' . basename($file['name']);
        $targetPath = $uploadDir . $fileName;
        
        // Mover o arquivo para o diretório de uploads
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return false;
        }
        
        return $fileName;
    }
}
