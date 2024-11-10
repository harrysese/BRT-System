<?php
namespace App\Controllers;

use App\Database;
use PDOException;
use SimpleXMLElement;

class UserController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    private function generateXmlResponse($data) {
        $xml = new SimpleXMLElement('<?xml version="1.0"?><response></response>');
        $this->arrayToXml($data, $xml);
        return $xml->asXML();
    }

    private function arrayToXml($data, &$xml) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    $key = 'item' . $key;
                }
                $subnode = $xml->addChild($key);
                $this->arrayToXml($value, $subnode);
            } else {
                $xml->addChild($key, htmlspecialchars((string)$value));
            }
        }
    }

    public function getAllUsers() {
        try {
            $stmt = $this->db->query("SELECT * FROM users");
            $users = $stmt->fetchAll();
            return $this->generateXmlResponse(['users' => $users]);
        } catch (PDOException $e) {
            return $this->generateXmlResponse(['error' => 'Failed to fetch users: ' . $e->getMessage()]);
        }
    }

    public function getUserById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE userid = :id");
            $stmt->execute(['id' => $id]);
            $user = $stmt->fetch();

            if (!$user) {
                return $this->generateXmlResponse(['error' => 'User not found']);
            }

            return $this->generateXmlResponse(['user' => $user]);
        } catch (PDOException $e) {
            return $this->generateXmlResponse(['error' => 'Failed to fetch user: ' . $e->getMessage()]);
        }
    }

    public function register($username, $email, $password) {
        // Validate input
        if (empty($username) || empty($email) || empty($password)) {
            return $this->generateXmlResponse(['error' => 'All fields are required.']);
        }

        try {
            // Check if user already exists
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email OR username = :username");
            $stmt->execute(['email' => $email, 'username' => $username]);
            if ($stmt->fetch()) {
                return $this->generateXmlResponse(['error' => 'User with this email or username already exists.']);
            }

            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insert new user
            $stmt = $this->db->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
            ]);
            
            return $this->generateXmlResponse(['success' => 'User registered successfully!']);
        } catch (PDOException $e) {
            return $this->generateXmlResponse(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }

    public function authenticateUser($username, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch();
        
            if ($user && password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['username'] = $username;
                return $this->generateXmlResponse(['success' => 'User authenticated successfully!']);
            } else {
                return $this->generateXmlResponse(['error' => 'Authentication failed: Incorrect username or password.']);
            }
        } catch (PDOException $e) {
            return $this->generateXmlResponse(['error' => 'Authentication failed: ' . $e->getMessage()]);
        }
    }
    
    public function updateUser($id, $username, $password, $email) {
        try {
            if (empty($username) || empty($password) || empty($email)) {
                return $this->generateXmlResponse(['error' => 'All fields are required.']);
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            $sql = "UPDATE users SET username = :username, email = :email, password = :password WHERE userid = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':id' => $id,
                ':password' => $hashedPassword
            ]);
            
            if ($stmt->rowCount() > 0) {
                return $this->generateXmlResponse(['success' => 'User updated successfully']);
            } else {
                return $this->generateXmlResponse(['error' => 'User not found or no changes made']);
            }
        } catch (PDOException $e) {
            return $this->generateXmlResponse(['error' => 'Update failed: ' . $e->getMessage()]);
        }
    }

    public function deleteUser($id) {
        try {
            $sql = "DELETE FROM users WHERE userid = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            if ($stmt->rowCount() > 0) {
                return $this->generateXmlResponse(['success' => 'User deleted successfully']);
            } else {
                return $this->generateXmlResponse(['error' => 'User not found']);
            }
        } catch (PDOException $e) {
            return $this->generateXmlResponse(['error' => 'Delete failed: ' . $e->getMessage()]);
        }
    }
}