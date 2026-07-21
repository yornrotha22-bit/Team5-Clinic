<?php
// ============================================
// Database Connection (PDO)
// ============================================
$host = 'localhost';
$dbname = 'clinic_db';
$username = 'root';
$password = ''; // put your MySQL password here

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'DB Connection failed: ' . $e->getMessage()]));
}