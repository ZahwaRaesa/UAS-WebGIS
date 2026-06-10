<?php
include 'koneksi.php';

$data = [];

$result = $conn->query("SELECT * FROM spbu");

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>