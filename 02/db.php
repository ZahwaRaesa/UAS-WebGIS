<?php

$host = getenv('DB_HOST') ?: 'db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: 'rootpassword';
$db   = getenv('DB_NAME') ?: 'uas_06';

$conn = @mysqli_connect($host, $user, $pass, $db);
