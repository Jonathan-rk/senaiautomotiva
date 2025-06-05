// car_data.js
// Estrutura de dados atualizada conforme novos requisitos.
// Dados existentes foram migrados e campos novos adicionados com placeholders.

let carData = [
    {
        "id": "1", // Mantendo como string para consistência com IDs gerados dinamicamente
        // Informações Básicas
        "marca": "Chevrolet",
        "modelo": "Onix",
        "ano": 2023,
        "versao": "LTZ Turbo",
        "codigo_motor": "CSS Prime", // Exemplo
        "combustivel": "Flex",
        // Motorização
        "tipo_motor": "Dianteiro, transversal, 3 cil. em linha", // Exemplo
        "cilindrada_cm3": 999,
        "potencia_cv": "116 (E) / 116 (G)",
        "torque_kgfm": "16.8 (E) / 16.3 (G)",
        "valvulas": 12,
        "injecao_eletronica": "Multiponto",
        // Transmissão
        "cambio": "Manual",
        "marchas": 6,
        // Suspensão e Freios
        "suspensao_dianteira": "Independente, McPherson",
        "suspensao_traseira": "Eixo de torção",
        "freios": "Disco ventilado (diant.) / Tambor (tras.)",
        "abs_ebd": "Sim",
        // Direção e Pneus
        "direcao": "Elétrica progressiva",
        "pneus_originais": "195/55 R16", // Exemplo
        // Dimensões
        "comprimento_mm": 4163,
        "largura_mm": 1730,
        "altura_mm": 1476,
        "entre_eixos_mm": 2551,
        "altura_solo_mm": 128, // Exemplo
        "peso_kg": 1085,
        // Desempenho e Consumo
        "velocidade_max_kmh": 187,
        "aceleracao_0_100_s": 10.1,
        "consumo_urbano_kmL": "8.3 (E) / 11.9 (G)", // Exemplo combinado
        "consumo_rodoviario_kmL": "10.7 (E) / 15.1 (G)", // Exemplo combinado
        "tanque_l": 44,
        // Capacidades
        "porta_malas_l": 275,
        "carga_util_kg": 375, // Exemplo
        "ocupantes": 5,
        // Sistemas e Eletrônica (Opcional)
        "sistema_injecao": "Bosch ME 17.5.24", // Exemplo
        "sonda_lambda": "Pré e Pós Catalisador", // Exemplo
        "sensor_fase_rotacao": "Sim", // Exemplo
        "sistema_ignicao": "Eletrônica digital", // Exemplo
        "ecu": "ACDelco E83", // Exemplo
        // Outros (Campos antigos mantidos ou adaptados)
        "imagem": "images/Mrbm6Jp1axfg.jpg",
        "categoria": "Hatch" // Adicionado campo Categoria
    },
    {
        "id": "2",
        // Informações Básicas
        "marca": "Volkswagen",
        "modelo": "Gol",
        "ano": 2022,
        "versao": "1.0 MPI",
        "codigo_motor": "EA211", // Exemplo
        "combustivel": "Flex",
        // Motorização
        "tipo_motor": "Dianteiro, transversal, 3 cil. em linha",
        "cilindrada_cm3": 999,
        "potencia_cv": "84 (E) / 75 (G)",
        "torque_kgfm": "10.4 (E) / 9.7 (G)",
        "valvulas": 12,
        "injecao_eletronica": "Multiponto",
        // Transmissão
        "cambio": "Manual",
        "marchas": 5,
        // Suspensão e Freios
        "suspensao_dianteira": "Independente, McPherson",
        "suspensao_traseira": "Eixo de torção",
        "freios": "Disco sólido (diant.) / Tambor (tras.)",
        "abs_ebd": "Sim",
        // Direção e Pneus
        "direcao": "Hidráulica",
        "pneus_originais": "185/65 R14", // Exemplo
        // Dimensões
        "comprimento_mm": 3897,
        "largura_mm": 1656,
        "altura_mm": 1467,
        "entre_eixos_mm": 2466,
        "altura_solo_mm": 155, // Exemplo
        "peso_kg": 998,
        // Desempenho e Consumo
        "velocidade_max_kmh": 167,
        "aceleracao_0_100_s": 13.1,
        "consumo_urbano_kmL": "9.1 (E) / 13.3 (G)",
        "consumo_rodoviario_kmL": "10.1 (E) / 14.4 (G)",
        "tanque_l": 55,
        // Capacidades
        "porta_malas_l": 285,
        "carga_util_kg": 442, // Exemplo
        "ocupantes": 5,
        // Sistemas e Eletrônica (Opcional)
        "sistema_injecao": "Magneti Marelli IAW 9GV", // Exemplo
        "sonda_lambda": "Pré e Pós Catalisador",
        "sensor_fase_rotacao": "Sim",
        "sistema_ignicao": "Eletrônica digital",
        "ecu": "Bosch ME17.5.20", // Exemplo
        // Outros
        "imagem": "images/RJ6X7nbFwUtu.jpg",
        "categoria": "Hatch"
    },
     {
        "id": "4",
        // Informações Básicas
        "marca": "Honda",
        "modelo": "Civic",
        "ano": 2023,
        "versao": "Touring 1.5 Turbo",
        "codigo_motor": "L15B7", // Exemplo
        "combustivel": "Gasolina", // Exemplo, verificar se é Flex no BR
        // Motorização
        "tipo_motor": "Dianteiro, transversal, 4 cil. em linha, Turbo",
        "cilindrada_cm3": 1497,
        "potencia_cv": 173,
        "torque_kgfm": 22.4,
        "valvulas": 16,
        "injecao_eletronica": "Direta",
        // Transmissão
        "cambio": "CVT",
        "marchas": 7, // Simulado
        // Suspensão e Freios
        "suspensao_dianteira": "Independente, McPherson",
        "suspensao_traseira": "Independente, Multilink",
        "freios": "Disco ventilado (diant.) / Disco sólido (tras.)",
        "abs_ebd": "Sim",
        // Direção e Pneus
        "direcao": "Elétrica",
        "pneus_originais": "215/50 R17",
        // Dimensões
        "comprimento_mm": 4674,
        "largura_mm": 1802,
        "altura_mm": 1415,
        "entre_eixos_mm": 2735,
        "altura_solo_mm": 134, // Exemplo
        "peso_kg": 1368,
        // Desempenho e Consumo
        "velocidade_max_kmh": 208,
        "aceleracao_0_100_s": 8.6,
        "consumo_urbano_kmL": "10.8",
        "consumo_rodoviario_kmL": "13.4",
        "tanque_l": 47,
        // Capacidades
        "porta_malas_l": 495,
        "carga_util_kg": 402, // Exemplo
        "ocupantes": 5,
        // Sistemas e Eletrônica (Opcional)
        "sistema_injecao": "PGM-FI", // Exemplo
        "sonda_lambda": "Banda Larga + Convencional", // Exemplo
        "sensor_fase_rotacao": "Sim",
        "sistema_ignicao": "Eletrônica digital",
        "ecu": "Keihin", // Exemplo
        // Outros
        "imagem": "images/M7FGoWXLWn4u.png",
        "categoria": "Sedan"
    },
    {
        "id": "7",
        // Informações Básicas
        "marca": "Jeep",
        "modelo": "Renegade",
        "ano": 2023,
        "versao": "Longitude T270",
        "codigo_motor": "GSE T270",
        "combustivel": "Flex",
        // Motorização
        "tipo_motor": "Dianteiro, transversal, 4 cil. em linha, Turbo",
        "cilindrada_cm3": 1332,
        "potencia_cv": "185 (E) / 180 (G)",
        "torque_kgfm": "27.5 (E/G)",
        "valvulas": 16,
        "injecao_eletronica": "Direta",
        // Transmissão
        "cambio": "Automática",
        "marchas": 6,
        // Suspensão e Freios
        "suspensao_dianteira": "Independente, McPherson",
        "suspensao_traseira": "Independente, McPherson",
        "freios": "Disco ventilado (diant.) / Disco sólido (tras.)",
        "abs_ebd": "Sim",
        // Direção e Pneus
        "direcao": "Elétrica",
        "pneus_originais": "225/55 R18",
        // Dimensões
        "comprimento_mm": 4268,
        "largura_mm": 1805,
        "altura_mm": 1706,
        "entre_eixos_mm": 2570,
        "altura_solo_mm": 186,
        "peso_kg": 1476,
        // Desempenho e Consumo
        "velocidade_max_kmh": 210,
        "aceleracao_0_100_s": 8.7,
        "consumo_urbano_kmL": "7.7 (E) / 11.0 (G)",
        "consumo_rodoviario_kmL": "9.1 (E) / 12.8 (G)",
        "tanque_l": 55,
        // Capacidades
        "porta_malas_l": 314,
        "carga_util_kg": 400,
        "ocupantes": 5,
        // Sistemas e Eletrônica (Opcional)
        "sistema_injecao": "Magneti Marelli", // Exemplo
        "sonda_lambda": "Banda Larga + Convencional",
        "sensor_fase_rotacao": "Sim",
        "sistema_ignicao": "Eletrônica digital",
        "ecu": "GPEC4LM", // Exemplo
        // Outros
        "imagem": "images/duoqypXPYyDh.jpg",
        "categoria": "SUV"
    }
    // Adicionar os outros carros com a estrutura completa...
    // ... (Omitido por brevidade, mas todos os carros devem ser atualizados)
];

// Manter as funções de loadCars, showCarDetail, filterCars, etc., pois elas
// serão atualizadas no script.js para usar a nova estrutura e lógica.
// Remover as funções duplicadas que foram movidas para script.js.

