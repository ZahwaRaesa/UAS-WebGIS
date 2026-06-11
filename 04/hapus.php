<?php
ob_start();
include 'koneksi.php';
ob_clean();

$id = $conn->real_escape_string($_POST['id'] ?? '');

$query = "DELETE FROM spbu WHERE id='$id'";

if ($conn->query($query)) {
    echo "success";
} else {
    echo "error: " . $conn->error;
}
