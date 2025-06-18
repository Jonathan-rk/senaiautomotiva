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

// Buscar montadoras
$stmt = $pdo->query("SELECT * FROM montadoras ORDER BY nome");
$montadoras = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        "montadoras_id" => $_POST["montadora_id"] ?: null,
        "modelo" => sanitizeInput($_POST["modelo"]),
        "ano" => sanitizeInput($_POST["ano"]),
        "lugares" => sanitizeInput($_POST["lugares"]),
        "portas" => sanitizeInput($_POST["portas"]),
        "identificacaomotor" => sanitizeInput($_POST["identificacaomotor"]),
        "materialconstrucao" => sanitizeInput($_POST["materialconstrucao"]),
        "instalacao" => sanitizeInput($_POST["instalacao"]),
        "disposicao" => sanitizeInput($_POST["disposicao"]),
        "combustivel" => sanitizeInput($_POST["combustivel"]),
        "cilindros" => sanitizeInput($_POST["cilindros"]),
        "valvulasporcilindro" => sanitizeInput($_POST["valvulasporcilindro"]),
        "aspiracao" => sanitizeInput($_POST["aspiracao"]),
        "alimentacao" => sanitizeInput($_POST["alimentacao"]),
        "potencia" => sanitizeInput($_POST["potencia"]),
        "cilindrada" => sanitizeInput($_POST["cilindrada"]),
        "torque" => sanitizeInput($_POST["torque"]),
        "rotacao" => sanitizeInput($_POST["rotacao"]),
        "tracao" => sanitizeInput($_POST["tracao"]),
        "cambio" => sanitizeInput($_POST["tipo_cambio"]),
        "numero_marchas" => sanitizeInput($_POST["numero_marchas"]),
        "embreagem" => sanitizeInput($_POST["embreagem"]),
        "dianteira" => sanitizeInput($_POST["dianteira"]),
        "traseira" => sanitizeInput($_POST["traseira"]),
        "dianteirosfreios" => sanitizeInput($_POST["dianteirosfreios"]),
        "traseirosfreios" => sanitizeInput($_POST["traseirosfreios"]),
        "assistencia" => sanitizeInput($_POST["assistencia"]),
        "dianteira_pressao_enchimento" => sanitizeInput($_POST["dianteira_pressao_enchimento"]),
        "traseira_pressao_enchimento" => sanitizeInput($_POST["traseira_pressao_enchimento"]),
        "dimensao_estepe" => sanitizeInput($_POST["dimensao_estepe"]),
        "material_rodas" => sanitizeInput($_POST["material_rodas"]),
        "comprimento" => sanitizeInput($_POST["comprimento"]),
        "distancia_eixos" => sanitizeInput($_POST["distancia_eixos"]),
        "largura" => sanitizeInput($_POST["largura"]),
        "altura" => sanitizeInput($_POST["altura"]),
        "peso_bruto" => sanitizeInput($_POST["peso_bruto"]),
        "porta_malas" => sanitizeInput($_POST["porta_malas"]),
        "velocidade_maxima" => sanitizeInput($_POST["velocidade_maxima"]),
        "aceleracao" => sanitizeInput($_POST["aceleracao"]),
        "capacidade_tanque" => sanitizeInput($_POST["capacidade_tanque"]),
        "consumo_urbano" => sanitizeInput($_POST["consumo_urbano"]),
        "consumo_rodovia" => sanitizeInput($_POST["consumo_rodovia"]),
        "autonomia_urbana" => sanitizeInput($_POST["autonomia_urbana"]),
        "autonomia_rodovia" => sanitizeInput($_POST["autonomia_rodovia"]),
        "oleo_motor" => sanitizeInput($_POST["oleo_motor"]),
        "oleo_transmissao" => sanitizeInput($_POST["oleo_transmissao"]),
        "fluido_freio" => sanitizeInput($_POST["fluido_freio"]),
        "carroceria_id" => $_POST["carroceria_id"] ?: null
    ];

    // Validação básica
    if (empty($dados["montadoras_id"]) || empty($dados["modelo"]) || empty($dados["ano"])) {
        $error = "Montadora, modelo e ano são obrigatórios.";
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
                            montadoras_id = ?, modelo = ?, ano = ?, lugares = ?, portas = ?,
                            identificacaomotor = ?, materialconstrucao = ?, instalacao = ?, disposicao = ?, combustivel = ?,
                            cilindros = ?, valvulasporcilindro = ?, aspiracao = ?, alimentacao = ?, potencia = ?,
                            cilindrada = ?, torque = ?, rotacao = ?, tracao = ?, cambio = ?, numero_marchas = ?, embreagem = ?,
                            dianteira = ?, traseira = ?, dianteirosfreios = ?, traseirosfreios = ?, assistencia = ?,
                            dianteira_pressao_enchimento = ?, traseira_pressao_enchimento = ?, dimensao_estepe = ?, material_rodas = ?, comprimento = ?,
                            distancia_eixos = ?, largura = ?, altura = ?, peso_bruto = ?, porta_malas = ?,
                            velocidade_maxima = ?, aceleracao = ?, capacidade_tanque = ?, consumo_urbano = ?, consumo_rodovia = ?,
                            autonomia_urbana = ?, autonomia_rodovia = ?, oleo_motor = ?, oleo_transmissao = ?, fluido_freio = ?,
                            carroceria_id = ?, imagem_path = ?
                            WHERE id = ?";
                    
                    $params = array_values($dados);
                    $params[] = $imagemPath;
                    $params[] = $fichaId;
                } else {
                    $sql = "UPDATE fichas_tecnicas SET 
                            montadoras_id = ?, modelo = ?, ano = ?, lugares = ?, portas = ?,
                            identificacaomotor = ?, materialconstrucao = ?, instalacao = ?, disposicao = ?, combustivel = ?,
                            cilindros = ?, valvulasporcilindro = ?, aspiracao = ?, alimentacao = ?, potencia = ?,
                            cilindrada = ?, torque = ?, rotacao = ?, tracao = ?, cambio = ?, numero_marchas = ?, embreagem = ?,
                            dianteira = ?, traseira = ?, dianteirosfreios = ?, traseirosfreios = ?, assistencia = ?,
                            dianteira_pressao_enchimento = ?, traseira_pressao_enchimento = ?, dimensao_estepe = ?, material_rodas = ?, comprimento = ?,
                            distancia_eixos = ?, largura = ?, altura = ?, peso_bruto = ?, porta_malas = ?,
                            velocidade_maxima = ?, aceleracao = ?, capacidade_tanque = ?, consumo_urbano = ?, consumo_rodovia = ?,
                            autonomia_urbana = ?, autonomia_rodovia = ?, oleo_motor = ?, oleo_transmissao = ?, fluido_freio = ?,
                            carroceria_id = ?
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
                        montadoras_id, modelo, ano, lugares, portas,
                        identificacaomotor, materialconstrucao, instalacao, disposicao, combustivel,
                        cilindros, valvulasporcilindro, aspiracao, alimentacao, potencia,
                        cilindrada, torque, rotacao, tracao, cambio, numero_marchas, embreagem,
                        dianteira, traseira, dianteirosfreios, traseirosfreios, assistencia,
                        dianteira_pressao_enchimento, traseira_pressao_enchimento, dimensao_estepe, material_rodas, comprimento,
                        distancia_eixos, largura, altura, peso_bruto, porta_malas,
                        velocidade_maxima, aceleracao, capacidade_tanque, consumo_urbano, consumo_rodovia,
                        autonomia_urbana, autonomia_rodovia, oleo_motor, oleo_transmissao, fluido_freio,
                        carroceria_id, imagem_path
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

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
                    <a href="montadoras.php" class="text-gray-700 hover:text-blue-600 transition-colors">
                        <i class="fas fa-car mr-2"></i>Montadoras
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
                <a href="montadoras.php" class="block py-2 text-gray-700">
                    <i class="fas fa-car mr-2"></i>Montadoras
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

                <!-- Imagem do Veículo -->
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

                <!-- Descrição do Veículo -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-info-circle mr-2"></i>Descrição do Veículo
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="form-group">
                            <label for="montadoras_id" class="block text-sm font-medium text-gray-700 mb-1">Montadora *</label>
                            <select id="montadoras_id" name="montadora_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" required>
                                <option value="">Selecione uma montadora</option>
                                <?php foreach ($montadoras as $montadora): ?>
                                    <option value="<?php echo $montadora['id']; ?>" 
                                            <?php echo ($dados['montadoras_id'] ?? '') == $montadora['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($montadora['nome']); ?>
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
                            <label for="lugares" class="block text-sm font-medium text-gray-700 mb-1">Lugares</label>
                            <select id="lugares" name="lugares" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm">
                                <option value="">Selecione</option>
                                <option value="2" <?php echo ($dados['lugares'] ?? '') == '2' ? 'selected' : ''; ?>>2</option>
                                <option value="4" <?php echo ($dados['lugares'] ?? '') == '4' ? 'selected' : ''; ?>>4</option>
                                <option value="5" <?php echo ($dados['lugares'] ?? '') == '5' ? 'selected' : ''; ?>>5</option>
                                <option value="7" <?php echo ($dados['lugares'] ?? '') == '7' ? 'selected' : ''; ?>>7</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="portas" class="block text-sm font-medium text-gray-700 mb-1">Portas</label>
                            <select id="portas" name="portas" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm">
                                <option value="">Selecione</option>
                                <option value="2" <?php echo ($dados['portas'] ?? '') == '2' ? 'selected' : ''; ?>>2</option>
                                <option value="4" <?php echo ($dados['portas'] ?? '') == '4' ? 'selected' : ''; ?>>4</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Motorização -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-engine mr-2"></i>Motorização
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="form-group">
                            <label for="identificacaomotor" class="block text-sm font-medium text-gray-700 mb-1">Identificação do Motor</label>
                            <input type="text" id="identificacaomotor" name="identificacaomotor" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['identificacaomotor'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="materialconstrucao" class="block text-sm font-medium text-gray-700 mb-1">Material de Construção</label>
                            <input type="text" id="materialconstrucao" name="materialconstrucao" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['materialconstrucao'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="instalacao" class="block text-sm font-medium text-gray-700 mb-1">Instalação</label>
                            <select name="instalacao" id="instalacao" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm">
                                <option value="">Selecione</option>
                                <option value="Dianteira" <?php echo ($dados['instalacao'] ?? '') == 'Dianteira' ? 'selected' : ''; ?>>Dianteira</option>
                                <option value="Central" <?php echo ($dados['instalacao'] ?? '') == 'Central' ? 'selected' : ''; ?>>Central</option>
                                <option value="Traseira" <?php echo ($dados['instalacao'] ?? '') == 'Traseira' ? 'selected' : ''; ?>>Traseira</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="disposicao" class="block text-sm font-medium text-gray-700 mb-1">Disposição</label>
                            <select name="disposicao" id="disposicao" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm">
                                <option value="">Selecione</option>
                                <option value="Traversal" <?php echo ($dados['disposicao'] ?? '') == 'Traversal' ? 'selected' : ''; ?>>Traversal</option>
                                <option value="Longitudinal" <?php echo ($dados['disposicao'] ?? '') == 'Longitudinal' ? 'selected' : ''; ?>>Longitudinal</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="combustivel" class="block text-sm font-medium text-gray-700 mb-1">Combustível</label>
                            <select name="combustivel" id="combustivel" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm">
                                <option value="">Selecione</option>
                                <option value="Flex" <?php echo ($dados['combustivel'] ?? '') == 'Flex' ? 'selected' : ''; ?>>Flex</option>
                                <option value="Gasolina" <?php echo ($dados['combustivel'] ?? '') == 'Gasolina' ? 'selected' : ''; ?>>Gasolina</option>
                                <option value="Etanol" <?php echo ($dados['combustivel'] ?? '') == 'Etanol' ? 'selected' : ''; ?>>Etanol</option>
                                <option value="Diesel" <?php echo ($dados['combustivel'] ?? '') == 'Diesel' ? 'selected' : ''; ?>>Diesel</option>
                                <option value="Hibrido" <?php echo ($dados['combustivel'] ?? '') == 'Hibrido' ? 'selected' : ''; ?>>Híbrido</option>
                                <option value="Eletrico" <?php echo ($dados['combustivel'] ?? '') == 'Eletrico' ? 'selected' : ''; ?>>Elétrico</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="cilindros" class="block text-sm font-medium text-gray-700 mb-1">Cilindros</label>
                            <input type="text" id="cilindros" name="cilindros" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['cilindros'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="valvulasporcilindro" class="block text-sm font-medium text-gray-700 mb-1">Válvulas por Cilindros e Total</label>
                            <input type="text" id="valvulasporcilindro" name="valvulasporcilindro" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['valvulasporcilindro'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="aspiracao" class="block text-sm font-medium text-gray-700 mb-1">Aspiração/Admissão</label>
                           <select name="aspiracao" id="aspiracao" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm">
                                <option value="">Selecione</option>
                                <option value="Natural" <?php echo ($dados['aspiracao'] ?? '') == 'Natural' ? 'selected' : ''; ?>>Natural</option>
                                <option value="Turboalimentado" <?php echo ($dados['aspiracao'] ?? '') == 'Turboalimentado' ? 'selected' : ''; ?>>Turboalimentado</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="alimentacao" class="block text-sm font-medium text-gray-700 mb-1">Alimentação</label>
                            <input type="text" id="alimentacao" name="alimentacao" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['alimentacao'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="potencia" class="block text-sm font-medium text-gray-700 mb-1">Potência</label>
                            <input type="text" id="potencia" name="potencia" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['potencia'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="cilindrada" class="block text-sm font-medium text-gray-700 mb-1">Cilindrada</label>
                            <input type="text" id="cilindrada" name="cilindrada" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['cilindrada'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="torque" class="block text-sm font-medium text-gray-700 mb-1">Torque</label>
                            <input type="text" id="torque" name="torque" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['torque'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="rotacao" class="block text-sm font-medium text-gray-700 mb-1">Rotações Máxima</label>
                            <input type="text" id="rotacao" name="rotacao" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['rotacao'] ?? ''); ?>">
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
                            <label for="tracao" class="block text-sm font-medium text-gray-700 mb-1">Tração</label>
                            <select id="tracao" name="tracao" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm">
                                <option value="">Selecione</option>
                                <option value="Dianteira" <?php echo ($dados['tracao'] ?? '') == 'Dianteira' ? 'selected' : ''; ?>>Dianteira (FWD)</option>
                                <option value="Traseira" <?php echo ($dados['tracao'] ?? '') == 'Traseira' ? 'selected' : ''; ?>>Traseira (RWD)</option>
                                <option value="Integral" <?php echo ($dados['tracao'] ?? '') == 'Integral' ? 'selected' : ''; ?>>Integral (AWD)</option>
                                <option value="4x4" <?php echo ($dados['tracao'] ?? '') == '4x4' ? 'selected' : ''; ?>>4x4 (4WD)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tipo_cambio" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Câmbio</label>
                            <select id="tipo_cambio" name="tipo_cambio" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm">
                                <option value="">Selecione</option>
                                <option value="Manual" <?php echo ($dados['cambio'] ?? '') == 'Manual' ? 'selected' : ''; ?>>Manual</option>
                                <option value="Automático" <?php echo ($dados['cambio'] ?? '') == 'Automático' ? 'selected' : ''; ?>>Automático</option>
                                <option value="CVT" <?php echo ($dados['cambio'] ?? '') == 'CVT' ? 'selected' : ''; ?>>CVT</option>
                                <option value="Automatizado" <?php echo ($dados['cambio'] ?? '') == 'Automatizado' ? 'selected' : ''; ?>>Automatizado</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="numero_marchas" class="block text-sm font-medium text-gray-700 mb-1">Número de Marchas</label>
                            <input type="text" id="numero_marchas" name="numero_marchas" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['numero_marchas'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="embreagem" class="block text-sm font-medium text-gray-700 mb-1">Embreagem</label>
                            <input type="text" id="embreagem" name="embreagem" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['embreagem'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Suspensão -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-cogs mr-2"></i>Suspensão
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div class="form-group">
                            <label for="dianteira" class="block text-sm font-medium text-gray-700 mb-1">Suspensão Dianteira</label>
                            <input type="text" id="dianteira" name="dianteira" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['dianteira'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="traseira" class="block text-sm font-medium text-gray-700 mb-1">Suspensão Traseira</label>
                            <input type="text" id="traseira" name="traseira" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['traseira'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Freios -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-cogs mr-2"></i>Freios
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div class="form-group">
                            <label for="dianteirosfreios" class="block text-sm font-medium text-gray-700 mb-1">Freio Dianteiro</label>
                            <input type="text" id="dianteirosfreios" name="dianteirosfreios" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['dianteirosfreios'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="traseirosfreios" class="block text-sm font-medium text-gray-700 mb-1">Freio Traseiro</label>
                            <input type="text" id="traseirosfreios" name="traseirosfreios" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['traseirosfreios'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Direção -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-cogs mr-2"></i>Direção
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div class="form-group">
                            <label for="assistencia" class="block text-sm font-medium text-gray-700 mb-1">Assistência</label>
                            <input type="text" id="assistencia" name="assistencia" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['assistencia'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Rodas e Pneus -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-cogs mr-2"></i>Rodas e Pneus
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div class="form-group">
                            <label for="dianteira_pressao_enchimento" class="block text-sm font-medium text-gray-700 mb-1">Pressão de Enchimento Dianteira</label>
                            <input type="text" id="dianteira_pressao_enchimento" name="dianteira_pressao_enchimento" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['dianteira_pressao_enchimento'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="traseira_pressao_enchimento" class="block text-sm font-medium text-gray-700 mb-1">Pressão de Enchimento Traseira</label>
                            <input type="text" id="traseira_pressao_enchimento" name="traseira_pressao_enchimento" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['traseira_pressao_enchimento'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="dimensao_estepe" class="block text-sm font-medium text-gray-700 mb-1">Dimensão do Estepe / Velocidade</label>
                            <input type="text" id="dimensao_estepe" name="dimensao_estepe" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['dimensao_estepe'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="material_rodas" class="block text-sm font-medium text-gray-700 mb-1">Material das Rodas / Dimensão</label>
                            <input type="text" id="material_rodas" name="material_rodas" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['material_rodas'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Chassi / Carroceria -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-car-alt mr-2"></i>Chassi / Carroceria
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="form-group">
                            <label for="comprimento" class="block text-sm font-medium text-gray-700 mb-1">Comprimento</label>
                            <input type="text" id="comprimento" name="comprimento" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['comprimento'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="distancia_eixos" class="block text-sm font-medium text-gray-700 mb-1">Distância Entre-Eixos</label>
                            <input type="text" id="distancia_eixos" name="distancia_eixos" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['distancia_eixos'] ?? ''); ?>">
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
                            <label for="peso_bruto" class="block text-sm font-medium text-gray-700 mb-1">Peso bruto / Em ordem de Marchas</label>
                            <input type="text" id="peso_bruto" name="peso_bruto" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['peso_bruto'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="porta_malas" class="block text-sm font-medium text-gray-700 mb-1">Porta-Malas (Litros)</label>
                            <input type="text" id="porta_malas" name="porta_malas" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['porta_malas'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Desempenho -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-ruler mr-2"></i>Desempenho
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="form-group">
                            <label for="velocidade_maxima" class="block text-sm font-medium text-gray-700 mb-1">Velocidade Máxima</label>
                            <input type="text" id="velocidade_maxima" name="velocidade_maxima" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['velocidade_maxima'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="aceleracao" class="block text-sm font-medium text-gray-700 mb-1">Aceleração 0-100 km/h</label>
                            <input type="text" id="aceleracao" name="aceleracao" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['aceleracao'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="capacidade_tanque" class="block text-sm font-medium text-gray-700 mb-1">Capacidade do Tanque de Combustível</label>
                            <input type="text" id="capacidade_tanque" name="capacidade_tanque" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['capacidade_tanque'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="consumo_urbano" class="block text-sm font-medium text-gray-700 mb-1">Consumo Urbano</label>
                            <input type="text" id="consumo_urbano" name="consumo_urbano" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['consumo_urbano'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="consumo_rodovia" class="block text-sm font-medium text-gray-700 mb-1">Consumo Rodovia</label>
                            <input type="text" id="consumo_rodovia" name="consumo_rodovia" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['consumo_rodovia'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="autonomia_urbana" class="block text-sm font-medium text-gray-700 mb-1">Autonomia Urbana</label>
                            <input type="text" id="autonomia_urbana" name="autonomia_urbana" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['autonomia_urbana'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="autonomia_rodovia" class="block text-sm font-medium text-gray-700 mb-1">Autonomia Rodovia</label>
                            <input type="text" id="autonomia_rodovia" name="autonomia_rodovia" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['autonomia_rodovia'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Fluidos -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-tachometer-alt mr-2"></i>Fluidos
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="form-group">
                            <label for="oleo_motor" class="block text-sm font-medium text-gray-700 mb-1">Óleo do Motor</label>
                            <input type="text" id="oleo_motor" name="oleo_motor" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['oleo_motor'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="oleo_transmissao" class="block text-sm font-medium text-gray-700 mb-1">Óleo de Transmissão</label>
                            <input type="text" id="oleo_transmissao" name="oleo_transmissao" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['oleo_transmissao'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="fluido_freio" class="block text-sm font-medium text-gray-700 mb-1">Fluido de Freio</label>
                            <input type="text" id="fluido_freio" name="fluido_freio" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-senai-blue focus:border-senai-blue sm:text-sm" 
                                   value="<?php echo htmlspecialchars($dados['fluido_freio'] ?? ''); ?>">
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

