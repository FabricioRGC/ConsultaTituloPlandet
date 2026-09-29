<?php
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../models/AuditModel.php';

class AuthController {

    private $service;
    private AuditModel $audit;

    public function __construct() {
        $this->service = new AuthService();
        $this->audit = new AuditModel();
    }

    private function getClientIp(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }

    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /ConsultaTituloPlandet/index.php");
            exit;
        }
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $credential = trim($_POST['name'] ?? '');
        $password = $_POST['password'] ?? '';
        $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
        $ip = $this->getClientIp();
        $sessionToken = session_id();

        if ($credential === '' || $password === '') {
            header("Location: /ConsultaTituloPlandet/index.php?error=1");
            exit;
        }

        $user = $this->service->login($credential, $password);

        if (!$user) {
            $email = filter_var($credential, FILTER_VALIDATE_EMAIL) ? $credential : null;
            $this->audit->logLogin(null, $email, 'failed', $ip, $userAgent, $sessionToken);
            header("Location: /ConsultaTituloPlandet/index.php?error=1");
            exit;
        }

        session_regenerate_id(true);
        $sessionToken = session_id();

        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_name'] = $user['name'];
        $_SESSION['usuario_email'] = $user['email'];
        $_SESSION['usuario_rol'] = $user['rol'];
        $_SESSION['audit_session_token'] = $sessionToken;

        $this->audit->logLogin((int)$user['id'], $user['email'], 'success', $ip, $userAgent, $sessionToken);
        $this->audit->logActivity((int)$user['id'], 'LOGIN', 'AUTH', null, null, 'Inicio de sesion', null, $sessionToken);

        header("Location: /ConsultaTituloPlandet/index.php?action=dasboard");
        exit;
    }

    public function handleLogout() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $userId = $_SESSION['usuario_id'] ?? null;
        $sessionToken = $_SESSION['audit_session_token'] ?? session_id();

        if (!empty($userId)) {
            $this->audit->logActivity((int)$userId, 'LOGOUT', 'AUTH', null, null, 'Cierre de sesion', null, $sessionToken);
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        header("Location: /ConsultaTituloPlandet/index.php");
        exit;
    }
}
