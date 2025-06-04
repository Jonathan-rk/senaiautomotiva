const express = require("express");
const cookieParser = require("cookie-parser");
const dotenv = require("dotenv");
const path = require("path");

dotenv.config();

const authRoutes = require("./routes/authRoutes"); // Reintroduzindo authRoutes
const fichaRoutes = require("./routes/fichaRoutes");

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(cookieParser(process.env.COOKIE_SECRET));

// Servir arquivos estáticos do frontend
const frontendPath = path.join(__dirname, "../senai_automotivo/senai_automotivo");
app.use(express.static(frontendPath));

// Rotas da API
app.use("/api/auth", authRoutes); // Reintroduzindo uso de authRoutes
app.use("/api/fichas", fichaRoutes);

// Rota principal para servir o index.html (Temporariamente comentada)
// app.get("*", (req, res) => {
//   res.sendFile(path.join(frontendPath, "index.html"));
// });

// Rota raiz simples para teste (Manter por enquanto para garantir que o servidor suba)
app.get("/", (req, res) => {
  // Tenta servir o index.html da raiz estática
  res.sendFile(path.join(frontendPath, "index.html"), (err) => {
      if (err) {
          console.error("Erro ao enviar index.html:", err);
          res.status(500).send("Erro ao carregar a página inicial.");
      }
  });
});


// Iniciar o servidor
app.listen(PORT, () => {
  console.log(`Servidor rodando na porta ${PORT}`);
});

