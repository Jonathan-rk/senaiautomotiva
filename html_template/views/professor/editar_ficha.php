<?php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../controllers/FichaController.php';

require_professor();

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: /views/professor/interface.php?error=no_id');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$controller = new FichaController();
$ficha = $controller->getFicha($id);

if (!$ficha) {
    header('Location: /views/professor/interface.php?error=not_found');
    exit;
}

$authController = new AuthController();
$user = $authController->getCurrentUser();

// Verificar se a ficha pertence ao professor atual
if ($ficha->id_professor != $user->id) {
    header('Location: /views/professor/interface.php?error=permission');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Ficha Técnica - SENAI</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <!-- Header/Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-senai">
        <div class="container">
            <a class="navbar-brand" href="#">SENAI - Sistema de Fichas Técnicas</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="interface.php"><i class="fas fa-tachometer-alt me-1"></i> Painel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="nova_ficha.php"><i class="fas fa-plus-circle me-1"></i> Nova Ficha</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog me-1"></i> Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/api/logout.php"><i class="fas fa-sign-out-alt me-1"></i> Sair</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title mb-0">Editar Ficha Técnica</h1>
            <a href="interface.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
        
        <!-- Error Messages -->
        <div id="error-container" class="alert alert-danger d-none" role="alert"></div>
        
        <!-- Form -->
        <div class="card">
            <div class="card-body">
                <form id="carForm" action="/api/update_car.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $ficha->id; ?>">
                    <input type="hidden" name="id_professor" value="<?php echo $user->id; ?>">
                    
                    <div class="row">
                        <!-- Informações básicas -->
                        <div class="col-md-12 mb-4">
                            <h4><i class="fas fa-info-circle me-2"></i>Informações Básicas</h4>
                            <hr>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label for="marca" class="form-label">Marca*</label>
                                <input type="text" class="form-control" id="marca" name="marca" 
                                       value="<?php echo htmlspecialchars($ficha->marca); ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label for="modelo" class="form-label">Modelo*</label>
                                <input type="text" class="form-control" id="modelo" name="modelo" 
                                       value="<?php echo htmlspecialchars($ficha->modelo); ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label for="ano" class="form-label">Ano*</label>
                                <input type="number" class="form-control" id="ano" name="ano" min="1900" max="2099" 
                                       value="<?php echo htmlspecialchars($ficha->ano); ?>" required>
                            </div>
                        </div>
                        
                        <!-- Especificações do motor -->
                        <div class="col-md-12 mt-3 mb-4">
                            <h4><i class="fas fa-cogs me-2"></i>Especificações do Motor</h4>
                            <hr>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="motor" class="form-label">Motor</label>
                                <input type="text" class="form-control" id="motor" name="motor" 
                                       value="<?php echo htmlspecialchars($ficha->motor ?? ''); ?>" 
                                       placeholder="Ex: 2.0 Turbo">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="potencia" class="form-label">Potência</label>
                                <input type="text" class="form-control" id="potencia" name="potencia" 
                                       value="<?php echo htmlspecialchars($ficha->potencia ?? ''); ?>" 
                                       placeholder="Ex: 150 cv @ 5500 rpm">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="torque" class="form-label">Torque</label>
                                <input type="text" class="form-control" id="torque" name="torque" 
                                       value="<?php echo htmlspecialchars($ficha->torque ?? ''); ?>" 
                                       placeholder="Ex: 20,4 kgfm @ 2000 rpm">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="consumo" class="form-label">Consumo</label>
                                <input type="text" class="form-control" id="consumo" name="consumo" 
                                       value="<?php echo htmlspecialchars($ficha->consumo ?? ''); ?>" 
                                       placeholder="Ex: 10,5 km/l (cidade) / 13,2 km/l (estrada)">
                            </div>
                        </div>
                        
                        <!-- Características do veículo -->
                        <div class="col-md-12 mt-3 mb-4">
                            <h4><i class="fas fa-car-side me-2"></i>Características do Veículo</h4>
                            <hr>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label for="cambio" class="form-label">Câmbio</label>
                                <input type="text" class="form-control" id="cambio" name="cambio" 
                                       value="<?php echo htmlspecialchars($ficha->cambio ?? ''); ?>" 
                                       placeholder="Ex: Automático de 6 velocidades">
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label for="tracao" class="form-label">Tração</label>
                                <input type="text" class="form-control" id="tracao" name="tracao" 
                                       value="<?php echo htmlspecialchars($ficha->tracao ?? ''); ?>" 
                                       placeholder="Ex: Dianteira">
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label for="portas" class="form-label">Portas</label>
                                <input type="number" class="form-control" id="portas" name="portas" 
                                       value="<?php echo htmlspecialchars($ficha->portas ?? ''); ?>" min="1" max="6">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="porta_malas" class="form-label">Porta-malas (litros)</label>
                                <input type="text" class="form-control" id="porta_malas" name="porta_malas" 
                                       value="<?php echo htmlspecialchars($ficha->porta_malas ?? ''); ?>" 
                                       placeholder="Ex: 420 litros">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="peso" class="form-label">Peso (kg)</label>
                                <input type="text" class="form-control" id="peso" name="peso" 
                                       value="<?php echo htmlspecialchars($ficha->peso ?? ''); ?>" 
                                       placeholder="Ex: 1250 kg">
                            </div>
                        </div>
                        
                        <!-- Imagem do veículo -->
                        <div class="col-md-12 mt-3 mb-4">
                            <h4><i class="fas fa-image me-2"></i>Imagem do Veículo</h4>
                            <hr>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <?php if (!empty($ficha->imagem)): ?>
                                <div class="mb-3">
                                    <p>Imagem atual:</p>
                                    <img src="/uploads/<?php echo htmlspecialchars($ficha->imagem); ?>" 
                                         alt="Imagem atual" class="img-thumbnail" style="max-width: 300px; max-height: 200px;">
                                </div>
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <label for="imagem" class="form-label">Alterar Imagem</label>
                                <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*">
                                <div class="form-text">Formatos aceitos: JPG, PNG, WEBP. Tamanho máximo: 5MB. Deixe em branco para manter a imagem atual.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <div id="imagePreview" class="d-none mt-2">
                                <p>Pré-visualização da nova imagem:</p>
                                <img id="preview" src="" alt="Pré-visualização" style="max-width: 300px; max-height: 200px;" class="img-thumbnail">
                            </div>
                        </div>
                        
                        <!-- Botões -->
                        <div class="col-12 mt-4 text-end">
                            <button type="button" class="btn btn-secondary me-2" onclick="window.location.href='interface.php'">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Atualizar Ficha Técnica
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer text-center mt-5">
        <div class="container">
            <p class="mb-0">© <?php echo date('Y'); ?> SENAI - Sistema de Fichas Técnicas de Veículos. Todos os direitos reservados.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Pré-visualização da imagem
            const imagemInput = document.getElementById('imagem');
            const imagePreview = document.getElementById('imagePreview');
            const preview = document.getElementById('preview');
            
            imagemInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        imagePreview.classList.remove('d-none');
                    }
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.classList.add('d-none');
                }
            });
            
            // Validação do formulário
            const form = document.getElementById('carForm');
            const errorContainer = document.getElementById('error-container');
            
            form.addEventListener('submit', function(event) {
                let errors = [];
                const marca = document.getElementById('marca').value.trim();
                const modelo = document.getElementById('modelo').value.trim();
                const ano = document.getElementById('ano').value.trim();
                
                if (!marca) errors.push('A marca é obrigatória.');
                if (!modelo) errors.push('O modelo é obrigatório.');
                if (!ano) errors.push('O ano é obrigatório.');
                
                if (ano && (ano < 1900 || ano > 2099)) {
                    errors.push('O ano deve estar entre 1900 e 2099.');
                }
                
                // Verificar o tamanho da imagem
                if (imagemInput.files.length > 0) {
                    const file = imagemInput.files[0];
                    if (file.size > 5 * 1024 * 1024) {
                        errors.push('A imagem deve ter no máximo 5MB.');
                    }
                }
                
                if (errors.length > 0) {
                    event.preventDefault();
                    errorContainer.innerHTML = errors.map(error => `<p class="mb-0">${error}</p>`).join('');
                    errorContainer.classList.remove('d-none');
                    window.scrollTo(0, 0);
                }
            });
        });
    </script>
</body>
</html>