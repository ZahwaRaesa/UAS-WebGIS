<?php
$host = getenv('DB_HOST') ?: 'db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: 'rootpassword';
$db   = 'db_spbu';

$conn = @mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Database gagal konek"]);
    exit;
}

$koneksi = $conn;
