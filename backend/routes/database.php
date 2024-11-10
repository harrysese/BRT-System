<?php
// database.php
require 'vendor/autoload.php'; // Load Composer dependencies

use Dotenv\Dotenv;

class Database {
    private $conn;

    public function __construct() {
        // Load .env file
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        // Use environment variables to set database connection details
        $host = $_ENV['DB_HOST'];
        $db_name = $_ENV['DB_NAME'];
        $username = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASSWORD'];
        $port = $_ENV['DB_PORT'];

        // Establish a PDO connection using these variables
        try {
            $this->conn = new PDO("pgsql:host=$host;port=$port;dbname=$db_name", $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Database connection error: " . $exception->getMessage();
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}
