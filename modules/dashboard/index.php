<?php
// modules/dashboard/index.php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../middleware/auth.php';
requireLogin();

// Get statistics
$stats = [
    'total_patients'    => $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn(),
    'total_doctors'     => $pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn(),
    'total_appointments'=> $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn(),
    'total_departments' => $pdo->query("SELECT COUNT(*) FROM departments")->fetchColumn(),
    'pending_appts'     => $pdo->query("SELECT COUNT(*) FROM appointments WHERE status='Pending'")->fetchColumn(),
    'today_appts'       => $pdo->query("SELECT COUNT(*) FROM appointments WHERE appointment_date = CURDATE()")->fetchColumn(),
];

// Today's appointments
$todayAppointments = $pdo->query("
    SELECT a.id, a.appointment_time, a.status,
           p.name AS patient_name, d.name AS doctor_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN doctors d ON a.doctor_id = d.id
    WHERE a.appointment_date = CURDATE()
    ORDER BY a.appointment_time ASC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

// Recent appointments
$recentAppointments = $pdo->query("
    SELECT a.id, a.appointment_date, a.appointment_time, a.status,
           p.name AS patient_name, d.name AS doctor_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN doctors d ON a.doctor_id = d.id
    ORDER BY a.created_at DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Recent patients
$recentPatients = $pdo->query("
    SELECT id, name, phone, created_at
    FROM patients
    ORDER BY created_at DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

$user_role = $_SESSION['role'] ?? 'guest';
$user_name = $_SESSION['username'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Team5 Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Dashboard</h2>
                <p class="text-muted mb-0">Welcome back, <?= htmlspecialchars($user_name) ?>! Here's your clinic overview.</p>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-primary fs-6 px-3 py-2">
                    <i class="bi bi-calendar-check me-1"></i>
                    <?= date('l, F j, Y') ?>
                </span>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm p-3 h-100">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="bi bi-people fs-4 text-primary"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold"><?= $stats['total_patients'] ?></h3>
                            <small class="text-muted">Total Patients</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm p-3 h-100">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="bi bi-person-badge fs-4 text-success"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold"><?= $stats['total_doctors'] ?></h3>
                            <small class="text-muted">Total Doctors</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm p-3 h-100">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                            <i class="bi bi-calendar3 fs-4 text-warning"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold"><?= $stats['total_appointments'] ?></h3>
                            <small class="text-muted">Appointments</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm p-3 h-100">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                            <i class="bi bi-building fs-4 text-info"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold"><?= $stats['total_departments'] ?></h3>
                            <small class="text-muted">Departments</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                            <i class="bi bi-clock-history fs-4 text-danger"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold"><?= $stats['pending_appts'] ?></h3>
                            <small class="text-muted">Pending Appointments</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="bi bi-calendar-check fs-4 text-success"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold"><?= $stats['today_appts'] ?></h3>
                            <small class="text-muted">Today's Appointments</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Appointments & Recent Activity -->
        <div class="row g-4">
            <div class="col-md-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-calendar-day text-primary me-2"></i>
                            Today's Appointments
                        </h5>
                        <a href="../appointments/index.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <?php if (count($todayAppointments) > 0): ?>
                        <div class="table-responsive">
                            <table class="dashboard-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Patient</th>
                                        <th>Doctor</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($todayAppointments as $appt): ?>
                                    <tr>
                                        <td><?= date('h:i A', strtotime($appt['appointment_time'])) ?></td>
                                        <td><?= htmlspecialchars($appt['patient_name']) ?></td>
                                        <td><?= htmlspecialchars($appt['doctor_name']) ?></td>
                                        <td>
                                            <span class="badge <?= strtolower($appt['status']) === 'pending' ? 'bg-warning text-dark' : (strtolower($appt['status']) === 'completed' ? 'bg-success' : 'bg-danger') ?>">
                                                <?= $appt['status'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-check text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2 mb-0">No appointments scheduled for today.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-activity text-success me-2"></i>
                            Recent Activity
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="activity-list px-3 py-2">
                            <?php foreach ($recentAppointments as $ra): ?>
                            <li>
                                <span class="activity-dot <?= strtolower($ra['status']) === 'completed' ? 'green' : (strtolower($ra['status']) === 'pending' ? 'yellow' : 'red') ?>"></span>
                                <div>
                                    <strong><?= htmlspecialchars($ra['patient_name']) ?></strong>
                                    <small class="text-muted d-block">
                                        with <?= htmlspecialchars($ra['doctor_name']) ?> - 
                                        <?= date('M d, h:i A', strtotime($ra['appointment_date'] . ' ' . $ra['appointment_time'])) ?>
                                    </small>
                                </div>
                            </li>
                            <?php endforeach; ?>
                            <?php if (count($recentAppointments) === 0): ?>
                            <li class="text-muted text-center py-3">No recent activity</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- Recent Patients -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-person-plus text-primary me-2"></i>
                            Recent Patients
                        </h5>
                        <a href="../patients/index.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <ul class="activity-list px-3 py-2">
                            <?php foreach ($recentPatients as $rp): ?>
                            <li>
                                <span class="activity-dot blue"></span>
                                <div>
                                    <strong><?= htmlspecialchars($rp['name']) ?></strong>
                                    <small class="text-muted d-block">
                                        <?= htmlspecialchars($rp['phone']) ?> - 
                                        Registered <?= date('M d', strtotime($rp['created_at'])) ?>
                                    </small>
                                </div>
                            </li>
                            <?php endforeach; ?>
                            <?php if (count($recentPatients) === 0): ?>
                            <li class="text-muted text-center py-3">No patients registered yet</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>