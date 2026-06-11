<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();
session_start();
include 'koneksi.php';
ob_clean(); // buang output apapun sebelum json_out
$aksi = $_REQUEST['aksi'] ?? 'list';

// ── LIST ─────────────────────────────────────────────────────
if ($aksi === 'list') {
    $where = ["p.status_hidup='hidup'"];
    $params = []; $types = '';
    if (!empty($_GET['status_ekonomi'])) { $where[] = "p.status_ekonomi=?"; $params[] = $_GET['status_ekonomi']; $types .= 's'; }
    if (!empty($_GET['rumah_ibadah_id'])) { $where[] = "p.rumah_ibadah_id=?"; $params[] = (int)$_GET['rumah_ibadah_id']; $types .= 'i'; }
    if (!empty($_GET['status_bantuan'])) {
        if ($_GET['status_bantuan'] === 'sudah') $where[] = "p.id IN (SELECT DISTINCT penduduk_id FROM histori_bantuan WHERE status='disalurkan')";
        else $where[] = "p.id NOT IN (SELECT DISTINCT penduduk_id FROM histori_bantuan WHERE status='disalurkan')";
    }
    if (!empty($_GET['kelurahan'])) { $where[] = "k.kelurahan LIKE ?"; $params[] = '%'.$_GET['kelurahan'].'%'; $types .= 's'; }
    if (!empty($_GET['penghasilan_min'])) { $where[] = "p.penghasilan>=?"; $params[] = (float)$_GET['penghasilan_min']; $types .= 'd'; }
    if (!empty($_GET['penghasilan_max'])) { $where[] = "p.penghasilan<=?"; $params[] = (float)$_GET['penghasilan_max']; $types .= 'd'; }
    if (!empty($_GET['q'])) { $like = '%'.$_GET['q'].'%'; $where[] = "(p.nama LIKE ? OR p.nik LIKE ?)"; $params[] = $like; $params[] = $like; $types .= 'ss'; }

    $w = implode(' AND ', $where);
    $sql = "SELECT p.id,p.nik,p.nama,p.jenis_kelamin,p.tanggal_lahir,
        TIMESTAMPDIFF(YEAR,p.tanggal_lahir,CURDATE()) AS umur,
        p.status_keluarga,p.status_ekonomi,p.status_hidup,
        p.pekerjaan,p.penghasilan,p.pendidikan_terakhir,p.status_pendidikan,
        p.lat,p.lng,p.no_hp,p.jenis_bantuan,p.created_at,
        k.no_kk,k.rt,k.rw,k.kelurahan,k.kecamatan,
        ri.nama AS nama_ibadah,
        (SELECT COUNT(*) FROM histori_bantuan hb WHERE hb.penduduk_id=p.id AND hb.status='disalurkan') AS total_bantuan
        FROM penduduk p
        LEFT JOIN keluarga k ON p.keluarga_id=k.id
        LEFT JOIN rumah_ibadah ri ON p.rumah_ibadah_id=ri.id
        WHERE $w ORDER BY p.nama";
    if ($params) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types,...$params);
        $stmt->execute(); $res = $stmt->get_result(); $stmt->close();
    } else {
        $res = $conn->query($sql);
    }
    if(!$res) json_out(['error'=>$conn->error],500);
    $rows = [];
    while ($r = $res->fetch_assoc()) { $r['penghasilan']=(float)$r['penghasilan']; $rows[]=$r; }
    json_out($rows);
}

