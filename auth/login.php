<?php
// auth/login.php
session_start();
require_once __DIR__ . '/../config/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login    = trim($_POST['login'] ?? ''); // username or email
    $password = $_POST['password'] ?? '';

    if ($login === '') {
        $errors[] = "Username or email is required.";
    }
    if ($password === '') {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id, username, email, password, role FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Login success - store session data
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email']    = $user['email'];
            $_SESSION['role']     = $user['role'];

            // Redirect based on role (adjust paths to match your modules folder)
            switch ($user['role']) {
                case 'doctor':
                    header("Location: ../modules/doctors/index.php");
                    break;
                case 'admin':
                    header("Location: ../modules/dashboard/index.php");
                    break;
                default:
                    header("Location: ../modules/patients/index.php");
                    break;
            }
            exit;
        } else {
            $errors[] = "Invalid username/email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Team5 Clinic</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <div class="auth-container">
        <h2>Welcome Back</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="auth-form">
            <div class="form-group">
                <label for="login">Username or Email</label>
                <input type="text" id="login" name="login"
                       value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn-submit">Login</button>
        </form>

        <p class="auth-switch">Don't have an account? <a href="register.php">Register here</a></p>
    </div>

</body>
</html>