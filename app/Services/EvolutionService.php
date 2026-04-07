<?php

namespace App\Services;

use GuzzleHttp\Client;

class EvolutionService {
    private $client;

    public function __construct() {
        $evolutionUrl = rtrim($_ENV['EVOLUTION_API_URL'], '/');
        $evolutionToken = $_ENV['EVOLUTION_API_TOKEN'];

        $this->client = new Client([
            'base_uri' => $evolutionUrl . '/',
            'verify' => false,
            'headers' => [
                'apikey' => $evolutionToken,
                'Content-Type' => 'application/json'
            ],
            'http_errors' => false,
            'timeout' => 15
        ]);
    }

    public function fetchInstanceStatus($instanceName) {
        $response = $this->client->get('instance/connectionState/' . $instanceName);
        return json_decode($response->getBody(), true);
    }
    
    public function sendMessageText($instanceName, $phone, $text) {
        $response = $this->client->post('message/sendText/' . $instanceName, [
            'json' => [
                'number' => $phone,
                'text' => $text
            ]
        ]);
        return json_decode($response->getBody(), true);
    }
    
    public function fetchChats($instanceName) {
        $response = $this->client->post('chat/findChats/' . $instanceName, [
            'json' => []
        ]);
        return json_decode($response->getBody(), true);
    }

    /**
     * Busca mensagens de um chat específico com paginação (lazy loading)
     * $page começa em 1 e traz $limit msgs por vez
     */
    public function fetchMessages($instanceName, $remoteJid, $limit = 20, $page = 1) {
        $skip = ($page - 1) * $limit;
        $response = $this->client->post('chat/findMessages/' . $instanceName, [
            'json' => [
                'where' => [
                    'key' => [
                        'remoteJid' => $remoteJid
                    ]
                ],
                'limit' => $limit,
                'skip' => $skip,
                'sort' => [
                    'messageTimestamp' => -1  // Mais recente primeiro
                ]
            ]
        ]);
        $data = json_decode($response->getBody(), true);
        // Retorna array de mensagens ou vazio
        if (isset($data['messages']['records'])) {
            return array_reverse($data['messages']['records']);
        }
        if (is_array($data)) return $data;
        return [];
    }
}
