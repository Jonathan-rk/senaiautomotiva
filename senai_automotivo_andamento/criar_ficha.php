<?php
session_start();

// Verificar se o usuário está logado e é professor
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'professor') {
    header('Location: login.php');
    exit;
}

// Configuração do banco de dados
$host = 'localhost';
$dbname = 'senai_automotivo';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

$success = '';
$error = '';
$isEdit = false;
$ficha = null;

// Verificar se é edição
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $isEdit = true;
    $fichaId = $_GET['edit'];
    
    $stmt = $pdo->prepare("SELECT * FROM fichas_tecnicas WHERE id = ?");
    $stmt->execute([$fichaId]);
    $ficha = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$ficha) {
        header('Location: painel_professor.php');
        exit;
    }
}

// Buscar categorias
$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nome");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coletar dados do formulário
    $dados = [
        'marca' => trim($_POST['marca']),
        'modelo' => trim($_POST['modelo']),
        'ano' => $_POST['ano'],
        'versao' => trim($_POST['versao']),
        'codigo_motor' => trim($_POST['codigo_motor']),
        'tipo_combustivel' => trim($_POST['tipo_combustivel']),
        'tipo_motor' => trim($_POST['tipo_motor']),
        'cilindrada' => trim($_POST['cilindrada']),
        'potencia_maxima' => trim($_POST['potencia_maxima']),
        'torque_maximo' => trim($_POST['torque_maximo']),
        'numero_valvulas' => trim($_POST['numero_valvulas']),
        'injecao_eletronica' => trim($_POST['injecao_eletronica']),
        'tipo_cambio' => trim($_POST['tipo_cambio']),
        'numero_marchas' => trim($_POST['numero_marchas']),
        'suspensoes' => trim($_POST['suspensoes']),
        'freios' => trim($_POST['freios']),
        'abs_ebd' => trim($_POST['abs_ebd']),
        'tipo_direcao' => trim($_POST['tipo_direcao']),
        'pneus_originais' => trim($_POST['pneus_originais']),
        'comprimento' => trim($_POST['comprimento']),
        'largura' => trim($_POST['largura']),
        'altura' => trim($_POST['altura']),
        'entre_eixos' => trim($_POST['entre_eixos']),
        'altura_livre_solo' => trim($_POST['altura_livre_solo']),
        'peso' => trim($_POST['peso']),
        'velocidade_maxima' => trim($_POST['velocidade_maxima']),
        'aceleracao' => trim($_POST['aceleracao']),
        'consumo_urbano' => trim($_POST['consumo_urbano']),
        'consumo_rodoviario' => trim($_POST['consumo_rodoviario']),
        'tanque' => trim($_POST['tanque']),
        'porta_malas' => trim($_POST['porta_malas']),
        'carga_util' => trim($_POST['carga_util']),
        'ocupantes' => trim($_POST['ocupantes']),
        'sistema_injecao' => trim($_POST['sistema_injecao']),
        'sonda_lambda' => trim($_POST['sonda_lambda']),
        'sensor_fase' => trim($_POST['sensor_fase']),
        'sistema_ignicao' => trim($_POST['sistema_ignicao']),
        'tipo_ecu' => trim($_POST['tipo_ecu']),
        'categoria_id' => $_POST['categoria_id'] ?: null
    ];
    
    // Validação básica
    if (empty($dados['marca']) || empty($dados['modelo']) || empty($dados['ano'])) {
        $error = 'Marca, modelo e ano são obrigatórios.';
    } else {
        try {
            if ($isEdit) {
                // Atualizar ficha existente
                $sql = "UPDATE fichas_tecnicas SET 
                        marca = ?, modelo = ?, ano = ?, versao = ?, codigo_motor = ?, tipo_combustivel = ?,
                        tipo_motor = ?, cilindrada = ?, potencia_maxima = ?, torque_maximo = ?, numero_valvulas = ?,
                        injecao_eletronica = ?, tipo_cambio = ?, numero_marchas = ?, suspensoes = ?, freios = ?,
                        abs_ebd = ?, tipo_direcao = ?, pneus_originais = ?, comprimento = ?, largura = ?, altura = ?,
                        entre_eixos = ?, altura_livre_solo = ?, peso = ?, velocidade_maxima = ?, aceleracao = ?,
                        consumo_urbano = ?, consumo_rodoviario = ?, tanque = ?, porta_malas = ?, carga_util = ?,
                        ocupantes = ?, sistema_injecao = ?, sonda_lambda = ?, sensor_fase = ?, sistema_ignicao = ?,
                        tipo_ecu = ?, categoria_id = ?
                        WHERE id = ?";
                
                $params = array_values($dados);
                $params[] = $fichaId;
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                
                $success = 'Ficha técnica atualizada com sucesso!';
            } else {
                // Criar nova ficha
                $sql = "INSERT INTO fichas_tecnicas (
                        marca, modelo, ano, versao, codigo_motor, tipo_combustivel, tipo_motor, cilindrada,
                        potencia_maxima, torque_maximo, numero_valvulas, injecao_eletronica, tipo_cambio,
                        numero_marchas, suspensoes, freios, abs_ebd, tipo_direcao, pneus_originais,
                        comprimento, largura, altura, entre_eixos, altura_livre_solo, peso, velocidade_maxima,
                        aceleracao, consumo_urbano, consumo_rodoviario, tanque, porta_malas, carga_util,
                        ocupantes, sistema_injecao, sonda_lambda, sensor_fase, sistema_ignicao, tipo_ecu,
                        categoria_id
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array_values($dados));
                
                $success = 'Ficha técnica criada com sucesso!';
                
                // Limpar dados para nova ficha
                $dados = array_fill_keys(array_keys($dados), '');
            }
        } catch(PDOException $e) {
            $error = 'Erro ao salvar ficha técnica: ' . $e->getMessage();
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
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container mx-auto px-4">
            <nav class="flex justify-between items-center py-4">
                <a href="index.php" class="logo">
                    <i class="fas fa-car mr-2"></i>SENAI Automotivo
                </a>
                
                <div class="hidden md:flex space-x-4">
                    <a href="index.php" class="nav-link">
                        <i class="fas fa-home mr-2"></i>Início
                    </a>
                    <a href="fichas.php" class="nav-link">
                        <i class="fas fa-file-alt mr-2"></i>Fichas Técnicas
                    </a>
                    <a href="painel_professor.php" class="nav-link">
                        <i class="fas fa-cog mr-2"></i>Painel
                    </a>
                    <a href="categorias.php" class="nav-link">
                        <i class="fas fa-tags mr-2"></i>Categorias
                    </a>
                    <a href="logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt mr-2"></i>Sair
                    </a>
                </div>
                
                <!-- Mobile menu button -->
                <button class="md:hidden text-white" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </nav>
            
            <!-- Mobile menu -->
            <div id="mobileMenu" class="hidden md:hidden pb-4">
                <a href="index.php" class="nav-link block">
                    <i class="fas fa-home mr-2"></i>Início
                </a>
                <a href="fichas.php" class="nav-link block">
                    <i class="fas fa-file-alt mr-2"></i>Fichas Técnicas
                </a>
                <a href="painel_professor.php" class="nav-link block">
                    <i class="fas fa-cog mr-2"></i>Painel
                </a>
                <a href="categorias.php" class="nav-link block">
                    <i class="fas fa-tags mr-2"></i>Categorias
                </a>
                <a href="logout.php" class="nav-link block">
                    <i class="fas fa-sign-out-alt mr-2"></i>Sair
                </a>
            </div>
        </div>
    </header>

    <!-- Page Header -->
    <section class="bg-senai-blue text-white py-8">
        <div class="container mx-auto px-4">
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
                    <a href="painel_professor.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Voltar ao Painel
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Form Section -->
    <section class="py-8">
        <div class="container mx-auto px-4">
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

            <form method="POST" class="bg-white rounded-xl shadow p-8">
                <!-- Informações Básicas -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-info-circle mr-2"></i>Informações Básicas
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="form-group">
                            <label for="marca" class="form-label">Marca *</label>
                            <input type="text" id="marca" name="marca" class="form-input" 
                                   value="<?php echo htmlspecialchars($dados['marca'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="modelo" class="form-label">Modelo *</label>
                            <input type="text" id="modelo" name="modelo" class="form-input" 
                                   value="<?php echo htmlspecialchars($dados['modelo'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="ano" class="form-label">Ano *</label>
                            <input type="number" id="ano" name="ano" class="form-input" 
                                   min="1900" max="<?php echo date('Y') + 1; ?>"
                                   value="<?php echo htmlspecialchars($dados['ano'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="versao" class="form-label">Versão</label>
                            <input type="text" id="versao" name="versao" class="form-input" 
                                   value="<?php echo htmlspecialchars($dados['versao'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="categoria_id" class="form-label">Categoria</label>
                            <select id="categoria_id" name="categoria_id" class="form-select">
                                <option value="">Selecione uma categoria</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?php echo $categoria['id']; ?>" 
                                            <?php echo ($dados['categoria_id'] ?? '') == $categoria['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($categoria['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="tipo_combustivel" class="form-label">Tipo de Combustível</label>
                            <select id="tipo_combustivel" name="tipo_combustivel" class="form-select">
                                <option value="">Selecione</option>
                                <option value="Flex" <?php echo ($dados['tipo_combustivel'] ?? '') == 'Flex' ? 'selected' : ''; ?>>Flex</option>
                                <option value="Gasolina" <?php echo ($dados['tipo_combustivel'] ?? '') == 'Gasolina' ? 'selected' : ''; ?>>Gasolina</option>
                                <option value="Etanol" <?php echo ($dados['tipo_combustivel'] ?? '') == 'Etanol' ? 'selected' : ''; ?>>Etanol</option>
                                <option value="Diesel" <?php echo ($dados['tipo_combustivel'] ?? '') == 'Diesel' ? 'selected' : ''; ?>>Diesel</option>
                                <option value="Elétrico" <?php echo ($dados['tipo_combustivel'] ?? '') == 'Elétrico' ? 'selected' : ''; ?>>Elétrico</option>
                                <option value="Híbrido" <?php echo ($dados['tipo_combustivel'] ?? '') == 'Híbrido' ? 'selected' : ''; ?>>Híbrido</option>
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
                            <label for="codigo_motor" class="form-label">Código do Motor</label>
                            <input type="text" id="codigo_motor" name="codigo_motor" class="form-input" 
                                   value="<?php echo htmlspecialchars($dados['codigo_motor'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="tipo_motor" class="form-label">Tipo de Motor</label>
                            <input type="text" id="tipo_motor" name="tipo_motor" class="form-input" 
                                   placeholder="Ex: 1.0 Turbo, 2.0 Aspirado"
                                   value="<?php echo htmlspecialchars($dados['tipo_motor'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="cilindrada" class="form-label">Cilindrada</label>
                            <input type="text" id="cilindrada" name="cilindrada" class="form-input" 
                                   placeholder="Ex: 1000 cm³, 1.6L"
                                   value="<?php echo htmlspecialchars($dados['cilindrada'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="potencia_maxima" class="form-label">Potência Máxima</label>
                            <input type="text" id="potencia_maxima" name="potencia_maxima" class="form-input" 
                                   placeholder="Ex: 116 cv, 85 kW"
                                   value="<?php echo htmlspecialchars($dados['potencia_maxima'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="torque_maximo" class="form-label">Torque Máximo</label>
                            <input type="text" id="torque_maximo" name="torque_maximo" class="form-input" 
                                   placeholder="Ex: 16,8 kgfm, 165 Nm"
                                   value="<?php echo htmlspecialchars($dados['torque_maximo'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="numero_valvulas" class="form-label">Número de Válvulas</label>
                            <input type="text" id="numero_valvulas" name="numero_valvulas" class="form-input" 
                                   placeholder="Ex: 8, 16"
                                   value="<?php echo htmlspecialchars($dados['numero_valvulas'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="injecao_eletronica" class="form-label">Injeção Eletrônica</label>
                            <input type="text" id="injecao_eletronica" name="injecao_eletronica" class="form-input" 
                                   placeholder="Ex: Multiponto, Direta"
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
                            <label for="tipo_cambio" class="form-label">Tipo de Câmbio</label>
                            <select id="tipo_cambio" name="tipo_cambio" class="form-select">
                                <option value="">Selecione</option>
                                <option value="Manual" <?php echo ($dados['tipo_cambio'] ?? '') == 'Manual' ? 'selected' : ''; ?>>Manual</option>
                                <option value="Automático" <?php echo ($dados['tipo_cambio'] ?? '') == 'Automático' ? 'selected' : ''; ?>>Automático</option>
                                <option value="CVT" <?php echo ($dados['tipo_cambio'] ?? '') == 'CVT' ? 'selected' : ''; ?>>CVT</option>
                                <option value="DSG" <?php echo ($dados['tipo_cambio'] ?? '') == 'DSG' ? 'selected' : ''; ?>>DSG</option>
                                <option value="Automatizado" <?php echo ($dados['tipo_cambio'] ?? '') == 'Automatizado' ? 'selected' : ''; ?>>Automatizado</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="numero_marchas" class="form-label">Número de Marchas</label>
                            <input type="text" id="numero_marchas" name="numero_marchas" class="form-input" 
                                   placeholder="Ex: 5, 6, 7"
                                   value="<?php echo htmlspecialchars($dados['numero_marchas'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Suspensão e Freios -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-car-side mr-2"></i>Suspensão e Freios
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="form-group">
                            <label for="suspensoes" class="form-label">Suspensões</label>
                            <input type="text" id="suspensoes" name="suspensoes" class="form-input" 
                                   placeholder="Ex: Dianteira independente, Traseira semi-independente"
                                   value="<?php echo htmlspecialchars($dados['suspensoes'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="freios" class="form-label">Freios</label>
                            <input type="text" id="freios" name="freios" class="form-input" 
                                   placeholder="Ex: Dianteiros a disco, Traseiros a tambor"
                                   value="<?php echo htmlspecialchars($dados['freios'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="abs_ebd" class="form-label">ABS/EBD</label>
                            <select id="abs_ebd" name="abs_ebd" class="form-select">
                                <option value="">Selecione</option>
                                <option value="ABS" <?php echo ($dados['abs_ebd'] ?? '') == 'ABS' ? 'selected' : ''; ?>>ABS</option>
                                <option value="ABS + EBD" <?php echo ($dados['abs_ebd'] ?? '') == 'ABS + EBD' ? 'selected' : ''; ?>>ABS + EBD</option>
                                <option value="Não possui" <?php echo ($dados['abs_ebd'] ?? '') == 'Não possui' ? 'selected' : ''; ?>>Não possui</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Direção e Pneus -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-steering-wheel mr-2"></i>Direção e Pneus
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="tipo_direcao" class="form-label">Tipo de Direção</label>
                            <select id="tipo_direcao" name="tipo_direcao" class="form-select">
                                <option value="">Selecione</option>
                                <option value="Hidráulica" <?php echo ($dados['tipo_direcao'] ?? '') == 'Hidráulica' ? 'selected' : ''; ?>>Hidráulica</option>
                                <option value="Elétrica" <?php echo ($dados['tipo_direcao'] ?? '') == 'Elétrica' ? 'selected' : ''; ?>>Elétrica</option>
                                <option value="Eletro-hidráulica" <?php echo ($dados['tipo_direcao'] ?? '') == 'Eletro-hidráulica' ? 'selected' : ''; ?>>Eletro-hidráulica</option>
                                <option value="Mecânica" <?php echo ($dados['tipo_direcao'] ?? '') == 'Mecânica' ? 'selected' : ''; ?>>Mecânica</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="pneus_originais" class="form-label">Pneus Originais</label>
                            <input type="text" id="pneus_originais" name="pneus_originais" class="form-input" 
                                   placeholder="Ex: 185/65 R15, 205/55 R16"
                                   value="<?php echo htmlspecialchars($dados['pneus_originais'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Dimensões -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-ruler mr-2"></i>Dimensões
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="form-group">
                            <label for="comprimento" class="form-label">Comprimento</label>
                            <input type="text" id="comprimento" name="comprimento" class="form-input" 
                                   placeholder="Ex: 4.163 mm"
                                   value="<?php echo htmlspecialchars($dados['comprimento'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="largura" class="form-label">Largura</label>
                            <input type="text" id="largura" name="largura" class="form-input" 
                                   placeholder="Ex: 1.731 mm"
                                   value="<?php echo htmlspecialchars($dados['largura'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="altura" class="form-label">Altura</label>
                            <input type="text" id="altura" name="altura" class="form-input" 
                                   placeholder="Ex: 1.474 mm"
                                   value="<?php echo htmlspecialchars($dados['altura'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="entre_eixos" class="form-label">Entre-eixos</label>
                            <input type="text" id="entre_eixos" name="entre_eixos" class="form-input" 
                                   placeholder="Ex: 2.551 mm"
                                   value="<?php echo htmlspecialchars($dados['entre_eixos'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="altura_livre_solo" class="form-label">Altura Livre do Solo</label>
                            <input type="text" id="altura_livre_solo" name="altura_livre_solo" class="form-input" 
                                   placeholder="Ex: 150 mm"
                                   value="<?php echo htmlspecialchars($dados['altura_livre_solo'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="peso" class="form-label">Peso</label>
                            <input type="text" id="peso" name="peso" class="form-input" 
                                   placeholder="Ex: 1.050 kg"
                                   value="<?php echo htmlspecialchars($dados['peso'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Desempenho e Consumo -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-tachometer-alt mr-2"></i>Desempenho e Consumo
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="form-group">
                            <label for="velocidade_maxima" class="form-label">Velocidade Máxima</label>
                            <input type="text" id="velocidade_maxima" name="velocidade_maxima" class="form-input" 
                                   placeholder="Ex: 185 km/h"
                                   value="<?php echo htmlspecialchars($dados['velocidade_maxima'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="aceleracao" class="form-label">Aceleração (0-100 km/h)</label>
                            <input type="text" id="aceleracao" name="aceleracao" class="form-input" 
                                   placeholder="Ex: 10,2 s"
                                   value="<?php echo htmlspecialchars($dados['aceleracao'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="consumo_urbano" class="form-label">Consumo Urbano</label>
                            <input type="text" id="consumo_urbano" name="consumo_urbano" class="form-input" 
                                   placeholder="Ex: 13,7 km/l"
                                   value="<?php echo htmlspecialchars($dados['consumo_urbano'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="consumo_rodoviario" class="form-label">Consumo Rodoviário</label>
                            <input type="text" id="consumo_rodoviario" name="consumo_rodoviario" class="form-input" 
                                   placeholder="Ex: 17,1 km/l"
                                   value="<?php echo htmlspecialchars($dados['consumo_rodoviario'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="tanque" class="form-label">Capacidade do Tanque</label>
                            <input type="text" id="tanque" name="tanque" class="form-input" 
                                   placeholder="Ex: 44 litros"
                                   value="<?php echo htmlspecialchars($dados['tanque'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Capacidades -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-suitcase mr-2"></i>Capacidades
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="form-group">
                            <label for="porta_malas" class="form-label">Porta-malas</label>
                            <input type="text" id="porta_malas" name="porta_malas" class="form-input" 
                                   placeholder="Ex: 275 litros"
                                   value="<?php echo htmlspecialchars($dados['porta_malas'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="carga_util" class="form-label">Carga Útil</label>
                            <input type="text" id="carga_util" name="carga_util" class="form-input" 
                                   placeholder="Ex: 450 kg"
                                   value="<?php echo htmlspecialchars($dados['carga_util'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="ocupantes" class="form-label">Ocupantes</label>
                            <input type="text" id="ocupantes" name="ocupantes" class="form-input" 
                                   placeholder="Ex: 5"
                                   value="<?php echo htmlspecialchars($dados['ocupantes'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Sistemas e Eletrônica (Opcional) -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold senai-blue mb-6">
                        <i class="fas fa-microchip mr-2"></i>Sistemas e Eletrônica (Opcional)
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="sistema_injecao" class="form-label">Sistema de Injeção</label>
                            <input type="text" id="sistema_injecao" name="sistema_injecao" class="form-input" 
                                   placeholder="Ex: Bosch ME7.9.9"
                                   value="<?php echo htmlspecialchars($dados['sistema_injecao'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="sonda_lambda" class="form-label">Sonda Lambda</label>
                            <input type="text" id="sonda_lambda" name="sonda_lambda" class="form-input" 
                                   placeholder="Ex: Sim, Não"
                                   value="<?php echo htmlspecialchars($dados['sonda_lambda'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="sensor_fase" class="form-label">Sensor de Fase</label>
                            <input type="text" id="sensor_fase" name="sensor_fase" class="form-input" 
                                   placeholder="Ex: Sim, Não"
                                   value="<?php echo htmlspecialchars($dados['sensor_fase'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="sistema_ignicao" class="form-label">Sistema de Ignição</label>
                            <input type="text" id="sistema_ignicao" name="sistema_ignicao" class="form-input" 
                                   placeholder="Ex: Eletrônica mapeada"
                                   value="<?php echo htmlspecialchars($dados['sistema_ignicao'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group md:col-span-2">
                            <label for="tipo_ecu" class="form-label">Tipo de ECU</label>
                            <input type="text" id="tipo_ecu" name="tipo_ecu" class="form-input" 
                                   placeholder="Ex: Bosch ME7.9.9"
                                   value="<?php echo htmlspecialchars($dados['tipo_ecu'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="flex flex-col md:flex-row gap-4 justify-end">
                    <a href="painel_professor.php" class="btn btn-secondary">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>
                        <?php echo $isEdit ? 'Atualizar' : 'Criar'; ?> Ficha Técnica
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> SENAI Automotivo. Todos os direitos reservados.</p>
                <p class="mt-2 text-sm opacity-75">
                    Formulário de fichas técnicas - Acesso restrito a professores
                </p>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
        
        // Auto-focus no primeiro campo
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('marca').focus();
        });
        
        // Validação de ano
        document.getElementById('ano').addEventListener('input', function() {
            const currentYear = new Date().getFullYear();
            const inputYear = parseInt(this.value);
            
            if (inputYear < 1900 || inputYear > currentYear + 1) {
                this.setCustomValidity('Ano deve estar entre 1900 e ' + (currentYear + 1));
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>

