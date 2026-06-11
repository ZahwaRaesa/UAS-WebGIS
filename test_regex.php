<?php
$sql = "CREATE DATABASE IF NOT EXISTS uas_06 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE uas_06;
CREATE TABLE role (id INT);";
$sql = preg_replace('/CREATE DATABASE IF NOT EXISTS [^\;]+\;/i', '', $sql);
$sql = preg_replace('/USE [^\;]+\;/i', '', $sql);
echo $sql;
