<?php
require_once __DIR__ . '/../models/User.php';

class AuthService {

    public function login(string $credential, string $password) {
        $user = Usuario::findByCredential($credential);
        if (!$user) return false;

         if ($user['password'] !== $password) {
            return false;
        }

        Usuario::updateLastLogin((int)$user['id']);

        return [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'rol' => $user['rol'],
        ];
    }
}
