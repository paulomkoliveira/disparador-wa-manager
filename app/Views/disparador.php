<div class="p-8 h-full flex flex-col">
    <header class="mb-8">
        <h1 class="text-3xl font-bold text-slate-800">Disparo em Massa</h1>
        <p class="text-slate-500">Envie mensagens segmentadas via WhatsApp com sistema anti-ban.</p>
    </header>

    <div class="flex-1 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2">Configurar Disparo</h2>
                
                <form class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nome da Campanha</label>
                            <input type="text" id="campaignName" class="w-full border-slate-300 rounded-lg p-2.5 border focus:ring-primary-500 focus:border-primary-500" placeholder="Ex: Lançamento Abril 2024">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Instância Remetente</label>
                            <select id="instanciaSelect" onchange="verHistorico(this.value)" class="w-full border-slate-300 rounded-lg p-2.5 border focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Selecione uma instância...</option>
                            <?php foreach($instancias as $inst): ?>
                                <option value="<?= htmlspecialchars($inst['nome']) ?>"><?= htmlspecialchars($inst['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div id="historicoInstanciaPlaceholder"></div>


                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Público Alvo (Upload ou Manual)</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Dropzone CSV -->
                            <div id="csvDrop" class="border-2 border-dashed border-slate-300 rounded-lg p-4 flex flex-col justify-center items-center text-center hover:bg-slate-50 transition-colors cursor-pointer min-h-[120px]">
                                <i class="fa-solid fa-cloud-arrow-up text-2xl text-primary-500 mb-2"></i>
                                <p class="text-xs text-slate-600">CSV: Nome,WhatsApp</p>
                            </div>
                            
                            <!-- Input Manual -->
                            <div class="flex flex-col">
                                <textarea id="manualNumbers" rows="3" class="w-full border-slate-300 rounded-t-lg px-3 py-2 border border-b-0 focus:ring-primary-500 focus:border-primary-500 text-xs resize-none" placeholder="Ou cole números separados por vírgula... Ex: 55119999, 55118888" oninput="contarNumerosManuais()"></textarea>
                                <div class="bg-slate-50 border-x border-slate-300 px-3 py-1 flex justify-between items-center text-[10px] text-slate-500">
                                    <span>Insira os números com DDD</span>
                                    <span id="manualCountBadge" class="font-semibold text-primary-600">0 identificados</span>
                                </div>
                                <button type="button" onclick="carregarManuais()" class="w-full py-2 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded-b-lg text-slate-700 text-xs font-bold transition-colors">
                                    <i class="fa-solid fa-plus"></i> Usar Números
                                </button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Mensagem (Use {nome} como variável)</label>
                        <textarea id="templateMsg" rows="5" class="w-full border-slate-300 rounded-lg p-3 border focus:ring-primary-500 focus:border-primary-500" placeholder="Olá {nome}, temos uma novidade..."></textarea>
                    </div>

                    <div class="flex items-center bg-yellow-50 text-yellow-800 p-4 rounded-lg border border-yellow-200">
                        <i class="fa-solid fa-shield-halved text-2xl mr-3"></i>
                        <p class="text-sm">Sistema Anti-Ban ativado. Intervalo de disparos aleatório entre 5 e 15 segundos inserido automaticamente.</p>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col">
            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="text-lg font-bold text-slate-800">Progresso do Envio</h2>
            </div>
            
            <div class="p-6 flex-1 flex flex-col gap-4">
                <!-- Círculo de Progresso -->
                <div class="flex justify-center">
                    <div class="w-28 h-28 rounded-full border-8 border-slate-100 flex flex-col items-center justify-center relative">
                        <span id="progressText" class="text-2xl font-bold text-slate-400">0%</span>
                        <span id="progressSub" class="text-[10px] text-slate-400">aguardando</span>
                    </div>
                </div>
                
                <!-- Status Text -->
                <p id="statusMsg" class="text-slate-600 text-center text-sm">Nenhum envio em andamento.</p>
                
                <!-- Botões Ação -->
                <div class="flex gap-2">
                    <button type="button" id="btnIniciar" class="flex-1 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-lg shadow-lg flex justify-center items-center cursor-pointer transition-colors" onclick="iniciarDisparos()">
                        <i class="fa-solid fa-rocket mr-2"></i> Iniciar Disparo
                    </button>
                    <button type="button" id="btnParar" class="px-4 py-3 bg-red-500 hover:bg-red-600 text-white font-bold rounded-lg shadow-lg hidden cursor-pointer transition-colors" onclick="pararDisparos()" title="Parar Disparos">
                        <i class="fa-solid fa-stop"></i>
                    </button>
                </div>

                <!-- Log de Envios -->
                <div id="logContainer" class="hidden">
                    <p class="text-xs font-semibold text-slate-500 mb-2 uppercase tracking-wide">Log de Envios</p>
                    <div id="logList" class="max-h-40 overflow-y-auto space-y-1 text-xs font-mono bg-slate-50 rounded-lg p-2 border border-slate-200"></div>
                </div>

                <!-- Gráfico de Resultados (pós-envio) -->
                <div id="resultChart" class="hidden">
                    <p class="text-xs font-semibold text-slate-500 mb-3 uppercase tracking-wide">Resultado do Disparo</p>
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <p id="cntEnviados" class="text-2xl font-black text-green-600">0</p>
                            <p class="text-[10px] text-green-700 font-medium">Enviados</p>
                        </div>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <p id="cntDuplicados" class="text-2xl font-black text-yellow-600">0</p>
                            <p class="text-[10px] text-yellow-700 font-medium">Duplicados</p>
                        </div>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <p id="cntErros" class="text-2xl font-black text-red-500">0</p>
                            <p class="text-[10px] text-red-700 font-medium">Erros</p>
                        </div>
                    </div>
                    <!-- Barra visual proporcional -->
                    <div id="barraChart" class="mt-3 h-3 rounded-full overflow-hidden bg-slate-100 flex">
                        <div id="barEnviados" class="bg-green-500 h-full transition-all duration-500" style="width:0%"></div>
                        <div id="barDuplicados" class="bg-yellow-400 h-full transition-all duration-500" style="width:0%"></div>
                        <div id="barErros" class="bg-red-400 h-full transition-all duration-500" style="width:0%"></div>
                    </div>
                    <button onclick="limparHistorico()" class="mt-3 w-full text-xs text-slate-400 hover:text-red-500 transition-colors"><i class="fa-solid fa-trash-can mr-1"></i>Limpar histórico de enviados</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Nova Área de Histórico -->
    <div id="historicoGeral" class="hidden mt-8">
        <!-- Total Summary Cards -->
        <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-chart-pie text-slate-500"></i> Total Summary
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-white border border-slate-200 text-left p-4 rounded-lg shadow-sm">
                <p class="text-[11px] uppercase text-slate-500 font-semibold mb-1 flex items-center gap-1"><i class="fa-solid fa-arrow-trend-up text-slate-400"></i> Tentativas</p>
                <p id="hsTotalTentativas" class="text-xl font-black text-slate-800">0</p>
            </div>
            <div class="bg-white border border-slate-200 text-left p-4 rounded-lg shadow-sm">
                <p class="text-[11px] uppercase text-slate-500 font-semibold mb-1 flex items-center gap-1"><i class="fa-regular fa-circle-check text-green-500"></i> Enviados</p>
                <p id="hsTotalEnviados" class="text-xl font-black text-green-600">0</p>
            </div>
            <div class="bg-white border border-slate-200 text-left p-4 rounded-lg shadow-sm">
                <p class="text-[11px] uppercase text-slate-500 font-semibold mb-1 flex items-center gap-1"><i class="fa-solid fa-hourglass-half text-slate-400"></i> Pendentes</p>
                <p id="hsTotalPendentes" class="text-xl font-black text-slate-400">0</p>
            </div>
            <div class="bg-white border border-slate-200 text-left p-4 rounded-lg shadow-sm">
                <p class="text-[11px] uppercase text-slate-500 font-semibold mb-1 flex items-center gap-1"><i class="fa-solid fa-ban text-red-500"></i> Ignorados (opt-out)</p>
                <p id="hsTotalIgnorados" class="text-xl font-black text-red-600">0</p>
            </div>
            <div class="bg-white border border-slate-200 text-left p-4 rounded-lg shadow-sm">
                <p class="text-[11px] uppercase text-slate-500 font-semibold mb-1 flex items-center gap-1"><i class="fa-solid fa-phone-slash text-yellow-500"></i> Núm. Inválidos</p>
                <p id="hsTotalErros" class="text-xl font-black text-yellow-500">0</p>
            </div>
        </div>

        <!-- Past Campaign Table -->
        <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-calendar text-slate-500"></i> Past Campaign
        </h2>
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden mb-8">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[11px] font-semibold uppercase">
                    <tr>
                        <th class="px-4 py-3">Campanha</th>
                        <th class="px-4 py-3 text-center">Total</th>
                        <th class="px-4 py-3 text-center">Enviados</th>
                        <th class="px-4 py-3 text-center">Núm. Inválidos</th>
                        <th class="px-4 py-3 text-center">Ignorados (opt-out)</th>
                        <th class="px-4 py-3 text-center">Ação</th>
                    </tr>
                </thead>
                <tbody id="historicoTabelaBody" class="divide-y divide-slate-100">
                    <!-- Rows rendered via JS -->
                </tbody>
            </table>
            <div id="historicoVazio" class="py-8 text-center text-slate-400 text-sm hidden">
                Nenhuma campanha registrada para esta instância.
            </div>
        </div>
    </div>
</div>


<script>
// ──────────────────────────────────────────────
// ESTADO GLOBAL
// ──────────────────────────────────────────────
let contatos = [];
let idxAtual = 0;
let enviando = false;
let parado   = false; // Flag para parar o disparo
let stats    = { enviados: 0, duplicados: 0, erros: 0 };

// ──────────────────────────────────────────────
// DEDUPLICAÇÃO via localStorage
// ──────────────────────────────────────────────
const STORAGE_KEY = 'wam_numeros_enviados';

function getHistorico() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); } catch(e) { return []; }
}

