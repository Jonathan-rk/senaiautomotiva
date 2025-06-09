# SENAI Automotivo - Sistema de Fichas Técnicas

Sistema web desenvolvido para gerenciamento de fichas técnicas de veículos, voltado para uso educacional no SENAI.

## 📋 Características

- **Interface Responsiva**: Funciona perfeitamente em desktops, tablets e celulares
- **Sistema de Login**: Acesso restrito para professores
- **Gerenciamento de Fichas**: Criação, edição, visualização e exclusão de fichas técnicas
- **Sistema de Categorias**: Organização das fichas por categorias
- **Busca e Filtros**: Localização rápida de veículos por diversos critérios
- **Exportação PDF**: Download das fichas em formato PDF
- **Compartilhamento**: Links diretos para fichas específicas

## 🛠️ Tecnologias Utilizadas

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP 7.4+
- **Banco de Dados**: MySQL 5.7+
- **Bibliotecas**: Font Awesome, jsPDF
- **Servidor**: Apache/Nginx (XAMPP recomendado para desenvolvimento)

## 📦 Instalação

### Pré-requisitos

- XAMPP (ou LAMP/WAMP)
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Navegador web moderno

### Passo a Passo

1. **Clone ou baixe o projeto**
   ```bash
   git clone [url-do-repositorio]
   # ou extraia o arquivo ZIP na pasta htdocs do XAMPP
   ```

2. **Configure o banco de dados**
   - Inicie o XAMPP (Apache e MySQL)
   - Acesse o phpMyAdmin (http://localhost/phpmyadmin)
   - Crie um novo banco de dados chamado `senai_automotivo`
   - Importe o arquivo `database.sql` ou execute o script SQL:

   ```sql
   CREATE DATABASE IF NOT EXISTS senai_automotivo;
   USE senai_automotivo;

   CREATE TABLE IF NOT EXISTS categorias (
       id INT AUTO_INCREMENT PRIMARY KEY,
       nome VARCHAR(255) NOT NULL UNIQUE
   );

   CREATE TABLE IF NOT EXISTS fichas_tecnicas (
       id INT AUTO_INCREMENT PRIMARY KEY,
       marca VARCHAR(255) NOT NULL,
       modelo VARCHAR(255) NOT NULL,
       ano INT NOT NULL,
       versao VARCHAR(255),
       codigo_motor VARCHAR(255),
       tipo_combustivel VARCHAR(255),
       tipo_motor VARCHAR(255),
       cilindrada VARCHAR(255),
       potencia_maxima VARCHAR(255),
       torque_maximo VARCHAR(255),
       numero_valvulas VARCHAR(255),
       injecao_eletronica VARCHAR(255),
       tipo_cambio VARCHAR(255),
       numero_marchas VARCHAR(255),
       suspensoes VARCHAR(255),
       freios VARCHAR(255),
       abs_ebd VARCHAR(255),
       tipo_direcao VARCHAR(255),
       pneus_originais VARCHAR(255),
       comprimento VARCHAR(255),
       largura VARCHAR(255),
       altura VARCHAR(255),
       entre_eixos VARCHAR(255),
       altura_livre_solo VARCHAR(255),
       peso VARCHAR(255),
       velocidade_maxima VARCHAR(255),
       aceleracao VARCHAR(255),
       consumo_urbano VARCHAR(255),
       consumo_rodoviario VARCHAR(255),
       tanque VARCHAR(255),
       porta_malas VARCHAR(255),
       carga_util VARCHAR(255),
       ocupantes VARCHAR(255),
       sistema_injecao VARCHAR(255),
       sonda_lambda VARCHAR(255),
       sensor_fase VARCHAR(255),
       sistema_ignicao VARCHAR(255),
       tipo_ecu VARCHAR(255),
       categoria_id INT,
       FOREIGN KEY (categoria_id) REFERENCES categorias(id)
   );

   CREATE TABLE IF NOT EXISTS usuarios (
       id INT AUTO_INCREMENT PRIMARY KEY,
       username VARCHAR(255) NOT NULL UNIQUE,
       password VARCHAR(255) NOT NULL,
       role VARCHAR(50) NOT NULL
   );

   INSERT IGNORE INTO usuarios (username, password, role) VALUES ('prof@senai.com', 'senha123', 'professor');
   ```

3. **Configure as permissões**
   - Certifique-se de que o Apache tem permissão de leitura/escrita na pasta do projeto

4. **Acesse o sistema**
   - Abra o navegador e vá para: `http://localhost/senai_automotivo`

## 🔐 Credenciais de Acesso

### Professor (Administrador)
- **E-mail**: prof@senai.com
- **Senha**: senha123

## 📱 Estrutura do Projeto

```
senai_automotivo/
├── index.php              # Página inicial
├── fichas.php             # Listagem de fichas técnicas
├── detalhes.php           # Detalhes da ficha técnica
├── login.php              # Página de login
├── logout.php             # Script de logout
├── painel_professor.php   # Painel administrativo
├── criar_ficha.php        # Formulário de criação/edição
├── categorias.php         # Gerenciamento de categorias
├── gerar_pdf.php          # Geração de PDF
├── config.php             # Configurações do sistema
├── style.css              # Estilos CSS
├── script.js              # JavaScript
├── database.sql           # Script do banco de dados
├── images/                # Imagens do sistema
└── README.md              # Este arquivo
```

## 🎯 Funcionalidades

### Para Visitantes
- Visualizar fichas técnicas públicas
- Buscar e filtrar veículos
- Ver detalhes completos dos veículos
- Baixar fichas em PDF
- Compartilhar links de fichas

### Para Professores
- Todas as funcionalidades de visitantes
- Criar novas fichas técnicas
- Editar fichas existentes
- Excluir fichas técnicas
- Gerenciar categorias
- Painel administrativo com estatísticas

## 🔧 Configuração Avançada

### Personalização do Banco de Dados

Edite o arquivo `config.php` para alterar as configurações de conexão:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'senai_automotivo');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Adicionando Novos Usuários

