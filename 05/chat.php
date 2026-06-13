<?php
error_reporting(0);
ini_set("display_errors", 0);
ob_start();
session_start();
include 'koneksi.php';
$aksi = $_REQUEST['aksi'] ?? 'inbox';
$uid = (int)($_SESSION['user_id']??0);

if ($aksi === 'inbox') {
    if(!$uid) json_out([]);
    $sql="SELECT p.*,u.nama AS nama_pengirim
          FROM pesan p
          JOIN `user` u ON p.dari_user=u.id
          WHERE (p.ke_user=$uid OR p.ke_user IS NULL)
          ORDER BY p.created_at DESC LIMIT 80";
    $res=$conn->query($sql); $rows=[];
    if($res) while($r=$res->fetch_assoc()) $rows[]=$r;
    // tandai dibaca
    $conn->query("UPDATE pesan SET dibaca=1 WHERE ke_user=$uid AND dibaca=0");
    json_out(array_reverse($rows));
}

if ($aksi === 'history') {
    $ke=(int)($_GET['ke']??0);
    $res=$conn->query("SELECT p.*,u.nama AS nama_pengirim FROM pesan p JOIN `user` u ON p.dari_user=u.id WHERE (p.dari_user=$uid AND p.ke_user=$ke) OR (p.dari_user=$ke AND p.ke_user=$uid) ORDER BY p.created_at ASC LIMIT 100");
    $rows=[]; if($res) while($r=$res->fetch_assoc()) $rows[]=$r;
    json_out($rows);
}

if ($aksi === 'kirim') {
    if(!$uid) json_out(['error'=>'Belum login'],401);
    $ke=!empty($_POST['ke_user'])?(int)$_POST['ke_user']:'NULL';
    $isi=$conn->real_escape_string(trim($_POST['isi']??''));
    if(!$isi) json_out(['error'=>'Pesan kosong'],400);
    $conn->query("INSERT INTO pesan (dari_user,ke_user,isi) VALUES ($uid,$ke,'$isi')");
    if($conn->error) json_out(['error'=>$conn->error],500);
    json_out(['success'=>true,'id'=>$conn->insert_id,'time'=>date('H:i')]);
}

if ($aksi === 'kontak') {
    $res=$conn->query("SELECT u.id,u.nama,r.nama AS role FROM `user` u JOIN `role` r ON u.role_id=r.id WHERE u.aktif=1".($uid?" AND u.id!=$uid":'')." ORDER BY u.nama");
    $rows=[]; if($res) while($r=$res->fetch_assoc()) $rows[]=$r;
    json_out($rows);
}

if ($aksi === 'unread') {
    if(!$uid){ json_out(['count'=>0]); }
    $res=$conn->query("SELECT COUNT(*) c FROM pesan WHERE (ke_user=$uid OR ke_user IS NULL) AND dibaca=0 AND dari_user!=$uid");
    json_out(['count'=>(int)($res->fetch_assoc()['c']??0)]);
}
