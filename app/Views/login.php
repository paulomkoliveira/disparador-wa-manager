<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar — WA Manager</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', system-ui, sans-serif;
            background: #0a0f1a;
            overflow: hidden;
        }

        /* Animated background */
        .bg-glow {
            position: fixed;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 50%, rgba(5,150,105,0.15) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 80% 30%, rgba(6,95,70,0.1) 0%, transparent 60%),
                radial-gradient(ellipse 40% 40% at 50% 80%, rgba(16,185,129,0.08) 0%, transparent 60%);
            animation: bgPulse 8s ease-in-out infinite alternate;
        }

        @keyframes bgPulse {
            0%   { opacity: 0.7; }
            100% { opacity: 1; }
        }

        .grid-overlay {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image:
                linear-gradient(rgba(16,185,129,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(16,185,129,0.04) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        /* Card */
        .card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            margin: 24px;
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(16,185,129,0.2);
            border-radius: 20px;
            padding: 40px;
            box-shadow:
                0 0 0 1px rgba(255,255,255,0.04) inset,
                0 40px 80px rgba(0,0,0,0.5),
                0 0 60px rgba(5,150,105,0.1);
            backdrop-filter: blur(20px);
            animation: slideUp 0.5s cubic-bezier(0.16,1,0.3,1) both;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Logo */
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
        }

        .logo-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #059669, #10b981);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            box-shadow: 0 8px 20px rgba(5,150,105,0.35);
        }

        .logo-text h1 {
            font-size: 1.25rem;
            font-weight: 800;
            color: #f1f5f9;
            letter-spacing: -0.5px;
        }

        .logo-text p {
            font-size: 0.7rem;
            color: #64748b;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Subtitle */
        .subtitle {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 28px;
            line-height: 1.5;
        }

        .subtitle strong {
            color: #94a3b8;
            font-weight: 600;
        }

        /* Error message */
        .error-msg {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 0.82rem;
            color: #fca5a5;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Form */
        .field {
            margin-bottom: 16px;
        }

        .field label {
            display: block;
            font-size: 0.72rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .field-wrap {
            position: relative;
        }

        .field-wrap .icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #475569;
            font-size: 0.85rem;
        }

        .field input {
            width: 100%;
            padding: 13px 14px 13px 40px;
            background: rgba(30,41,59,0.6);
            border: 1px solid rgba(51,65,85,0.8);
            border-radius: 10px;
            color: #f1f5f9;
            font-size: 0.9rem;
            outline: none;
            transition: all 0.2s;
        }

        .field input:focus {
            border-color: rgba(16,185,129,0.5);
            background: rgba(30,41,59,0.9);
            box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
        }

        .field input::placeholder {
            color: #475569;
        }

        /* Forgot password */
        .forgot {
            display: flex;
            justify-content: flex-end;
            margin-top: -8px;
            margin-bottom: 20px;
        }

        .forgot a {
            font-size: 0.75rem;
            color: #10b981;
            text-decoration: none;
            font-weight: 500;
        }

        /* Submit button */
        .btn-primary {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            font-size: 0.9rem;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 8px 20px rgba(5,150,105,0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 28px rgba(5,150,105,0.35);
        }

        .btn-primary:active { transform: scale(0.98); }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
            color: #334155;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(51,65,85,0.8);
        }

        /* Google button */
        .btn-google {
            width: 100%;
            padding: 13px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            color: #cbd5e1;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }

        .btn-google:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.2);
            color: #f1f5f9;
        }

        .google-icon {
            width: 18px;
            height: 18px;
        }

        /* Footer */
        .footer {
            margin-top: 28px;
            text-align: center;
            font-size: 0.78rem;
            color: #475569;
        }

        .footer a {
            color: #10b981;
            text-decoration: none;
            font-weight: 600;
        }

        /* Loading state */
        .btn-primary.loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="bg-glow"></div>
    <div class="grid-overlay"></div>

    <div class="card">
        <!-- Logo -->
        <div class="logo">
            <div class="logo-icon">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div class="logo-text">
                <h1>WA Manager</h1>
                <p>Plataforma de Automação</p>
            </div>
        </div>

        <p class="subtitle">
            Bem-vindo de volta! Faça login para acessar o <strong>painel de controle</strong>.
        </p>

        <!-- Mensagem de erro -->
        <?php if (!empty($erro)): ?>
            <div class="error-msg">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <!-- Formulário Login -->
        <form method="POST" action="/login" id="loginForm">
            <div class="field">
                <label>E-mail</label>
                <div class="field-wrap">
                    <i class="fa-solid fa-envelope icon"></i>
                    <input type="email" name="email" placeholder="seu@email.com" required
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
            </div>

            <div class="field">
                <label>Senha</label>
                <div class="field-wrap">
                    <i class="fa-solid fa-lock icon"></i>
                    <input type="password" name="senha" placeholder="••••••••" required>
                </div>
            </div>

            <div class="forgot">
                <a href="/recuperar-senha">Esqueceu a senha?</a>
            </div>

            <button type="submit" class="btn-primary" id="btnLogin">
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                Entrar
            </button>
        </form>

        <div class="footer">
            WA Manager &copy; <?= date('Y') ?> &mdash; Acesso restrito
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('btnLogin');
            btn.classList.add('loading');
            btn.innerHTML = '<i class="fa-solid fa-spinner spin"></i> Entrando...';
        });
    </script>
</body>
</html>
