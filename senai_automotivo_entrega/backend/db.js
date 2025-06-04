const sqlite3 = require('sqlite3').verbose();
const path = require('path');
const fs = require('fs');
require('dotenv').config();

// Define o caminho para o arquivo do banco de dados SQLite
const dbPath = path.resolve(__dirname, '..', 'database', 'senai_automotivo.db');
const dbDir = path.dirname(dbPath);

// Cria o diretório do banco de dados se não existir
if (!fs.existsSync(dbDir)) {
  fs.mkdirSync(dbDir, { recursive: true });
}

// Cria ou abre o banco de dados
const db = new sqlite3.Database(dbPath, (err) => {
  if (err) {
    console.error('Erro ao conectar com o banco de dados SQLite:', err.message);
    process.exit(1);
  } else {
    console.log('Conexão com o banco de dados SQLite estabelecida com sucesso!');
    // Habilita chaves estrangeiras (importante para integridade)
    db.run('PRAGMA foreign_keys = ON;', (pragmaErr) => {
      if (pragmaErr) {
        console.error("Erro ao habilitar foreign keys:", pragmaErr.message);
      }
    });
    // Chama a função para inicializar o schema
    initializeSchema();
  }
});

// Função para ler e executar o schema SQL
const initializeSchema = () => {
  const schemaPath = path.resolve(__dirname, '..', 'database_schema_sqlite.sql');
  fs.readFile(schemaPath, 'utf8', (err, sql) => {
    if (err) {
      console.error('Erro ao ler o arquivo de schema SQL:', err);
      // Não sair necessariamente, pode já existir
      return;
    }

    // Tenta executar o script SQL
    // SQLite executa múltiplos statements separados por ;
    // Precisamos tratar comentários e garantir compatibilidade
    // Simplificação: Remover comentários e executar
    const cleanedSql = sql.replace(/--.*$/gm, '').replace(/\/\*[\s\S]*?\*\//gm, '').trim();
    
    // Verifica se há tabelas antes de tentar criar para evitar erros
    db.get("SELECT name FROM sqlite_master WHERE type='table' AND name='professores'", (err, row) => {
        if (err) {
            console.error("Erro ao verificar tabelas existentes:", err.message);
            return;
        }
        if (!row) { // Se a tabela 'professores' não existe, executa o schema
            console.log("Inicializando schema do banco de dados...");
            db.exec(cleanedSql, (execErr) => {
                if (execErr) {
                    console.error('Erro ao executar o schema SQL:', execErr.message);
                    // Verificar se o erro é por tabela já existente pode ser útil
                    if (!execErr.message.includes('already exists')) {
                       // process.exit(1); // Considerar sair apenas em erros críticos
                    }
                } else {
                    console.log('Schema do banco de dados inicializado com sucesso!');
                    // Adiciona o professor demo após criar as tabelas
                    addDemoProfessorAfterSchema(); 
                }
            });
        } else {
            console.log("Banco de dados já parece estar inicializado (tabela 'professores' encontrada).");
            // Adiciona o professor demo caso não exista, mesmo se schema já existe
            addDemoProfessorAfterSchema(); 
        }
    });
  });
};

// Função para adicionar professor demo (adaptada para SQLite e chamada após schema)
const addDemoProfessorAfterSchema = async () => {
    const bcrypt = require("bcrypt"); // Precisa do bcrypt aqui
    const nome = "Professor SENAI Demo";
    const usuario = "prof@senai.com";
    const senha = "senha123";

    try {
        // Verifica se o usuário já existe
        db.get("SELECT id FROM professores WHERE usuario = ?", [usuario], async (err, row) => {
            if (err) {
                console.error("Erro ao verificar professor demo:", err.message);
                return;
            }
            if (row) {
                console.log(`Professor demo '${usuario}' já existe.`);
                return;
            }

            // Se não existe, cria
            const saltRounds = 10;
            const senhaHash = await bcrypt.hash(senha, saltRounds);
            db.run(
                "INSERT INTO professores (nome, usuario, senha_hash) VALUES (?, ?, ?)",
                [nome, usuario, senhaHash],
                function(insertErr) { // Usar function para ter acesso a this.lastID
                    if (insertErr) {
                        console.error("Erro ao registrar professor demo:", insertErr.message);
                    } else {
                        console.log(`Professor demo '${usuario}' registrado com ID: ${this.lastID}`);
                    }
                }
            );
        });
    } catch (error) {
        console.error("Erro geral ao adicionar professor demo:", error);
    }
};


// Exporta o objeto 'db' para ser usado pelos controllers
// Os controllers precisarão ser adaptados para usar db.all, db.get, db.run etc.
module.exports = db;

