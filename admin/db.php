<?php
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "inventora_db";
    private $conn;

    public function __construct() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
        
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function closeConnection() {
        $this->conn->close();
    }
}

// Function to sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// user login vayo ki vayena check garxa
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// user ko role check garxa
function checkRole($allowedRoles = []) {
    if (!isset($_SESSION['user_role'])) {
        header("Location: login.php");
        exit();
    }
    
    if (!empty($allowedRoles) && !in_array($_SESSION['user_role'], $allowedRoles)) {
        header("Location: unauthorized.php");
        exit();
    }
}
?>