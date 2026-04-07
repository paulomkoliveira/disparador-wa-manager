<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

use App\Core\SupabaseClient;

$supabase = new SupabaseClient();

$usuario_id = 'd4cabb6f-b6bd-4ae7-817a-514d3f3bd8a9'; // UUID mockado local

$data = [
    'usuario_id' => $usuario_id,
    'nome' => 'pauloprofissional',
    'url' => rtrim($_ENV['EVOLUTION_API_URL'], '/'),
    'token' => $_ENV['EVOLUTION_API_TOKEN'],
    'status' => 'online'
];

$result = $supabase->insert('instancias_api', $data);

print_r($result);
