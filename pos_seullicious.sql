-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 20 Apr 2026 pada 11.04
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
-- Database: `pos_seullicious`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `menu`
--

CREATE TABLE `menu` (
  `id_menu` int(11) NOT NULL,
  `nama_menu` varchar(100) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `harga` int(11) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `menu`
--

INSERT INTO `menu` (`id_menu`, `nama_menu`, `kategori`, `harga`, `gambar`) VALUES
(5, 'yakult', 'drink', 25000, 'Yakult.jpg'),
(7, 'Tonkotsu Ramen', 'food', 45000, 'Tonkotsu Rame.jpg'),
(8, 'tteokbokki', 'food', 30000, 'tteokbokki.avif'),
(9, 'sujeonggwa', 'food', 25000, 'Sujeonggwa.png'),
(10, 'strawbery milk', 'drink', 25000, 'strawbery milk.jpg'),
(11, 'soju', 'drink', 25000, 'soju drink.png'),
(12, 'Samgyetang', 'food', 50000, 'samgyetang.png'),
(13, 'samgyeopsal', 'food', 45000, 'Samgyeopsal .jpg'),
(15, 'oden', 'snack', 25000, 'odeng.png'),
(16, 'nakji bokkeum', 'food', 45000, 'Nakji-bokkeum .jpg'),
(17, 'mandu', 'snack', 25000, 'mandu.png'),
(19, 'jjampong', 'food', 45000, 'Jjampong.png'),
(20, 'japcahe', 'food', 45000, 'japcahe.png'),
(21, 'Jajangmyeon', 'food', 45000, 'jajangmyeon.avif'),
(22, 'hotteok', 'snack', 25000, 'Hotteok.png'),
(23, 'kimbab', 'food', 45000, 'gimbab.png'),
(24, 'kimchi', 'food', 30, 'generated-image (12).png'),
(25, 'dubai chewy cookie', 'snack', 45000, 'Dubai chewy cookie.jpg'),
(26, 'double bianco', 'snack', 25000, 'Double bianco.jpg'),
(27, 'dalgona coffee', 'drink', 45000, 'Dalgona Coffee.jpg'),
(28, 'corn tea', 'drink', 25000, 'Corn tea.jpg'),
(29, 'chilsung cider', 'drink', 25000, 'Chilsung Cider.jpg'),
(30, 'bungeoppang', 'snack', 30, 'Bungeoppang.png'),
(31, 'banana milk', 'drink', 25000, 'Banana milk.jpg'),
(32, 'bibimbap', 'food', 45000, 'bibimbap.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id_order` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `total_items` int(11) DEFAULT NULL,
  `total_price` int(11) DEFAULT NULL,
  `order_status` enum('NEW','PROCESS','DONE') DEFAULT 'NEW',
  `table_number` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `tanggal` datetime DEFAULT NULL,
  `total` int(11) DEFAULT NULL,
  `meja` int(11) DEFAULT NULL,
  `order_type` varchar(20) DEFAULT NULL,
  `metode_bayar` varchar(20) DEFAULT NULL,
  `jam_mulai` time DEFAULT NULL,
  `jam_selesai` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `orders`
--

INSERT INTO `orders` (`id_order`, `id_user`, `total_items`, `total_price`, `order_status`, `table_number`, `created_at`, `tanggal`, `total`, `meja`, `order_type`, `metode_bayar`, `jam_mulai`, `jam_selesai`) VALUES
(1, 10, NULL, NULL, 'NEW', NULL, '2026-03-09 10:20:50', '2026-03-09 17:20:50', 175000, NULL, NULL, NULL, NULL, NULL),
(2, 10, NULL, NULL, 'NEW', NULL, '2026-03-09 10:20:52', '2026-03-09 17:20:52', 175000, NULL, NULL, NULL, NULL, NULL),
(3, 10, NULL, NULL, 'NEW', NULL, '2026-03-09 10:23:33', '2026-03-09 17:23:33', 175000, NULL, NULL, NULL, NULL, NULL),
(4, 10, NULL, NULL, 'NEW', NULL, '2026-03-09 10:23:35', '2026-03-09 17:23:35', 175000, NULL, NULL, NULL, NULL, NULL),
(5, 10, NULL, NULL, 'NEW', NULL, '2026-03-09 10:24:26', '2026-03-09 17:24:26', 175000, NULL, NULL, NULL, NULL, NULL),
(6, 10, NULL, NULL, 'NEW', NULL, '2026-03-09 10:25:04', '2026-03-09 17:25:04', 25000, NULL, NULL, NULL, NULL, NULL),
(7, 10, NULL, NULL, 'DONE', NULL, '2026-03-09 10:29:23', '2026-03-09 17:29:23', 25000, NULL, NULL, NULL, NULL, NULL),
(9, 10, NULL, NULL, 'DONE', NULL, '2026-03-09 10:29:32', '2026-03-09 17:29:32', 75000, NULL, NULL, NULL, NULL, NULL),
(10, 10, NULL, NULL, 'DONE', NULL, '2026-03-09 10:39:40', '2026-03-09 17:39:40', 25000, NULL, NULL, NULL, NULL, NULL),
(12, 10, NULL, NULL, 'DONE', NULL, '2026-03-09 10:47:25', '2026-03-09 17:47:25', 25000, NULL, NULL, NULL, NULL, NULL),
(13, 10, NULL, NULL, 'DONE', NULL, '2026-03-09 10:47:37', '2026-03-09 17:47:37', 25000, NULL, NULL, NULL, NULL, NULL),
(14, 10, NULL, NULL, 'DONE', NULL, '2026-03-09 10:48:08', '2026-03-09 17:48:08', 25000, NULL, NULL, NULL, NULL, NULL),
(15, 10, NULL, NULL, 'DONE', NULL, '2026-03-09 10:53:42', '2026-03-09 17:53:42', 25000, NULL, NULL, NULL, NULL, NULL),
(16, 10, NULL, NULL, 'DONE', NULL, '2026-03-09 10:53:57', '2026-03-09 17:53:57', 25000, NULL, NULL, NULL, NULL, NULL),
(17, 10, NULL, NULL, 'DONE', NULL, '2026-03-09 10:56:19', '2026-03-09 17:56:19', 25000, 1, 'Dine In', 'Cash', NULL, NULL),
(18, 10, NULL, NULL, 'DONE', NULL, '2026-03-09 11:03:18', '2026-03-09 18:03:18', 25000, 2, 'Dine In', 'Cash', NULL, NULL),
(19, 10, NULL, NULL, 'DONE', NULL, '2026-03-09 11:35:18', '2026-03-09 18:35:18', 25000, 3, 'Dine In', 'Cash', NULL, NULL),
(20, 10, NULL, NULL, 'DONE', NULL, '2026-03-09 11:36:05', '2026-03-09 18:36:05', 25000, 0, 'Take Away', 'Cash', NULL, NULL),
(26, 10, NULL, NULL, 'DONE', NULL, '2026-03-10 00:50:41', '2026-03-10 07:50:41', 55000, 7, 'Dine In', 'Cash', NULL, NULL),
(27, 10, NULL, NULL, 'DONE', NULL, '2026-03-10 02:27:58', '2026-03-10 09:27:58', 85000, 2, 'Dine In', 'Cash', NULL, NULL),
(28, 10, NULL, NULL, 'DONE', NULL, '2026-03-10 02:49:13', '2026-03-10 09:49:13', 25000, 0, 'Take Away', 'Cash', NULL, NULL),
(29, 10, NULL, NULL, 'DONE', NULL, '2026-03-10 02:51:22', '2026-03-10 09:51:22', 55000, 1, 'Dine In', 'Cash', NULL, NULL),
(30, 10, NULL, NULL, 'DONE', NULL, '2026-03-10 03:46:09', '2026-03-10 10:46:09', 45000, 16, 'Dine In', 'QRIS', NULL, NULL),
(31, 10, NULL, NULL, 'DONE', NULL, '2026-03-30 14:46:02', '2026-03-30 21:46:02', 55000, NULL, 'dine-in', 'cash', NULL, NULL),
(32, 10, NULL, NULL, 'DONE', NULL, '2026-03-30 14:46:42', '2026-03-30 21:46:42', 45000, NULL, 'dine-in', 'cash', NULL, NULL),
(33, 10, NULL, NULL, 'DONE', NULL, '2026-03-30 14:55:49', '2026-03-30 21:55:49', 55000, NULL, 'dine-in', 'cash', NULL, NULL),
(34, 10, NULL, NULL, 'DONE', NULL, '2026-03-31 03:05:36', '2026-03-31 10:05:36', 95000, NULL, 'dine-in', 'cash', NULL, NULL),
(35, 10, NULL, NULL, 'DONE', NULL, '2026-04-20 05:52:33', '2026-04-20 12:52:33', 110000, NULL, 'dinein', 'Card', NULL, NULL),
(36, 10, NULL, NULL, 'DONE', NULL, '2026-04-20 05:53:06', '2026-04-20 12:53:06', 49500, NULL, 'dinein', 'Card', NULL, NULL),
(37, 10, NULL, NULL, 'DONE', NULL, '2026-04-20 05:53:38', '2026-04-20 12:53:38', 82500, NULL, 'dinein', 'QR', NULL, NULL),
(38, 10, NULL, NULL, 'DONE', NULL, '2026-04-20 05:54:07', '2026-04-20 12:54:07', 49500, NULL, 'takeaway', 'Cash', NULL, NULL),
(39, 10, NULL, NULL, 'DONE', NULL, '2026-04-20 06:02:35', '2026-04-20 13:02:35', 27500, NULL, 'dinein', 'Card', NULL, NULL),
(40, NULL, NULL, NULL, 'DONE', NULL, '2026-04-20 09:01:52', '2026-04-20 16:01:52', 27500, NULL, 'dinein', 'Card', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_items`
--

CREATE TABLE `order_items` (
  `id_item` int(11) NOT NULL,
  `id_order` int(11) DEFAULT NULL,
  `id_menu` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `subtotal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `order_items`
--

INSERT INTO `order_items` (`id_item`, `id_order`, `id_menu`, `qty`, `price`, `subtotal`) VALUES
(25, 26, 31, 1, NULL, 25000),
(26, 26, 8, 1, NULL, 30000),
(27, 27, 17, 1, NULL, 25000),
(28, 27, 8, 2, NULL, 60000),
(29, 28, 11, 1, NULL, 25000),
(30, 29, 11, 1, NULL, 25000),
(31, 29, 8, 1, NULL, 30000),
(32, 30, 7, 1, NULL, 45000),
(33, 31, NULL, 2, NULL, 0),
(34, 31, 8, 1, NULL, 30000),
(35, 31, 10, 1, NULL, 25000),
(36, 32, 7, 1, NULL, 45000),
(37, 33, 8, 1, NULL, 30000),
(38, 33, 9, 1, NULL, 25000),
(39, 34, 10, 2, NULL, 50000),
(40, 34, 7, 1, NULL, 45000),
(41, 35, 7, 1, NULL, 45000),
(42, 35, 8, 1, NULL, 30000),
(43, 35, 9, 1, NULL, 25000),
(44, 36, 7, 1, NULL, 45000),
(45, 37, 8, 1, NULL, 30000),
(46, 37, 16, 1, NULL, 45000),
(47, 38, 7, 1, NULL, 45000),
(48, 39, 9, 1, NULL, 25000),
(49, 40, 17, 1, NULL, 25000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

CREATE TABLE `produk` (
  `id_menu` int(11) NOT NULL,
  `nama_menu` varchar(100) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `harga` int(11) DEFAULT NULL,
  `stok` int(11) DEFAULT 0,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `review_menu`
--

CREATE TABLE `review_menu` (
  `id_review` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_menu` int(11) DEFAULT NULL,
  `id_order` int(11) DEFAULT NULL,
  `bintang` tinyint(1) DEFAULT NULL,
  `komentar` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `review_restoran`
--

CREATE TABLE `review_restoran` (
  `id_review` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_order` int(11) DEFAULT NULL,
  `bintang` tinyint(1) DEFAULT NULL,
  `komentar` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `setting`
--

CREATE TABLE `setting` (
  `id` int(11) NOT NULL,
  `nama_toko` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `metode` varchar(100) DEFAULT NULL,
  `pajak` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `status` enum('admin','kasir') DEFAULT 'kasir'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `nama_lengkap`, `status`) VALUES
(7, 'cinta', '1234', 'aaaa', 'kasir'),
(8, 'bunga', '1234', 'asss', 'admin'),
(9, 'andi', '1234', NULL, ''),
(10, 'Melinn', '123456', NULL, ''),
(11, 'Melin', '123456', NULL, '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_settings`
--

CREATE TABLE `user_settings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `theme` varchar(20) DEFAULT 'light',
  `color` varchar(20) DEFAULT 'gold',
  `language` varchar(5) DEFAULT 'id',
  `notif_order` tinyint(4) DEFAULT 1,
  `notif_payment` tinyint(4) DEFAULT 1,
  `sound` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id_order`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `id_order` (`id_order`),
  ADD KEY `fk_menu` (`id_menu`);

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indeks untuk tabel `review_menu`
--
ALTER TABLE `review_menu`
  ADD PRIMARY KEY (`id_review`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_menu` (`id_menu`),
  ADD KEY `id_order` (`id_order`);

--
-- Indeks untuk tabel `review_restoran`
--
ALTER TABLE `review_restoran`
  ADD PRIMARY KEY (`id_review`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_order` (`id_order`);

--
-- Indeks untuk tabel `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- Indeks untuk tabel `user_settings`
--
ALTER TABLE `user_settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id_order` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT untuk tabel `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `review_menu`
--
ALTER TABLE `review_menu`
  MODIFY `id_review` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `review_restoran`
--
ALTER TABLE `review_restoran`
  MODIFY `id_review` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `setting`
--
ALTER TABLE `setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `user_settings`
--
ALTER TABLE `user_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Ketidakleluasaan untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_menu` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`),
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- ── Tambahan kolom untuk fitur profil user (home.php v2) ──────────────────
ALTER TABLE `user_settings`
  ADD COLUMN IF NOT EXISTS `display_name` VARCHAR(80) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `photo_path` VARCHAR(200) DEFAULT NULL;
