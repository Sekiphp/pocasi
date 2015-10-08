-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Počítač: 127.0.0.1
-- Vygenerováno: Úte 02. dub 2013, 13:49
-- Verze MySQL: 5.5.25a-log
-- Verze PHP: 5.3.14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `d2286_pocasi`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `pocasi`
--

CREATE TABLE IF NOT EXISTS `pocasi` (
  `den` int(11) NOT NULL,
  `t_rano` decimal(3,1) NOT NULL,
  `t_odpoledne` decimal(3,1) NOT NULL,
  `v_rano` int(11) NOT NULL,
  `v_odpoledne` decimal(3,1) NOT NULL,
  `srazky` decimal(3,1) NOT NULL,
  `tlak` int(11) NOT NULL,
  PRIMARY KEY (`den`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
