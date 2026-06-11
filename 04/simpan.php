<?php
ob_start();
include 'koneksi.php';
ob_clean();

$nama   = $conn->real_escape_string($_POST['nama']   ?? '');
$wa     = $conn->real_escape_string($_POST['wa']     ?? '');
$status = $conn->real_escape_string($_POST['status'] ?? '');
$lat    = $conn->real_escape_string($_POST['lat']    ?? '');
$lng    = $conn->real_escape_string($_POST['lng']    ?? '');

$query = "INSERT INTO spbu_04 (nama_spbu, no_wa, status, latitude, longitude)
          VALUES ('$nama', '$wa', '$status', $lat, $lng)";

if ($conn->query($query)) {
    echo "success";
} else {
    echo "error: " . $conn->error;
}
