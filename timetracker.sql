-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Gazdă: localhost:8889
-- Timp de generare: mai 23, 2023 la 06:29 PM
-- Versiune server: 5.7.39
-- Versiune PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Bază de date: `timetracker`
--

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nume_activitate` varchar(255) NOT NULL,
  `departament_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Eliminarea datelor din tabel `categories`
--

INSERT INTO `categories` (`id`, `nume_activitate`, `departament_id`) VALUES
(2, 'Planificare', 1),
(3, 'Organizare', 1),
(4, 'Control', 1),
(5, 'Publicitate', 2),
(6, 'Cercetare de piata', 2),
(7, 'Promovare', 2),
(8, 'Asamblare', 3),
(9, 'Control calitate', 3),
(10, 'Ambalare', 3),
(11, 'Contabilitate', 4),
(12, 'Analiza financiara', 4),
(13, 'Gestionare buget', 4),
(14, 'Chilling', 2),
(20, 'asa', 10);

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Eliminarea datelor din tabel `departments`
--

INSERT INTO `departments` (`id`, `name`) VALUES
(1, 'Administrativ'),
(2, 'Marketing'),
(3, 'Productie'),
(4, 'Financiar'),
(5, 'Test Adauga Departament'),
(10, 'as');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `hoursWorked`
--

CREATE TABLE `hoursWorked` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `departament_id` int(11) DEFAULT NULL,
  `categorie_id` int(11) DEFAULT NULL,
  `data` date NOT NULL,
  `numar_ore` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Eliminarea datelor din tabel `hoursWorked`
--

INSERT INTO `hoursWorked` (`id`, `user_id`, `departament_id`, `categorie_id`, `data`, `numar_ore`) VALUES
(1, 9, 4, 11, '2023-05-18', 4),
(2, 9, 3, 10, '2023-05-18', 4),
(3, 9, 3, 8, '2023-05-13', 4),
(4, 9, 3, 9, '2023-05-09', 1),
(5, 7, 1, 2, '2023-05-20', 4),
(6, 9, 1, 2, '2023-05-22', 3),
(7, 9, 1, 2, '2023-05-16', 1),
(8, 7, 1, 4, '2023-05-23', 2),
(9, 13, 3, 9, '2023-05-23', 2),
(10, 13, 3, 10, '2023-05-23', 4),
(11, 13, 3, 9, '2023-05-16', 1),
(12, 13, 3, 10, '2023-05-19', 2);

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) DEFAULT 'user',
  `department_id` int(11) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Eliminarea datelor din tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `department_id`, `phone_number`, `is_active`) VALUES
(7, 'Admin Robert', 'onwer@1.de', '$2y$10$SamXrBEt9IVlpRCw/DDqo.yn0FkxoqxvEXdCQuUcbuEBpLP7xWnWu', 'administrator', 1, '12', 1),
(8, 'cont activ', 'cont@activ.de', '$2y$10$BlY3eLIXYBIFGu1r.nYakOAerFtCTPxmdoMDAQibkjqcs5MRMR4.6', 'user', 3, '0312301293', 1),
(9, 'Robert M.', 'test@1.de', '$2y$10$DOiYtxvVXW0AcTL1hpaXPOADi9GzXw6378HtcLli/rFNO3NeAbJpS', 'administrator', 1, '31', 1),
(10, 'dasdasd', 'asdas@1.de', '$2y$10$G9Wat5Uo5HkRHqbkfrRFk.MvS44dXZjrRVPfztVlb3/0PjDFDFD42', 'user', 10, '', 0),
(11, 'dasd asd asda', 'qwe@2.de', '$2y$10$Jn8RRbbLj/Testtkrw5rYeHazrRnbODuWemrCbWnUk1KDblXQH3cm', 'user', 2, NULL, 0),
(12, 'Verificare Logare', 'v@1.de', '$2y$10$so52PHD35ezjgdqTT10scuQvtRmklwU2Zs8sUUmxST5kMmUgVeH/y', 'user', 3, NULL, 0),
(13, 'Cosmin Ghergheles', 'cosmin@ghergheles.ro', '$2y$10$i9aAaYCI/EGTUf4y907WZ.zFpjhlNCNwjJEy2SjJOb4aUJ/nvu.9a', 'user', 3, NULL, 1),
(14, 'Utilizator Random', 'u@r.de', '$2y$10$SD7AZP1MbFQsyuUcE.t1reAC8JAHzBjtDgkAtwzCyZJmbFMtD7cCa', 'user', 4, NULL, 1);

--
-- Indexuri pentru tabele eliminate
--

--
-- Indexuri pentru tabele `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `departament_id` (`departament_id`);

--
-- Indexuri pentru tabele `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexuri pentru tabele `hoursWorked`
--
ALTER TABLE `hoursWorked`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `departament_id` (`departament_id`),
  ADD KEY `categorie_id` (`categorie_id`);

--
-- Indexuri pentru tabele `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

--
-- AUTO_INCREMENT pentru tabele eliminate
--

--
-- AUTO_INCREMENT pentru tabele `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pentru tabele `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pentru tabele `hoursWorked`
--
ALTER TABLE `hoursWorked`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pentru tabele `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constrângeri pentru tabele eliminate
--

--
-- Constrângeri pentru tabele `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`departament_id`) REFERENCES `Departments` (`id`);

--
-- Constrângeri pentru tabele `hoursWorked`
--
ALTER TABLE `hoursWorked`
  ADD CONSTRAINT `hoursworked_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `hoursworked_ibfk_2` FOREIGN KEY (`departament_id`) REFERENCES `Departments` (`id`),
  ADD CONSTRAINT `hoursworked_ibfk_3` FOREIGN KEY (`categorie_id`) REFERENCES `Categories` (`id`);

--
-- Constrângeri pentru tabele `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
