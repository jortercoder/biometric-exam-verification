<?php
require_once __DIR__ . '/../config/Database.php';

class Enrollment {
    private $conn;
    private $table = 'enrollments';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function enrollStudent($student_id, $fingerprint_template, $fingerprint_image_path, $finger_position = 'right_index', $quality_score = 0) {
        $check_query = "SELECT id FROM " . $this->table . " WHERE student_id = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bind_param("i", $student_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            return ['success' => false, 'message' => 'Student already enrolled'];
        }

        $query = "INSERT INTO " . $this->table . " (student_id, fingerprint_template, fingerprint_image_path, finger_position, quality_score) VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isssi", $student_id, $fingerprint_template, $fingerprint_image_path, $finger_position, $quality_score);

        if ($stmt->execute()) {
            $update_query = "UPDATE students SET is_enrolled_biometric = 1 WHERE id = ?";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bind_param("i", $student_id);
            $update_stmt->execute();

            return ['success' => true, 'id' => $this->conn->insert_id, 'message' => 'Student enrolled successfully'];
        }

        return ['success' => false, 'message' => 'Enrollment failed'];
    }

    public function getEnrollment($student_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE student_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getAllEnrollments() {
        $query = "SELECT e.*, s.matric_number, s.first_name, s.last_name, s.email, d.name as department_name FROM " . $this->table . " e INNER JOIN students s ON e.student_id = s.id LEFT JOIN departments d ON s.department_id = d.id ORDER BY s.first_name ASC";
        return $this->conn->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalEnrollments() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $result = $this->conn->query($query)->fetch_assoc();
        return $result['total'];
    }
}
?>