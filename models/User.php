<?php
require_once __DIR__ . '/../config/database.php';

class Usuario {

     public static function findByCredential(string $credential) {
        $db = Database::connection();
        $sql = "SELECT id, name, email, password, rol
                FROM app_users
                WHERE name = ? OR email = ?
                LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$credential, $credential]);
        $user = $stmt->fetch();
        return $user ?: false;
    }

    public static function updateLastLogin(int $id): void {
        $db = Database::connection();
        $stmt = $db->prepare("UPDATE app_users SET last_login_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }
}