function registrarEnviado(numero) {
    let hist = getHistorico();
    if (!hist.includes(numero)) {
        hist.push(numero);
        localStorage.setItem(STORAGE_KEY, JSON.stringify(hist));
    }
}

function jaFoiEnviado(numero) {
    return getHistorico().includes(numero);
}

function limparHistorico() {
    if(!confirm('Apagar todo o histórico de números enviados? Eles poderão receber mensagens novamente.')) return;
    localStorage.removeItem(STORAGE_KEY);
    adicionarLog('🗑️ Histórico limpo.', 'slate');
}

// ──────────────────────────────────────────────
// LOG VISUAL
// ──────────────────────────────────────────────
function adicionarLog(msg, cor = 'slate') {
    const colors = {
        green:  'text-green-600',
        yellow: 'text-yellow-600',
        red:    'text-red-500',
        slate:  'text-slate-500',
        blue:   'text-blue-500',
    };
    let logList = document.getElementById('logList');
    let el = document.createElement('div');
    el.className = colors[cor] || colors.slate;
    el.innerText = new Date().toLocaleTimeString('pt-BR') + ' › ' + msg;
    logList.appendChild(el);
    logList.scrollTop = logList.scrollHeight;
}

// ──────────────────────────────────────────────
// GRÁFICO DE RESULTADOS
// ──────────────────────────────────────────────
function atualizarGrafico() {
    const total = stats.enviados + stats.duplicados + stats.erros || 1;
    document.getElementById('cntEnviados').innerText   = stats.enviados;
    document.getElementById('cntDuplicados').innerText = stats.duplicados;
    document.getElementById('cntErros').innerText      = stats.erros;
    document.getElementById('barEnviados').style.width   = ((stats.enviados   / total) * 100) + '%';
    document.getElementById('barDuplicados').style.width = ((stats.duplicados / total) * 100) + '%';
    document.getElementById('barErros').style.width      = ((stats.erros      / total) * 100) + '%';
    document.getElementById('resultChart').classList.remove('hidden');
}

