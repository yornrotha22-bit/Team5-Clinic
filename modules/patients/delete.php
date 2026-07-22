<?php
require_once __DIR__ . '/../../middleware/auth.php';
checkAuth();
require_once __DIR__ . '/../../config/db.php';
$id = $_GET['id'] ?? null;
if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM patients WHERE id = ?");
        $stmt->execute([$id]);

    } catch (PDOException $e) {
        session_start();
        $_SESSION['error'] = "Cannot delete patient because they have related records (e.g., appointments).";
    }
}

header('Location: index.php');
exit;