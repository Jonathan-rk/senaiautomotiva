# SENAI Automotivo

## Visão Geral

O SENAI Automotivo é um sistema web para cadastro, consulta e gerenciamento de fichas técnicas de veículos, montadoras e tipos de carroceria. O sistema foi desenvolvido para uso interno de professores e alunos do SENAI, permitindo fácil acesso e atualização das informações automotivas.

## Funcionalidades

- Cadastro, edição e exclusão de fichas técnicas de veículos.
- Cadastro, edição e exclusão de montadoras (com imagem).
- Cadastro, edição e exclusão de tipos de carroceria.
- Visualização detalhada das fichas técnicas.
- Geração de PDF das fichas técnicas.
- Controle de acesso por usuário (login obrigatório, perfil de professor).
- Interface responsiva e moderna.

## Como Utilizar

### 1. Instalação

https://estudante-team-xvx59jjh.atlassian.net/wiki/external/MTMwODg3M2E5ZWZkNGMxMTgwY2E0NWQ5NWEyOGM5YTM

- Clone ou extraia o projeto em seu servidor local (ex: XAMPP).
- Crie o banco de dados usando o arquivo `database.sql`.
- Configure o acesso ao banco em `config.php` se necessário.
- Certifique-se de que as extensões PHP necessárias estão habilitadas (PDO, mbstring, etc).

### 2. Acesso

- Acesse `http://localhost/senaiautomotivo` no navegador.
- Faça login com o usuário de demonstração:
  - **Usuário:** prof@senai.com
  - **Senha:** senha123

### 3. Cadastro e Gerenciamento

- Após login, acesse o **Painel do Professor**.
- Cadastre montadoras e carrocerias antes de criar fichas técnicas.
- Para cada ficha técnica, preencha todos os campos obrigatórios e, se desejar, adicione uma imagem do veículo.
- Para excluir qualquer registro, utilize o botão de exclusão e confirme na janela modal.

### 4. Visualização

- As fichas técnicas podem ser visualizadas por qualquer usuário.
- É possível gerar um PDF da ficha técnica para impressão ou download.

### 5. Recomendações de Uso

- Sempre cadastre montadoras e carrocerias antes de cadastrar fichas técnicas.
- Utilize imagens otimizadas para não sobrecarregar o servidor.
- Para maior segurança, altere as credenciais padrão do usuário professor após a instalação.
- Não compartilhe o acesso de professor com terceiros.

## Estrutura de Usuários

- **Professor:** pode cadastrar, editar e excluir fichas, montadoras e carrocerias.
- **Visitante/Aluno:** pode visualizar fichas técnicas, mas não pode editar ou excluir.

## Segurança

- O sistema utiliza sessões para controle de acesso.
- Senhas devem ser armazenadas com hash em ambiente de produção.
- Inputs são sanitizados para evitar SQL Injection e XSS.

## Suporte

Em caso de dúvidas, consulte a equipe de desenvolvimento ou o arquivo `TECNOLOGIAS.md` para detalhes sobre as tecnologias utilizadas.

## Desenvolvedores e Professores responsáveis

Dev
- Jonathan Redmerski Kalinoski
- Amanda Reis Carvalho
- Maria Gabriela Massignan
- Gustavo Joaquim Ferreira do Vale

Professores
- Vitor Fabiano Pruss
- Caíque Batista Rossi Torres
