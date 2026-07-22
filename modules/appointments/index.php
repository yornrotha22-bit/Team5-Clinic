<?php
require_once '../../config/db.php';
include '../../includes/header.php';
include '../../includes/sidebar.php';

// ===== Statistics =====
$pending   = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status='Pending'")->fetchColumn();
$completed = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status='Completed'")->fetchColumn();
$cancelled = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status='Cancelled'")->fetchColumn();

// ===== Filter =====
$statusFilter = $_GET['status'] ?? '';

$where = '';
$params = [];

if($statusFilter !== ''){
    $where = ' WHERE a.status = ? ';
    $params[] = $statusFilter;
}

// ===== Appointments =====
$sql = "SELECT a.*, p.name AS patient_name, d.name AS doctor_name
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        JOIN doctors d ON a.doctor_id = d.id
        $where
        ORDER BY a.appointment_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ===== Today appointments =====
$today = $pdo->query(
    "SELECT p.name AS patient_name, d.name AS doctor_name, appointment_date
     FROM appointments a
     JOIN patients p ON a.patient_id=p.id
     JOIN doctors d ON a.doctor_id=d.id
     WHERE DATE(appointment_date)=CURDATE()
     ORDER BY appointment_date ASC"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Appointment Management</h2>

<?php if(isset($_GET['success'])): ?>
    <div class="toast success">
        <?= htmlspecialchars($_GET['success']) ?>
    </div>
<?php endif; ?>

<div class="cards">
    <div class="card stat pending-card">
        <h3>Pending</h3>
        <p><?= $pending ?></p>
    </div>

    <div class="card stat completed-card">
        <h3>Completed</h3>
        <p><?= $completed ?></p>
    </div>

    <div class="card stat cancelled-card">
        <h3>Cancelled</h3>
        <p><?= $cancelled ?></p>
    </div>
</div>

<div class="today-panel">
    <h3>📌 Today’s Appointments</h3>

    <?php if(count($today) > 0): ?>
        <ul>
        <?php foreach($today as $t): ?>
            <li>
                <strong><?= htmlspecialchars($t['patient_name']) ?></strong>
                with Dr. <?= htmlspecialchars($t['doctor_name']) ?>
                at <?= date('h:i A', strtotime($t['appointment_date'])) ?>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No appointments for today.</p>
    <?php endif; ?>
</div>

<div class="toolbar">
    <a href="create.php" class="btn">➕ New Appointment</a>
</div>

<form method="GET" class="filter-form">
    <select name="status">
        <option value="">All Status</option>
        <option value="Pending"   <?= $statusFilter=='Pending'?'selected':'' ?>>Pending</option>
        <option value="Completed" <?= $statusFilter=='Completed'?'selected':'' ?>>Completed</option>
        <option value="Cancelled" <?= $statusFilter=='Cancelled'?'selected':'' ?>>Cancelled</option>
    </select>

    <button type="submit" class="btn">Filter</button>
    <a href="index.php" class="btn-secondary">Reset</a>
</form>

<div class="search-box">
    <input type="text" id="searchPatient" placeholder="Search patient by name...">
    <div id="result" class="search-result"></div>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Patient</th>
            <th>Doctor</th>
            <th>Date & Time</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
    <?php if(count($appointments) > 0): ?>

        <?php foreach($appointments as $a): ?>
        <tr>
            <td><?= $a['id'] ?></td>
            <td><?= htmlspecialchars($a['patient_name']) ?></td>
            <td><?= htmlspecialchars($a['doctor_name']) ?></td>
            <td><?= date('d M Y h:i A', strtotime($a['appointment_date'])) ?></td>
            <td>
                <span class="badge <?= strtolower($a['status']) ?>">
                    <?= $a['status'] ?>
                </span>
            </td>
            <td>
                <a class="btn-success"
                   href="update_status.php?id=<?= $a['id'] ?>&status=Completed">
                   Complete
                </a>

                <a class="btn-danger"
                   href="update_status.php?id=<?= $a['id'] ?>&status=Cancelled">
                   Cancel
                </a>

                <a class="btn-danger"
                   href="delete.php?id=<?= $a['id'] ?>"
                   onclick="return confirm('Delete this appointment?')">
                   Delete
                </a>
            </td>
        </tr>
        <?php endforeach; ?>

    <?php else: ?>

        <tr>
            <td colspan="6" class="empty-state">
                <div>
                    <div style="font-size:40px">📅</div>
                    <h3>No appointments found</h3>
                    <p>Create a new appointment to get started.</p>
                    <a href="create.php" class="btn">Create Appointment</a>
                </div>
            </td>
        </tr>

    <?php endif; ?>
    </tbody>
</table>

<script src="/Team5-Clinic/assets/js/search_patient.js"></script>

<?php include '../../includes/footer.php'; ?>