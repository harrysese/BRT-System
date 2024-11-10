<?php
 namespace App\controllers;

use App\Database;
use PDOException;
use SimpleXMLElement;

class BusController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function createBus($busNumber, $capacity, $routeId) {
        header("Content-Type: application/xml"); // Set header to XML
        if (empty($busNumber) || empty($capacity) || empty($routeId)) {
            return $this->generateXmlResponse(['error' => 'All fields are required.']);
        }

        try {
            $sql = "INSERT INTO BUS (BusNumber, Capacity, RouteID) VALUES (:busNumber, :capacity, :routeId)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':busNumber' => $busNumber,
                ':capacity' => $capacity,
                ':routeId' => $routeId,
            ]);

            return $this->generateXmlResponse(['success' => 'Bus added successfully!', 'id' => $this->db->lastInsertId()]);
        } catch (PDOException $e) {
            return $this->generateXmlResponse(['error' => 'Failed to add bus: ' . $e->getMessage()]);
        }
    }

    public function getBusById($busId) {
        header("Content-Type: application/xml"); // Set header to XML
        try {
            $stmt = $this->db->prepare("SELECT * FROM BUS WHERE BusID = :busId");
            $stmt->execute([':busId' => $busId]);
            $bus = $stmt->fetch();

            if (!$bus) {
                return $this->generateXmlResponse(['error' => 'Bus not found']);
            }
            return $this->generateXmlResponse($bus);
        } catch (PDOException $e) {
            return $this->generateXmlResponse(['error' => 'Failed to fetch bus: ' . $e->getMessage()]);
        }
    }
    public function updateBus($busId, $busNumber, $capacity, $routeId) {
        // Check if required fields are provided
        if (empty($busNumber) || empty($capacity) || empty($routeId)) {
            return $this->generateXmlResponse(['error' => 'All fields are required.']);
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
    
            // Check if any rows were updated
            if ($stmt->rowCount() === 0) {
                return $this->generateXmlResponse(['error' => 'Bus not found or no changes made']);
            }
    
            return $this->generateXmlResponse(['success' => 'Bus updated successfully']);
        } catch (PDOException $e) {
            return $this->generateXmlResponse(['error' => 'Failed to update bus: ' . $e->getMessage()]);
        }
    }
    

// Helper function to convert an array to XML
private function arrayToXml(array $data, &$xmlData) {
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $subnode = $xmlData->addChild($key);
            $this->arrayToXml($value, $subnode);
        } else {
            $xmlData->addChild($key, htmlspecialchars($value));
        }
    }
}

// Method to generate XML response
private function generateXmlResponse(array $data) {
    $xmlData = new \SimpleXMLElement('<?xml version="1.0"?><response></response>');
    $this->arrayToXml($data, $xmlData);
    header('Content-Type: application/xml');
    return $xmlData->asXML();
}
}