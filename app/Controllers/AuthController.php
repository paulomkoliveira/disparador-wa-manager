<?php

namespace App\Controllers;

use App\Core\Controller;

class AuthController extends Controller {

    private string $supabaseUrl;
    private string $supabaseKey;

    public function __construct() {
        $this->supabaseUrl = rtrim($_ENV['SUPABASE_URL'], '/');
        $this->supabaseKey = $_ENV['SUPABASE_KEY'];
    }

    // ─── Tela de login ──────────────────────────────
    public function index() {
        if (isset($_SESSION['user_id'])) {
            header("Location: /");
            exit();
        }
        // render() sem layout (login é página standalone)
        $erro = $_SESSION['auth_erro'] ?? null;
        unset($_SESSION['auth_erro']);
        $this->render('login', ['erro' => $erro]);
    }

    // ─── POST /login (email + senha) ─────────────────
    public function login() {
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if (!$email || !$senha) {
            $_SESSION['auth_erro'] = 'Preencha e-mail e senha.';
            header("Location: /login");
            exit();
        }

        $result = $this->supabaseSignIn($email, $senha);

        if ($result['success']) {
            $user = $result['data'];
            $_SESSION['user_id']    = $user['user']['id'];
            $_SESSION['user_email'] = $user['user']['email'];
            $_SESSION['user_name']  = $user['user']['user_metadata']['full_name']
                                      ?? $user['user']['email'];
            $_SESSION['access_token'] = $user['access_token'];
            header("Location: /");
            exit();
        }

        $_SESSION['auth_erro'] = $this->traduzirErro($result['error']);
        header("Location: /login");
        exit();
    }

    // ─── GET /auth/google (redireciona pro Supabase OAuth) ──
    public function googleRedirect() {
        $redirectUrl = $this->supabaseUrl . '/auth/v1/authorize?provider=google'
            . '&redirect_to=' . urlencode('http://localhost:8000/auth/google/callback');
        header("Location: $redirectUrl");
        exit();
    }

    // ─── GET /auth/google/callback ───────────────────
    public function googleCallback() {
        // O Supabase redireciona com fragment (#access_token=...)
        // Capturamos via JS e postamos para completar a sessão no servidor
        echo <<<HTML
        <!DOCTYPE html>
        <html>
        <head><title>Autenticando...</title></head>
        <body style="font-family:system-ui;display:flex;align-items:center;justify-content:center;height:100vh;background:#0a0f1a;color:#fff;">
        <div style="text-align:center">
            <p style="font-size:1.1rem;margin-bottom:8px;">🔐 Finalizando autenticação...</p>
            <p style="color:#64748b;font-size:.85rem;">Aguarde um momento.</p>
        </div>
        <script>
            // O Supabase envia o token via URL fragment (#access_token=...)
            const hash = window.location.hash.substring(1);
            const params = new URLSearchParams(hash);
            const accessToken = params.get('access_token');

            if (accessToken) {
                fetch('/auth/google/session', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ access_token: accessToken })
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) window.location.href = '/';
                    else window.location.href = '/login?erro=oauth';
                });
            } else {
                window.location.href = '/login?erro=oauth';
            }
        </script>
        </body>
        </html>
        HTML;
    }

    // ─── POST /auth/google/session (seta sessão via token) ──
    public function googleSession() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $token = $data['access_token'] ?? '';

        if (!$token) {
            echo json_encode(['success' => false]);
            return;
        }

        // Busca dados do usuário com o token
        $user = $this->supabaseGetUser($token);

        if ($user) {
            $_SESSION['user_id']      = $user['id'];
            $_SESSION['user_email']   = $user['email'];
            $_SESSION['user_name']    = $user['user_metadata']['full_name']
                                        ?? $user['user_metadata']['name']
                                        ?? $user['email'];
            $_SESSION['user_avatar']  = $user['user_metadata']['avatar_url'] ?? null;
            $_SESSION['access_token'] = $token;
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    // ─── Logout ──────────────────────────────────────
    public function logout() {
        // Revoga token no Supabase se existir
        if (!empty($_SESSION['access_token'])) {
            $this->supabaseSignOut($_SESSION['access_token']);
        }
        session_unset();
        session_destroy();
        header("Location: /login");
        exit();
    }

    // ═══════════════════════════════════════════════════
    // Helpers Supabase
    // ═══════════════════════════════════════════════════

    private function supabaseSignIn(string $email, string $senha): array {
        $url = $this->supabaseUrl . '/auth/v1/token?grant_type=password';
        $response = $this->httpPost($url, [
            'email'    => $email,
            'password' => $senha,
        ], [
            'apikey: ' . $this->supabaseKey,
            'Content-Type: application/json',
        ]);

        if ($response['status'] === 200) {
            return ['success' => true, 'data' => json_decode($response['body'], true)];
        }

        $body = json_decode($response['body'], true);
        return ['success' => false, 'error' => $body['error_description'] ?? $body['msg'] ?? 'Erro desconhecido'];
    }

    private function supabaseGetUser(string $token): ?array {
        $url = $this->supabaseUrl . '/auth/v1/user';
        $response = $this->httpGet($url, [
            'apikey: ' . $this->supabaseKey,
            'Authorization: Bearer ' . $token,
        ]);
        if ($response['status'] === 200) {
            return json_decode($response['body'], true);
        }
        return null;
    }

    private function supabaseSignOut(string $token): void {
        $url = $this->supabaseUrl . '/auth/v1/logout';
        $this->httpPost($url, [], [
            'apikey: ' . $this->supabaseKey,
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ]);
    }

    private function httpPost(string $url, array $body, array $headers): array {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($body),
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => false, // Windows: ignora verificação SSL
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $out    = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err    = curl_error($ch);
        curl_close($ch);
        if ($out === false) {
            return ['status' => 0, 'body' => json_encode(['error_description' => 'Curl error: ' . $err])];
        }
        return ['status' => $status, 'body' => $out];
    }

    private function httpGet(string $url, array $headers): array {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $out    = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ['status' => $status, 'body' => $out];
    }

    private function traduzirErro(string $erro): string {
        $mapa = [
            'Invalid login credentials' => 'E-mail ou senha incorretos.',
            'Email not confirmed'        => 'Confirme seu e-mail antes de entrar.',
            'User not found'             => 'Usuário não encontrado.',
            'Too many requests'          => 'Muitas tentativas. Aguarde alguns minutos.',
        ];
        return $mapa[$erro] ?? 'Erro ao autenticar. Verifique suas credenciais.';
    }
}
