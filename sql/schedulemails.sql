-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 03, 2017 at 06:42 AM
-- Server version: 5.7.19
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `schedulemails`
--

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
CREATE TABLE IF NOT EXISTS `members` (
  `sr` int(11) NOT NULL AUTO_INCREMENT,
  `accountid` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `email` text NOT NULL,
  `signupdays` text NOT NULL,
  `users` int(11) NOT NULL DEFAULT '0',
  `projects` int(11) NOT NULL DEFAULT '0',
  `mxtimeentdate` text NOT NULL,
  `entriescount` text NOT NULL,
  `expdate` text NOT NULL,
  PRIMARY KEY (`sr`),
  UNIQUE KEY `accountid` (`accountid`)
) ENGINE=MyISAM AUTO_INCREMENT=135 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`sr`, `accountid`, `status`, `email`, `signupdays`, `users`, `projects`, `mxtimeentdate`, `entriescount`, `expdate`) VALUES
(1, 8590, 0, 'testemail1@dummyserver.com', '1', 0, 0, '0', '0', '0'),
(1, 8591, 0, 'testemail2@dummyserver.com', '5', 0, 0, '0', '0', '0'),
(1, 8592, 0, 'testemail3@dummyserver.com', '10', 0, 0, '0', '0', '0'),
(1, 8593, 0, 'testemail4@dummyserver.com', '11', 0, 0, '0', '0', '0'),
(1, 8594, 0, 'testemail5@dummyserver.com', '81', 0, 0, '0', '0', '0');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
