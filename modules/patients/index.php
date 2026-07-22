<?php
require_once __DIR__ . '/../../middleware/auth.php';
checkAuth();
require_once __DIR__ . '/../../config/db.php';

// Search & Filter Query
$search = trim($_GET['search'] ?? '');
$gender = $_GET['gender'] ?? '';

$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(name LIKE ? OR phone LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($gender) && in_array($gender, ['Male', 'Female', 'Other'])) {
    $where[] = "gender = ?";
    $params[] = $gender;
}

$whereSql = '';
if (count($where) > 0) {
    $whereSql = 'WHERE ' . implode(' AND ', $where);
}

try {
    $sql = "SELECT * FROM patients $whereSql ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $patients = [];
}

// 🔹 រាប់ចំនួន Patients ទាំងអស់ដែលមាននៅក្នុង Array/Database
$totalPatients = count($patients);

// 🔹 កំណត់ Header Titles ឱ្យត្រូវជាមួយ Topbar Header
$pageTitle = "Patients";
$pageSubtitle = $totalPatients . " patients registered";

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="main-wrapper" style="margin-left: 260px; padding: 102px 40px 32px 40px; width: calc(100% - 260px); box-sizing: border-box; min-height: 100vh; background-color: #f8fafc;">

    <style>
        .patients-page {
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
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

        .btn-add-new {
            background-color: #2563eb;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s;
        }

        .btn-add-new:hover {
            background-color: #1d4ed8;
        }

        /* Container Card លាត 100% */
        .card-table {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            width: 100%;
            box-sizing: border-box;
        }

        /* Filter & Search Bar */
        .filter-bar {
            padding: 20px 24px;
            display: flex;
            gap: 16px;
            border-bottom: 1px solid #f1f5f9;
            align-items: center;
            width: 100%;
            box-sizing: border-box;
        }

        .search-box {
            position: relative;
            flex: 1;
        }

        .search-input {
            width: 100%;
            padding: 10px 16px 10px 38px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            font-size: 14px;
            outline: none;
            box-sizing: border-box;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .filter-select {
            padding: 10px 16px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            font-size: 14px;
            color: #334155;
            outline: none;
            cursor: pointer;
        }

        /* Table Design */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .custom-table th {
            background-color: #f8fafc;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 16px 24px;
            border-bottom: 1px solid #f1f5f9;
        }

        .custom-table td {
            padding: 18px 24px;
            border-bottom: 1px solid #f8fafc;
            color: #334155;
            font-size: 14px;
            vertical-align: middle;
        }

        .custom-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .patient-avatar-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .patient-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }

        .patient-name {
            font-weight: 600;
            color: #0f172a;
        }

        .badge-gender {
            padding: 4px 12px;
            border-radius: 14px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .badge-male {
            background-color: #eff6ff;
            color: #2563eb;
        }

        .badge-female {
            background-color: #fdf2f8;
            color: #db2777;
        }

        .badge-other {
            background-color: #f1f5f9;
            color: #475569;
        }

        .action-buttons {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-icon-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-icon-action:hover {
            background-color: #f1f5f9;
            color: #2563eb;
        }

        .btn-icon-action.delete:hover {
            background-color: #fef2f2;
            color: #ef4444;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #94a3b8;
        }
    </style>

    <div class="patients-page">
        
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Patient Directory</h1>
            <a href="create.php" class="btn-add-new">
                <span>+</span> New Patient
            </a>
        </div>

        <!-- Table Card Container -->
        <div class="card-table">
            
            <!-- Filter & Search Form -->
            <form method="GET" action="index.php" class="filter-bar">
                <div class="search-box">
                    <span class="search-icon">🔍</span>
                    <input type="text" name="search" class="search-input" placeholder="Search by name, phone, or email..." value="<?= htmlspecialchars($search); ?>">
                </div>
                
                <select name="gender" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Genders</option>
                    <option value="Male" <?= $gender === 'Male' ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?= $gender === 'Female' ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?= $gender === 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </form>

            <!-- Table Structure -->
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>PATIENT NAME</th>
                            <th>GENDER</th>
                            <th>DATE OF BIRTH</th>
                            <th>PHONE</th>
                            <th>EMAIL</th>
                            <th>ADDRESS</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($patients) > 0): ?>
                            <?php foreach ($patients as $pt): ?>
                                <tr>
                                    <td>
                                        <div class="patient-avatar-cell">
                                            <div class="patient-avatar">
                                                <?= strtoupper(substr($pt['name'] ?? 'P', 0, 1)); ?>
                                            </div>
                                            <span class="patient-name"><?= htmlspecialchars($pt['name'] ?? ''); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php 
                                            $genderClass = 'badge-other';
                                            if (($pt['gender'] ?? '') === 'Male') $genderClass = 'badge-male';
                                            if (($pt['gender'] ?? '') === 'Female') $genderClass = 'badge-female';
                                        ?>
                                        <span class="badge-gender <?= $genderClass; ?>"><?= htmlspecialchars($pt['gender'] ?? 'N/A'); ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($pt['dob'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($pt['phone'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($pt['email'] ?: '-'); ?></td>
                                    <td><?= htmlspecialchars($pt['address'] ?: '-'); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="edit.php?id=<?= $pt['id']; ?>" class="btn-icon-action" title="Edit Patient">
                                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                            </a>

                                            <a href="delete.php?id=<?= $pt['id']; ?>" class="btn-icon-action delete" title="Delete Patient" onclick="return confirm('Are you sure you want to delete this patient?');">
                                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="empty-state">No patients found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>