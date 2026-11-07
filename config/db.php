<?php
$host = '127.0.0.1';
$db   = 'auth_demo';
$user = 'root';    // mặc định XAMPP user = root
$pass = '';        // mặc định không có password
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
