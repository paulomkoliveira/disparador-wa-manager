<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\SupabaseClient;

class ChatController extends Controller {

    // Caminho do cache local de conversas por instância
    private function cacheFile(string $instancia): string {
        $dir = dirname(__DIR__, 2) . '/storage/chats';
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        return $dir . '/' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $instancia) . '.json';
    }

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit();
        }
    }

    public function index() {
        $supabase = new SupabaseClient();
        $userId   = $_SESSION['user_id'];
        $instancias = $supabase->select('instancias_api', "usuario_id=eq.$userId");
        if (!is_array($instancias)) $instancias = [];

        // Mock instância local
        array_push($instancias, [
            'id'     => 'mock-id-01',
            'nome'   => 'pauloprofissional',
            'status' => 'online',
            'url'    => $_ENV['EVOLUTION_API_URL']
        ]);

        // Instância ativa (via query string ou primeira da lista)
        $instanciaAtivaNome = '';
        if (!empty($_GET['instancia'])) {
            $instanciaAtivaNome = $_GET['instancia'];
        } elseif (count($instancias) > 0) {
            $instanciaAtivaNome = $instancias[0]['nome'];
        }

        $conversas = [];

        if (!empty($instanciaAtivaNome)) {
            $evoService = new \App\Services\EvolutionService();
            $cacheFile  = $this->cacheFile($instanciaAtivaNome);
            $agora      = time();
            $seteDias   = 7 * 24 * 3600; // 7 dias em segundos

            // ────────────────────────────────────────────────────
            // 1. Lê cache local (conversas já registradas)
            // ────────────────────────────────────────────────────
            $cacheAtual = [];
            if (file_exists($cacheFile)) {
                $cacheAtual = json_decode(file_get_contents($cacheFile), true) ?? [];
            }

            // ────────────────────────────────────────────────────
            // 2. Busca lista de chats da Evolution API
            // ────────────────────────────────────────────────────
            $rawChats = $evoService->fetchChats($instanciaAtivaNome);
            if (!is_array($rawChats)) $rawChats = [];

            // ────────────────────────────────────────────────────
            // 3. Filtra apenas conversas com atividade nos últimos 7 dias
            //    e mescla com o cache (para manter conversas registradas)
            // ────────────────────────────────────────────────────
            $novasIds = [];

            foreach ($rawChats as $chat) {
                $jid = $chat['remoteJid'] ?? $chat['id'] ?? null;
                if (!$jid) continue;

                // Timestamp da última mensagem (campo pode variar na versão da Evolution)
                $ts = $chat['lastMessage']['messageTimestamp']
                    ?? $chat['updatedAt']
                    ?? $chat['timestamp']
                    ?? 0;

                // Se vier como string ISO (ex: "2024-04-06T..."), converte
                if (is_string($ts) && !is_numeric($ts)) {
                    $ts = strtotime($ts);
                }

                // Considera ativo se o timestamp for dos últimos 7 dias
                // Se não houver timestamp, inclui por precaução
                $ativo = ($ts == 0) || (($agora - (int)$ts) <= $seteDias);

                if ($ativo) {
                    $novasIds[$jid] = true;
                    // Mescla / atualiza no cache
                    $cacheAtual[$jid] = [
                        'remoteJid'   => $jid,
                        'name'        => $chat['name'] ?? $chat['pushName'] ?? null,
                        'pushName'    => $chat['pushName'] ?? null,
                        'timestamp'   => $ts ?: $agora,
                        'registrado'  => $cacheAtual[$jid]['registrado'] ?? date('Y-m-d H:i:s'),
                        'raw'         => $chat,
                    ];
                }
            }

            // ────────────────────────────────────────────────────
            // 4. Do cache, mantém conversas dos últimos 7 dias
            //    (inclui as registradas que não vieram na resposta atual)
            // ────────────────────────────────────────────────────
            $merged = [];
            foreach ($cacheAtual as $jid => $entry) {
                $ts = $entry['timestamp'] ?? 0;
                if ($ts == 0 || (($agora - (int)$ts) <= $seteDias)) {
                    $merged[$jid] = $entry;
                }
            }

            // Ordena por timestamp decrescente (mais recente primeiro)
            uasort($merged, fn($a, $b) => ($b['timestamp'] ?? 0) <=> ($a['timestamp'] ?? 0));

            // Salva cache atualizado
            file_put_contents($cacheFile, json_encode($merged, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            // Converte para array indexado para a view
            $conversas = array_values($merged);
        }

        $this->view('chat', [
            'instancias'     => $instancias,
            'conversas'      => $conversas,
            'instanciaAtiva' => $instanciaAtivaNome
        ]);
    }
}
