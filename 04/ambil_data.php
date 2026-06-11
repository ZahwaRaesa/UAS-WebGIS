<?php
include 'koneksi.php';

$data = [];

$result = $conn->query("SELECT * FROM spbu_04");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    // If table doesn't exist or other error, return empty array
}

echo json_encode($data);