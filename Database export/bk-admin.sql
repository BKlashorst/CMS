-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 02 jun 2025 om 10:02
-- Serverversie: 10.4.32-MariaDB
-- PHP-versie: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bk-admin`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `content`
--

CREATE TABLE `content` (
  `cont_id` int(11) NOT NULL,
  `cont_name` varchar(265) DEFAULT NULL,
  `cont_content` longtext DEFAULT NULL,
  `cont_order` int(11) DEFAULT NULL,
  `cont_block` varchar(265) DEFAULT NULL,
  `cont_settings` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `media`
--

CREATE TABLE `media` (
  `medi_id` int(11) NOT NULL,
  `medi_name` varchar(265) DEFAULT NULL,
  `medi_url` text DEFAULT NULL,
  `medi_type` varchar(265) DEFAULT NULL,
  `medi_uploaded` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `mediacontent`
--

CREATE TABLE `mediacontent` (
  `medi_id` int(11) NOT NULL,
  `cont_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `page`
--

CREATE TABLE `page` (
  `page_id` int(11) NOT NULL,
  `page_name` varchar(265) DEFAULT NULL,
  `page_slug` varchar(265) DEFAULT NULL,
  `page_status` int(11) DEFAULT NULL,
  `page_date` datetime DEFAULT current_timestamp(),
  `page_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pagecontent`
--

CREATE TABLE `pagecontent` (
  `cont_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `post`
--

CREATE TABLE `post` (
  `post_id` int(11) NOT NULL,
  `post_name` varchar(265) DEFAULT NULL,
  `post_slug` varchar(265) DEFAULT NULL,
  `post_status` int(11) DEFAULT NULL,
  `post_date` datetime DEFAULT current_timestamp(),
  `post_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `postcontent`
--

CREATE TABLE `postcontent` (
  `cont_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `role`
--

CREATE TABLE `role` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(265) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `role`
--

INSERT INTO `role` (`role_id`, `role_name`) VALUES
(1, 'Administrator'),
(2, 'Editor');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `setting`
--

CREATE TABLE `setting` (
  `sett_id` int(11) NOT NULL,
  `sett_name` varchar(265) DEFAULT NULL,
  `sett_value` varchar(265) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(265) DEFAULT NULL,
  `user_mail` varchar(265) DEFAULT NULL,
  `user_password` varchar(265) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `user`
--

INSERT INTO `user` (`user_id`, `user_name`, `user_mail`, `user_password`, `role_id`) VALUES
(2, 'Bram', 'bramvdklashorst@gmail.com', '$2y$10$xwTYehHNkv47.OgzL7w.g.jGewYazWIwjqu7aSm9o..mzFh0Br1WW', 1);

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `content`
--
ALTER TABLE `content`
  ADD PRIMARY KEY (`cont_id`);

--
-- Indexen voor tabel `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`medi_id`);

--
-- Indexen voor tabel `mediacontent`
--
ALTER TABLE `mediacontent`
  ADD PRIMARY KEY (`medi_id`,`cont_id`),
  ADD KEY `cont_id` (`cont_id`);

--
-- Indexen voor tabel `page`
--
ALTER TABLE `page`
  ADD PRIMARY KEY (`page_id`);

--
-- Indexen voor tabel `pagecontent`
--
ALTER TABLE `pagecontent`
  ADD PRIMARY KEY (`cont_id`,`page_id`),
  ADD KEY `page_id` (`page_id`);

--
-- Indexen voor tabel `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`post_id`);

--
-- Indexen voor tabel `postcontent`
--
ALTER TABLE `postcontent`
  ADD PRIMARY KEY (`cont_id`,`post_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexen voor tabel `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexen voor tabel `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`sett_id`);

--
-- Indexen voor tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `content`
--
ALTER TABLE `content`
  MODIFY `cont_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `media`
--
ALTER TABLE `media`
  MODIFY `medi_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `page`
--
ALTER TABLE `page`
  MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `post`
--
ALTER TABLE `post`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `role`
--
ALTER TABLE `role`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT voor een tabel `setting`
--
ALTER TABLE `setting`
  MODIFY `sett_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `mediacontent`
--
ALTER TABLE `mediacontent`
  ADD CONSTRAINT `mediacontent_ibfk_1` FOREIGN KEY (`medi_id`) REFERENCES `media` (`medi_id`),
  ADD CONSTRAINT `mediacontent_ibfk_2` FOREIGN KEY (`cont_id`) REFERENCES `content` (`cont_id`);

--
-- Beperkingen voor tabel `pagecontent`
--
ALTER TABLE `pagecontent`
  ADD CONSTRAINT `pagecontent_ibfk_1` FOREIGN KEY (`cont_id`) REFERENCES `content` (`cont_id`),
  ADD CONSTRAINT `pagecontent_ibfk_2` FOREIGN KEY (`page_id`) REFERENCES `page` (`page_id`);

--
-- Beperkingen voor tabel `postcontent`
--
ALTER TABLE `postcontent`
  ADD CONSTRAINT `postcontent_ibfk_1` FOREIGN KEY (`cont_id`) REFERENCES `content` (`cont_id`),
  ADD CONSTRAINT `postcontent_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `post` (`post_id`);

--
-- Beperkingen voor tabel `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
