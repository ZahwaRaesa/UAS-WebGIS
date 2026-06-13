<?php
error_reporting(0);
ini_set("display_errors", 0);
ob_start();
session_start();
include 'koneksi.php';
$aksi = $_REQUEST['aksi'] ?? 'list';

if ($aksi === 'list') {
    $res=$conn->query("SELECT u.id,u.nama,u.email,u.aktif,u.created_at,r.nama AS role_nama,r.id AS role_id FROM `user` u JOIN `role` r ON u.role_id=r.id ORDER BY u.nama");
    if(!$res) json_out(['error'=>$conn->error],500);
    $rows=[]; while($r=$res->fetch_assoc()) $rows[]=$r;
    json_out($rows);
}

if ($aksi === 'simpan') {
    $nm=$conn->real_escape_string($_POST['nama']??'');
    $em=$conn->real_escape_string($_POST['email']??'');
    $pw=password_hash($_POST['password']??'password',PASSWORD_DEFAULT);
    $rid=(int)($_POST['role_id']??1);
    $cek=$conn->query("SELECT id FROM `user` WHERE email='$em'")->num_rows;
    if($cek>0) json_out(['error'=>'Email sudah digunakan'],400);
    $conn->query("INSERT INTO `user` (nama,email,password,role_id) VALUES ('$nm','$em','$pw',$rid)");
    if($conn->error) json_out(['error'=>$conn->error],500);
    json_out(['success'=>true,'id'=>$conn->insert_id]);
}

if ($aksi === 'edit') {
    $id=(int)($_POST['id']??0);
    $nm=$conn->real_escape_string($_POST['nama']??'');
    $em=$conn->real_escape_string($_POST['email']??'');
    $rid=(int)($_POST['role_id']??1);
    $akt=(int)($_POST['aktif']??1);
    $conn->query("UPDATE `user` SET nama='$nm',email='$em',role_id=$rid,aktif=$akt WHERE id=$id");
    if(!empty($_POST['password'])){
        $pw=password_hash($_POST['password'],PASSWORD_DEFAULT);
        $conn->query("UPDATE `user` SET password='$pw' WHERE id=$id");
    }
    json_out(['success'=>true]);
}

if ($aksi === 'hapus') {
    $id=(int)($_POST['id']??0);
    $conn->query("DELETE FROM `user` WHERE id=$id");
    json_out(['success'=>true]);
}

if ($aksi === 'roles') {
    $res=$conn->query("SELECT * FROM `role`"); $rows=[];
    while($r=$res->fetch_assoc()) $rows[]=$r;
    json_out($rows);
}
