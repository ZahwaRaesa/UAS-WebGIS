<?php
session_start();
include 'koneksi.php';
$aksi = $_REQUEST['aksi'] ?? 'cek';

if ($aksi === 'cek') {
    if (!empty($_SESSION['user_id'])) json_out(['login'=>true,'user'=>$_SESSION['user']]);
    else json_out(['login'=>false]);
}

if ($aksi === 'login') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$email || !$password) json_out(['error'=>'Email dan password wajib diisi'],400);
    $stmt = $conn->prepare("SELECT u.*,r.nama AS role_nama FROM `user` u JOIN `role` r ON u.role_id=r.id WHERE u.email=? AND u.aktif=1 LIMIT 1");
    if (!$stmt) {
        json_out(['error' => 'Database error: ' . $conn->error], 500);
    }
    $stmt->bind_param('s',$email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$user || !password_verify($password,$user['password'])) json_out(['error'=>'Email atau password salah'],401);
    unset($user['password']);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user']    = $user;
    log_aktivitas($conn,$user['id'],'LOGIN','user',$user['id'],'Login berhasil');
    json_out(['login'=>true,'user'=>$user]);
}

if ($aksi === 'logout') {
    session_destroy();
    json_out(['logout'=>true]);
}
