CREATE DATABASE IF NOT EXISTS senai_automotivo;
USE senai_automotivo;

-- Tabela para Marcas (antiga categorias)
CREATE TABLE IF NOT EXISTS marcas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL UNIQUE,
    imagem_path VARCHAR(255) -- Caminho para a imagem da marca
);

-- Tabela para Carrocerias
CREATE TABLE IF NOT EXISTS carrocerias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL UNIQUE
);

-- Tabela para Fichas Técnicas
CREATE TABLE IF NOT EXISTS fichas_tecnicas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marca_id INT NOT NULL, -- Chave estrangeira para a tabela marcas
    modelo VARCHAR(255) NOT NULL,
    ano INT NOT NULL,
    versao VARCHAR(255),
    codigo_motor VARCHAR(255),
    tipo_combustivel VARCHAR(255),
    tipo_motor VARCHAR(255),
    cilindrada VARCHAR(255),
    potencia_maxima VARCHAR(255),
    torque_maximo VARCHAR(255),
    numero_valvulas VARCHAR(255),
    injecao_eletronica VARCHAR(255),
    tipo_cambio VARCHAR(255),
    numero_marchas VARCHAR(255),
    suspensoes VARCHAR(255),
    freios VARCHAR(255),
    abs_ebd VARCHAR(255),
    tipo_direcao VARCHAR(255),
    pneus_originais VARCHAR(255),
    comprimento VARCHAR(255),
    largura VARCHAR(255),
    altura VARCHAR(255),
    entre_eixos VARCHAR(255),
    altura_livre_solo VARCHAR(255),
    peso VARCHAR(255),
    velocidade_maxima VARCHAR(255),
    aceleracao VARCHAR(255),
    consumo_urbano VARCHAR(255),
    consumo_rodoviario VARCHAR(255),
    tanque VARCHAR(255),
    porta_malas VARCHAR(255),
    carga_util VARCHAR(255),
    ocupantes VARCHAR(255),
    sistema_injecao VARCHAR(255),
    sonda_lambda VARCHAR(255),
    sensor_fase VARCHAR(255),
    sistema_ignicao VARCHAR(255),
    tipo_ecu VARCHAR(255),
    carroceria_id INT, -- Chave estrangeira para a tabela carrocerias
    imagem_path VARCHAR(255), -- Caminho para a imagem do veículo
    FOREIGN KEY (marca_id) REFERENCES marcas(id) ON DELETE CASCADE,
    FOREIGN KEY (carroceria_id) REFERENCES carrocerias(id) ON DELETE SET NULL
);

-- Tabela para Usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL
);

-- Inserir usuários de demonstração (se não existirem)
INSERT IGNORE INTO usuarios (username, password, role) VALUES (
    'prof@senai.com', 
    'senha123', -- Em um ambiente real, use password_hash() para armazenar senhas com segurança
    'professor'
);
