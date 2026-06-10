<?php
include 'koneksi.php';
$tipe=$_GET['tipe']??'semua';
$data=[];

if ($tipe==='semua'||$tipe==='ibadah') {
    $res=$conn->query("SELECT ri.*,
        (SELECT COUNT(*) FROM penduduk p WHERE p.rumah_ibadah_id=ri.id AND p.status_hidup='hidup') AS total_penduduk,
        (SELECT COUNT(*) FROM penduduk p WHERE p.rumah_ibadah_id=ri.id AND p.status_ekonomi='miskin') AS total_miskin
        FROM rumah_ibadah ri");
    if($res) while($r=$res->fetch_assoc()){$r['tipe']='ibadah';$data[]=$r;}
}

if ($tipe==='semua'||$tipe==='penduduk') {
    $where=["p.lat IS NOT NULL","p.lng IS NOT NULL","p.status_hidup='hidup'"];
    if(!empty($_GET['status_ekonomi'])){$se=$conn->real_escape_string($_GET['status_ekonomi']);$where[]="p.status_ekonomi='$se'";}
    if(!empty($_GET['rumah_ibadah_id'])){$ri_id=(int)$_GET['rumah_ibadah_id'];$where[]="p.rumah_ibadah_id=$ri_id";}
    if(!empty($_GET['status_bantuan'])){
        if($_GET['status_bantuan']==='sudah') $where[]="p.id IN (SELECT DISTINCT penduduk_id FROM histori_bantuan WHERE status='disalurkan')";
        else $where[]="p.id NOT IN (SELECT DISTINCT penduduk_id FROM histori_bantuan WHERE status='disalurkan')";
    }
    $w=implode(' AND ',$where);
    $res=$conn->query("SELECT p.id,p.nik,p.nama,p.jenis_kelamin,
        TIMESTAMPDIFF(YEAR,p.tanggal_lahir,CURDATE()) AS umur,
        p.status_ekonomi,p.pekerjaan,p.penghasilan,p.lat,p.lng,p.rumah_ibadah_id,
        ri.nama AS nama_ibadah,
        (SELECT COUNT(*) FROM histori_bantuan hb WHERE hb.penduduk_id=p.id AND hb.status='disalurkan') AS total_bantuan
        FROM penduduk p
        LEFT JOIN rumah_ibadah ri ON p.rumah_ibadah_id=ri.id
        WHERE $w");
    if($res) while($r=$res->fetch_assoc()){$r['tipe']='penduduk';$r['penghasilan']=(float)$r['penghasilan'];$data[]=$r;}
}

if ($tipe==='semua'||$tipe==='laporan') {
    $res=$conn->query("SELECT id,pelapor,deskripsi,lat,lng,urgensi,status,created_at FROM laporan WHERE lat IS NOT NULL AND lng IS NOT NULL");
    if($res) while($r=$res->fetch_assoc()){$r['tipe']='laporan';$data[]=$r;}
}

json_out($data);
