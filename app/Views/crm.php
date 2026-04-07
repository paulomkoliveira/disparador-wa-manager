<?php
$colunas = [
    'novo'        => ['label' => 'Novo Lead',        'cor' => 'blue',   'icon' => 'fa-user-plus'],
    'contato'     => ['label' => 'Em Contato',       'cor' => 'yellow', 'icon' => 'fa-phone'],
    'proposta'    => ['label' => 'Proposta Enviada', 'cor' => 'orange', 'icon' => 'fa-file-invoice'],
    'fechado'     => ['label' => 'Fechado ✓',        'cor' => 'green',  'icon' => 'fa-circle-check'],
    'perdido'     => ['label' => 'Perdido',          'cor' => 'red',    'icon' => 'fa-circle-xmark'],
];

// Agrupa leads por coluna
$porColuna = [];
foreach (array_keys($colunas) as $col) {
    $porColuna[$col] = [];
}
foreach ($leads as $lead) {
    $col = $lead['coluna'] ?? 'novo';
    if (isset($porColuna[$col])) {
        $porColuna[$col][] = $lead;
    }
}

// Métricas para relatório
$totalLeads     = count($leads);
$totalFechados  = count($porColuna['fechado']);
$totalPerdidos  = count($porColuna['perdido']);
$valorTotal     = array_sum(array_column($leads, 'valor'));
$valorFechado   = array_sum(array_column($porColuna['fechado'], 'valor'));
$taxaConversao  = $totalLeads > 0 ? round(($totalFechados / $totalLeads) * 100, 1) : 0;

$corMap = [
    'blue'   => 'bg-blue-100 border-blue-300 text-blue-800',
    'yellow' => 'bg-yellow-100 border-yellow-300 text-yellow-800',
    'orange' => 'bg-orange-100 border-orange-300 text-orange-800',
    'green'  => 'bg-green-100 border-green-300 text-green-800',
    'red'    => 'bg-red-100 border-red-300 text-red-800',
];
$headerCorMap = [
    'blue'   => 'bg-blue-500',
    'yellow' => 'bg-yellow-500',
    'orange' => 'bg-orange-500',
    'green'  => 'bg-green-500',
    'red'    => 'bg-red-500',
];
?>

