<?php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

require_professor();

$authController = new AuthController();
$user = $authController->getCurrentUser();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Ficha Técnica - SENAI</title>
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
                        <a class="nav-link active" href="nova_ficha.php"><i class="fas fa-plus-circle me-1"></i> Nova Ficha</a>
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
            <h1 class="page-title mb-0">Nova Ficha Técnica</h1>
            <a href="interface.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
        
        <!-- Error Messages -->
        <div id="error-container" class="alert alert-danger d-none" role="alert"></div>
        
        <!-- Form -->
        <div class="card">
            <div class="card-body">
                <form id="carForm" action="/api/create_car.php" method="POST" enctype="multipart/form-data">
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
                                <input type="text" class="form-control" id="marca" name="marca" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label for="modelo" class="form-label">Modelo*</label>
                                <input type="text" class="form-control" id="modelo" name="modelo" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label for="ano" class="form-label">Ano*</label>
                                <input type="number" class="form-control" id="ano" name="ano" min="1900" max="2099" 
                                       value="<?php echo date('Y'); ?>" required>
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
                                       placeholder="Ex: 2.0 Turbo">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="potencia" class="form-label">Potência</label>
                                <input type="text" class="form-control" id="potencia" name="potencia" 
                                       placeholder="Ex: 150 cv @ 5500 rpm">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="torque" class="form-label">Torque</label>
                                <input type="text" class="form-control" id="torque" name="torque" 
                                       placeholder="Ex: 20,4 kgfm @ 2000 rpm">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="consumo" class="form-label">Consumo</label>
                                <input type="text" class="form-control" id="consumo" name="consumo" 
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
                                       placeholder="Ex: Automático de 6 velocidades">
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label for="tracao" class="form-label">Tração</label>
                                <input type="text" class="form-control" id="tracao" name="tracao" 
                                       placeholder="Ex: Dianteira">
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label for="portas" class="form-label">Portas</label>
                                <input type="number" class="form-control" id="portas" name="portas" min="1" max="6">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="porta_malas" class="form-label">Porta-malas (litros)</label>
                                <input type="text" class="form-control" id="porta_malas" name="porta_malas" 
                                       placeholder="Ex: 420 litros">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="peso" class="form-label">Peso (kg)</label>
                                <input type="text" class="form-control" id="peso" name="peso" 
                                       placeholder="Ex: 1250 kg">
                            </div>
                        </div>
                        
                        <!-- Imagem do veículo -->
                        <div class="col-md-12 mt-3 mb-4">
                            <h4><i class="fas fa-image me-2"></i>Imagem do Veículo</h4>
                            <hr>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label for="imagem" class="form-label">Upload de Imagem</label>
                                <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*">
                                <div class="form-text">Formatos aceitos: JPG, PNG, WEBP. Tamanho máximo: 5MB.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <div id="imagePreview" class="d-none mt-2">
                                <p>Pré-visualização:</p>
                                <img id="preview" src="" alt="Pré-visualização" style="max-width: 300px; max-height: 200px;" class="img-thumbnail">
                            </div>
                        </div>
                        
                        <!-- Botões -->
                        <div class="col-12 mt-4 text-end">
                            <button type="button" class="btn btn-secondary me-2" onclick="window.location.href='interface.php'">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Salvar Ficha Técnica
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