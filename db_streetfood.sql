-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2026 at 02:01 PM
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
-- Database: `db_streetfood`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `tambah_review` (IN `p_id_toko` INT, IN `p_nama` VARCHAR(100), IN `p_rating` INT, IN `p_komentar` TEXT)   BEGIN

    INSERT INTO review
    (id_toko, nama_pengulas, rating, komentar)
    VALUES
    (p_id_toko, p_nama, p_rating, p_komentar);

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `bayar`
--

CREATE TABLE `bayar` (
  `id_metode` int(11) NOT NULL,
  `metode_pembayaran` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bayar`
--

INSERT INTO `bayar` (`id_metode`, `metode_pembayaran`, `logo`) VALUES
(1, 'GoPay', '1778989744-c74065540ccade0683a869b622cdc4a6.jpg'),
(2, 'OVO', '1778989778-61c98a1dffc2e04424d592564cef941f.jpg'),
(3, 'DANA', '1778989819-cbaa0388892e0a154353c2a1cb8b3fee.jpg'),
(4, 'ShopeePay', '1778989866-a6cbe6a3c5e9b03ef09ebfc0969323d2.jpg'),
(5, 'Uang Tunai', '1778990035-8f34360bb7a1a91b4b0ba9452f11ae08.jpg'),
(6, 'Qris', '1779272475-988827ba70ba45ec5fb9c36423d8d09e.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `kategori_makanan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `kategori_makanan`) VALUES
(1, 'Makanan berat'),
(2, 'Cemilan'),
(3, 'Minuman'),
(4, 'Dessert');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id_menu` int(11) NOT NULL,
  `nama_menu` varchar(255) DEFAULT NULL,
  `foto_menu` text NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` int(11) DEFAULT NULL,
  `rasa` enum('pedas','asin','manis','berkuah','asam') DEFAULT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `id_toko` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id_menu`, `nama_menu`, `foto_menu`, `deskripsi`, `harga`, `rasa`, `id_kategori`, `id_toko`) VALUES
(1, 'contoh', '1779278418_gofood.png', '                dsnkjncjkdscbsd                ', 15000, 'pedas', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `metode_toko`
--

CREATE TABLE `metode_toko` (
  `id_toko` int(11) NOT NULL,
  `id_metode` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `metode_toko`
--

INSERT INTO `metode_toko` (`id_toko`, `id_metode`) VALUES
(1, 1),
(1, 5),
(1, 6);

-- --------------------------------------------------------

--
-- Table structure for table `mitra`
--

CREATE TABLE `mitra` (
  `id_mitra` int(11) NOT NULL,
  `nama_mitra` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mitra`
--

INSERT INTO `mitra` (`id_mitra`, `nama_mitra`, `logo`) VALUES
(1, 'GoFood', '1778989503-02e93f409c073b09f20262ed135fccca.png'),
(2, 'GrabFood', '1778989583-bfde3f38cf9202d49795528ad2045211.jpg'),
(3, 'ShopeeFood', '1778989601-6148d61d91688acaa7208499af9a378c.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `id_review` int(11) NOT NULL,
  `id_toko` int(11) DEFAULT NULL,
  `nama_pengulas` varchar(100) DEFAULT NULL,
  `rating` int(1) DEFAULT NULL,
  `komentar` text DEFAULT NULL,
  `tanggal_review` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `review`
--
DELIMITER $$
CREATE TRIGGER `trg_update_rating` AFTER INSERT ON `review` FOR EACH ROW BEGIN

    UPDATE toko
    SET rating = (
        SELECT AVG(rating)
        FROM review
        WHERE id_toko = NEW.id_toko
    )
    WHERE id_toko = NEW.id_toko;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id_role` int(11) NOT NULL,
  `nama_role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `toko`
--

CREATE TABLE `toko` (
  `id_toko` int(11) NOT NULL,
  `nama_toko` varchar(255) NOT NULL,
  `foto_outlet` text NOT NULL,
  `lokasi` text DEFAULT NULL,
  `jam_buka` time DEFAULT NULL,
  `jam_tutup` time DEFAULT NULL,
  `status_halal` enum('tersertifikasi','belum tersertifikasi','non halal') DEFAULT NULL,
  `no_telepon` varchar(14) DEFAULT NULL,
  `rating` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `toko`
--

INSERT INTO `toko` (`id_toko`, `nama_toko`, `foto_outlet`, `lokasi`, `jam_buka`, `jam_tutup`, `status_halal`, `no_telepon`, `rating`) VALUES
(1, 'contoh', 'gofood.png', 'hdhasjhjsabdjhcbsbd', '07:00:00', '22:00:00', 'belum tersertifikasi', '089999999999', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `toko_mitra`
--

CREATE TABLE `toko_mitra` (
  `id_toko` int(11) NOT NULL,
  `id_mitra` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `toko_mitra`
--

INSERT INTO `toko_mitra` (`id_toko`, `id_mitra`) VALUES
(1, 1),
(1, 2),
(1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `id_role` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bayar`
--
ALTER TABLE `bayar`
  ADD PRIMARY KEY (`id_metode`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `id_toko` (`id_toko`);

--
-- Indexes for table `metode_toko`
--
ALTER TABLE `metode_toko`
  ADD PRIMARY KEY (`id_toko`,`id_metode`),
  ADD KEY `id_metode` (`id_metode`);

--
-- Indexes for table `mitra`
--
ALTER TABLE `mitra`
  ADD PRIMARY KEY (`id_mitra`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id_review`),
  ADD KEY `id_toko` (`id_toko`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`);

--
-- Indexes for table `toko`
--
ALTER TABLE `toko`
  ADD PRIMARY KEY (`id_toko`);

--
-- Indexes for table `toko_mitra`
--
ALTER TABLE `toko_mitra`
  ADD PRIMARY KEY (`id_toko`,`id_mitra`),
  ADD KEY `id_mitra` (`id_mitra`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `id_role` (`id_role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bayar`
--
ALTER TABLE `bayar`
  MODIFY `id_metode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mitra`
--
ALTER TABLE `mitra`
  MODIFY `id_mitra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `id_review` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `toko`
--
ALTER TABLE `toko`
  MODIFY `id_toko` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`),
  ADD CONSTRAINT `menu_ibfk_2` FOREIGN KEY (`id_toko`) REFERENCES `toko` (`id_toko`);

--
-- Constraints for table `metode_toko`
--
ALTER TABLE `metode_toko`
  ADD CONSTRAINT `metode_toko_ibfk_1` FOREIGN KEY (`id_toko`) REFERENCES `toko` (`id_toko`),
  ADD CONSTRAINT `metode_toko_ibfk_2` FOREIGN KEY (`id_metode`) REFERENCES `bayar` (`id_metode`);

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`id_toko`) REFERENCES `toko` (`id_toko`);

--
-- Constraints for table `toko_mitra`
--
ALTER TABLE `toko_mitra`
  ADD CONSTRAINT `toko_mitra_ibfk_1` FOREIGN KEY (`id_toko`) REFERENCES `toko` (`id_toko`),
  ADD CONSTRAINT `toko_mitra_ibfk_2` FOREIGN KEY (`id_mitra`) REFERENCES `mitra` (`id_mitra`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
