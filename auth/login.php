<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ប្រសិនបើបាន Login រួចហើយ ឱ្យ Redirect ទៅ Index ទំព័រដើមតែម្តង
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// ហៅ DB Configuration
require_once __DIR__ . '/../config/db.php';

$error = '';

// នៅពេល User ចុច Submit Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        // ស្វែងរក User តាមរយៈ Email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // ពិនិត្យពាក្យសម្ងាត់ (Password Verification)
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'] ?? $user['full_name'] ?? 'User';
            
            header('Location: ../index.php');
            exit;
        } else {
            // ករណី Password មិនទាន់ Hash (Plain Text) សម្រាប់ធ្វើតេស្ត
            if ($user && $password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'] ?? $user['full_name'] ?? 'User';
                
                header('Location: ../index.php');
                exit;
            } else {
                $error = 'Email ឬ Password មិនត្រឹមត្រូវឡើយ!';
            }
        }
    } else {
        $error = 'សូមបំពេញ Email និង Password ឱ្យបានគ្រប់!';
    }
}
?>

<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Clinic Management</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        body {
            background-color: #f8fafc;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-card {
            background: #ffffff;
            padding: 36px 32px;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 28px;
        }
        .login-header h2 {
            margin: 0;
            color: #0f172a;
            font-size: 24px;
            font-weight: 700;
        }
        .login-header p {
            color: #64748b;
            font-size: 14px;
            margin-top: 6px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            color: #1e293b;
            outline: none;
            transition: all 0.2s;
        }
        .form-group input:focus {
            background-color: #ffffff;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 8px;
        }
        .btn-submit:hover {
            background-color: #1d4ed8;
        }
        .error-message {
            background-color: #fef2f2;
            color: #dc2626;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            border: 1px solid #fecaca;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <h2>Welcome Back</h2>
        <p>Sign in to Clinic Management System</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" required placeholder="admin@clinic.com">
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="••••••••">
        </div>

        <button type="submit" class="btn-submit">Sign In</button>
    </form>
</div>

</body>
</html>