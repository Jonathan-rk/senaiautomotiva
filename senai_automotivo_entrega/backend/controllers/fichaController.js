const db = require("../db"); // Changed from pool to db (SQLite)

// Helper function to map DB columns to frontend expected fields
const formatFichaForFrontend = (f) => ({
    id: f.id,
    marca: f.marca,
    modelo: f.modelo,
    ano: f.ano_modelo || f.ano_fabricacao, // Prioritize ano_modelo
    preco: f.preco,
    motor: f.codigo_motor || f.tipo_motor, // Combine or choose one
    potencia: f.potencia_maxima,
    combustivel: f.tipo_combustivel,
    transmissao: f.tipo_cambio,
    // carroceria: f.carroceria, // Assuming 'carroceria' column exists in schema
    // portas: f.numero_ocupantes, // Approximation, might need dedicated field
    consumo_cidade: f.consumo_urbano_km_l,
    consumo_estrada: f.consumo_rodoviario_km_l,
    imagem: f.imagem_url,
    descricao: f.descricao
    // Add other fields as needed by the frontend card display
});

// Get all fichas (public access, adapted for SQLite)
const getAllFichas = (req, res) => {
  try {
    // Basic query without filters for now
    // TODO: Implement filtering based on query parameters
    let query = "SELECT * FROM fichas_tecnicas ORDER BY data_cadastro DESC";
    
    db.all(query, [], (err, fichas) => {
      if (err) {
        console.error("Erro ao buscar fichas técnicas (SQLite):", err.message);
        return res.status(500).json({ message: "Erro interno ao buscar fichas técnicas." });
      }
      
      // Map DB columns to frontend expected fields
      const formattedFichas = fichas.map(formatFichaForFrontend);
      res.status(200).json(formattedFichas);
    });

  } catch (error) {
    // Catch synchronous errors, though less likely here
    console.error("Erro inesperado em getAllFichas:", error);
    res.status(500).json({ message: "Erro interno inesperado ao buscar fichas técnicas." });
  }
};

// Get a single ficha by ID (public access, adapted for SQLite)
const getFichaById = (req, res) => {
  const { id } = req.params;
  try {
    const query = "SELECT * FROM fichas_tecnicas WHERE id = ?";
    db.get(query, [id], (err, ficha) => {
      if (err) {
        console.error("Erro ao buscar ficha técnica por ID (SQLite):", err.message);
        return res.status(500).json({ message: "Erro interno ao buscar ficha técnica." });
      }
      if (!ficha) {
        return res.status(404).json({ message: "Ficha técnica não encontrada." });
      }
      // TODO: Map all DB fields to the detailed structure expected by the frontend's showCarDetail function
      // For now, sending the raw data fetched
      res.status(200).json(ficha); 
    });
  } catch (error) {
    console.error("Erro inesperado em getFichaById:", error);
    res.status(500).json({ message: "Erro interno inesperado ao buscar ficha técnica." });
  }
};

