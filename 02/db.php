<?php

$host = getenv('DB_HOST') ?: 'db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: 'rootpassword';
$db   = '02_kerusakan';

$conn = @mysqli_connect($host, $user, $pass, $db);
