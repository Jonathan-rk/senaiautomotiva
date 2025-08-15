<?php
session_start();
require_once "config.php";

$pdo = getDBConnection();

// Verificar se o usuário está logado
$isLoggedIn = isLoggedIn();
$userRole = $isLoggedIn ? $_SESSION["user_role"] : null;

// Buscar montadoras para o carrossel
$stmt_montadoras = $pdo->query("SELECT * FROM montadoras ORDER BY id DESC");
$montadoras = $stmt_montadoras->fetchAll(PDO::FETCH_ASSOC);

// Buscar fichas em destaque (as 6 mais recentes com imagem)
$stmt_destaques = $pdo->query("
    SELECT f.*, m.nome as montadoras_nome 
    FROM fichas_tecnicas f 
    LEFT JOIN montadoras m ON f.montadoraS_id = m.id 
    WHERE f.imagem_path IS NOT NULL 
    ORDER BY f.id DESC 
    LIMIT 6
");
$fichas_destaque = $stmt_destaques->fetchAll(PDO::FETCH_ASSOC);

// Se não houver fichas com imagem, buscar as mais recentes sem imagem
if (empty($fichas_destaque)) {
    $stmt_destaques = $pdo->query("
        SELECT f.*, m.nome as montadoras_nome 
        FROM fichas_tecnicas f 
        LEFT JOIN montadoras m ON f.montadoras_id = m.id 
        ORDER BY f.id DESC 
        LIMIT 6
    ");
    $fichas_destaque = $stmt_destaques->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENAI Automotivo - Fichas Técnicas</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/interface_style.css">
</head>
<style>
    .team-section {
  text-align: center;
  padding: 60px 20px;
  background: #fff;
}

.team-section h1 {
  font-size: 36px;
  margin-bottom: 40px;
  color: #333;
}

.team-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 30px;
}

.card {
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
  padding: 20px;
  width: 260px;
  text-align: center;
  transition: transform 0.3s ease;
}

.card:hover {
  transform: translateY(-8px);
}

.card img {
  width: 120px;
  height: 120px;
  object-fit: cover;
  border-radius: 50%;
  margin: 0 auto 15px auto;
  display: block;
}

.card h2 {
  font-size: 20px;
  color: #333;
  margin: 10px 0 5px;
}

.card p {
  font-size: 16px;
  color: #777;
  margin-bottom: 15px;
}

.socials {
  display: flex;
  justify-content: center;
  gap: 15px;
}

.socials a {
  color: #555;
  font-size: 20px;
  transition: color 0.3s ease;
}

.socials a:hover {
  color: #0077b5;
}

/* Responsividade */
@media (max-width: 600px) {
  .card {
    width: 90%;
  }

  .team-section h1 {
    font-size: 28px;
  }
}
</style>
<body class="bg-gray-50 min-h-screen flex flex-col">
     <!-- Navegação -->
     <nav class="navbar">
        <div class="container">
            <div class="navbar-header">
                <a href="index.php">
                   <img src="images/wU7KMxCEL6DS.png" alt="SENAI Logo" class="logo">
                </a>
                
                <div class="menu-desktop">
                    <a href="index.php" class="active">Início</a>
                    <a href="fichas.php">Fichas Técnicas</a>
                    
                    <?php if ($isLoggedIn && $userRole === "professor"): ?>
                        <a href="painel_professor.php">
                            <i class="fas fa-cog icon"></i>Painel
                        </a>
                        <a href="montadoras.php">
                            <i class="fas fa-car icon"></i>Montadoras
                        </a>
                        <a href="carrocerias.php">
                            <i class="fas fa-shapes icon"></i>Carrocerias
                        </a>
                        <a href="logout.php">
                            <i class="fas fa-sign-out-alt icon"></i>Sair
                        </a>
                    <?php else: ?>
                        <a href="login.php">
                            <i class="fas fa-sign-in-alt icon"></i>Login
                        </a>
                    <?php endif; ?>
                </div>
                
                <button class="hamburger" id="hamburger" type="button" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobileMenu" class="menu-mobile hidden">
                <a href="index.php" class="active">Início</a>
                <a href="fichas.php">Fichas Técnicas</a>
                
                <?php if ($isLoggedIn && $userRole === "professor"): ?>
                    <a href="painel_professor.php">
                        <i class="fas fa-cog icon"></i>Painel
                    </a>
                    <a href="montadoras.php">
                        <i class="fas fa-car icon"></i>Montadoras
                    </a>
                    <a href="carrocerias.php">
                        <i class="fas fa-shapes icon"></i>Carrocerias
                    </a>
                    <a href="logout.php">
                        <i class="fas fa-sign-out-alt icon"></i>Sair
                    </a>
                <?php else: ?>
                    <a href="login.php">
                        <i class="fas fa-sign-in-alt icon"></i>Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <section class="team-section flex-1">
    <h1>Equipe de Desenvolvimento</h1>
    <div class="team-container">

      <!-- Desenvolvedor 1 -->
      <div class="card">
        <img src="images/Jonathan Redmerski Kalinoski.jpeg" alt="Dev 1" />
        <h2>Jonathan R. Kalinoski</h2>
        <p>Full Stack e UX/UI</p>
        <div class="socials">
          <a href="https://github.com/Jonathan-rk"><i class="fab fa-github"></i></a>
          <a href="https://www.linkedin.com/in/jonathan-redmerski-kalinoski-7830aa2b0/"><i class="fab fa-linkedin"></i></a>
          <a href="https://www.instagram.com/_jo_kali?igsh=MWdyeWw1bXUxM251aw=="><i class="fab fa-instagram"></i></a>
        </div>
      </div>

      <!-- Desenvolvedor 2 -->
      <div class="card">
        <img src="images/Amanda Reis.jpeg" alt="Dev 2" />
        <h2>Amanda Reis Carvalho</h2>
        <p>Designer</p>
        <div class="socials">
          <a href="https://github.com/Amanda-reiss"><i class="fab fa-github"></i></a>
          <a href="#"><i class="fab fa-linkedin"></i></a>
          <a href="https://www.instagram.com/reiss.amandaaa?igsh=MWdyeWw1bXUxM251aw=="><i class="fab fa-instagram"></i></a>
        </div>
      </div>

      <!-- Desenvolvedor 3 -->
      <div class="card">
        <img src="images/Maria Gabriela.jpeg" alt="Dev 3" />
        <h2>Maria G. Massignan</h2>
        <p>Front-End</p>
        <div class="socials">
          <a href="https://github.com/mgmassignan"><i class="fab fa-github"></i></a>
          <a href="https://www.linkedin.com/in/maria-gabriela-massignan-28a673339/"><i class="fab fa-linkedin"></i></a>
          <a href="https://www.instagram.com/mgmassignan?igsh=MWdyeWw1bXUxM251aw=="><i class="fab fa-instagram"></i></a>
        </div>
      </div>

      <!-- Desenvolvedor 4 -->
      <div class="card">
        <img src="images/Gustavo Joaquin.jpeg" alt="Dev 4" />
        <h2>Gustavo J. F. do Vale</h2>
        <p>Front-End</p>
        <div class="socials">
          <a href="https://github.com/gustavojfv26"><i class="fab fa-github"></i></a>
          <a href="#"><i class="fab fa-linkedin"></i></a>
          <a href="https://www.instagram.com/guh_jfv/"><i class="fab fa-instagram"></i></a>
        </div>
      </div>

    </div>
  </section>

    <!-- Footer -->
    <footer class="footer mt-auto">
        <div class="footer-container">
            <div class="footer-main-content">
                <div class="footer-logo-section">
                    <img src="images/wU7KMxCEL6DS.png" alt="SENAI Logo" class="footer-logo">
                    <p class="footer-copyright">SENAI - Serviço Nacional de Aprendizagem Industrial</p>
                </div>
                <div class="footer-social-links">
                    <!-- Social Media Links -->
                    <a href="https://www.facebook.com/senaijoinvilleoficial" target="_blank" rel="noopener noreferrer" class="footer-social-link">
                        <i class="fab fa-facebook-f footer-social-icon"></i>
                    </a>
                    <a href="https://www.instagram.com/senai.joinvillesc/" target="_blank" rel="noopener noreferrer" class="footer-social-link">
                        <i class="fab fa-instagram footer-social-icon"></i>
                    </a>
                    <a href="https://www.linkedin.com/company/senai-sc" target="_blank" rel="noopener noreferrer" class="footer-social-link">
                        <i class="fab fa-linkedin-in footer-social-icon"></i>
                    </a>
                    <a href="https://www.youtube.com/@senaibrasil" target="_blank" rel="noopener noreferrer" class="footer-social-link">
                        <i class="fab fa-youtube footer-social-icon"></i>
                    </a>
                </div>
            </div>
            <div class="footer-bottom-section">
                <div class="footer-bottom-content">
                    <div class="footer-links-section">
                        <a href="desenvolvedores.php" class="footer-link margin-right-4">Desenvolvedores</a>
                        <a href="https://fiesc.com.br/pt-br/politica-de-privacidade#:~:text=O%20Sistema%20FIESC%2C%20composto%20pela,e%20sites%20do%20Sistema%20FIESC" class="footer-link margin-right-4">Política de Privacidade</a>
                       <a href="https://ava.sesisenai.org.br/local/faqs/viewall.php" class="footer-link">Termos de Uso</a>
                    </div>
                    <div class="footer-text-left">
                        <p class="footer-copyright">&copy; <?php echo date("Y"); ?> SENAI Automotivo. Todos os direitos reservados.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script src="js/script.js"></script>
</body>
</html>

