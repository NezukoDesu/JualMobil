-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2025 at 08:31 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.4.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jualmobil`
--

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `senderId` int(11) NOT NULL,
  `receiverId` int(11) NOT NULL,
  `message` text NOT NULL,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `senderId`, `receiverId`, `message`, `createdAt`, `updatedAt`) VALUES
(1, 11, 12, 'halo', '2025-05-26 18:30:54', '2025-05-26 18:30:54'),
(2, 12, 11, 'halo', '2025-05-26 18:33:56', '2025-05-26 18:33:56'),
(3, 12, 10, 'halo', '2025-05-26 18:34:00', '2025-05-26 18:34:00'),
(4, 10, 12, 'halo', '2025-05-26 18:36:35', '2025-05-26 18:36:35'),
(5, 11, 12, 'Halo Apa Kabar', '2025-05-26 18:36:55', '2025-05-26 18:36:55'),
(6, 12, 11, 'Halo', '2025-05-26 18:37:14', '2025-05-26 18:37:14'),
(7, 11, 12, 'halo', '2025-05-26 18:39:11', '2025-05-26 18:39:11'),
(8, 12, 11, 'halo', '2025-05-26 18:39:18', '2025-05-26 18:39:18'),
(9, 12, 11, 'halo bos', '2025-05-26 18:41:45', '2025-05-26 18:41:45'),
(10, 11, 12, 'halo dek', '2025-05-26 18:41:53', '2025-05-26 18:41:53'),
(11, 12, 11, 'halo pak', '2025-05-26 18:43:36', '2025-05-26 18:43:36'),
(12, 11, 12, 'halo pak', '2025-05-26 18:43:41', '2025-05-26 18:43:41'),
(13, 12, 11, 'tes', '2025-05-26 18:43:45', '2025-05-26 18:43:45'),
(14, 11, 12, 'tes', '2025-05-26 18:46:40', '2025-05-26 18:46:40'),
(15, 12, 11, 'lala', '2025-05-26 18:46:43', '2025-05-26 18:46:43'),
(16, 10, 12, 'Halo Bro', '2025-05-26 18:47:06', '2025-05-26 18:47:06'),
(17, 12, 10, 'apa kabar', '2025-05-26 18:47:12', '2025-05-26 18:47:12'),
(18, 10, 12, 'hehe', '2025-05-26 18:47:16', '2025-05-26 18:47:16');

-- --------------------------------------------------------

--
-- Table structure for table `mobil`
--

CREATE TABLE `mobil` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `stok` int(11) DEFAULT NULL,
  `harga` int(11) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mobil`
--

