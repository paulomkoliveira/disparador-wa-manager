<div class="flex h-full bg-white">
    
    <!-- Sidebar de Instâncias / Conversas -->
    <div class="w-80 border-r border-slate-200 flex flex-col h-full bg-slate-50">
        <div class="p-4 border-b border-slate-200 bg-white">
            <h2 class="text-xl font-bold text-slate-800">Conversas</h2>
            <div class="mt-3 relative">
                <select onchange="window.location.href='?instancia='+this.value" class="w-full border-slate-300 rounded-lg p-2 text-sm border focus:ring-primary-500 focus:border-primary-500 appearance-none bg-slate-50">
                    <option value="">Selecione a Instância...</option>
                    <?php foreach($instancias as $inst): ?>
                        <option value="<?= htmlspecialchars($inst['nome']) ?>" <?= ($inst['nome'] == ($instanciaAtiva ?? '')) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($inst['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mt-4 relative">
                <i class="fa-solid fa-search absolute left-3 top-2.5 text-slate-400"></i>
                <input type="text" placeholder="Buscar conversa..." class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
        </div>

        <div class="flex-1 overflow-y-auto">
            <!-- Lista de Chats - últimos 7 dias -->
            <?php if(empty($conversas)): ?>
                <div class="p-6 text-center text-slate-500 text-sm">
                    <i class="fa-solid fa-clock-rotate-left text-2xl text-slate-300 mb-2"></i>
                    <p>Nenhuma conversa nos últimos 7 dias.</p>
                </div>
            <?php else: ?>
                <?php foreach($conversas as $conv): ?>
                <?php
                    // Campos vindos do novo cache estruturado
                    $remoteJid = $conv['remoteJid'] ?? '';
                    $name      = $conv['name'] ?? $conv['pushName'] ?? explode('@', $remoteJid)[0] ?? 'Desconhecido';
                    if (!$name) $name = 'Contato sem Nome';
                    $num       = str_replace(['@s.whatsapp.net','@g.us'], '', $remoteJid);

                    // Formata timestamp
                    $ts = $conv['timestamp'] ?? 0;
                    if ($ts) {
                        $diff = time() - $ts;
                        if ($diff < 60)           $tsLabel = 'agora';
                        elseif ($diff < 3600)     $tsLabel = floor($diff/60) . 'min';
                        elseif ($diff < 86400)    $tsLabel = floor($diff/3600) . 'h';
                        else                      $tsLabel = floor($diff/86400) . 'd';
                    } else {
                        $tsLabel = '';
                    }

                    $isGroup = str_contains($remoteJid, '@g.us');
                ?>
                <div onclick="openChat('<?= addslashes($name) ?>', '<?= addslashes($remoteJid) ?>')"
                     class="p-3 border-b border-slate-200 cursor-pointer hover:bg-slate-100 transition-colors flex items-center bg-white">
                    <div class="relative mr-3 flex-shrink-0">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($name) ?>&background=random&size=48" class="w-12 h-12 rounded-full">
                        <?php if($isGroup): ?>
                        <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-users text-[8px] text-white"></i>
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline mb-0.5">
                            <h3 class="font-semibold text-slate-800 truncate text-sm"><?= htmlspecialchars($name) ?></h3>
                            <span class="text-[11px] text-slate-400 ml-1 flex-shrink-0"><?= $tsLabel ?></span>
                        </div>
                        <p class="text-xs text-slate-400 truncate"><?= htmlspecialchars($num) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Badge total -->
                <div class="p-3 text-center text-[11px] text-slate-400 border-t border-slate-100">
                    <i class="fa-solid fa-calendar-days mr-1"></i>
                    <?= count($conversas) ?> conversa<?= count($conversas) !== 1 ? 's' : '' ?> nos últimos 7 dias
                </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- Área de Mensagens -->
    <div class="flex-1 flex flex-col bg-slate-100 relative">
        <!-- Header Chat -->
        <div class="h-16 border-b border-slate-200 bg-white flex items-center px-6 justify-between shadow-sm z-10">
            <div class="flex items-center">
                <img id="chatHeaderImg" src="https://ui-avatars.com/api/?name=Chat&background=random" class="w-10 h-10 rounded-full mr-3">
                <div>
                    <h3 id="chatHeaderName" class="font-bold text-slate-800">Selecione um Chat</h3>
                    <p class="text-xs text-green-500"><i class="fa-solid fa-circle text-[8px] mr-1"></i>Online</p>
                </div>
            </div>
            <div class="flex space-x-3 text-slate-400">
                <button class="hover:text-primary-600 transition-colors"><i class="fa-solid fa-tags"></i></button>
                <button class="hover:text-primary-600 transition-colors"><i class="fa-solid fa-paperclip"></i></button>
                <button class="hover:text-primary-600 transition-colors"><i class="fa-solid fa-ellipsis-vertical"></i></button>
            </div>
        </div>

        <!-- Mensagens -->
        <div id="messagesArea" class="flex-1 overflow-y-auto p-6 space-y-4" style="background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-opacity: 0.1;">
            <div class="flex justify-center items-center h-full text-slate-400">
                <p>Selecione uma conversa ao lado para abrir o chat.</p>
            </div>
        </div>

        <!-- Input Box -->
        <div class="bg-slate-200 p-4 shrink-0 flex items-end">
            <button class="p-3 text-slate-500 hover:text-slate-700 transition-colors mr-2">
                <i class="fa-regular fa-face-smile text-xl"></i>
            </button>
            <textarea id="chatInput" rows="1" class="flex-1 w-full rounded-xl border-none focus:ring-0 resize-none py-3 px-4 max-h-32 text-slate-800" placeholder="Digite uma mensagem..."></textarea>
            <button onclick="enviarMensagemUnit()" class="ml-2 w-12 h-12 rounded-full bg-primary-600 hover:bg-primary-500 text-white flex items-center justify-center transition-colors shadow-sm">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<script>
const instanciaAtiva = '<?= htmlspecialchars($instanciaAtiva ?? '') ?>';
let currentChat = null;
let loadingMsgs = false;

// ────────────────────────────────────────────────
// Abre conversa e carrega mensagens via AJAX lazy
// ────────────────────────────────────────────────
async function openChat(name, remoteJid) {
    currentChat = { name, remoteJid };

    document.getElementById('chatHeaderName').innerText = name;
    document.getElementById('chatHeaderImg').src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name) + '&background=random';

    const area = document.getElementById('messagesArea');
    area.innerHTML = `
        <div class="flex justify-center items-center h-full">
            <div class="text-center text-slate-400">
                <i class="fa-solid fa-circle-notch fa-spin text-3xl mb-3 text-primary-400"></i>
                <p class="text-sm">Carregando mensagens...</p>
            </div>
        </div>`;

    await carregarMensagens(remoteJid, 1);
}

async function carregarMensagens(remoteJid, page = 1) {
    if (loadingMsgs) return;
    loadingMsgs = true;

    try {
        const res = await fetch('/api/mensagens', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ instancia: instanciaAtiva, remoteJid, page, limit: 20 })
        });
        const data = await res.json();
        const area = document.getElementById('messagesArea');

        if (!data.success || !data.mensagens || data.mensagens.length === 0) {
            area.innerHTML = `<div class="flex justify-center items-center h-full text-slate-400 text-sm"><p>Nenhuma mensagem encontrada nesta conversa.</p></div>`;
            return;
        }

        area.innerHTML = '';
        data.mensagens.forEach(msg => renderMensagem(msg));
        area.scrollTop = area.scrollHeight;

    } catch(e) {
        document.getElementById('messagesArea').innerHTML =
            `<div class="flex justify-center items-center h-full text-red-400 text-sm"><p>Erro ao carregar mensagens. Tente novamente.</p></div>`;
    } finally {
        loadingMsgs = false;
    }
}

