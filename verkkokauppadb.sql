-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 02.12.2024 klo 10:44
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
-- Rakenne taululle `tuotteet`
--

CREATE TABLE `tuotteet` (
  `id` int(11) NOT NULL,
  `nimi` varchar(255) NOT NULL,
  `kuvaus` text DEFAULT NULL,
  `hinta` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- AUTO_INCREMENT for table `tuotteet`
--
ALTER TABLE `tuotteet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
