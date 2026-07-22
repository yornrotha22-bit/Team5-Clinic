<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'ClinicCare'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #f8fafc;
            color: #1e293b;
        }

        /* 🔹 Topbar Header - លាតពេញអេក្រង់ផ្នែកខាងស្តាំពីលើ Sidebar */
        .app-topbar {
            position: fixed;
            top: 0;
            left: 260px; /* ស្មើប្រវែង Sidebar */
            width: calc(100% - 260px); /* លាត 100% នៃលំហខាងស្តាំ */
            height: 70px;
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            z-index: 100;
            box-sizing: border-box;
        }

        .topbar-title h2 {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .topbar-title p {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .icon-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: #64748b;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            transition: background-color 0.2s;
        }

        .icon-btn:hover {
            background-color: #f1f5f9;
            color: #2563eb;
        }

        .user-avatar-badge {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #3b82f6;
            color: #ffffff;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
    </style>
</head>
<body>

<!-- Header Topbar Element -->
<header class="app-topbar">
    <div class="topbar-title">
        <h2><?= $pageTitle ?? 'Dashboard'; ?></h2>
        <?php if (!empty($pageSubtitle)): ?>
            <p><?= $pageSubtitle; ?></p>
        <?php endif; ?>
    </div>

    <div class="topbar-actions">
        <!-- Notification Bell Icon -->
        <button class="icon-btn" title="Notifications">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
        </button>

        <!-- User Profile Avatar -->
        <div class="user-avatar-badge" title="Admin User">
            AD
        </div>
    </div>
</header>