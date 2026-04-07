<?php
require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$url = rtrim($_ENV['EVOLUTION_API_URL'], '/') . '/chat/findMessages/pauloprofissional';
$token = $_ENV['EVOLUTION_API_TOKEN'];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'apikey: ' . $token,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'where' => [
        'key' => [
            'remoteJid' => '557798109649@s.whatsapp.net'
        ]
    ],
    'limit' => 10
]));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
file_put_contents('debug_messages_structure.json', $response);
echo "Check debug_messages_structure.json";
curl_close($ch);
