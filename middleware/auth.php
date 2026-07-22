<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ដាក់ Fake Session សម្រាប់ Test
$_SESSION['user_id'] = 1; 

function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /PHP+laravel/PHP/Team5-Clinic/auth/login.php');
        exit;
    }
}
?>