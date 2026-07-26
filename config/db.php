<?php
$host = "localhost";
$dbname = "clinic_db";
$username = "root";
$password = "200612";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'DB Connection failed: ' . $e->getMessage()]));
}
