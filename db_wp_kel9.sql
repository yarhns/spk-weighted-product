-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 02 Nov 2026 pada 08.44
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_wp_kel9`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `alternatif`
--

CREATE TABLE `alternatif` (
  `id_alter` int(11) NOT NULL,
  `code` varchar(128) DEFAULT NULL,
  `alternatif` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `alternatif`
--

INSERT INTO `alternatif` (`id_alter`, `code`, `alternatif`) VALUES
(1, 'R1', 'Komplek Villa Issaura'),
(2, 'R2', 'Komplek Pondok Pelangi'),
(3, 'R3', 'Perum Permata Bhayangkara'),
(4, 'R4', 'Rumah di Jalan Ampera'),
(5, 'R5', 'Rumah di Jalan Serdam'),
(10, 'R6', 'Komplek Ampera Raya Residence'),
(11, 'R7', 'Komplek Bumi Sepakat Permai'),
(12, 'R8', 'Rumah Jl Serdam (Dekat Xing Mart)'),
(13, 'R9', 'Rindang Alam'),
(14, 'R10', 'Rumah Jalan Semandang'),
(15, 'R11', 'Perumahan Elang Khatulistiwa'),
(16, 'R12', 'Komplek Mega Park');

-- --------------------------------------------------------

--
-- Struktur dari tabel `bobot`
--

CREATE TABLE `bobot` (
  `id_bobot` int(11) NOT NULL,
  `id_alter` int(11) DEFAULT NULL,
  `c1` int(128) DEFAULT NULL,
  `c2` int(128) DEFAULT NULL,
  `c3` int(128) DEFAULT NULL,
  `c4` int(128) DEFAULT NULL,
  `c5` int(128) DEFAULT NULL,
  `c6` int(128) DEFAULT NULL,
  `c7` int(128) DEFAULT NULL,
  `c8` int(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bobot`
--

INSERT INTO `bobot` (`id_bobot`, `id_alter`, `c1`, `c2`, `c3`, `c4`, `c5`, `c6`, `c7`, `c8`) VALUES
(1, 1, 4, 3, 5, 4, 6, 2, 2, 3),
(2, 2, 2, 3, 5, 4, 7, 2, 2, 3),
(3, 3, 4, 3, 4, 4, 5, 1, 2, 2),
(4, 4, 4, 3, 4, 4, 5, 2, 2, 3),
(5, 5, 4, 3, 4, 4, 6, 2, 2, 3),
(6, 10, 5, 3, 4, 4, 2, 1, 2, 3),
(9, 11, 3, 3, 4, 2, 7, 2, 2, 2),
(10, 12, 4, 2, 4, 2, 6, 2, 2, 3),
(11, 13, 5, 3, 3, 1, 2, 2, 2, 3),
(12, 14, 5, 3, 4, 1, 5, 2, 2, 3),
(13, 15, 5, 3, 1, 1, 2, 1, 2, 3),
(14, 16, 4, 3, 2, 2, 6, 2, 2, 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kriteria`
--

CREATE TABLE `kriteria` (
  `id` int(11) NOT NULL,
  `code` varchar(32) DEFAULT NULL,
  `kriteria` varchar(256) DEFAULT NULL,
  `jenis` varchar(32) DEFAULT NULL,
  `bobot` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kriteria`
--

INSERT INTO `kriteria` (`id`, `code`, `kriteria`, `jenis`, `bobot`) VALUES
(1, 'C1', 'Harga', 'cost', 8),
(2, 'C2', 'Luas Tanah', 'benefit', 7),
(3, 'C3', 'Waktu Tempuh Ke Pusat Kota', 'cost', 3),
(4, 'C4', 'Akses Menuju Rumah', 'benefit', 5),
(5, 'C5', 'Tipe Bangunan', 'benefit', 6),
(6, 'C6', 'Fasilitas Umum', 'benefit', 5),
(7, 'C7', 'Perizinan', 'cost', 8),
(8, 'C8', 'Desain Rumah', 'benefit', 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `level` enum('Admin','User') DEFAULT 'Admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `level`, `created_at`) VALUES
(2, 'admin', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1HlCS4bZJ1kL9y/8n0a.wQ6p5s8j8.6', 'Administrator', 'Admin', '2026-11-02 07:44:36');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `alternatif`
--
ALTER TABLE `alternatif`
  ADD PRIMARY KEY (`id_alter`);

--
-- Indeks untuk tabel `bobot`
--
ALTER TABLE `bobot`
  ADD PRIMARY KEY (`id_bobot`),
  ADD KEY `id_alter` (`id_alter`);

--
-- Indeks untuk tabel `kriteria`
--
ALTER TABLE `kriteria`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `alternatif`
--
ALTER TABLE `alternatif`
  MODIFY `id_alter` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `bobot`
--
ALTER TABLE `bobot`
  MODIFY `id_bobot` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `kriteria`
--
ALTER TABLE `kriteria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bobot`
--
ALTER TABLE `bobot`
  ADD CONSTRAINT `bobot_ibfk_1` FOREIGN KEY (`id_alter`) REFERENCES `alternatif` (`id_alter`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