<div class="p-6 h-full flex flex-col">
    <header class="mb-6 flex justify-between items-center flex-shrink-0">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">CRM / Kanban de Leads</h1>
            <p class="text-slate-500 text-sm">Gerencie seus leads e atendimentos</p>
        </div>
        <button onclick="abrirModalLead()" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-lg shadow flex items-center transition-colors">
            <i class="fa-solid fa-plus mr-2"></i> Novo Lead
        </button>
    </header>

    <!-- Relatório de Desempenho -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6 flex-shrink-0">
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <p class="text-xs text-slate-400 uppercase font-medium">Total Leads</p>
            <p class="text-3xl font-black text-slate-800"><?= $totalLeads ?></p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <p class="text-xs text-slate-400 uppercase font-medium">Fechados</p>
            <p class="text-3xl font-black text-green-600"><?= $totalFechados ?></p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <p class="text-xs text-slate-400 uppercase font-medium">Perdidos</p>
            <p class="text-3xl font-black text-red-500"><?= $totalPerdidos ?></p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <p class="text-xs text-slate-400 uppercase font-medium">Valor Fechado</p>
            <p class="text-2xl font-black text-green-700">R$ <?= number_format($valorFechado, 2, ',', '.') ?></p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <p class="text-xs text-slate-400 uppercase font-medium">Taxa Conversão</p>
            <p class="text-3xl font-black text-primary-600"><?= $taxaConversao ?>%</p>
        </div>
    </div>

    <!-- Barra de progresso de conversão -->
    <div class="mb-6 flex-shrink-0 bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
        <div class="flex justify-between text-xs text-slate-500 mb-2">
            <span>Funil de Conversão (<?= $totalLeads ?> leads no total)</span>
            <span>Valor Pipeline: <strong class="text-slate-700">R$ <?= number_format($valorTotal, 2, ',', '.') ?></strong></span>
        </div>
        <div class="flex h-4 rounded-full overflow-hidden bg-slate-100">
            <?php foreach ($colunas as $col => $info):
                $cnt = count($porColuna[$col]);
                $pct = $totalLeads > 0 ? round(($cnt / $totalLeads) * 100) : 0;
                $colors = ['blue'=>'bg-blue-400','yellow'=>'bg-yellow-400','orange'=>'bg-orange-400','green'=>'bg-green-500','red'=>'bg-red-400'];
            ?>
                <div class="<?= $colors[$info['cor']] ?> h-full transition-all" style="width:<?= $pct ?>%" title="<?= $info['label'] ?>: <?= $cnt ?>"></div>
            <?php endforeach; ?>
        </div>
        <div class="flex gap-4 mt-2 flex-wrap">
            <?php foreach ($colunas as $col => $info): 
                $cnt = count($porColuna[$col]);
                $dotColors = ['blue'=>'bg-blue-400','yellow'=>'bg-yellow-400','orange'=>'bg-orange-400','green'=>'bg-green-500','red'=>'bg-red-400'];
            ?>
                <span class="flex items-center text-xs text-slate-500 gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full <?= $dotColors[$info['cor']] ?>"></span>
                    <?= $info['label'] ?>: <?= $cnt ?>
                </span>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Board Kanban -->
    <div class="flex gap-4 flex-1 overflow-x-auto pb-4">
        <?php foreach ($colunas as $colId => $info): ?>
        <div class="kanban-col flex-shrink-0 w-72 flex flex-col" data-col="<?= $colId ?>">
            <!-- Header da Coluna -->
            <div class="<?= $headerCorMap[$info['cor']] ?> text-white px-4 py-2.5 rounded-t-xl flex items-center justify-between">
                <span class="font-bold text-sm flex items-center gap-2">
                    <i class="fa-solid <?= $info['icon'] ?>"></i>
                    <?= $info['label'] ?>
                </span>
                <span class="bg-white/30 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                    <?= count($porColuna[$colId]) ?>
                </span>
            </div>

            <!-- Área de Drop -->
            <div class="kanban-drop-area flex-1 bg-slate-100 rounded-b-xl p-2 space-y-2 min-h-[200px]"
                 ondrop="drop(event, '<?= $colId ?>')"
                 ondragover="allowDrop(event)">
                <?php foreach ($porColuna[$colId] as $lead): ?>
                <div class="kanban-card bg-white rounded-lg p-3 shadow-sm border border-slate-200 cursor-grab hover:shadow-md transition-shadow"
                     draggable="true"
                     ondragstart="drag(event, '<?= $lead['id'] ?>')"
                     data-id="<?= $lead['id'] ?>">
                    <div class="flex justify-between items-start mb-1">
                        <h4 class="font-bold text-slate-800 text-sm truncate flex-1"><?= htmlspecialchars($lead['nome']) ?></h4>
                        <button onclick="editarLead('<?= $lead['id'] ?>')" class="text-slate-300 hover:text-primary-500 ml-1 transition-colors"><i class="fa-solid fa-pen text-xs"></i></button>
                    </div>
                    <?php if ($lead['telefone']): ?>
                        <p class="text-xs text-slate-400 mb-1"><i class="fa-solid fa-phone text-[10px] mr-1"></i><?= htmlspecialchars($lead['telefone']) ?></p>
                    <?php endif; ?>
                    <?php if ($lead['descricao']): ?>
                        <p class="text-xs text-slate-500 mb-2 line-clamp-2"><?= htmlspecialchars($lead['descricao']) ?></p>
                    <?php endif; ?>
                    <div class="flex justify-between items-center mt-2 pt-2 border-t border-slate-100">
                        <?php if ($lead['valor'] > 0): ?>
                            <span class="text-xs font-bold text-green-600"><i class="fa-solid fa-dollar-sign text-[10px] mr-0.5"></i>R$ <?= number_format($lead['valor'], 2, ',', '.') ?></span>
                        <?php else: ?>
                            <span class="text-xs text-slate-300">Sem valor</span>
                        <?php endif; ?>
                        <button onclick="confirmarExcluir('<?= $lead['id'] ?>')" class="text-slate-200 hover:text-red-400 transition-colors"><i class="fa-solid fa-trash-can text-xs"></i></button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal de Lead -->
