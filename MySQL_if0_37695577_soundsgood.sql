-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Generation Time: 27.11.2024 klo 15:06
-- Palvelimen versio: 10.6.19-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_37695577_soundsgood`
--

-- --------------------------------------------------------

--
-- Rakenne taululle `band`
--

CREATE TABLE `band` (
  `band_id` int(2) NOT NULL,
  `band_name` varchar(120) NOT NULL,
  `city_code` int(2) NOT NULL,
  `country_code` int(2) NOT NULL,
  `formed_year` year(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vedos taulusta `band`
--

INSERT INTO `band` (`band_id`, `band_name`, `city_code`, `country_code`, `formed_year`) VALUES
(1, 'Aavikko', 749, 246, 1995),
(2, 'Aikakone', 91, 246, 1995),
(3, 'Aknestik', 84, 246, 1984),
(4, 'Apulanta', 111, 246, 1991),
(5, 'Charon', 678, 246, 1992),
(6, 'Children of Bodom', 49, 246, 1997),
(7, 'Dingo', 609, 246, 1982),
(8, 'Haloo Helsinki!', 91, 246, 2006),
(9, 'Hanoi Rocks', 91, 246, 1979),
(10, 'Hassisenkone', 167, 246, 1979),
(11, 'HIM', 91, 246, 1995),
(12, 'Indica', 91, 246, 2001),
(13, 'Killer', 91, 246, 1999),
(14, 'Kissa', 91, 246, 2020),
(15, 'Nightwish', 260, 246, 1996),
(16, 'Nykyaika', 91, 246, 2016),
(17, 'Nyrkkitappelu', 837, 246, 2010),
(18, 'PMMP', 91, 246, 2002),
(19, 'Poisonblack', 564, 246, 2000),
(20, 'RinneRadio', 91, 246, 1988),
(21, 'Saimaa', 398, 246, 2004),
(22, 'Sentenced', 494, 246, 1989),
(23, 'Tehosekoitin', 398, 246, 1991),
(24, 'The 69 eyes', 91, 246, 1989),
(25, 'V for Violence', 91, 246, 2007),
(26, 'Zen Café', 853, 246, 1992);

-- --------------------------------------------------------

--
-- Rakenne taululle `band_rating`
--

CREATE TABLE `band_rating` (
  `band_rating_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `band_id` int(11) NOT NULL,
  `star_rating` int(1) NOT NULL,
  `modified` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Vedos taulusta `band_rating`
--

INSERT INTO `band_rating` (`band_rating_id`, `user_id`, `band_id`, `star_rating`, `modified`) VALUES
(113, 1, 1, 5, '2024-11-16 13:15:59'),
(115, 1, 5, 5, '2024-11-16 13:16:42'),
(117, 1, 3, 4, '2024-11-16 13:16:15'),
(116, 1, 4, 4, '2024-11-16 13:16:09'),
(119, 1, 26, 4, '2024-11-16 13:16:45'),
(118, 1, 25, 5, '2024-11-16 13:16:19'),
(120, 1, 24, 5, '2024-11-16 13:16:28'),
(121, 1, 9, 5, '2024-11-16 13:16:31'),
(122, 1, 6, 5, '2024-11-16 13:16:37'),
(137, 110, 4, 5, '2024-11-16 13:25:37'),
(179, 108, 4, 4, '2024-11-26 14:00:37'),
(123, 1, 11, 5, '2024-11-16 13:16:53'),
(124, 1, 19, 5, '2024-11-16 13:17:03'),
(125, 1, 21, 5, '2024-11-16 13:17:07'),
(126, 1, 23, 5, '2024-11-16 13:17:15'),
(178, 108, 23, 5, '2024-11-26 14:00:31'),
(140, 110, 3, 5, '2024-11-16 13:25:56'),
(174, 108, 1, 5, '2024-11-26 14:30:08'),
(145, 108, 11, 4, '2024-11-16 13:26:27'),
(146, 108, 9, 5, '2024-11-16 13:26:30'),
(147, 108, 8, 5, '2024-11-16 13:26:35'),
(148, 108, 16, 5, '2024-11-16 13:26:39'),
(149, 108, 26, 4, '2024-11-16 13:26:42'),
(150, 108, 10, 4, '2024-11-16 13:26:45'),
(151, 114, 1, 4, '2024-11-16 13:27:12'),
(152, 114, 2, 4, '2024-11-16 13:27:17'),
(153, 114, 25, 5, '2024-11-16 13:27:19'),
(155, 114, 14, 4, '2024-11-16 13:27:30'),
(156, 114, 6, 4, '2024-11-16 13:27:33'),
(157, 114, 5, 4, '2024-11-16 13:27:37'),
(187, 122, 1, 5, '2024-11-27 07:45:33'),
(186, 120, 3, 4, '2024-11-27 07:22:43'),
(180, 108, 3, 4, '2024-11-26 14:00:42');

