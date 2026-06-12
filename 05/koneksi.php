<?php
// ============================================================
//  koneksi.php  –  Database uas_06
// ============================================================
$host = getenv('DB_HOST') ?: 'db';
$port = getenv('DB_PORT') ?: 3306;
$user = getenv('DB_USERNAME') ?: 'webgis_user';
$pass = getenv('DB_PASSWORD') ?: 'webgis_password';
$db   = getenv('DB_DATABASE') ?: 'webgis_spbu';

$conn = @mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    http_response_code(500);
    header('Content-Type: application/json');
    die(json_encode(['error' => 'Koneksi database gagal: ' . mysqli_connect_error()]));
}

$conn->set_charset('utf8mb4');

function log_aktivitas($conn, $user_id, $aksi, $tabel, $data_id = null, $ket = '') {
    $ip   = $_SERVER['REMOTE_ADDR'] ?? '';
    $stmt = $conn->prepare("INSERT INTO log_aktivitas (user_id,aksi,tabel,data_id,keterangan,ip_address) VALUES (?,?,?,?,?,?)");
    if($stmt){
        $stmt->bind_param('ississ', $user_id, $aksi, $tabel, $data_id, $ket, $ip);
        $stmt->execute();
        $stmt->close();
    }
}

function json_out($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function requireLogin() {
    session_start();
    if (empty($_SESSION['user_id'])) {
        json_out(['error' => 'Belum login', 'redirect' => 'login.php'], 401);
    }
    return $_SESSION['user'];
}
