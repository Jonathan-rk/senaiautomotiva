# Tecnologias Utilizadas - SENAI Automotivo

## Backend

- **PHP 7.1+**
  - PDO para acesso ao banco de dados
  - Sessões para autenticação
  - Funções utilitárias para segurança e sanitização

- **MySQL**
  - Estrutura relacional para fichas técnicas, montadoras, carrocerias e usuários

## Frontend

- **HTML5**
- **CSS3**
  - Tailwind CSS (via CDN)
  - Estilos customizados em `css/interface_style.css`
- **JavaScript**
  - Funções para interação (menus, modais, etc.)
  - jsPDF (para geração de PDF no frontend, se necessário)

- **Fontes e Ícones**
  - Font Awesome
  - Bootstrap Icons
## Bibliotecas e Dependências

- **Dompdf**
  - Geração de PDFs a partir de HTML no backend versão 3.0.0
- **php-font-lib** e **php-svg-lib**
  - Suporte a fontes e SVG no Dompdf
- **sabberworm/php-css-parser**
  - Parser CSS para Dompdf

## Outras

- **Composer**
  - Gerenciamento de dependências PHP

## Requisitos do Servidor

- PHP 7.1 ou superior
- Extensões PHP: PDO, mbstring, fileinfo, etc.
- Servidor web Apache/Nginx
- MySQL/MariaDB

## Observações

- O sistema é responsivo e funciona em dispositivos móveis e desktops.
- O upload de imagens é permitido para veículos e montadoras, mas recomenda-se uso de links externos para imagens de montadoras para evitar sobrecarga no servidor.
- O código segue boas práticas de segurança e organização para projetos PHP modernos.
