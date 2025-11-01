-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 01, 2025 at 03:03 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `boemi_camp_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `full_name`, `email`, `created_at`) VALUES
(1, 'admin', '$2y$10$3v7pjQI5689OKCDsbACy5.ahKD9ot8IuPjBNILJIoHEHLlo9BYbQ.', 'Admin Utama', 'admin@boemicamp.com', '2025-10-28 11:20:11'),
(2, 'manager', '$2a$10$differentHash', 'Manager Boemi', 'manager@boemicamp.com', '2025-10-28 11:20:20');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `alamat` text DEFAULT NULL,
  `tanggal_ambil` datetime NOT NULL,
  `tanggal_kembali` datetime NOT NULL,
  `metode_pengantaran` varchar(50) NOT NULL,
  `metode_pembayaran` varchar(50) NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `status_pesanan` varchar(50) NOT NULL DEFAULT 'Menunggu Pembayaran',
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `order_created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `nama_pelanggan`, `no_hp`, `alamat`, `tanggal_ambil`, `tanggal_kembali`, `metode_pengantaran`, `metode_pembayaran`, `total_harga`, `status_pesanan`, `bukti_pembayaran`, `order_created_at`) VALUES
(1, 'Irvan Reki Purnama', '085347247557', 'Elektro Polnep', '2025-11-11 10:01:00', '2025-11-27 10:01:00', 'Ambil Sendiri', '', 320000.00, 'Dibatalkan', NULL, '2025-11-01 03:01:52'),
(2, 'Irvan Reki Purnama', '085347247557', 'Elektro Polnep', '2025-11-02 10:05:00', '2025-11-10 10:05:00', 'Ambil Sendiri', '', 240000.00, 'Dibatalkan', NULL, '2025-11-01 03:05:50'),
(3, 'BIma', '085347247557', 'beting', '2025-11-15 10:10:00', '2025-11-16 10:10:00', 'Ambil Sendiri', '', 45000.00, 'Dibatalkan', NULL, '2025-11-01 03:10:51'),
(4, 'Bima', '085347247557', 'Elektro Polnep', '2025-11-01 10:15:00', '2025-11-03 10:15:00', 'Ambil Sendiri', '', 60000.00, 'Dibatalkan', NULL, '2025-11-01 03:15:52'),
(5, 'irvan', '085347247557', 'Elektro Polnep', '2025-11-01 10:21:00', '2025-11-03 10:21:00', 'Dianter - Gratis', '', 30000.00, 'Dibatalkan', '5-1761967810-gunung.jpg', '2025-11-01 03:22:02'),
(6, 'Irvan', '085347247557', 'Elektro Polnep', '2025-11-14 10:26:00', '2025-11-29 10:26:00', 'Dianter - Gratis', '', 225000.00, 'Selesai (Kembali)', '6-1761967718-KTM.jpg', '2025-11-01 03:26:25'),
(7, 'yani', '082199457870', 'beting', '2025-11-01 10:39:00', '2025-11-13 10:39:00', 'Dianter - Gratis', '', 180000.00, 'Dibatalkan', NULL, '2025-11-01 03:40:12'),
(8, 'amba', '085347247557', 'beting', '2025-11-01 10:46:00', '2025-11-02 10:46:00', 'Ambil Sendiri', 'COD', 15000.00, 'Selesai (Kembali)', NULL, '2025-11-01 03:47:12'),
(9, 'Rusdi', '085347247557', 'beting', '2025-11-04 12:08:00', '2025-11-05 12:08:00', 'Dianter - Gratis', 'COD', 25000.00, 'COD (Siap Diantar/Diambil)', NULL, '2025-11-01 05:08:59'),
(10, 'fsdfs', 'dss', 'dss', '2025-11-01 12:14:00', '2025-11-08 12:14:00', 'Dianter - Gratis', 'COD', 245000.00, 'Dibatalkan', NULL, '2025-11-01 05:14:19'),
(11, 'penjual Balon', '085347247557', 'beting', '2025-11-01 12:44:00', '2025-11-02 12:44:00', 'Ambil Sendiri', 'COD', 15000.00, 'Dibatalkan', NULL, '2025-11-01 05:44:44');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `jumlah_sewa` int(11) NOT NULL,
  `harga_saat_sewa` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `product_id`, `jumlah_sewa`, `harga_saat_sewa`) VALUES
(1, 1, 12, 1, 20000.00),
(2, 2, 3, 1, 30000.00),
(3, 3, 4, 1, 15000.00),
(4, 3, 3, 1, 30000.00),
(5, 4, 3, 1, 30000.00),
(6, 5, 4, 1, 15000.00),
(7, 6, 4, 1, 15000.00),
(8, 7, 4, 1, 15000.00),
(9, 8, 4, 1, 15000.00),
(10, 9, 18, 1, 25000.00),
(11, 10, 26, 1, 35000.00),
(12, 11, 4, 1, 15000.00);

-- --------------------------------------------------------

--
-- Table structure for table `paket_grup`
--

CREATE TABLE `paket_grup` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(100) NOT NULL,
  `group_image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paket_grup`
--