// ──────────────────────────────────────────────
// BOTÃO PARAR
// ──────────────────────────────────────────────
function pararDisparos() {
    parado   = true;
    enviando = false;
    document.getElementById('btnParar').classList.add('hidden');
    document.getElementById('btnIniciar').disabled = false;
    document.getElementById('btnIniciar').innerHTML = '<i class="fa-solid fa-rocket mr-2"></i> Iniciar Disparo';
    document.getElementById('statusMsg').innerText = '⛔ Disparo interrompido pelo usuário.';
    document.getElementById('progressSub').innerText = 'parado';
    adicionarLog('⛔ Disparo interrompido pelo usuário.', 'red');
    atualizarGrafico();
}

// ──────────────────────────────────────────────
// FILE INPUT – CSV DROPZONE
// ──────────────────────────────────────────────
document.getElementById('csvDrop').addEventListener('click', () => {
    let input = document.createElement('input');
    input.type = 'file';
    input.accept = '.csv,.txt';
    input.onchange = e => {
        let file = e.target.files[0];
        let reader = new FileReader();
        reader.onload = evt => { parseCSV(evt.target.result); };
        reader.readAsText(file, 'UTF-8');
    };
    input.click();
});

// ──────────────────────────────────────────────
// FORMATADOR BR
// ──────────────────────────────────────────────
function tratarNumeroBR(numStr) {
    let num = String(numStr).replace(/\D/g, '');
    if(num.length === 0) return '';
    if(!num.startsWith('55')) {
        if(num.length === 10 || num.length === 11) {
            num = '55' + num;
        } else if(num.length > 11) {
            // Deixa como está (pode já ter 55 em outro formato)
        } else {
            num = '55' + num;
        }
    }
    return num;
}

