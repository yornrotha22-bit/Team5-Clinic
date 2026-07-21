<?php
// auth/register.php
session_start();
require_once __DIR__ . '/../config/db.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    $name    = trim($_POST['name'] ?? '');
    $gender  = $_POST['gender'] ?? '';
    $phone   = trim($_POST['phone'] ?? '');
    $dob     = $_POST['dob'] ?? '';
    $address = trim($_POST['address'] ?? '');

    // Validation
    if ($username === '') {
        $errors[] = "Username is required.";
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email is required.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }
    if ($name === '') {
        $errors[] = "Full name is required.";
    }
    if (!in_array($gender, ['Male', 'Female', 'Other'])) {
        $errors[] = "Please select a gender.";
    }
    if ($phone === '') {
        $errors[] = "Phone number is required.";
    }

    // Check if username or email already exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = "An account with this username or email already exists.";
        }
    }

    // Insert new user + patient record (transaction, since two tables are involved)
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'patient')"
            );
            $stmt->execute([$username, $email, $hashed_password]);
            $user_id = $pdo->lastInsertId();

            $stmt = $pdo->prepare(
                "INSERT INTO patients (user_id, name, gender, phone, dob, address) VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $user_id,
                $name,
                $gender,
                $phone,
                $dob !== '' ? $dob : null,
                $address !== '' ? $address : null
            ]);

            $pdo->commit();
            $success = true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Something went wrong. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Team5 Clinic</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <div class="auth-container">
        <h2>Create an Account</h2>

        <?php if ($success): ?>
            <div class="alert alert-success">
                Registration successful! You can now <a href="login.php">log in</a>.
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form action="register.php" method="POST" class="auth-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name"
                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="">-- Select --</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone"
                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob"
                       value="<?php echo htmlspecialchars($_POST['dob'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address"
                       value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>">
            </div>

            <button type="submit" class="btn-submit">Register</button>
        </form>
        <?php endif; ?>

        <p class="auth-switch">Already have an account? <a href="login.php">Login here</a></p>
    </div>

</body>
</html>