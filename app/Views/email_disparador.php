
<!-- ======================================================
     DISPARADOR DE E-MAIL
     ====================================================== -->
<div class="flex flex-col h-full bg-slate-50">

    <!-- HEADER -->
    <header style="position:sticky;top:0;z-index:20;background:#fff;border-bottom:1px solid #e2e8f0;padding:10px 24px;display:flex;align-items:center;justify-content:space-between;gap:16px;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
        <div>
            <h1 style="font-size:1.15rem;font-weight:900;color:#1e293b;line-height:1.2;">Disparador de E-mail</h1>
            <p style="font-size:0.75rem;color:#94a3b8;margin-top:2px;">Envie campanhas via SMTP próprio (Hostinger / Gmail / VPS).</p>
        </div>
        <button
            onclick="abrirModalConta()"
            style="display:inline-flex;align-items:center;gap:8px;padding:8px 16px;background:#1e293b;color:#fff;font-size:0.78rem;font-weight:700;border:none;border-radius:8px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.15);transition:background 0.2s;white-space:nowrap;"
            onmouseover="this.style.background='#334155'"
            onmouseout="this.style.background='#1e293b'">
            <i class="fa-solid fa-gear"></i> Gerenciar Contas SMTP
        </button>
    </header>

    <!-- ÁREA DE CONTEÚDO -->
    <div class="flex-1 p-6 flex flex-col gap-6 overflow-auto">

    <!-- CORPO PRINCIPAL: Formulário + Monitor -->
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-3 gap-6 min-h-0">

        <!-- COLUNA ESQUERDA: Formulário (2/3) -->
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl shadow-sm p-6 flex flex-col gap-6">

            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-3">
                Nova Campanha
            </h2>

            <!-- Linha 1: Remetente + Assunto -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Conta Remetente</label>
                    <select id="contaRemetente" class="w-full border border-slate-200 bg-slate-50 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">— Selecione uma conta —</option>
                        <?php foreach($contas as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?> (<?= $c['usuario'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Assunto do E-mail</label>
                    <input type="text" id="emailAssunto" placeholder="Ex: Oferta Especial de Abril"
                        class="w-full border border-slate-200 bg-slate-50 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <!-- Linha 2: Destinatários -->
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Lista de Destinatários</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                    <!-- Drop CSV -->
                    <label for="emailCsvInput"
                        id="emailCsvDrop"
                        class="relative border-2 border-dashed border-slate-300 hover:border-primary-500 rounded-xl p-5 flex flex-col items-center justify-center text-center cursor-pointer transition-colors min-h-[120px] bg-slate-50 hover:bg-primary-50/30">
                        <input type="file" id="emailCsvInput" class="sr-only" accept=".csv">
                        <i class="fa-solid fa-file-csv text-2xl text-primary-500 mb-2"></i>
                        <p class="text-sm font-semibold text-slate-700">Importar CSV</p>
                        <p class="text-xs text-slate-400 mt-1">Clique ou arraste o arquivo</p>
                    </label>

                    <!-- Input Manual -->
                    <div class="flex flex-col">
                        <textarea id="manualEmails" rows="4"
                            placeholder="Ou cole os e-mails separados por vírgula ou linha..."
                            class="flex-1 border border-slate-200 border-b-0 bg-slate-50 rounded-t-lg px-3 py-2.5 text-xs font-mono resize-none focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                        <button type="button" onclick="carregarEmailsManuais()"
                            class="w-full py-2 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-b-lg text-slate-700 text-xs font-semibold transition-colors">
                            <i class="fa-solid fa-check-circle mr-1"></i> Confirmar Lista
                        </button>
                    </div>
                </div>

                <!-- Badge: Quantidade na fila -->
                <div id="badgeFila" class="hidden">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-primary-50 border border-primary-200 rounded-full text-xs font-semibold text-primary-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                        <span id="qtdFila">0</span> destinatários na fila
                    </span>
                </div>
            </div>

            <!-- Linha 3: Mensagem -->
            <div class="flex flex-col gap-1.5 flex-1">
                <div class="flex justify-between items-center">
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Conteúdo da Mensagem</label>
                    <span class="text-[10px] text-slate-400">Use <code class="bg-slate-100 px-1 py-0.5 rounded text-primary-600 font-bold">{nome}</code> para personalizar</span>
                </div>
                <textarea id="emailCorpo" rows="10"
                    placeholder="Olá {nome}, temos uma novidade para você..."
                    class="w-full flex-1 border border-slate-200 bg-slate-50 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
            </div>

        </div>

        <!-- COLUNA DIREITA: Monitor de Progresso (1/3) -->
        <div class="lg:col-span-1 flex flex-col gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm flex flex-col overflow-hidden">

                <!-- Header do Monitor -->
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Monitor de Disparo
                    </h2>
                    <div id="emailLoading" class="hidden">
                        <i class="fa-solid fa-spinner fa-spin text-primary-500 text-xs"></i>
                    </div>
                </div>

                <!-- Círculo de Progresso -->
                <div class="p-6 flex flex-col items-center gap-4 border-b border-slate-100">
                    <div class="relative w-32 h-32">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="52" fill="none" stroke="#f1f5f9" stroke-width="8"/>
                            <circle id="svgCircle" cx="60" cy="60" r="52" fill="none"
                                stroke="currentColor"
                                stroke-width="8"
                                stroke-linecap="round"
                                stroke-dasharray="326.7"
                                stroke-dashoffset="326.7"
                                class="text-primary-500 transition-all duration-700"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span id="emailProgressPct" class="text-2xl font-black text-slate-800">0%</span>
                            <span class="text-[10px] text-slate-400 font-medium uppercase tracking-wide">Progresso</span>
                        </div>
                    </div>
                    <p id="emailProgressSub" class="text-xs text-slate-500 text-center">Aguardando início...</p>
                </div>

                <!-- Botões de Ação -->
                <div class="p-5 flex gap-3">
                    <button id="btnIniciarEmail" type="button" onclick="iniciarDisparoEmail()"
                        class="flex-1 py-3 bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold rounded-xl shadow flex items-center justify-center gap-2 transition-all active:scale-95">
                        <i class="fa-solid fa-paper-plane"></i> Iniciar
                    </button>
                    <button id="btnPararEmail" type="button" onclick="pararEmail()"
                        class="hidden px-4 py-3 bg-red-500 hover:bg-red-600 text-white text-sm font-bold rounded-xl shadow transition-all active:scale-95">
                        <i class="fa-solid fa-stop"></i>
                    </button>
                </div>

                <!-- Log em Tempo Real -->
                <div id="emailLogContainer" class="hidden border-t border-slate-100 flex flex-col">
                    <div class="px-5 pt-3 pb-1 flex items-center justify-between">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Log em Tempo Real</p>
                    </div>
                    <div id="emailLogList"
                        class="mx-4 mb-4 max-h-52 overflow-y-auto text-[10px] font-mono bg-slate-900 text-slate-300 rounded-lg p-3 space-y-1">
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /grid principal -->
    </div><!-- /área de conteúdo -->
</div><!-- /wrapper principal -->

<!-- =====================================================
     MODAL: Gerenciar Contas SMTP
     Overlay div-based — sem bugs de posicionamento do <dialog>
     ===================================================== -->
<div id="modalContas"
    class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background: rgba(15,23,42,0.7); backdrop-filter: blur(4px);"
    onclick="if(event.target===this) fecharModal()">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl flex flex-col overflow-hidden"
         style="max-height: 90vh;">

        <!-- Header do Modal -->
        <div class="flex items-center justify-between px-6 py-4 bg-slate-800 text-white shrink-0">
            <div>
                <h3 class="font-bold text-base">⚙️ Configurações SMTP</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Hostinger · Gmail · VPS própria</p>
            </div>
            <button onclick="fecharModal()"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Corpo (scrollable) -->
        <div class="overflow-y-auto p-6 flex flex-col gap-6">

            <!-- Contas existentes -->
            <div>
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Contas Cadastradas</h4>
                <div class="flex flex-col gap-2">
                    <?php if (count($contas) === 0): ?>
                        <div class="py-8 text-center border-2 border-dashed border-slate-200 rounded-xl text-slate-400 bg-slate-50">
                            <i class="fa-solid fa-envelope-open text-3xl mb-2 text-slate-200"></i>
                            <p class="text-sm font-medium">Nenhuma conta cadastrada ainda.</p>
                        </div>
                    <?php endif; ?>
                    <?php foreach($contas as $c): ?>
                        <div class="flex items-center justify-between px-4 py-3 border border-slate-200 rounded-xl bg-white hover:border-primary-300 transition-colors">
                            <div class="min-w-0">
                                <p class="font-semibold text-sm text-slate-800"><?= htmlspecialchars($c['nome']) ?></p>
                                <p class="text-xs text-slate-500 truncate"><?= $c['usuario'] ?></p>
                                <span class="inline-block mt-1 text-[10px] px-2 py-0.5 bg-slate-100 text-slate-500 rounded-full"><?= $c['host'] ?>:<?= $c['porta'] ?></span>
                            </div>
                            <button onclick="excluirConta('<?= $c['id'] ?>')"
                                class="ml-3 p-2 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors shrink-0">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <hr class="border-slate-100">

            <!-- Formulário nova conta -->
            <div class="flex flex-col gap-4">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Adicionar Nova Conta</h4>

                <!-- Nome -->
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold text-slate-600">Nome de Exibição</label>
                    <input type="text" id="smtpNome" placeholder="Ex: Suporte Paulo"
                        class="border border-slate-200 rounded-lg px-3 py-2.5 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <!-- Host + Porta + Enc (linha) -->
                <div class="grid grid-cols-12 gap-3">
                    <div class="col-span-6 flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">Host SMTP</label>
                        <input type="text" id="smtpHost" placeholder="smtp.gmail.com"
                            class="border border-slate-200 rounded-lg px-3 py-2.5 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div class="col-span-3 flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">Porta</label>
                        <input type="number" id="smtpPorta" placeholder="587"
                            class="border border-slate-200 rounded-lg px-3 py-2.5 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div class="col-span-3 flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">Encriptação</label>
                        <select id="smtpEnc"
                            class="border border-slate-200 rounded-lg px-3 py-2.5 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="tls">TLS (587)</option>
                            <option value="ssl">SSL (465)</option>
                        </select>
                    </div>
                </div>

                <!-- Usuário + Senha (linha) -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">E-mail (Usuário)</label>
                        <input type="email" id="smtpUser" placeholder="contato@empresa.com"
                            class="border border-slate-200 rounded-lg px-3 py-2.5 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">Senha / Senha de App</label>
                        <input type="password" id="smtpSenha"
                            class="border border-slate-200 rounded-lg px-3 py-2.5 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>

                <!-- Dica Gmail -->
                <div class="flex items-start gap-2 px-3 py-2.5 bg-blue-50 border border-blue-200 rounded-lg">
                    <i class="fa-solid fa-circle-info text-blue-500 text-sm mt-0.5 shrink-0"></i>
                    <p class="text-xs text-blue-700 leading-relaxed">
                        <strong>Gmail:</strong> Host <code class="bg-blue-100 px-1 rounded">smtp.gmail.com</code>,
                        Porta <code class="bg-blue-100 px-1 rounded">587</code>, TLS.
                        Use a <strong>Senha de App</strong> (16 dígitos, <u>sem espaços</u>).
                    </p>
                </div>

                <button id="btnSalvarConta" onclick="salvarConta()"
                    class="w-full py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold text-sm rounded-xl shadow flex items-center justify-center gap-2 transition-all active:scale-95">
                    <i class="fa-solid fa-save"></i> Salvar Conta
                </button>
            </div>

        </div>
    </div>
</div>

<!-- ===========================================
     SCRIPT — Único bloco, sem duplicatas
     =========================================== -->
<script>
let listaEmails = [];
let pararEmails = false;
let idxEmail = 0;

// --- CSV Drag & Drop ---
const csvDropArea = document.getElementById('emailCsvDrop');
const csvInput    = document.getElementById('emailCsvInput');
// O click é gerenciado pelo <label for="emailCsvInput">, sem necessidade de handler manual

csvDropArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    csvDropArea.classList.add('border-primary-500', 'bg-primary-50');
});

csvDropArea.addEventListener('dragleave', () => {
    csvDropArea.classList.remove('border-primary-500', 'bg-primary-50');
});

csvDropArea.addEventListener('drop', (e) => {
    e.preventDefault();
    csvDropArea.classList.remove('border-primary-500', 'bg-primary-50');
    const file = e.dataTransfer.files[0];
    if (file) processarCsv(file);
});

csvInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (file) processarCsv(file);
});

