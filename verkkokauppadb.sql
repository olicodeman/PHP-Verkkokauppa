-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 09.01.2025 klo 12:43
-- Palvelimen versio: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `verkkokauppadb`
--

-- --------------------------------------------------------

--
-- Rakenne taululle `kategoriat`
--

CREATE TABLE `kategoriat` (
  `id` int(11) NOT NULL,
  `nimi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vedos taulusta `kategoriat`
--

INSERT INTO `kategoriat` (`id`, `nimi`) VALUES
(1, 'Elektroniikka'),
(2, 'Pannut ja kattilat'),
(3, 'Pienet tuotteet');

-- --------------------------------------------------------

--
-- Rakenne taululle `members`
--

CREATE TABLE `members` (
  `member_id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `address` varchar(50) NOT NULL,
  `phonenumber` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vedos taulusta `members`
--

INSERT INTO `members` (`member_id`, `firstname`, `lastname`, `email`, `address`, `phonenumber`, `username`, `password`) VALUES
(1, 'Teppo-sakari', 'Testiniemi', 'teppotestausta@jokuemail.com', 'jokukatu 9', '+123 45 67894', 'teppo5569', '827ccb0eea8a706c4c34a16891f84e7b'),
(2, 'Thomasio', 'Testeringus', 'tomtester@somethinsmail.com', 'someplace street 15', '+123 456 7896', 'tomtesterXD56', 'cf9d344afc8a2061ce216ae59e691b9c'),
(3, 'Rianna', 'Sarajärvi', 's3sari00@students.osao.fi', 'koti', '345 67', 'Rianna', '6530e95e3fcf785ea9febde39f567630'),
(4, 'admin', 'admin', 'admin@adminemail.com', 'no', '+123 654 7891', 'admin', '1e783b87df681e37f6456f64cb9fadd8');

-- --------------------------------------------------------

--
-- Rakenne taululle `tilaukset`
--

CREATE TABLE `tilaukset` (
  `order_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vedos taulusta `tilaukset`
--

INSERT INTO `tilaukset` (`order_id`, `member_id`, `total_price`, `order_date`) VALUES
(1, 2, 685.00, '2024-12-19 10:46:00'),
(2, 3, 85.00, '2025-01-09 06:39:59'),
(3, 3, 70.00, '2025-01-09 09:13:03'),
(4, 2, 655.00, '2025-01-09 11:17:17'),
(5, 2, 2100.00, '2025-01-09 11:18:31');

-- --------------------------------------------------------

--
-- Rakenne taululle `tilaus_tuotteet`
--

CREATE TABLE `tilaus_tuotteet` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vedos taulusta `tilaus_tuotteet`
--

INSERT INTO `tilaus_tuotteet` (`order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 29, 1, 85.00),
(1, 28, 1, 600.00),
(2, 29, 1, 85.00),
(3, 36, 1, 20.00),
(3, 31, 1, 50.00),
(4, 31, 1, 50.00),
(4, 26, 1, 500.00),
(4, 29, 1, 85.00),
(4, 36, 1, 20.00),
(5, 27, 1, 1500.00),
(5, 28, 1, 600.00);

-- --------------------------------------------------------

--
-- Rakenne taululle `tuote_kategoria`
--

CREATE TABLE `tuote_kategoria` (
  `tuote_id` int(11) NOT NULL,
  `kategoria_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vedos taulusta `tuote_kategoria`
--

INSERT INTO `tuote_kategoria` (`tuote_id`, `kategoria_id`) VALUES
(26, 1),
(26, 3),
(27, 1),
(28, 1),
(29, 1),
(29, 3),
(31, 3),
(32, 3),
(33, 3),
(34, 3),
(35, 2),
(36, 2),
(38, 2),
(39, 2);

-- --------------------------------------------------------

--
-- Rakenne taululle `tuotteet`
--

CREATE TABLE `tuotteet` (
  `id` int(11) NOT NULL,
  `nimi` varchar(255) NOT NULL,
  `kuvaus` text DEFAULT NULL,
  `hinta` decimal(10,2) NOT NULL,
  `kuva` varchar(255) DEFAULT NULL,
  `varastomäärä` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vedos taulusta `tuotteet`
--

INSERT INTO `tuotteet` (`id`, `nimi`, `kuvaus`, `hinta`, `kuva`, `varastomäärä`) VALUES
(26, 'Blenderi', 'Vain sileitä smoothieita!', 500.00, 'kuvat/blender.jpg', 0),
(27, 'Uuni', 'Uuni jolla saat täyteläisen ja rapean tuloksen!', 1500.00, 'kuvat/uuni.png', 9),
(28, 'mikroaaltouuni', 'Lämmitä ruokasi nopeaa ja tehokkaasti!', 600.00, 'kuvat/mikroaaltouuni.jpg', 5),
(29, 'Sauvasekoitin', 'Tehokas sauvasekoitin jolla saat sileän sekä paukuttoman sopan!', 85.00, 'kuvat/sauvasekoitin.jpg', 11),
(31, 'Aterin setti', 'Ruostumaton teräs aterin setti. 10 kpl jokaista. Isot lusikat, pienet lusikat, haarukat, sekä veitset.', 50.00, 'kuvat/aterinsetti.jpg', 14),
(32, 'Aterin setti', 'Hopeinen aterin setti. 5 kpl jokaista. Pienet lusikat, isot lusikat, haarukat ja veitset.', 50.00, 'kuvat/HopeinenAterinsetti.jpg', 10),
(33, 'Veitsisetti', '6 erilaista veitseä. 20-10cm pituus väli.', 20.00, 'kuvat/veitsisetti.jpg', 8),
(34, 'Keittiövälinesetti', '14 eri keittiötarviketta. Lisäksi keittiövälinepidike.', 12.00, 'kuvat/keittiovalineet.jpg', 5),
(35, 'Kattila', 'Kiiltävä teräskattila.', 15.00, 'kuvat/kattila.jpg', 6),
(36, 'Kattila', 'Musta kahvallinen kattila, tarttumaton pinta. Sisältää kannen.', 20.00, 'kuvat/kattilamusta.jpg', 11),
(38, 'Valurauta paistinpannu', 'Valurauta paistinpannu 28cm. ', 50.00, 'kuvat/paistinpannu.jpg', 8),
(39, 'Wokkipannu', 'Wokkipannu kasvisten paistoon!', 52.00, 'kuvat/wokkipannu.jpg', 16);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kategoriat`
--
ALTER TABLE `kategoriat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`member_id`);

--
-- Indexes for table `tilaukset`
--
ALTER TABLE `tilaukset`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `tilaus_tuotteet`
--
ALTER TABLE `tilaus_tuotteet`
  ADD KEY `fk_order_id` (`order_id`),
  ADD KEY `fk_product_id` (`product_id`);

--
-- Indexes for table `tuote_kategoria`
--
ALTER TABLE `tuote_kategoria`
  ADD PRIMARY KEY (`tuote_id`,`kategoria_id`),
  ADD KEY `kategoria_id` (`kategoria_id`);

--
-- Indexes for table `tuotteet`
--
ALTER TABLE `tuotteet`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kategoriat`
--
ALTER TABLE `kategoriat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tilaukset`
--
ALTER TABLE `tilaukset`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tuotteet`
--
ALTER TABLE `tuotteet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Rajoitteet vedostauluille
--

--
-- Rajoitteet taululle `tilaukset`
--
ALTER TABLE `tilaukset`
  ADD CONSTRAINT `tilaukset_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`);

--
-- Rajoitteet taululle `tilaus_tuotteet`
--
ALTER TABLE `tilaus_tuotteet`
  ADD CONSTRAINT `fk_order_id` FOREIGN KEY (`order_id`) REFERENCES `tilaukset` (`order_id`),
  ADD CONSTRAINT `fk_product_id` FOREIGN KEY (`product_id`) REFERENCES `tuotteet` (`id`);

--
-- Rajoitteet taululle `tuote_kategoria`
--
ALTER TABLE `tuote_kategoria`
  ADD CONSTRAINT `tuote_kategoria_ibfk_1` FOREIGN KEY (`tuote_id`) REFERENCES `tuotteet` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tuote_kategoria_ibfk_2` FOREIGN KEY (`kategoria_id`) REFERENCES `kategoriat` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
