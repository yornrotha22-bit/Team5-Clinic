<?php
require_once '../../config/db.php';
include '../../includes/header.php';
include '../../includes/sidebar.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $patient_id = $_POST['patient_id'] ?? '';
    $doctor_id  = $_POST['doctor_id'] ?? '';
    $appointment_date = $_POST['appointment_date'] ?? '';

    if($patient_id && $doctor_id && $appointment_date){

        $stmt = $pdo->prepare(
            "INSERT INTO appointments(patient_id, doctor_id, appointment_date)
             VALUES(?,?,?)"
        );

        $stmt->execute([$patient_id, $doctor_id, $appointment_date]);

        header('Location: index.php?success=Appointment created successfully');
        exit;
    }
}

$patients = $pdo->query(
    'SELECT id,name FROM patients ORDER BY name'
)->fetchAll(PDO::FETCH_ASSOC);

$doctors = $pdo->query(
    'SELECT id,name FROM doctors ORDER BY name'
)->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>➕ Create Appointment</h2>

<form method="POST" class="form-card">

    <label>Patient</label>
    <select name="patient_id" required>
        <option value="">-- Select Patient --</option>
        <?php foreach($patients as $p): ?>
            <option value="<?= $p['id'] ?>">
                <?= htmlspecialchars($p['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Doctor</label>
    <select name="doctor_id" required>
        <option value="">-- Select Doctor --</option>
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

    <br><br>

    <button type="submit" class="btn"> Save Appointment</button>
    <a href="index.php" class="btn-secondary">Cancel</a>
</form>

<?php include '../../includes/footer.php'; ?>