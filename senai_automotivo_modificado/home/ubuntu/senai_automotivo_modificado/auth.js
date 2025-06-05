// auth.js

// --- Configuração de Autenticação ---
// ATENÇÃO: Armazenar credenciais diretamente no código não é seguro.
// Para um ambiente real, use um backend seguro.
// Para este exercício, usaremos um hash SHA-256 simples gerado previamente.

// Usuário e hash SHA-256 da senha "senha123" (exemplo)
// Você pode gerar hashes SHA-256 online ou usando ferramentas.
// Exemplo de hash para "senha123": a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3
const PROFESSOR_USERNAME = "professor";
const PROFESSOR_PASSWORD_HASH = "a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3";

// --- Funções de Hash (Usando SubtleCrypto API) ---
async function sha256(message) {
    // encode as UTF-8
    const msgBuffer = new TextEncoder().encode(message);
    // hash the message
    const hashBuffer = await crypto.subtle.digest("SHA-256", msgBuffer);
    // convert ArrayBuffer to Array
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    // convert bytes to hex string
    const hashHex = hashArray.map(b => b.toString(16).padStart(2, "0")).join("");
    return hashHex;
}

// --- Funções de Autenticação ---

async function handleLogin(event) {
    event.preventDefault();
    const usernameInput = document.getElementById("username");
    const passwordInput = document.getElementById("password");
    const errorMessage = document.getElementById("loginErrorMessage");

    const username = usernameInput.value.trim();
    const password = passwordInput.value;

    if (!username || !password) {
        errorMessage.textContent = "Por favor, preencha usuário e senha.";
        errorMessage.classList.remove("hidden");
        return;
    }

    const enteredPasswordHash = await sha256(password);

    if (username === PROFESSOR_USERNAME && enteredPasswordHash === PROFESSOR_PASSWORD_HASH) {
        // Login bem-sucedido
        localStorage.setItem("professorLoggedIn", "true");
        errorMessage.classList.add("hidden");
        updateLoginStatus();
        showPage("fichas"); // Ou redirecionar para a área do professor
        // Adicionar lógica para mostrar a seção do professor aqui (próximo passo)
        showProfessorSection();
    } else {
        // Login falhou
        errorMessage.textContent = "Usuário ou senha inválidos.";
        errorMessage.classList.remove("hidden");
        passwordInput.value = ""; // Limpa o campo de senha
    }
}

function handleLogout() {
    localStorage.removeItem("professorLoggedIn");
    updateLoginStatus();
    hideProfessorSection();
    showPage("home"); // Volta para a página inicial
}

function checkLoginStatus() {
    const isLoggedIn = localStorage.getItem("professorLoggedIn") === "true";
    updateLoginStatusUI(isLoggedIn);
    if (isLoggedIn) {
        showProfessorSection();
    } else {
        hideProfessorSection();
    }
}

function updateLoginStatus() {
    const isLoggedIn = localStorage.getItem("professorLoggedIn") === "true";
    updateLoginStatusUI(isLoggedIn);
}

function updateLoginStatusUI(isLoggedIn) {
    const loginLinkDesktop = document.getElementById("loginLinkDesktop");
    const loginLinkMobile = document.getElementById("loginLinkMobile");
    const settingsDropdownDesktopButton = document.getElementById("settingsDropdownDesktopButton");
    const settingsDropdownMobileButton = document.getElementById("settingsDropdownMobileButton");

    if (isLoggedIn) {
        if (loginLinkDesktop) {
            loginLinkDesktop.innerHTML = `<i class="fas fa-sign-out-alt mr-2"></i>Sair`;
            loginLinkDesktop.onclick = handleLogout;
        }
        if (loginLinkMobile) {
            loginLinkMobile.innerHTML = `<i class="fas fa-sign-out-alt mr-2"></i>Sair`;
            loginLinkMobile.onclick = handleLogout;
        }
        // Opcional: Esconder o botão de Configurações se o dropdown só tiver Sair
        // if (settingsDropdownDesktopButton) settingsDropdownDesktopButton.classList.add('hidden');
        // if (settingsDropdownMobileButton) settingsDropdownMobileButton.classList.add('hidden');

    } else {
        if (loginLinkDesktop) {
            loginLinkDesktop.innerHTML = `<i class="fas fa-sign-in-alt mr-2"></i>Login`;
            loginLinkDesktop.onclick = () => showPage("login");
        }
        if (loginLinkMobile) {
            loginLinkMobile.innerHTML = `<i class="fas fa-sign-in-alt mr-2"></i>Login`;
            loginLinkMobile.onclick = () => showPage("login");
        }
        // Opcional: Mostrar o botão de Configurações
        // if (settingsDropdownDesktopButton) settingsDropdownDesktopButton.classList.remove('hidden');
        // if (settingsDropdownMobileButton) settingsDropdownMobileButton.classList.remove('hidden');
    }
}

// --- Funções para mostrar/esconder seção do professor (serão implementadas depois) ---
function showProfessorSection() {
    console.log("Mostrando seção do professor...");
    const professorSection = document.getElementById('professorSection');
    if (professorSection) {
        professorSection.classList.remove('hidden');
    }
    // Adicionar lógica para mostrar botões de Adicionar/Editar/Excluir Ficha
    // Adicionar lógica para mostrar botão de Adicionar Categoria
}

function hideProfessorSection() {
    console.log("Escondendo seção do professor...");
    const professorSection = document.getElementById('professorSection');
    if (professorSection) {
        professorSection.classList.add('hidden');
    }
     // Adicionar lógica para esconder botões de Adicionar/Editar/Excluir Ficha
    // Adicionar lógica para esconder botão de Adicionar Categoria
}


// --- Inicialização ---
document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("loginForm");
    if (loginForm) {
        loginForm.addEventListener("submit", handleLogin);
    }
    checkLoginStatus(); // Verifica o status de login ao carregar a página
});

