<?php

class Database {
    private static ?PDO $instance = null;

    private static string $host = 'localhost';
    private static string $dbname = 'db_inventaris_sarana';
    private static string $username = 'root';
    private static string $password = '';
    private static string $charset = 'utf8mb4';

    private function __construct() {}

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            try {
                $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=" . self::$charset;
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => true,
                ];
                self::$instance = new PDO($dsn, self::$username, self::$password, $options);
            } catch (PDOException $e) {
                die("Koneksi ke database gagal: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
