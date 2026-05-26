<div align="center">

# 🧠 Braingonizer

**Plataforma pessoal de organização mental e produtividade**

*Construída para resolver problemas reais do dia a dia — organizar pensamentos, ideias e informações de forma centralizada.*

[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES6%2B-F7DF1E?style=flat-square&logo=javascript&logoColor=black)](https://developer.mozilla.org/docs/Web/JavaScript)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat-square&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-green?style=flat-square)](LICENSE)

</div>

---

## 🎯 Sobre o Projeto

O Braingonizer nasceu de uma necessidade real: minha mente acumula informações, ideias e tarefas de forma caótica, e eu precisava de uma ferramenta **feita sob medida para o meu jeito de pensar**.

Este projeto serve a dois propósitos:

- **Ferramenta pessoal** — Uma plataforma construída para organizar meu cérebro e meu fluxo de trabalho do dia a dia
- **Sandbox de aprendizado** — Um laboratório para experimentar tecnologias, padrões e arquiteturas novas

> ⚠️ **Uso pessoal:** O Braingonizer é uma ferramenta para uso local e pessoal, sem sistema de autenticação por design.

---

## ✨ Funcionalidades

O projeto é dividido em dois módulos principais:

### 🧠 Braingonizer *(organização pessoal)*
| Módulo | Descrição |
|---|---|
| 📋 **Dashboard** | Hub central com visão geral de todos os módulos |
| 💡 **Ideias** | Capture e organize pensamentos e ideias com categorias |
| 📝 **Notas** | Crie e mantenha anotações pessoais com suporte a tags |
| ⏰ **Lembretes** | Gerencie lembretes e compromissos |
| ✅ **Tarefas** | Controle to-dos e ações com prioridades |
| 🔗 **Links** | Salve e categorize links úteis |
| 📁 **Arquivos** | Gerencie e rastreie arquivos com metadados |
| 🔬 **Pesquisas** | Organize pesquisas e referências |
| 🏷️ **Tags** | Sistema global de tags para organização cruzada |

### 💼 Workonizer *(organização profissional)*
| Módulo | Descrição |
|---|---|
| 📊 **Dashboard** | Visão geral do ambiente de trabalho |
| 📁 **Projetos** | Gerencie projetos profissionais |
| ✅ **Tarefas** | Tarefas com prioridade e status |
| 📄 **Documentos** | Crie e edite documentos de trabalho |

---

## 🛠️ Tecnologias

| Tecnologia | Uso |
|---|---|
| **PHP 7.4+** | Backend, server-side rendering, lógica de negócio |
| **JavaScript (ES6+)** | Interatividade client-side, requisições AJAX |
| **MySQL 8.0+** | Banco de dados relacional |
| **HTML5 / CSS3** | Estrutura e estilização |
| **Bootstrap 5** | Componentes UI e layout responsivo |
| **Axios** | Requisições HTTP assíncronas |
| **Font Awesome** | Ícones |

> Stack vanilla clássica — sem frameworks como React ou Next.js. Controle total do código.

---

## 🏗️ Arquitetura do Projeto

```
braingonizer/
├── public/                          # Web root (aponte o servidor aqui)
│   ├── index.php                    # Página de seleção de app
│   ├── braingonizer/                # Módulos do Braingonizer
│   │   ├── dashboard.php
│   │   ├── ideas.php
│   │   ├── notes.php
│   │   ├── tasks.php
│   │   ├── reminders.php
│   │   ├── links.php
│   │   ├── files.php
│   │   ├── researches.php
│   │   ├── tags.php
│   │   ├── categories.php
│   │   └── settings.php
│   ├── workonizer/                  # Módulos do Workonizer
│   │   ├── dashboard.php
│   │   ├── projects.php
│   │   ├── tasks.php
│   │   └── documents.php
│   ├── includes/                    # Componentes globais reutilizáveis
│   │   ├── global_header.php
│   │   ├── global_footer.php
│   │   ├── braingonizer/            # Sidebars e includes específicos
│   │   └── workonizer/
│   ├── assets/
│   │   ├── css/                     # Folhas de estilo
│   │   └── images/                  # Imagens e favicon
│   └── src/
│       ├── db_connection.php        # Conexão com banco de dados (usa .env)
│       ├── helpers.php              # Funções utilitárias globais
│       ├── js/                      # JavaScript por módulo
│       │   ├── braingonizer/
│       │   └── workonizer/
│       └── helpers/                 # Helpers PHP por módulo
│           ├── braingonizer/
│           └── workonizer/
├── database/                        # Schemas SQL
├── .env.example                     # Modelo de configuração (copie para .env)
├── .gitignore
├── LICENSE
└── README.md
```

### Padrão Arquitetural

Cada módulo segue o mesmo padrão:
- **`modulo.php`** — Camada de apresentação (thin controller)
- **`modulo_helpers.php`** — Toda a lógica de negócio + handlers de requisições POST
- **`modulo.js`** — Interatividade client-side com Axios

---

## 🚀 Instalação e Configuração

### Pré-requisitos

- PHP 7.4 ou superior
- MySQL 8.0 ou superior
- Servidor web (Apache, Nginx, ou PHP built-in server)

### Passo a Passo

**1. Clone o repositório**
```bash
git clone https://github.com/LeandroKuchendorf/braingonizer.git
cd braingonizer
```

**2. Configure as variáveis de ambiente**
```bash
cp .env.example .env
```

Edite o arquivo `.env` com suas credenciais:
```env
DB_HOST=localhost
DB_NAME=nome_do_seu_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

**3. Configure o banco de dados**

Crie um banco de dados MySQL e execute os schemas em ordem:
```bash
# No MySQL ou phpMyAdmin, execute os arquivos da pasta database/
# Consulte database/README.md para a ordem correta
```

**4. Configure o servidor web**

Aponte o document root do seu servidor para a pasta `public/`.

Exemplo com PHP built-in server (desenvolvimento):
```bash
php -S localhost:8080 -t public/
```

**5. Acesse o projeto**
```
http://localhost:8080
```

---

## 🗺️ Roadmap

Funcionalidades planejadas para versões futuras:

- [ ] Melhorias de UI/UX e design responsivo
- [ ] Suporte mobile / PWA
- [ ] Integração com IA para organização automática
- [ ] Sincronização em nuvem
- [ ] Sistema de busca global
- [ ] Refatoração da arquitetura backend
- [ ] API REST documentada
- [ ] Sistema de autenticação (para uso em servidor)

---

## 🧪 Filosofia de Desenvolvimento

- **Vanilla first** — Stack clássica para máximo controle e aprendizado
- **Desenvolvimento iterativo** — Funcionalidades adicionadas conforme a necessidade real
- **Personal first** — Construído para o meu fluxo de trabalho, mas desenhado para ser adaptável
- **Aprender fazendo** — Experimentando diferentes abordagens, padrões e arquiteturas

---

## 📄 Licença

Distribuído sob a licença MIT. Veja [LICENSE](LICENSE) para mais informações.

---

<div align="center">

Feito com 🧠 por **Leandro Kuchendorf**

</div>