// ──────────────────────────────────────────────
// PARSERS
// ──────────────────────────────────────────────
function parseCSV(text) {
    let linhas = text.trim().split('\n');
    contatos = [];
    for(let i = 0; i < linhas.length; i++) {
        if(linhas[i].trim() === '') continue;
        let p = linhas[i].split(',');
        if(p.length >= 2) {
            let numeroBr = tratarNumeroBR(p[1]);
            if(numeroBr.length >= 12) {
                contatos.push({ nome: (p[0] || 'Amigo').trim(), numero: numeroBr });
            }
        }
    }
    document.getElementById('statusMsg').innerText = contatos.length + " contatos (CSV) carregados. Clique em Iniciar.";
    document.getElementById('progressText').innerText = contatos.length;
    document.getElementById('progressSub').innerText = 'na fila';
    document.getElementById('progressText').classList.replace('text-slate-400', 'text-primary-500');
}

function contarNumerosManuais() {
    let text = document.getElementById('manualNumbers').value;
    let parts = text.split(',');
    let validos = 0;
    for(let i=0; i<parts.length; i++) {
        if(tratarNumeroBR(parts[i]).length >= 12) validos++;
    }
    document.getElementById('manualCountBadge').innerText = validos + ' identificados';
}

function carregarManuais() {
    let text = document.getElementById('manualNumbers').value;
    if(!text.trim()) { alert('Cole os números na caixa primeiro!'); return false; }
    
    let parts = text.split(',');
    contatos = [];
    
    for(let i = 0; i < parts.length; i++) {
        let numeroBr = tratarNumeroBR(parts[i]);
        if(numeroBr.length >= 12) {
            contatos.push({ nome: 'Amigo', numero: numeroBr });
        }
    }
    
    if(contatos.length === 0) {
        alert('Nenhum número válido encontrado. Verifique se os números têm DDD.');
        return false;
    }
    
    document.getElementById('statusMsg').innerText = contatos.length + " números manuais na fila.";
    document.getElementById('progressText').innerText = contatos.length;
    document.getElementById('progressSub').innerText = 'na fila';
    document.getElementById('progressText').classList.replace('text-slate-400', 'text-primary-500');
    return true;
}