INSERT INTO `paket_grup` (`group_id`, `group_name`, `group_image_url`) VALUES
(1, 'Paket Bucin', 'Paket Bucin.jpg'),
(2, 'Paket Bestie', 'Paket Bestie.jpg'),
(3, 'Paket Circle', 'Paket Circle.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `group_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image_url`, `category`, `stock`, `group_id`, `created_at`, `updated_at`) VALUES
(1, 'Tenda Dome 4 Orang', 'Tenda waterproof untuk 4 orang, cocok untuk camping di gunung', 50000.00, '1761927413-Tenda 4 Orang.png', 'tenda', 5, NULL, '2025-10-28 12:28:12', '2025-10-31 23:16:53'),
(2, 'Sleeping Bag Premium', 'Sleeping bag nyaman untuk cuaca dingin, bahan berkualitas', 25000.00, '1761928044-Sleeping Bag.jpg', 'tenda', 8, NULL, '2025-10-28 12:28:12', '2025-10-31 23:27:24'),
(3, 'Kompor Portable', 'Kompor gas portable praktis untuk camping, mudah dibawa', 30000.00, '1761927972-kompor portable.jpg', 'tenda', 0, NULL, '2025-10-28 12:28:12', '2025-11-01 10:15:52'),
(4, 'Lampu Camping LED', 'Lampu camping LED hemat energi, tahan air', 15000.00, '1761927919-lampu camping.jpg', 'tenda', 8, NULL, '2025-10-28 12:28:12', '2025-11-01 12:45:32'),
(6, 'Kursi Lipat', 'Kursi Lipat Berkualitas', 10000.00, '1761928210-Kursi Lipat.jpg', NULL, 0, NULL, '2025-10-31 23:30:10', '2025-10-31 23:30:10'),
(7, 'Nesting', 'Alat Masak', 25000.00, '1761928302-Nesting.jpg', NULL, 0, NULL, '2025-10-31 23:31:42', '2025-10-31 23:31:42'),
(8, 'Matras', 'Matras tidur', 10000.00, '1761928462-Matras.jpg', NULL, 0, NULL, '2025-10-31 23:34:22', '2025-10-31 23:34:22'),
(9, 'Meja Lipat', 'Meja Lipat', 10000.00, '1761928576-Meja Lipat.jpg', NULL, 0, NULL, '2025-10-31 23:36:16', '2025-10-31 23:36:16'),
(10, 'Tripod Remote', 'tripod', 10000.00, '1761928608-Tripod.jpg', NULL, 0, NULL, '2025-10-31 23:36:48', '2025-10-31 23:36:48'),
(12, 'Hammock', 'hammock', 20000.00, '1761931392-hammock.jpg', NULL, 11, NULL, '2025-11-01 00:23:12', '2025-11-01 10:01:52'),
(14, 'Flsheet', 'Flysheet', 25000.00, '1761970365-Flysheet.jpg', NULL, 15, NULL, '2025-11-01 11:12:45', '2025-11-01 11:12:45'),
(18, '3 Kursi', '', 25000.00, '1761972801-Kursi Lipat.jpg', '', 11, 2, '2025-11-01 11:53:21', '2025-11-01 12:08:59'),
(19, '3 Kursi + Meja', '', 30000.00, '1761972879-Kursi Lipat.jpg', '', 12, 2, '2025-11-01 11:54:39', '2025-11-01 11:54:39'),
(20, '3 Kursi + Tripod Remote ', '', 30000.00, '1761973199-Kursi Lipat.jpg', '', 13, 2, '2025-11-01 11:59:59', '2025-11-01 11:59:59'),
(21, '3 Kursi + Tripod Remote + Meja', '', 35000.00, '1761973232-Kursi Lipat.jpg', '', 10, 2, '2025-11-01 12:00:32', '2025-11-01 12:00:32'),
(22, '2 Kursi', '', 15000.00, '1761973281-Kursi Lipat.jpg', '', 13, 1, '2025-11-01 12:01:21', '2025-11-01 12:01:21'),
(23, '2 Kursi + Meja ', '', 25000.00, '1761973324-Kursi Lipat.jpg', '', 25000, 1, '2025-11-01 12:02:04', '2025-11-01 12:02:04'),
(24, '2 Kursi + Tripod Remote', '', 25000.00, '1761973375-Kursi Lipat.jpg', '', 0, 1, '2025-11-01 12:02:55', '2025-11-01 12:02:55'),
(25, '2 Kursi + Tripod + Meja', '', 35000.00, '1761973420-Kursi Lipat.jpg', '', 3, 1, '2025-11-01 12:03:40', '2025-11-01 12:03:40'),
(26, '4 kursi', '', 35000.00, '1761973490-Kursi Lipat.jpg', '', 2, 3, '2025-11-01 12:04:50', '2025-11-01 12:22:02'),
(27, '4 Kursi + meja', '', 40000.00, '1761973531-Kursi Lipat.jpg', '', 3, 3, '2025-11-01 12:05:31', '2025-11-01 12:05:31'),
(28, '4 Kursi + meja', '', 40000.00, '1761973531-Kursi Lipat.jpg', '', 3, 3, '2025-11-01 12:05:31', '2025-11-01 12:05:31'),
(29, '4 Kursi + Tripod Remote', '', 40000.00, '1761973589-Kursi Lipat.jpg', '', 3, 3, '2025-11-01 12:06:29', '2025-11-01 12:06:29'),
(30, '4 kursi  + Tripod + Meja', '', 50000.00, '1761973629-Kursi Lipat.jpg', '', 3, 3, '2025-11-01 12:07:09', '2025-11-01 12:07:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `paket_grup`
--
ALTER TABLE `paket_grup`
  ADD PRIMARY KEY (`group_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `paket_grup`
--
ALTER TABLE `paket_grup`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
