-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 12, 2025 at 04:43 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Tambahan agar tidak muncul error 1046
CREATE DATABASE IF NOT EXISTS `notulen` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `notulen`;

--
-- Struktur tabel untuk tabel `users`
--
CREATE TABLE `users` (
`id` int(11) NOT NULL,
`foto` varchar(255) DEFAULT 'default.jpg',
`nama` varchar(100) NOT NULL,
`email` varchar(100) NOT NULL,
`username` varchar(50) NOT NULL,
`password` varchar(255) NOT NULL,
`role` enum('admin','peserta') DEFAULT 'peserta',
`created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Isi data awal untuk tabel `users`
--
INSERT INTO `users` (`id`, `foto`, `nama`, `email`, `username`, `password`, `role`, `created_at`) VALUES
(2, 'default.jpg', 'DIDIT', 'admin@gmail.com', 'didit', 'lopolo9090', 'admin', '2025-11-11 06:52:44');

--
-- Indexes untuk tabel `users`
--
ALTER TABLE `users`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

COMMIT;