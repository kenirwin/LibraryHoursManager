-- phpMyAdmin SQL Dump
-- version 3.2.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 22, 2015 at 04:53 PM
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
  `except_id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `opentime` varchar(50) DEFAULT NULL,
  `closetime` varchar(50) DEFAULT NULL,
  `latenight` enum('Y','N') DEFAULT NULL,
  `closed` enum('Y','N') DEFAULT NULL,
  PRIMARY KEY (`except_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `presets`
--

CREATE TABLE IF NOT EXISTS `presets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `rank` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `settings_key` int(11) NOT NULL AUTO_INCREMENT,
  `preset_id` int(11) DEFAULT NULL,
  `day` enum('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') DEFAULT NULL,
  `opentime` varchar(50) DEFAULT NULL,
  `closetime` varchar(50) DEFAULT NULL,
  `latenight` enum('Y','N') NOT NULL DEFAULT 'N',
  `closed` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`settings_key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='hourse of operation - starting Aug 2015';

-- --------------------------------------------------------

--
-- Table structure for table `timeframes`
--

CREATE TABLE IF NOT EXISTS `timeframes` (
  `timeframe_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `first_date` date NOT NULL,
  `last_date` date NOT NULL,
  `apply_preset_id` int(11) NOT NULL,
  PRIMARY KEY (`timeframe_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
