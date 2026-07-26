<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../config/db.php';

if (file_exists('../../middleware/auth.php')) {
    require_once '../../middleware/auth.php';
}
$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $stmt = $pdo->prepare("DELETE FROM departments WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    } catch (PDOException $e) {
        die("<h3 style='color:red;'>Delete Failed: " . $e->getMessage() . "</h3>");
    }
}
header('Location: index.php');
exit;