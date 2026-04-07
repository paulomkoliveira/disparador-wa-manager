<?php

namespace App\Core;

use GuzzleHttp\Client;

class SupabaseClient {
    private $client;
    private $apiKey;

    public function __construct() {
        $supabaseUrl = $_ENV['SUPABASE_URL'];
        $this->apiKey = $_ENV['SUPABASE_KEY'];

        $this->client = new Client([
            'base_uri' => $supabaseUrl . '/rest/v1/',
            'verify' => false, // <-- Desabilita SSL verify no ambiente local Windows
            'headers' => [
                'apikey' => $this->apiKey,
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Prefer' => 'return=representation'
            ]
        ]);
    }

    public function select($table, $query = '') {
        $response = $this->client->get($table . ($query ? '?' . $query : ''));
        return json_decode($response->getBody(), true);
    }

    public function insert($table, $data) {
        $response = $this->client->post($table, [
            'json' => $data
        ]);
        return json_decode($response->getBody(), true);
    }

    public function update($table, $query, $data) {
        $response = $this->client->patch($table . '?' . $query, [
            'json' => $data
        ]);
        return json_decode($response->getBody(), true);
    }

    public function delete($table, $query) {
        $response = $this->client->delete($table . '?' . $query);
        return json_decode($response->getBody(), true);
    }
}
