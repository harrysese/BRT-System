<?php
namespace App\controllers;

use App\Database;
use PDOException;

class BusController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function createBus($busNumber, $capacity, $routeId) {
        // Validate input
        if (empty($busNumber) || empty($capacity) || empty($routeId)) {
            return json_encode(['error' => 'All fields are required.']);
        }

        try {
            $sql = "INSERT INTO BUS (BusNumber, Capacity, RouteID) VALUES (:busNumber, :capacity, :routeId)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':busNumber' => $busNumber,
                ':capacity' => $capacity,
                ':routeId' => $routeId,
            ]);

            return json_encode(['success' => 'Bus added successfully!', 'id' => $this->db->lastInsertId()]);
        } catch (PDOException $e) {
            return json_encode(['error' => 'Failed to add bus: ' . $e->getMessage()]);
        }
    }

    public function getBusById($busId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM BUS WHERE BusID = :busId");
            $stmt->execute([':busId' => $busId]);
            $bus = $stmt->fetch();

            if (!$bus) {
                return json_encode(['error' => 'Bus not found']);
            }
            return json_encode($bus);
        } catch (PDOException $e) {
            return json_encode(['error' => 'Failed to fetch bus: ' . $e->getMessage()]);
        }
    }

    public function getBusesByRouteId($routeId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM BUS WHERE RouteID = :routeId");
            $stmt->execute([':routeId' => $routeId]);
            $buses = $stmt->fetchAll();

            return json_encode($buses);
        } catch (PDOException $e) {
            return json_encode(['error' => 'Failed to fetch buses: ' . $e->getMessage()]);
        }
    }

    public function updateBus($busId, $busNumber, $capacity, $routeId) {
        if (empty($busNumber) || empty($capacity) || empty($routeId)) {
            return json_encode(['error' => 'All fields are required.']);
        }

        try {
            $sql = "UPDATE BUS SET BusNumber = :busNumber, Capacity = :capacity, RouteID = :routeId WHERE BusID = :busId";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':busNumber' => $busNumber,
                ':capacity' => $capacity,
                ':routeId' => $routeId,
                ':busId' => $busId
            ]);

            if ($stmt->rowCount() === 0) {
                return json_encode(['error' => 'Bus not found']);
            }

            return json_encode(['success' => 'Bus updated successfully']);
        } catch (PDOException $e) {
            return json_encode(['error' => 'Failed to update bus: ' . $e->getMessage()]);
        }
    }

    public function deleteBus($busId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM BUS WHERE BusID = :busId");
            $stmt->execute([':busId' => $busId]);

            if ($stmt->rowCount() === 0) {
                return json_encode(['error' => 'Bus not found']);
            }

            return json_encode(['success' => 'Bus deleted successfully']);
        } catch (PDOException $e) {
            return json_encode(['error' => 'Failed to delete bus: ' . $e->getMessage()]);
        }
    }
}
