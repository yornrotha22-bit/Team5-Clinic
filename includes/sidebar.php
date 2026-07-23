<!-- includes/sidebar.php -->
<style>
    :root {
        --sidebar-bg: #0f172a;
        --sidebar-text: #94a3b8;
        --sidebar-active-bg: #2563eb;
        --sidebar-active-text: #ffffff;
        --sidebar-hover-bg: #1e293b;
    }

    .sidebar, .sidebar * {
        box-sizing: border-box;
    }

    .sidebar {
        width: 260px;
        height: 100vh;
        background-color: var(--sidebar-bg);
        display: flex;
        flex-direction: column;
        position: fixed;
        left: 0;
        top: 0;
        z-index: 1000;
        padding: 24px 16px;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 24px;
        margin-bottom: 12px;
        border-bottom: 1px solid #1e293b;
    }

    .brand-icon {
        width: 38px;
        height: 38px;
        background-color: #0284c7;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
    }

    .brand-details {
        display: flex;
        flex-direction: column;
    }

    .brand-title {
        color: #ffffff;
        font-size: 17px;
        font-weight: 700;
        line-height: 1.2;
    }

    .brand-subtitle {
        color: var(--sidebar-text);
        font-size: 12px;
        margin-top: 2px;
    }

    .nav-menu {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 6px;
        flex: 1;
        overflow-y: auto;
        padding: 0;
        margin: 0;
    }

    .nav-item a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        color: var(--sidebar-text);
        text-decoration: none;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .nav-item a:hover {
        background-color: var(--sidebar-hover-bg);
        color: #ffffff;
    }

    .nav-item.active a {
        background-color: var(--sidebar-active-bg);
        color: var(--sidebar-active-text);
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .nav-item svg {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
    }

    .sidebar-footer {
        padding-top: 16px;
        border-top: 1px solid #1e293b;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .avatar-circle {
        width: 38px;
        height: 38px;
        background-color: #3b82f6;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .user-info {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .user-name {
        color: #ffffff;
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-email {
        color: var(--sidebar-text);
        font-size: 11px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .logout-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--sidebar-text);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        padding: 8px 12px;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .logout-btn:hover {
        color: #ef4444;
        background-color: rgba(239, 68, 68, 0.1);
    }
</style>

<?php
$current_uri = $_SERVER['REQUEST_URI'];
$user_name = $_SESSION['user_name'] ?? 'Admin User';
$user_email = $_SESSION['user_email'] ?? 'admin@cliniccare.com';
$avatar_initials = strtoupper(substr($user_name, 0, 2));

// Base URL path របស់ project របស់អ្នក
$base_url = '/PHP+laravel/PHP/Team5-Clinic';
?>

<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
        </div>
        <div class="brand-details">
            <span class="brand-title">ClinicCare</span>
            <span class="brand-subtitle">Team 5 System</span>
        </div>
    </div>

    <ul class="nav-menu">
        <li class="nav-item <?= strpos($current_uri, 'dashboard') !== false ? 'active' : ''; ?>">
            <a href="<?= $base_url ?>/modules/dashboard/index.php">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                Dashboard
            </a>
        </li>

        <li class="nav-item <?= strpos($current_uri, 'patients') !== false ? 'active' : ''; ?>">
            <a href="<?= $base_url ?>/modules/patients/index.php">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                Patients
            </a>
        </li>

        <li class="nav-item <?= strpos($current_uri, 'doctors') !== false ? 'active' : ''; ?>">
            <a href="<?= $base_url ?>/modules/doctors/index.php">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4.8 2.3A.3.3 0 0 0 4.5 2.6V5A1 1 0 0 0 5.5 6h13a1 1 0 0 0 1-1V2.6a.3.3 0 0 0-.3-.3H4.8z"></path><path d="M12 6v14"></path><path d="M8 12h8"></path></svg>
                Doctors
            </a>
        </li>

        <li class="nav-item <?= strpos($current_uri, 'departments') !== false ? 'active' : ''; ?>">
            <a href="<?= $base_url ?>/modules/departments/index.php">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 21h18"></path><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"></path></svg>
                Departments
            </a>
        </li>

        <li class="nav-item <?= strpos($current_uri, 'appointments') !== false ? 'active' : ''; ?>">
            <a href="<?= $base_url ?>/modules/appointments/index.php">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                Appointments
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="avatar-circle"><?= htmlspecialchars($avatar_initials) ?></div>
            <div class="user-info">
                <span class="user-name"><?= htmlspecialchars($user_name) ?></span>
                <span class="user-email"><?= htmlspecialchars($user_email) ?></span>
            </div>
        </div>
        <a href="<?= $base_url ?>/logout.php" class="logout-btn">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            Logout
        </a>
    </div>
</aside>