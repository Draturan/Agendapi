-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Creato il: Dic 10, 2018 alle 07:28
-- Versione del server: 10.1.10-MariaDB
-- Versione PHP: 5.6.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `miningful_lapiary`
--
CREATE DATABASE IF NOT EXISTS `lapiary` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `lapiary`;

-- --------------------------------------------------------

--
-- Struttura della tabella `libri`
--

CREATE TABLE `libri` (
  `id` int(11) NOT NULL,
  `titolo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `autore` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` year(4) NOT NULL,
  `genere` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `libri`
--

INSERT INTO `libri` (`id`, `titolo`, `autore`, `data`, `genere`) VALUES
(1, 'Il Visconte Dimezzato', 'Italo Calvino', 1952, 'Narrativa'),
(4, 'Correva l''anno', 'Bruno Vespa', 1997, 'Autobiografia'),
(5, 'Il codice Da Vinci', 'Dan Brown', 1997, 'Romanzo Storico'),
(8, 'Davide Brunelleschi', 'Raimondo Cassani', 1998, 'Arte Beni Culturali e Moda'),
(9, 'Greggi di pecore', 'Peppone Rattoni', 2004, 'Satirico'),
(10, 'Rotoballe della Val Venosta', 'Terenzio Casanova', 1987, 'Tecnico Scientifico'),
(11, 'Carlo Martello', 'Piero Angela', 2001, 'Storiografia'),
(12, 'Termopili', 'Valerio Massimo Manfredi', 1995, 'Romanzo Storico');

-- --------------------------------------------------------

--
-- Struttura della tabella `prestiti`
--

CREATE TABLE `prestiti` (
  `id` int(11) NOT NULL,
  `fk_libro` int(11) NOT NULL,
  `fk_utente` int(11) NOT NULL,
  `data_inizio` date NOT NULL,
  `data_fine` date NOT NULL,
  `data_riconsegna` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `prestiti`
--

INSERT INTO `prestiti` (`id`, `fk_libro`, `fk_utente`, `data_inizio`, `data_fine`, `data_riconsegna`) VALUES
(1, 1, 6, '2018-11-01', '2018-12-31', '0000-00-00'),
(2, 4, 16, '2018-08-01', '2018-09-30', '2018-10-03'),
(3, 2, 5, '2018-05-01', '2018-05-31', '2018-06-01'),
(4, 5, 19, '2018-12-01', '2019-01-19', '0000-00-00'),
(5, 2, 16, '2018-11-01', '2018-12-08', '0000-00-00'),
(9, 1, 16, '2018-10-12', '2018-11-12', '2018-11-15'),
(11, 11, 30, '2018-12-08', '2018-12-22', '2018-12-18');

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `id` int(11) NOT NULL,
  `nome` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `cognome` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_di_nascita` date NOT NULL,
  `cap` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`id`, `nome`, `cognome`, `data_di_nascita`, `cap`, `email`) VALUES
(1, 'Simone', 'Armadoro', '1988-04-13', '00100', 'simone.armadoro@test.it'),
(6, 'Carlo', 'Vanzina', '1897-06-08', '05100', 'carlovanzi@libero.it'),
(16, 'Roberto', 'Scamarcio', '1988-04-01', '27512', 'rotterdam47@pallino.it'),
(19, 'Tommaso', 'Torreggiani', '2001-11-05', '05100', 'simone.armadoro@libero.it'),
(21, 'Magdalena', 'Iglesias', '1991-02-21', '44121', 'meggulina@tqm.it'),
(30, 'Carlo', 'Goldoni', '1989-06-01', '05100', 'carlo@hotmail.it'),
(31, 'Francesco', 'Raimondo', '1962-01-09', '10522', 'carlo.raimondo@libero.it');

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti_telefono`
--

CREATE TABLE `utenti_telefono` (
  `id` int(11) NOT NULL,
  `fk_utente` int(11) NOT NULL,
  `tipo` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `utenti_telefono`
--

INSERT INTO `utenti_telefono` (`id`, `fk_utente`, `tipo`, `telefono`) VALUES
(1, 1, 'Casa', '+39 051233215'),
(2, 1, 'Ufficio', '+39 02 1236548'),
(3, 1, 'Mare', '141 474747'),
(10, 6, 'Casa', '1023810938'),
(11, 6, 'Lavoro', '1293081039'),
(12, 6, 'Montagna', '1919919191'),
(13, 7, 'Lavoro', '074458882'),
(14, 7, 'Casa', '0516129654296'),
(15, 7, '', ''),
(40, 16, 'Roma', '+39 02 2565478'),
(41, 16, '', ''),
(42, 16, '', ''),
(49, 19, 'Lavoro', '+39 02 45123252'),
(50, 19, 'Casa', '0516129654296'),
(51, 19, '', ''),
(55, 21, 'Casa', '5241254565'),
(56, 21, '', ''),
(57, 21, '', ''),
(75, 30, 'Casa', '1234321123'),
(76, 30, '', ''),
(77, 30, '', ''),
(78, 31, 'Lavoro', '0521 0151245'),
(79, 31, 'Casa al Mare', '0516 29654297'),
(80, 31, '', '');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `libri`
--
ALTER TABLE `libri`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `prestiti`
--
ALTER TABLE `prestiti`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `utenti_telefono`
--
ALTER TABLE `utenti_telefono`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `libri`
--
ALTER TABLE `libri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT per la tabella `prestiti`
--
ALTER TABLE `prestiti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT per la tabella `utenti`
--
ALTER TABLE `utenti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT per la tabella `utenti_telefono`
--
ALTER TABLE `utenti_telefono`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
