<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador WhatsApp | Evolution</title>
    <link href="/css/output.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 text-slate-800 font-sans h-screen flex overflow-hidden">
    
    <?php if (isset($_SESSION['user_id'])): ?>
    <!-- Sidebar -->
    <aside class="w-64 bg-primary-900 text-white flex flex-col shadow-xl">
        <div class="h-16 flex items-center justify-center border-b border-primary-800/50">
            <h1 class="text-xl font-bold tracking-wider"><i class="fa-brands fa-whatsapp text-primary-400 mr-2"></i> WA<span class="font-light">Manager</span></h1>
        </div>

        <nav class="flex-1 py-4 px-3 space-y-1">
            <a href="/" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-primary-800 transition-colors <?= $_SERVER['REQUEST_URI'] == '/' ? 'bg-primary-800' : '' ?>">
                <i class="fa-solid fa-chart-pie w-6"></i> Dashboard
            </a>
            <a href="/chat" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-primary-800 transition-colors <?= $_SERVER['REQUEST_URI'] == '/chat' ? 'bg-primary-800' : '' ?>">
                <i class="fa-solid fa-comments w-6"></i> Chat Multi-Instância
            </a>
            <a href="/disparador" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-primary-800 transition-colors <?= $_SERVER['REQUEST_URI'] == '/disparador' ? 'bg-primary-800' : '' ?>">
                <i class="fa-solid fa-paper-plane w-6"></i> Disparador em Massa
            </a>
            <a href="/crm" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-primary-800 transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/crm') === 0 ? 'bg-primary-800' : '' ?>">
                <i class="fa-solid fa-kanban w-6"></i> CRM / Leads
            </a>
            <a href="/email" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-primary-800 transition-colors <?= $_SERVER['REQUEST_URI'] == '/email' ? 'bg-primary-800' : '' ?>">
                <i class="fa-solid fa-envelope w-6"></i> Disparador E-mail
            </a>
        </nav>

        <div class="p-4 border-t border-primary-800/50">
            <!-- Perfil do usuário -->
            <div class="flex items-center gap-3 mb-3 px-1">
                <?php if (!empty($_SESSION['user_avatar'])): ?>
                    <img src="<?= htmlspecialchars($_SESSION['user_avatar']) ?>"
                         class="w-8 h-8 rounded-full object-cover border-2 border-primary-600">
                <?php else: ?>
                    <div class="w-8 h-8 rounded-full bg-primary-600 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                        <?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-slate-200 truncate">
                        <?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuário') ?>
                    </p>
                    <p class="text-[10px] text-primary-400 truncate">
                        <?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>
                    </p>
                </div>
            </div>
            <a href="/logout" class="flex items-center justify-center w-full px-4 py-2 bg-red-500/10 hover:bg-red-500/20 border border-red-500/30 text-red-400 rounded-lg transition-colors text-sm font-medium">
                <i class="fa-solid fa-right-from-bracket mr-2 text-xs"></i> Sair
            </a>
        </div>
    </aside>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="flex-1 overflow-auto bg-slate-50 flex flex-col">
        <?php $this->render($view, $data ?? []); ?>
    </main>

</body>
</html>
