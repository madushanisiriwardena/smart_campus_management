<?php

class Database {
    public static $connection;

    public static function setUpConnection() {
        if (!isset(Database::$connection)) {
            Database::$connection = new mysqli("localhost", "root", "", "scm", "3306");

            if (Database::$connection->connect_error) {
                die("Connection failed: " . Database::$connection->connect_error);
            }

            // Set the charset to avoid character set issues
            Database::$connection->set_charset("utf8mb4");
        }
    }

    public static function iud($q) {
        Database::setUpConnection();
        if (Database::$connection->query($q) === false) {
            throw new Exception("Database Error: " . Database::$connection->error);
        }
    }

    public static function search($q) {
        Database::setUpConnection();
        $resultset = Database::$connection->query($q);
        if ($resultset === false) {
            throw new Exception("Database Error: " . Database::$connection->error);
        }
        return $resultset;
    }

    // Method to start a transaction
    public static function begin_transaction() {
        Database::setUpConnection();
        Database::$connection->begin_transaction();
    }

    // Method to commit a transaction
    public static function commit() {
        Database::$connection->commit();
    }

    // Method to rollback a transaction
    public static function rollback() {
        Database::$connection->rollback();
    }
}

// Initialize the connection (optional, can be done lazily)
Database::setUpConnection();
?>
