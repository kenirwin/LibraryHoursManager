-- phpMyAdmin SQL Dump
-- version 3.2.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 06, 2015 at 03:16 PM
-- Server version: 5.6.26
-- PHP Version: 5.4.16

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `hours`
--

-- --------------------------------------------------------

--
-- Table structure for table `exceptions`
--

CREATE TABLE IF NOT EXISTS `exceptions` (
  `date` date DEFAULT NULL,
  `opentime` varchar(50) NOT NULL,
  `closetime` varchar(50) NOT NULL,
  `latenight` enum('Y','N') NOT NULL,
  `closed` enum('Y','N') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `presets`
--

CREATE TABLE IF NOT EXISTS `presets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `rank` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `preset_id` int(11) DEFAULT NULL,
  `day` enum('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') DEFAULT NULL,
  `opentime` varchar(50) DEFAULT NULL,
  `closetime` varchar(50) DEFAULT NULL,
  `latenight` enum('Y','N') NOT NULL DEFAULT 'N',
  `closed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='hourse of operation - starting Aug 2015';

-- --------------------------------------------------------

--
-- Table structure for table `timeframes`
--

CREATE TABLE IF NOT EXISTS `timeframes` (
  `name` varchar(50) DEFAULT NULL,
  `first_date` date NOT NULL,
  `last_date` date NOT NULL,
  `apply_preset_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
