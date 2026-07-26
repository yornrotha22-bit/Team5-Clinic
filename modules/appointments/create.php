<?php
require_once __DIR__ . '/../../config/db.php';

$error = '';
$success = '';

/* ==========================
   LOAD PATIENTS
========================== */

$patientStmt = $pdo->query("
    SELECT id, name
    FROM patients
    ORDER BY name ASC
");

$patients = $patientStmt->fetchAll(PDO::FETCH_ASSOC);


/* ==========================
   LOAD DOCTORS
========================== */

$doctorStmt = $pdo->query("
    SELECT
        d.id,
        d.name,
        dep.name AS department_name
    FROM doctors d
    INNER JOIN departments dep
        ON d.department_id = dep.id
    ORDER BY d.name ASC
");

$doctors = $doctorStmt->fetchAll(PDO::FETCH_ASSOC);


/* ==========================
   SAVE APPOINTMENT
========================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $patient_id = intval($_POST['patient_id'] ?? 0);
    $doctor_id  = intval($_POST['doctor_id'] ?? 0);

    $appointment_date = trim($_POST['appointment_date'] ?? '');
    $appointment_time = trim($_POST['appointment_time'] ?? '');

    $status = trim($_POST['status'] ?? 'Pending');
    $notes  = trim($_POST['notes'] ?? '');

    // Validation
    if (
        $patient_id <= 0 ||
        $doctor_id <= 0 ||
        empty($appointment_date) ||
        empty($appointment_time)
    ) {

        $error = "Please fill in all required fields.";

    } else {

        $sql = "
            INSERT INTO appointments
            (
                patient_id,
                doctor_id,
                appointment_date,
                appointment_time,
                status,
                notes
            )
            VALUES
            (
                ?, ?, ?, ?, ?, ?
            )
        ";

        $stmt = $pdo->prepare($sql);

        $saved = $stmt->execute([

            $patient_id,
            $doctor_id,
            $appointment_date,
            $appointment_time,
            $status,
            $notes

        ]);

        if ($saved) {

            header("Location: index.php?page=appointments");
            exit;

        } else {

            $error = "Unable to save appointment.";

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
            <h2>New Appointment</h2>
            <p>Create a new appointment for a patient.</p>
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

                        Patient
                        <span>*</span>

                    </label>

                    <select
                        name="patient_id"
                        required>

                        <option value="">

                            Select Patient

                        </option>

                        <?php foreach($patients as $patient): ?>

                            <option
                                value="<?= $patient['id'] ?>">

                                <?= htmlspecialchars($patient['name']) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- ================= DOCTOR ================= -->

                <div class="form-group">

                    <label>

                        Doctor
                        <span>*</span>

                    </label>

                    <select
                        id="doctorSelect"
                        name="doctor_id"
                        required>

                        <option value="">

                            Select Doctor

                        </option>

                        <?php foreach($doctors as $doctor): ?>

                            <option
                                value="<?= $doctor['id'] ?>"
                                data-department="<?= htmlspecialchars($doctor['department_name']) ?>">

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
                        readonly
                        placeholder="Automatically selected">

                </div>


                <!-- ================= STATUS ================= -->

                <div class="form-group">

                    <label>

                        Status

                    </label>

                    <select name="status">

                        <option value="Pending">

                            Pending

                        </option>

                        <option value="Approved">

                            Approved

                        </option>

                        <option value="Completed">

                            Completed

                        </option>

                        <option value="Cancelled">

                            Cancelled

                        </option>

                    </select>

                </div>


                <!-- ================= DATE ================= -->

                <div class="form-group">

                    <label>

                        Appointment Date
                        <span>*</span>

                    </label>

                    <input
                        type="date"
                        name="appointment_date"
                        required>

                </div>


                <!-- ================= TIME ================= -->

                <div class="form-group">

                    <label>

                        Appointment Time
                        <span>*</span>

                    </label>

                    <input
                        type="time"
                        name="appointment_time"
                        required>

                </div>

            </div>
                            <!-- ================= NOTES ================= -->

                <div class="form-group full-width">

                    <label>

                        Notes

                    </label>

                    <textarea
                        name="notes"
                        rows="5"
                        placeholder="Enter appointment notes or reason..."></textarea>

                </div>

            </div>

            <!-- ================= BUTTONS ================= -->

            <div class="form-actions">

                <button
                    type="submit"
                    class="btn-primary">

                    💾 Save Appointment

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

doctorSelect.addEventListener('change', function(){

    const option = this.options[this.selectedIndex];

    departmentBox.value = option.dataset.department || '';

});

</script>
</body>
</html>
