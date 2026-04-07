<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\EvolutionService;

class ApiController extends Controller {

    public function enviar() {
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['instancia']) || !isset($data['numero']) || !isset($data['mensagem'])) {
            echo json_encode(['success' => false, 'error' => 'Parâmetros inválidos.']);
            return;
        }

        $evolutionApiKey = $_ENV['EVOLUTION_API_TOKEN'];
        if (!$evolutionApiKey) {
            echo json_encode(['success' => false, 'error' => 'API Token não configurado no backend.']);
            return;
        }

        $evoService = new EvolutionService();
        $response = $evoService->sendMessageText($data['instancia'], $data['numero'], $data['mensagem']);

        if (isset($response['key']) && isset($response['key']['id'])) {
            echo json_encode(['success' => true, 'data' => $response]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Falha no envio da mensagem', 'debug' => $response]);
        }
    }

    /**
     * Lazy loading de mensagens de um chat - chamado via AJAX no frontend
     * POST /api/mensagens { instancia, remoteJid, page }
     */
    public function mensagens() {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['instancia']) || !isset($data['remoteJid'])) {
            echo json_encode(['success' => false, 'error' => 'Faltam parâmetros instancia/remoteJid.']);
            return;
        }

        $page  = (int)($data['page'] ?? 1);
        $limit = (int)($data['limit'] ?? 20);

        try {
            $evoService = new EvolutionService();
            $response   = $evoService->fetchMessages($data['instancia'], $data['remoteJid'], $limit, $page);
            
            $mensagens = [];
            if (isset($response['messages']['records'])) {
                $mensagens = $response['messages']['records'];
            } elseif (isset($response['records'])) {
                $mensagens = $response['records'];
            } elseif (is_array($response) && !isset($response['messages'])) {
                $mensagens = $response;
            }

            echo json_encode(['success' => true, 'mensagens' => $mensagens ?? [], 'page' => $page]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

}
