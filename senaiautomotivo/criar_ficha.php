<?php
session_start();
require_once 'config.php';

// Verificar se o usuário está logado e é professor
requireLogin();
requireProfessor();

$pdo = getDBConnection();

$success = "";
$error = "";
$isEdit = false;
$ficha = null;
$dados = [];

// Verificar se é edição
if (isset($_GET["edit"]) && is_numeric($_GET["edit"])) {
    $isEdit = true;
    $fichaId = $_GET["edit"];
    
    $stmt = $pdo->prepare("SELECT * FROM fichas_tecnicas WHERE id = ?");
    $stmt->execute([$fichaId]);
    $ficha = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$ficha) {
        header("Location: painel_professor.php");
        exit;
    }
}

// Buscar marcas
$stmt = $pdo->query("SELECT * FROM marcas ORDER BY nome");
$marcas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar carrocerias
$stmt = $pdo->query("SELECT * FROM carrocerias ORDER BY nome");
$carrocerias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Função para upload de imagem
function uploadImage($file, $uploadDir = 'uploads/veiculos/') {
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Tipo de arquivo não permitido. Use JPEG, PNG, GIF ou WebP.');
    }
    
    if ($file['size'] > $maxSize) {
        throw new Exception('Arquivo muito grande. Tamanho máximo: 5MB.');
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filepath;
    } else {
        throw new Exception('Erro ao fazer upload da imagem.');
    }
}

