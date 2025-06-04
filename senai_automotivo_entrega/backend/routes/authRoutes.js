const express = require("express");
const { login, logout, checkSession } = require("../controllers/authController");
const { authenticateToken } = require("../middleware/authMiddleware"); // Para checkSession

const router = express.Router();

router.post("/login", login);
router.post("/logout", logout);
// Rota para verificar se o usuário está logado (útil ao carregar o frontend)
router.get("/check-session", authenticateToken, checkSession); 

module.exports = router;