// Create a new ficha (professor access only, adapted for SQLite)
const createFicha = (req, res) => {
  const professor_id = req.user.id;
  const {
    marca, modelo, ano_fabricacao, ano_modelo, versao, codigo_motor, tipo_combustivel, imagem_url, descricao, preco,
    tipo_motor, cilindrada, potencia_maxima, torque_maximo, numero_valvulas, tipo_injecao,
    tipo_cambio, numero_marchas,
    suspensao_dianteira, suspensao_traseira, freios_dianteiros, freios_traseiros, possui_abs, possui_ebd,
    tipo_direcao, pneus_originais,
    comprimento_mm, largura_mm, altura_mm, entre_eixos_mm, altura_solo_mm, peso_kg,
    velocidade_maxima_kmh, aceleracao_0_100_s, consumo_urbano_km_l, consumo_rodoviario_km_l, capacidade_tanque_l,
    porta_malas_l, carga_util_kg, numero_ocupantes,
    sistema_injecao_detalhes, sonda_lambda_detalhes, ecu_detalhes, sensores_detalhes, outros_sistemas_eletronicos
    // Add carroceria if added to schema
  } = req.body;

  if (!marca || !modelo) {
    return res.status(400).json({ message: "Marca e Modelo são obrigatórios." });
  }

  // Convert boolean values if necessary (SQLite stores them as 0 or 1)
  const possui_abs_int = possui_abs ? 1 : 0;
  const possui_ebd_int = possui_ebd ? 1 : 0;

  try {
    const query = `
      INSERT INTO fichas_tecnicas (
        professor_id, marca, modelo, ano_fabricacao, ano_modelo, versao, codigo_motor, tipo_combustivel, imagem_url, descricao, preco,
        tipo_motor, cilindrada, potencia_maxima, torque_maximo, numero_valvulas, tipo_injecao,
        tipo_cambio, numero_marchas,
        suspensao_dianteira, suspensao_traseira, freios_dianteiros, freios_traseiros, possui_abs, possui_ebd,
        tipo_direcao, pneus_originais,
        comprimento_mm, largura_mm, altura_mm, entre_eixos_mm, altura_solo_mm, peso_kg,
        velocidade_maxima_kmh, aceleracao_0_100_s, consumo_urbano_km_l, consumo_rodoviario_km_l, capacidade_tanque_l,
        porta_malas_l, carga_util_kg, numero_ocupantes,
        sistema_injecao_detalhes, sonda_lambda_detalhes, ecu_detalhes, sensores_detalhes, outros_sistemas_eletronicos,
        data_cadastro /* Add data_cadastro automatically */
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
    `; // Using CURRENT_TIMESTAMP for data_cadastro
    const values = [
      professor_id, marca, modelo, ano_fabricacao, ano_modelo, versao, codigo_motor, tipo_combustivel, imagem_url, descricao, preco,
      tipo_motor, cilindrada, potencia_maxima, torque_maximo, numero_valvulas, tipo_injecao,
      tipo_cambio, numero_marchas,
      suspensao_dianteira, suspensao_traseira, freios_dianteiros, freios_traseiros, possui_abs_int, possui_ebd_int, // Use integer values
      tipo_direcao, pneus_originais,
      comprimento_mm, largura_mm, altura_mm, entre_eixos_mm, altura_solo_mm, peso_kg,
      velocidade_maxima_kmh, aceleracao_0_100_s, consumo_urbano_km_l, consumo_rodoviario_km_l, capacidade_tanque_l,
      porta_malas_l, carga_util_kg, numero_ocupantes,
      sistema_injecao_detalhes, sonda_lambda_detalhes, ecu_detalhes, sensores_detalhes, outros_sistemas_eletronicos
    ];

    // Use db.run for INSERT, UPDATE, DELETE
    db.run(query, values, function(err) { // Use function() to access this.lastID
      if (err) {
        console.error("Erro ao criar ficha técnica (SQLite):", err.message);
        // Handle potential constraint errors, e.g., foreign key violation
        return res.status(500).json({ message: "Erro interno ao criar ficha técnica." });
      }
      res.status(201).json({ message: "Ficha técnica criada com sucesso!", id: this.lastID });
    });

  } catch (error) {
    console.error("Erro inesperado em createFicha:", error);
    res.status(500).json({ message: "Erro interno inesperado ao criar ficha técnica." });
  }
};

