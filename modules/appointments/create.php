<?php
require_once '../../config/db.php';
include '../../includes/header.php';
include '../../includes/sidebar.php';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $stmt = $pdo->prepare(
        "INSERT INTO appointments(patient_id,doctor_id,appointment_date,status)
         VALUES(?,?,?,?)"
    );

    $stmt->execute([
        $_POST['patient_id'],
        $_POST['doctor_id'],
        $_POST['appointment_date'],
        'Pending'
    ]);

    header('Location: index.php');
    exit;
}

$patients = $pdo->query("SELECT id,name FROM patients ORDER BY name")->fetchAll();
$doctors  = $pdo->query("SELECT id,name FROM doctors ORDER BY name")->fetchAll();
?>

<link rel="stylesheet" href="/Team5-Clinic/assets/css/appointments.css">

<div class="form-card">
    <h2>New Appointment</h2>

    <form method="POST">

        <label>Patient</label>
        <select name="patient_id" required>
            <option value="">Select patient</option>
            <?php foreach($patients as $p): ?>
                <option value="<?= $p['id'] ?>">
                    <?= htmlspecialchars($p['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Doctor</label>
        <select name="doctor_id" required>
            <option value="">Select doctor</option>
            <?php foreach($doctors as $d): ?>
                <option value="<?= $d['id'] ?>">
                    <?= htmlspecialchars($d['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Appointment Date & Time</label>
        <input type="datetime-local"
               name="appointment_date"
               min="<?= date('Y-m-d\\TH:i') ?>"
               required>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Save Appointment</button>
            <a href="index.php" class="btn-secondary">Cancel</a>
        </div>

    </form>
</div>

<?php include '../../includes/footer.php'; ?>