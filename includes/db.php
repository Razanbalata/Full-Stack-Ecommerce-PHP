<?php

// دالة برمجية بسيطة لقراءة ملف الـ .env وتحميل المتغيرات إلى البيئة وسيرفر PHP
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // تخطي التعليقات
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

$host = "localhost";
$username = "root";
$password = $_ENV['DB_PASS'] ?? '';
$database = "ecommerce_db";

$conn = mysqli_connect($host, $username, $password, $database);

// فحص الاتصال
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
//echo "Connected successfully";