// ──────────────────────────────────────────────
// INICIAR DISPAROS
// ──────────────────────────────────────────────
async function iniciarDisparos() {
    let instancia  = document.getElementById('instanciaSelect').value;
    let template   = document.getElementById('templateMsg').value;
    let manualTxt  = document.getElementById('manualNumbers').value;

    if(!instancia) return alert('Selecione uma instância!');
    
    if(contatos.length === 0 && manualTxt.trim() !== '') {
        let sucesso = carregarManuais();
        if(!sucesso) return;
    }

    if(contatos.length === 0) return alert('Por favor, carregue um CSV ou insira números manuais.');
    if(!template.trim())      return alert('Digite a mensagem padrão.');

    if(enviando) return;

    // Reset stats
    stats    = { enviados: 0, duplicados: 0, erros: 0 };
    enviando = true;
    parado   = false;
    idxAtual = 0;

    document.getElementById('btnIniciar').innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Enviando...';
    document.getElementById('btnIniciar').disabled  = true;
    document.getElementById('btnParar').classList.remove('hidden');
    document.getElementById('logContainer').classList.remove('hidden');
    document.getElementById('resultChart').classList.add('hidden');
    document.getElementById('logList').innerHTML = '';

    processarFila(instancia, template);
}

// ──────────────────────────────────────────────
// FILA DE PROCESSAMENTO
// ──────────────────────────────────────────────
async function processarFila(instancia, template) {
    // Verifica parada
    if(parado) return;

    // Conclui
    if(idxAtual >= contatos.length) {
        document.getElementById('statusMsg').innerText  = `✅ Disparo concluído! ${stats.enviados} enviados.`;
        document.getElementById('btnIniciar').innerHTML = '<i class="fa-solid fa-rocket mr-2"></i> Iniciar Disparo';
        document.getElementById('btnIniciar').disabled  = false;
        document.getElementById('btnParar').classList.add('hidden');
        document.getElementById('progressText').innerText = '100%';
        document.getElementById('progressSub').innerText  = 'concluído';
        enviando = false;
        atualizarGrafico();
        // Grava campanha no histórico
        gravarCampanha(document.getElementById('instanciaSelect').value);
        // Limpa nome da campanha para o próximo
        document.getElementById('campaignName').value = '';
        return;
    }

    let c        = contatos[idxAtual];
    let msgFinal = template.replace(/{nome}/g, c.nome);

    // ── DEDUPLICAÇÃO ──
    if(jaFoiEnviado(c.numero)) {
        stats.duplicados++;
        adicionarLog(`⚠️ Duplicado: ${c.numero} (${c.nome}) — ignorado.`, 'yellow');
        idxAtual++;
        atualizarGrafico();
        setTimeout(() => processarFila(instancia, template), 200); // Avança rápido sem delay
        return;
    }

    // Atualiza UI progresso
    let pct = Math.floor((idxAtual / contatos.length) * 100);
    document.getElementById('progressText').innerText = pct + '%';
    document.getElementById('progressSub').innerText  = `${idxAtual+1}/${contatos.length}`;
    document.getElementById('statusMsg').innerText    = `Enviando para ${c.nome} (${c.numero})...`;

    try {
        let req = await fetch('/api/enviar-mensagem', {
            method:  'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ instancia, numero: c.numero, mensagem: msgFinal })
        });
        let res = await req.json();

        if(req.ok && !res.error) {
            stats.enviados++;
            registrarEnviado(c.numero); // Marca no histórico
            adicionarLog(`✅ Enviado: ${c.nome} (${c.numero})`, 'green');
        } else {
            stats.erros++;
            adicionarLog(`❌ Erro: ${c.nome} (${c.numero}) — ${JSON.stringify(res)}`, 'red');
        }
    } catch(e) {
        stats.erros++;
        adicionarLog(`❌ Falha de rede: ${c.numero}`, 'red');
    }

    idxAtual++;
    atualizarGrafico();

    // Se ainda houver contatos e não foi parado, aplica delay anti-ban
    if(!parado && idxAtual < contatos.length) {
        let delayMs = Math.floor(Math.random() * (15000 - 5000 + 1)) + 5000;
        document.getElementById('statusMsg').innerText = `⏳ Anti-Ban: aguardando ${(delayMs/1000).toFixed(1)}s...`;
        document.getElementById('progressSub').innerText = 'delay';
        setTimeout(() => processarFila(instancia, template), delayMs);
    } else {
        setTimeout(() => processarFila(instancia, template), 200);
    }
}

