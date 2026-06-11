<?php
include 'koneksi.php';

$id = $_POST['id'];
$nama = $_POST['nama'];
$lat = $_POST['lat'];
$lng = $_POST['lng'];

$stmt = $koneksi->prepare("UPDATE spbu SET nama_spbu=?, lat=?, lng=? WHERE id=?");
$stmt->bind_param("sddi", $nama, $lat, $lng, $id);

echo $stmt->execute() ? "success" : "error";
?>
