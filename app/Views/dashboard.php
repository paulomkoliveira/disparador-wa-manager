<div class="p-8">
    <header class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Dashboard</h1>
            <p class="text-slate-500">Resumo das suas instâncias e envios recentes</p>
        </div>
        <button onclick="document.getElementById('modalNovaInstancia').showModal()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg shadow-md transition-colors flex items-center">
            <i class="fa-solid fa-plus mr-2"></i> Nova Instância
        </button>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center">
            <div class="w-12 h-12 rounded-full bg-primary-100 flexitems-center justify-center text-primary-600 text-xl mr-4">
                <i class="fa-solid fa-server mt-3 ml-3"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Instâncias Ativas</p>
                <p class="text-2xl font-bold text-slate-800"><?= count($instancias) ?></p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center">
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xl mr-4">
                <i class="fa-solid fa-paper-plane mt-3 ml-3"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Mensagens Enviadas</p>
                <p class="text-2xl font-bold text-slate-800">0</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center">
            <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 text-xl mr-4">
                <i class="fa-solid fa-users mt-3 ml-3"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Contatos Salvos</p>
                <p class="text-2xl font-bold text-slate-800">0</p>
            </div>
        </div>
    </div>

    <!-- Lista de instâncias -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="border-b border-slate-200 px-6 py-4">
            <h2 class="text-lg font-bold text-slate-800">Instâncias Evolution API</h2>
        </div>
        <div class="p-6">
            <?php if(empty($instancias)): ?>
                <div class="text-center py-8 text-slate-500">
                    <i class="fa-solid fa-qrcode text-4xl mb-3 text-slate-300"></i>
                    <p>Nenhuma instância conectada.</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach($instancias as $inst): ?>
                        <div class="flex items-center justify-between p-4 border border-slate-200 rounded-lg hover:border-primary-300 transition-colors">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 mr-4">
                                    <i class="fa-brands fa-whatsapp text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800"><?= htmlspecialchars($inst['nome']) ?></p>
                                    <p class="text-xs text-green-500"><i class="fa-solid fa-circle text-[8px] mr-1"></i><?= htmlspecialchars($inst['status']) ?></p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button class="px-3 py-1.5 text-sm bg-slate-100 hover:bg-slate-200 text-slate-700 rounded transition-colors" onclick="alert('Conectar nova instância em desenvolvimento!')"><i class="fa-solid fa-link"></i> Reconectar</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Nova Instância -->
<dialog id="modalNovaInstancia" class="bg-transparent overflow-visible p-0 w-full max-w-md m-auto inset-0 fixed z-50 rounded-xl backdrop:bg-slate-900/50">
    <div class="bg-white rounded-xl shadow-2xl overflow-hidden flex flex-col">
        <div class="p-6 border-b border-slate-200 flex justify-between items-center">
            <h3 class="text-xl font-bold text-slate-800">Conectar Nova Instância</h3>
            <button onclick="document.getElementById('modalNovaInstancia').close()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nome da Instância</label>
                <input type="text" id="novaInstanciaNome" class="w-full border-slate-300 rounded-lg p-2.5 border focus:ring-primary-500 focus:border-primary-500" placeholder="Ex: meu_numero_suporte">
                <p class="text-xs text-slate-500 mt-1">Este nome será criado na Evolution API.</p>
            </div>
            <button onclick="salvarNovaInstancia()" class="w-full py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-lg shadow transition-colors flex justify-center items-center">
                <i class="fa-solid fa-cloud-bolt mr-2"></i> Adicionar Instância
            </button>
        </div>
    </div>
</dialog>

<script>
async function salvarNovaInstancia() {
    let nome = document.getElementById('novaInstanciaNome').value;
    if(!nome) return alert('Digite o nome da instância!');
    
    // Na próxima iteração vamos fazer o POST `/api/nova-instancia`
    // Para resolver agora de forma rápida vamos dar alert
    alert('Função na fila do backend para criar "'+nome+'" no Supabase e Evolution!');
    document.getElementById('modalNovaInstancia').close();
}
</script>
