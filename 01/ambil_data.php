<?php
include 'koneksi.php';

$result = $koneksi->query("SELECT * FROM spbu");

$data = [];
while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);
?>
