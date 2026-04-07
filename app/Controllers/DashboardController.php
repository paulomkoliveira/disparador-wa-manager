<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\SupabaseClient;

class DashboardController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit();
        }
    }

    public function index() {
        $supabase = new SupabaseClient();
        
        // Exemplo: buscar instâncias do usuário
        $userId = $_SESSION['user_id'];
        $instancias = $supabase->select('instancias_api', "usuario_id=eq.$userId");
        if (!is_array($instancias)) {
            $instancias = [];
        }

        // Mock para mostrar a instância real do .env (pauloprofissional) pro usuário visualizar
        array_push($instancias, [
            'id' => 'mock-id-01',
            'nome' => 'pauloprofissional',
            'status' => 'online',
            'url' => $_ENV['EVOLUTION_API_URL']
        ]);

        $this->view('dashboard', [
            'instancias' => $instancias
        ]);
    }

    public function disparador() {
        $supabase = new SupabaseClient();
        $userId = $_SESSION['user_id'];
        $instancias = $supabase->select('instancias_api', "usuario_id=eq.$userId&status=eq.online");
        if (!is_array($instancias)) {
            $instancias = [];
        }

        // Mock para mostrar a instância real do .env (pauloprofissional) pro usuário visualizar
        array_push($instancias, [
            'id' => 'mock-id-01',
            'nome' => 'pauloprofissional',
            'status' => 'online',
            'url' => $_ENV['EVOLUTION_API_URL']
        ]);

        $this->view('disparador', [
            'instancias' => $instancias
        ]);
    }
}
