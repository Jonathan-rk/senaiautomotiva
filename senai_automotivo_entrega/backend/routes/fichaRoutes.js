const express = require("express");
const {
  getAllFichas,
  getFichaById,
  createFicha,
  updateFicha,
  deleteFicha
} = require("../controllers/fichaController");
const { authenticateToken, isProfessor } = require("../middleware/authMiddleware");

const router = express.Router();

// Rotas Públicas (acessíveis por todos, incluindo alunos/visitantes)
router.get("/", authenticateToken, getAllFichas); // authenticateToken anexa req.user se logado, mas não bloqueia
router.get("/:id", authenticateToken, getFichaById); // authenticateToken anexa req.user se logado, mas não bloqueia

// Rotas Protegidas (acessíveis apenas por professores logados)
// O middleware isProfessor já inclui a verificação do authenticateToken
router.post("/", isProfessor, createFicha);       // Criar nova ficha
router.put("/:id", isProfessor, updateFicha);     // Atualizar ficha existente
router.delete("/:id", isProfessor, deleteFicha); // Excluir ficha

module.exports = router;

