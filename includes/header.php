<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Management System</title>
    
    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
    <div class="container-fluid">
        <!-- Brand -->
        <a class="navbar-brand fw-bold d-flex align-items-center" href="../dashboard/index.php">
            <i class="bi bi-hospital me-2"></i>
            MediCare
        </a>

        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <!-- Search -->
                <li class="nav-item me-3">
                    <form class="d-flex">
                        <div class="input-group input-group-sm" style="width: 280px;">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" placeholder="Search patients, doctors...">
                        </div>
                    </form>
                </li>

                <!-- Notifications -->
                <li class="nav-item dropdown me-3">
                    <a class="nav-link position-relative" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">New appointment at 2:30 PM</a></li>
                        <li><a class="dropdown-item" href="#">Dr. Smith requested leave</a></li>
                    </ul>
                </li>

                <!-- User -->
                <li class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                        <img src="../assets/images/avatar.jpg" alt="User" class="rounded-circle me-2" width="32" height="32">
                        <div>
                            <span class="fw-medium">Dr. Admin</span><br>
                            <small class="text-light opacity-75">Administrator</small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>