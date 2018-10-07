-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 25, 2011 at 04:02 PM
-- Server version: 5.1.41
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `EA`
--

-- --------------------------------------------------------

--
-- Table structure for table `Chromosome`
--

CREATE TABLE IF NOT EXISTS `Chromosome` (
  `ChromosomeID` int(11) NOT NULL AUTO_INCREMENT,
  `GeneIDs` varchar(255) COLLATE utf8_bin NOT NULL,
  `Default` tinyint(4) NOT NULL DEFAULT '0',
  `Fitness` float DEFAULT NULL,
  `Vinner` tinyint(4) NOT NULL DEFAULT '0',
  `Created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ChromosomeID`),
  KEY `GeneID` (`GeneIDs`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=340 ;

-- --------------------------------------------------------

--
-- Table structure for table `Context`
--

CREATE TABLE IF NOT EXISTS `Context` (
  `ContextID` int(11) NOT NULL AUTO_INCREMENT,
  `Data` longtext COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`ContextID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `FitnessRelation`
--

CREATE TABLE IF NOT EXISTS `FitnessRelation` (
  `FitnessRelationID` int(11) NOT NULL AUTO_INCREMENT,
  `GeneID` int(11) NOT NULL,
  `Gene2ID` int(11) NOT NULL,
  `Data` float NOT NULL,
  PRIMARY KEY (`FitnessRelationID`),
  KEY `GeneID` (`GeneID`),
  KEY `Gene2ID` (`Gene2ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `Gene`
--

CREATE TABLE IF NOT EXISTS `Gene` (
  `GeneID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `Longtitude` float NOT NULL,
  `Latitude` float NOT NULL,
  `Created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`GeneID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `Log`
--

CREATE TABLE IF NOT EXISTS `Log` (
  `LogID` int(11) NOT NULL AUTO_INCREMENT,
  `SessionID` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `Section` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `Message` text COLLATE utf8_bin NOT NULL,
  `LogentryType` varchar(255) COLLATE utf8_bin NOT NULL,
  `Created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Updated` timestamp NULL DEFAULT NULL,
  `Description` text COLLATE utf8_bin,
  PRIMARY KEY (`LogID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=9210 ;

-- --------------------------------------------------------

--
-- Table structure for table `Parent`
--

CREATE TABLE IF NOT EXISTS `Parent` (
  `ParentID` int(11) NOT NULL AUTO_INCREMENT,
  `PopulationID` int(11) NOT NULL,
  `ChromosomeID` int(11) NOT NULL,
  `Created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ParentID`),
  KEY `PopulationID` (`PopulationID`),
  KEY `ChromosomeID` (`ChromosomeID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=136 ;

-- --------------------------------------------------------

--
-- Table structure for table `Population`
--

CREATE TABLE IF NOT EXISTS `Population` (
  `PopulationID` int(11) NOT NULL,
  `ChromosomeID` int(11) NOT NULL,
  `Created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`PopulationID`,`ChromosomeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `temp`
--

CREATE TABLE IF NOT EXISTS `temp` (
  `tempID` int(11) NOT NULL AUTO_INCREMENT,
  `Data` longtext COLLATE utf8_bin,
  `Created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`tempID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;
