<?php
require_once '../config/db.php';

$q = $_GET['q'] ?? '';

if (!empty($q)) {
    $stmt = $conn->prepare("SELECT id, name, phone, gender FROM patients WHERE name LIKE ? OR phone LIKE ? LIMIT 10");
    $searchTerm = "%{$q}%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $patients = [];
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($patients);
}
?>