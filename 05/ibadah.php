<?php
error_reporting(0);
ini_set("display_errors", 0);
ob_start();
session_start();
include 'koneksi.php';
$aksi = $_REQUEST['aksi'] ?? 'list';

if ($aksi === 'list') {
    $res=$conn->query("SELECT ri.*,
        (SELECT COUNT(*) FROM penduduk p WHERE p.rumah_ibadah_id=ri.id AND p.status_hidup='hidup') AS total_penduduk,
        (SELECT COUNT(*) FROM penduduk p WHERE p.rumah_ibadah_id=ri.id AND p.status_ekonomi='miskin' AND p.status_hidup='hidup') AS total_miskin
        FROM rumah_ibadah ri ORDER BY ri.nama");
    if(!$res) json_out(['error'=>$conn->error],500);
    $rows=[]; while($r=$res->fetch_assoc()) $rows[]=$r;
    json_out($rows);
}

if ($aksi === 'simpan') {
    $nm=$conn->real_escape_string($_POST['nama']??'');
    $jn=$conn->real_escape_string($_POST['jenis']??'masjid');
    $kt=$conn->real_escape_string($_POST['kontak']??'');
    $al=$conn->real_escape_string($_POST['alamat']??'');
    $lat=(float)($_POST['lat']??0);
    $lng=(float)($_POST['lng']??0);
    $rad=(float)($_POST['radius']??500);
    $conn->query("INSERT INTO rumah_ibadah (nama,jenis,kontak,alamat,lat,lng,radius) VALUES ('$nm','$jn','$kt','$al',$lat,$lng,$rad)");
    if($conn->error) json_out(['error'=>$conn->error],500);
    json_out(['success'=>true,'id'=>$conn->insert_id]);
}

if ($aksi === 'edit') {
    $id=(int)($_POST['id']??0);
    $nm=$conn->real_escape_string($_POST['nama']??'');
    $jn=$conn->real_escape_string($_POST['jenis']??'masjid');
    $kt=$conn->real_escape_string($_POST['kontak']??'');
    $al=$conn->real_escape_string($_POST['alamat']??'');
    $rad=(float)($_POST['radius']??500);
    $conn->query("UPDATE rumah_ibadah SET nama='$nm',jenis='$jn',kontak='$kt',alamat='$al',radius=$rad WHERE id=$id");
    if($conn->error) json_out(['error'=>$conn->error],500);
    json_out(['success'=>true]);
}

if ($aksi === 'hapus') {
    $id=(int)($_POST['id']??0);
    $conn->query("UPDATE penduduk SET rumah_ibadah_id=NULL WHERE rumah_ibadah_id=$id");
    $conn->query("DELETE FROM rumah_ibadah WHERE id=$id");
    json_out(['success'=>true]);
}
