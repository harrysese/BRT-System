<?php
namespace App\Controllers;

use App\Database;
use PDOException;

class UserController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getAllUsers() {
        $stmt = $this->db->query("SELECT * FROM users");
        $users = $stmt->fetchAll();

        echo json_encode($users);
    }

    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();

        echo json_encode($user);
    }

    public function register($username, $email, $password) {
        // Validate input
        if (empty($username) || empty($email) || empty($password)) {
            return json_encode(['error' => 'All fields are required.']);
        }

        // Check if user already exists
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email OR username = :username");
        $stmt->execute(['email' => $email, 'username' => $username]);
        if ($stmt->fetch()) {
            return json_encode(['error' => 'User with this email or username already exists.']);
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert new user
        try {
            $stmt = $this->db->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
            ]);
            return json_encode(['success' => 'User registered successfully!']);
        } catch (PDOException $e) {
            return json_encode(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }
}
