# Plano de Implementação: CRM Kanban WhatsApp & Evolution API

Este documento detalha as etapas para a construção do CRM Full Stack utilizando Next.js 14/15, Supabase e Evolution API.

## 🛠️ Tecnologias
- **Frontend**: Next.js (App Router), TypeScript, Tailwind CSS, Lucide React.
- **Backend**: Supabase (Auth, Database, Realtime).
- **Integração**: Evolution API.
- **Interatividade**: `@hello-pangea/dnd` para o Kanban.

---

## 📅 Cronograma de Execução

### Fase 1: Infraestrutura e Banco de Dados (Supabase)
- [ ] Criar tabelas no Supabase:
    - `instances`: Configurações da Evolution API (link, key, token).
    - `kanban_columns`: Colunas dinâmicas do board.
    - `leads`: Dados do contato (nome, telefone, foto, etc).
    - `messages`: Histórico de mensagens (opcional, dependendo do cache).
- [ ] Configurar RLS (Row Level Security) e Realtime para as tabelas `leads` e `kanban_columns`.

### Fase 2: Gestão de Instâncias (`/app/settings`)
- [ ] Criar página de configurações para cadastrar e listar instâncias.
- [ ] Implementar teste de conexão com a Evolution API.
- [ ] Salvar credenciais de forma segura.

### Fase 3: Kanban Board (`/app/kanban`)
- [ ] Implementar a estrutura visual do Kanban com scroll horizontal.
- [ ] Integrar `@hello-pangea/dnd` para movimentação dos cards.
- [ ] Lógica de persistência: Ao mover o card, atualizar o `column_id` no Supabase.
- [ ] Exibir labels de instância e fotos de perfil (via Evolution API).

### Fase 4: Central de Chat (`/app/chat`)
- [ ] Lista de conversas com filtro por instância.
- [ ] Janela de chat estilo WhatsApp Web.
- [ ] Sistema de Macros: Inserir textos pré-definidos com `/`.
- [ ] Realtime: Novas mensagens devem aparecer instantaneamente.

### Fase 5: Disparo em Massa (Bulk)
- [ ] Interface para upload de lista ou seleção de leads.
- [ ] Lógica de fila com `setTimeout` randômico (5-15s).
- [ ] Barra de progresso visual.

---

## 🏗️ Estrutura de Arquivos

```text
/app
  /kanban
    page.tsx       <- Main Kanban Board
    column.tsx     <- Componente de Coluna
    card.tsx       <- Componente de Lead
  /chat
    page.tsx       <- Multi-instance Chat
    chat-window.tsx
    sidebar-chats.tsx
  /settings
    page.tsx       <- Instance Management
/lib
  supabase.ts      <- Client config
  evolution.ts     <- Helper para chamadas da API
/hooks
  useEvolution.ts  <- Hook para buscar instâncias e conversas
  useKanban.ts     <- Hook para lógica de drag and drop
/components
  Sidebar.tsx      <- Navegação atualizada
```

---

## 🚀 Próximos Passos
1. Instalar dependências necessárias.
2. Executar o SQL no Supabase para criar a estrutura de tabelas.
3. Iniciar pela página de configurações (`/settings`).
