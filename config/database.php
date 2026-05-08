<?php
$host = getenv('DB_HOST') ?: 'mysql-spk';
$dbname = getenv('DB_NAME') ?: 'spk_inventaris';
$user = getenv('DB_USER') ?: 'spk_user';
$pass = getenv('DB_PASSWORD') ?: 'spk_password';
$port = getenv('DB_PORT') ?: '3306';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>