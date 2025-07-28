<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirectIfNotLoggedIn($redirect = 'index.php') {
    if (!isLoggedIn()) {
        header("Location: $redirect");
        exit();
    }
}

function redirectIfLoggedIn($redirect = 'student_dashboard.php') {
    if (isLoggedIn()) {
        header("Location: $redirect");
        exit();
    }
}

function getCurrentUser($mysqli) {
    if (isLoggedIn()) {
        $user_id = $_SESSION['user_id'];
        $stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    return null;
}
?>