// auth.js - Autenticação do professor com senha criptografada e localStorage

// Função para criar hash SHA-256 da senha
async function sha256(message) {
    // Codificar a mensagem como bytes
    const msgBuffer = new TextEncoder().encode(message);
    // Hash da mensagem
    const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
    // Converter para array de bytes
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    // Converter bytes para string hexadecimal
    const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    return hashHex;
}

// Credenciais do professor (em produção, isso seria armazenado no servidor)
const PROFESSOR_USERNAME = "professor";
// Senha: senha123 (em produção, apenas o hash seria armazenado)
const PROFESSOR_PASSWORD_HASH = "67f942aa0e7007c19a0e34f5f05d0865bc5ad3c4255a536c1394c77b8a4a2698";

// Verificar se o usuário está logado ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    checkLoginStatus();
});

// Verificar status de login
function checkLoginStatus() {
    const isLoggedIn = localStorage.getItem("professorLoggedIn") === "true";
    
    // Atualizar elementos da interface com base no status de login
    updateLoginUI(isLoggedIn);
    
    // Se estiver logado, mostrar a área do professor
    if (isLoggedIn && window.location.hash === "#professor") {
        showPage('professor');
    }
}

// Atualizar a interface com base no status de login
function updateLoginUI(isLoggedIn) {
    // Elementos de login no menu desktop e mobile
    const loginLinkDesktop = document.querySelector('a[onclick="showPage(\'login\')"]');
    const loginLinkMobile = document.querySelector('#mobileMenu a[onclick="showPage(\'login\')"]');
    
    if (isLoggedIn) {
        // Mudar texto e ação para "Sair"
        if (loginLinkDesktop) {
            loginLinkDesktop.innerHTML = '<i class="fas fa-sign-out-alt mr-2"></i>Sair';
            loginLinkDesktop.setAttribute('onclick', 'logout()');
        }
        
        if (loginLinkMobile) {
            loginLinkMobile.innerHTML = '<i class="fas fa-sign-out-alt mr-2"></i>Sair';
            loginLinkMobile.setAttribute('onclick', 'logout()');
        }
        
        // Adicionar link para área do professor no menu
        addProfessorMenuLink();
    } else {
        // Restaurar texto e ação para "Login"
        if (loginLinkDesktop) {
            loginLinkDesktop.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i>Login';
            loginLinkDesktop.setAttribute('onclick', 'showPage(\'login\')');
        }
        
        if (loginLinkMobile) {
            loginLinkMobile.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i>Login';
            loginLinkMobile.setAttribute('onclick', 'showPage(\'login\')');
        }
        
        // Remover link para área do professor
        removeProfessorMenuLink();
    }
}

// Adicionar link para área do professor no menu
function addProfessorMenuLink() {
    // Menu desktop
    const desktopMenu = document.querySelector('.md\\:flex.items-center.space-x-6');
    if (desktopMenu && !document.getElementById('professorLinkDesktop')) {
        const professorLink = document.createElement('a');
        professorLink.href = "#";
        professorLink.id = "professorLinkDesktop";
        professorLink.className = "text-gray-700 hover:text-blue-600 transition-colors";
        professorLink.setAttribute('onclick', 'showPage(\'professor\')');
        professorLink.innerHTML = '<i class="fas fa-chalkboard-teacher mr-2"></i>Área do Professor';
        desktopMenu.appendChild(professorLink);
    }
    
    // Menu mobile
    const mobileMenu = document.getElementById('mobileMenu');
    if (mobileMenu && !document.getElementById('professorLinkMobile')) {
        const professorLink = document.createElement('a');
        professorLink.href = "#";
        professorLink.id = "professorLinkMobile";
        professorLink.className = "block py-2 text-gray-700";
        professorLink.setAttribute('onclick', 'showPage(\'professor\')');
        professorLink.innerHTML = '<i class="fas fa-chalkboard-teacher mr-2"></i>Área do Professor';
        mobileMenu.appendChild(professorLink);
    }
}

// Remover link para área do professor do menu
function removeProfessorMenuLink() {
    const desktopLink = document.getElementById('professorLinkDesktop');
    if (desktopLink) {
        desktopLink.remove();
    }
    
    const mobileLink = document.getElementById('professorLinkMobile');
    if (mobileLink) {
        mobileLink.remove();
    }
}

// Função de login
async function handleLogin(event) {
    event.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    // Verificar credenciais
    if (username === PROFESSOR_USERNAME) {
        // Calcular hash da senha fornecida
        const passwordHash = await sha256(password);
        
        if (passwordHash === PROFESSOR_PASSWORD_HASH) {
            // Login bem-sucedido
            localStorage.setItem("professorLoggedIn", "true");
            updateLoginUI(true);
            showPage('professor');
            return;
        }
    }
    
    // Login falhou
    alert("Usuário ou senha incorretos. Tente novamente.");
}

// Função de logout
function logout() {
    localStorage.removeItem("professorLoggedIn");
    updateLoginUI(false);
    showPage('home');
}
