-- phpMyAdmin SQL Dump
-- version 4.0.10.18
-- https://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Jul 02, 2018 at 04:21 PM
-- Server version: 5.6.39-cll-lve
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `hs_online_data`
--

-- --------------------------------------------------------

--
-- Table structure for table `hs_games`
--

CREATE TABLE IF NOT EXISTS `hs_games` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `gamename` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `gdoc` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `gametime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `allow_comments` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gamename` (`gamename`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10663 ;

-- --------------------------------------------------------

--
-- Table structure for table `hs_login_attempts`
--

CREATE TABLE IF NOT EXISTS `hs_login_attempts` (
  `user_id` int(11) NOT NULL,
  `time` varchar(30) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hs_masterclock`
--

CREATE TABLE IF NOT EXISTS `hs_masterclock` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `gamename` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `tracktime` int(1) NOT NULL DEFAULT '0',
  `countOmPlacement` int(1) NOT NULL DEFAULT '0',
  `countInitRoll` int(1) NOT NULL DEFAULT '0',
  `gameclock` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `totaltime` int(12) NOT NULL,
  `omtimecount` int(1) NOT NULL DEFAULT '0',
  `ominitcount` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gamename` (`gamename`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7783 ;

-- --------------------------------------------------------

--
-- Table structure for table `hs_oms`
--

CREATE TABLE IF NOT EXISTS `hs_oms` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `om1` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `om2` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `om3` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `omx` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `omxx` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `game` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `omtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `revealom1` int(1) NOT NULL,
  `revealom2` int(1) NOT NULL,
  `revealom3` int(1) NOT NULL,
  `revealom4` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8110 ;

-- --------------------------------------------------------

--
-- Table structure for table `hs_omtime`
--

CREATE TABLE IF NOT EXISTS `hs_omtime` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `gamename` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `omopen` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `omclose` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `omtotal` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `omnumber` int(100) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hs_playergames`
--

CREATE TABLE IF NOT EXISTS `hs_playergames` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `gid` int(5) NOT NULL,
  `pid` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hs_rolls`
--

CREATE TABLE IF NOT EXISTS `hs_rolls` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `gamename` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ipaddress` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `skulls` int(6) NOT NULL,
  `shields` int(6) NOT NULL,
  `blanks` int(6) NOT NULL,
  `dskulls` int(6) NOT NULL,
  `dshields` int(6) NOT NULL,
  `dblanks` int(6) NOT NULL,
  `gametime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tracktime` int(1) NOT NULL DEFAULT '0' COMMENT 'Track game time if set to 1',
  `chessclock` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `startclock` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `totalclock` int(12) NOT NULL,
  `paused` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8333 ;

-- --------------------------------------------------------

--
-- Table structure for table `hs_rolls_itemized`
--

CREATE TABLE IF NOT EXISTS `hs_rolls_itemized` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `gamename` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `action` int(1) NOT NULL COMMENT '1 = attack, 2 = defend, 0= d20, 3=d20-Init',
  `skulls` int(6) NOT NULL,
  `shields` int(6) NOT NULL,
  `blanks` int(6) NOT NULL,
  `totals` int(6) NOT NULL,
  `d20` int(2) NOT NULL,
  `gametime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ipaddress` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Gamename` (`username`(15))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=693478 ;

-- --------------------------------------------------------

--
-- Table structure for table `hs_time_master`
--

CREATE TABLE IF NOT EXISTS `hs_time_master` (
  `gamename` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `activeplayer` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `omnumber` int(1) NOT NULL,
  UNIQUE KEY `gamename` (`gamename`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hs_users`
--

CREATE TABLE IF NOT EXISTS `hs_users` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(75) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `jointime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `verified` tinyint(4) NOT NULL,
  `ver_code` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `salt` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `ipaddress` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1141 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
