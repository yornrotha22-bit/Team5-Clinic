<?php
require_once __DIR__ . '/../../middleware/auth.php';
checkAuth();
require_once __DIR__ . '/../../config/db.php';

// Search & Fetch Data
$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $pdo->prepare("SELECT d.*, dept.name AS department_name 
                           FROM doctors d 
                           LEFT JOIN departments dept ON d.department_id = dept.id 
                           WHERE d.name LIKE ? OR d.specialization LIKE ? OR d.phone LIKE ? OR d.email LIKE ?
                           ORDER BY d.id DESC");
    $stmt->execute(["%$search%", "%$search%", "%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT d.*, dept.name AS department_name 
                         FROM doctors d 
                         LEFT JOIN departments dept ON d.department_id = dept.id 
                         ORDER BY d.id DESC");
}
$doctors = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<style>
    /* 🔹 កែប្រែ Layout Container ដើម្បីគេចចេញពី Sidebar 260px */
    .page-container {
        margin-left: 260px; /* រុញ Content មកខាងស្តាំឲ្យផុតពី Sidebar */
        width: calc(100% - 260px); /* កំណត់ទំហំទទឹងដែលនៅសល់ */
        background-color: #f8fafc;
        min-height: 100vh;
        padding: 40px 32px;
        box-sizing: border-box;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .page-title {
        font-size: 24px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .page-subtitle {
        color: #64748b;
        font-size: 14px;
        margin-top: 4px;
    }

    .table-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border: 1px solid #f1f5f9;
    }

    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        gap: 16px;
    }

    .search-box {
        display: flex;
        gap: 8px;
        flex: 1;
        max-width: 420px;
    }

    .form-input {
        width: 100%;
        padding: 10px 16px;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        outline: none;
    }

    .form-input:focus {
        background-color: #ffffff;
        border-color: #2563eb;
    }
    .btn-search {
        background-color: #f1f5f9;
        color: #334155;
        font-weight: 600;
        padding: 10px 18px;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        cursor: pointer;
    }

    .btn-primary {
        background-color: #2563eb;
        color: #ffffff;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 20px;
        text-decoration: none;
        font-size: 14px;
        transition: background-color 0.2s ease;
    }

    .btn-primary:hover {
        background-color: #1d4ed8;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .custom-table th {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        padding: 14px 16px;
        border-bottom: 2px solid #f1f5f9;
        letter-spacing: 0.5px;
    }

    .custom-table td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        color: #334155;
    }

    .doctor-name {
        font-weight: 600;
        color: #0f172a;
    }

    .badge-specialty {
        background-color: #eff6ff;
        color: #2563eb;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .badge-dept {
        background-color: #f1f5f9;
        color: #475569;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
    }

    .btn-action {
        text-decoration: none;
        margin-right: 10px;
        font-size: 16px;
        display: inline-block;
        transition: transform 0.1s ease;
    }

    .btn-action:hover {
        transform: scale(1.2);
    }
</style>

<div class="page-container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Doctors List</h1>
            <p class="page-subtitle"><?= count($doctors); ?> doctors registered</p>
        </div>
    </div>

    <div class="table-card">
        <div class="action-bar">
            <form method="GET" action="index.php" class="search-box">
                <input type="text" name="search" class="form-input" value="<?= htmlspecialchars($search); ?>" placeholder="Search name, specialty, phone, email...">
                <button type="submit" class="btn-search">Search</button>
            </form>
            <a href="create.php" class="btn-primary">+ Add Doctor</a>
        </div>

        <table class="custom-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>FULL NAME</th>
                    <th>SPECIALTY</th>
                    <th>DEPARTMENT</th>
                    <th>PHONE</th>
                    <th>EMAIL</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($doctors)): ?>
                    <?php foreach ($doctors as $doc): ?>
                    <tr>
                        <td><strong>#<?= htmlspecialchars($doc['id']) ?></strong></td>
                        <td><span class="doctor-name"><?= htmlspecialchars($doc['name']) ?></span></td>
                        <td><span class="badge-specialty"><?= htmlspecialchars($doc['specialization'] ?? 'General') ?></span></td>
                        <td><span class="badge-dept"><?= htmlspecialchars($doc['department_name'] ?? 'N/A') ?></span></td>
                        <td><?= htmlspecialchars($doc['phone']) ?></td>
                        <td><?= htmlspecialchars($doc['email'] ?? '—') ?></td>
                        <td>
                            <a href="edit.php?id=<?= $doc['id'] ?>" class="btn-action" title="Edit Doctor">✏️</a>
                            <a href="delete.php?id=<?= $doc['id'] ?>" class="btn-action" onclick="return confirm('Are you sure you want to delete this doctor?')" title="Delete Doctor">🗑️</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #94a3b8; padding: 36px;">No doctors found in the records.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>