INSERT INTO `mobil` (`id`, `nama`, `stok`, `harga`, `keterangan`, `gambar`) VALUES
(1, 'Chery Omoda GT', 20, 518800000, 'Chery OMODA 5 GT adalah varian tertinggi dari keluarga OMODA 5, yang ditawarkan dengan mesin bensin 1.600 cc Turbo, transmisi 7-Speed Dual Clutch (DCT), dan penggerak roda depan (FWD) atau semua roda (AWD).', 'omoda gt.jpg'),
(3, 'Chery J6', 20, 595800000, 'Chery J6 adalah mobil SUV listrik dari Chery yang menawarkan desain boxy yang unik, kemampuan off-road, dan berbagai fitur modern. Mobil ini hadir dalam beberapa varian, termasuk RWD dan IWD, dengan pilihan baterai berkapasitas berbeda yang mempengaruhi jarak tempuh.', 'j6.jpg'),
(6, 'Chery Omoda E5', 20, 518800000, 'Omoda E5 EV adalah mobil listrik yang diproduksi oleh Chery. Mobil ini memiliki desain SUV, panjang 4424 mm, lebar 1830 mm, dan tinggi 1588 mm, serta ground clearance 190 mm, cocok untuk penggunaan di perkotaan. Omoda E5 EV menggunakan tenaga listrik dan memiliki jangkauan hingga 430 km (WLTP) atau 505 km (NEDC). ', 'omoda ev.jpg'),
(7, 'Chery Tiggo Cross', 20, 299800000, 'Chery Tiggo Cross adalah crossover 5-seater yang tersedia dalam dua varian, Comfort dan Premium, dengan harga mulai dari Rp 259,5 juta untuk Comfort dan Rp 289,5 juta untuk Premium. Tiggo Cross memiliki mesin 1.500 cc naturally aspirated 4 silinder yang menghasilkan 114 HP dan torsi 138 Nm, dengan transmisi CVT. ', 'tigoo cross.jpg'),
(8, 'Chery Tiggo 8 CSH', 20, 529800000, 'Tiggo 8 CSH adalah SUV 7-seater dengan teknologi hybrid yang menggabungkan mesin bensin dan motor listrik, memberikan performa yang bertenaga dan irit. Ia memiliki sistem pengisian daya listrik dan dapat dioperasikan dalam mode EV (listrik murni) hingga 90 km. Tiggo 8 CSH juga dilengkapi dengan berbagai fitur canggih, seperti layar 15,6 inci 2.5K high-definition, kamera 540 HD Panoramic, dan pengisian daya nirkabel 50W. ', 'tigoo 8 csh.jpg'),
(12, 'Chery Omoda Z', 20, 368800000, 'Chery Omoda 5 Z adalah sebuah crossover 5 kursi dari Chery yang dijual di Indonesia dengan harga mulai dari Rp 334 juta (OTR Jakarta). Mobil ini ditenagai mesin 1,5L TCI yang menghasilkan tenaga 145 hp dan torsi 230 Nm, dengan transmisi 9 CVT. ', 'omoda z.jpg'),
(13, 'Chery Omoda RZ', 20, 435800000, 'Chery Omoda 5 RZ adalah varian tertinggi dari SUV kompak Chery Omoda 5. Varian ini menawarkan spesifikasi dan fitur yang lebih lengkap dibandingkan dengan varian Z, seperti mesin 1.5L turbo, transmisi 9-speed CVT, dan berbagai fitur kenyamanan serta keamanan. Omoda 5 RZ juga memiliki desain eksterior dan interior yang lebih mewah dan stylish. ', 'omoda rz.jpg'),
(14, 'Chery Tiggo 5X', 20, 299800000, 'Chery Tiggo 5X adalah sebuah SUV kompak yang menawarkan desain modern, fitur canggih, dan performa andal, cocok untuk mobilitas sehari-hari maupun perjalanan antar kota. Mobil ini memiliki mesin 1.500 cc bensin yang menghasilkan tenaga 112 HP dan torsi 138 Nm, dipadukan dengan transmisi otomatis 6 percepatan CVT. ', 'tigoo 5x.jpg'),
(15, 'Chery Tiggo 8', 20, 399800000, 'Chery Tiggo 8 adalah sebuah SUV 7-seater yang dikenal dengan dimensi yang luas dan kenyamanan kabin yang baik, serta performa mesin yang responsif. Tiggo 8 tersedia dalam beberapa varian, termasuk varian Premium dan Pro. Mesinnya, 1.6 TGDI, mampu menghasilkan tenaga 186 HP dan torsi 290 Nm. Tiggo 8 juga dikenal dengan fitur-fitur keamanan yang lengkap, seperti central locking, power door locks, dan anti-theft device. ', 'Tigoo 8.jpg'),
(17, 'Chery Tiggo 8 Pro', 20, 598800000, 'Chery Tiggo 8 Pro adalah sebuah SUV yang menawarkan kombinasi antara desain mewah, ruang kabin yang luas, dan performa yang bertenaga. Mobil ini dilengkapi dengan mesin 2.0L TGDI yang mampu menghasilkan tenaga hingga 250 HP, serta transmisi 7-Speed Dual Clutch,', 'tigoo 8 pro.jpg'),
(18, 'Chery Tiggo 7 Pro', 20, 299800000, 'Chery Tiggo 7 Pro adalah sebuah SUV kompak yang menawarkan kombinasi antara performa yang baik, interior yang nyaman, dan beragam fitur canggih. Mobil ini hadir dengan mesin 1.5 TCI yang menghasilkan tenaga 155 hp dan torsi 230 Nm, serta transmisi CVT yang halus dan responsif. Spesifikasi lain termasuk dimensi 4500 mm x 1842 mm x 1705 mm, kapasitas 5 tempat duduk, dan beberapa fitur seperti sunroof panoramik, ventilated rear seats, dan engine start stop button. ', 'tigoo 7 pro.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `type` enum('chat','rental','approval') NOT NULL,
  `message` varchar(255) NOT NULL,
  `isRead` tinyint(1) DEFAULT 0,
  `targetId` int(11) DEFAULT NULL,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `itemId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `status` enum('Menunggu','Disetujui','Ditolak') NOT NULL DEFAULT 'Menunggu',
  `createdAt` datetime DEFAULT current_timestamp(),
  `updatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `itemId`, `userId`, `status`, `createdAt`, `updatedAt`) VALUES
