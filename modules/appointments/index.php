<?php
require_once __DIR__ . '/../../config/db.php';

// =======================
// FILTERS
// =======================

$search        = trim($_GET['search'] ?? '');
$date          = $_GET['date'] ?? '';
$status        = $_GET['status'] ?? '';
$doctor_id     = $_GET['doctor_id'] ?? '';
$department_id = $_GET['department_id'] ?? '';


// =======================
// STATISTICS
// =======================

$stats = [

    'total' => $pdo->query("
        SELECT COUNT(*)
        FROM appointments
    ")->fetchColumn(),

    'pending' => $pdo->query("
        SELECT COUNT(*)
        FROM appointments
        WHERE status='Pending'
    ")->fetchColumn(),

    'approved' => $pdo->query("
        SELECT COUNT(*)
        FROM appointments
        WHERE status='Approved'
    ")->fetchColumn(),

    'completed' => $pdo->query("
        SELECT COUNT(*)
        FROM appointments
        WHERE status='Completed'
    ")->fetchColumn(),

    'cancelled' => $pdo->query("
        SELECT COUNT(*)
        FROM appointments
        WHERE status='Cancelled'
    ")->fetchColumn()

];


// =======================
// LOAD DOCTORS
// =======================

$doctorStmt = $pdo->query("
SELECT id,name
FROM doctors
ORDER BY name ASC
");

$doctors = $doctorStmt->fetchAll(PDO::FETCH_ASSOC);


// =======================
// LOAD DEPARTMENTS
// =======================

$departmentStmt = $pdo->query("
SELECT id,name
FROM departments
ORDER BY name ASC
");

$departments = $departmentStmt->fetchAll(PDO::FETCH_ASSOC);


// =======================
// APPOINTMENT QUERY
// =======================

$sql = "

SELECT

a.id,

a.patient_id,

a.doctor_id,

a.appointment_date,

a.appointment_time,

a.status,

a.notes,

p.name AS patient_name,

d.name AS doctor_name,

dep.name AS department_name

FROM appointments a

INNER JOIN patients p
ON a.patient_id = p.id

INNER JOIN doctors d
ON a.doctor_id = d.id

INNER JOIN departments dep
ON d.department_id = dep.id

WHERE 1=1

";

$params = [];


// SEARCH

if($search != ''){

    $sql .= "

    AND

    (

        p.name LIKE ?

        OR

        d.name LIKE ?

    )

    ";

    $params[] = "%{$search}%";
    $params[] = "%{$search}%";

}


// DATE

if($date != ''){

    $sql .= " AND a.appointment_date=? ";

    $params[] = $date;

}


// STATUS

if($status != ''){

    $sql .= " AND a.status=? ";

    $params[] = $status;

}


// DOCTOR

if($doctor_id != ''){

    $sql .= " AND d.id=? ";

    $params[] = $doctor_id;

}


// DEPARTMENT

if($department_id != ''){

    $sql .= " AND dep.id=? ";

    $params[] = $department_id;

}


// ORDER

$sql .= "

ORDER BY

a.appointment_date ASC,

a.appointment_time ASC

";


$stmt = $pdo->prepare($sql);

$stmt->execute($params);

$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);


// =======================
// STATUS BADGE
// =======================

function badgeClass($status)
{

    switch($status){

        case 'Approved':
            return 'badge-approved';

        case 'Completed':
            return 'badge-completed';

        case 'Cancelled':
            return 'badge-cancelled';

        default:
            return 'badge-pending';

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
    <div class="appointments-page">

<<<<<<< HEAD
<link rel="stylesheet"
      href="/Team5-Clinic/assets/css/appointments.css">

<div class="page-header">
    <div>
        <h1>📅 Appointments</h1>
        <p><?= $total ?> total appointments</p>
    </div>

    <a href="create.php" class="btn-primary">
        + New Appointment
    </a>
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
            <input type="text"
                   id="searchPatient"
                   placeholder="Search appointments...">
        </div>

        <form method="GET" class="filters-inline">

            <select name="doctor">
                <option value="">All Doctors</option>
                <?php foreach($doctors as $d): ?>
                    <option value="<?= $d['id'] ?>"
                        <?= $doctorFilter==$d['id']?'selected':'' ?>>
                        <?= htmlspecialchars($d['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="status">
                <option value="">All Statuses</option>
                <option value="Pending"
                    <?= $statusFilter=='Pending'?'selected':'' ?>>
                    Pending
                </option>

                <option value="Confirmed"
                    <?= $statusFilter=='Confirmed'?'selected':'' ?>>
                    Confirmed
                </option>

                <option value="Completed"
                    <?= $statusFilter=='Completed'?'selected':'' ?>>
                    Completed
                </option>

                <option value="Cancelled"
                    <?= $statusFilter=='Cancelled'?'selected':'' ?>>
                    Cancelled
                </option>
            </select>

            <button type="submit" class="btn-primary">Filter</button>
        </form>
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

                <td class="appt-id">
                    APT<?= str_pad($a['id'],3,'0',STR_PAD_LEFT) ?>
                </td>

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

                        <a href="update_status.php?id=<?= $a['id'] ?>&status=Confirmed"
                           title="Confirm">✔️</a>

                        <a href="update_status.php?id=<?= $a['id'] ?>&status=Completed"
                           title="Complete">✅</a>

                        <a href="update_status.php?id=<?= $a['id'] ?>&status=Cancelled"
                           title="Cancel">❌</a>

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
=======
    <!-- ================= PAGE HEADER ================= -->
    <div class="page-header">

        <div class="page-title">

            <h2>Appointments</h2>

            <p><?= $stats['total'] ?> total appointments</p>

        </div>

        <div class="page-actions">

            <a href="create.php" class="btn-primary">
                <span>＋</span>
                New Appointment
            </a>

        </div>

    </div>


    <!-- ================= STATISTICS ================= -->

    <div class="stats-grid">

        <a href="index.php?page=appointments&status=Pending"
           class="stat-card">

            <h3><?= $stats['pending'] ?></h3>

            <span class="stat-badge badge-pending">

                Pending

            </span>

        </a>


        <a href="index.php?page=appointments&status=Approved"
           class="stat-card">

            <h3><?= $stats['approved'] ?></h3>

            <span class="stat-badge badge-approved">

                Confirmed

            </span>

        </a>


        <a href="index.php?page=appointments&status=Completed"
           class="stat-card">

            <h3><?= $stats['completed'] ?></h3>

            <span class="stat-badge badge-completed">

                Completed

            </span>

        </a>


        <a href="index.php?page=appointments&status=Cancelled"
           class="stat-card">

            <h3><?= $stats['cancelled'] ?></h3>

            <span class="stat-badge badge-cancelled">

                Cancelled

            </span>

        </a>

    </div>


    <!-- ================= FILTER BAR ================= -->

    <div class="toolbar">

        <form method="GET"
              class="filters">

            <input
                type="hidden"
                name="page"
                value="appointments">


            <!-- Search -->

            <input
                type="text"
                name="search"
                class="search-input"
                placeholder="Search appointments..."
                value="<?= htmlspecialchars($search) ?>">


            <!-- Date -->

            <input
                type="date"
                name="date"
                value="<?= htmlspecialchars($date) ?>">


            <!-- Doctor -->

            <select name="doctor_id">

                <option value="">

                    All Doctors

                </option>

                <?php foreach($doctors as $doctor): ?>

                    <option
                        value="<?= $doctor['id'] ?>"
                        <?= $doctor_id==$doctor['id'] ? 'selected' : '' ?>>

                        <?= htmlspecialchars($doctor['name']) ?>

                    </option>

                <?php endforeach; ?>

            </select>


            <!-- Department -->

            <select name="department_id">

                <option value="">

                    All Departments

                </option>

                <?php foreach($departments as $department): ?>

                    <option
                        value="<?= $department['id'] ?>"
                        <?= $department_id==$department['id'] ? 'selected' : '' ?>>

                        <?= htmlspecialchars($department['name']) ?>

                    </option>

                <?php endforeach; ?>

            </select>


            <!-- Status -->

            <select name="status">

                <option value="">

                    All Statuses

                </option>

                <?php

                $statusList = [

                    'Pending',

                    'Approved',

                    'Completed',

                    'Cancelled'

                ];

                foreach($statusList as $s):

                ?>

                    <option
                        value="<?= $s ?>"
                        <?= $status==$s ? 'selected' : '' ?>>

                        <?= $s ?>

                    </option>

                <?php endforeach; ?>

            </select>


            <button
                type="submit"
                class="btn-filter">

                Search

            </button>


            <?php if(
                $search ||
                $date ||
                $status ||
                $doctor_id ||
                $department_id
            ): ?>

                <a
                    href="index.php?page=appointments"
                    class="btn-clear">

                    Clear

                </a>

            <?php endif; ?>

        </form>

    </div>


    <!-- ================= TABLE CARD ================= -->

    <div class="table-card">

        <table class="appointments-table">

            <thead>

                <tr>

                    <th>ID</th>

                    <th>PATIENT</th>

                    <th>DOCTOR</th>

                    <th>DEPARTMENT</th>

                    <th>DATE</th>

                    <th>TIME</th>

                    <th>REASON</th>

                    <th>STATUS</th>

                    <th width="120">

                        ACTIONS

                    </th>

                </tr>

            </thead>

            <tbody>
<?php if (empty($appointments)): ?>

<tr>

    <td colspan="9" class="empty-state">

        <div class="empty-box">

            <h3>No appointments found</h3>

            <p>No appointment matches your search criteria.</p>

        </div>

    </td>

</tr>

<?php else: ?>

<?php foreach($appointments as $row): ?>

<tr>

    <!-- ID -->
    <td>

        <strong>

            APT<?= str_pad($row['id'],3,'0',STR_PAD_LEFT) ?>

        </strong>

    </td>


    <!-- Patient -->
    <td>

        <div class="patient-info">

            <div class="patient-avatar">

                <?= strtoupper(substr($row['patient_name'],0,1)) ?>

            </div>

            <div>

                <strong>

                    <?= htmlspecialchars($row['patient_name']) ?>

                </strong>

            </div>

        </div>

    </td>


    <!-- Doctor -->
    <td>

        <?= htmlspecialchars($row['doctor_name']) ?>

    </td>


    <!-- Department -->
    <td>

        <?= htmlspecialchars($row['department_name']) ?>

    </td>


    <!-- Date -->
    <td>

        <?= date('Y-m-d',strtotime($row['appointment_date'])) ?>

    </td>


    <!-- Time -->
    <td>

        <?= date('H:i',strtotime($row['appointment_time'])) ?>

    </td>


    <!-- Notes -->
    <td>

        <?php if(!empty($row['notes'])): ?>

            <?= htmlspecialchars($row['notes']) ?>

        <?php else: ?>

            <span class="text-muted">

                -

            </span>

        <?php endif; ?>

    </td>


    <!-- Status -->
    <td>

        <select
            class="status-select <?= badgeClass($row['status']) ?>"
            onchange="changeStatus(<?= $row['id'] ?>, this.value)">

            <?php
            $statuses = ['Pending','Approved','Completed','Cancelled'];
            foreach($statuses as $st):
            ?>

            <option
                value="<?= $st ?>"
                <?= $row['status'] == $st ? 'selected' : '' ?>>

                <?= $st ?>

            </option>

            <?php endforeach; ?>

        </select>

    </td>


    <!-- Actions -->
    <td>

        <div class="action-buttons">

            <!-- Edit -->

            <a
                href="edit.php?id=<?= $row['id'] ?>"
                class="btn-icon"
                title="Edit">

                ✏️

            </a>


           
            <button

                type="button"

                class="btn-icon delete-icon"

                onclick="openDeleteModal(

                    <?= $row['id'] ?>,

                    '<?= htmlspecialchars($row['patient_name'],ENT_QUOTES) ?>'

                )"

                title="Delete">

                🗑️

            </button>

        </div>

    </td>

</tr>

<?php endforeach; ?>

<?php endif; ?>git 

</tbody>

</table>

</div>
>>>>>>> 2127c6620f0140ca0fe0b214361a8e20fe625c7b


<!-- ================= TABLE FOOTER ================= -->

<div class="table-footer">

    <div>

        Showing

        <?= count($appointments) ?>

        appointment(s)

    </div>

</div>

</div>
<!-- ================= DELETE MODAL ================= -->

<div id="deleteModal" class="modal">

    <div class="modal-content">

        <div class="modal-header">

            <h3>Delete Appointment</h3>

        </div>

        <div class="modal-body">

            <p>

                Are you sure you want to delete

                <strong id="patientName"></strong> ?

            </p>

            <p class="text-danger">

                This action cannot be undone.

            </p>

        </div>

        <div class="modal-footer">

            <button
                type="button"
                class="btn-secondary"
                onclick="closeDeleteModal()">

                Cancel

            </button>

            <a
                id="deleteLink"
                href="#"
                class="btn-danger">

                Delete

            </a>

        </div>

    </div>

</div>


<script>

function openDeleteModal(id, name){

    document.getElementById("patientName").textContent = name;

    document.getElementById("deleteLink").href =
    'delete.php?id=' + id;

    document.getElementById("deleteModal").classList.add("show");

    document.body.style.overflow = "hidden";
}


function closeDeleteModal(){

    document.getElementById("deleteModal").classList.remove("show");

    document.body.style.overflow="auto";

}


window.onclick=function(e){

    const modal=document.getElementById("deleteModal");

    if(e.target===modal){

        closeDeleteModal();

    }

}


document.addEventListener("keydown",function(e){

    if(e.key==="Escape"){

        closeDeleteModal();

    }

});

function changeStatus(id, status){

    if(confirm("Change appointment status to " + status + "?")){

        window.location.href =
            "update_status.php?id="
            + id
            + "&status="
            + encodeURIComponent(status);

    }

}

</script>
</body>
</html>
