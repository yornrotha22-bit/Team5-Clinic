<?php
require_once '../config/db.php';

<<<<<<< HEAD
header('Content-Type: application/json');

$q = trim($_GET['q'] ?? '');

if($q === ''){
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare(
    "SELECT id, name, phone
     FROM patients
     WHERE name LIKE ?
     ORDER BY name
     LIMIT 10"
);

$stmt->execute(["%$q%"]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
=======
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
>>>>>>> rotha-data-management
