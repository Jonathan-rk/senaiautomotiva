CREATE DATABASE IF NOT EXISTS senai_automotivo;
USE senai_automotivo;

-- Tabela para Montadoras
CREATE TABLE IF NOT EXISTS montadoras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL UNIQUE,
    imagem_path VARCHAR(255) -- Caminho para a imagem da montadora
);

-- Tabela para Carrocerias
CREATE TABLE IF NOT EXISTS carrocerias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL UNIQUE
);

-- Tabela para Fichas Técnicas
CREATE TABLE IF NOT EXISTS fichas_tecnicas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    montadoras_id INT NOT NULL, -- Chave estrangeira para a tabela montadoras
    modelo VARCHAR(255) NOT NULL,
    ano INT NOT NULL,
    lugares VARCHAR(255) NOT NULL,
    portas VARCHAR(255) NOT NULL,
    identificacaomotor VARCHAR(255),
    materialconstrucao VARCHAR(255),
    instalacao VARCHAR(255),
    disposicao VARCHAR(255),
    combustivel VARCHAR(255),
    cilindros VARCHAR(255),
    valvulasporcilindro VARCHAR(255),
    aspiracao VARCHAR(255),
    alimentacao VARCHAR(255),
    potencia VARCHAR(255),
    cilindrada VARCHAR(255),
    torque VARCHAR(255),
    rotacao VARCHAR(255),
    tracao VARCHAR(255),
    cambio VARCHAR(255),
    numero_marchas VARCHAR(255),
    embreagem VARCHAR(255),
    dianteira VARCHAR(255),
    traseira VARCHAR(255),
    dianteirosfreios VARCHAR(255),
    traseirosfreios VARCHAR(255),
    assistencia VARCHAR(255),
    dianteira_pressao_enchimento VARCHAR(255),
    traseira_pressao_enchimento VARCHAR(255),
    dimensao_estepe VARCHAR(255),
    material_rodas VARCHAR(255),
    comprimento VARCHAR(255),
    distancia_eixos VARCHAR(255),
    largura VARCHAR(255),
    altura VARCHAR(255),
    peso_bruto VARCHAR(255),
    porta_malas VARCHAR(255),
    velocidade_maxima VARCHAR(255),
    aceleracao VARCHAR(255),
    capacidade_tanque VARCHAR(255),
    consumo_urbano VARCHAR(255),
    consumo_rodovia VARCHAR(255),
    autonomia_urbana VARCHAR(255),
    autonomia_rodovia VARCHAR(255),
    oleo_motor VARCHAR(255),
    oleo_transmissao VARCHAR(255),
    fluido_freio VARCHAR(255),
    carroceria_id INT, -- Chave estrangeira para a tabela carrocerias
    imagem_path VARCHAR(255), -- Caminho para a imagem do veículo
    FOREIGN KEY (montadoras_id) REFERENCES montadoras(id) ON DELETE CASCADE,
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
