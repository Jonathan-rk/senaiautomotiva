-- Dados de exemplo para demonstração do sistema
-- Execute este script após criar as tabelas principais

USE senai_automotivo;

-- Inserir categorias de exemplo
INSERT IGNORE INTO categorias (nome) VALUES 
('Sedans'),
('Hatchbacks'),
('SUVs'),
('Pickups'),
('Esportivos'),
('Compactos');

-- Inserir fichas técnicas de exemplo
INSERT IGNORE INTO fichas_tecnicas (
    marca, modelo, ano, versao, codigo_motor, tipo_combustivel, tipo_motor, cilindrada,
    potencia_maxima, torque_maximo, numero_valvulas, injecao_eletronica, tipo_cambio,
    numero_marchas, suspensoes, freios, abs_ebd, tipo_direcao, pneus_originais,
    comprimento, largura, altura, entre_eixos, altura_livre_solo, peso, velocidade_maxima,
    aceleracao, consumo_urbano, consumo_rodoviario, tanque, porta_malas, carga_util,
    ocupantes, categoria_id
) VALUES 
(
    'Chevrolet', 'Onix', 2023, 'LT 1.0 Turbo', 'LIV', 'Flex', '1.0 Turbo', '999 cm³',
    '116 cv', '16,8 kgfm', '12', 'Multiponto', 'Manual', '6',
    'Dianteira independente McPherson, Traseira semi-independente', 'Dianteiros a disco, Traseiros a tambor', 'ABS + EBD',
    'Elétrica', '185/65 R15', '4.163 mm', '1.731 mm', '1.474 mm', '2.551 mm', '150 mm',
    '1.050 kg', '185 km/h', '10,2 s', '13,7 km/l', '17,1 km/l', '44 litros', '275 litros',
    '450 kg', '5', 2
),
(
    'Volkswagen', 'Golf GTI', 2023, '2.0 TSI', 'EA888', 'Gasolina', '2.0 TSI Turbo', '1984 cm³',
    '230 cv', '35,7 kgfm', '16', 'Direta', 'Automático DSG', '7',
    'Dianteira independente McPherson, Traseira multilink', 'Dianteiros e traseiros a disco ventilados', 'ABS + EBD + ESP',
    'Elétrica', '225/40 R18', '4.258 mm', '1.799 mm', '1.452 mm', '2.636 mm', '140 mm',
    '1.420 kg', '250 km/h', '6,2 s', '9,8 km/l', '12,3 km/l', '50 litros', '380 litros',
    '500 kg', '5', 5
),
(
    'Honda', 'Civic', 2023, 'EXL 2.0', 'R20A3', 'Flex', '2.0 i-VTEC', '1997 cm³',
    '155 cv', '19,4 kgfm', '16', 'Multiponto', 'CVT', 'Contínuo',
    'Dianteira independente McPherson, Traseira multilink', 'Dianteiros a disco ventilados, Traseiros a disco sólidos', 'ABS + EBD + VSA',
    'Elétrica', '215/55 R16', '4.678 mm', '1.802 mm', '1.415 mm', '2.735 mm', '134 mm',
    '1.350 kg', '200 km/h', '9,8 s', '12,1 km/l', '16,8 km/l', '47 litros', '519 litros',
    '520 kg', '5', 1
),
(
    'Toyota', 'Corolla', 2023, 'XEI 2.0', '3ZR-FAE', 'Flex', '2.0 Dual VVT-i', '1987 cm³',
    '177 cv', '21,4 kgfm', '16', 'Multiponto', 'CVT', 'Contínuo',
    'Dianteira independente McPherson, Traseira semi-independente', 'Dianteiros a disco ventilados, Traseiros a disco sólidos', 'ABS + EBD + VSC',
    'Elétrica', '205/55 R16', '4.630 mm', '1.780 mm', '1.435 mm', '2.700 mm', '139 mm',
    '1.395 kg', '195 km/h', '9,2 s', '11,2 km/l', '15,1 km/l', '50 litros', '470 litros',
    '500 kg', '5', 1
),
(
    'Jeep', 'Compass', 2023, 'Limited 2.0', 'Tigershark', 'Flex', '2.0 Tigershark', '1995 cm³',
    '166 cv', '20,6 kgfm', '16', 'Multiponto', 'Automático', '9',
    'Dianteira independente McPherson, Traseira multilink', 'Dianteiros e traseiros a disco ventilados', 'ABS + EBD + ESC',
    'Elétrica', '225/55 R18', '4.394 mm', '1.818 mm', '1.640 mm', '2.636 mm', '200 mm',
    '1.515 kg', '190 km/h', '10,5 s', '9,8 km/l', '12,5 km/l', '60 litros', '438 litros',
    '600 kg', '5', 3
),
(
    'Ford', 'Ka', 2023, 'SE 1.0', 'Dragon', 'Flex', '1.0 Dragon', '999 cm³',
    '82 cv', '10,8 kgfm', '12', 'Multiponto', 'Manual', '5',
    'Dianteira independente McPherson, Traseira semi-independente', 'Dianteiros a disco, Traseiros a tambor', 'ABS',
    'Hidráulica', '175/65 R14', '3.859 mm', '1.694 mm', '1.525 mm', '2.491 mm', '168 mm',
    '1.015 kg', '165 km/h', '13,8 s', '14,2 km/l', '16,8 km/l', '40 litros', '225 litros',
    '400 kg', '5', 6
);

-- Inserir dados adicionais para algumas fichas (sistemas eletrônicos)
UPDATE fichas_tecnicas SET 
    sistema_injecao = 'Bosch ME17.9.21',
    sonda_lambda = 'Sim',
    sensor_fase = 'Sim',
    sistema_ignicao = 'Eletrônica mapeada',
    tipo_ecu = 'Bosch ME17.9.21'
WHERE marca = 'Chevrolet' AND modelo = 'Onix';

UPDATE fichas_tecnicas SET 
    sistema_injecao = 'Continental Simos18.1',
    sonda_lambda = 'Sim',
    sensor_fase = 'Sim',
    sistema_ignicao = 'Eletrônica mapeada',
    tipo_ecu = 'Continental Simos18.1'
WHERE marca = 'Volkswagen' AND modelo = 'Golf GTI';

UPDATE fichas_tecnicas SET 
    sistema_injecao = 'Honda PGM-FI',
    sonda_lambda = 'Sim',
    sensor_fase = 'Sim',
    sistema_ignicao = 'Eletrônica mapeada',
    tipo_ecu = 'Honda PCM'
WHERE marca = 'Honda' AND modelo = 'Civic';

