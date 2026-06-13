<?php
error_reporting(0);
ini_set("display_errors", 0);
ob_start();
session_start();
include 'koneksi.php';
ob_clean();

$tp=$conn->query("SELECT COUNT(*) c FROM penduduk WHERE status_hidup='hidup'")->fetch_assoc()['c'];
$tm=$conn->query("SELECT COUNT(*) c FROM penduduk WHERE status_ekonomi='miskin' AND status_hidup='hidup'")->fetch_assoc()['c'];
$tr=$conn->query("SELECT COUNT(*) c FROM penduduk WHERE status_ekonomi='rentan' AND status_hidup='hidup'")->fetch_assoc()['c'];
$ta=$conn->query("SELECT COUNT(*) c FROM penduduk WHERE status_ekonomi='mampu' AND status_hidup='hidup'")->fetch_assoc()['c'];
$tk=$conn->query("SELECT COUNT(*) c FROM keluarga")->fetch_assoc()['c'];
$tl=$conn->query("SELECT COUNT(*) c FROM laporan")->fetch_assoc()['c'];
$tld=$conn->query("SELECT COUNT(*) c FROM laporan WHERE urgensi='darurat' AND status='pending'")->fetch_assoc()['c'];
$tlp=$conn->query("SELECT COUNT(*) c FROM laporan WHERE status='pending'")->fetch_assoc()['c'];
$tbs=$conn->query("SELECT COUNT(*) c FROM histori_bantuan WHERE status='disalurkan'")->fetch_assoc()['c'];
$tbd=$conn->query("SELECT COUNT(*) c FROM penduduk WHERE status_ekonomi IN ('miskin','rentan') AND status_hidup='hidup' AND id NOT IN (SELECT DISTINCT penduduk_id FROM histori_bantuan WHERE status='disalurkan')")->fetch_assoc()['c'];
$twi=$conn->query("SELECT COUNT(DISTINCT kelurahan) c FROM keluarga")->fetch_assoc()['c'];
$tri_res=$conn->query("SELECT COUNT(DISTINCT CONCAT(rt,rw,kelurahan)) c FROM keluarga")->fetch_assoc()['c'];

// per-bulan bantuan (6 bln terakhir)
$tren=[]; $res=$conn->query("SELECT DATE_FORMAT(tanggal,'%Y-%m') bln,COUNT(*) jml FROM histori_bantuan WHERE tanggal>=DATE_SUB(CURDATE(),INTERVAL 6 MONTH) GROUP BY bln ORDER BY bln");
if($res) while($r=$res->fetch_assoc()) $tren[]=$r;

// statistik ibadah
$stat_ibadah=[]; $res=$conn->query("SELECT * FROM v_statistik_ibadah");
if($res) while($r=$res->fetch_assoc()) $stat_ibadah[]=$r;

// prioritas
$prioritas=[]; $res=$conn->query("SELECT p.id,p.nik,p.nama,p.status_ekonomi,TIMESTAMPDIFF(YEAR,p.tanggal_lahir,CURDATE()) AS umur,ri.nama AS nama_ibadah,(SELECT COUNT(*) FROM laporan la WHERE la.penduduk_id=p.id AND la.urgensi IN ('tinggi','darurat') AND la.status!='selesai') AS lap_darurat FROM penduduk p LEFT JOIN rumah_ibadah ri ON p.rumah_ibadah_id=ri.id WHERE p.status_hidup='hidup' AND p.status_ekonomi IN ('miskin','rentan') ORDER BY lap_darurat DESC,FIELD(p.status_ekonomi,'miskin','rentan') LIMIT 10");
if($res) while($r=$res->fetch_assoc()) $prioritas[]=$r;

// aktivitas
$aktivitas=[]; $res=$conn->query("SELECT la.*,u.nama AS nama_user FROM log_aktivitas la LEFT JOIN `user` u ON la.user_id=u.id ORDER BY la.created_at DESC LIMIT 15");
if($res) while($r=$res->fetch_assoc()) $aktivitas[]=$r;

json_out([
    'total_penduduk'=>(int)$tp,'total_miskin'=>(int)$tm,'total_rentan'=>(int)$tr,'total_mampu'=>(int)$ta,
    'total_keluarga'=>(int)$tk,'total_laporan'=>(int)$tl,'total_darurat'=>(int)$tld,'laporan_pending'=>(int)$tlp,
    'total_bantuan_salur'=>(int)$tbs,'belum_dibantu'=>(int)$tbd,'total_wilayah'=>(int)$twi,'total_rt_rw'=>(int)$tri_res,
    'tren_bantuan'=>$tren,'stat_ibadah'=>$stat_ibadah,'prioritas'=>$prioritas,'aktivitas'=>$aktivitas,
]);
