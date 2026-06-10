<?php
error_reporting(0);
ini_set("display_errors", 0);
ob_start();
session_start();
include 'koneksi.php';
$aksi = $_REQUEST['aksi'] ?? 'list_bantuan';

if ($aksi === 'list_bantuan') {
    $res=$conn->query("SELECT * FROM bantuan ORDER BY jenis,nama");
    if(!$res) json_out(['error'=>$conn->error],500);
    $rows=[]; while($r=$res->fetch_assoc()) $rows[]=$r;
    json_out($rows);
}

if ($aksi === 'simpan_master') {
    $nm=$conn->real_escape_string($_POST['nama']??'');
    $jn=$conn->real_escape_string($_POST['jenis']??'sembako');
    $sm=$conn->real_escape_string($_POST['sumber']??'');
    $bt=$conn->real_escape_string($_POST['bentuk']??'');
    $conn->query("INSERT INTO bantuan (nama,jenis,sumber,bentuk) VALUES ('$nm','$jn','$sm','$bt')");
    if($conn->error) json_out(['error'=>$conn->error],500);
    json_out(['success'=>true,'id'=>$conn->insert_id]);
}

if ($aksi === 'edit_master') {
    $id=(int)($_POST['id']??0);
    $nm=$conn->real_escape_string($_POST['nama']??'');
    $jn=$conn->real_escape_string($_POST['jenis']??'sembako');
    $sm=$conn->real_escape_string($_POST['sumber']??'');
    $bt=$conn->real_escape_string($_POST['bentuk']??'');
    $conn->query("UPDATE bantuan SET nama='$nm',jenis='$jn',sumber='$sm',bentuk='$bt' WHERE id=$id");
    json_out(['success'=>true]);
}

if ($aksi === 'hapus_master') {
    $id=(int)($_POST['id']??0);
    $conn->query("DELETE FROM histori_bantuan WHERE bantuan_id=$id");
    $conn->query("DELETE FROM bantuan WHERE id=$id");
    json_out(['success'=>true]);
}

if ($aksi === 'histori') {
    $where=['1=1']; $params=[]; $types='';
    if(!empty($_GET['penduduk_id'])){$where[]="hb.penduduk_id=?";$params[]=(int)$_GET['penduduk_id'];$types.='i';}
    if(!empty($_GET['bantuan_id'])){$where[]="hb.bantuan_id=?";$params[]=(int)$_GET['bantuan_id'];$types.='i';}
    if(!empty($_GET['status'])){$where[]="hb.status=?";$params[]=$_GET['status'];$types.='s';}
    if(!empty($_GET['tgl_dari'])){$where[]="hb.tanggal>=?";$params[]=$_GET['tgl_dari'];$types.='s';}
    if(!empty($_GET['tgl_sampai'])){$where[]="hb.tanggal<=?";$params[]=$_GET['tgl_sampai'];$types.='s';}
    $w=implode(' AND ',$where);
    $sql="SELECT hb.*,b.nama AS nama_bantuan,b.jenis,b.sumber,p.nama AS nama_penduduk,p.status_ekonomi,p.nik,u.nama AS nama_pengurus
          FROM histori_bantuan hb
          JOIN bantuan b ON hb.bantuan_id=b.id
          JOIN penduduk p ON hb.penduduk_id=p.id
          LEFT JOIN user u ON hb.disalurkan_oleh=u.id
          WHERE $w ORDER BY hb.tanggal DESC,hb.id DESC";
    if($params){$stmt=$conn->prepare($sql);$stmt->bind_param($types,...$params);$stmt->execute();$res=$stmt->get_result();$stmt->close();}
    else $res=$conn->query($sql);
    $rows=[]; while($r=$res->fetch_assoc()) $rows[]=$r;
    json_out($rows);
}

if ($aksi === 'salurkan') {
    $pid=(int)($_POST['penduduk_id']??0);
    $bid=(int)($_POST['bantuan_id']??0);
    $tgl=$conn->real_escape_string($_POST['tanggal']??date('Y-m-d'));
    $jml=$conn->real_escape_string($_POST['jumlah']??'');
    $ket=$conn->real_escape_string($_POST['keterangan']??'');
    $uid=!empty($_SESSION['user_id'])?(int)$_SESSION['user_id']:'NULL';
    $conn->query("INSERT INTO histori_bantuan (penduduk_id,bantuan_id,tanggal,jumlah,status,disalurkan_oleh,keterangan) VALUES ($pid,$bid,'$tgl','$jml','disalurkan',$uid,'$ket')");
    if($conn->error) json_out(['error'=>$conn->error],500);
    $new_id=$conn->insert_id;
    if($uid!=='NULL'){
        log_aktivitas($conn,$uid,'CREATE','histori_bantuan',$new_id,'Salurkan bantuan ke penduduk id='.$pid);
    }
    json_out(['success'=>true,'id'=>$new_id]);
}

if ($aksi === 'edit_histori') {
    $id=(int)($_POST['id']??0);
    $status=$conn->real_escape_string($_POST['status']??'disalurkan');
    $jml=$conn->real_escape_string($_POST['jumlah']??'');
    $ket=$conn->real_escape_string($_POST['keterangan']??'');
    $conn->query("UPDATE histori_bantuan SET status='$status',jumlah='$jml',keterangan='$ket' WHERE id=$id");
    json_out(['success'=>true]);
}

if ($aksi === 'hapus_histori') {
    $id=(int)($_POST['id']??0);
    $conn->query("DELETE FROM histori_bantuan WHERE id=$id");
    json_out(['success'=>true]);
}

if ($aksi === 'belum_dibantu') {
    $sql="SELECT p.id,p.nik,p.nama,p.status_ekonomi,TIMESTAMPDIFF(YEAR,p.tanggal_lahir,CURDATE()) AS umur,ri.nama AS nama_ibadah,k.kelurahan
          FROM penduduk p
          LEFT JOIN rumah_ibadah ri ON p.rumah_ibadah_id=ri.id
          LEFT JOIN keluarga k ON p.keluarga_id=k.id
          WHERE p.status_hidup='hidup'
            AND p.status_ekonomi IN ('miskin','rentan')
            AND p.id NOT IN (SELECT DISTINCT penduduk_id FROM histori_bantuan WHERE status='disalurkan')
          ORDER BY FIELD(p.status_ekonomi,'miskin','rentan'),p.nama";
    $res=$conn->query($sql); $rows=[];
    while($r=$res->fetch_assoc()) $rows[]=$r;
    json_out($rows);
}

if ($aksi === 'statistik') {
    $sql="SELECT b.id,b.nama,b.jenis,COUNT(hb.id) AS total_salur,SUM(hb.status='disalurkan') AS berhasil
          FROM bantuan b
          LEFT JOIN histori_bantuan hb ON hb.bantuan_id=b.id
          GROUP BY b.id ORDER BY total_salur DESC";
    $res=$conn->query($sql); $rows=[];
    while($r=$res->fetch_assoc()) $rows[]=$r;
    json_out($rows);
}
