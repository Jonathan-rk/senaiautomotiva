const db = require("../db"); // Changed from pool to db (SQLite)
const bcrypt = require("bcrypt");
const jwt = require("jsonwebtoken");
require("dotenv").config();

// Removed registerProfessor and addDemoProfessor functions - handled in db.js

// Função para login (adaptada para SQLite)
const login = async (req, res) => {
  const { username, password } = req.body;

  if (!username || !password) {
    return res.status(400).json({ message: "Usuário e senha são obrigatórios." });
  }

  try {
    // Busca o professor pelo usuário usando db.get
    db.get("SELECT * FROM professores WHERE usuario = ?", [username], async (err, professor) => {
      if (err) {
        console.error("Erro ao buscar professor no login (SQLite):", err.message);
        return res.status(500).json({ message: "Erro interno no servidor durante o login." });
      }

      if (!professor) {
        // Usuário não encontrado
        return res.status(401).json({ message: "Usuário ou senha inválidos." });
      }

      // Compara a senha fornecida com o hash armazenado
      try {
        const match = await bcrypt.compare(password, professor.senha_hash);

        if (!match) {
          // Senha não confere
          return res.status(401).json({ message: "Usuário ou senha inválidos." });
        }

        // Gerar token JWT
        const payload = {
          id: professor.id,
          usuario: professor.usuario,
          role: "professor" // Define a role como professor
        };

        const token = jwt.sign(payload, process.env.JWT_SECRET, { expiresIn: "1h" }); // Token expira em 1 hora

        // Define o cookie com o token
        res.cookie("token", token, {
          httpOnly: true, // Impede acesso via JavaScript no cliente
          secure: process.env.NODE_ENV === "production", // Usar true em produção (HTTPS)
          sameSite: "strict", // Ajuda a prevenir CSRF
          maxAge: 3600000 // 1 hora em milissegundos
          // path: "/" // Opcional: define o caminho do cookie
        });

        // Retorna sucesso e informações básicas do usuário (sem a senha)
        res.status(200).json({ 
            message: "Login bem-sucedido!", 
            user: { 
                id: professor.id, 
                nome: professor.nome, 
                usuario: professor.usuario 
            } 
        });

      } catch (compareError) {
        console.error("Erro ao comparar senha (bcrypt):", compareError);
        res.status(500).json({ message: "Erro interno no servidor durante a autenticação." });
      }
    });

  } catch (error) {
    // Este catch pode não ser alcançado devido à natureza assíncrona do db.get,
    // mas mantido por segurança.
    console.error("Erro inesperado no login:", error);
    res.status(500).json({ message: "Erro interno inesperado no servidor durante o login." });
  }
};

// Função para logout (sem alterações, não acessa DB)
const logout = (req, res) => {
  res.clearCookie("token", { 
      httpOnly: true, 
      secure: process.env.NODE_ENV === "production", 
      sameSite: "strict" 
      // path: "/" 
  });
  res.status(200).json({ message: "Logout realizado com sucesso." });
};

// Função para verificar o status da sessão (sem alterações, não acessa DB diretamente)
const checkSession = (req, res) => {
    if (req.user && req.user.role === 'professor') {
        res.status(200).json({ 
            isLoggedIn: true, 
            user: { 
                id: req.user.id, 
                usuario: req.user.usuario,
                role: req.user.role
            } 
        });
    } else {
        res.status(200).json({ isLoggedIn: false, user: null });
    }
};

module.exports = { login, logout, checkSession };

