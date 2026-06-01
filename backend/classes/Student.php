<?php
require_once __DIR__ . '/../config/Database.php';

class Student {
    private $conn;
    private $table = 'students';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function createStudent($data) {
        $query = "INSERT INTO " . $this->table . " (matric_number, first_name, last_name, email, phone, department_id, level_id, date_of_birth, gender, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssssiisss", $data['matric_number'], $data['first_name'], $data['last_name'], $data['email'], $data['phone'], $data['department_id'], $data['level_id'], $data['date_of_birth'], $data['gender'], $data['address']);

        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->insert_id, 'message' => 'Student created successfully'];
        }

        return ['success' => false, 'message' => 'Failed to create student'];
    }

    public function getStudent($id) {
        $query = "SELECT s.*, d.name as department_name, l.name as level_name FROM " . $this->table . " s LEFT JOIN departments d ON s.department_id = d.id LEFT JOIN levels l ON s.level_id = l.id WHERE s.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getStudentByMatric($matric_number) {
        $query = "SELECT s.*, d.name as department_name, l.name as level_name FROM " . $this->table . " s LEFT JOIN departments d ON s.department_id = d.id LEFT JOIN levels l ON s.level_id = l.id WHERE s.matric_number = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $matric_number);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getAllStudents($filters = []) {
        $query = "SELECT s.*, d.name as department_name, l.name as level_name FROM " . $this->table . " s LEFT JOIN departments d ON s.department_id = d.id LEFT JOIN levels l ON s.level_id = l.id WHERE 1=1";

        $params = [];
        $types = "";

        if (isset($filters['department_id'])) {
            $query .= " AND s.department_id = ?";
            $params[] = $filters['department_id'];
            $types .= "i";
        }

        if (isset($filters['level_id'])) {
            $query .= " AND s.level_id = ?";
            $params[] = $filters['level_id'];
            $types .= "i";
        }

        $query .= " ORDER BY s.first_name ASC";

        if (!empty($params)) {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        return $this->conn->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalStudents() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $result = $this->conn->query($query)->fetch_assoc();
        return $result['total'];
    }

    public function getTotalEnrolledBiometric() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE is_enrolled_biometric = 1";
        $result = $this->conn->query($query)->fetch_assoc();
        return $result['total'];
    }
}
?>