function renderMensagem(msg) {
    const area = document.getElementById('messagesArea');
    const fromMe = msg.key?.fromMe ?? false;
    
    // Na v2, msg.message pode ser a própria mensagem ou conter o objeto
    let m = msg.message;
    // Se msg.message for null, tenta ver se os campos estão na raiz do objeto (comum em algumas versões da v2)
    if (!m) m = msg;

    const texto  = m.conversation
                || m.extendedTextMessage?.text
                || m.imageMessage?.caption
                || m.videoMessage?.caption
                || '';

    const imgMsg = m.imageMessage;
    const vidMsg = m.videoMessage;
    const audMsg = m.audioMessage;

    const ts = msg.messageTimestamp
                ? new Date(msg.messageTimestamp * 1000).toLocaleTimeString('pt-BR', { hour:'2-digit', minute:'2-digit' })
                : '';

    const el = document.createElement('div');
    el.className = fromMe ? 'flex justify-end' : 'flex';
    
    let midiaHtml = '';
    const evoUrl = '<?= rtrim($_ENV['EVOLUTION_API_URL'], '/') ?>';
    
    if (imgMsg) {
        // Tenta buscar via base64 se disponível ou URL da Evolution
        const imgSrc = imgMsg.url || `${evoUrl}/chat/getBase64FromMediaMessage/${instanciaAtiva}?messageId=${msg.key.id}`;
        midiaHtml = `<div class="mb-2"><img src="${imgSrc}" class="rounded-lg max-w-full cursor-pointer hover:opacity-90" onclick="window.open(this.src)" onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\"text-xs text-slate-400\">[Imagem]</span>'"></div>`;
    } else if (vidMsg) {
        midiaHtml = `<div class="mb-2"><video controls class="rounded-lg max-w-full"><source src="${vidMsg.url || ''}" type="video/mp4">Seu navegador não suporta vídeos.</video></div>`;
    } else if (audMsg) {
        midiaHtml = `<div class="mb-2"><audio controls class="max-w-full"><source src="${audMsg.url || ''}" type="audio/mpeg"></audio></div>`;
    }

    const contentHtml = `
        <div class="${fromMe ? 'bg-primary-100 rounded-tr-sm' : 'bg-white rounded-tl-sm'} rounded-2xl px-4 py-2 shadow-sm max-w-[70%] text-sm">
            ${midiaHtml}
            ${texto ? `<p class="text-slate-800 whitespace-pre-wrap">${escHtml(texto)}</p>` : ''}
            <p class="text-[10px] text-slate-400 text-right mt-1">
                ${ts} ${fromMe ? '<i class="fa-solid fa-check-double text-blue-400 ml-1"></i>' : ''}
            </p>
        </div>
    `;

    el.innerHTML = contentHtml;
    area.appendChild(el);
}

function escHtml(text) {
    return String(text).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ────────────────────────────────────────────────
// Enviar mensagem individual
// ────────────────────────────────────────────────
async function enviarMensagemUnit() {
    let text = document.getElementById('chatInput').value.trim();
    if(!text || !currentChat) return;

    // Visual imediato
    const fakeMsgObj = { key: { fromMe: true }, message: { conversation: text }, messageTimestamp: Date.now() / 1000 };
    renderMensagem(fakeMsgObj);
    const area = document.getElementById('messagesArea');
    area.scrollTop = area.scrollHeight;
    document.getElementById('chatInput').value = '';

    // Extrai número limpo do remoteJid
    const numero = currentChat.remoteJid.replace('@s.whatsapp.net', '').replace('@g.us', '');

    await fetch('/api/enviar-mensagem', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ instancia: instanciaAtiva, numero, mensagem: text })
    });
}

// Enter para enviar
document.getElementById('chatInput').addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); enviarMensagemUnit(); }
});
</script>
    </div>
</div>
