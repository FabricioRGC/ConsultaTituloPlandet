<?php
require_once __DIR__ . '/../config/database.php';

class AuditModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function logLogin(
        ?int $userId,
        ?string $email,
        string $status,
        ?string $ip,
        ?string $userAgent,
        ?string $sessionToken = null
    ): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO login_history (user_id, email, ip_address, user_agent, status, session_token)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$userId, $email, $ip, $userAgent, $status, $sessionToken]);
    }

    public function logActivity(
        ?int $userId,
        string $action,
        string $module,
        ?string $entityType = null,
        ?string $entityId = null,
        ?string $description = null,
        ?array $metadata = null,
        ?string $sessionToken = null
    ): void {
        $stmt = $this->db->prepare(
            "INSERT INTO activity_log (user_id, action, module, entity_type, entity_id, description, metadata, session_token)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $jsonMetadata = $metadata ? json_encode($metadata, JSON_UNESCAPED_UNICODE) : null;
        $stmt->execute([$userId, $action, $module, $entityType, $entityId, $description, $jsonMetadata, $sessionToken]);
    }

    public function getRecentLogins(int $limit = 20): array
    {
        $limit = max(1, min(100, $limit));
        $sql = "SELECT
                    lh.id,
                    lh.email,
                    lh.ip_address,
                    lh.user_agent,
                    lh.status,
                    lh.session_token,
                    lh.created_at,
                    u.name AS user_name,
                    u.rol AS user_role
                FROM login_history lh
                LEFT JOIN app_users u ON u.id = lh.user_id
                ORDER BY lh.created_at DESC
                LIMIT $limit";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    //Agregado nuevo Rom
    public function getTodayActivitiesForUserOrAdmin(int $userId, string $role): array
{
    // 🔥 Filtramos para traer solo los registros de subida/creación de archivos (ej. 'CREATE_QR')
    if ($role === 'admin') {
        $stmt = $this->db->prepare(
            "SELECT 
                DATE_FORMAT(al.created_at, '%h:%i %p') AS hora,
                al.action,
                al.module,
                al.description,
                al.metadata,
                al.created_at,
                u.name AS user_name
             FROM activity_log al
             LEFT JOIN app_users u ON u.id = al.user_id
             WHERE DATE(al.created_at) = CURDATE() 
               AND al.action = 'CREATE_QR'
             ORDER BY al.created_at DESC"
        );
        $stmt->execute();
    } else {
        $stmt = $this->db->prepare(
            "SELECT 
                DATE_FORMAT(al.created_at, '%h:%i %p') AS hora,
                al.action,
                al.module,
                al.description,
                al.metadata,
                al.created_at,
                u.name AS user_name
             FROM activity_log al
             LEFT JOIN app_users u ON u.id = al.user_id
             WHERE al.user_id = ? 
               AND DATE(al.created_at) = CURDATE() 
               AND al.action = 'CREATE_QR'
             ORDER BY al.created_at DESC"
        );
        $stmt->execute([$userId]);
    }
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function getRecentActivities(int $limit = 30): array
    {
        $limit = max(1, min(200, $limit));
        $sql = "SELECT
                    al.id,
                    al.action,
                    al.module,
                    al.entity_type,
                    al.entity_id,
                    al.description,
                    al.metadata,
                    al.session_token,
                    al.created_at,
                    u.name AS user_name,
                    u.email AS user_email,
                    u.rol AS user_role
                FROM activity_log al
                LEFT JOIN app_users u ON u.id = al.user_id
                ORDER BY al.created_at DESC
                LIMIT $limit";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSessionActivities(string $sessionToken, int $limit = 300): array
    {
        $limit = max(1, min(500, $limit));
        $stmt = $this->db->prepare(
            "SELECT
                al.created_at,
                u.name AS user_name,
                u.email AS user_email,
                al.action,
                al.module,
                al.entity_type,
                al.entity_id,
                al.description,
                al.metadata
             FROM activity_log al
             LEFT JOIN app_users u ON u.id = al.user_id
             WHERE al.session_token = ?
             ORDER BY al.created_at ASC
             LIMIT $limit"
        );
        $stmt->execute([$sessionToken]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
