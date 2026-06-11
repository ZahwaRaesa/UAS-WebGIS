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

// Buat database utama jika belum ada (berguna untuk local development, tapi mungkin gagal di production jika tidak ada akses)
if (!$conn->query("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
    echo "Warning: Gagal membuat database $db (mungkin sudah ada atau tidak ada akses). Error: " . $conn->error . "\n";
}

if (!$conn->select_db($db)) {
    die("Fatal Error: Gagal memilih database $db. Error: " . $conn->error . "\n");
}

function importSql($conn, $file) {
    if (!file_exists($file)) return;
    $sql = file_get_contents($file);
    // Hapus baris CREATE DATABASE dan USE agar aman
    $sql = preg_replace('/CREATE DATABASE IF NOT EXISTS [^\;]+\;/i', '', $sql);
    $sql = preg_replace('/USE [^\;]+\;/i', '', $sql);
    
    if ($conn->multi_query($sql)) {
        do {
            if ($res = $conn->store_result()) {
                $res->free();
            }
        } while ($conn->more_results() && $conn->next_result());
    } else {
        echo "Error saat import file $file: " . $conn->error . "\n";
    }
}

// Cek apakah tabel uas_06 sudah ada (menggunakan tabel role sebagai patokan)
$res = $conn->query("SHOW TABLES LIKE 'role'");
if ($res) {
    if ($res->num_rows == 0) {
        echo "Tabel utama (05) belum ada, menjalankan import...\n";
        importSql($conn, __DIR__ . '/05/database_uas_06.sql');
    } else {
        echo "Tabel utama (05) sudah ada, skip import.\n";
    }
} else {
    echo "Query gagal saat cek tabel role: " . $conn->error . "\n";
}

// Cek tabel SPBU (04)
$db04 = getenv('DB_NAME_04') ?: $db;
if (!$conn->query("CREATE DATABASE IF NOT EXISTS `$db04` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
    echo "Warning: Gagal membuat database $db04. Error: " . $conn->error . "\n";
}
if (!$conn->select_db($db04)) {
    echo "Fatal Error: Gagal memilih database $db04. Error: " . $conn->error . "\n";
} else {
    $res = $conn->query("SHOW TABLES LIKE 'spbu'");
    if ($res) {
        if ($res->num_rows == 0) {
            echo "Tabel spbu (04) belum ada, menjalankan import...\n";
            importSql($conn, __DIR__ . '/04/db_spbu.sql');
        } else {
            echo "Tabel spbu (04) sudah ada, skip import.\n";
        }
    } else {
        echo "Query gagal saat cek tabel spbu: " . $conn->error . "\n";
    }
}

$conn->close();
echo "Inisialisasi database selesai.\n";
