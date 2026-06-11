<?php
include 'koneksi.php';

$nama = $_POST['nama'];
$lat = $_POST['lat'];
$lng = $_POST['lng'];

$stmt = $koneksi->prepare("INSERT INTO spbu (nama_spbu, lat, lng) VALUES (?, ?, ?)");
$stmt->bind_param("sdd", $nama, $lat, $lng);

echo $stmt->execute() ? "success" : "error";
?>
