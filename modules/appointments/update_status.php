<?php
require_once __DIR__ . '/../../config/db.php';

// =========================
// VALID STATUS
// =========================

$allowedStatus = [
    'Pending',
    'Approved',
    'Completed',
    'Cancelled'
];

// =========================
// GET DATA
// =========================

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$status = $_GET['status'] ?? '';

// =========================
// VALIDATE
// =========================

if ($id <= 0) {

    header("Location: ../../index.php?page=appointments");
    exit;

}

if (!in_array($status, $allowedStatus, true)) {

    header("Location: ../../index.php?page=appointments");
    exit;

}

// =========================
// CHECK APPOINTMENT
// =========================

$stmt = $pdo->prepare("
    SELECT id
    FROM appointments
    WHERE id = ?
");

$stmt->execute([$id]);

if (!$stmt->fetch()) {

    header("Location: ../../index.php?page=appointments");
    exit;

}

// =========================
// UPDATE STATUS
// =========================

$update = $pdo->prepare("
    UPDATE appointments
    SET status = ?
    WHERE id = ?
");

$update->execute([
    $status,
    $id
]);

// =========================
// REDIRECT
// =========================

header("Location: ../../index.php?page=appointments");
exit;