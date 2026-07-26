<?php

require_once __DIR__.'/../config/db.php';

header("Content-Type: application/json");

$q = trim($_GET['q'] ?? '');

if($q==""){
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("
SELECT id,name
FROM patients
WHERE name LIKE ?
ORDER BY name
LIMIT 10
");

$stmt->execute(["%$q%"]);

echo json_encode($stmt->fetchAll());
