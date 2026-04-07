<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
$evo = new App\Services\EvolutionService();
// We won't actually send to a real user, we'll try a dummy number to see if it rejects standard payload shape
$response = $evo->sendMessageText('pauloprofissional', '5511900000000', 'Teste de payload');
echo json_encode($response);