<dialog id="modalLead" class="bg-transparent overflow-visible p-0 w-full max-w-md m-auto inset-0 fixed z-50 rounded-xl backdrop:bg-slate-900/60">
    <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex justify-between items-center bg-primary-600">
            <h3 id="modalLeadTitulo" class="text-lg font-bold text-white"><i class="fa-solid fa-user-plus mr-2"></i>Novo Lead</h3>
            <button onclick="fecharModalLead()" class="text-white/70 hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></button>
        </div>
        <div class="p-5 space-y-4">
            <input type="hidden" id="leadId">
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nome *</label>
                    <input type="text" id="leadNome" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Nome do Lead">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Telefone</label>
                    <input type="text" id="leadTelefone" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm" placeholder="55119999...">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Valor (R$)</label>
                    <input type="number" id="leadValor" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm" placeholder="0.00" step="0.01" min="0">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Coluna</label>
                    <select id="leadColuna" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm">
                        <option value="novo">Novo Lead</option>
                        <option value="contato">Em Contato</option>
                        <option value="proposta">Proposta Enviada</option>
                        <option value="fechado">Fechado</option>
                        <option value="perdido">Perdido</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Descrição</label>
                    <textarea id="leadDescricao" rows="3" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm resize-none" placeholder="Informações relevantes sobre o lead..."></textarea>
                </div>
            </div>
            <button onclick="salvarLeadForm()" class="w-full py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-lg transition-colors flex items-center justify-center">
                <i class="fa-solid fa-floppy-disk mr-2"></i> Salvar Lead
            </button>
        </div>
    </div>
</dialog>

<!-- Dados para JS -->
<script>
const leadsData = <?= json_encode($leads, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) ?>;

// ── MODAL ──────────────────────────────────
function abrirModalLead() {
    document.getElementById('leadId').value = '';
    document.getElementById('leadNome').value = '';
    document.getElementById('leadTelefone').value = '';
    document.getElementById('leadValor').value = '';
    document.getElementById('leadDescricao').value = '';
    document.getElementById('leadColuna').value = 'novo';
    document.getElementById('modalLeadTitulo').innerHTML = '<i class="fa-solid fa-user-plus mr-2"></i>Novo Lead';
    document.getElementById('modalLead').showModal();
}

function fecharModalLead() { document.getElementById('modalLead').close(); }

function editarLead(id) {
    const l = leadsData.find(x => x.id === id);
    if(!l) return;
    document.getElementById('leadId').value = l.id;
    document.getElementById('leadNome').value = l.nome || '';
    document.getElementById('leadTelefone').value = l.telefone || '';
    document.getElementById('leadValor').value = l.valor || 0;
    document.getElementById('leadDescricao').value = l.descricao || '';
    document.getElementById('leadColuna').value = l.coluna || 'novo';
    document.getElementById('modalLeadTitulo').innerHTML = '<i class="fa-solid fa-pen mr-2"></i>Editar Lead';
    document.getElementById('modalLead').showModal();
}

async function salvarLeadForm() {
    const nome = document.getElementById('leadNome').value.trim();
    if(!nome) return alert('Nome é obrigatório!');

    const payload = {
        id:        document.getElementById('leadId').value || null,
        nome:      nome,
        telefone:  document.getElementById('leadTelefone').value,
        valor:     parseFloat(document.getElementById('leadValor').value || 0),
        descricao: document.getElementById('leadDescricao').value,
        coluna:    document.getElementById('leadColuna').value,
    };

    await fetch('/api/crm/salvar-lead', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(payload)
    });
    fecharModalLead();
    location.reload();
}

async function confirmarExcluir(id) {
    if(!confirm('Excluir este lead?')) return;
    await fetch('/api/crm/excluir-lead', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ id })
    });
    location.reload();
}

// ── DRAG & DROP ────────────────────────────
let dragId = null;

function drag(e, id) {
    dragId = id;
    e.dataTransfer.effectAllowed = 'move';
}

function allowDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.add('ring-2', 'ring-primary-400', 'bg-primary-50');
}

function drop(e, colunaDestino) {
    e.preventDefault();
    e.currentTarget.classList.remove('ring-2', 'ring-primary-400', 'bg-primary-50');
    if(!dragId) return;

    fetch('/api/crm/mover-lead', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ id: dragId, coluna: colunaDestino })
    }).then(() => location.reload());
}

// Remove highlight ao sair da área de drop
document.querySelectorAll('.kanban-drop-area').forEach(el => {
    el.addEventListener('dragleave', () => {
        el.classList.remove('ring-2', 'ring-primary-400', 'bg-primary-50');
    });
});
</script>