// Processar formulário
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Coletar dados do formulário
    $dados = [
        "marca_id" => $_POST["marca_id"] ?: null,
        "modelo" => sanitizeInput($_POST["modelo"]),
        "ano" => sanitizeInput($_POST["ano"]),
        "versao" => sanitizeInput($_POST["versao"]),
        "codigo_motor" => sanitizeInput($_POST["codigo_motor"]),
        "tipo_combustivel" => sanitizeInput($_POST["tipo_combustivel"]),
        "tipo_motor" => sanitizeInput($_POST["tipo_motor"]),
        "cilindrada" => sanitizeInput($_POST["cilindrada"]),
        "potencia_maxima" => sanitizeInput($_POST["potencia_maxima"]),
        "torque_maximo" => sanitizeInput($_POST["torque_maximo"]),
        "numero_valvulas" => sanitizeInput($_POST["numero_valvulas"]),
        "injecao_eletronica" => sanitizeInput($_POST["injecao_eletronica"]),
        "tipo_cambio" => sanitizeInput($_POST["tipo_cambio"]),
        "numero_marchas" => sanitizeInput($_POST["numero_marchas"]),
        "suspensoes" => sanitizeInput($_POST["suspensoes"]),
        "freios" => sanitizeInput($_POST["freios"]),
        "abs_ebd" => sanitizeInput($_POST["abs_ebd"]),
        "tipo_direcao" => sanitizeInput($_POST["tipo_direcao"]),
        "pneus_originais" => sanitizeInput($_POST["pneus_originais"]),
        "comprimento" => sanitizeInput($_POST["comprimento"]),
        "largura" => sanitizeInput($_POST["largura"]),
        "altura" => sanitizeInput($_POST["altura"]),
        "entre_eixos" => sanitizeInput($_POST["entre_eixos"]),
        "altura_livre_solo" => sanitizeInput($_POST["altura_livre_solo"]),
        "peso" => sanitizeInput($_POST["peso"]),
        "velocidade_maxima" => sanitizeInput($_POST["velocidade_maxima"]),
        "aceleracao" => sanitizeInput($_POST["aceleracao"]),
        "consumo_urbano" => sanitizeInput($_POST["consumo_urbano"]),
        "consumo_rodoviario" => sanitizeInput($_POST["consumo_rodoviario"]),
        "tanque" => sanitizeInput($_POST["tanque"]),
        "porta_malas" => sanitizeInput($_POST["porta_malas"]),
        "carga_util" => sanitizeInput($_POST["carga_util"]),
        "ocupantes" => sanitizeInput($_POST["ocupantes"]),
        "sistema_injecao" => sanitizeInput($_POST["sistema_injecao"]),
        "sonda_lambda" => sanitizeInput($_POST["sonda_lambda"]),
        "sensor_fase" => sanitizeInput($_POST["sensor_fase"]),
        "sistema_ignicao" => sanitizeInput($_POST["sistema_ignicao"]),
        "tipo_ecu" => sanitizeInput($_POST["tipo_ecu"]),
        "carroceria_id" => $_POST["carroceria_id"] ?: null
    ];
    
    // Validação básica
    if (empty($dados["marca_id"]) || empty($dados["modelo"]) || empty($dados["ano"])) {
        $error = "Marca, modelo e ano são obrigatórios.";
    } else {
        try {
            $imagemPath = null;
            
            // Processar upload de imagem se fornecida
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $imagemPath = uploadImage($_FILES['imagem']);
            }
            
            if ($isEdit) {
                // Atualizar ficha existente
                if ($imagemPath) {
                    // Buscar imagem antiga para deletar
                    $stmt = $pdo->prepare("SELECT imagem_path FROM fichas_tecnicas WHERE id = ?");
                    $stmt->execute([$fichaId]);
                    $oldImage = $stmt->fetchColumn();
                    
                    if ($oldImage && file_exists($oldImage)) {
                        unlink($oldImage);
                    }
                    
                    $sql = "UPDATE fichas_tecnicas SET 
                            marca_id = ?, modelo = ?, ano = ?, versao = ?, codigo_motor = ?, tipo_combustivel = ?,
                            tipo_motor = ?, cilindrada = ?, potencia_maxima = ?, torque_maximo = ?, numero_valvulas = ?,
                            injecao_eletronica = ?, tipo_cambio = ?, numero_marchas = ?, suspensoes = ?, freios = ?,
                            abs_ebd = ?, tipo_direcao = ?, pneus_originais = ?, comprimento = ?, largura = ?, altura = ?,
                            entre_eixos = ?, altura_livre_solo = ?, peso = ?, velocidade_maxima = ?, aceleracao = ?,
                            consumo_urbano = ?, consumo_rodoviario = ?, tanque = ?, porta_malas = ?, carga_util = ?,
                            ocupantes = ?, sistema_injecao = ?, sonda_lambda = ?, sensor_fase = ?, sistema_ignicao = ?,
                            tipo_ecu = ?, carroceria_id = ?, imagem_path = ?
                            WHERE id = ?";
                    
                    $params = array_values($dados);
                    $params[] = $imagemPath;
                    $params[] = $fichaId;
                } else {
                    $sql = "UPDATE fichas_tecnicas SET 
                            marca_id = ?, modelo = ?, ano = ?, versao = ?, codigo_motor = ?, tipo_combustivel = ?,
                            tipo_motor = ?, cilindrada = ?, potencia_maxima = ?, torque_maximo = ?, numero_valvulas = ?,
                            injecao_eletronica = ?, tipo_cambio = ?, numero_marchas = ?, suspensoes = ?, freios = ?,
                            abs_ebd = ?, tipo_direcao = ?, pneus_originais = ?, comprimento = ?, largura = ?, altura = ?,
                            entre_eixos = ?, altura_livre_solo = ?, peso = ?, velocidade_maxima = ?, aceleracao = ?,
                            consumo_urbano = ?, consumo_rodoviario = ?, tanque = ?, porta_malas = ?, carga_util = ?,
                            ocupantes = ?, sistema_injecao = ?, sonda_lambda = ?, sensor_fase = ?, sistema_ignicao = ?,
                            tipo_ecu = ?, carroceria_id = ?
                            WHERE id = ?";
                    
                    $params = array_values($dados);
                    $params[] = $fichaId;
                }
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                
                $success = "Ficha técnica atualizada com sucesso!";
            } else {
                // Criar nova ficha
                $sql = "INSERT INTO fichas_tecnicas (
                        marca_id, modelo, ano, versao, codigo_motor, tipo_combustivel, tipo_motor, cilindrada,
                        potencia_maxima, torque_maximo, numero_valvulas, injecao_eletronica, tipo_cambio,
                        numero_marchas, suspensoes, freios, abs_ebd, tipo_direcao, pneus_originais,
                        comprimento, largura, altura, entre_eixos, altura_livre_solo, peso, velocidade_maxima,
                        aceleracao, consumo_urbano, consumo_rodoviario, tanque, porta_malas, carga_util,
                        ocupantes, sistema_injecao, sonda_lambda, sensor_fase, sistema_ignicao, tipo_ecu,
                        carroceria_id, imagem_path
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $params = array_values($dados);
                $params[] = $imagemPath;
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                
                $success = "Ficha técnica criada com sucesso!";
                
                // Limpar dados para nova ficha
                $dados = array_fill_keys(array_keys($dados), "");
            }
        } catch(Exception $e) {
            $error = $e->getMessage();
        } catch(PDOException $e) {
            $error = "Erro ao salvar ficha técnica: " . $e->getMessage();
        }
    }
}

