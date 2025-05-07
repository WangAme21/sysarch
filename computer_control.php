<?php
class computer_control {
    private $db;

    public function __construct() {
        $this->db = new PDO("mysql:host=localhost;dbname=my_database", "root", "");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getAllLabs() {
        $stmt = $this->db->query("SELECT * FROM labs");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLabComputers($labId) {
        $stmt = $this->db->prepare("SELECT * FROM computers WHERE lab_id = ?");
        $stmt->execute([$labId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function setComputerStatus($computerId, $status) {
        $stmt = $this->db->prepare("UPDATE computers SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $computerId]);
    }

    public function startSession($computerId, $studentId) {
        // Update computer status
        $this->setComputerStatus($computerId, 'in_use');

        // Log session
        $stmt = $this->db->prepare("
            INSERT INTO computer_sessions (computer_id, student_id, session_start)
            VALUES (?, ?, NOW())
        ");
        return $stmt->execute([$computerId, $studentId]);
    }

    public function endSession($computerId) {
        // Get latest session ID
        $stmt = $this->db->prepare("
            SELECT id FROM computer_sessions 
            WHERE computer_id = ? AND session_end IS NULL 
            ORDER BY session_start DESC LIMIT 1
        ");
        $stmt->execute([$computerId]);
        $sessionId = $stmt->fetchColumn();

        if ($sessionId) {
            $this->db->prepare("
                UPDATE computer_sessions 
                SET session_end = NOW() 
                WHERE id = ?
            ")->execute([$sessionId]);

            $this->setComputerStatus($computerId, 'available');
            return true;
        }

        return false;
    }

    public function getSessionHistory($computerId) {
        $stmt = $this->db->prepare("SELECT * FROM computer_sessions WHERE computer_id = ? ORDER BY session_start DESC");
        $stmt->execute([$computerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