function processarCsv(file) {
    const reader = new FileReader();
    reader.onload = (evt) => {
        listaEmails = [];
        const linhas = evt.target.result.split('\n');
        linhas.forEach(linha => {
            const partes = linha.split(',');
            if (partes.length >= 2) {
                const nome  = partes[0].trim();
                const email = partes[1].trim();
                if (email.includes('@')) listaEmails.push({ nome, email });
            } else {
                const email = (partes[0] || '').trim();
                if (email.includes('@')) listaEmails.push({ nome: 'Cliente', email });
            }
        });
        atualizarBadgeFila();
        alert(`${listaEmails.length} destinatários carregados via CSV.`);
    };
    reader.readAsText(file);
}

// --- Lista Manual ---
function carregarEmailsManuais() {
    const raw = document.getElementById('manualEmails').value;
    listaEmails = [];
    raw.split(/[\n,]/).forEach(item => {
        const email = item.trim();
        if (email.includes('@')) listaEmails.push({ nome: 'Cliente', email });
    });
    atualizarBadgeFila();
    alert(`${listaEmails.length} destinatários válidos carregados.`);
}

function atualizarBadgeFila() {
    const badge = document.getElementById('badgeFila');
    const qtd   = document.getElementById('qtdFila');
    qtd.textContent = listaEmails.length;
    badge.classList.toggle('hidden', listaEmails.length === 0);
    document.getElementById('emailProgressSub').textContent = `${listaEmails.length} na fila`;
}

