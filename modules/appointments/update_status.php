<?php
require_once '../../config/db.php';

$id = $_GET['id'] ?? 0;
$status = $_GET['status'] ?? 'Pending';

$allowed = ['Pending','Confirmed','Completed','Cancelled'];

if(in_array($status,$allowed)){
    $stmt = $pdo->prepare(
        "UPDATE appointments SET status=? WHERE id=?"
    );
    $stmt->execute([$status,$id]);
}

header('Location: index.php');
exit;