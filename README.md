Security Lab API — Autenticação e Demonstração de Vulnerabilidades

Este projeto é uma API desenvolvida em PHP + MySQL com foco em dois objetivos principais:

1. Ensino e conscientização sobre segurança em autenticação
2. Demonstrar, de forma prática e controlada, a facilidade em quebrar senhas fracas

Este projeto é exclusivamente educativo.
Ele não incentiva invasões, mas sim mostra como ataques simples funcionam para reforçar a importância de boas práticas.

---

 Objetivo do Projeto

O principal objetivo deste projeto é mostrar que:

- Senhas compostas apenas por números
- Senhas com poucos caracteres
- Senhas comuns (ex: 123456, senha123, admin)
- Senhas previsíveis

Podem ser facilmente descobertas com ataques simples como:

- Ataque de dicionário
- Força bruta limitada
- Exploração de hash fraco (MD5 / SHA1)

Este laboratório simula um ataque fraco e controlado.

Agora imagine um atacante experiente que:

- Utiliza listas com milhões de senhas vazadas
- Usa GPUs para acelerar força bruta
- Explora falhas adicionais do sistema
- Realiza engenharia social
- Executa ataques distribuídos

O que foi demonstrado aqui é apenas o nível mais básico.

---

Conceitos Demonstrados

Fluxo Seguro (Modo Produção)

- Registro com password_hash
- Verificação com password_verify
- Uso de Argon2id ou bcrypt
- Senhas nunca são armazenadas em texto puro
- Não existe “descriptografar senha”

Esse é o padrão correto.

---

## 🧪 Modo Laboratório (Demonstração)

Para fins educativos, o sistema permite:

- Criar hash fraco (MD5 / SHA1)
- Executar ataque de dicionário limitado
- Executar tentativa de força bruta controlada
- Aplicar rate limit básico

Isso mostra como:

- Hash rápido facilita ataque
- Senhas comuns são perigosas
- Combinações numéricas simples são facilmente previsíveis

---

Tecnologias Utilizadas

- PHP 8+
- MySQL
- Servidor embutido do PHP
- Interface web simples para testes
- JavaScript (Fetch API)

---

Estrutura do Projeto

public/
 ├── index.php (API)
 └── test-ui/
      ├── index.html
      ├── explicacao.html
      ├── style.css
      └── script.js
 └── src/    
      ├──AuthController.php
      ├──db.php
      ├──DemoController.php
      ├──Responde.php
      ├──Router.php

---

Como Executar

## 1️⃣ Criar banco de dados

Criar banco `security_lab` e executar as tabelas do projeto.

## 2️⃣ Ajustar conexão

Editar:

src/db.php

## 3️⃣ Rodar servidor

php -S localhost:8000 -t public

## 4️⃣ Acessar interface

http://localhost:8000/test-ui/index.html

---

O que este projeto prova

Este projeto deixa claro que:

- Segurança não depende apenas de tecnologia
- Depende principalmente da qualidade das senhas
- Hash fraco + senha fraca = desastre
- Ataques simples já são suficientes para comprometer dados

Se um ataque básico consegue descobrir a senha em poucos segundos,
imagine um ataque profissional.

---

Aviso Importante

Este laboratório:

- Não executa ataques reais
- Não tenta invadir sistemas
- Não armazena senhas reais em texto
- É limitado e controlado

O foco é educação, conscientização e demonstração técnica para portfólio backend.

---

Autor

Desenvolvido por Felippe Oliveira
Projeto educacional voltado para demonstração de conceitos de segurança e autenticação em backend.
