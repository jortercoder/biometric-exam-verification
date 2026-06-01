<?php
require_once __DIR__ . '/../config/Database.php';

class Attendance {
    private $conn;
    private $table = 'attendance';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function markAttendance($student_id, $enrollment_id, $examination_id, $status, $confidence_score = 0, $recorded_by = null) {
        $check_query = "SELECT id FROM " . $this->table . " WHERE student_id = ? AND examination_id = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bind_param("ii", $student_id, $examination_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            return ['success' => false, 'message' => 'Attendance already marked for this student'];
        }

        $query = "INSERT INTO " . $this->table . " (student_id, enrollment_id, examination_id, status, verification_time, confidence_score, recorded_by) VALUES (?, ?, ?, ?, NOW(), ?, ?)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iiiisi", $student_id, $enrollment_id, $examination_id, $status, $confidence_score, $recorded_by);

        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->insert_id, 'message' => 'Attendance marked successfully'];
        }

        return ['success' => false, 'message' => 'Failed to mark attendance'];
    }

    public function getAttendanceByExamination($examination_id) {
        $query = "SELECT a.*, s.matric_number, s.first_name, s.last_name, s.email, d.name as department_name FROM " . $this->table . " a INNER JOIN students s ON a.student_id = s.id LEFT JOIN departments d ON s.department_id = d.id WHERE a.examination_id = ? ORDER BY a.status DESC, s.first_name ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $examination_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAttendanceByStudent($student_id) {
        $query = "SELECT a.*, e.course_code, e.course_name, e.exam_date FROM " . $this->table . " a INNER JOIN examinations e ON a.examination_id = e.id WHERE a.student_id = ? ORDER BY e.exam_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAttendanceStatistics($examination_id = null) {
        $query = "SELECT COUNT(*) as total, SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count, SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count, ROUND(SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as attendance_rate FROM " . $this->table;

        if ($examination_id) {
            $query .= " WHERE examination_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $examination_id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }

        return $this->conn->query($query)->fetch_assoc();
    }
}
?>