-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 06.02.2025 klo 12:40
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
-- Rakenne taululle `arvostelut`
--

CREATE TABLE `arvostelut` (
  `id` int(11) NOT NULL,
  `tuote_id` int(11) NOT NULL,
  `nimi` varchar(255) NOT NULL,
  `sähköposti` varchar(255) NOT NULL,
  `otsikko` varchar(255) NOT NULL,
  `kommentti` text NOT NULL,
  `tähtiarvostelu` int(1) NOT NULL,
  `luotu` timestamp NOT NULL DEFAULT current_timestamp(),
  `arvosana` int(11) NOT NULL,
  `kieli` enum('fi','en') NOT NULL DEFAULT 'fi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vedos taulusta `arvostelut`
--

INSERT INTO `arvostelut` (`id`, `tuote_id`, `nimi`, `sähköposti`, `otsikko`, `kommentti`, `tähtiarvostelu`, `luotu`, `arvosana`, `kieli`) VALUES
(20, 39, 'Roope', 'roop@gmail.com', 'Wokkipannu', 'Maukkaat wokkiruoat on tulleet tällä pannulla!', 4, '2025-01-16 11:47:37', 0, 'fi'),
(21, 33, 'Saara', 'SaaraVuoko@email.com', 'Veitsinsetti', 'Kaikki veitset ovat todella hyviä ruoanlaitossa! Kestävä materiaalikin.', 4, '2025-01-20 07:11:04', 0, 'fi'),
(22, 35, 'Johannes', 'JohannesKoivisto@hotmail.com', 'Teräskattila', 'Vahva kunnon teräs! Helppo tehdä ruokaa.', 5, '2025-01-20 07:27:16', 0, 'fi'),
(23, 32, 'Pasi', 'PasiKauljumaa@gmail.com', 'Aterinsetti (hopeinen)', 'Tällä on monesti pidetty juhlia ja on ollut hyvä setti!', 3, '2025-01-20 07:33:14', 0, 'fi'),
(24, 39, 'Sofi', 'SofiMaalima@hotmail.fi', 'Wokkipannu', 'Dietti lähtenyt hyvin käyntiin, kun on saanut tällä tehtyä maukasta wokkiruokaa!', 3, '2025-01-20 07:37:44', 0, 'fi'),
(33, 26, 'Waltteri', 'WalterGamer898@gmail.com', 'Blender', 'Hinta laatu suhde ei ole hyvä!', 1, '2025-01-20 07:48:26', 0, 'fi'),
(34, 27, 'Ellen', 'jajjajaja@kaaja.com', 'Uunin arvostelu', 'Tämä uuni on todella hyvä!!! Suosittelen uunia kaikille, jotka haluavat toimivan ja upean näköisen uunin mikä sopii joka sisustukseen. KIITOS', 5, '2025-01-23 06:14:12', 0, 'fi'),
(35, 33, 'Johan Dealerson', 'DealJohan@email.com', 'Knife set', 'Really good and reliable! I recomend these.', 4, '2025-01-30 10:25:37', 0, 'en'),
(42, 28, 'Brian Smith', 'BrianSmith@email.com', 'Mikrowave', 'Very good micorwave warm foor in no time!', 4, '2025-01-30 10:48:55', 0, 'en'),
(43, 39, 'Katelyn Carlson', 'CarlsonKatlyn@gmail.com', 'Wok pan', 'absolutely MAGNIFICENT food!!', 5, '2025-01-30 11:12:21', 0, 'en'),
(55, 31, 'Seth Rockan', 'RockanSe@email.com', 'Cutlery set', 'really good for daily use! Beautiful design .', 4, '2025-01-30 11:46:47', 0, 'en'),
(56, 40, 'Fanni Luoja', 'FanniLuo@gmail.com', 'Moccamaster', 'Kallis mutta joka pennin arvoinen! Päivittäin hyvät kahvit.', 5, '2025-01-30 11:48:42', 0, 'fi'),
(57, 31, 'Greg', 'GregWellington@gmail.cok', 'Cutlery set', 'Very good product. LOved it!', 4, '2025-02-06 06:54:18', 0, 'en'),
(71, 33, 'Elisabeth', 'queen123dead@gmail.com', 'Review of knifesetting lols', 'A very mid product. I wouldn\\\'t buy this for anyone but if you need to get someone unalived, this is a good choice. :)', 2, '2025-02-06 10:29:14', 0, 'en');

-- --------------------------------------------------------

--
-- Rakenne taululle `kategoriat`
--

CREATE TABLE `kategoriat` (
  `id` int(11) NOT NULL,
  `nimi` varchar(255) NOT NULL,
  `nimi_en` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vedos taulusta `kategoriat`
--

INSERT INTO `kategoriat` (`id`, `nimi`, `nimi_en`) VALUES
(1, 'Elektroniikka', 'Electronics'),
(2, 'Pannut ja kattilat', 'Pans and pots'),
(3, 'Pienet tuotteet', 'Small products');

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
  `password` varchar(100) NOT NULL,
  `language` varchar(2) NOT NULL DEFAULT 'fi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vedos taulusta `members`
--

INSERT INTO `members` (`member_id`, `firstname`, `lastname`, `email`, `address`, `phonenumber`, `username`, `password`, `language`) VALUES
(1, 'Teppo-sakari', 'Testiniemi', 'teppotestausta@jokuemail.com', 'jokukatu 9', '+123 45 67894', 'teppo5569', '827ccb0eea8a706c4c34a16891f84e7b', 'fi'),
(2, 'Thomasio', 'Testeringus', 'tomtester@somethinsmail.com', 'someplace street 15', '+123 456 7896', 'tomtesterXD56', 'cf9d344afc8a2061ce216ae59e691b9c', 'fi'),
(3, 'Rianna', 'Sarajärvi', 's3sari00@students.osao.fi', 'koti', '345 67', 'Rianna', '6530e95e3fcf785ea9febde39f567630', 'fi'),
(4, 'admin', 'admin', 'admin@adminemail.com', 'no', '+123 654 7891', 'admin', '1e783b87df681e37f6456f64cb9fadd8', 'fi'),
(6, 'Greg', 'Wellington', 'GregWellington@gmail.cok', 'Home', '12345', 'GregWell', '646c490db8f1951b717be6816af287e0', 'fi');

-- --------------------------------------------------------

--
-- Rakenne taululle `tilaukset`
--

CREATE TABLE `tilaukset` (
  `order_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `Maksutapa` varchar(20) NOT NULL,
  `Toimitustapa` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vedos taulusta `tilaukset`
--

INSERT INTO `tilaukset` (`order_id`, `member_id`, `total_price`, `order_date`, `Maksutapa`, `Toimitustapa`) VALUES
(1, 2, 685.00, '2024-12-19 10:46:00', 'Kortti', 'Nouto'),
(2, 3, 85.00, '2025-01-09 06:39:59', 'Lasku', 'Nouto'),
(3, 3, 70.00, '2025-01-09 09:13:03', 'Kortti', 'Nouto'),
(4, 2, 655.00, '2025-01-09 11:17:17', 'Lasku', 'Nouto'),
(5, 2, 2100.00, '2025-01-09 11:18:31', 'Kortti', 'Postitus'),
(6, 2, 50.00, '2025-01-13 07:50:32', 'Lasku', 'Postitus'),
(7, 2, 20.00, '2025-01-13 08:03:41', 'Kortti', 'Postitus'),
(8, 2, 650.00, '2025-01-13 08:12:56', 'Lasku', 'Postitus'),
(9, 2, 1500.00, '2025-01-13 08:51:35', 'Kortti', 'Postitus'),
(10, 2, 85.00, '2025-01-13 08:53:43', 'Lasku', 'Nouto'),
(11, 2, 155.00, '2025-01-13 08:55:32', 'Kortti', 'Postitus'),
(12, 2, 1500.00, '2025-01-13 09:57:31', 'Lasku', 'Nouto'),
(13, 2, 52.00, '2025-01-13 11:21:00', 'Kortti', 'Postitus'),
(14, 2, 100.00, '2025-01-13 11:30:05', 'Lasku', 'Nouto'),
(15, 2, 109.00, '2025-01-13 11:45:02', 'Kortti', 'Postitus'),
(16, 2, 150.00, '2025-01-13 11:57:14', 'Kortti', 'Nouto'),
(17, 2, 255.00, '2025-01-13 11:58:56', 'Lasku', 'Postitus'),
(18, 3, 50.00, '2025-01-16 06:15:43', 'Lasku', 'Nouto'),
(19, 3, 500.00, '2025-01-16 06:37:57', 'Lasku', 'Postitus'),
(20, 3, 1500.00, '2025-01-20 10:39:44', 'Lasku', 'Nouto'),
(21, 6, 600.00, '2025-02-03 12:20:52', 'Lasku', 'Postitus'),
(22, 6, 300.00, '2025-02-06 06:44:21', 'Kortti', 'Postitus'),
(23, 6, 300.00, '2025-02-06 06:50:18', 'Lasku', 'Nouto'),
(24, 6, 50.00, '2025-02-06 06:53:15', 'Lasku', 'Nouto');

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
(5, 28, 1, 600.00),
(6, 38, 1, 50.00),
(7, 36, 1, 20.00),
(8, 28, 1, 600.00),
(8, 31, 1, 50.00),
(9, 27, 1, 1500.00),
(10, 29, 1, 85.00),
(11, 31, 1, 50.00),
(11, 33, 1, 20.00),
(11, 29, 1, 85.00),
(12, 27, 1, 1500.00),
(13, 39, 1, 52.00),
(14, 38, 2, 50.00),
(15, 34, 2, 12.00),
(15, 29, 1, 85.00),
(16, 31, 3, 50.00),
(17, 29, 3, 85.00),
(18, 38, 1, 50.00),
(19, 26, 1, 500.00),
(20, 27, 1, 1500.00),
(21, 28, 1, 600.00),
(22, 40, 1, 300.00),
(23, 40, 1, 300.00),
(24, 31, 1, 50.00);

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
(39, 2),
(40, 1);

-- --------------------------------------------------------

--
-- Rakenne taululle `tuotteet`
--

CREATE TABLE `tuotteet` (
  `id` int(11) NOT NULL,
  `nimi` varchar(255) NOT NULL,
  `nimi_en` varchar(255) DEFAULT NULL,
  `kuvaus` text DEFAULT NULL,
  `kuvaus_en` text DEFAULT NULL,
  `hinta` decimal(10,2) NOT NULL,
  `kuva` varchar(255) DEFAULT NULL,
  `varastomäärä` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vedos taulusta `tuotteet`
--

INSERT INTO `tuotteet` (`id`, `nimi`, `nimi_en`, `kuvaus`, `kuvaus_en`, `hinta`, `kuva`, `varastomäärä`) VALUES
(26, 'Blenderi', 'Blender', 'Vain sileitä smoothieita!', 'For smooth smoothies only!', 500.00, 'kuvat/blender.jpg', 4),
(27, 'Uuni', 'Oven', 'Uuni jolla saat täyteläisen ja rapean tuloksen!', 'Oven that gives you a full and crisp result!', 1500.00, 'kuvat/uuni.png', 6),
(28, 'mikroaaltouuni', 'Microwave', 'Lämmitä ruokasi nopeaa ja tehokkaasti!', 'Warm up your food fast and efficient!', 600.00, 'kuvat/mikroaaltouuni.jpg', 3),
(29, 'Sauvasekoitin', 'Immersion blender', 'Tehokas sauvasekoitin jolla saat sileän sekä paukuttoman sopan!', 'Efficient immersion blender that gives you a smooth and spotless result!', 85.00, 'kuvat/sauvasekoitin.jpg', 5),
(31, 'Aterin setti', 'Stainless Steel Cutlery Set', 'Ruostumaton teräs aterin setti. 10 kpl jokaista. Isot lusikat, pienet lusikat, haarukat, sekä veitset.', 'Stainless steel cutlery set. 10 pieces of each. Large spoons, small spoons, forks, and knives.', 50.00, 'kuvat/aterinsetti.jpg', 8),
(32, 'Aterin setti', 'Silver Cutlery Set', 'Hopeinen aterin setti. 5 kpl jokaista. Pienet lusikat, isot lusikat, haarukat ja veitset.', 'Silver cutlery set. 5 pieces of each. Small spoons, large spoons, forks, and knives.', 50.00, 'kuvat/HopeinenAterinsetti.jpg', 10),
(33, 'Veitsisetti', 'Knife Set', '6 erilaista veitseä. 20-10cm pituus väli.', '6 different types of knives. 10-20 cm in length.', 20.00, 'kuvat/veitsisetti.jpg', 7),
(34, 'Keittiövälinesetti', 'Kitchen tools', '14 eri keittiötarviketta. Lisäksi keittiövälinepidike.', '14 different kitchen tools. Additionally, a kitchen utensil holder.', 12.00, 'kuvat/keittiovalineet.jpg', 3),
(35, 'Kattila', 'Stainless steel pot', 'Kiiltävä teräskattila.', 'Shiny stainless steel pot.', 15.00, 'kuvat/kattila.jpg', 6),
(36, 'Kattila', 'Non-stick pot', 'Musta kahvallinen kattila, tarttumaton pinta. Sisältää kannen.', 'Black pot with handle, non-stick surface. Includes lid.', 20.00, 'kuvat/kattilamusta.jpg', 10),
(38, 'Valurauta paistinpannu', 'Iron frying pan', 'Valurauta paistinpannu 28cm. ', 'Cast iron frying pan 28cm', 50.00, 'kuvat/paistinpannu.jpg', 4),
(39, 'Wokkipannu', 'Wok pan', 'Wokkipannu kasvisten paistoon!', 'Wok pan for frying vegetablesand other ingridients!', 52.00, 'kuvat/wokkipannu.jpg', 15),
(40, 'Kahvinkeitin', 'Coffee maker', 'Moccamaster kahvinkeitin, maukkaat kahvit päivittäin!', 'Moccamaster coffee maker, delicious coffee daily!', 300.00, 'kuvat/moccamaster.jpg', 14);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `arvostelut`
--
ALTER TABLE `arvostelut`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tuote_id` (`tuote_id`);

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
-- AUTO_INCREMENT for table `arvostelut`
--
ALTER TABLE `arvostelut`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `kategoriat`
--
ALTER TABLE `kategoriat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tilaukset`
--
ALTER TABLE `tilaukset`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `tuotteet`
--
ALTER TABLE `tuotteet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- Rajoitteet vedostauluille
--

--
-- Rajoitteet taululle `arvostelut`
--
ALTER TABLE `arvostelut`
  ADD CONSTRAINT `arvostelut_ibfk_1` FOREIGN KEY (`tuote_id`) REFERENCES `tuotteet` (`id`) ON DELETE CASCADE;

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
