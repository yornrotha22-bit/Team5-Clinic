<?php
// middleware/auth.php
// Session-based auth guard. Include this at the top of any page that requires login.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Path to the login page, relative to the FILE THAT INCLUDES THIS MIDDLEWARE.
 * Adjust per-page if needed, e.g. in modules/patients/dashboard.php the login
 * page is two levels up: '../../auth/login.php'
 */
define('LOGIN_REDIRECT_DEFAULT', '/S_ETEC/PHP + LARAVEL/PHP/TEAM-PROJECT/Team5-Clinic/auth/login.php');

/**
 * Require that a user is logged in. If not, redirect to the login page.
 */
function requireLogin(): void
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . LOGIN_REDIRECT_DEFAULT);
        exit;
    }
}

/**
 * Require that the logged-in user has one of the given roles.
 * Call requireLogin() first (or use requireRole() which does it for you).
 *
 * Usage:
 *   requireRole('admin');
 *   requireRole(['admin', 'doctor']);
 */
function requireRole($roles): void
{
    requireLogin();

    $allowed = is_array($roles) ? $roles : [$roles];

    if (!in_array($_SESSION['role'], $allowed, true)) {
        http_response_code(403);
        echo "<h2>403 - Access Denied</h2><p>You don't have permission to view this page.</p>";
        exit;
    }
}

/**
 * Helper: get the current logged-in user's data from the session.
 * Returns null if not logged in.
 */
function currentUser(): ?array
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    return [
        'id'       => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? null,
        'email'    => $_SESSION['email'] ?? null,
        'role'     => $_SESSION['role'] ?? null,
    ];
}

/**
 * Helper: check if the current user is logged in, without redirecting.
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}