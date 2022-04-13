-- phpMyAdmin SQL Dump
-- version 3.5.8.1
-- http://www.phpmyadmin.net
--
-- Host: misaw.se.mysql:3306
-- Generation Time: Sep 03, 2018 at 06:55 PM
-- Server version: 10.1.30-MariaDB-1~xenial
-- PHP Version: 5.4.45-0+deb7u13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `capratek_se`
--
USE `capratek_se`;

--
-- Table structure for table `db_project`
--

DROP TABLE IF EXISTS `db_project`;
CREATE TABLE IF NOT EXISTS `db_project` (
  `client` varchar(255) NOT NULL,
  `number` varchar(20) NOT NULL,
  `name` varchar(30) NOT NULL,
  `program` varchar(60) DEFAULT NULL,
  `jobsite` varchar(40) NOT NULL,
  `created` date NOT NULL,
  PRIMARY KEY (`client`,`number`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `db_project`
--

INSERT INTO `db_project` (`client`, `number`, `name`, `program`, `jobsite`, `created`) VALUES
('micke@gmail.com', '01', 'Magnus W', '', 'Västerås', '2018-04-27'),
('micke@gmail.com', '01', 'SBUPP', 'Upprustning Saltsjöbanan', 'Sickla', '2018-04-03'),
('micke@gmail.com', '02', 'SB 24', 'Upprustning Saltsjöbanan', 'Sickla', '2018-04-06'),
('micke@gmail.com', '03', 'SBkap', 'Upprustning Saltsjöbanan', 'Sickla', '2018-04-12'),
('micke@gmail.com', '123456', 'Test', 'SB', 'Stockholm', '2017-10-27'),
('simon@gmail.com', '246', 'Kuken', 'Röv', 'Moskva', '2018-05-19');

-- --------------------------------------------------------

--
-- Table structure for table `db_project_abnormality`
--

DROP TABLE IF EXISTS `db_project_abnormality`;
CREATE TABLE IF NOT EXISTS `db_project_abnormality` (
  `client` varchar(255) NOT NULL,
  `number` varchar(20) NOT NULL,
  `name` varchar(30) NOT NULL,
  `id` int(11) NOT NULL,
  `workday` int(11) NOT NULL,
  `company` varchar(11) NOT NULL,
  `header` varchar(255) NOT NULL,
  `jobsite` varchar(40) NOT NULL,
  `comments` varchar(255) NOT NULL,
  `economic_consequence` tinyint(1) NOT NULL,
  `time_consequence` tinyint(1) NOT NULL,
  `status` tinyint(2) NOT NULL,
  `locked` tinyint(2) NOT NULL,
  PRIMARY KEY (`client`,`number`,`name`,`id`,`workday`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `db_project_abnormality`
--

INSERT INTO `db_project_abnormality` (`client`, `number`, `name`, `id`, `workday`, `company`, `header`, `jobsite`, `comments`, `economic_consequence`, `time_consequence`, `status`, `locked`) VALUES
('micke@gmail.com', '123456', 'Test', 1, 1, '559070-2915', 'Rör och sten', 'km 1+300', 'deswvkjcbdeswvewv', 0, 0, 1, 2),
('micke@gmail.com', '123456', 'Test', 1, 2, '559070-2915', 'Rör och sten', 'km 1+300', 'ewewbvewb', 0, 0, 3, 0),
('micke@gmail.com', '123456', 'Test', 1, 3, '559070-2915', 'ewfwfg', 'sfsdvf', 'dsvsdv', 0, 0, 1, 2),
('micke@gmail.com', '123456', 'Test', 1, 4, '559070-2915', 'ewfwfg', 'sfsdvf', 'wvwevewv', 0, 0, 3, 0),
('micke@gmail.com', '123456', 'Test', 1, 5, '559070-2915', 'Test', 'Sickla', 'Ingen', 0, 0, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `db_project_abnormality_crew`
--

DROP TABLE IF EXISTS `db_project_abnormality_crew`;
CREATE TABLE IF NOT EXISTS `db_project_abnormality_crew` (
  `client` varchar(255) NOT NULL,
  `number` varchar(20) NOT NULL,
  `name` varchar(30) NOT NULL,
  `id` int(11) NOT NULL,
  `workday` int(11) NOT NULL,
  `crewid` int(11) NOT NULL,
  `company` varchar(11) NOT NULL,
  `fullname` varchar(80) NOT NULL,
  `jobtype` varchar(60) NOT NULL,
  `time` float NOT NULL,
  `own` tinyint(1) NOT NULL,
  PRIMARY KEY (`client`,`number`,`name`,`id`,`workday`,`crewid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `db_project_abnormality_crew`
--

INSERT INTO `db_project_abnormality_crew` (`client`, `number`, `name`, `id`, `workday`, `crewid`, `company`, `fullname`, `jobtype`, `time`, `own`) VALUES
('micke@gmail.com', '123456', 'Test', 1, 1, 1, '559070-2915', 'scfniwevewv', 'Grävmaskin', 2, 1),
('micke@gmail.com', '123456', 'Test', 1, 2, 1, '559070-2915', 'rebgreb', 'Grävmaskin', 8.5, 1),
('micke@gmail.com', '123456', 'Test', 1, 3, 1, '559070-2915', 'sdvdsvdv', 'Grävmaskin', 8, 1),
('micke@gmail.com', '123456', 'Test', 1, 4, 1, '559070-2915', 'ewfewvfew', 'Kranbil', 7.5, 1),
('micke@gmail.com', '123456', 'Test', 1, 5, 1, '559070-2915', 'Micke W', 'Arbetsledare', 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `db_project_diary`
--

DROP TABLE IF EXISTS `db_project_diary`;
CREATE TABLE IF NOT EXISTS `db_project_diary` (
  `client` varchar(255) NOT NULL,
  `number` varchar(20) NOT NULL,
  `name` varchar(30) NOT NULL,
  `company` varchar(11) NOT NULL,
  `workday` int(11) NOT NULL,
  `author` varchar(255) NOT NULL,
  `supervisor` varchar(255) NOT NULL,
  `reviewer` varchar(255) DEFAULT NULL,
  `locked` tinyint(2) NOT NULL,
  `jobsite` varchar(40) NOT NULL,
  `date` date NOT NULL,
  `clientcomments` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`client`,`number`,`name`,`company`,`workday`),
  KEY `db_project_diary_ibfk_2` (`author`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `db_project_diary`
--

INSERT INTO `db_project_diary` (`client`, `number`, `name`, `company`, `workday`, `author`, `supervisor`, `reviewer`, `locked`, `jobsite`, `date`, `clientcomments`) VALUES
('micke@gmail.com', '123456', 'Test', '559070-2915', 1, 'lars@gmail.com', 'rasmus@gmail.com', 'micke@gmail.com', 2, 'Stockholm', '2018-02-20', 'rebhrenrene'),
('micke@gmail.com', '123456', 'Test', '559070-2915', 2, 'lars@gmail.com', 'rasmus@gmail.com', '', 0, 'Stockholm', '2018-02-24', ''),
('micke@gmail.com', '123456', 'Test', '559070-2915', 3, 'rasmus@gmail.com', 'rasmus@gmail.com', 'micke@gmail.com', 2, 'Stockholm', '2018-02-25', ''),
('micke@gmail.com', '123456', 'Test', '559070-2915', 4, 'lars@gmail.com', 'rasmus@gmail.com', '', 0, 'Stockholm', '2018-02-23', ''),
('micke@gmail.com', '123456', 'Test', '559070-2915', 5, 'lars@gmail.com', 'rasmus@gmail.com', '', 0, 'Stockholm', '2018-03-29', '');

-- --------------------------------------------------------

--
-- Table structure for table `db_project_diary_categorytype`
--

DROP TABLE IF EXISTS `db_project_diary_categorytype`;
CREATE TABLE IF NOT EXISTS `db_project_diary_categorytype` (
  `type` varchar(60) NOT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `db_project_diary_categorytype`
--

INSERT INTO `db_project_diary_categorytype` (`type`) VALUES
('Hinder'),
('Ny eller ändrad handling'),
('Olycksfall/Arbetsskada'),
('Övrig kommentar'),
('Uppmätning'),
('Utförd kontroll och provning'),
('Väderförhållanden');

-- --------------------------------------------------------

--
-- Table structure for table `db_project_diary_crew`
--

DROP TABLE IF EXISTS `db_project_diary_crew`;
CREATE TABLE IF NOT EXISTS `db_project_diary_crew` (
  `client` varchar(255) NOT NULL,
  `number` varchar(20) NOT NULL,
  `name` varchar(30) NOT NULL,
  `company` varchar(11) NOT NULL,
  `workday` int(11) NOT NULL,
  `jobid` int(11) NOT NULL,
  `crewid` int(11) NOT NULL,
  `fullname` varchar(80) NOT NULL,
  `jobtype` varchar(60) NOT NULL,
  `time` float NOT NULL,
  `own` tinyint(1) NOT NULL,
  PRIMARY KEY (`client`,`number`,`name`,`company`,`workday`,`jobid`,`crewid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `db_project_diary_crew`
--

INSERT INTO `db_project_diary_crew` (`client`, `number`, `name`, `company`, `workday`, `jobid`, `crewid`, `fullname`, `jobtype`, `time`, `own`) VALUES
('micke@gmail.com', '123456', 'Test', '559070-2915', 1, 1, 1, 'Msjasodjws', 'Grävmaskin', 8, 1),
('micke@gmail.com', '123456', 'Test', '559070-2915', 1, 1, 2, 'rewvgrebvgrebvg', 'Lastbil', 1.5, 1),
('micke@gmail.com', '123456', 'Test', '559070-2915', 1, 2, 1, 'vewvewvwe', 'Lastbil', 2.5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `db_project_diary_job`
--

DROP TABLE IF EXISTS `db_project_diary_job`;
CREATE TABLE IF NOT EXISTS `db_project_diary_job` (
  `client` varchar(255) NOT NULL,
  `number` varchar(20) NOT NULL,
  `name` varchar(30) NOT NULL,
  `company` varchar(11) NOT NULL,
  `workday` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `job` varchar(100) NOT NULL,
  `comments` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`client`,`number`,`name`,`company`,`workday`,`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `db_project_diary_job`
--

INSERT INTO `db_project_diary_job` (`client`, `number`, `name`, `company`, `workday`, `id`, `job`, `comments`, `status`) VALUES
('micke@gmail.com', '123456', 'Test', '559070-2915', 1, 1, 'Test', 'cvjsdnvklsdnv', 1),
('micke@gmail.com', '123456', 'Test', '559070-2915', 1, 2, 'wfvwdevewv', 'wevewvwev', 3);

-- --------------------------------------------------------

--
-- Table structure for table `db_project_diary_jobtype`
--

DROP TABLE IF EXISTS `db_project_diary_jobtype`;
CREATE TABLE IF NOT EXISTS `db_project_diary_jobtype` (
  `type` varchar(60) NOT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `db_project_diary_jobtype`
--

INSERT INTO `db_project_diary_jobtype` (`type`) VALUES
('Arbetsledare'),
('Grävmaskin'),
('Kranbil'),
('Lastbil'),
('Spårgående arbetsredskap'),
('Säkerhetspersonal'),
('Yrkesarbetare');

-- --------------------------------------------------------

--
-- Table structure for table `db_project_diary_misc`
--

DROP TABLE IF EXISTS `db_project_diary_misc`;
CREATE TABLE IF NOT EXISTS `db_project_diary_misc` (
  `client` varchar(255) NOT NULL,
  `number` varchar(20) NOT NULL,
  `name` varchar(30) NOT NULL,
  `company` varchar(11) NOT NULL,
  `workday` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `category` varchar(60) NOT NULL,
  `comments` varchar(255) NOT NULL,
  PRIMARY KEY (`client`,`number`,`name`,`company`,`workday`,`id`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `db_project_diary_misc`
--

INSERT INTO `db_project_diary_misc` (`client`, `number`, `name`, `company`, `workday`, `id`, `category`, `comments`) VALUES
('micke@gmail.com', '123456', 'Test', '559070-2915', 1, 1, 'Ny eller ändrad handling', 'PM 1');

-- --------------------------------------------------------

--
-- Table structure for table `db_project_diary_statustype`
--

DROP TABLE IF EXISTS `db_project_diary_statustype`;
CREATE TABLE IF NOT EXISTS `db_project_diary_statustype` (
  `index` int(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  PRIMARY KEY (`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `db_project_diary_statustype`
--

INSERT INTO `db_project_diary_statustype` (`index`, `type`) VALUES
(1, 'Påbörjad'),
(2, 'Pågående'),
(3, 'Avslutad');

-- --------------------------------------------------------

--
-- Table structure for table `db_project_meeting_header`
--

DROP TABLE IF EXISTS `db_project_meeting_header`;
CREATE TABLE IF NOT EXISTS `db_project_meeting_header` (
  `client` varchar(255) NOT NULL,
  `number` varchar(20) NOT NULL,
  `name` varchar(30) NOT NULL,
  `series` int(11) NOT NULL,
  `meeting` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `text` varchar(255) NOT NULL,
  PRIMARY KEY (`client`,`number`,`name`,`series`,`meeting`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `db_project_meeting_header`
--

INSERT INTO `db_project_meeting_header` (`client`, `number`, `name`, `series`, `meeting`, `id`, `text`) VALUES
('micke@gmail.com', '123456', 'Test', 1, 1, 0, 'Test'),
('micke@gmail.com', '123456', 'Test', 1, 1, 1, 'Test 2'),
('micke@gmail.com', '123456', 'Test', 1, 2, 0, 'Test'),
('micke@gmail.com', '123456', 'Test', 1, 2, 1, 'Test 2'),
('micke@gmail.com', '123456', 'Test', 1, 3, 0, 'Test'),
('micke@gmail.com', '123456', 'Test', 1, 3, 1, 'Test 2'),
('micke@gmail.com', '123456', 'Test', 1, 4, 0, 'Test'),
('micke@gmail.com', '123456', 'Test', 1, 4, 1, 'Test 2'),
('micke@gmail.com', '123456', 'Test', 1, 5, 0, 'Test'),
('micke@gmail.com', '123456', 'Test', 1, 5, 1, 'Test 2'),
('micke@gmail.com', '123456', 'Test', 1, 6, 0, 'Test'),
('micke@gmail.com', '123456', 'Test', 1, 6, 1, 'Test 2'),
('micke@gmail.com', '123456', 'Test', 1, 7, 0, 'Test'),
('micke@gmail.com', '123456', 'Test', 1, 7, 1, 'Test 2'),
('micke@gmail.com', '123456', 'Test', 2, 1, 0, 'Inledning'),
('micke@gmail.com', '123456', 'Test', 2, 2, 0, 'Inledning'),
('micke@gmail.com', '123456', 'Test', 2, 3, 0, 'Inledning'),
('micke@gmail.com', '123456', 'Test', 2, 4, 0, 'Inledning'),
('micke@gmail.com', '123456', 'Test', 3, 1, 0, 'ingrfgfgf'),
('micke@gmail.com', '123456', 'Test', 3, 1, 1, 'erbebaebearbe'),
('micke@gmail.com', '123456', 'Test', 3, 2, 0, 'ingrfgfgf'),
('micke@gmail.com', '123456', 'Test', 3, 2, 1, 'erbebaebearbe'),
('micke@gmail.com', '123456', 'Test', 3, 3, 0, 'ingrfgfgf'),
('micke@gmail.com', '123456', 'Test', 3, 3, 1, 'erbebaebearbe'),
('micke@gmail.com', '123456', 'Test', 3, 4, 0, 'ingrfgfgf'),
('micke@gmail.com', '123456', 'Test', 3, 4, 1, 'erbebaebearbe'),
('micke@gmail.com', '123456', 'Test', 3, 5, 0, 'ingrfgfgf'),
('micke@gmail.com', '123456', 'Test', 3, 5, 1, 'erbebaebearbe'),
('micke@gmail.com', '123456', 'Test', 3, 6, 0, 'ingrfgfgf'),
('micke@gmail.com', '123456', 'Test', 3, 6, 1, 'erbebaebearbe'),
('micke@gmail.com', '123456', 'Test', 3, 7, 0, 'ingrfgfgf'),
('micke@gmail.com', '123456', 'Test', 3, 7, 1, 'erbebaebearbe'),
('micke@gmail.com', '123456', 'Test', 3, 8, 0, 'ingrfgfgf'),
('micke@gmail.com', '123456', 'Test', 3, 8, 1, 'erbebaebearbe'),
('micke@gmail.com', '123456', 'Test', 4, 1, 0, 'Rubrik 1'),
('micke@gmail.com', '123456', 'Test', 4, 2, 0, 'Rubrik 1'),
('micke@gmail.com', '123456', 'Test', 4, 3, 0, 'Rubrik 1');

-- --------------------------------------------------------

--
-- Table structure for table `db_project_meeting_present`
--

DROP TABLE IF EXISTS `db_project_meeting_present`;
CREATE TABLE IF NOT EXISTS `db_project_meeting_present` (
  `client` varchar(255) NOT NULL,
  `number` varchar(20) NOT NULL,
  `name` varchar(30) NOT NULL,
  `series` int(11) NOT NULL,
  `meeting` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `fullname` varchar(80) NOT NULL,
  `company` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `present` tinyint(1) NOT NULL,
  PRIMARY KEY (`client`,`number`,`name`,`series`,`meeting`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `db_project_meeting_present`
--

INSERT INTO `db_project_meeting_present` (`client`, `number`, `name`, `series`, `meeting`, `id`, `fullname`, `company`, `email`, `present`) VALUES
('micke@gmail.com', '123456', 'Test', 1, 1, 1, 'Mikael Wållner', 'Capratek AB', 'micke@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 1, 1, 2, 'Sandra Lindqvist', 'Nikita', 'sandra@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 1, 2, 1, 'Mikael Wållner', 'Capratek AB', 'micke@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 1, 2, 2, 'Sandra Lindqvist', 'Nikita', 'sandra@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 1, 2, 3, 'Patrik Larsson ', 'Dknow', 'patrik@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 1, 3, 1, 'Mikael Wållner', 'Capratek AB', 'micke@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 1, 3, 2, 'Sandra Lindqvist', 'Nikita', 'sandra@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 1, 3, 3, 'Patrik Larsson ', 'Dknow', 'patrik@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 1, 4, 1, 'Mikael Wållner', 'Capratek AB', 'micke@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 1, 4, 2, 'Sandra Lindqvist', 'Nikita', 'sandra@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 1, 4, 3, 'Patrik Larsson ', 'Dknow', 'patrik@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 1, 5, 1, 'Mikael Wållner', 'Capratek AB', 'micke@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 1, 5, 2, 'Sandra Lindqvist', 'Nikita', 'sandra@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 1, 5, 3, 'Patrik Larsson ', 'Dknow', 'patrik@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 1, 6, 1, 'Mikael Wållner', 'Capratek AB', 'micke@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 1, 6, 2, 'Sandra Lindqvist', 'Nikita', 'sandra@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 1, 6, 3, 'Patrik Larsson ', 'Dknow', 'patrik@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 1, 7, 1, 'Mikael Wållner', 'Capratek AB', 'micke@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 1, 7, 2, 'Sandra Lindqvist', 'Nikita', 'sandra@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 1, 7, 3, 'Patrik Larsson ', 'Dknow', 'patrik@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 2, 1, 1, 'Mikael Wållner', 'Capratek AB', 'micke@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 2, 1, 2, 'Sandra Wållner ', 'Capratek AB', 'sandra@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 2, 2, 1, 'Mikael Wållner', 'Capratek AB', 'micke@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 2, 2, 2, 'Sandra Wållner ', 'Capratek AB', 'sandra@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 2, 3, 1, 'Mikael Wållner', 'Capratek AB', 'micke@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 2, 3, 2, 'Sandra Wållner ', 'Capratek AB', 'sandra@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 2, 4, 1, 'Mikael Wållner', 'Capratek AB', 'micke@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 2, 4, 2, 'Sandra Wållner ', 'Capratek AB', 'sandra@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 3, 1, 1, 'Mikael Wållner', 'Capratek AB', 'micke@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 3, 1, 2, 'Sandra Lindqvist', 'svbsvsdvsvd', 'sandra@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 3, 2, 1, 'Mikael Wållner', 'Capratek AB', 'micke@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 3, 2, 2, 'Sandra Lindqvist', 'svbsvsdvsvd', 'sandra@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 3, 3, 1, 'Mikael Wållner', 'Capratek AB', 'micke@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 3, 3, 2, 'Sandra Lindqvist', 'svbsvsdvsvd', 'sandra@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 3, 4, 1, 'Mikael Wållner', 'Capratek AB', 'micke@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 3, 4, 2, 'Sandra Lindqvist', 'svbsvsdvsvd', 'sandra@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 3, 5, 1, 'Mikael Wållner', 'Capratek AB', 'micke@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 3, 5, 2, 'Sandra Lindqvist', 'svbsvsdvsvd', 'sandra@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 3, 6, 1, 'Mikael Wållner', 'Capratek AB', 'micke@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 3, 6, 2, 'Sandra Lindqvist', 'svbsvsdvsvd', 'sandra@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 3, 6, 3, 'Benny Eriksson', 'Aliud', 'benny@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 3, 7, 1, 'Mikael Wållner', 'Capratek AB', 'micke@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 3, 7, 2, 'Sandra Lindqvist', 'svbsvsdvsvd', 'sandra@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 3, 7, 3, 'Benny Eriksson', 'Aliud', 'benny@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 3, 8, 1, 'Mikael Wållner', 'Capratek AB', 'micke@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 3, 8, 2, 'Sandra Lindqvist', 'svbsvsdvsvd', 'sandra@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 3, 8, 3, 'Benny Eriksson', 'Aliud', 'benny@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 4, 1, 1, 'Mikael Wållner', 'Capratek AB', 'micke@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 4, 1, 2, 'Sandra Wållner', 'Capratek AB', 'sandra@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 4, 2, 1, 'Mikael Wållner', 'Capratek AB', 'micke@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 4, 2, 2, 'Sandra Wållner', 'Capratek AB', 'sandra@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 4, 2, 3, 'Rasmus Lindqvist', 'Capratek AB', 'rasmus@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 4, 3, 1, 'Mikael Wållner', 'Capratek AB', 'micke@gmail.com', 1),
('micke@gmail.com', '123456', 'Test', 4, 3, 2, 'Sandra Wållner', 'Capratek AB', 'sandra@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 4, 3, 3, 'Rasmus Lindqvist', 'Capratek AB', 'rasmus@gmail.com', 0);

-- --------------------------------------------------------

--
-- Table structure for table `db_project_meeting_protocol`
--

DROP TABLE IF EXISTS `db_project_meeting_protocol`;
CREATE TABLE IF NOT EXISTS `db_project_meeting_protocol` (
  `client` varchar(255) NOT NULL,
  `number` varchar(20) NOT NULL,
  `name` varchar(30) NOT NULL,
  `series` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `author` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `time` varchar(5) NOT NULL,
  `time2` varchar(5) NOT NULL,
  `jobsite` varchar(40) NOT NULL,
  `locked` tinyint(2) NOT NULL,
  PRIMARY KEY (`client`,`number`,`name`,`series`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `db_project_meeting_protocol`
--

INSERT INTO `db_project_meeting_protocol` (`client`, `number`, `name`, `series`, `id`, `author`, `date`, `time`, `time2`, `jobsite`, `locked`) VALUES
('micke@gmail.com', '123456', 'Test', 1, 1, 'micke@gmail.com', '2018-02-19', '21:57', '22:03', 'Västerås', 2),
('micke@gmail.com', '123456', 'Test', 1, 2, 'micke@gmail.com', '2018-02-19', '22:02', '23:02', 'Västerås', 1),
('micke@gmail.com', '123456', 'Test', 1, 3, 'micke@gmail.com', '2018-02-21', '08:49', '09:49', 'Sickla', 2),
('micke@gmail.com', '123456', 'Test', 1, 4, 'micke@gmail.com', '2018-04-17', '00:00', '01:00', 'Sickla', 1),
('micke@gmail.com', '123456', 'Test', 1, 5, 'micke@gmail.com', '2018-04-17', '00:00', '00:01', 'Nacka', 1),
('micke@gmail.com', '123456', 'Test', 1, 6, 'micke@gmail.com', '2018-04-18', '01:01', '01:02', 'Sickla', 1),
('micke@gmail.com', '123456', 'Test', 1, 7, 'micke@gmail.com', '2018-04-22', '05:18', '05:21', 'New york', 1),
('micke@gmail.com', '123456', 'Test', 2, 1, 'micke@gmail.com', '2018-03-05', '23:22', '23:24', 'Sickla', 2),
('micke@gmail.com', '123456', 'Test', 2, 2, 'micke@gmail.com', '2018-03-05', '23:23', '23:25', 'Här', 2),
('micke@gmail.com', '123456', 'Test', 2, 3, 'micke@gmail.com', '2018-04-10', '00:00', '01:01', 'Sickla', 0),
('micke@gmail.com', '123456', 'Test', 2, 4, 'micke@gmail.com', '2018-04-30', '00:00', '01:01', 'här', 2),
('micke@gmail.com', '123456', 'Test', 3, 1, 'micke@gmail.com', '2018-03-28', '08:00', '09:00', 'Sickla', 2),
('micke@gmail.com', '123456', 'Test', 3, 2, 'micke@gmail.com', '2018-03-28', '08:00', '09:00', 'Här', 2),
('micke@gmail.com', '123456', 'Test', 3, 3, 'micke@gmail.com', '2018-03-29', '00:00', '01:00', 'Här', 2),
('micke@gmail.com', '123456', 'Test', 3, 4, 'micke@gmail.com', '2018-04-30', '00:00', '01:00', 'Här ', 1),
('micke@gmail.com', '123456', 'Test', 3, 5, 'micke@gmail.com', '2018-04-30', '07:29', '10:29', 'Hä''r', 2),
('micke@gmail.com', '123456', 'Test', 3, 6, 'micke@gmail.com', '2018-05-02', '05:00', '06:00', 'Sickla', 2),
('micke@gmail.com', '123456', 'Test', 3, 7, 'micke@gmail.com', '2018-05-11', '00:00', '01:00', 'här', 1),
('micke@gmail.com', '123456', 'Test', 3, 8, 'micke@gmail.com', '2018-05-14', '01:00', '02:00', 'Hemma Test', 1),
('micke@gmail.com', '123456', 'Test', 4, 1, 'micke@gmail.com', '2018-05-12', '01:00', '02:05', 'Hemma', 2),
('micke@gmail.com', '123456', 'Test', 4, 2, 'micke@gmail.com', '2018-05-14', '01:00', '05:00', 'Här', 2),
('micke@gmail.com', '123456', 'Test', 4, 3, 'micke@gmail.com', '2018-05-14', '00:09', '01:00', 'här', 2);

-- --------------------------------------------------------

--
-- Table structure for table `db_project_meeting_series`
--

DROP TABLE IF EXISTS `db_project_meeting_series`;
CREATE TABLE IF NOT EXISTS `db_project_meeting_series` (
  `client` varchar(255) NOT NULL,
  `number` varchar(20) NOT NULL,
  `name` varchar(30) NOT NULL,
  `id` int(11) NOT NULL,
  `author` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `type` int(11) NOT NULL,
  `header` varchar(255) NOT NULL,
  PRIMARY KEY (`client`,`number`,`name`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `db_project_meeting_series`
--

INSERT INTO `db_project_meeting_series` (`client`, `number`, `name`, `id`, `author`, `date`, `type`, `header`) VALUES
('micke@gmail.com', '123456', 'Test', 1, 'micke@gmail.com', '2018-02-19', 1, 'För Klyvargatan 10'),
('micke@gmail.com', '123456', 'Test', 2, 'micke@gmail.com', '2018-03-05', 4, 'SB 5'),
('micke@gmail.com', '123456', 'Test', 3, 'micke@gmail.com', '2018-03-28', 2, 'test'),
('micke@gmail.com', '123456', 'Test', 4, 'micke@gmail.com', '2018-05-12', 1, 'Nyaste');

-- --------------------------------------------------------

--
-- Table structure for table `db_project_meeting_task`
--

DROP TABLE IF EXISTS `db_project_meeting_task`;
CREATE TABLE IF NOT EXISTS `db_project_meeting_task` (
  `client` varchar(255) NOT NULL,
  `number` varchar(20) NOT NULL,
  `name` varchar(30) NOT NULL,
  `series` int(11) NOT NULL,
  `meeting` int(11) NOT NULL,
  `header` int(11) NOT NULL,
  `id` varchar(20) NOT NULL,
  `text` varchar(255) NOT NULL,
  `supervisor1` varchar(80) DEFAULT NULL,
  `supervisor2` varchar(80) DEFAULT NULL,
  `supervisor3` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`client`,`number`,`name`,`series`,`meeting`,`header`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `db_project_meeting_task`
--

INSERT INTO `db_project_meeting_task` (`client`, `number`, `name`, `series`, `meeting`, `header`, `id`, `text`, `supervisor1`, `supervisor2`, `supervisor3`) VALUES
('micke@gmail.com', '123456', 'Test', 1, 1, 0, '1_0.1', 'Kul', 'micke@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 1, 1, 1, '1_1.1', 'Tjoss', 'sandra@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 1, 1, 1, '1_1.2', 'Rambo', 'Info', '', ''),
('micke@gmail.com', '123456', 'Test', 1, 2, 0, '1_0.1', 'Kul', 'Klart', '', ''),
('micke@gmail.com', '123456', 'Test', 1, 2, 0, '2_0.1', 'Mossa', 'Info', '', ''),
('micke@gmail.com', '123456', 'Test', 1, 2, 1, '1_1.1', 'Tjoss', 'sandra@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 1, 2, 1, '1_1.2', 'Rambo', 'micke@gmail.com', 'patrik@gmail.com', ''),
('micke@gmail.com', '123456', 'Test', 1, 3, 0, '2_0.1', 'Mossa', 'Klart', '', ''),
('micke@gmail.com', '123456', 'Test', 1, 3, 0, '3_0.1', 'Kul', 'Klart', '', ''),
('micke@gmail.com', '123456', 'Test', 1, 3, 1, '1_1.1', 'Tjoss', 'micke@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 1, 3, 1, '1_1.2', 'Rambo', 'sandra@gmail.com', 'patrik@gmail.com', ''),
('micke@gmail.com', '123456', 'Test', 1, 3, 1, '3_1.1', 'Rambo', 'Info', '', ''),
('micke@gmail.com', '123456', 'Test', 1, 3, 1, '3_1.2', 'Testar 2018-04-17', 'micke@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 1, 4, 1, '1_1.1', 'Tjoss', 'micke@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 1, 4, 1, '1_1.2', 'Rambo', 'sandra@gmail.com', 'patrik@gmail.com', ''),
('micke@gmail.com', '123456', 'Test', 1, 4, 1, '3_1.1', 'Rambo', 'Info', '', ''),
('micke@gmail.com', '123456', 'Test', 1, 4, 1, '3_1.2', 'Testar 2018-04-17', 'micke@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 1, 5, 1, '1_1.1', 'Tjoss', 'Klart', '', ''),
('micke@gmail.com', '123456', 'Test', 1, 5, 1, '1_1.2', 'Rambo', 'Klart', '', ''),
('micke@gmail.com', '123456', 'Test', 1, 5, 1, '3_1.1', 'Rambo', 'Klart', '', ''),
('micke@gmail.com', '123456', 'Test', 1, 5, 1, '3_1.2', 'Testar 2018-04-17', 'micke@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 1, 6, 1, '3_1.2', 'Testar 2018-04-17', 'micke@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 1, 6, 1, '6_1.1', 'Test av mötesprotokoll 2018-04-18', 'micke@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 1, 7, 1, '3_1.2', 'Testar 2018-04-17', 'micke@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 1, 7, 1, '6_1.1', 'Test av mötesprotokoll 2018-04-18', 'micke@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 1, 7, 1, '7_1.1', 'Bl.a. Bla', 'micke@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 2, 1, 0, '1_0.1', 'Test', 'micke@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 2, 2, 0, '1_0.1', 'Test', 'Klart', '', ''),
('micke@gmail.com', '123456', 'Test', 2, 3, 0, '3_0.1', 'Test', 'micke@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 2, 4, 0, '3_0.1', 'Test', 'micke@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 2, 4, 0, '4_0.1', 'Test 2018-04-30', 'micke@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 1, 0, '1_0.1', 'eraerbnerbner', 'sandra@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 1, 0, '1_0.2', 'rbebwerberberb', 'Info', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 1, 1, '1_1.1', 'rbaerbaerbearberab', 'Info', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 2, 0, '1_0.1', 'eraerbnerbner', 'sandra@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 2, 0, '1_0.2', 'rbebwerberberb', 'Info', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 2, 1, '1_1.1', 'rbaerbaerbearberab', 'Info', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 3, 0, '1_0.1', 'eraerbnerbner', 'sandra@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 3, 0, '1_0.2', 'rbebwerberberb', 'micke@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 3, 0, '3_0.1', 'rbebwerberberb', 'sandra@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 3, 1, '1_1.1', 'rbaerbaerbearberab', 'Info', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 3, 1, '3_1.1', 'rbaerbaerbearberab', 'Info', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 3, 1, '3_1.2', 'Test av uppgift', 'micke@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 3, 1, '3_1.3', 'Test av uppgift för Sandra', 'sandra@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 4, 0, '1_0.1', 'eraerbnerbner', 'Klart', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 4, 0, '1_0.2', 'rbebwerberberb', 'Klart', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 4, 0, '3_0.1', 'rbebwerberberb', 'Klart', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 4, 1, '1_1.1', 'rbaerbaerbearberab', 'Klart', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 4, 1, '3_1.1', 'rbaerbaerbearberab', 'Klart', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 4, 1, '3_1.2', 'Test av uppgift', 'Klart', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 4, 1, '3_1.3', 'Test av uppgift för Sandra', 'Klart', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 4, 1, '4_1.1', 'Test av uppgiftslista 2017-04-30', 'micke@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 5, 1, '4_1.1', 'Test av uppgiftslista 2017-04-30', 'Klart', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 6, 1, '6_1.1', 'dndfnfgnfgnbfbfb', 'micke@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 7, 1, '6_1.1', 'dndfnfgnfgnbfbfb', 'micke@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 3, 8, 1, '6_1.1', 'dndfnfgnfgnbfbfb', 'Klart', '', ''),
('micke@gmail.com', '123456', 'Test', 4, 1, 0, '1_0.1', 'Test', 'micke@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 4, 2, 0, '1_0.1', 'Test', 'micke@gmail.com', '', ''),
('micke@gmail.com', '123456', 'Test', 4, 2, 0, '2_0.1', 'Test 2', 'Klart', '', ''),
('micke@gmail.com', '123456', 'Test', 4, 3, 0, '1_0.1', 'Test', 'Klart', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `db_project_meeting_type`
--

DROP TABLE IF EXISTS `db_project_meeting_type`;
CREATE TABLE IF NOT EXISTS `db_project_meeting_type` (
  `id` int(11) NOT NULL,
  `type` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `db_project_meeting_type`
--

INSERT INTO `db_project_meeting_type` (`id`, `type`) VALUES
(1, 'Möte'),
(2, 'Byggmöte'),
(3, 'Ekonomimöte'),
(4, 'Startmöte'),
(5, 'Samordningsmöte'),
(6, 'Projekteringsmöte'),
(7, 'Planeringsmöte'),
(8, 'Besiktningsmöte'),
(9, 'Projekteringssamordningsmöte'),
(10, 'Projektmöte'),
(11, 'Programmöte');

-- --------------------------------------------------------

--
-- Table structure for table `db_project_member`
--

DROP TABLE IF EXISTS `db_project_member`;
CREATE TABLE IF NOT EXISTS `db_project_member` (
  `client` varchar(255) NOT NULL,
  `number` varchar(20) NOT NULL,
  `name` varchar(30) NOT NULL,
  `user` varchar(255) NOT NULL,
  `title` varchar(40) NOT NULL,
  `permission` tinyint(11) NOT NULL,
  PRIMARY KEY (`client`,`number`,`name`,`user`),
  KEY `title` (`title`),
  KEY `permission` (`permission`),
  KEY `db_project_member_ibfk_2` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `db_project_member`
--

INSERT INTO `db_project_member` (`client`, `number`, `name`, `user`, `title`, `permission`) VALUES
('micke@gmail.com', '01', 'Magnus W', 'info@holisticsolutions.se', 'Projektledare', 1),
('micke@gmail.com', '01', 'Magnus W', 'micke@gmail.com', 'Datasamordnare', 1),
('micke@gmail.com', '01', 'SBUPP', 'erik.landstrom@sl.se', 'Projekteringsledare', 1),
('micke@gmail.com', '01', 'SBUPP', 'micke@gmail.com', 'Datasamordnare', 1),
('micke@gmail.com', '02', 'SB 24', 'erik.landstrom@sl.se', 'Projekteringsledare', 1),
('micke@gmail.com', '02', 'SB 24', 'micke@gmail.com', 'Datasamordnare', 1),
('micke@gmail.com', '03', 'SBkap', 'micke@gmail.com', 'Datasamordnare', 1),
('micke@gmail.com', '123456', 'Test', 'ake@gmail.com', 'Projektör', 4),
('micke@gmail.com', '123456', 'Test', 'elsa@gmail.com', 'Besiktningsman', 5),
('micke@gmail.com', '123456', 'Test', 'eva@gmail.com', 'Projektör', 4),
('micke@gmail.com', '123456', 'Test', 'lars@gmail.com', 'Byggledare', 7),
('micke@gmail.com', '123456', 'Test', 'micke@gmail.com', 'Datasamordnare', 1),
('micke@gmail.com', '123456', 'Test', 'rasmus@gmail.com', 'Arbetsledare', 5),
('micke@gmail.com', '123456', 'Test', 'sandra@gmail.com', 'Projektör', 4),
('simon@gmail.com', '246', 'Kuken', 'simon@gmail.com', 'Datasamordnare', 1);

-- --------------------------------------------------------

--
-- Table structure for table `db_project_member_title`
--

DROP TABLE IF EXISTS `db_project_member_title`;
CREATE TABLE IF NOT EXISTS `db_project_member_title` (
  `title` varchar(40) NOT NULL,
  PRIMARY KEY (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `db_project_member_title`
--

INSERT INTO `db_project_member_title` (`title`) VALUES
('Arbetsledare'),
('BAS-P'),
('BAS-U'),
('Besiktningsman'),
('Byggledare'),
('Datasamordnare'),
('Gruppledare'),
('Miljösamordnare'),
('Platschef'),
('Produktionsledare'),
('Programledare'),
('Projektadministratör'),
('Projekteringsledare'),
('Projektledare'),
('Projektör'),
('Risk och säkerhetssamordnare');

-- --------------------------------------------------------

--
-- Table structure for table `db_project_monthly_report`
--

DROP TABLE IF EXISTS `db_project_monthly_report`;
CREATE TABLE IF NOT EXISTS `db_project_monthly_report` (
  `client` varchar(255) NOT NULL,
  `number` varchar(20) NOT NULL,
  `name` varchar(30) NOT NULL,
  `author` varchar(255) NOT NULL,
  `year` int(11) NOT NULL,
  `month` tinyint(4) NOT NULL,
  `program` varchar(60) DEFAULT NULL,
  `role` varchar(40) NOT NULL,
  `supervisor` varchar(80) NOT NULL,
  `company` varchar(11) NOT NULL,
  `locked` tinyint(2) NOT NULL,
  PRIMARY KEY (`client`,`number`,`name`,`author`,`year`,`month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `db_project_monthly_report`
--

INSERT INTO `db_project_monthly_report` (`client`, `number`, `name`, `author`, `year`, `month`, `program`, `role`, `supervisor`, `company`, `locked`) VALUES
('micke@gmail.com', '123456', 'Test', 'ake@gmail.com', 2018, 4, 'SB', 'Projektör', 'Jag', '556583-9460', 0),
('micke@gmail.com', '123456', 'Test', 'eva@gmail.com', 2018, 3, 'SB', 'Projektör', 'Micke', '559070-2915', 0),
('micke@gmail.com', '123456', 'Test', 'eva@gmail.com', 2018, 4, 'SB', 'Projektör', 'Micke', '559070-2915', 0),
('micke@gmail.com', '123456', 'Test', 'sandra@gmail.com', 2018, 2, 'SB', 'Projektör', 'Micke', '559070-2915', 1),
('micke@gmail.com', '123456', 'Test', 'sandra@gmail.com', 2018, 3, 'SB', 'Projektör', 'Janne', '559070-2915', 1),
('micke@gmail.com', '123456', 'Test', 'sandra@gmail.com', 2018, 4, 'SB', 'Projektör', 'Micke', '559070-2915', 0),
('micke@gmail.com', '123456', 'Test', 'sandra@gmail.com', 2018, 5, 'SB', 'Projektör', 'Micke', '559070-2915', 0);

-- --------------------------------------------------------

--
-- Table structure for table `db_project_monthly_report_day`
--

DROP TABLE IF EXISTS `db_project_monthly_report_day`;
CREATE TABLE IF NOT EXISTS `db_project_monthly_report_day` (
  `client` varchar(255) NOT NULL,
  `number` varchar(20) NOT NULL,
  `name` varchar(30) NOT NULL,
  `author` varchar(255) NOT NULL,
  `year` int(11) NOT NULL,
  `month` tinyint(4) NOT NULL,
  `day` tinyint(4) NOT NULL,
  `job` varchar(255) NOT NULL,
  `time` float NOT NULL,
  PRIMARY KEY (`client`,`number`,`name`,`author`,`year`,`month`,`day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `db_project_monthly_report_day`
--

INSERT INTO `db_project_monthly_report_day` (`client`, `number`, `name`, `author`, `year`, `month`, `day`, `job`, `time`) VALUES
('micke@gmail.com', '123456', 'Test', 'ake@gmail.com', 2018, 4, 2, 'Mvyhjcvc', 8),
('micke@gmail.com', '123456', 'Test', 'eva@gmail.com', 2018, 3, 2, 'Test', 5),
('micke@gmail.com', '123456', 'Test', 'eva@gmail.com', 2018, 4, 2, 'ewfwefwefewfwefewf', 50),
('micke@gmail.com', '123456', 'Test', 'sandra@gmail.com', 2018, 2, 8, 'Test', 8),
('micke@gmail.com', '123456', 'Test', 'sandra@gmail.com', 2018, 2, 10, 'Nu funkar det', 4),
('micke@gmail.com', '123456', 'Test', 'sandra@gmail.com', 2018, 3, 5, 'Test', 4),
('micke@gmail.com', '123456', 'Test', 'sandra@gmail.com', 2018, 3, 9, 'ok', 5),
('micke@gmail.com', '123456', 'Test', 'sandra@gmail.com', 2018, 4, 3, 'test', 5),
('micke@gmail.com', '123456', 'Test', 'sandra@gmail.com', 2018, 5, 1, 'Test', 8),
('micke@gmail.com', '123456', 'Test', 'sandra@gmail.com', 2018, 5, 10, 'test', 8);

-- --------------------------------------------------------

--
-- Table structure for table `db_project_permission`
--

DROP TABLE IF EXISTS `db_project_permission`;
CREATE TABLE IF NOT EXISTS `db_project_permission` (
  `id` tinyint(11) NOT NULL,
  `type` varchar(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `db_project_permission`
--

INSERT INTO `db_project_permission` (`id`, `type`) VALUES
(1, 'B1'),
(2, 'B2'),
(3, 'B3'),
(4, 'P1'),
(5, 'E1'),
(6, 'E2'),
(7, 'E3');

-- --------------------------------------------------------

--
-- Table structure for table `db_project_task`
--

DROP TABLE IF EXISTS `db_project_task`;
CREATE TABLE IF NOT EXISTS `db_project_task` (
  `client` varchar(255) NOT NULL,
  `number` varchar(20) NOT NULL,
  `name` varchar(30) NOT NULL,
  `id` int(11) NOT NULL,
  `category` varchar(60) NOT NULL,
  `created` date NOT NULL,
  `question` varchar(255) NOT NULL,
  `supervisor1` varchar(255) NOT NULL,
  `supervisor2` varchar(255) DEFAULT NULL,
  `supervisor3` varchar(255) DEFAULT NULL,
  `author` varchar(255) NOT NULL,
  `deadline` date DEFAULT NULL,
  `answer` varchar(255) DEFAULT NULL,
  `completed` date DEFAULT NULL,
  `worker` varchar(255) DEFAULT NULL,
  `private` tinyint(1) NOT NULL,
  PRIMARY KEY (`client`,`number`,`name`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `db_project_task`
--

INSERT INTO `db_project_task` (`client`, `number`, `name`, `id`, `category`, `created`, `question`, `supervisor1`, `supervisor2`, `supervisor3`, `author`, `deadline`, `answer`, `completed`, `worker`, `private`) VALUES
('micke@gmail.com', '01', 'Magnus W', 1, 'Bolag', '2018-04-27', 'Fundera kring investerare och värde på bolag', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-04-30', 'Utgår', '2018-05-20', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'Magnus W', 2, 'Föreläsning', '2018-04-27', 'Färdigställa kursmaterial', 'info@holisticsolutions.se', '', '', 'micke@gmail.com', '2018-05-18', '', NULL, '', 0),
('micke@gmail.com', '01', 'SBUPP', 1, 'Projektering', '2018-04-06', 'Lägg in Mofix egenkontroll på PP', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Upplagt 2018-04-10', '2018-04-12', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 2, 'Projektering', '2018-04-06', 'Skapa en F/S om hur långt Nacka svara för gestaltning på bron', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Klart', '2018-04-18', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 3, 'Projektering', '2018-04-06', 'Maila Nacka och Projledare om att fylla i liggare när man besvarat en fråga', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Tas upp på mötet, Erik L är informerad', '2018-04-18', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 4, 'Projektering', '2018-04-06', 'Skapa ett dokument som beskriver angränsande projekt', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Skickat till Erik för "granskning"', '2018-04-18', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 5, 'Projektering', '2018-04-06', 'Formatera rubriker och skicka till Tomas för godkännande', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Skickat', '2018-04-12', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 6, 'Projektering', '2018-04-06', 'Läs igenom SH och speciellt avsnittet om dagvattenmagasin. Och red ut om det är idé med 2st.', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0),
('micke@gmail.com', '01', 'SBUPP', 7, 'Projektering', '2018-04-06', 'Skriv ut viktiga dok. Såsom ATR o Tidplan', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0),
('micke@gmail.com', '01', 'SBUPP', 8, 'Projektering', '2018-04-06', 'Stäm av tidigare granskningssynpunkter', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0),
('micke@gmail.com', '01', 'SBUPP', 9, 'Resurs', '2018-04-12', 'Lägg in i kommande protokoll att Jonatan har resurs för inmätning. Han kollar om resursen har möjlighet att hjälpa till och återkommer med info', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Erik är tillsagd att göra detta då jag är ledag', '2018-04-16', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 10, 'Övrigt', '2018-04-12', 'Lägg in dagbok på PP för SBUPP', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Jan och Feb upplagd', '2018-04-18', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 11, 'Planering', '2018-04-16', 'Läs på om BAS-P och utför', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0),
('micke@gmail.com', '01', 'SBUPP', 12, 'Projektering', '2018-04-17', 'Kalla till Modellfilsmöte', 'erik.landstrom@sl.se', '', '', 'micke@gmail.com', NULL, 'Klart', '2018-04-18', 'erik.landstrom@sl.se', 0),
('micke@gmail.com', '01', 'SBUPP', 13, 'Övrigt', '2018-04-17', 'Granska miljöchecklista', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Excelfil skickad till sanna idag', '2018-04-17', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 14, 'Möte', '2018-04-17', 'Lägga in länk till sem lista i protokoll, https://service.projectplace.com/pp/pp.cgi/r843935287', 'erik.landstrom@sl.se', '', '', 'micke@gmail.com', NULL, 'Klart', '2018-04-18', 'erik.landstrom@sl.se', 0),
('micke@gmail.com', '01', 'SBUPP', 15, 'Möte', '2018-04-18', 'Lägg in info om FUT:s webforum i kommande protokoll', 'erik.landstrom@sl.se', '', '', 'micke@gmail.com', NULL, 'Klart', '2018-04-23', 'erik.landstrom@sl.se', 0),
('micke@gmail.com', '01', 'SBUPP', 16, 'Möte', '2018-04-18', 'Hänvisa till F/S rutiner för att påvisa hur man tex fyller i Liggaren. https://service.projectplace.com/pp/pp.cgi/r1399281473', 'erik.landstrom@sl.se', '', '', 'micke@gmail.com', NULL, 'Klart', '2018-04-23', 'erik.landstrom@sl.se', 0),
('micke@gmail.com', '01', 'SBUPP', 17, 'Projektering', '2018-04-18', 'Stäm av tidigare granskningssynpunkter', 'erik.landstrom@sl.se', '', '', 'micke@gmail.com', NULL, 'Klart', '2018-06-26', 'erik.landstrom@sl.se', 0),
('micke@gmail.com', '01', 'SBUPP', 18, 'Möte', '2018-04-18', 'Lägg in länk för avgränsande projekt. https://service.projectplace.com/pp/pp.cgi/r869374315', 'erik.landstrom@sl.se', '', '', 'micke@gmail.com', NULL, 'Klart', '2018-04-23', 'erik.landstrom@sl.se', 0),
('micke@gmail.com', '01', 'SBUPP', 19, 'Resurs', '2018-04-18', 'Intern kontaktlista', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Klart, finns redan', '2018-04-30', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 20, 'Projektering', '2018-04-19', 'Delge FLi timpriser för projektörer.', 'erik.landstrom@sl.se', '', '', 'erik.landstrom@sl.se', NULL, 'Klart', '2018-04-23', 'erik.landstrom@sl.se', 0),
('micke@gmail.com', '01', 'SBUPP', 21, 'Projektering', '2018-04-19', 'Eftersök uppdaterad grundkarta hos FUT samt uppdaterad ledningssamordningsmodell från FUT.', 'erik.landstrom@sl.se', '', '', 'erik.landstrom@sl.se', NULL, 'Klart', '2018-04-23', 'erik.landstrom@sl.se', 0),
('micke@gmail.com', '01', 'SBUPP', 22, 'Projektering', '2018-04-23', 'Kalla till granskningsmöte 27/8 för systemhandling', 'micke@gmail.com', '', '', 'erik.landstrom@sl.se', '2018-08-27', 'Klart', '2018-04-23', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 23, 'Projektering', '2018-04-23', 'Kalla till granskningsmöte 27/8 för systemhandling', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-04-27', 'Klart', '2018-05-30', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 24, 'Projektering', '2018-04-24', 'Ta reda på vart man hittar PM 2015-11-16 version 1 och Upphöjning av Nacka station, 2015-09-22', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-04-25', 'Klart', '2018-05-20', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 25, 'Projektering', '2018-04-24', 'Efterfråga ritningar i redigerbart format', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-04-25', 'Zoltan är meddelad att dessa finns på Storegate', '2018-05-03', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 26, 'Projektering', '2018-04-26', 'Se om lathund behövs', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-01', 'Klart', '2018-05-20', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 27, 'Möte', '2018-04-26', 'Skall man ev stämma av ATR och kostnader på mötet', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-16', '', NULL, '', 0),
('micke@gmail.com', '01', 'SBUPP', 28, 'Projektering', '2018-05-02', 'Kontrollera och skapa "projekteringsunderlag" dokument till projektörerna', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-10', 'Utgår', '2018-05-11', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 29, 'Övrigt', '2018-05-03', 'Klistra in viktiga tider på pärm sida', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-07', '', NULL, '', 0),
('micke@gmail.com', '01', 'SBUPP', 30, 'Möte', '2018-05-03', 'Lägg in punkt 1_3.7 från modellfilsmötet', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-15', 'Klart', '2018-06-01', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 31, 'Projektering', '2018-05-03', 'Skapa "dokument och ritningsförteckning"', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-11', 'Utgår', '2018-05-03', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 32, 'Projektering', '2018-05-03', 'Jakob ek(d)blad på WSP ang. Railview film', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0),
('micke@gmail.com', '01', 'SBUPP', 33, 'Projektering', '2018-05-03', 'Skapa "dokument och ritningsförteckning"', 'erik.landstrom@sl.se', '', '', 'micke@gmail.com', '2018-05-11', 'Klart', '2018-06-26', 'erik.landstrom@sl.se', 0),
('micke@gmail.com', '01', 'SBUPP', 34, 'Projektering', '2018-05-03', 'Skapa en dokument och ritningsförteckning', 'erik.landstrom@sl.se', '', '', 'micke@gmail.com', '2018-05-11', 'Klart', '2018-06-26', 'erik.landstrom@sl.se', 0),
('micke@gmail.com', '01', 'SBUPP', 35, 'Projektering', '2018-05-03', 'Skapa en dokument och ritningsförteckning', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-11', 'Klart', '2018-05-30', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 36, 'Granskning', '2018-05-03', 'Sannas Miljökravlista', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-11', 'Svar skickat', '2018-05-11', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 37, 'Projektering', '2018-05-03', 'Skicka sträcka till Jakob på WSP', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-03', 'Utfört 4/5', '2018-05-07', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 38, 'Projektering', '2018-05-09', 'Se över punkt 3_4.10 i projekteringsprotokoll med Sanna', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-16', 'Klar', '2018-06-01', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 39, 'Projektering', '2018-05-09', 'Se över ATC med Simon samt bjud in till möte', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-16', 'Klar', '2018-06-01', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 40, 'Projektering', '2018-05-09', 'Vilken teknik finns i LS Sickla?', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-16', '', NULL, '', 0),
('micke@gmail.com', '01', 'SBUPP', 41, 'Projektering', '2018-05-09', 'Kolla med Sanna om Åke och buller punkt i projekteringsprotokoll', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-16', 'Utgår', '2018-05-31', 'micke@gmail.com', 1),
('micke@gmail.com', '01', 'SBUPP', 42, 'Möte', '2018-05-09', 'Kolla med Angelica om riskutredning ligger på PP', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-16', 'Klart', '2018-05-18', 'micke@gmail.com', 1),
('micke@gmail.com', '01', 'SBUPP', 43, 'Projektering', '2018-05-14', 'Skicka ut mall för projekteringsunderlag till projekteringsledare', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0),
('micke@gmail.com', '01', 'SBUPP', 44, 'Projektering', '2018-05-15', 'Lägg upp kontaktlista för projektörer', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-18', '', NULL, '', 0),
('micke@gmail.com', '01', 'SBUPP', 45, 'Övrigt', '2018-05-15', 'Se över om man kan lägga till resurser och se krockar i MS Project', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-15', 'Klart', '2018-06-01', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 46, 'Möte', '2018-05-15', 'Ändra lokal till Vänern', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-15', 'Klart', '2018-05-18', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 47, 'Möte', '2018-05-15', 'Skicka påminnelse om punkt från protokoll', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-15', 'Klart', '2018-05-18', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 48, 'Möte', '2018-05-18', 'Skriv mötesprotokoll', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-21', 'Klart', '2018-06-01', 'micke@gmail.com', 0),
('micke@gmail.com', '01', 'SBUPP', 49, 'Möte', '2018-06-05', 'Lägg in frågan om Alt 4 spårprofil som en ny punkt, Jonatan är positiv till detta, vad säger Nacka?', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-06-13', '', NULL, '', 0),
('micke@gmail.com', '01', 'SBUPP', 50, 'Projektmöte', '2018-06-14', 'Eftersök motivering till varför man valt den spårlinje man gjort', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-06-18', '', NULL, '', 0),
('micke@gmail.com', '01', 'SBUPP', 51, 'GF', '2018-06-14', 'Skapa en lista på aktiviteter för GF bilaga från SH', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-06-18', '', NULL, '', 0),
('micke@gmail.com', '01', 'SBUPP', 52, 'Projektering', '2018-06-26', 'Stäm av huruvida ledningssamordningsplaner är tillräckliga eller ej', 'erik.landstrom@sl.se', '', '', 'erik.landstrom@sl.se', '2018-06-28', '', NULL, '', 0),
('micke@gmail.com', '02', 'SB 24', 1, 'Mot TF Slu', '2018-04-06', 'Besvara Yemanes mail om tider', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Skickat 2018-04-16, tidplansmöte skall avhållas med David', '2018-04-18', 'micke@gmail.com', 0),
('micke@gmail.com', '02', 'SB 24', 2, 'Planering', '2018-04-06', 'Fyll i Tomas PM fil på PP', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0),
('micke@gmail.com', '02', 'SB 24', 3, 'Granskning', '2018-04-12', 'Granska SN83, läs mail från Gunilla 2018-04-12', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Dubblett', '2018-05-03', 'micke@gmail.com', 0),
('micke@gmail.com', '02', 'SB 24', 4, 'Granskning', '2018-04-16', 'Kontrollera vilken växel som används i SLU i SH', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Info skickat till Tra 2018-04-16', '2018-04-17', 'micke@gmail.com', 0),
('micke@gmail.com', '02', 'SB 24', 5, 'Övrigt', '2018-04-17', 'Leta och läs granskningssynpunkter för växel i Slu', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0),
('micke@gmail.com', '02', 'SB 24', 6, 'Övrigt', '2018-04-17', 'Ta fram en gränssnittslista mellan SB BEST och TF Slu i kolumn för genomförandeavtal ink ritningar', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Utgår', '2018-05-04', 'micke@gmail.com', 0),
('micke@gmail.com', '02', 'SB 24', 7, 'Övrigt', '2018-04-17', 'Be Tobias kolla av lösning med 300 växel i Slu, om möjligt be David skriva en ensidig konsekvensrapport för detta.', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Uppgift skickad till Tobias och Blanka idag', '2018-04-18', 'micke@gmail.com', 0),
('micke@gmail.com', '02', 'SB 24', 8, 'Granskning', '2018-04-18', 'Elmatning till fläktrum. Kolla med Johannes. Hur är det med samma matning för olika fastigheter?', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0),
('micke@gmail.com', '02', 'SB 24', 9, 'Granskning', '2018-04-18', 'Se över resurser för granskning av SN83 (Brand från Jonatan?) Dela upp handlingen i det som berör SB', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Klart', '2018-05-04', 'micke@gmail.com', 0),
('micke@gmail.com', '02', 'SB 24', 10, 'Granskning', '2018-05-02', 'Granska SN83 samt punkt 8 o 9 i denna lista', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-03', 'Klart', '2018-05-04', 'micke@gmail.com', 0),
('micke@gmail.com', '02', 'SB 24', 11, 'Övrigt', '2018-05-04', 'Gränsdragningslista i plan och sektion mot TF slussen / SB', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-25', '', NULL, '', 0),
('micke@gmail.com', '02', 'SB 24', 12, 'Övrigt', '2018-05-04', 'Input till staden från stadens gränsdragningslista vad gäller 5.7 - 5.19', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0),
('micke@gmail.com', '02', 'SB 24', 13, 'Input', '2018-05-04', 'Redogöra för vad som behövs gällande avvattning i Slu, kolla med projektörer från Neglinge', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-15', 'deblett', '2018-06-01', 'micke@gmail.com', 0),
('micke@gmail.com', '02', 'SB 24', 14, 'Input', '2018-05-04', 'Redogöra för vad som behövs gällande avvattning i Slu Teknikrum, kolla med projektörer från Neglinge', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-15', '', NULL, '', 0),
('micke@gmail.com', '02', 'SB 24', 15, 'Input', '2018-05-04', 'Godkänn Teknikrums utbredning i Slu', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-15', '', NULL, '', 0),
('micke@gmail.com', '02', 'SB 24', 16, 'Input', '2018-05-04', 'Input för gestaltning i Slu tex matrisgjutning. Ev Yemanes uppgift', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-15', '', NULL, '', 0),
('micke@gmail.com', '02', 'SB 24', 17, 'Input', '2018-05-04', 'Input för kanalisation och matning av fläktar i Slu, matning av fläktrum saknas idag', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-15', '', NULL, '', 0),
('micke@gmail.com', '02', 'SB 24', 18, 'Resurser', '2018-05-04', 'Se över med Tomas hur vi ska göra med Systemhandling/systembeskrivning för att vara mer behjälplig mot staden, de skall vara klar med sin förslagshandling i okt ', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-11', '', NULL, '', 0),
('micke@gmail.com', '02', 'SB 24', 19, 'Input', '2018-05-09', 'Delge input för vent vid låga höjder i Slu (partiklar vid spårområde)', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-18', '', NULL, '', 0),
('micke@gmail.com', '02', 'SB 24', 20, 'Input', '2018-05-09', 'Finns det behov av servicetunnel till plf Slu?', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-18', '', NULL, '', 0),
('micke@gmail.com', '02', 'SB 24', 21, 'Övrigt', '2018-05-09', 'Maila underlag för gestaltning till Yemane', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-11', 'Klart', '2018-05-09', 'micke@gmail.com', 1),
('micke@gmail.com', '02', 'SB 24', 22, 'Input', '2018-05-30', 'Heli vill ha input för om man kommer sätta någon signalutrustning på nya bron ovan bussinfart', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-06-08', '', NULL, '', 0),
('micke@gmail.com', '02', 'SB 24', 23, 'Tider', '2018-05-30', 'Delge tider till de som berörs', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-06-08', '', NULL, '', 0),
('micke@gmail.com', '02', 'SB 24', 24, 'Övrigt', '2018-05-30', 'När skall tunneln rustas? delge Patrik Lidgren info', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-06-08', '', NULL, '', 0),
('micke@gmail.com', '02', 'SB 24', 25, 'Övrigt', '2018-05-30', 'Lägg upp genomförandeavtal på PP', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-06-08', '', NULL, '', 0),
('micke@gmail.com', '02', 'SB 24', 26, 'Övrigt', '2018-05-30', 'Skicka förslag på hur SH kan delas upp och varför', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-06-08', '', NULL, '', 0),
('micke@gmail.com', '02', 'SB 24', 27, 'Övrigt', '2018-05-30', 'När skall LS SLu byggas och vart, kolla med Yemane och delge Tomas', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-06-01', '', NULL, '', 0),
('micke@gmail.com', '02', 'SB 24', 28, 'Övrigt', '2018-06-07', 'Sammanföra Stadens Vent Projektör med vår kalle', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-06-07', '', NULL, '', 0),
('micke@gmail.com', '03', 'SBkap', 1, 'Förberedelser', '2018-04-12', 'Hitta och läs de SH som finns för SBKap', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0),
('micke@gmail.com', '03', 'SBkap', 2, 'Möte', '2018-04-12', 'Lägg in ny punkt i kommande protokoll att man tar stor hänsyn till återanvändning av material i SH', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0),
('micke@gmail.com', '03', 'SBkap', 3, 'Resurs', '2018-04-18', 'Intern kontaktlista', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0),
('micke@gmail.com', '03', 'SBkap', 4, 'Tidplan', '2018-05-02', 'Granska ny tidplan', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-10', 'Info skickad till Richard o Tomas', '2018-05-11', 'micke@gmail.com', 0),
('micke@gmail.com', '03', 'SBkap', 5, 'Möte', '2018-05-16', 'Sätt ihop mötesserie utifrån Tidplan', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-05-18', 'Klart', '2018-05-31', 'micke@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 1, 'Dagbok', '2018-02-07', 'Testing', 'eva@gmail.com', '', '', 'micke@gmail.com', NULL, 'Ingen vet vällejvhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh', '2018-02-07', 'eva@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 2, 'Dagbok', '2018-02-07', 'Test 2', 'eva@gmail.com', '', '', 'micke@gmail.com', NULL, 'sdbddf', '2018-02-08', 'eva@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 3, 'Dagbok', '2018-02-07', 'Endast den berördas uppgifter skall synas ', 'sandra@gmail.com', '', '', 'sandra@gmail.com', NULL, 'Klart', '2018-02-09', 'sandra@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 4, 'Dagbok', '2018-02-12', 'Test 3', 'sandra@gmail.com', '', '', 'eva@gmail.com', NULL, 'Klart/Micke', '2018-02-12', 'sandra@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 5, 'Dagbok', '2018-02-12', 'Läsa och förstå handling', 'micke@gmail.com', '', '', 'eva@gmail.com', NULL, 'Klart', '2018-02-12', 'micke@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 6, 'Test', '2018-02-13', 'Men ', 'sandra@gmail.com', '', '', 'eva@gmail.com', NULL, 'Vill inte', '2018-02-14', 'sandra@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 7, 'Serie: 1 Möte: 3', '2018-04-17', 'Kul', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Utgår', '2018-04-18', 'micke@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 8, 'Serie: 1 Möte: 3', '2018-04-17', 'Tjoss', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Utgår', '2018-04-18', 'micke@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 9, 'Serie: 1 Möte: 3', '2018-04-17', 'Rambo', 'sandra@gmail.com', 'patrik@gmail.com', '', 'micke@gmail.com', NULL, 'Bra', '2018-04-30', 'sandra@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 10, 'Datum', '2018-04-23', 'Testar', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Klar', '2018-04-30', 'micke@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 11, 'Datum', '2018-04-23', 'Testar 2', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-04-23', 'Klart', '2018-04-23', 'micke@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 12, 'Datum', '2018-04-23', 'Testar 3', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-04-24', 'Klart', '2018-04-23', 'micke@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 13, 'Serie: 2 Möte: 4', '2018-04-30', 'Test', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Klart', '2018-04-30', 'micke@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 14, 'Serie: 2 Möte: 4', '2018-04-30', 'Test 2018-04-30', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Klart', '2018-04-30', 'micke@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 15, 'Test', '2018-04-30', 'Test', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0),
('micke@gmail.com', '123456', 'Test', 16, 'Rasmus', '2018-04-30', 'Spar test', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Klart', '2018-04-30', 'micke@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 17, 'Test', '2018-04-30', 'Test', 'sandra@gmail.com', '', '', 'micke@gmail.com', '2018-04-30', '', NULL, '', 0),
('micke@gmail.com', '123456', 'Test', 18, 'Serie: 3 Möte: 6', '2018-05-02', 'dndfnfgnfgnbfbfb', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Utgår', '2018-05-02', 'micke@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 19, 'Hemsida', '2018-05-05', 'Uppdatera Hemsidan med tex prislistan', 'micke@gmail.com', '', '', 'micke@gmail.com', '2018-06-29', '', NULL, '', 0),
('micke@gmail.com', '123456', 'Test', 20, 'Serie: 2 Möte: 1', '2018-05-12', 'Test', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0),
('micke@gmail.com', '123456', 'Test', 21, 'Serie: 1 Möte: 1', '2018-05-12', 'Kul', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0),
('micke@gmail.com', '123456', 'Test', 22, 'Serie: 1 Möte: 1', '2018-05-12', 'Tjoss', 'sandra@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0),
('micke@gmail.com', '123456', 'Test', 23, 'Serie: 4 Möte: 1', '2018-05-12', 'Test', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Klart', '2018-05-12', 'micke@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 24, 'Serie: 3 Möte: 3', '2018-05-13', 'eraerbnerbner', 'sandra@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0),
('micke@gmail.com', '123456', 'Test', 25, 'Serie: 3 Möte: 3', '2018-05-13', 'rbebwerberberb', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0),
('micke@gmail.com', '123456', 'Test', 26, 'Serie: 3 Möte: 3', '2018-05-13', 'Test av uppgift', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0),
('micke@gmail.com', '123456', 'Test', 27, 'Serie: 3 Möte: 3', '2018-05-13', 'Test av uppgift för Sandra', 'sandra@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0),
('micke@gmail.com', '123456', 'Test', 28, 'Serie: 3 Möte: 2', '2018-05-13', 'eraerbnerbner', 'sandra@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0),
('micke@gmail.com', '123456', 'Test', 29, 'Serie: 3 Möte: 1', '2018-05-13', 'eraerbnerbner', 'sandra@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0),
('micke@gmail.com', '123456', 'Test', 30, 'Serie: 4 Möte: 2', '2018-05-14', 'Test', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, 'Klart vid möte: 3', '2018-05-30', 'micke@gmail.com', 0),
('micke@gmail.com', '123456', 'Test', 31, 'Test', '2018-05-30', 'Test "test"', 'micke@gmail.com', '', '', 'micke@gmail.com', NULL, '', NULL, '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `db_user`
--

DROP TABLE IF EXISTS `db_user`;
CREATE TABLE IF NOT EXISTS `db_user` (
  `email` varchar(255) NOT NULL,
  `ssnumber` varchar(13) DEFAULT NULL,
  `firstname` varchar(40) DEFAULT NULL,
  `surname` varchar(40) DEFAULT NULL,
  `password` varchar(30) NOT NULL,
  `phonenumber` varchar(11) DEFAULT NULL,
  `company` varchar(11) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `db_user`
--

INSERT INTO `db_user` (`email`, `ssnumber`, `firstname`, `surname`, `password`, `phonenumber`, `company`, `admin`) VALUES
('ake@gmail.com', '19820215-7512', 'Åke', 'Larsson', 'pass', '070-1256987', '556583-9460', 0),
('elsa@gmail.com', '19830215-7510', 'Elsa', 'Norring', 'pass', '070-123459', '559070-2915', 0),
('erik.landstrom@sl.se', '', 'Erik', 'Landström', 'pass', '070-8287986', '556911-3268', 0),
('eva@gmail.com', '', 'Eva', 'Larsson', 'pass', '070-7654321', '559070-2915', 0),
('info@holisticsolutions.se', '', 'Magnus', 'Whalbeck', 'pass', '076-4197655', '556982-3841', 0),
('lars@gmail.com', '', 'Lars', 'Olsson', 'pass', '070-1234567', '559070-2915', 0),
('micke@gmail.com', '', 'Mikael', 'Wållner', 'pass', '070-5522747', '559070-2915', 1),
('rasmus@gmail.com', '', 'Rasmus', 'Lindqvist', 'pass', '073-0865656', '559070-2915', 0),
('sandra@gmail.com', '', 'Sandra', 'Wållner', 'pass', '073-0856656', '559070-2915', 0),
('simon@gmail.com', '', 'Simon', 'Nyqvist', 'pass', '850-5486431', '559070-2915', 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `db_project_diary`
--
ALTER TABLE `db_project_diary`
  ADD CONSTRAINT `db_project_diary_ibfk_2` FOREIGN KEY (`author`) REFERENCES `db_project_member` (`user`);

--
-- Constraints for table `db_project_diary_job`
--
ALTER TABLE `db_project_diary_job`
  ADD CONSTRAINT `db_project_diary_job_ibfk_1` FOREIGN KEY (`status`) REFERENCES `db_project_diary_statustype` (`index`);

--
-- Constraints for table `db_project_diary_misc`
--
ALTER TABLE `db_project_diary_misc`
  ADD CONSTRAINT `db_project_diary_misc_ibfk_1` FOREIGN KEY (`category`) REFERENCES `db_project_diary_categorytype` (`type`);

--
-- Constraints for table `db_project_member`
--
ALTER TABLE `db_project_member`
  ADD CONSTRAINT `db_project_member_ibfk_1` FOREIGN KEY (`title`) REFERENCES `db_project_member_title` (`title`),
  ADD CONSTRAINT `db_project_member_ibfk_2` FOREIGN KEY (`user`) REFERENCES `db_user` (`email`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
