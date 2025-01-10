-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Gen 10, 2025 alle 14:22
-- Versione del server: 10.11.6-MariaDB-0+deb12u1
-- Versione PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webrtc_db`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `ads`
--

CREATE TABLE `ads` (
  `id` int(11) NOT NULL,
  `door_id` int(11) DEFAULT NULL,
  `video_path` varchar(255) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `ad_schedule`
--

CREATE TABLE `ad_schedule` (
  `id` int(11) NOT NULL,
  `ad_id` int(11) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `max_doors` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `doors`
--

CREATE TABLE `doors` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `room` varchar(50) NOT NULL,
  `antagonist_door_id` int(11) DEFAULT NULL,
  `scrolling_text` varchar(255) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `description` text DEFAULT NULL,
  `connected_door_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `subtext` text DEFAULT NULL,
  `room_name` varchar(255) DEFAULT NULL,
  `ad_video_url` varchar(255) DEFAULT NULL,
  `inactivity_video_url` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `location` varchar(255) DEFAULT NULL,
  `on_air` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dump dei dati per la tabella `doors`
--

INSERT INTO `doors` (`id`, `name`, `room`, `antagonist_door_id`, `scrolling_text`, `client_id`, `created_at`, `description`, `connected_door_id`, `user_id`, `subtext`, `room_name`, `ad_video_url`, `inactivity_video_url`, `updated_at`, `location`, `on_air`) VALUES
(2, 'Napoli', '', NULL, NULL, NULL, '2025-01-09 08:24:12', 'Door2', 3, 1, '0', 'streamingroom', '/uploads/doors/2/ad_video/WhatsApp Video 2019-11-11 at 11.41.03.mp4', '/panel/uploads/doors/2/inactivity_video/WhatsApp Video 2019-11-11 at 11.41.03.mp4', '2025-01-10 14:21:35', 'Napoli', 0),
(3, 'Modena', '', NULL, NULL, NULL, '2025-01-09 08:35:11', 'Door3', 2, 1, '0', 'streamingroom', '/panel/uploads/doors/3/ad_video/WhatsApp Video 2019-11-11 at 11.41.03.mp4', '/panel/uploads/doors/3/inactivity_video/WhatsApp Video 2019-11-11 at 11.41.03.mp4', '2025-01-10 14:21:40', 'Modena', 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `door_schedule`
--

CREATE TABLE `door_schedule` (
  `id` int(11) NOT NULL,
  `door_id` int(11) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `schedule`
--

CREATE TABLE `schedule` (
  `id` int(11) NOT NULL,
  `door_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `videos` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dump dei dati per la tabella `schedule`
--

INSERT INTO `schedule` (`id`, `door_id`, `title`, `description`, `start_time`, `end_time`, `videos`) VALUES
(1, 3, 'Test programma', 'test programma descrizione', '2025-01-10 12:22:00', '2025-01-10 13:22:00', '[\"\\/panel\\/uploads\\/doors\\/3\\/ad_video\\/Spot1.mp4\"]'),
(2, 3, 'Spot2', '', '2025-01-10 15:32:00', '2025-01-11 11:32:00', '[\"\\/panel\\/uploads\\/doors\\/3\\/ad_video\\/Spot1.mp4\"]'),
(3, 2, 'spot 0', 'spot 0', '2025-01-20 11:47:00', '2025-01-20 13:47:00', '[\"\\/panel\\/uploads\\/doors\\/2\\/ad_video\\/Spot1.mp4\"]');

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','admin','client') NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `name`, `email`, `profile_image`, `created_at`) VALUES
(1, 'admin', 'fe01ce2a7fbac8fafaed7c982a04e229', 'superadmin', 'Super Admin', 'info@increative.it', 'IMG_20220709_214047.jpg', '2025-01-08 13:14:29'),
(4, 'demo', 'fe01ce2a7fbac8fafaed7c982a04e229', 'client', 'demo', 'demo@increative.it', NULL, '2025-01-08 15:56:27');

-- --------------------------------------------------------

--
-- Struttura della tabella `user_clients`
--

CREATE TABLE `user_clients` (
  `user_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `user_doors`
--

CREATE TABLE `user_doors` (
  `user_id` int(11) NOT NULL,
  `door_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `ads`
--
ALTER TABLE `ads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `door_id` (`door_id`);

--
-- Indici per le tabelle `ad_schedule`
--
ALTER TABLE `ad_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ad_id` (`ad_id`);

--
-- Indici per le tabelle `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `doors`
--
ALTER TABLE `doors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `connected_door_id` (`connected_door_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indici per le tabelle `door_schedule`
--
ALTER TABLE `door_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `door_id` (`door_id`);

--
-- Indici per le tabelle `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `door_id` (`door_id`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indici per le tabelle `user_clients`
--
ALTER TABLE `user_clients`
  ADD PRIMARY KEY (`user_id`,`client_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indici per le tabelle `user_doors`
--
ALTER TABLE `user_doors`
  ADD PRIMARY KEY (`user_id`,`door_id`),
  ADD KEY `door_id` (`door_id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `ads`
--
ALTER TABLE `ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `ad_schedule`
--
ALTER TABLE `ad_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `doors`
--
ALTER TABLE `doors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `door_schedule`
--
ALTER TABLE `door_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `ads`
--
ALTER TABLE `ads`
  ADD CONSTRAINT `ads_ibfk_1` FOREIGN KEY (`door_id`) REFERENCES `doors` (`id`);

--
-- Limiti per la tabella `ad_schedule`
--
ALTER TABLE `ad_schedule`
  ADD CONSTRAINT `ad_schedule_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`);

--
-- Limiti per la tabella `doors`
--
ALTER TABLE `doors`
  ADD CONSTRAINT `doors_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `doors_ibfk_2` FOREIGN KEY (`connected_door_id`) REFERENCES `doors` (`id`),
  ADD CONSTRAINT `doors_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Limiti per la tabella `door_schedule`
--
ALTER TABLE `door_schedule`
  ADD CONSTRAINT `door_schedule_ibfk_1` FOREIGN KEY (`door_id`) REFERENCES `doors` (`id`);

--
-- Limiti per la tabella `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `schedule_ibfk_1` FOREIGN KEY (`door_id`) REFERENCES `doors` (`id`);

--
-- Limiti per la tabella `user_clients`
--
ALTER TABLE `user_clients`
  ADD CONSTRAINT `user_clients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_clients_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`);

--
-- Limiti per la tabella `user_doors`
--
ALTER TABLE `user_doors`
  ADD CONSTRAINT `user_doors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_doors_ibfk_2` FOREIGN KEY (`door_id`) REFERENCES `doors` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