// ── DETAIL ─────────────────────────────────────────────
if ($aksi === 'detail') {
    $id = (int)($_GET['id']??0);
    $stmt = $conn->prepare("SELECT p.*,TIMESTAMPDIFF(YEAR,p.tanggal_lahir,CURDATE()) AS umur,k.no_kk,k.rt,k.rw,k.kelurahan,k.kecamatan,ri.nama AS nama_ibadah,ri.jenis AS jenis_ibadah FROM penduduk p LEFT JOIN keluarga k ON p.keluarga_id=k.id LEFT JOIN rumah_ibadah ri ON p.rumah_ibadah_id=ri.id WHERE p.id=?");
    $stmt->bind_param('i',$id); $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc(); $stmt->close();
    if (!$data) json_out(['error'=>'Tidak ditemukan'],404);
    if ($data['keluarga_id']) {
        $stmt2 = $conn->prepare("SELECT p2.id,p2.nik,p2.nama,p2.jenis_kelamin,TIMESTAMPDIFF(YEAR,p2.tanggal_lahir,CURDATE()) AS umur,p2.status_keluarga,p2.pekerjaan,p2.status_ekonomi FROM penduduk p2 WHERE p2.keluarga_id=? AND p2.id!=?");
        $stmt2->bind_param('ii',$data['keluarga_id'],$id); $stmt2->execute();
        $res2=$stmt2->get_result(); $anggota=[];
        while($r=$res2->fetch_assoc()) $anggota[]=$r;
        $stmt2->close(); $data['anggota_keluarga']=$anggota;
    } else $data['anggota_keluarga']=[];
    $stmt3 = $conn->prepare("SELECT hb.*,b.nama AS nama_bantuan,b.jenis FROM histori_bantuan hb JOIN bantuan b ON hb.bantuan_id=b.id WHERE hb.penduduk_id=? ORDER BY hb.tanggal DESC");
    $stmt3->bind_param('i',$id); $stmt3->execute();
    $res3=$stmt3->get_result(); $histori=[];
    while($r=$res3->fetch_assoc()) $histori[]=$r;
    $stmt3->close(); $data['histori_bantuan']=$histori;
    json_out($data);
}

