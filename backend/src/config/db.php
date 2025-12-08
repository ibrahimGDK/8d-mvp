<?php

/* namespace App\Config;

use PDO;
use PDOException;
use Exception; */

class Database {
    
    private static ?PDO $conn = null;

    /**
     * * @return PDO
     * @throws Exception
     */
    public static function getConnection(): PDO {
        if (self::$conn === null) {
            $host = getenv('DB_HOST') ?: '127.0.0.1';
            $db   = getenv('DB_NAME') ?: '8d_db';
            $user = getenv('DB_USER') ?: 'root';
            $pass = getenv('DB_PASS') ?: '';
            $port = getenv('DB_PORT') ?: '3308';
            $pass = getenv('DB_PASS') ?: '123456';

            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

            try {
                // Enterprise Seviye Ayarlar
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Hataları gizleme, fırlat
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Her zaman dizi olarak çek (Obj değil)
                    PDO::ATTR_EMULATE_PREPARES   => false,                  // SQL Injection korumasını DB seviyesine çek
                    PDO::ATTR_PERSISTENT         => true                    // Bağlantı havuzu (Opsiyonel ama performansı artırır)
                ];

                self::$conn = new PDO($dsn, $user, $pass, $options);
                
            } catch (PDOException $e) {
                throw new Exception("Database Connection Error: " . $e->getMessage());
            }
        }

        return self::$conn;
    }
}