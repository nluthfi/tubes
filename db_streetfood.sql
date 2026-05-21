-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2026 at 03:14 AM
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
-- Database: `db_streetfood(1)`
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
(1, 'contoh', '1779278418_gofood.png', '                dsnkjncjkdscbsd                ', 15000, 'pedas', 1, 1),
(2, 'Mie Yamin Manis Komplit', 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/b12016f4-f22e-47f1-81cf-28f1aec94c21_3dfc5855-c25a-4c7d-bf6c-1442337590d5_Go-Biz_20200411_191453.jpeg?auto=format', 'Yamin Manis + BASO + PANGSIT KUAH + CEKER', 25000, NULL, NULL, NULL),
(3, 'Mie Yamin Asin Komplit', 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/8df8ba2a-2606-4115-9588-9c9f43a731ce_0f9af109-cef9-426c-ba52-a6a638f04179_Go-Biz_20200411_191433.jpeg?auto=format', 'Mie Yamin + BASO + PANGSIT KUAH + CEKER', 25000, NULL, NULL, NULL),
(4, 'Yahun Manis Komplit', 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/058dadc5-7eaa-4526-a5ea-cc675027301a_e109e954-6650-4e11-99de-f0ef9d5d4f30_Go-Biz_20200411_191346.jpeg?auto=format', 'Yahun (Yamin Bihun) Manis + BASO + PANGSIT KUAH + CEKER', 25000, NULL, NULL, NULL),
(5, 'Yahun Asin Komplit', 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/4fc05983-9ceb-478c-8ce8-678273cfb952_2ff66768-3a93-46b2-b47a-b238727321d9_Go-Biz_20200411_191409.jpeg?auto=format', 'Yahun (Yamin Bihun) Asin + BASO + PANGSIT KUAH + CEKER', 25000, NULL, NULL, NULL),
(6, 'Mie Yamin Manis Polos', 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/b7b6bbce-1d00-4175-8bfd-e42718bcba32_1a10f09e-e8fc-4cab-ace5-277b94b839e0_Go-Biz_20200411_190957.jpeg?auto=format', 'Mie Yamin manis polos + Ditaburi Ayam Cingcang', 15000, NULL, NULL, NULL),
(7, 'Mie Yamin Asin Polos', 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/13b5e046-f080-43f9-a5f8-977da1eef41b_51d92be2-56bc-4a63-8823-dbf7fbc0fe66_Go-Biz_20200411_191027.jpeg?auto=format', 'Mie Yamin asin polos + Ditaburi Ayam Cingcang', 15000, NULL, NULL, NULL),
(8, 'Yamin Bihun Manis Polos', 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/e35ab707-403e-4f2b-b3b2-13c457282ebb_17567604-1aee-434e-be82-44976e399571_Go-Biz_20200411_191050.jpeg?auto=format', 'Yamin BIHUN + Ditaburi Ayam Cingcang', 15000, NULL, NULL, NULL),
(9, 'Yamin Bihun Asin Polos', 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/812262ac-2534-403e-b2c1-5210db9e47b7_6072eead-3f41-4a0a-a910-bdbe21df8ff4_Go-Biz_20200411_191116.jpeg?auto=format', 'Yamin BIHUN + Ditaburi Ayam Cingcang', 15000, NULL, NULL, NULL),
(10, 'Baso + Pangsit Kuah', 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/c9ea8a7d-06ac-45ab-aec5-c460b936957f_Go-Biz_20221218_105247.jpeg?auto=format', '6 baso sapi asli + 4 pangsit kuah isi ayam', 22000, NULL, NULL, NULL),
(11, 'Baso Polos', 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/13c7667d-f798-4fa7-8f4d-0fc121ea4a46_Go-Biz_20221218_105200.jpeg?auto=format', '10pcs baso sapi asli + kuah dan sayur', 20000, NULL, NULL, NULL),
(12, 'Pangsit Kuah Isi Ayam', 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/327765fa-6332-47f1-9ccb-cef75bdfbf3e_7b0a516a-0b49-4bad-a279-6fb7356d1622_Go-Biz_20200411_191231.jpeg?auto=format', '10pcs pangsit kuah isi ayam', 20000, NULL, NULL, NULL),
(13, 'Baso Kecil', 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/667c63f1-a592-42e1-9948-c8a9800839c4_dbfd1b45-c75c-477d-8959-917f281171c6_Go-Biz_20200411_190757.jpeg?auto=format', '1 buah baso sapi murni', 2000, NULL, NULL, NULL),
(14, 'Pangsit Basah', 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/3605f23d-8ce1-4450-a539-f5967de9885b_c1030f59-246b-4586-b186-9fced2d831e5_Go-Biz_20200411_190742.jpeg?auto=format', '1 buah pangsit kuah isi ayam special', 2000, NULL, NULL, NULL),
(15, 'Ceker Ayam Lunak', 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/664e0f70-e77d-4d86-9e0a-17d09121816c_b9ff1c91-5255-4beb-b4b5-dbdb039d52f3_Go-Biz_20200411_190705.jpeg?auto=format', '1-2 buah ceker ayam lunak', 2000, NULL, NULL, NULL),
(16, 'Mie Jebew Komplit', '', 'Mie dengan sambal chili oil, isi pangsit 1 baso 2', 25000, NULL, NULL, NULL),
(17, 'Mie Jebew Polos', '', 'Mie dengan sambal chili oil', 18000, NULL, NULL, NULL);

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
(1, 'contoh', 'gofood.png', 'hdhasjhjsabdjhcbsbd', '07:00:00', '22:00:00', 'belum tersertifikasi', '089999999999', NULL),
(2, 'MIE BASO RESTORJA DO\'EL', '', 'Jalan Geger Kalong Girang No.65 Isola, Gegerkalong, Kec. Sukasari, Kota Bandung, Jawa Barat 40154', '08:30:00', '21:30:00', 'tersertifikasi', '', 0),
(3, 'Mie Ayam Pedas Sugih', '', 'Jl. Gegerkalong Girang No.31, Gegerkalong, Kec. Sukasari, Kota Bandung, Jawa Barat 40154', '10:30:00', '20:30:00', 'tersertifikasi', '', 0),
(4, 'AYAM JUBER - JUARA BERTAHAN - GERLONG', '', 'Jl. Gegerkalong Girang No.17, Gegerkalong, Kec. Sukasari, Kota Bandung, Jawa Barat 40153', '07:30:00', '22:00:00', 'tersertifikasi', '', 0),
(5, 'Warkop Sukarasa Gerlong', '', 'Jalan Cibeunying Kolot No.87, Sadang, Serang, Kota Bandung, Jawa Barat 40133', NULL, NULL, 'belum tersertifikasi', '', 0),
(6, 'Pawon teteh gegerkalong', '', 'Jl. Gegerkalong Tengah, Gegerkalong, Kec. Sukasari, Kota Bandung, Jawa Barat 40153', '09:00:00', '21:30:00', 'belum tersertifikasi', '', 0),
(7, 'Hayang Thai Tea Koramil', '', '4HPV+663, Jl. Guru Gantangan, Isola, Kec. Sukasari, Kota Bandung, Jawa Barat 40154', '11:00:00', '21:00:00', 'tersertifikasi', '', 0),
(8, 'AndisPIZZA', '', '4HPR+7Q Gegerkalong, Kota Bandung, Jawa Barat', NULL, NULL, 'tersertifikasi', '', 0),
(9, 'Kababoss Geger Kalong', '', '4HPR+9F8, Jl. Gegerkalong Girang, Gegerkalong, Kec. Sukasari, Kota Bandung, Jawa Barat 40153', '09:00:00', '22:00:00', 'tersertifikasi', '', 0),
(10, 'AYAMIN', '', 'Jl. Gegerkalong Girang No.20C, Gegerkalong, Kec. Sukasari, Kota Bandung, Jawa Barat 40154', '10:00:00', '23:59:00', 'tersertifikasi', '', 0),
(11, 'HAPPY nasi telor', '', '4HPV+4GJ, Jl. Gegerkalong Girang, Gegerkalong, Kec. Sukasari, Kota Bandung, Jawa Barat 40153', '05:25:00', '13:00:00', 'tersertifikasi', '', 0),
(12, 'Waroeng BANG BOIM', '', 'Jl. Gegerkalong Girang No.36, Gegerkalong, Kec. Sukasari, Kota Bandung, Jawa Barat 40153', '17:00:00', '22:00:00', 'tersertifikasi', '', 0),
(13, 'Ayam Tulang Lunak Pawon Sesambelan', '', 'Jl. Gegerkalong Girang Samping Koramil No.4, Gegerkalong, Kec. Sukasari, Kota Bandung, Jawa Barat 40153', '08:00:00', '15:00:00', 'tersertifikasi', '', 0),
(14, 'Pisang Keju & Pisang Goreng Tanduk Isola', '', '4HPV+47Q, Jl. Gegerkalong Girang, Isola, Kec. Sukasari, Kota Bandung, Jawa Barat 40153', '07:00:00', '21:30:00', 'tersertifikasi', '', 0),
(15, 'GG Juicy \'n Fruity', '', 'Jl. Gegerkalong Girang No.23, Gegerkalong, Kec. Sukasari, Kota Bandung, Jawa Barat 40153', '10:00:00', '19:00:00', 'tersertifikasi', '', 0),
(16, 'Molen Aneka Rasa', '', 'Jl. Gegerkalong Girang No.101a, Gegerkalong, Kec. Sukasari, Kota Bandung, Jawa Barat 40153', '07:00:00', '17:00:00', 'tersertifikasi', '', 0),
(17, 'Megumi daifuku mochi', '', 'Jl. Gegerkalong Girang No.99, bandung, Kec. Sukasari, Kabupaten Bandung Barat, Jawa Barat 40153', '11:15:00', '22:00:00', 'tersertifikasi', '', 0),
(18, 'Alfathir frozen food', '', 'Jl. Gegerkalong Girang No.119, Isola, Kec. Sukasari, Kota Bandung, Jawa Barat 40153', '14:00:00', '23:00:00', 'tersertifikasi', '', 0),
(19, 'Ayam Geprek Bejeuk Gerlong', '', 'Jl. Gegerkalong Girang No.123, Isola, Kec. Sukasari, Kota Bandung, Jawa Barat 40153', '11:00:00', '22:00:00', 'tersertifikasi', '', 0),
(20, 'Warung Nasi Padang 88 Uni Angel', '', '4HPR+7VC, Jl. Gegerkalong Girang, Isola, Kec. Sukasari, Kota Bandung, Jawa Barat 40154', '00:00:00', '23:59:00', 'tersertifikasi', '', 0),
(21, 'Republic Kebab Premium', '', 'Jl. Gegerkalong Girang, Gegerkalong, Kec. Sukasari, Kota Bandung, Jawa Barat 40153', '00:00:00', '23:59:00', 'tersertifikasi', '', 0),
(22, 'Chocolate Changer Gegerkalong', '', 'Jl. Gegerkalong Girang No.53 A, Gegerkalong, Kec. Sukasari, Kota Bandung, Jawa Barat 40154', '09:30:00', '20:45:00', 'tersertifikasi', '', 0),
(23, 'Ayam Geprek Bebas, Gegerkalong', '', 'Jl. Gegerkalong Girang No.33, Gegerkalong, Kec. Sukasari, Kota Bandung, Jawa Barat 40153', '08:45:00', '21:45:00', 'tersertifikasi', '', 0),
(24, 'Jus Egan 71', '', 'Jl. Gegerkalong Girang No.71, Gegerkalong, Kec. Sukasari, Kota Bandung, Jawa Barat 40153', '07:00:00', '17:30:00', 'tersertifikasi', '', 0),
(25, 'Rumah makan padang Rajo Bungsu', '', 'Jl. Gegerkalong Girang No.75, Gegerkalong, Kec. Sukasari, Kota Bandung, Jawa Barat 40153', '10:00:00', '22:00:00', 'tersertifikasi', '', 0),
(26, 'Kantin 77', '', 'Jl. Gegerkalong Girang No.77, Gegerkalong, Kec. Sukasari, Kota Bandung, Jawa Barat 40153', '07:00:00', '21:00:00', 'tersertifikasi', '', 0),
(27, 'GEBROS Gegerkalong', '', 'Jl. Gegerkalong Girang No.44, Isola, Kec. Sukasari, Kota Bandung, Jawa Barat 40153', '10:00:00', '20:00:00', 'tersertifikasi', '', 0),
(28, 'ARA FRIED CHIKEN PLACE', '', 'Jl. Gegerkalong Girang No.44, Isola, Kec. Sukasari, Kota Bandung, Jawa Barat 40153', '09:30:00', '21:00:00', 'tersertifikasi', '', 0),
(29, 'Gerlong Dimsum', '', 'Jl. Gegerkalong Girang No.48 rt01, RW.06, Isola, Kec. Sukasari, Kota Bandung, Jawa Barat 40154', '11:00:00', '20:30:00', 'tersertifikasi', '', 0),
(30, 'RM Padang Maju Jaya', '', 'Jl. Gegerkalong Girang No.72, Gegerkalong, Kec. Sukasari, Kota Bandung, Jawa Barat 40154', '09:00:00', '21:45:00', 'tersertifikasi', '', 0),
(31, 'A.I. Drinks', '', 'Jl. Gegerkalong Girang No.95, Gegerkalong, Kec. Sukasari, Kota Bandung, Jawa Barat 40154', '13:00:00', '21:00:00', 'tersertifikasi', '', 0);

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
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
  MODIFY `id_toko` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

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
