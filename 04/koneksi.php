<?php
$host = getenv('DB_HOST') ?: 'db';
$port = getenv('DB_PORT') ?: 3306;
$user = getenv('DB_USERNAME') ?: 'webgis_user';
$pass = getenv('DB_PASSWORD') ?: 'webgis_password';
$db   = getenv('DB_DATABASE') ?: 'webgis_spbu';

$conn = @mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
