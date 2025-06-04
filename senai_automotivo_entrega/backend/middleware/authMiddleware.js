const jwt = require("jsonwebtoken");
require("dotenv").config();

const authenticateToken = (req, res, next) => {
  const token = req.cookies.token;

  if (!token) {
    // Se não houver token, permite acesso a rotas públicas (GET fichas)
    // Mas impede acesso a rotas protegidas (POST, PUT, DELETE fichas)
    // A verificação específica se é professor será feita na rota
    // Aqui apenas verificamos se existe um token para rotas que *exigem* login
    if (req.method !== 'GET') {
        return res.status(401).json({ message: "Acesso não autorizado. Faça login como professor." });
    }
    // Permite continuar para rotas GET públicas mesmo sem token
    req.user = null; // Indica que não há usuário logado
    return next(); 
  }

  jwt.verify(token, process.env.JWT_SECRET, (err, user) => {
    if (err) {
      // Token inválido ou expirado
      console.error("Erro na verificação do token:", err);
      // Limpa o cookie inválido
      res.clearCookie("token");
      if (req.method !== 'GET') {
        return res.status(403).json({ message: "Token inválido ou expirado. Faça login novamente." });
      }
      // Permite continuar para rotas GET públicas mesmo com token inválido
      req.user = null;
      return next();
    }
    // Token válido, anexa informações do usuário (professor) à requisição
    req.user = user; // user contém { id, usuario, role: 'professor' }
    next();
  });
};

// Middleware específico para verificar se o usuário é professor
const isProfessor = (req, res, next) => {
    // Primeiro, executa a autenticação geral para garantir que req.user está populado se houver token
    authenticateToken(req, res, () => {
        // Verifica se o usuário está logado e se tem a role 'professor'
        if (!req.user || req.user.role !== 'professor') {
            return res.status(403).json({ message: "Acesso negado. Somente professores podem realizar esta ação." });
        }
        // Usuário é professor, pode prosseguir
        next();
    });
};


module.exports = { authenticateToken, isProfessor };

