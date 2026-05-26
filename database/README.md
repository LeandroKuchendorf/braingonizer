# 🗄️ Banco de Dados — Braingonizer

Este diretório contém os schemas SQL necessários para configurar o banco de dados do Braingonizer.

---

## 📋 Ordem de Execução

Execute os arquivos SQL **nesta ordem** para evitar erros de chave estrangeira:

| Ordem | Arquivo | Descrição |
|---|---|---|
| 1 | `idea_categories.sql` | Tabela global de categorias |
| 2 | `link_categories.sql` | Tabela de categorias de links |
| 3 | `notes.sql` | Tabela de notas |
| 4 | `tasks.sql` | Tabela de projetos e tarefas (inclui `projects`) |
| 5 | `ideas.sql` | Tabela de ideias (FK → categories) |
| 6 | `files.sql` | Tabela de arquivos |
| 7 | `work_tasks.sql` | Tabela de tarefas do Workonizer |
| 8 | `work_documents.sql` | Tabela de documentos do Workonizer |

> ⚠️ Os arquivos `tasks.sql` e `ideas.sql` dependem de outras tabelas. Siga a ordem acima.

---

## 🧩 Estrutura das Tabelas

### Braingonizer

**`categories`** — Categorias globais (usadas por ideias e links)
- `id`, `name`, `color`, `created_at`

**`notes`** — Anotações pessoais
- `id`, `title`, `content`, `tags`, `created_at`, `updated_at`

**`tasks`** — Tarefas (vinculadas a projetos)
- `id`, `title`, `description`, `project_id` (FK), `priority`, `status`, `due_date`, `tags`, `created_at`, `updated_at`

**`projects`** — Projetos pessoais (definido dentro de `tasks.sql`)
- `id`, `name`, `description`, `color`, `created_at`, `updated_at`

**`ideas`** — Ideias e conceitos
- `id`, `title`, `description`, `category_id` (FK), `priority`, `status`, `created_at`, `updated_at`

**`files`** — Metadados de arquivos
- `id`, `name`, `description`, `category`, `file_path`, `file_size`, `mime_type`, `tags`, `created_at`, `updated_at`

### Workonizer

**`work_tasks`** — Tarefas profissionais
- `id`, `title`, `description`, `priority`, `status`, `due_date`, `tags`, `created_at`, `updated_at`

**`work_documents`** — Documentos de trabalho
- `id`, `title`, `content`, `category_id` (FK), `tags`, `created_at`, `updated_at`

---

## 🔗 Sistema de Tags

As tags são armazenadas como IDs separados por vírgula (máximo 3 por item) diretamente nas tabelas. Não há tabela de junção separada — é uma escolha arquitetural intencional para simplicidade.

Exemplo: `tags = "1,5,12"` → IDs das tags associadas ao item.

---

## ⚙️ Como Executar

### Via phpMyAdmin
1. Acesse o phpMyAdmin
2. Crie um banco de dados novo
3. Selecione o banco criado
4. Clique em **Importar** e selecione cada arquivo SQL na ordem indicada acima

### Via linha de comando (MySQL CLI)
```bash
mysql -u seu_usuario -p nome_do_banco < database/idea_categories.sql
mysql -u seu_usuario -p nome_do_banco < database/link_categories.sql
mysql -u seu_usuario -p nome_do_banco < database/notes.sql
mysql -u seu_usuario -p nome_do_banco < database/tasks.sql
mysql -u seu_usuario -p nome_do_banco < database/ideas.sql
mysql -u seu_usuario -p nome_do_banco < database/files.sql
mysql -u seu_usuario -p nome_do_banco < database/work_tasks.sql
mysql -u seu_usuario -p nome_do_banco < database/work_documents.sql
```
