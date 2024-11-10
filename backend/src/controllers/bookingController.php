<?php
namespace App\Controllers;

use App\Database;
use PDOException;
use SimpleXMLElement;

class BookingController {
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
                    $key = 'booking';
                }
                $subnode = $xml->addChild($key);
                $this->arrayToXml($value, $subnode);
            } else {
                $xml->addChild($key, htmlspecialchars((string)$value));
            }
        }
    }

    public function createBooking($userId, $scheduleId) {
        try {
            // Validate input
            if (empty($userId) || empty($scheduleId)) {
                return $this->generateXmlResponse(['error' => 'UserID and ScheduleID are required.']);
            }

            // Check if user already has a booking for this schedule
            $stmt = $this->db->prepare("SELECT * FROM booking WHERE UserID = :userId AND ScheduleID = :scheduleId AND status != 'cancelled'");
            $stmt->execute([
                'userId' => $userId,
                'scheduleId' => $scheduleId
            ]);

            if ($stmt->fetch()) {
                return $this->generateXmlResponse(['error' => 'User already has a booking for this schedule.']);
            }

            // Create new booking
            $sql = "INSERT INTO booking (datetime, status, UserID, ScheduleID) 
                   VALUES (NOW(), 'Confirmed', :userId, :scheduleId)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'userId' => $userId,
                'scheduleId' => $scheduleId
            ]);

            return $this->generateXmlResponse([
                'success' => 'Booking created successfully!',
                'bookingId' => $this->db->lastInsertId()
            ]);

        } catch (PDOException $e) {
            return $this->generateXmlResponse(['error' => 'Booking failed: ' . $e->getMessage()]);
        }
    }

    public function getBookingById($bookingId) {
        try {
            $stmt = $this->db->prepare("
                SELECT b.*, u.username, s.* 
                FROM booking b
                JOIN users u ON b.UserID = u.UserID
                JOIN schedule s ON b.ScheduleID = s.ScheduleID
                WHERE b.bookingid = :bookingId
            ");
            $stmt->execute(['bookingId' => $bookingId]);
            $booking = $stmt->fetch();

            if (!$booking) {
                return $this->generateXmlResponse(['error' => 'Booking not found']);
            }

            return $this->generateXmlResponse(['booking' => $booking]);
        } catch (PDOException $e) {
            return $this->generateXmlResponse(['error' => 'Failed to fetch booking: ' . $e->getMessage()]);
        }
    }

    public function getBookingsByUser($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT b.*, s.* 
                FROM booking b
                JOIN schedule s ON b.ScheduleID = s.ScheduleID
                WHERE b.UserID = :userId
                ORDER BY b.datetime DESC
            ");
            $stmt->execute(['userId' => $userId]);
            $bookings = $stmt->fetchAll();

            return $this->generateXmlResponse(['bookings' => $bookings]);
        } catch (PDOException $e) {
            return $this->generateXmlResponse(['error' => 'Failed to fetch bookings: ' . $e->getMessage()]);
        }
    }

    public function updateBookingStatus($bookingId, $status) {
        try {
            // Validate status
            $validStatuses = ['confirmed', 'cancelled', 'pending'];
            if (!in_array($status, $validStatuses)) {
                return $this->generateXmlResponse(['error' => 'Invalid status']);
            }

            $sql = "UPDATE booking SET status = :status, datetime = NOW() WHERE bookingid = :bookingId";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'status' => $status,
                'bookingId' => $bookingId
            ]);

            if ($stmt->rowCount() > 0) {
                return $this->generateXmlResponse(['success' => 'Booking status updated successfully']);
            } else {
                return $this->generateXmlResponse(['error' => 'Booking not found']);
            }
        } catch (PDOException $e) {
            return $this->generateXmlResponse(['error' => 'Update failed: ' . $e->getMessage()]);
        }
    }

    public function cancelBooking($bookingId) {
        try {
            // Check if booking exists and is not already cancelled
            $stmt = $this->db->prepare("SELECT status FROM booking WHERE bookingid = :bookingId");
            $stmt->execute(['bookingId' => $bookingId]);
            $booking = $stmt->fetch();

            if (!$booking) {
                return $this->generateXmlResponse(['error' => 'Booking not found']);
            }

            if ($booking['status'] === 'cancelled') {
                return $this->generateXmlResponse(['error' => 'Booking is already cancelled']);
            }

            // Cancel the booking
            $sql = "UPDATE booking SET status = 'cancelled', datetime = NOW() WHERE bookingid = :bookingId";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['bookingId' => $bookingId]);

            return $this->generateXmlResponse(['success' => 'Booking cancelled successfully']);
        } catch (PDOException $e) {
            return $this->generateXmlResponse(['error' => 'Cancellation failed: ' . $e->getMessage()]);
        }
    }
}
