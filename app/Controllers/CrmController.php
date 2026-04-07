<?php

namespace App\Controllers;

use App\Core\Controller;

class CrmController extends Controller {

    private $dbFile;

    public function __construct() {
        // Usamos um arquivo JSON como mini-banco para o CRM (sem depender do Supabase por ora)
        $this->dbFile = dirname(__DIR__, 2) . '/storage/crm_leads.json';
        if (!file_exists(dirname($this->dbFile))) {
            mkdir(dirname($this->dbFile), 0777, true);
        }
        if (!file_exists($this->dbFile)) {
            file_put_contents($this->dbFile, json_encode([]));
        }
    }

    private function lerLeads(): array {
        $content = file_get_contents($this->dbFile);
        return json_decode($content, true) ?? [];
    }

    private function salvar(array $leads): void {
        file_put_contents($this->dbFile, json_encode($leads, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login'); exit;
        }
        $leads = $this->lerLeads();
        $this->view('crm', ['leads' => $leads]);
    }

    public function salvarLead() {
        header('Content-Type: application/json');
        $body = json_decode(file_get_contents('php://input'), true);

        $leads = $this->lerLeads();

        if (!empty($body['id'])) {
            // Editar existente
            foreach ($leads as &$l) {
                if ($l['id'] === $body['id']) {
                    $l['nome']      = $body['nome']      ?? $l['nome'];
                    $l['telefone']  = $body['telefone']  ?? $l['telefone'];
                    $l['valor']     = $body['valor']     ?? $l['valor'];
                    $l['descricao'] = $body['descricao'] ?? $l['descricao'];
                    $l['coluna']    = $body['coluna']    ?? $l['coluna'];
                    $l['atualizado_em'] = date('Y-m-d H:i:s');
                    break;
                }
            }
        } else {
            // Novo lead
            $leads[] = [
                'id'          => uniqid('lead_', true),
                'nome'        => $body['nome']      ?? 'Sem Nome',
                'telefone'    => $body['telefone']  ?? '',
                'valor'       => $body['valor']     ?? 0,
                'descricao'   => $body['descricao'] ?? '',
                'coluna'      => $body['coluna']    ?? 'novo',
                'criado_em'   => date('Y-m-d H:i:s'),
                'atualizado_em' => date('Y-m-d H:i:s'),
            ];
        }

        $this->salvar($leads);
        echo json_encode(['ok' => true]);
    }

    public function moverLead() {
        header('Content-Type: application/json');
        $body = json_decode(file_get_contents('php://input'), true);
        $leads = $this->lerLeads();
        foreach ($leads as &$l) {
            if ($l['id'] === $body['id']) {
                $l['coluna'] = $body['coluna'];
                $l['atualizado_em'] = date('Y-m-d H:i:s');
                break;
            }
        }
        $this->salvar($leads);
        echo json_encode(['ok' => true]);
    }

    public function excluirLead() {
        header('Content-Type: application/json');
        $body = json_decode(file_get_contents('php://input'), true);
        $leads = $this->lerLeads();
        $leads = array_values(array_filter($leads, fn($l) => $l['id'] !== $body['id']));
        $this->salvar($leads);
        echo json_encode(['ok' => true]);
    }
}
