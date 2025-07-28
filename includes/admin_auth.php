<?php
session_start();

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function redirectIfAdminNotLoggedIn($redirect = '../admin/login.php') {
    if (!isAdminLoggedIn()) {
        header("Location: $redirect");
        exit();
    }
}

function redirectIfAdminLoggedIn($redirect = '../admin/dashboard.php') {
    if (isAdminLoggedIn()) {
        header("Location: $redirect");
        exit();
    }
}

function getCurrentAdmin($mysqli) {
    if (isAdminLoggedIn()) {
        $admin_id = $_SESSION['admin_id'];
        $stmt = $mysqli->prepare("SELECT * FROM admin_users WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        }
    }
    return null;
}
?>