// ──────────────────────────────────────────────
// HISTÓRICO DE CAMPANHAS (por instância)
// ──────────────────────────────────────────────
const HIST_KEY = 'wam_campanhas';

function gravarCampanha(instanciaId) {
    if(!instanciaId) return;
    let lista = JSON.parse(localStorage.getItem(HIST_KEY) || '{}');
    if(!lista[instanciaId]) lista[instanciaId] = [];
    let nomeCampanha = document.getElementById('campaignName').value.trim() || `Campanha ${new Date().toLocaleDateString('pt-BR')}`;
    
    lista[instanciaId].unshift({
        id:         'camp_' + Date.now(),
        nome:       nomeCampanha,
        data:       new Date().toLocaleString('pt-BR'),
        total:      contatos.length,
        enviados:   stats.enviados,
        duplicados: stats.duplicados,
        erros:      stats.erros,
    });
    // Mantém somente as últimas 10 campanhas
    lista[instanciaId] = lista[instanciaId].slice(0, 10);
    localStorage.setItem(HIST_KEY, JSON.stringify(lista));
    // Atualiza painel sem precisar recarregar
    renderizarHistorico(instanciaId, lista[instanciaId]);
}

function verHistorico(instanciaId) {
    const div = document.getElementById('historicoGeral');
    if(!instanciaId) { div.classList.add('hidden'); return; }
    div.classList.remove('hidden');
    let lista = JSON.parse(localStorage.getItem(HIST_KEY) || '{}');
    renderizarHistorico(instanciaId, lista[instanciaId] || []);
}

function renderizarHistorico(instanciaId, campanhas) {
    const tbody = document.getElementById('historicoTabelaBody');
    const vazio = document.getElementById('historicoVazio');
    tbody.innerHTML = '';
    
    // Calcula totais
    let tTentativas = 0, tEnviados = 0, tIgnorados = 0, tErros = 0, tPendentes = 0;

    if(!campanhas || campanhas.length === 0) {
        vazio.classList.remove('hidden');
    } else {
        vazio.classList.add('hidden');
        campanhas.forEach((c, index) => {
            tTentativas += parseInt(c.total || 0);
            tEnviados   += parseInt(c.enviados || 0);
            tIgnorados  += parseInt(c.duplicados || 0);
            tErros      += parseInt(c.erros || 0);

            let row = document.createElement('tr');
            row.className = "hover:bg-slate-50 transition-colors";
            
            // Usa o nome gravado na campanha ou fallback
            let nomeExibir = c.nome || `Campanha ${c.data.split(' ')[0]}`;

            row.innerHTML = `
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 bg-primary-50 rounded text-primary-600"><i class="fa-solid fa-message text-[10px]"></i></div>
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-700 text-sm">${nomeExibir}</span>
                            <span class="text-[10px] text-slate-400">${c.data}</span>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-center text-slate-700 font-medium">${c.total}</td>
                <td class="px-4 py-3 text-center text-green-600 font-semibold">${c.enviados}</td>
                <td class="px-4 py-3 text-center text-yellow-600 font-medium">${c.erros}</td>
                <td class="px-4 py-3 text-center text-red-500 font-medium flex justify-center items-center gap-2">
                    <i class="fa-solid fa-lock text-slate-300 text-[10px]"></i> ${c.duplicados}
                </td>
                <td class="px-4 py-3 text-center">
                    <button class="text-slate-400 hover:text-primary-600 transition-colors mx-1"><i class="fa-solid fa-download"></i></button>
                    <button class="text-slate-400 hover:text-green-600 transition-colors mx-1"><i class="fa-solid fa-arrow-up-right-from-square"></i></button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    document.getElementById('hsTotalTentativas').innerText = tTentativas;
    document.getElementById('hsTotalEnviados').innerText   = tEnviados;
    document.getElementById('hsTotalIgnorados').innerText  = tIgnorados;
    document.getElementById('hsTotalErros').innerText      = tErros;
    document.getElementById('hsTotalPendentes').innerText  = tPendentes; // No flow pending when done
}
</script>