(31, 3, 10, 'Disetujui', '2025-04-29 15:16:06', '2025-04-29 15:16:40'),
(32, 3, 10, 'Disetujui', '2025-05-05 22:56:09', '2025-05-05 22:56:28'),
(33, 3, 10, 'Disetujui', '2025-05-05 22:58:41', '2025-05-26 11:11:59'),
(34, 3, 26, 'Disetujui', '2025-05-26 11:11:26', '2025-05-26 11:11:52'),
(35, 1, 26, 'Ditolak', '2025-05-26 11:11:33', '2025-05-26 11:11:55'),
(36, 1, 26, 'Disetujui', '2025-05-27 13:18:20', '2025-05-27 13:18:34'),
(37, 1, 26, 'Ditolak', '2025-05-27 13:18:48', '2025-05-27 13:19:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `username`, `password`, `role`, `foto`) VALUES
(6, 'a@gmail.com', 'a', '$2y$12$NPh3B2Z76ZuS0g29Rjs3EOwDAj.IsvbdcULRBxnE.vSVLKm5yM2oq', 'Super Admin', '68061e94d8df1.jpg'),
(7, '', 'Admin', '$2y$12$maz7Zns646z1tlSBS09bQuSlR.WDNz4zTE.zz/m2edaf0DvZrIfIW', 'Super Admin', ''),
(10, 'Customer@gmail.com', 'Customer', '$2y$12$fSKNENrOBsar1Dus9zQKueHhSMtA61Vwx7KnuurJaMYD5OzzZUvRe', 'Customer', '6805221272060.jpg'),
(11, '', 'Manager', '$2y$12$9AGEJU2Ix/Bxv1XSFrNs4ejbg3xekF31VQLVhaSC8hHgetDNDkeTe', 'Manager', ''),
(12, '', 'Sales', '$2y$12$/9CNb0xXP.RH24LmdrEzv.gBwd7y5oT9.XaNxmbZeeGON0rFJSiSG', 'Sales', ''),
(25, 'tes@gmail.com', 'tes', '$2y$12$/pkXXGV2Vkbgy6ly3Vyxk.qsgnJmQEi/0qsYG078AQH26KVALe6pi', 'Customer', ''),
(26, 'b@gmail.com', 'b', '$2y$12$v9msQdwNW97CsKA.QYvtUOc8ok.C9MSmaWgJjxHpfyDjcxV6ktPQO', 'Customer', '6833e9e4a436d_QRCode_6QYBoN.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `senderId` (`senderId`),
  ADD KEY `receiverId` (`receiverId`);

--
-- Indexes for table `mobil`
--
ALTER TABLE `mobil`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `itemId` (`itemId`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `mobil`
--
ALTER TABLE `mobil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`senderId`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiverId`) REFERENCES `users` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`);

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`itemId`) REFERENCES `mobil` (`id`),
  ADD CONSTRAINT `requests_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