// ── SIMPAN ─────────────────────────────────────────────
if ($aksi === 'simpan') {
    $nik  = trim($_POST['nik']??'');
    $nama = trim($_POST['nama']??'');
    $jk   = $_POST['jenis_kelamin']??'L';
    $tgl  = $_POST['tanggal_lahir']??'';
    $stk  = $_POST['status_keluarga']??'anggota';
    $stm  = $_POST['status_perkawinan']??'belum_kawin';
    $pek  = $_POST['pekerjaan']??'';
    $pgh  = (float)($_POST['penghasilan']??0);
    $sek  = $_POST['status_ekonomi']??'rentan';
    $pend = $_POST['pendidikan_terakhir']??'tidak_sekolah';
    $spend= $_POST['status_pendidikan']??'tidak_sekolah';
    $kid  = !empty($_POST['keluarga_id'])?(int)$_POST['keluarga_id']:null;
    $lat  = !empty($_POST['lat'])?(float)$_POST['lat']:null;
    $lng  = !empty($_POST['lng'])?(float)$_POST['lng']:null;
    $nohp = $_POST['no_hp']??'';
    $jban = $_POST['jenis_bantuan']??'';

    if (!$nik||!$nama||!$tgl) json_out(['error'=>'NIK, nama, dan tanggal lahir wajib diisi'],400);

    $cek=$conn->prepare("SELECT id FROM penduduk WHERE nik=?");
    $cek->bind_param('s',$nik); $cek->execute();
    if($cek->get_result()->num_rows>0) { $cek->close(); json_out(['error'=>'NIK sudah terdaftar'],400); }
    $cek->close();

    // cari ibadah terdekat
    $ibadah_id=null;
    if($lat!==null && $lng!==null){
        $si=$conn->prepare("SELECT id FROM rumah_ibadah ORDER BY (6371*acos(cos(radians(?))*cos(radians(lat))*cos(radians(lng)-radians(?))+sin(radians(?))*sin(radians(lat)))) ASC LIMIT 1");
        $si->bind_param('ddd',$lat,$lng,$lat); $si->execute();
        $ri=$si->get_result()->fetch_assoc(); $si->close();
        $ibadah_id=$ri['id']??null;
    }

    // Build query with NULL handling
    $lat_sql  = $lat!==null  ? $lat  : 'NULL';
    $lng_sql  = $lng!==null  ? $lng  : 'NULL';
    $kid_sql  = $kid!==null  ? $kid  : 'NULL';
    $ibd_sql  = $ibadah_id!==null ? $ibadah_id : 'NULL';

    $conn->query("INSERT INTO penduduk
        (nik,nama,jenis_kelamin,tanggal_lahir,status_keluarga,status_perkawinan,
         pekerjaan,penghasilan,status_ekonomi,pendidikan_terakhir,status_pendidikan,
         keluarga_id,rumah_ibadah_id,lat,lng,no_hp,jenis_bantuan)
        VALUES (
        '".$conn->real_escape_string($nik)."',
        '".$conn->real_escape_string($nama)."',
        '".$conn->real_escape_string($jk)."',
        '".$conn->real_escape_string($tgl)."',
        '".$conn->real_escape_string($stk)."',
        '".$conn->real_escape_string($stm)."',
        '".$conn->real_escape_string($pek)."',
        $pgh,
        '".$conn->real_escape_string($sek)."',
        '".$conn->real_escape_string($pend)."',
        '".$conn->real_escape_string($spend)."',
        $kid_sql,$ibd_sql,$lat_sql,$lng_sql,
        '".$conn->real_escape_string($nohp)."',
        '".$conn->real_escape_string($jban)."'
    )");
    if($conn->error) json_out(['error'=>$conn->error],500);
    $new_id=$conn->insert_id;

    if(!empty($_SESSION['user_id'])){
        log_aktivitas($conn,(int)$_SESSION['user_id'],'CREATE','penduduk',$new_id,'Tambah penduduk: '.$nama);
    }

    $umur=(int)date_diff(date_create($tgl),date_create('today'))->y;
    $warning=null;
    if($umur>=7&&$umur<=12&&$spend!='sekolah') $warning='Umur 7-12 th seharusnya masih SD.';
    elseif($umur>=13&&$umur<=15&&$spend!='sekolah') $warning='Umur 13-15 th seharusnya masih SMP.';
    elseif($umur>=16&&$umur<=18&&$spend=='tidak_sekolah') $warning='Umur 16-18 th biasanya masih SMA/SMK, mohon verifikasi.';
    json_out(['success'=>true,'id'=>$new_id,'warning'=>$warning]);
}

// ── EDIT ───────────────────────────────────────────────
if ($aksi === 'edit') {
    $id=(int)($_POST['id']??0);
    $nm  = $conn->real_escape_string($_POST['nama']??'');
    $jk  = $conn->real_escape_string($_POST['jenis_kelamin']??'L');
    $tgl = $conn->real_escape_string($_POST['tanggal_lahir']??'');
    $stk = $conn->real_escape_string($_POST['status_keluarga']??'anggota');
    $stm = $conn->real_escape_string($_POST['status_perkawinan']??'belum_kawin');
    $sh  = $conn->real_escape_string($_POST['status_hidup']??'hidup');
    $pek = $conn->real_escape_string($_POST['pekerjaan']??'');
    $pgh = (float)($_POST['penghasilan']??0);
    $sek = $conn->real_escape_string($_POST['status_ekonomi']??'rentan');
    $pend= $conn->real_escape_string($_POST['pendidikan_terakhir']??'tidak_sekolah');
    $spend=$conn->real_escape_string($_POST['status_pendidikan']??'tidak_sekolah');
    $nohp= $conn->real_escape_string($_POST['no_hp']??'');
    $jban= $conn->real_escape_string($_POST['jenis_bantuan']??'');

    $conn->query("UPDATE penduduk SET
        nama='$nm',jenis_kelamin='$jk',tanggal_lahir='$tgl',
        status_keluarga='$stk',status_perkawinan='$stm',status_hidup='$sh',
        pekerjaan='$pek',penghasilan=$pgh,status_ekonomi='$sek',
        pendidikan_terakhir='$pend',status_pendidikan='$spend',
        no_hp='$nohp',jenis_bantuan='$jban'
        WHERE id=$id");
    if($conn->error) json_out(['error'=>$conn->error],500);
    if(!empty($_SESSION['user_id'])){
        log_aktivitas($conn,(int)$_SESSION['user_id'],'UPDATE','penduduk',$id,'Edit penduduk id='.$id);
    }
    json_out(['success'=>true]);
}

// ── HAPUS ──────────────────────────────────────────────
if ($aksi === 'hapus') {
    $id=(int)($_POST['id']??0);
    $conn->query("DELETE FROM histori_bantuan WHERE penduduk_id=$id");
    $conn->query("DELETE FROM pelatihan WHERE penduduk_id=$id");
    $conn->query("DELETE FROM laporan WHERE penduduk_id=$id");
    $conn->query("DELETE FROM penduduk WHERE id=$id");
    json_out(['success'=>true]);
}
