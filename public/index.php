<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ChatController;
use App\Controllers\CrmController;

session_start();

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$router = new Router();

// ─── Autenticação ─────────────────────────────────────────
$router->get('/login',  [AuthController::class, 'index']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// Google OAuth (removido da interface, mas callback mantido para compatibilidade)
// $router->get('/auth/google',          [AuthController::class, 'googleRedirect']);
// $router->get('/auth/google/callback', [AuthController::class, 'googleCallback']);
// $router->post('/auth/google/session', [AuthController::class, 'googleSession']);

// ─── Guard: rotas protegidas ──────────────────────────────
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$rotasPublicas = ['/login'];

if (!isset($_SESSION['user_id']) && !in_array($uri, $rotasPublicas)) {
    header("Location: /login");
    exit();
}

// ─── Dashboard / Home ────────────────────────────────────
$router->get('/', [DashboardController::class, 'index']);

// ─── Chat App (Multi-instâncias) ─────────────────────────
$router->get('/chat', [ChatController::class, 'index']);

// ─── Disparador WhatsApp ──────────────────────────────────
$router->get('/disparador', [DashboardController::class, 'disparador']);

// ─── CRM Kanban ───────────────────────────────────────────
$router->get('/crm',                    [CrmController::class, 'index']);
$router->post('/api/crm/salvar-lead',   [CrmController::class, 'salvarLead']);
$router->post('/api/crm/mover-lead',    [CrmController::class, 'moverLead']);
$router->post('/api/crm/excluir-lead',  [CrmController::class, 'excluirLead']);

// ─── Disparador de E-mail ─────────────────────────────────
$router->get('/email',                      [\App\Controllers\EmailController::class, 'index']);
$router->post('/api/email/salvar-conta',    [\App\Controllers\EmailController::class, 'salvarConta']);
$router->post('/api/email/excluir-conta',   [\App\Controllers\EmailController::class, 'excluirConta']);
$router->post('/api/email/enviar',          [\App\Controllers\EmailController::class, 'enviarEmail']);

// ─── API WhatsApp ─────────────────────────────────────────
$router->post('/api/enviar-mensagem', [\App\Controllers\ApiController::class, 'enviar']);
$router->post('/api/mensagens',       [\App\Controllers\ApiController::class, 'mensagens']);

$router->resolve();
