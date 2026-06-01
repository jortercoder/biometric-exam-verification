<?php
require_once __DIR__ . '/../config/Database.php';

class User {
    private $conn;
    private $table = 'users';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function login($username, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE username = ? AND is_active = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            return ['success' => false, 'message' => 'User not found'];
        }

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $update_query = "UPDATE " . $this->table . " SET last_login = NOW() WHERE id = ?";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bind_param("i", $user['id']);
            $update_stmt->execute();

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            return ['success' => true, 'user' => $user];
        }

        return ['success' => false, 'message' => 'Invalid password'];
    }

    public function register($username, $email, $password, $first_name, $last_name, $role = 'invigilator') {
        $check_query = "SELECT id FROM " . $this->table . " WHERE username = ? OR email = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            return ['success' => false, 'message' => 'Username or email already exists'];
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $query = "INSERT INTO " . $this->table . " (username, email, password, first_name, last_name, role) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssss", $username, $email, $hashed_password, $first_name, $last_name, $role);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'User registered successfully'];
        }

        return ['success' => false, 'message' => 'Registration failed'];
    }

    public function getUser($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}
?>