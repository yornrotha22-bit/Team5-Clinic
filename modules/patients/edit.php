<?php
require_once __DIR__ . '/../../middleware/auth.php';
checkAuth();
require_once __DIR__ . '/../../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['full_name']);
    $gender  = $_POST['gender'] ?? '';
    $dob     = $_POST['dob'];
    $phone   = trim($_POST['phone']);
    $email   = trim($_POST['email']);
    $address = trim($_POST['address']);

    if (!empty($name) && !empty($gender) && !empty($dob) && !empty($phone)) {
        try {
            $sql = "INSERT INTO patients (name, gender, dob, phone, email, address) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $gender, $dob, $phone, $email, $address]);

            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all required fields!";
    }
}
$pageTitle = "Edit Patient";
$pageSubtitle = "Editing profile: " . htmlspecialchars($patient['name'] ?? '');

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div class="main-wrapper" style="margin-left: 260px; padding: 32px 40px; width: calc(100% - 260px); box-sizing: border-box; min-height: 100vh; background-color: #f8fafc;">

    <style>
        .patients-page {
            width: 100%;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            width: 100%;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .form-page-body {
            width: 100%;
            display: flex;
            /* justify-content: center; */
            margin-left: 60px;
            padding: 40px;
            align-items: flex-start;
        }

        .form-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 36px;
            width: 100%;
            max-width: 720px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
            border: 1px solid #f1f5f9;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px 24px;
        }

        .full-width {
            grid-column: span 2;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-label {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 12px 16px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            color: #1e293b;
            outline: none;
            box-sizing: border-box;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            background-color: #ffffff;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 90px;
        }

        .form-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 12px;
            grid-column: span 2;
            border-top: 1px solid #f1f5f9;
            padding-top: 24px;
        }

        .btn-register {
            background-color: #2563eb;
            color: #ffffff;
            font-weight: 600;
            font-size: 14px;
            padding: 12px 28px;
            border-radius: 20px;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .btn-register:hover {
            background-color: #1d4ed8;
        }

        .btn-cancel {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 600;
            font-size: 14px;
            padding: 12px 28px;
            border-radius: 20px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.2s ease;
        }

        .btn-cancel:hover {
            background-color: #e2e8f0;
        }

        .error-alert {
            background-color: #fef2f2;
            color: #dc2626;
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 24px;
            border: 1px solid #fecaca;
        }
    </style>

    <div class="patients-page">
        <!-- 🔹 Page Header -->
        <div class="page-header">
            <h1 class="page-title">New Patient</h1>
            <a href="index.php" class="btn-cancel" style="padding: 8px 18px;">← Back to Directory</a>
        </div>

        <div class="form-page-body">
            <div class="form-card">

                <?php if ($error): ?>
                    <div class="error-alert"><?= htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" action="create.php">
                    <div class="form-grid">
                        
                        <div class="form-group full-width">
                            <label class="form-label">FULL NAME *</label>
                            <input type="text" name="full_name" class="form-input" placeholder="e.g. William Harrison" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">GENDER *</label>
                            <select name="gender" class="form-select" required>
                                <option value="Male" selected>Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">DATE OF BIRTH *</label>
                            <input type="date" name="dob" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">PHONE *</label>
                            <input type="text" name="phone" class="form-input" placeholder="089 761 563" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">EMAIL</label>
                            <input type="email" name="email" class="form-input" placeholder="patient@email.com">
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">ADDRESS</label>
                            <textarea name="address" class="form-textarea" placeholder="Street, City, Province..."></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-register">Register Patient</button>
                            <a href="index.php" class="btn-cancel">Cancel</a>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>