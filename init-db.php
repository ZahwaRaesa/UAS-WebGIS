<?php
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';
$db   = getenv('DB_NAME') ?: 'uas_06';

echo "Menunggu MySQL di $host siap...\n";
$maxTries = 30;
for ($i = 0; $i < $maxTries; $i++) {
    $conn = @new mysqli($host, $user, $pass);
    if (!$conn->connect_error) {
        break;
    }
    sleep(2);
}

if ($conn->connect_error) {
    die("Gagal koneksi ke database: " . $conn->connect_error . "\n");
}

// Buat database utama jika belum ada (berguna untuk local development)
$conn->query("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db($db);

function importSql($conn, $file) {
    if (!file_exists($file)) return;
    $sql = file_get_contents($file);
    // Hapus CREATE DATABASE dan USE agar menggunakan DB saat ini
    $sql = preg_replace('/CREATE DATABASE IF NOT EXISTS [^\;]+\;/i', '', $sql);
    $sql = preg_replace('/USE [^\;]+\;/i', '', $sql);
    
    if ($conn->multi_query($sql)) {
        do {
            if ($res = $conn->store_result()) {
                $res->free();
            }
        } while ($conn->more_results() && $conn->next_result());
    }
}

// Cek apakah tabel uas_06 sudah ada (menggunakan tabel role sebagai patokan)
$res = $conn->query("SHOW TABLES LIKE 'role'");
if ($res->num_rows == 0) {
    echo "Tabel utama (05) belum ada, menjalankan import...\n";
    importSql($conn, __DIR__ . '/05/database_uas_06.sql');
} else {
    echo "Tabel utama (05) sudah ada, skip import.\n";
}

// Cek tabel SPBU (04)
$db04 = getenv('DB_NAME_04') ?: $db;
$conn->query("CREATE DATABASE IF NOT EXISTS `$db04` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db($db04);
$res = $conn->query("SHOW TABLES LIKE 'spbu'");
if ($res->num_rows == 0) {
    echo "Tabel spbu (04) belum ada, menjalankan import...\n";
    importSql($conn, __DIR__ . '/04/db_spbu.sql');
} else {
    echo "Tabel spbu (04) sudah ada, skip import.\n";
}

$conn->close();
echo "Inisialisasi database selesai.\n";
