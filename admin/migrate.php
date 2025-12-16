<?php
error_reporting(E_ALL); //sabai error, warning, notice dekhauxa
ini_set('display_errors', 1); // error dekhaune ki nadekhaune vanera herxa

require_once 'connect.php';

$DB = "inventora_db";

// Create database
if (!$conn->query("CREATE DATABASE IF NOT EXISTS $DB")) {
    die("DB Error: " . $conn->error);
}

$conn->select_db($DB);

// Create table
$table_sql = "CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user','viewer') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($table_sql)) {
    echo "Database & Table created successfully";
} else {
    echo " Table Error: " . $conn->error;
}