// --- Modal ---
function abrirModalConta() {
    const m = document.getElementById('modalContas');
    m.classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // trava scroll da página
}

function fecharModal() {
    const m = document.getElementById('modalContas');
    m.classList.add('hidden');
    document.body.style.overflow = '';
}

// Fecha com ESC
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') fecharModal();
});

// --- Salvar Conta SMTP ---
async function salvarConta() {
    const btn = document.getElementById('btnSalvarConta');
    const old = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Salvando...';
    btn.disabled  = true;

    const payload = {
        nome:       document.getElementById('smtpNome').value.trim(),
        host:       document.getElementById('smtpHost').value.trim(),
        porta:      document.getElementById('smtpPorta').value.trim(),
        usuario:    document.getElementById('smtpUser').value.trim(),
        senha:      document.getElementById('smtpSenha').value,
        encryption: document.getElementById('smtpEnc').value,
    };

    if (!payload.host || !payload.usuario || !payload.senha) {
        alert('Preencha Host, Usuário e Senha!');
        btn.innerHTML = old;
        btn.disabled  = false;
        return;
    }

    try {
        await fetch('/api/email/salvar-conta', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify(payload),
        });
        location.reload();
    } catch (err) {
        alert('Erro ao salvar: ' + err.message);
        btn.innerHTML = old;
        btn.disabled  = false;
    }
}

