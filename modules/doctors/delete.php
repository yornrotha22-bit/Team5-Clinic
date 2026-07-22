<?php
require_once __DIR__ . '/../../middleware/auth.php';
checkAuth();
require_once __DIR__ . '/../../config/db.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM doctors WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: index.php');
exit;