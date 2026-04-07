# WA Manager - Disparador Automático de WhatsApp & E-mail

Plataforma inteligente de automação multi-canal integrada com **Supabase** (Auth & DB), **Evolution API** (WhatsApp) e **SMTP** (E-mail).

## 🚀 Funcionalidades Principais
- **Login Seguro**: Autenticação com e-mail/senha via Supabase.
- **Disparador WhatsApp**: Envio em massa com variáveis personalizadas.
- **Disparador E-mail**: Integração SMTP (Gmail, Hostinger, etc).
- **Gestão de Contas**: Múltiplas contas de e-mail e instâncias WhatsApp.
- **CRM Kanban**: Gestão visual de leads e conversões (em desenvolvimento).
- **Relatórios**: Status de envio em tempo real.

## 🛠️ Stack Tecnológica
- **Backend**: PHP 8.2+ (Composer)
- **Frontend**: Tailwind CSS v3, JavaScript Vanilla
- **Banco de Dados**: Supabase (PostgreSQL)
- **WhatsApp**: Evolution API / whatsapp-web.js
- **Segurança**: PHPMailer com tratamento de SSL para Windows.

## ⚙️ Configuração Local
1. Clone o repositório.
2. Execute `composer install` para dependências PHP.
3. Execute `npm install` para dependências frontend.
4. Crie um arquivo `.env` baseado no seu ambiente:
   ```env
   SUPABASE_URL="..."
   SUPABASE_KEY="..."
   EVOLUTION_API_URL="..."
   EVOLUTION_API_TOKEN="..."
   ```
5. Inicie o servidor: `php -S localhost:8000 -t public`

## 📦 Deploy
Projetado para rodar em **VPS Hostinger** ou qualquer servidor que suporte PHP 8+.

---
*WA Manager - Transformando Leads em Clientes via Automação.*
