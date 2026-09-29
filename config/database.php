<?php
// app/config/database.php
// Conexión PDO reutilizable

class Database {
    private static $connection = null;

    public static function connection() {
        if (self::$connection === null) {
            try {
                // Ajusta host, dbname, user y password según tu entorno
                $dsn = "mysql:host=localhost;dbname=titulo;charset=utf8mb4";
                $user = "root";
                $pass = ""; // <- pon tu contraseña si aplica

                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];

                self::$connection = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                // En producción no mostrar el error: loguear y mostrar genérico
                die("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }

        return self::$connection;
    }
}
