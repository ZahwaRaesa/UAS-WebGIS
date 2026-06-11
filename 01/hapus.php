<?php
include 'koneksi.php';

$id = $_POST['id'];

$stmt = $koneksi->prepare("DELETE FROM spbu_01 WHERE id=?");
$stmt->bind_param("i", $id);

echo $stmt->execute() ? "success" : "error";
?>
