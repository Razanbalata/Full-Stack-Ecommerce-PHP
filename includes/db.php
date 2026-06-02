<?php

$host = "localhost";
$username = "root";
$password = 12345;
$database = "ecommerce_db";

$conn = mysqli_connect($host, $username, $password, $database);

// فحص الاتصال
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
//echo "Connected successfully";