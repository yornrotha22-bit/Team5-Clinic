<?php
require_once '../../config/db.php';
include '../../includes/header.php';
include '../../includes/sidebar.php';

// Statistics
$total = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
$pending = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status='Pending'")->fetchColumn();
$confirmed = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status='Confirmed'")->fetchColumn();
$completed = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status='Completed'")->fetchColumn();
$cancelled = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status='Cancelled'")->fetchColumn();

$status = $_GET['status'] ?? '';
$doctor = $_GET['doctor'] ?? '';

$where = [];
$params = [];

if($status !== ''){
    $where[] = 'a.status = ?';
    $params[] = $status;
}

if($doctor !== ''){
    $where[] = 'd.id = ?';
    $params[] = $doctor;
}

$whereSql = count($where) ? 'WHERE '.implode(' AND ', $where) : '';

$sql = "SELECT a.*, p.name AS patient_name,
               d.name AS doctor_name,
               dep.name AS department_name
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        JOIN doctors d ON a.doctor_id = d.id
        LEFT JOIN departments dep ON d.department_id = dep.id
        $whereSql
        ORDER BY a.appointment_date ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$doctors = $pdo->query("SELECT id,name FROM doctors ORDER BY name")->fetchAll();
?>

<link rel="stylesheet" href="/Team5-Clinic/assets/css/appointments.css">

<div class="page-header">
    <div>
        <h1>Appointments</h1>
        <p><?= $total ?> total appointments</p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number"><?= $pending ?></div>
        <span class="status-badge pending">Pending</span>
    </div>

    <div class="stat-card">
        <div class="stat-number"><?= $confirmed ?></div>
        <span class="status-badge confirmed">Confirmed</span>
    </div>

    <div class="stat-card">
        <div class="stat-number"><?= $completed ?></div>
        <span class="status-badge completed">Completed</span>
    </div>

    <div class="stat-card">
        <div class="stat-number"><?= $cancelled ?></div>
        <span class="status-badge cancelled">Cancelled</span>
    </div>
</div>

<div class="filters-card">
    <div class="filters-row">
        <div class="search-input">
            <input type="text" id="searchPatient" placeholder="Search appointments...">
        </div>

        <input type="date" class="date-input">

        <form method="GET" class="filters-inline">
            <select name="doctor">
                <option value="">All Doctors</option>
                <?php foreach($doctors as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= $doctor==$d['id']?'selected':'' ?>>
                        <?= htmlspecialchars($d['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="status">
                <option value="">All Statuses</option>
                <option value="Pending" <?= $status=='Pending'?'selected':'' ?>>Pending</option>
                <option value="Confirmed" <?= $status=='Confirmed'?'selected':'' ?>>Confirmed</option>
                <option value="Completed" <?= $status=='Completed'?'selected':'' ?>>Completed</option>
                <option value="Cancelled" <?= $status=='Cancelled'?'selected':'' ?>>Cancelled</option>
            </select>

            <button type="submit" class="btn-primary">Filter</button>
        </form>

        <a href="create.php" class="btn-primary new-btn">+ New Appointment</a>
    </div>
</div>

<div class="table-card">
    <table class="appointments-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Department</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody id="appointmentsTable">
        <?php foreach($appointments as $a): ?>
            <tr>
                <td class="appt-id">APT<?= str_pad($a['id'],3,'0',STR_PAD_LEFT) ?></td>

                <td>
                    <div class="patient-cell">
                        <div class="avatar">
                            <?= strtoupper(substr($a['patient_name'],0,1)) ?>
                        </div>
                        <div class="patient-name">
                            <?= htmlspecialchars($a['patient_name']) ?>
                        </div>
                    </div>
                </td>

                <td><?= htmlspecialchars($a['doctor_name']) ?></td>

                <td><?= htmlspecialchars($a['department_name']) ?></td>

                <td class="date-cell">
                    <?= date('Y-m-d', strtotime($a['appointment_date'])) ?>
                </td>

                <td>
                    <?= date('H:i', strtotime($a['appointment_date'])) ?>
                </td>

                <td>
                    <span class="status-badge <?= strtolower($a['status']) ?>">
                        <?= $a['status'] ?>
                    </span>
                </td>

                <td>
                    <div class="action-buttons">
                        <a href="update_status.php?id=<?= $a['id'] ?>&status=Confirmed" title="Confirm">✔️</a>
                        <a href="update_status.php?id=<?= $a['id'] ?>&status=Completed" title="Complete">✅</a>
                        <a href="update_status.php?id=<?= $a['id'] ?>&status=Cancelled" title="Cancel">❌</a>
                        <a href="delete.php?id=<?= $a['id'] ?>"
                           onclick="return confirm('Delete this appointment?')"
                           title="Delete">🗑️</a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="/Team5-Clinic/assets/js/appointments.js"></script>

<?php include '../../includes/footer.php'; ?>