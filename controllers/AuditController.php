<?php
require_once __DIR__ . '/../models/AuditModel.php';

class AuditController
{
    public function trackEvent(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (empty($_SESSION['usuario_id'])) {
            http_response_code(401);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['ok' => false, 'error' => 'unauthorized']);
            exit;
        }

        $raw = file_get_contents('php://input');
        $payload = json_decode($raw ?: '{}', true);

        $action = trim((string)($payload['action'] ?? 'UI_EVENT'));
        $module = trim((string)($payload['module'] ?? 'UI'));
        $description = trim((string)($payload['description'] ?? 'Evento de interfaz'));
        $entityType = isset($payload['entity_type']) ? (string)$payload['entity_type'] : null;
        $entityId = isset($payload['entity_id']) ? (string)$payload['entity_id'] : null;
        $metadata = isset($payload['metadata']) && is_array($payload['metadata']) ? $payload['metadata'] : null;

        $audit = new AuditModel();
        $audit->logActivity(
            (int)$_SESSION['usuario_id'],
            $action !== '' ? $action : 'UI_EVENT',
            $module !== '' ? $module : 'UI',
            $entityType,
            $entityId,
            $description !== '' ? $description : 'Evento de interfaz',
            $metadata,
            $_SESSION['audit_session_token'] ?? session_id()
        );

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['ok' => true]);
        exit;
    }
}