// Update a ficha (professor access only, adapted for SQLite)
const updateFicha = (req, res) => {
  const { id } = req.params;
  // const professor_id = req.user.id; // Can add check later if needed
  
  const {
    marca, modelo, ano_fabricacao, ano_modelo, versao, codigo_motor, tipo_combustivel, imagem_url, descricao, preco,
    tipo_motor, cilindrada, potencia_maxima, torque_maximo, numero_valvulas, tipo_injecao,
    tipo_cambio, numero_marchas,
    suspensao_dianteira, suspensao_traseira, freios_dianteiros, freios_traseiros, possui_abs, possui_ebd,
    tipo_direcao, pneus_originais,
    comprimento_mm, largura_mm, altura_mm, entre_eixos_mm, altura_solo_mm, peso_kg,
    velocidade_maxima_kmh, aceleracao_0_100_s, consumo_urbano_km_l, consumo_rodoviario_km_l, capacidade_tanque_l,
    porta_malas_l, carga_util_kg, numero_ocupantes,
    sistema_injecao_detalhes, sonda_lambda_detalhes, ecu_detalhes, sensores_detalhes, outros_sistemas_eletronicos
  } = req.body;

  if (!marca || !modelo) {
    return res.status(400).json({ message: "Marca e Modelo são obrigatórios." });
  }

  const possui_abs_int = possui_abs ? 1 : 0;
  const possui_ebd_int = possui_ebd ? 1 : 0;

  try {
    // Optional: Check if ficha exists first using db.get

    const query = `
      UPDATE fichas_tecnicas SET
        marca = ?, modelo = ?, ano_fabricacao = ?, ano_modelo = ?, versao = ?, codigo_motor = ?, tipo_combustivel = ?, imagem_url = ?, descricao = ?, preco = ?,
        tipo_motor = ?, cilindrada = ?, potencia_maxima = ?, torque_maximo = ?, numero_valvulas = ?, tipo_injecao = ?,
        tipo_cambio = ?, numero_marchas = ?,
        suspensao_dianteira = ?, suspensao_traseira = ?, freios_dianteiros = ?, freios_traseiros = ?, possui_abs = ?, possui_ebd = ?,
        tipo_direcao = ?, pneus_originais = ?,
        comprimento_mm = ?, largura_mm = ?, altura_mm = ?, entre_eixos_mm = ?, altura_solo_mm = ?, peso_kg = ?,
        velocidade_maxima_kmh = ?, aceleracao_0_100_s = ?, consumo_urbano_km_l = ?, consumo_rodoviario_km_l = ?, capacidade_tanque_l = ?,
        porta_malas_l = ?, carga_util_kg = ?, numero_ocupantes = ?,
        sistema_injecao_detalhes = ?, sonda_lambda_detalhes = ?, ecu_detalhes = ?, sensores_detalhes = ?, outros_sistemas_eletronicos = ?
        /* professor_id is not updated */
      WHERE id = ?
    `; 
    const values = [
      marca, modelo, ano_fabricacao, ano_modelo, versao, codigo_motor, tipo_combustivel, imagem_url, descricao, preco,
      tipo_motor, cilindrada, potencia_maxima, torque_maximo, numero_valvulas, tipo_injecao,
      tipo_cambio, numero_marchas,
      suspensao_dianteira, suspensao_traseira, freios_dianteiros, freios_traseiros, possui_abs_int, possui_ebd_int,
      tipo_direcao, pneus_originais,
      comprimento_mm, largura_mm, altura_mm, entre_eixos_mm, altura_solo_mm, peso_kg,
      velocidade_maxima_kmh, aceleracao_0_100_s, consumo_urbano_km_l, consumo_rodoviario_km_l, capacidade_tanque_l,
      porta_malas_l, carga_util_kg, numero_ocupantes,
      sistema_injecao_detalhes, sonda_lambda_detalhes, ecu_detalhes, sensores_detalhes, outros_sistemas_eletronicos,
      id // WHERE clause
    ];

    db.run(query, values, function(err) { // Use function() to access this.changes
      if (err) {
        console.error("Erro ao atualizar ficha técnica (SQLite):", err.message);
        return res.status(500).json({ message: "Erro interno ao atualizar ficha técnica." });
      }
      if (this.changes === 0) {
        // No rows were updated, likely because the ID wasn't found
        return res.status(404).json({ message: "Ficha técnica não encontrada para atualização." });
      }
      res.status(200).json({ message: "Ficha técnica atualizada com sucesso!" });
    });

  } catch (error) {
    console.error("Erro inesperado em updateFicha:", error);
    res.status(500).json({ message: "Erro interno inesperado ao atualizar ficha técnica." });
  }
};

// Delete a ficha (professor access only, adapted for SQLite)
const deleteFicha = (req, res) => {
  const { id } = req.params;
  // const professor_id = req.user.id; // Can add check later

  try {
    // Optional: Check if ficha exists first

    const query = "DELETE FROM fichas_tecnicas WHERE id = ?";
    db.run(query, [id], function(err) { // Use function() to access this.changes
      if (err) {
        console.error("Erro ao excluir ficha técnica (SQLite):", err.message);
        return res.status(500).json({ message: "Erro interno ao excluir ficha técnica." });
      }
      if (this.changes === 0) {
        // No rows were deleted, likely because the ID wasn't found
        return res.status(404).json({ message: "Ficha técnica não encontrada para exclusão." });
      }
      res.status(200).json({ message: "Ficha técnica excluída com sucesso!" });
    });

  } catch (error) {
    console.error("Erro inesperado em deleteFicha:", error);
    res.status(500).json({ message: "Erro interno inesperado ao excluir ficha técnica." });
  }
};

module.exports = {
  getAllFichas,
  getFichaById,
  createFicha,
  updateFicha,
  deleteFicha
};

