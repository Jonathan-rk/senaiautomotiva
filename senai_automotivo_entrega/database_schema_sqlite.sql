-- Tabela para armazenar informações dos professores (SQLite)
CREATE TABLE IF NOT EXISTS professores (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    usuario TEXT NOT NULL UNIQUE,
    senha_hash TEXT NOT NULL, -- Armazenar hash da senha
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabela principal para armazenar as fichas técnicas dos veículos (SQLite)
CREATE TABLE IF NOT EXISTS fichas_tecnicas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    professor_id INTEGER, -- Chave estrangeira para relacionar com o professor que cadastrou
    
    -- Informações Básicas
    marca TEXT NOT NULL,
    modelo TEXT NOT NULL,
    ano_fabricacao INTEGER,
    ano_modelo INTEGER,
    versao TEXT,
    codigo_motor TEXT,
    tipo_combustivel TEXT, -- Ex: Flex, Gasolina, Diesel, Elétrico, Híbrido
    imagem_url TEXT, -- URL da imagem principal da ficha
    descricao TEXT, -- Descrição geral como vista no frontend
    preco REAL, -- Preço como visto no frontend (pode ser útil)
    
    -- Motorização
    tipo_motor TEXT,
    cilindrada REAL, -- Ex: 1.0, 1.6, 2.0
    potencia_maxima INTEGER, -- Em cv
    torque_maximo REAL, -- Em kgfm
    numero_valvulas INTEGER,
    tipo_injecao TEXT,
    
    -- Transmissão
    tipo_cambio TEXT, -- Ex: Manual, Automática, CVT, Automatizada
    numero_marchas INTEGER,
    
    -- Suspensão e Freios
    suspensao_dianteira TEXT,
    suspensao_traseira TEXT,
    freios_dianteiros TEXT,
    freios_traseiros TEXT,
    possui_abs INTEGER DEFAULT 0, -- 0 for false, 1 for true
    possui_ebd INTEGER DEFAULT 0, -- 0 for false, 1 for true
    
    -- Direção e Pneus
    tipo_direcao TEXT, -- Ex: Hidráulica, Elétrica, Eletro-hidráulica
    pneus_originais TEXT,
    
    -- Dimensões
    comprimento_mm INTEGER,
    largura_mm INTEGER,
    altura_mm INTEGER,
    entre_eixos_mm INTEGER,
    altura_solo_mm INTEGER,
    peso_kg INTEGER,
    
    -- Desempenho e Consumo
    velocidade_maxima_kmh INTEGER,
    aceleracao_0_100_s REAL,
    consumo_urbano_km_l REAL,
    consumo_rodoviario_km_l REAL,
    capacidade_tanque_l INTEGER,
    
    -- Capacidades
    porta_malas_l INTEGER,
    carga_util_kg INTEGER,
    numero_ocupantes INTEGER,
    
    -- Sistemas e Eletrônica (Opcional)
    sistema_injecao_detalhes TEXT,
    sonda_lambda_detalhes TEXT,
    ecu_detalhes TEXT,
    sensores_detalhes TEXT,
    outros_sistemas_eletronicos TEXT,

    -- Metadados
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP, -- Removed ON UPDATE
    
    FOREIGN KEY (professor_id) REFERENCES professores(id) ON DELETE SET NULL -- Define o que acontece se o professor for excluído
);

-- Trigger para atualizar data_atualizacao (SQLite)
CREATE TRIGGER IF NOT EXISTS update_fichas_tecnicas_data_atualizacao
AFTER UPDATE ON fichas_tecnicas
FOR EACH ROW
BEGIN
    UPDATE fichas_tecnicas SET data_atualizacao = CURRENT_TIMESTAMP WHERE id = OLD.id;
END;