async function excluirConta(id) {
    if (!confirm('Excluir esta conta SMTP?')) return;
    await fetch('/api/email/excluir-conta', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ id }),
    });
    location.reload();
}

// --- Disparo ---
async function iniciarDisparoEmail() {
    const contaId = document.getElementById('contaRemetente').value;
    const assunto = document.getElementById('emailAssunto').value.trim();
    const corpo   = document.getElementById('emailCorpo').value.trim();

    if (!contaId)             return alert('Selecione uma conta remetente!');
    if (!listaEmails.length)  return alert('Carregue a lista de destinatários!');
    if (!assunto || !corpo)   return alert('Preencha o assunto e o conteúdo!');

    pararEmails = false;
    idxEmail    = 0;

    document.getElementById('btnIniciarEmail').classList.add('opacity-50', 'pointer-events-none');
    document.getElementById('btnPararEmail').classList.remove('hidden');
    document.getElementById('emailLogContainer').classList.remove('hidden');
    document.getElementById('emailLogList').innerHTML = '';

    processarFila(contaId, assunto, corpo);
}

async function processarFila(contaId, assunto, corpo) {
    if (pararEmails || idxEmail >= listaEmails.length) {
        document.getElementById('emailProgressSub').textContent = 'Concluído ✅';
        document.getElementById('btnIniciarEmail').classList.remove('opacity-50', 'pointer-events-none');
        document.getElementById('btnPararEmail').classList.add('hidden');
        return;
    }

    const dest   = listaEmails[idxEmail];
    const corpo_ = corpo.replace(/{nome}/g, dest.nome);
    const pct    = Math.round((idxEmail / listaEmails.length) * 100);

    // Atualizar UI
    document.getElementById('emailProgressPct').textContent  = pct + '%';
    document.getElementById('emailProgressSub').textContent  = `Enviando para ${dest.email}…`;

    // Círculo SVG (r=52, circunferência ≈ 326.7)
    const offset = 326.7 - (pct / 100) * 326.7;
    document.getElementById('svgCircle').style.strokeDashoffset = offset;

    try {
        const res  = await fetch('/api/email/enviar', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ conta_id: contaId, destinatario: dest.email, assunto, mensagem: corpo_ }),
        });
        const data = await res.json();

        adicionarLog(
            data.success
                ? `<span class="text-emerald-400">[OK]</span> ${dest.email}`
                : `<span class="text-red-400">[ERRO]</span> ${dest.email}: ${data.error ?? 'Falha'}`
        );
    } catch {
        adicionarLog(`<span class="text-red-500">[FALHA]</span> ${dest.email}`);
    }

    idxEmail++;
    const delay = 2000 + Math.random() * 2000; // 2–4s anti-spam
    setTimeout(() => processarFila(contaId, assunto, corpo), delay);
}

function adicionarLog(html) {
    const el = document.createElement('div');
    el.innerHTML = html;
    document.getElementById('emailLogList').prepend(el);
}

function pararEmail() {
    pararEmails = true;
    document.getElementById('emailProgressSub').textContent = 'Interrompido 🛑';
}
</script>
