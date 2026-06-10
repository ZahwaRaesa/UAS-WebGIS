<?php
error_reporting(0);
ini_set("display_errors", 0);
ob_start();
session_start();
include 'koneksi.php';
$aksi = $_REQUEST['aksi'] ?? 'list';

if ($aksi === 'list') {
    $where=['1=1'];
    if(!empty($_GET['status'])){$where[]="l.status='".$conn->real_escape_string($_GET['status'])."'";}
    if(!empty($_GET['urgensi'])){$where[]="l.urgensi='".$conn->real_escape_string($_GET['urgensi'])."'";}
    if(!empty($_GET['q'])){
        $q=$conn->real_escape_string($_GET['q']);
        $where[]="(l.pelapor LIKE '%$q%' OR l.deskripsi LIKE '%$q%')";
    }
    $w=implode(' AND ',$where);
    $sql="SELECT l.*,p.nama AS nama_penduduk,p.status_ekonomi,p.nik,u.nama AS nama_verifikator
          FROM laporan l
          LEFT JOIN penduduk p ON l.penduduk_id=p.id
          LEFT JOIN user u ON l.diverifikasi_oleh=u.id
          WHERE $w
          ORDER BY FIELD(l.urgensi,'darurat','tinggi','sedang','rendah'),l.created_at DESC";
    $res=$conn->query($sql);
    if(!$res) json_out(['error'=>$conn->error],500);
    $rows=[]; while($r=$res->fetch_assoc()) $rows[]=$r;
    json_out($rows);
}

if ($aksi === 'detail') {
    $id=(int)($_GET['id']??0);
    $res=$conn->query("SELECT l.*,p.nama AS nama_penduduk,u.nama AS nama_verifikator FROM laporan l LEFT JOIN penduduk p ON l.penduduk_id=p.id LEFT JOIN user u ON l.diverifikasi_oleh=u.id WHERE l.id=$id LIMIT 1");
    json_out($res->fetch_assoc()??['error'=>'Tidak ditemukan']);
}

if ($aksi === 'kirim') {
    $pel=$conn->real_escape_string(trim($_POST['pelapor']??'Anonim'));
    if(empty($pel)) $pel='Anonim';
    $desk=$conn->real_escape_string(trim($_POST['deskripsi']??''));
    if(!$desk) json_out(['error'=>'Deskripsi wajib'],400);
    $lat=!empty($_POST['lat'])?(float)$_POST['lat']:'NULL';
    $lng=!empty($_POST['lng'])?(float)$_POST['lng']:'NULL';
    $urg=$conn->real_escape_string($_POST['urgensi']??'sedang');
    $pid=!empty($_POST['penduduk_id'])?(int)$_POST['penduduk_id']:'NULL';
    $foto='NULL';
    if(!empty($_FILES['foto']['name'])){
        $ext=pathinfo($_FILES['foto']['name'],PATHINFO_EXTENSION);
        $fname='laporan_'.time().'.'.$ext;
        $uploadDir=__DIR__.'/uploads/bukti/';
        if(!is_dir($uploadDir)) mkdir($uploadDir,0777,true);
        move_uploaded_file($_FILES['foto']['tmp_name'],$uploadDir.$fname);
        $foto="'".$fname."'";
    }
    $conn->query("INSERT INTO laporan (pelapor,penduduk_id,deskripsi,lat,lng,foto,urgensi) VALUES ('$pel',$pid,'$desk',$lat,$lng,$foto,'$urg')");
    if($conn->error) json_out(['error'=>$conn->error],500);
    if(!empty($_SESSION['user_id'])){
        log_aktivitas($conn,(int)$_SESSION['user_id'],'CREATE','laporan',$conn->insert_id,'Kirim laporan oleh '.$pel);
    }
    json_out(['success'=>true,'id'=>$conn->insert_id]);
}

if ($aksi === 'edit') {
    $id=(int)($_POST['id']??0);
    $desk=$conn->real_escape_string($_POST['deskripsi']??'');
    $urg=$conn->real_escape_string($_POST['urgensi']??'sedang');
    $pel=$conn->real_escape_string($_POST['pelapor']??'');
    $status=$conn->real_escape_string($_POST['status']??'pending');
    $uid=!empty($_SESSION['user_id'])?(int)$_SESSION['user_id']:'NULL';
    $conn->query("UPDATE laporan SET pelapor='$pel',deskripsi='$desk',urgensi='$urg',status='$status',diverifikasi_oleh=$uid WHERE id=$id");
    json_out(['success'=>true]);
}

if ($aksi === 'update_status') {
    $id=(int)($_POST['id']??0);
    $status=$conn->real_escape_string($_POST['status']??'pending');
    $uid=!empty($_SESSION['user_id'])?(int)$_SESSION['user_id']:'NULL';
    $conn->query("UPDATE laporan SET status='$status',diverifikasi_oleh=$uid WHERE id=$id");
    json_out(['success'=>true]);
}

if ($aksi === 'hapus') {
    $id=(int)($_POST['id']??0);
    $conn->query("DELETE FROM laporan WHERE id=$id");
    json_out(['success'=>true]);
}
