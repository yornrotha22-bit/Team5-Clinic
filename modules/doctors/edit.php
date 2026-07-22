<?php
require_once __DIR__ . '/../../middleware/auth.php';
checkAuth();
require_once __DIR__ . '/../../config/db.php';

$error = '';
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit;
}

// Fetch Doctor Data
$stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = ?");
$stmt->execute([$id]);
$doctor = $stmt->fetch();

if (!$doctor) {
    header('Location: index.php');
    exit;
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name          = trim($_POST['full_name']);
    $specialization = trim($_POST['specialty']);
    $department_id  = $_POST['department_id'];
    $phone          = trim($_POST['phone']);
    $email          = trim($_POST['email']);

    if (!empty($name) && !empty($department_id) && !empty($phone)) {
        try {
            $sql = "UPDATE doctors 
                    SET name = ?, specialization = ?, department_id = ?, phone = ?, email = ? 
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $specialization, $department_id, $phone, $email, $id]);

            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all required fields!";
    }
}

// Fetch Departments
$departments = $pdo->query("SELECT * FROM departments")->fetchAll();

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<style>
    .form-page-body {
        margin-left: 260px;
        width: calc(100% - 260px);
        background-color: #f8fafc;
        min-height: 100vh;
        padding: 100px;
        box-sizing: border-box;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    .form-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 36px;
        max-width: 680px;
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

    .form-input, .form-select {
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
    }

    .form-input:focus, .form-select:focus {
        background-color: #ffffff;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        grid-column: span 2;
    }

    .btn-update {
        background-color: #2563eb;
        color: #ffffff;
        font-weight: 600;
        font-size: 14px;
        padding: 12px 28px;
        border-radius: 20px;
        border: none;
        cursor: pointer;
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
    }

    .error-alert {
        background-color: #fef2f2;
        color: #dc2626;
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 14px;
        margin-bottom: 20px;
        border: 1px solid #fecaca;
    }
</style>

<div class="form-page-body">
    <div class="form-card">
        
        <h2 style="margin-top: 0; margin-bottom: 24px; color: #0f172a;">Edit Doctor Details</h2>

        <?php if ($error): ?>
            <div class="error-alert"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="edit.php?id=<?= $doctor['id']; ?>">
            <div class="form-grid">
                
                <!-- Full Name -->
                <div class="form-group full-width">
                    <label class="form-label">FULL NAME *</label>
                    <input type="text" name="full_name" class="form-input" value="<?= htmlspecialchars($doctor['name']); ?>" required>
                </div>

                <!-- Specialty -->
                <div class="form-group">
                    <label class="form-label">SPECIALTY *</label>
                    <input type="text" name="specialty" class="form-input" value="<?= htmlspecialchars($doctor['specialization'] ?? ''); ?>" required>
                </div>

                <!-- Department -->
                <div class="form-group">
                    <label class="form-label">DEPARTMENT *</label>
                    <select name="department_id" class="form-select" required>
                        <option value="" disabled>Select Department</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id']; ?>" <?= ($dept['id'] == $doctor['department_id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($dept['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Phone -->
                <div class="form-group">
                    <label class="form-label">PHONE *</label>
                    <input type="text" name="phone" class="form-input" value="<?= htmlspecialchars($doctor['phone']); ?>" required>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label class="form-label">EMAIL</label>
                    <input type="email" name="email" class="form-input" value="<?= htmlspecialchars($doctor['email'] ?? ''); ?>" placeholder="doctor@cliniccare.com">
                </div>

                <!-- Action Buttons -->
                <div class="form-actions">
                    <button type="submit" class="btn-update">Update Doctor</button>
                    <a href="index.php" class="btn-cancel">Cancel</a>
                </div>

            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>