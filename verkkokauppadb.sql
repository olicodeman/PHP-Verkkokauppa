-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 16.12.2024 klo 11:43
-- Palvelimen versio: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
(2, 'Keittiövälineet'),
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
(3, 'Rianna', 'Sarajärvi', 's3sari00@students.osao.fi', 'koti', '345 67', 'Rianna', '3881731093517b98cb695eed8fdaa004'),
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

-- --------------------------------------------------------

--
-- Rakenne taululle `tilaus_tuotteet`
--

CREATE TABLE `tilaus_tuotteet` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(5, 1),
(6, 1),
(25, 1);

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
(1, 'mikroaaltouuni', 'Kiiltävä mikro!', 950.00, 'kuvat/product_675187e7c5d618.38854536.jpg', 0),
(5, 'Blenderi', 'Tehokas blender maukaan smoothien ekemiseen', 150.00, 'kuvat/product_6756942f6a9ee3.20237997.jpg', 12),
(6, 'Sauvasekoitin', 'Tee maukas sose parhaalla sekoittimella!', 80.00, 'kuvat/product_6756dde323cf57.06458184.jpg', 8),
(25, 'Blenderi', 'test', 800.00, 'kuvat/blender.jpg', 7);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

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
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tilaus_tuotteet`
--
ALTER TABLE `tilaus_tuotteet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tuotteet`
--
ALTER TABLE `tuotteet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
  ADD CONSTRAINT `tilaus_tuotteet_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `tilaukset` (`order_id`),
  ADD CONSTRAINT `tilaus_tuotteet_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `tuotteet` (`id`);

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
