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
        $stmt = $this->db->prepare("SELECT * FROM users WHERE userid = :id");
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

    public function authenticateUser($username, $password) {
        $stmt = $this->db->prepare("SELECT password FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();
    
        if ($user && password_verify($password, $user['password'])) {
            // Start session and set user as authenticated
            session_start();
            $_SESSION['username'] = $username;
            echo "User authenticated successfully!";
            return true;
        } else {
            echo "Authentication failed: Incorrect username or password.";
            return false;
        }
    }
    
    public function updateUser($id, $username, $password, $email) {
        $sql = "UPDATE users SET username = :username, email = :email, password = :password WHERE userid = :id";
        $stmt = $this->db->prepare($sql);

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':id' => $id,
            ':password' => $hashedPassword
        ]);
        
        return $stmt->rowCount() > 0; // Returns true if any rows were affected
    }

    public function deleteUser($id) {
        $sql = "DELETE FROM users WHERE userid = :id";
        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->rowCount() > 0; // Returns true if any rows were affected
    }
}