Execute no banco de dados:

```sql
INSERT INTO usuarios (username, password, role) VALUES ('novo@email.com', 'senha', 'professor');
```

### Personalizando Cores

Edite as variáveis CSS no arquivo `style.css`:

```css
:root {
    --senai-blue: #0A3871;
    --senai-red: #E31E24;
    --senai-dark: #1D2939;
}
```

## 📊 Campos das Fichas Técnicas

### Informações Básicas
- Marca, Modelo, Ano, Versão
- Código do motor, Tipo de combustível

### Motorização
- Tipo de motor, Cilindrada, Potência máxima
- Torque máximo, Número de válvulas, Injeção eletrônica

### Transmissão
- Tipo de câmbio, Número de marchas

### Suspensão e Freios
- Suspensões, Freios, ABS/EBD

### Direção e Pneus
- Tipo de direção, Pneus originais

### Dimensões
- Comprimento, Largura, Altura
- Entre-eixos, Altura livre do solo, Peso

### Desempenho e Consumo
- Velocidade máxima, Aceleração
- Consumo urbano/rodoviário, Tanque

### Capacidades
- Porta-malas, Carga útil, Ocupantes

### Sistemas e Eletrônica (Opcional)
- Sistema de injeção, Sonda lambda
- Sensor de fase, Sistema de ignição, Tipo de ECU

## 🎨 Responsividade

O sistema é totalmente responsivo e se adapta a:

- **Desktop**: 1024px+
- **Tablet**: 768px - 1023px
- **Celular**: até 767px

## 🔒 Segurança

- Sessões PHP para controle de acesso
- Sanitização de dados de entrada
- Proteção contra SQL Injection (PDO)
- Validação de formulários

## 🐛 Solução de Problemas

### Erro de Conexão com Banco
1. Verifique se o MySQL está rodando
2. Confirme as credenciais em `config.php`
3. Certifique-se de que o banco `senai_automotivo` existe

### Página em Branco
1. Ative a exibição de erros PHP
2. Verifique os logs do Apache
3. Confirme as permissões de arquivo

### CSS/JS não Carrega
1. Verifique o caminho dos arquivos
2. Confirme as permissões de leitura
3. Limpe o cache do navegador

## 📞 Suporte

Para dúvidas ou problemas:

1. Verifique a documentação acima
2. Consulte os logs de erro
3. Entre em contato com o desenvolvedor

## 📄 Licença

Este projeto foi desenvolvido para fins educacionais no SENAI.

## 🔄 Atualizações

### Versão 1.0
- Sistema básico de fichas técnicas
- Login de professor
- Gerenciamento de categorias
- Interface responsiva
- Exportação PDF

---

**Desenvolvido para SENAI Automotivo** 🚗

