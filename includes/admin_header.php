<?php
require_once '../config/database.php';
require_once '../includes/admin_auth.php';

redirectIfAdminNotLoggedIn();

$admin = getCurrentAdmin($mysqli);
if (!$admin) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة الإدارة - نظام الشكاوى</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">إدارة الشكاوى</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">مرحباً، <?= htmlspecialchars($admin['full_name']) ?></span>
                <a class="nav-link" href="logout.php">خروج</a>
            </div>
        </div>
    </nav>
    <div class="container mt-4">