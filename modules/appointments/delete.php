<?php
require_once __DIR__ . '/../../config/db.php';

// Validate ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: ../../index.php?page=appointments");
    exit;
}

// Check appointment exists
$stmt = $pdo->prepare("SELECT id FROM appointments WHERE id = ?");
$stmt->execute([$id]);

if (!$stmt->fetch()) {
    header("Location: ../../index.php?page=appointments");
    exit;
}

// Delete appointment
$stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
$stmt->execute([$id]);

// Redirect back
header("Location: ../../index.php?page=appointments");
exit;