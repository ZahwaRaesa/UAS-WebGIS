<?php
ob_start();
include 'koneksi.php';
ob_clean();

$id     = $conn->real_escape_string($_POST['id']     ?? '');
$nama   = $conn->real_escape_string($_POST['nama']   ?? '');
$wa     = $conn->real_escape_string($_POST['wa']     ?? '');
$status = $conn->real_escape_string($_POST['status'] ?? '');
$lat    = $conn->real_escape_string($_POST['lat']    ?? '');
$lng    = $conn->real_escape_string($_POST['lng']    ?? '');

$query = "UPDATE spbu SET 
    nama_spbu='$nama',
    no_wa='$wa',
    status='$status',
    latitude='$lat',
    longitude='$lng'
    WHERE id='$id'";

if ($conn->query($query)) {
    echo "success";
} else {
    echo "error";
}
?>
