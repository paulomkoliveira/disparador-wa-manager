<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\SupabaseClient;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailController extends Controller {

    private function getStoragePath() {
        $dir = dirname(__DIR__, 2) . '/storage';
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        return $dir . '/email_accounts.json';
    }

    private function getAccounts() {
        $path = $this->getStoragePath();
        if (!file_exists($path)) return [];
        return json_decode(file_get_contents($path), true) ?? [];
    }

    private function saveAccounts($accounts) {
        file_put_contents($this->getStoragePath(), json_encode($accounts, JSON_PRETTY_PRINT));
    }

    public function index() {
        $this->view('email_disparador', [
            'contas' => $this->getAccounts()
        ]);
    }

    public function salvarConta() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        
        $accounts = $this->getAccounts();
        $id = $data['id'] ?? uniqid();
        
        $accounts[$id] = [
            'id'       => $id,
            'nome'     => $data['nome'],
            'host'     => $data['host'],
            'porta'    => $data['porta'],
            'usuario'  => $data['usuario'],
            'senha'    => $data['senha'],
            'encryption' => $data['encryption'] ?? 'ssl'
        ];
        
        $this->saveAccounts($accounts);
        echo json_encode(['success' => true]);
    }

    public function excluirConta() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $accounts = $this->getAccounts();
        unset($accounts[$data['id']]);
        $this->saveAccounts($accounts);
        echo json_encode(['success' => true]);
    }

    public function enviarEmail() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        
        $accounts = $this->getAccounts();
        $conta = $accounts[$data['conta_id']] ?? null;
        
        if (!$conta) {
            echo json_encode(['success' => false, 'error' => 'Conta de e-mail não encontrada.']);
            return;
        }

        $mail = new PHPMailer(true);
        try {
            // Configurações do servidor
            $mail->isSMTP();
            $mail->Host       = trim($conta['host']);
            $mail->SMTPAuth   = true;
            $mail->Username   = trim($conta['usuario']);
            $mail->Password   = $conta['senha'];
            $mail->SMTPSecure = ($conta['encryption'] === 'tls')
                ? PHPMailer::ENCRYPTION_STARTTLS
                : PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = (int) $conta['porta'];
            $mail->CharSet    = 'UTF-8';
            $mail->Timeout    = 30;

            // Permite certificados SSL auto-assinados (VPS/Hostinger)
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ],
            ];

            // Remetente e destinatário
            $mail->setFrom($conta['usuario'], $conta['nome']);
            $mail->addAddress($data['destinatario']);

            // Conteúdo
            $mail->isHTML(true);
            $mail->Subject = $data['assunto'];
            $mail->Body    = nl2br(htmlspecialchars($data['mensagem']));
            $mail->AltBody = $data['mensagem'];

            $mail->send();
            echo json_encode(['success' => true]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error'   => $mail->ErrorInfo ?: $e->getMessage()
            ]);
        }
    }
}
