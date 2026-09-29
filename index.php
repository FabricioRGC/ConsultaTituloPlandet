<?php
// public/index.php
// Front controller - punto de entrada optimizado

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/AuditController.php';
require_once __DIR__ . '/services/AuthMiddleware.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/AuditModel.php';
require_once __DIR__ . '/views/templates/main-template.php';

// Iniciar sesión de forma segura si no está activa
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$action = $_GET['action'] ?? '';

$authController = new AuthController();
$auditController = new AuditController();

switch ($action) {
    case 'login':
        $authController->handleLogin();
        break;

    case 'logout':
        $authController->handleLogout();
        break;

    case 'track_event':
        AuthMiddleware::requireLogin();
        $auditController->trackEvent();
        break;

    // Corregido de 'dasboard' a 'dashboard' (mantenemos compatibilidad por si acaso)
    case 'dashboard':
    case 'dasboard':
        AuthMiddleware::requireLogin();
        
        $audit = new AuditModel();
        $currentTab = $_GET['tab'] ?? 'default';
        
        // Registrar auditoría del acceso
        $audit->logActivity(
            (int)($_SESSION['usuario_id'] ?? 0),
            'OPEN_DASHBOARD',
            'SYSTEM',
            null,
            null,
            'Ingreso al dashboard',
            ['tab' => $currentTab],
            $_SESSION['audit_session_token'] ?? session_id()
        );
        
        $rol = $_SESSION['usuario_rol'] ?? null;


        if ($rol === 'admin') {
            render_page(
                __DIR__ . '/views/pages/dash_admin.php',
                'Dashboard Admin',
                ['/ConsultaTituloPlandet-1/styles/dasboard.css'],
                true
            );
        } elseif ($rol === 'user') {
            render_page(
                __DIR__ . '/views/pages/dash_locador.php',
                'Dashboard User',
                ['/ConsultaTituloPlandet-1/styles/dasboard.css'],
                true
            );
        } else {
            session_destroy();
            header("Location: /ConsultaTituloPlandet-1/index.php");
            exit;
        }
        break;

    default:
        // Si ya hay una sesión activa y entra a la raíz, redirigir directamente al dashboard por comodidad (UX)
        if (isset($_SESSION['usuario_id'])) {
            header("Location: /ConsultaTituloPlandet-1/index.php?action=dashboard");
            exit;
        }
        // Mostrar login SIN template
        require_once __DIR__ . '/views/login.php';
        break;
}