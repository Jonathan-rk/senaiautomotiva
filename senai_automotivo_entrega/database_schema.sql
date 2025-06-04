-- Tabela para armazenar informações dos professores
CREATE TABLE professores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    usuario VARCHAR(100) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL, -- Armazenar hash da senha
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela principal para armazenar as fichas técnicas dos veículos
CREATE TABLE fichas_tecnicas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    professor_id INT, -- Chave estrangeira para relacionar com o professor que cadastrou
    
    -- Informações Básicas
    marca VARCHAR(100) NOT NULL,
    modelo VARCHAR(100) NOT NULL,
    ano_fabricacao INT,
    ano_modelo INT,
    versao VARCHAR(100),
    codigo_motor VARCHAR(100),
    tipo_combustivel VARCHAR(50), -- Ex: Flex, Gasolina, Diesel, Elétrico, Híbrido
    imagem_url VARCHAR(512), -- URL da imagem principal da ficha
    descricao TEXT, -- Descrição geral como vista no frontend
    preco DECIMAL(10, 2), -- Preço como visto no frontend (pode ser útil)
    
    -- Motorização
    tipo_motor VARCHAR(100),
    cilindrada DECIMAL(5,1), -- Ex: 1.0, 1.6, 2.0
    potencia_maxima INT, -- Em cv
    torque_maximo DECIMAL(5,1), -- Em kgfm
    numero_valvulas INT,
    tipo_injecao VARCHAR(100),
    
    -- Transmissão
    tipo_cambio VARCHAR(100), -- Ex: Manual, Automática, CVT, Automatizada
    numero_marchas INT,
    
    -- Suspensão e Freios
    suspensao_dianteira VARCHAR(255),
    suspensao_traseira VARCHAR(255),
    freios_dianteiros VARCHAR(100),
    freios_traseiros VARCHAR(100),
    possui_abs BOOLEAN DEFAULT FALSE,
    possui_ebd BOOLEAN DEFAULT FALSE,
    
    -- Direção e Pneus
    tipo_direcao VARCHAR(100), -- Ex: Hidráulica, Elétrica, Eletro-hidráulica
    pneus_originais VARCHAR(100),
    
    -- Dimensões
    comprimento_mm INT,
    largura_mm INT,
    altura_mm INT,
    entre_eixos_mm INT,
    altura_solo_mm INT,
    peso_kg INT,
    
    -- Desempenho e Consumo
    velocidade_maxima_kmh INT,
    aceleracao_0_100_s DECIMAL(4,1),
    consumo_urbano_km_l DECIMAL(4,1),
    consumo_rodoviario_km_l DECIMAL(4,1),
    capacidade_tanque_l INT,
    
    -- Capacidades
    porta_malas_l INT,
    carga_util_kg INT,
    numero_ocupantes INT,
    
    -- Sistemas e Eletrônica (Opcional)
    sistema_injecao_detalhes TEXT,
    sonda_lambda_detalhes TEXT,
    ecu_detalhes TEXT,
    sensores_detalhes TEXT,
    outros_sistemas_eletronicos TEXT,

    -- Metadados
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (professor_id) REFERENCES professores(id) ON DELETE SET NULL -- Define o que acontece se o professor for excluído
);

-- Tabela opcional para múltiplas imagens (se necessário no futuro)
-- CREATE TABLE imagens_fichas (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     ficha_tecnica_id INT NOT NULL,
--     url_imagem VARCHAR(512) NOT NULL,
--     descricao_imagem VARCHAR(255),
--     FOREIGN KEY (ficha_tecnica_id) REFERENCES fichas_tecnicas(id) ON DELETE CASCADE
-- );

-- Inserir um professor padrão para testes (a senha deve ser hashada na aplicação)
-- Exemplo: senha 'senha123' hashada com bcrypt
-- INSERT INTO professores (nome, usuario, senha_hash) VALUES ('Professor SENAI', 'prof@senai.com', '$2b$10$...'); 