-- --------------------------------------------------------

--
-- Rakenne taululle `city`
--

CREATE TABLE `city` (
  `city_id` int(2) NOT NULL,
  `city_code` int(3) NOT NULL COMMENT 'Finnish municiplaity code or code for foreign cities',
  `city_name` varchar(200) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Vedos taulusta `city`
--

INSERT INTO `city` (`city_id`, `city_code`, `city_name`) VALUES
(1, 49, 'Espoo'),
(2, 111, 'Heinola'),
(3, 167, 'Joensuu'),
(4, 398, 'Lahti'),
(5, 494, 'Muhos'),
(6, 564, 'Oulu'),
(7, 609, 'Pori'),
(8, 678, 'Raahe'),
(9, 749, 'Siilinjärvi'),
(10, 837, 'Tampere'),
(11, 1001, 'Basildon'),
(12, 1002, 'Birmingham'),
(13, 1003, 'Düsseldorf'),
(14, 1004, 'Eskilstuna'),
(15, 1005, 'Glendale'),
(16, 1006, 'Hamburg'),
(17, 1007, 'Jelgava'),
(18, 1008, 'London'),
(19, 1009, 'Los Angeles'),
(20, 1010, 'Melbourne'),
(21, 1011, 'New Jersey'),
(22, 1012, 'New york'),
(23, 1013, 'Orlando'),
(24, 1014, 'Perth'),
(25, 1015, 'Reykjavik'),
(26, 84, 'Haukipudas'),
(27, 91, 'Helsinki'),
(28, 260, 'Kitee'),
(29, 853, 'Turku');

-- --------------------------------------------------------

--
-- Rakenne taululle `country`
--

CREATE TABLE `country` (
  `country_id` int(11) NOT NULL,
  `country_code` int(3) NOT NULL COMMENT 'ISO 3166 country code',
  `country_name` varchar(250) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Vedos taulusta `country`
--

INSERT INTO `country` (`country_id`, `country_code`, `country_name` ) VALUES
(1, 246, 'Finland'),
(2, 276, 'Germany'),
(3, 428, 'Latvia'),
(4, 752, 'Sweden'),
(5, 826, 'the United Kingdom of Great Britain and Northern Ireland'),
(6, 840, 'the United States of America'),
(7, 578, 'Norway'),
(8, 352, 'Iceland'),
(9, 36, 'Australia');

-- --------------------------------------------------------

--
-- Rakenne taululle `user_table`
--

CREATE TABLE `user_table` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(10) NOT NULL,
  `role` enum('SUPERUSER','NORMAL') NOT NULL DEFAULT 'NORMAL',
  `password` varchar(10) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Vedos taulusta `user_table`
--

INSERT INTO `user_table` (`user_id`, `user_name`, `role`, `password`, `created`, `modified`) VALUES
(114, 'Orava', 'SUPERUSER', 'Kuusenkäpy', '2024-11-16 21:27:00', '0000-00-00 00:00:00'),
(118, 'Koira', 'NORMAL', 'puruluu', '2024-11-27 00:27:31', '0000-00-00 00:00:00'),
(1, 'Annika', 'SUPERUSER', 'salasana', '2024-11-15 20:48:31', '0000-00-00 00:00:00'),
(119, 'Heppa', 'NORMAL', 'heinäpaali', '2024-11-27 00:29:39', '0000-00-00 00:00:00'),
(122, 'Kissa', 'NORMAL', 'salasana', '2024-11-27 15:44:45', '0000-00-00 00:00:00'),
(108, 'Kettu', 'NORMAL', 'Siili', '2024-11-16 20:30:45', '0000-00-00 00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `band`
--
ALTER TABLE `band`
  ADD PRIMARY KEY (`band_id`),
  ADD UNIQUE KEY `band_name` (`band_name`);

--
-- Indexes for table `band_rating`
--
ALTER TABLE `band_rating`
  ADD PRIMARY KEY (`band_rating_id`),
  ADD UNIQUE KEY `user_band` (`user_id`,`band_id`);

--
-- Indexes for table `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`city_id`),
  ADD UNIQUE KEY `city_code` (`city_code`);

--
-- Indexes for table `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`country_id`),
  ADD UNIQUE KEY `country_code` (`country_code`);

--
-- Indexes for table `user_table`
--
ALTER TABLE `user_table`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_name` (`user_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `band_rating`
--
ALTER TABLE `band_rating`
  MODIFY `band_rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=189;

--
-- AUTO_INCREMENT for table `user_table`
--
ALTER TABLE `user_table`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
