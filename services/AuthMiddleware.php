<?php

class AuthMiddleware {

    private static function ensureSession() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function requireLogin() {
        self::ensureSession();
        if (empty($_SESSION['usuario_id'])) {
            header("Location: /ConsultaTituloPlandet-1/index.php");
            exit;
        }
    }

    public static function requireRole($roles) {
        self::ensureSession();

        if (empty($_SESSION['usuario_id'])) {
            header("Location: /ConsultaTituloPlandet-1/index.php");
            exit;
        }

        $userRole = $_SESSION['usuario_rol'] ?? null;

        if (is_string($roles)) {
            $roles = [$roles];
        }

        if (!in_array($userRole, $roles)) {
            header("Location: /ConsultaTituloPlandet-1/index.php?action=dasboard");
            exit;
        }
    }
}
