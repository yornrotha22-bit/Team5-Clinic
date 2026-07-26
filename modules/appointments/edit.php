<?php
require_once __DIR__ . '/../../config/db.php';

/* ==========================
   GET APPOINTMENT ID
========================== */

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {

    header("Location: index.php?page=appointments");
    exit;

}

$error = '';
$success = '';

/* ==========================
   LOAD PATIENTS
========================== */

$patients = $pdo->query("
    SELECT id, name
    FROM patients
    ORDER BY name ASC
")->fetchAll(PDO::FETCH_ASSOC);


/* ==========================
   LOAD DOCTORS
========================== */

$doctors = $pdo->query("
    SELECT
        d.id,
        d.name,
        dep.name AS department_name
    FROM doctors d
    INNER JOIN departments dep
        ON d.department_id = dep.id
    ORDER BY d.name ASC
")->fetchAll(PDO::FETCH_ASSOC);


/* ==========================
   LOAD APPOINTMENT
========================== */

$stmt = $pdo->prepare("
    SELECT *
    FROM appointments
    WHERE id = ?
");

$stmt->execute([$id]);

$appointment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$appointment) {

    header("Location: index.php?page=appointments");
    exit;

}


/* ==========================
   UPDATE
========================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $patient_id = intval($_POST['patient_id'] ?? 0);

    $doctor_id = intval($_POST['doctor_id'] ?? 0);

    $appointment_date = trim($_POST['appointment_date'] ?? '');

    $appointment_time = trim($_POST['appointment_time'] ?? '');

    $status = trim($_POST['status'] ?? 'Pending');

    $notes = trim($_POST['notes'] ?? '');

    if (

        $patient_id <= 0 ||

        $doctor_id <= 0 ||

        empty($appointment_date) ||

        empty($appointment_time)

    ) {

        $error = "Please fill in all required fields.";

    } else {

        $update = $pdo->prepare("
            UPDATE appointments
            SET
                patient_id = ?,
                doctor_id = ?,
                appointment_date = ?,
                appointment_time = ?,
                status = ?,
                notes = ?
            WHERE id = ?
        ");

        $saved = $update->execute([

            $patient_id,

            $doctor_id,

            $appointment_date,

            $appointment_time,

            $status,

            $notes,

            $id

        ]);

        if ($saved) {

            header("Location: index.php?page=appointments");
            exit;

        } else {

            $error = "Unable to update appointment.";

        }

    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/Team5-Clinic/assets/css/appointments.css">
</head>
<body>
    <div class="appointment-form-page">

    <div class="page-header">

        <div>

            <h2>Edit Appointment</h2>

            <p>Update appointment information.</p>

        </div>

        <a href="index.php?page=appointments" class="btn-secondary">

            ← Back

        </a>

    </div>

    <?php if($error): ?>

        <div class="alert alert-danger">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>

    <form method="POST" class="appointment-form">

        <div class="form-card">

            <h3>Appointment Information</h3>

            <div class="form-grid">

                <!-- ================= PATIENT ================= -->

                <div class="form-group">

                    <label>

                        Patient <span>*</span>

                    </label>

                    <select name="patient_id" required>

                        <?php foreach($patients as $patient): ?>

                            <option
                                value="<?= $patient['id'] ?>"
                                <?= $appointment['patient_id'] == $patient['id'] ? 'selected' : '' ?>>

                                <?= htmlspecialchars($patient['name']) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- ================= DOCTOR ================= -->

                <div class="form-group">

                    <label>

                        Doctor <span>*</span>

                    </label>

                    <select
                        id="doctorSelect"
                        name="doctor_id"
                        required>

                        <?php foreach($doctors as $doctor): ?>

                            <option
                                value="<?= $doctor['id'] ?>"
                                data-department="<?= htmlspecialchars($doctor['department_name']) ?>"
                                <?= $appointment['doctor_id'] == $doctor['id'] ? 'selected' : '' ?>>

                                <?= htmlspecialchars($doctor['name']) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- ================= DEPARTMENT ================= -->

                <div class="form-group">

                    <label>

                        Department

                    </label>

                    <input
                        type="text"
                        id="departmentBox"
                        readonly>

                </div>


                <!-- ================= STATUS ================= -->

                <div class="form-group">

                    <label>Status</label>

                    <select name="status">

                        <?php

                        $statuses = [

                            'Pending',

                            'Approved',

                            'Completed',

                            'Cancelled'

                        ];

                        foreach($statuses as $status):

                        ?>

                        <option
                            value="<?= $status ?>"
                            <?= $appointment['status'] == $status ? 'selected' : '' ?>>

                            <?= $status ?>

                        </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- ================= DATE ================= -->

                <div class="form-group">

                    <label>

                        Appointment Date <span>*</span>

                    </label>

                    <input
                        type="date"
                        name="appointment_date"
                        value="<?= htmlspecialchars($appointment['appointment_date']) ?>"
                        required>

                </div>


                <!-- ================= TIME ================= -->

                <div class="form-group">

                    <label>

                        Appointment Time <span>*</span>

                    </label>

                    <input
                        type="time"
                        name="appointment_time"
                        value="<?= htmlspecialchars(substr($appointment['appointment_time'],0,5)) ?>"
                        required>

                </div>
                                <!-- ================= NOTES ================= -->

                <div class="form-group full-width">

                    <label>

                        Notes

                    </label>

                    <textarea
                        name="notes"
                        rows="5"
                        placeholder="Enter appointment notes or reason..."><?= htmlspecialchars($appointment['notes']) ?></textarea>

                </div>

            </div>

            <!-- ================= BUTTONS ================= -->

            <div class="form-actions">

                <button
                    type="submit"
                    class="btn-primary">

                    💾 Update Appointment

                </button>

                <a
                    href="index.php?page=appointments"
                    class="btn-secondary">

                    Cancel

                </a>

            </div>

        </div>

    </form>

</div>

<!-- ================= AUTO DEPARTMENT ================= -->

<script>

const doctorSelect = document.getElementById('doctorSelect');
const departmentBox = document.getElementById('departmentBox');

function updateDepartment(){

    const option = doctorSelect.options[doctorSelect.selectedIndex];

    departmentBox.value = option.dataset.department || '';

}

doctorSelect.addEventListener('change', updateDepartment);

// Show current department when page loads
updateDepartment();

</script>
</body>
</html>
