<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
$evo = new App\Services\EvolutionService();
$chats = $evo->fetchChats('pauloprofissional');
echo json_encode($chats[0], JSON_PRETTY_PRINT);
