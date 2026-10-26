<?php
$pageTitle = $pageTitle ?? 'Online Viva Examination System';
$bodyClass = $bodyClass ?? '';
$role = $_SESSION['role'] ?? '';
$flashMessage = take_flash();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#101b32">
    <title><?= h($pageTitle) ?> | Online Viva</title>
    <link rel="stylesheet" href="style.css">
    <script src="app.js" defer></script>
</head>
<body class="<?= h($bodyClass) ?>">
<header class="site-header">
    <a class="brand" href="index.php" aria-label="Online Viva home">
        <span class="brand-mark">OV</span>
        <span>Online Viva<span class="brand-subtitle">Examination System</span></span>
    </a>
    <nav class="nav-links" aria-label="Main navigation">
        <?php if ($role === 'teacher'): ?>
            <a href="teacher_dashboard.php">Dashboard</a>
            <form class="inline-form" action="logout.php" method="post">
                <?= csrf_input() ?>
                <button class="nav-logout" type="submit">Log out</button>
            </form>
        <?php elseif ($role === 'student'): ?>
            <span class="nav-user">Student: <?= h($_SESSION['student_name'] ?? '') ?></span>
            <form class="inline-form" action="logout.php" method="post">
                <?= csrf_input() ?>
                <button class="nav-logout" type="submit">Log out</button>
            </form>
        <?php else: ?>
            <a href="teacher_login.php">Teacher</a>
            <a href="student_login.php">Student</a>
        <?php endif; ?>
    </nav>
</header>
<main class="page-shell">
    <?php if ($flashMessage !== null): ?>
        <div class="notice notice-info" role="status"><?= h($flashMessage) ?></div>
    <?php endif; ?>
