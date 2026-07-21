<?php
require_once '../../config/db.php';
include '../../includes/header.php';
include '../../includes/sidebar.php';

$sql = "SELECT a.*, 
               p.name AS patient_name,
               d.name AS doctor_name
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        JOIN doctors d ON a.doctor_id = d.id
        ORDER BY a.appointment_date DESC";

$appointments = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Appointment Management</h2>

<div class="toolbar">
    <a href="create.php" class="btn"> New Appointment</a>
</div>

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
                <a href="update_status.php?id=<?= $a['id'] ?>&status=Completed" class="btn-success">Complete</a>
                <a href="update_status.php?id=<?= $a['id'] ?>&status=Cancelled" class="btn-danger">Cancel</a>
                <a href="delete.php?id=<?= $a['id'] ?>" class="btn-danger"
                   onclick="return confirm('Delete this appointment?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script src="/Team5-Clinic/assets/js/search_patient.js"></script>

<?php include '../../includes/footer.php'; ?>