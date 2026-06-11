-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 07 Jun 2026 pada 10.53
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

CREATE DATABASE IF NOT EXISTS `db_spbu` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_spbu`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_spbu`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `spbu`
--

CREATE TABLE `spbu` (
  `id` int(11) NOT NULL,
  `nama_spbu` varchar(255) DEFAULT NULL,
  `lat` double DEFAULT NULL,
  `lng` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `spbu`
--

INSERT INTO `spbu` (`id`, `nama_spbu`, `lat`, `lng`) VALUES
(1, 'spbu kobar', -0.02197187355969219, 109.31207858442205),
(3, 'spbu paris', 0.007368924759898348, 109.37010012983221),
(4, 'spbu tanray', 0.016651739943910275, 109.29988329640928);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `spbu`
--
ALTER TABLE `spbu`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `spbu`
--
ALTER TABLE `spbu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
