<?php

class pdo_connection {
    private static $pdo = null;

    public static function getPdo() {
        if (is_null(self::$pdo)) {
            self::$pdo = new PDO('mysql:host=localhost;dbname=timezone', 'timezone', 'timezone');
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$pdo;
    }
}