// Se é edição, usar dados da ficha
if ($isEdit && $ficha && !$_POST) {
    $dados = $ficha;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Editar' : 'Criar'; ?> Ficha Técnica - SENAI Automotivo</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <link rel="stylesheet" href="interface_style.css">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <img src="images/wU7KMxCEL6DS.png" alt="SENAI Logo" class="h-10">
                </div>
                
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="text-gray-700 hover:text-blue-600 transition-colors">Início</a>
                    <a href="fichas.php" class="text-gray-700 hover:text-blue-600 transition-colors">Fichas Técnicas</a>
                    <a href="painel_professor.php" class="text-blue-600 font-semibold">
                        <i class="fas fa-cog mr-2"></i>Painel
                    </a>
                    <a href="marcas.php" class="text-gray-700 hover:text-blue-600 transition-colors">
                        <i class="fas fa-car mr-2"></i>Marcas
                    </a>
                    <a href="carrocerias.php" class="text-gray-700 hover:text-blue-600 transition-colors">
                        <i class="fas fa-shapes mr-2"></i>Carrocerias
                    </a>
                    <a href="logout.php" class="text-gray-700 hover:text-blue-600 transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>Sair
                    </a>
                </div>
                
                <div class="md:hidden">
                    <button onclick="toggleMobileMenu()" class="text-gray-700">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobileMenu" class="hidden md:hidden pb-4">
                <a href="index.php" class="block py-2 text-gray-700">Início</a>
                <a href="fichas.php" class="block py-2 text-gray-700">Fichas Técnicas</a>
                <a href="painel_professor.php" class="block py-2 text-blue-600 font-semibold">
                    <i class="fas fa-cog mr-2"></i>Painel
                </a>
                <a href="marcas.php" class="block py-2 text-gray-700">
                    <i class="fas fa-car mr-2"></i>Marcas
                </a>
                <a href="carrocerias.php" class="block py-2 text-gray-700">
                    <i class="fas fa-shapes mr-2"></i>Carrocerias
                </a>
                <a href="logout.php" class="block py-2 text-gray-700">
                    <i class="fas fa-sign-out-alt mr-2"></i>Sair
                </a>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="bg-senai-blue text-white py-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-2">
                        <i class="fas fa-<?php echo $isEdit ? 'edit' : 'plus'; ?> mr-3"></i>
                        <?php echo $isEdit ? 'Editar' : 'Criar'; ?> Ficha Técnica
                    </h1>
                    <p class="text-lg opacity-90">
                        <?php echo $isEdit ? 'Atualize as informações do veículo' : 'Adicione um novo veículo ao sistema'; ?>
                    </p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="painel_professor.php">
                        <i class="fas fa-arrow-left mr-2"></i>Voltar ao Painel
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Form Section -->
    <section class="py-8">
        <div class="max-w-7xl mx-auto px-4">
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-lg p-8">
                <!-- Informações Básicas -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-info-circle mr-2"></i>Informações Básicas
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="form-group">
                            <label for="marca_id" class="block text-sm font-medium text-gray-700 mb-1">Marca *</label>
                            <select id="marca_id" name="marca_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" required>
                                <option value="">Selecione uma marca</option>
                                <?php foreach ($marcas as $marca): ?>
                                    <option value="<?php echo $marca['id']; ?>" 
                                            <?php echo ($dados['marca_id'] ?? '') == $marca['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($marca['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="modelo" class="block text-sm font-medium text-gray-700 mb-1">Modelo *</label>
                            <input type="text" id="modelo" name="modelo" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['modelo'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="ano" class="block text-sm font-medium text-gray-700 mb-1">Ano *</label>
                            <input type="number" id="ano" name="ano" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   min="1900" max="<?php echo date('Y') + 1; ?>"
                                   value="<?php echo htmlspecialchars($dados['ano'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="versao" class="block text-sm font-medium text-gray-700 mb-1">Versão</label>
                            <input type="text" id="versao" name="versao" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['versao'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="carroceria_id" class="block text-sm font-medium text-gray-700 mb-1">Carroceria</label>
                            <select id="carroceria_id" name="carroceria_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm">
                                <option value="">Selecione uma carroceria</option>
                                <?php foreach ($carrocerias as $carroceria): ?>
                                    <option value="<?php echo $carroceria['id']; ?>" 
                                            <?php echo ($dados['carroceria_id'] ?? '') == $carroceria['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($carroceria['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="tipo_combustivel" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Combustível</label>
                            <select id="tipo_combustivel" name="tipo_combustivel" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm">
                                <option value="">Selecione</option>
                                <option value="Flex" <?php echo ($dados['tipo_combustivel'] ?? '') == 'Flex' ? 'selected' : ''; ?>>Flex</option>
                                <option value="Gasolina" <?php echo ($dados['tipo_combustivel'] ?? '') == 'Gasolina' ? 'selected' : ''; ?>>Gasolina</option>
                                <option value="Etanol" <?php echo ($dados['tipo_combustivel'] ?? '') == 'Etanol' ? 'selected' : ''; ?>>Etanol</option>
                                <option value="Diesel" <?php echo ($dados['tipo_combustivel'] ?? '') == 'Diesel' ? 'selected' : ''; ?>>Diesel</option>
                                <option value="Híbrido" <?php echo ($dados['tipo_combustivel'] ?? '') == 'Híbrido' ? 'selected' : ''; ?>>Híbrido</option>
                                <option value="Elétrico" <?php echo ($dados['tipo_combustivel'] ?? '') == 'Elétrico' ? 'selected' : ''; ?>>Elétrico</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Upload de Imagem -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-image mr-2"></i>Imagem do Veículo
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="imagem" class="block text-sm font-medium text-gray-700 mb-1">Imagem do Veículo</label>
                            <input type="file" id="imagem" name="imagem" accept="image/*" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm">
                            <p class="text-xs text-gray-500 mt-1">Formatos aceitos: JPEG, PNG, GIF, WebP. Tamanho máximo: 5MB</p>
                        </div>
                        
                        <?php if ($isEdit && $ficha && $ficha['imagem_path']): ?>
                            <div>
                                <p class="text-sm text-gray-600 mb-2">Imagem atual:</p>
                                <img src="<?php echo htmlspecialchars($ficha['imagem_path']); ?>" alt="Imagem do veículo" class="w-32 h-24 object-cover border rounded">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Motorização -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-engine mr-2"></i>Motorização
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="form-group">
                            <label for="codigo_motor" class="block text-sm font-medium text-gray-700 mb-1">Código do Motor</label>
                            <input type="text" id="codigo_motor" name="codigo_motor" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['codigo_motor'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="tipo_motor" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Motor</label>
                            <input type="text" id="tipo_motor" name="tipo_motor" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['tipo_motor'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="cilindrada" class="block text-sm font-medium text-gray-700 mb-1">Cilindrada</label>
                            <input type="text" id="cilindrada" name="cilindrada" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['cilindrada'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="potencia_maxima" class="block text-sm font-medium text-gray-700 mb-1">Potência Máxima</label>
                            <input type="text" id="potencia_maxima" name="potencia_maxima" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['potencia_maxima'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="torque_maximo" class="block text-sm font-medium text-gray-700 mb-1">Torque Máximo</label>
                            <input type="text" id="torque_maximo" name="torque_maximo" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['torque_maximo'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="numero_valvulas" class="block text-sm font-medium text-gray-700 mb-1">Número de Válvulas</label>
                            <input type="text" id="numero_valvulas" name="numero_valvulas" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['numero_valvulas'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="injecao_eletronica" class="block text-sm font-medium text-gray-700 mb-1">Injeção Eletrônica</label>
                            <input type="text" id="injecao_eletronica" name="injecao_eletronica" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['injecao_eletronica'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Transmissão -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-cogs mr-2"></i>Transmissão
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="tipo_cambio" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Câmbio</label>
                            <select id="tipo_cambio" name="tipo_cambio" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm">
                                <option value="">Selecione</option>
                                <option value="Manual" <?php echo ($dados['tipo_cambio'] ?? '') == 'Manual' ? 'selected' : ''; ?>>Manual</option>
                                <option value="Automático" <?php echo ($dados['tipo_cambio'] ?? '') == 'Automático' ? 'selected' : ''; ?>>Automático</option>
                                <option value="CVT" <?php echo ($dados['tipo_cambio'] ?? '') == 'CVT' ? 'selected' : ''; ?>>CVT</option>
                                <option value="Automatizado" <?php echo ($dados['tipo_cambio'] ?? '') == 'Automatizado' ? 'selected' : ''; ?>>Automatizado</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="numero_marchas" class="block text-sm font-medium text-gray-700 mb-1">Número de Marchas</label>
                            <input type="text" id="numero_marchas" name="numero_marchas" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['numero_marchas'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Chassi -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-car-alt mr-2"></i>Chassi
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="form-group">
                            <label for="suspensoes" class="block text-sm font-medium text-gray-700 mb-1">Suspensões</label>
                            <input type="text" id="suspensoes" name="suspensoes" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['suspensoes'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="freios" class="block text-sm font-medium text-gray-700 mb-1">Freios</label>
                            <input type="text" id="freios" name="freios" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['freios'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="abs_ebd" class="block text-sm font-medium text-gray-700 mb-1">ABS/EBD</label>
                            <select id="abs_ebd" name="abs_ebd" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm">
                                <option value="">Selecione</option>
                                <option value="Sim" <?php echo ($dados['abs_ebd'] ?? '') == 'Sim' ? 'selected' : ''; ?>>Sim</option>
                                <option value="Não" <?php echo ($dados['abs_ebd'] ?? '') == 'Não' ? 'selected' : ''; ?>>Não</option>
                                <option value="Opcional" <?php echo ($dados['abs_ebd'] ?? '') == 'Opcional' ? 'selected' : ''; ?>>Opcional</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="tipo_direcao" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Direção</label>
                            <input type="text" id="tipo_direcao" name="tipo_direcao" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['tipo_direcao'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="pneus_originais" class="block text-sm font-medium text-gray-700 mb-1">Pneus Originais</label>
                            <input type="text" id="pneus_originais" name="pneus_originais" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['pneus_originais'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Dimensões e Capacidades -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-ruler mr-2"></i>Dimensões e Capacidades
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="form-group">
                            <label for="comprimento" class="block text-sm font-medium text-gray-700 mb-1">Comprimento</label>
                            <input type="text" id="comprimento" name="comprimento" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['comprimento'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="largura" class="block text-sm font-medium text-gray-700 mb-1">Largura</label>
                            <input type="text" id="largura" name="largura" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['largura'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="altura" class="block text-sm font-medium text-gray-700 mb-1">Altura</label>
                            <input type="text" id="altura" name="altura" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['altura'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="entre_eixos" class="block text-sm font-medium text-gray-700 mb-1">Entre-eixos</label>
                            <input type="text" id="entre_eixos" name="entre_eixos" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['entre_eixos'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="altura_livre_solo" class="block text-sm font-medium text-gray-700 mb-1">Altura Livre do Solo</label>
                            <input type="text" id="altura_livre_solo" name="altura_livre_solo" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['altura_livre_solo'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="peso" class="block text-sm font-medium text-gray-700 mb-1">Peso</label>
                            <input type="text" id="peso" name="peso" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['peso'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="tanque" class="block text-sm font-medium text-gray-700 mb-1">Capacidade do Tanque</label>
                            <input type="text" id="tanque" name="tanque" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['tanque'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="porta_malas" class="block text-sm font-medium text-gray-700 mb-1">Porta-malas</label>
                            <input type="text" id="porta_malas" name="porta_malas" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['porta_malas'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="carga_util" class="block text-sm font-medium text-gray-700 mb-1">Carga Útil</label>
                            <input type="text" id="carga_util" name="carga_util" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['carga_util'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="ocupantes" class="block text-sm font-medium text-gray-700 mb-1">Número de Ocupantes</label>
                            <input type="text" id="ocupantes" name="ocupantes" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['ocupantes'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Performance -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-tachometer-alt mr-2"></i>Performance
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="form-group">
                            <label for="velocidade_maxima" class="block text-sm font-medium text-gray-700 mb-1">Velocidade Máxima</label>
                            <input type="text" id="velocidade_maxima" name="velocidade_maxima" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['velocidade_maxima'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="aceleracao" class="block text-sm font-medium text-gray-700 mb-1">Aceleração (0-100 km/h)</label>
                            <input type="text" id="aceleracao" name="aceleracao" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['aceleracao'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="consumo_urbano" class="block text-sm font-medium text-gray-700 mb-1">Consumo Urbano</label>
                            <input type="text" id="consumo_urbano" name="consumo_urbano" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['consumo_urbano'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="consumo_rodoviario" class="block text-sm font-medium text-gray-700 mb-1">Consumo Rodoviário</label>
                            <input type="text" id="consumo_rodoviario" name="consumo_rodoviario" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['consumo_rodoviario'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Sistema Eletrônico -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-microchip mr-2"></i>Sistema Eletrônico
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="form-group">
                            <label for="sistema_injecao" class="block text-sm font-medium text-gray-700 mb-1">Sistema de Injeção</label>
                            <input type="text" id="sistema_injecao" name="sistema_injecao" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['sistema_injecao'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="sonda_lambda" class="block text-sm font-medium text-gray-700 mb-1">Sonda Lambda</label>
                            <input type="text" id="sonda_lambda" name="sonda_lambda" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['sonda_lambda'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="sensor_fase" class="block text-sm font-medium text-gray-700 mb-1">Sensor de Fase</label>
                            <input type="text" id="sensor_fase" name="sensor_fase" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['sensor_fase'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="sistema_ignicao" class="block text-sm font-medium text-gray-700 mb-1">Sistema de Ignição</label>
                            <input type="text" id="sistema_ignicao" name="sistema_ignicao" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['sistema_ignicao'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="tipo_ecu" class="block text-sm font-medium text-gray-700 mb-1">Tipo de ECU</label>
                            <input type="text" id="tipo_ecu" name="tipo_ecu" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['tipo_ecu'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="painel_professor.php" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-semibold">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-3 bg-senai-blue text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                        <i class="fas fa-save mr-2"></i>
                        <?php echo $isEdit ? 'Atualizar' : 'Criar'; ?> Ficha Técnica
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <!-- Footer -->
<footer class="bg-senai-blue text-white py-6">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-start items-start">
            <div class="mb-6 md:mb-0">
                <img src="images/wU7KMxCEL6DS.png" alt="SENAI Logo" class="h-10">
                <p class="mt-2 text-sm opacity-80">&copy; <?php echo date("Y"); ?> SENAI Automotivo. Todos os direitos reservados.</p>
            </div>
            <div class="flex space-x-6 ml-0">
                <!-- Social Media Links -->
                <a href="https://www.facebook.com/senaijoinvilleoficial" target="_blank" rel="noopener noreferrer" class="hover:text-gray-300 transition-colors">
                    <i class="fab fa-facebook-f text-xl"></i>
                </a>
                <a href="https://www.instagram.com/senai.joinvillesc/" target="_blank" rel="noopener noreferrer" class="hover:text-gray-300 transition-colors">
                    <i class="fab fa-instagram text-xl"></i>
                </a>
                <a href="https://www.linkedin.com/company/senai-sc" target="_blank" rel="noopener noreferrer" class="hover:text-gray-300 transition-colors">
                    <i class="fab fa-linkedin-in text-xl"></i>
                </a>
                <a href="https://www.youtube.com/@SENAISantaCatarina" target="_blank" rel="noopener noreferrer" class="hover:text-gray-300 transition-colors">
                    <i class="fab fa-youtube text-xl"></i>
                </a>
            </div>
        </div>
        <div class="mt-8 pt-8 border-t border-blue-800 text-sm opacity-80">
            <div class="flex flex-col md:flex-row items-start">
                <div class="mb-4 md:mb-0 text-left">
                    <a href="#" class="hover:text-gray-300 transition-colors mr-4">Política de Privacidade</a>
                    <a href="#" class="hover:text-gray-300 transition-colors mr-4">Termos de Uso</a>
                    <a href="#" class="hover:text-gray-300 transition-colors">Contato</a>
                </div>
                <div class="text-left">
                    <p>SENAI - Serviço Nacional de Aprendizagem Industrial</p>
                </div>
            </div>
        </div>
    </div>
</footer>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html>

