<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'complaint_system';

// إنشاء الاتصال
$mysqli = new mysqli($host, $username, $password, $database);

// التحقق من الاتصال
if ($mysqli->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $mysqli->connect_error);
}

// تعيين الترميز
$mysqli->set_charset("utf8");
?>