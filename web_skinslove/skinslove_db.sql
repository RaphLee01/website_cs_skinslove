-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 19 Jul 2026 pada 18.48
-- Versi server: 10.11.18-MariaDB-cll-lve
-- Versi PHP: 8.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `angc2828_skinslove_rafli`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` enum('Knife','Glove','Rifle','Pistol','SMG') NOT NULL,
  `collection` varchar(100) NOT NULL,
  `wear` decimal(3,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `items`
--

INSERT INTO `items` (`id`, `name`, `category`, `collection`, `wear`, `price`, `image`, `is_available`, `created_at`) VALUES
(1, '★ Butterfly Knife | Fade', 'Knife', 'The Arms Deal Collection', 0.02, 1850.00, 'uploads/skin_1784209409.png', 0, '2026-07-16 12:44:04'),
(2, '★ Sport Gloves | Pandora Box', 'Glove', 'The Glove Collection', 0.15, 2400.00, 'uploads/skin_1784209364.png', 1, '2026-07-16 12:44:04'),
(3, 'M4A4 | Howl', 'Rifle', 'The Huntsman Collection', 0.08, 3100.00, 'uploads/skin_1784209194.png', 1, '2026-07-16 12:44:04'),
(4, 'AWP | Dragon Lore', 'Rifle', 'The Cobblestone Collection', 0.04, 4500.00, 'uploads/skin_1784209141.png', 1, '2026-07-16 12:44:04'),
(5, 'USP-S | Kill Confirmed', 'Pistol', 'The Shadow Collection', 0.12, 120.00, 'uploads/skin_1784209065.png', 1, '2026-07-16 12:44:04'),
(6, 'AWP | Hyper Beast', 'Rifle', 'Falchion Collection', 0.11, 278.00, 'uploads/skin_1784212081.webp', 1, '2026-07-16 14:28:01'),
(8, '★ Kukri Knife | Slaughter', 'Knife', 'The Kilowatt Collection', 0.01, 205.00, 'uploads/skin_1784294668.png', 1, '2026-07-17 13:24:28'),
(9, '★ ]Specialist Gloves | Mogul', 'Glove', 'The Clutch Collection', 0.02, 1111.00, 'uploads/skin_1784296551.png', 1, '2026-07-17 13:28:35'),
(10, '★ Sport Gloves | Nocts', 'Glove', '\"Operation Broken Fang\"', 0.09, 1800.00, 'uploads/skin_1784295310.png', 1, '2026-07-17 13:35:10'),
(11, 'AWP | Asiimov', 'Rifle', 'The Phoenix Collection', 0.93, 450.00, 'uploads/skin_1784295724.png', 1, '2026-07-17 13:42:04'),
(12, 'AK-47 | The Empress', 'Rifle', 'The Spectrum 2 Collection', 0.67, 140.00, 'uploads/skin_1784296044.png', 1, '2026-07-17 13:46:59'),
(13, 'M4A4 | The Emperor', 'Rifle', 'The Prisma Collection', 0.14, 100.00, 'uploads/skin_1784296154.png', 1, '2026-07-17 13:49:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `is_read`, `created_at`) VALUES
(35, 5, 'Top-Up Saldo Berhasil!', 'Selamat! Saldo sebesar $1,000.00 telah berhasil ditambahkan ke akun Anda menggunakan metode QRIS Instan. Selamat berbelanja skin CS2 impian!', 0, '2026-07-16 16:00:00'),
(36, 5, 'Top-Up Saldo Berhasil!', 'Selamat! Saldo sebesar $10,000.00 telah berhasil ditambahkan ke akun Anda menggunakan metode QRIS Instan. Selamat berbelanja skin CS2 impian!', 0, '2026-07-16 16:00:36'),
(37, 5, 'Pembelian Berhasil!', 'Anda telah sukses membeli skin ★ Sport Gloves | Pandora Box seharga $2,400.00.', 0, '2026-07-16 16:01:00'),
(50, 5, 'Pembelian Berhasil!', 'Anda telah sukses membeli skin ★ Butterfly Knife | Fade seharga $1,850.00.', 0, '2026-07-16 16:08:47'),
(51, 5, 'Penjualan Berhasil!', 'Anda telah sukses menjual kembali skin ★ Butterfly Knife | Fade seharga $1,850.00 dan saldo telah ditambahkan.', 0, '2026-07-16 16:09:32'),
(52, 5, 'Penjualan Berhasil!', 'Anda telah sukses menjual kembali skin ★ Sport Gloves | Pandora Box seharga $2,400.00 dan saldo telah ditambahkan.', 0, '2026-07-16 16:09:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `suggestions`
--

CREATE TABLE `suggestions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `suggestions`
--

INSERT INTO `suggestions` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'NiKolinho', 'niko@gmail.com', 'Saran Sistem Pembayaran', 'Tolong tambahkan metode pembayaran dengan e-wallet lokal seperti GoPay atau OVO agar transaksi di SkinsLove.gg semakin gampang!', '2026-07-16 12:44:04'),
(2, 'ohnepixel', 'mark.zimmermann@gmail.com', 'Masalah Pencarian', 'Tampilan website sangat mewah dan responsif di HP saya, tapi saya harap pilihan filter collection bisa otomatis ter-update.', '2026-07-16 12:44:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `price_paid` decimal(10,2) NOT NULL,
  `transaction_type` enum('buy','sell','trade') NOT NULL,
  `status` enum('pending','success','failed') DEFAULT 'success',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `item_id`, `price_paid`, `transaction_type`, `status`, `created_at`) VALUES
(31, 5, 2, 2400.00, 'buy', 'success', '2026-07-16 16:01:00'),
(42, 5, 1, 1850.00, 'buy', 'success', '2026-07-16 16:08:47'),
(43, 5, 1, 1850.00, 'sell', 'success', '2026-07-16 16:09:32'),
(44, 5, 2, 2400.00, 'sell', 'success', '2026-07-16 16:09:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `balance` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `balance`, `created_at`) VALUES
(1, 'Admin', 'admin@skinslove.gg', '$2y$10$bmVi2OhrvL7dOlmOfPueMOioIDVOvvtn6B.IgJOgXhyFifk3GNl3O', 'admin', 12500.00, '2026-07-16 12:44:04'),
(5, 'Ayam Jabrik', 'jabrik@gmail.com', '$2y$10$DXAV9IV.5A341/m/WpjdBec69BpFN19P0LTpMBZi7vMBA3FlbFTtC', 'user', 11000.00, '2026-07-16 15:56:25'),
(9, 'Ayam Gondrong', 'ayam@gmail.com', '$2y$10$fgG/s/HgS4gvnWkoGrF8n.pbAj8otuDsjh8w7mXT6H8a1JX9rxiw6', 'user', 0.00, '2026-07-18 16:14:14');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indeks untuk tabel `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `suggestions`
--
ALTER TABLE `suggestions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT untuk tabel `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT untuk tabel `suggestions`
--
ALTER TABLE `suggestions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
