-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 20, 2015 at 02:25 PM
-- Server version: 5.6.20
-- PHP Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `stylekar_blacklist`
--

-- --------------------------------------------------------

--
-- Table structure for table `access`
--

CREATE TABLE IF NOT EXISTS `access` (
`id` int(11) unsigned NOT NULL,
  `key` varchar(40) NOT NULL DEFAULT '',
  `controller` varchar(50) NOT NULL DEFAULT '',
  `date_created` datetime DEFAULT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `access`
--

INSERT INTO `access` (`id`, `key`, `controller`, `date_created`, `date_modified`) VALUES
(1, '6d9f729b765aae27f45e5ef9150fa073f8a61b94', 'key', '2014-03-03 04:04:07', '2014-03-03 19:30:57');

-- --------------------------------------------------------

--
-- Table structure for table `keys`
--

CREATE TABLE IF NOT EXISTS `keys` (
`id` int(11) NOT NULL,
  `key` varchar(40) NOT NULL,
  `level` int(2) NOT NULL,
  `ignore_limits` tinyint(1) NOT NULL DEFAULT '0',
  `is_private_key` tinyint(1) NOT NULL DEFAULT '0',
  `ip_addresses` text,
  `date_created` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `keys`
--

INSERT INTO `keys` (`id`, `key`, `level`, `ignore_limits`, `is_private_key`, `ip_addresses`, `date_created`) VALUES
(1, '6d9f729b765aae27f45e5ef9150fa073f8a61b94', 3, 0, 0, NULL, 1393594893);

-- --------------------------------------------------------

--
-- Table structure for table `limits`
--

CREATE TABLE IF NOT EXISTS `limits` (
`id` int(11) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `count` int(10) NOT NULL,
  `hour_started` int(11) NOT NULL,
  `api_key` varchar(40) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=460 ;

--
-- Dumping data for table `limits`
--

INSERT INTO `limits` (`id`, `uri`, `count`, `hour_started`, `api_key`) VALUES
(459, 'key', 1, 1413203803, '6d9f729b765aae27f45e5ef9150fa073f8a61b94');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
`id` int(11) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `method` varchar(6) NOT NULL,
  `params` text,
  `api_key` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `time` int(11) NOT NULL,
  `rtime` float DEFAULT NULL,
  `authorized` tinyint(1) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `uri`, `method`, `params`, `api_key`, `ip_address`, `time`, `rtime`, `authorized`) VALUES
(1, 'key/index/format/json', 'post', '{"format":"json"}', '6d9f729b765aae27f45e5ef9150fa073f8a61b94', '111.93.85.70', 1399616741, 0.059473, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE IF NOT EXISTS `tbl_admin` (
`iAdminID` int(11) NOT NULL,
  `eAdminType` enum('1','2') NOT NULL DEFAULT '1' COMMENT '1=Admin, 2=Restaurant',
  `iRestaurantID` int(11) NOT NULL,
  `vFirstName` varchar(50) NOT NULL,
  `vLastName` varchar(50) NOT NULL,
  `vEmail` varchar(255) NOT NULL,
  `vPassword` varchar(255) NOT NULL,
  `iAddedBy` int(11) NOT NULL,
  `iLastEditedBy` int(11) NOT NULL,
  `eStatus` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `dTlogouttime` datetime NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=102 ;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`iAdminID`, `eAdminType`, `iRestaurantID`, `vFirstName`, `vLastName`, `vEmail`, `vPassword`, `iAddedBy`, `iLastEditedBy`, `eStatus`, `dTlogouttime`) VALUES
(95, '1', 0, 'Poonam', '', 'poonam@openxcell.com', '21232f297a57a5a743894a0e4a801fc3', 0, 0, 'Active', '2014-11-24 05:22:23'),
(98, '1', 0, 'Girish', 'Solanki', 'girish@openxcell.info', '21232f297a57a5a743894a0e4a801fc3', 95, 0, 'Active', '0000-00-00 00:00:00'),
(99, '1', 0, 'Ruchi', 'Trivedi', 'ruchi@openxcelltechnolabs.com', '21232f297a57a5a743894a0e4a801fc3', 95, 0, 'Active', '0000-00-00 00:00:00'),
(100, '1', 0, 'Saad', 'Alsulaim', 'saadalsulaim@votegram.com', '21232f297a57a5a743894a0e4a801fc3', 0, 0, 'Active', '2014-12-15 00:00:00'),
(101, '2', 7, 'Chintan Restaurant', '', 'chintan@techtimetea.com', '21232f297a57a5a743894a0e4a801fc3', 0, 0, 'Active', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_category`
--

CREATE TABLE IF NOT EXISTS `tbl_category` (
`iCategoryID` int(11) NOT NULL,
  `vCategoryName` varchar(255) NOT NULL,
  `eStatus` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `tCreatedAt` datetime NOT NULL,
  `tModifiedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Store Category data' AUTO_INCREMENT=23 ;

--
-- Dumping data for table `tbl_category`
--

INSERT INTO `tbl_category` (`iCategoryID`, `vCategoryName`, `eStatus`, `tCreatedAt`, `tModifiedAt`) VALUES
(1, 'African', 'Active', '2014-12-26 12:13:08', '2014-12-30 07:15:33'),
(2, 'Japanese', 'Active', '2014-12-26 13:00:07', '2014-12-30 07:15:38'),
(3, 'American', 'Active', '2014-12-26 13:00:07', '2014-12-30 07:15:38'),
(4, 'Vietnamese', 'Active', '2014-12-26 13:00:07', '2014-12-30 07:15:38'),
(5, 'Continental', 'Active', '2014-12-26 13:00:07', '2014-12-30 07:15:38'),
(6, 'Chinese', 'Active', '2014-12-26 13:00:07', '2014-12-30 07:15:38'),
(7, 'French', 'Active', '2014-12-26 13:00:07', '2014-12-30 07:15:38'),
(8, 'Greek', 'Active', '2014-12-26 13:00:07', '2014-12-30 07:15:38'),
(9, 'North Indian', 'Active', '2014-12-26 13:00:07', '2014-12-30 07:15:38'),
(10, 'Irish', 'Active', '2014-12-26 13:00:07', '2014-12-30 07:15:38'),
(11, 'Italian', 'Active', '2014-12-26 13:00:07', '2014-12-30 07:15:38'),
(12, 'Mexican', 'Active', '2014-12-26 13:00:07', '2014-12-30 07:15:38'),
(13, 'Portuguese', 'Active', '2014-12-26 13:00:07', '2014-12-30 07:15:38'),
(14, 'Spanish', 'Active', '2014-12-26 13:00:07', '2014-12-30 07:15:38'),
(15, 'Thai', 'Active', '2014-12-26 13:00:07', '2014-12-30 07:15:38'),
(16, 'Turkish', 'Active', '2014-12-26 13:00:07', '2014-12-30 07:15:38'),
(17, 'Mediterranean', 'Active', '2014-12-26 13:00:07', '2014-12-30 07:15:38'),
(18, 'Cuban', 'Active', '2014-12-26 13:00:07', '2014-12-30 07:15:38'),
(19, 'Indonesian', 'Active', '2014-12-26 13:00:07', '2014-12-30 07:15:38'),
(20, 'Pakistani', 'Active', '2014-12-26 13:00:07', '2014-12-30 07:15:38'),
(21, 'South Indian', 'Active', '2014-12-26 13:00:07', '2014-12-30 07:15:38'),
(22, 'Street food', 'Active', '2014-12-26 13:00:07', '2014-12-30 07:15:38');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_comment`
--

CREATE TABLE IF NOT EXISTS `tbl_comment` (
`iCommentID` int(11) NOT NULL,
  `iUserID` int(11) NOT NULL,
  `iDealID` int(11) NOT NULL,
  `vCommentText` varchar(255) NOT NULL,
  `eStatus` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `tCreatedAt` datetime NOT NULL,
  `tModifiedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_cuisine`
--

CREATE TABLE IF NOT EXISTS `tbl_cuisine` (
`iCuisineID` int(11) NOT NULL,
  `vCuisineName` varchar(255) NOT NULL,
  `eStatus` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `tCreatedAt` datetime NOT NULL,
  `tModifiedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Store cuisine data' AUTO_INCREMENT=25 ;

--
-- Dumping data for table `tbl_cuisine`
--

INSERT INTO `tbl_cuisine` (`iCuisineID`, `vCuisineName`, `eStatus`, `tCreatedAt`, `tModifiedAt`) VALUES
(1, 'African', 'Active', '2015-01-01 11:33:42', '2015-01-01 11:16:17'),
(2, 'Japanese', 'Active', '2015-01-01 11:33:42', '2015-01-01 10:43:49'),
(3, 'American', 'Active', '2015-01-01 11:33:42', '2015-01-01 10:43:49'),
(4, 'Vietnamese', 'Active', '2015-01-01 11:33:42', '2015-01-01 10:43:49'),
(5, 'Continental', 'Active', '2015-01-01 11:33:42', '2015-01-01 10:43:49'),
(6, 'Chinese', 'Active', '2015-01-01 11:33:42', '2015-01-01 10:43:49'),
(9, 'French', 'Active', '2015-01-01 11:33:42', '2015-01-01 10:43:49'),
(10, 'Greek', 'Active', '2015-01-01 11:33:42', '2015-01-01 10:43:49'),
(11, 'North Indian', 'Active', '2015-01-01 11:33:42', '2015-01-01 10:43:49'),
(12, 'Irish', 'Active', '2015-01-01 11:33:42', '2015-01-01 10:43:49'),
(13, 'Italian', 'Active', '2015-01-01 11:33:42', '2015-01-01 10:43:49'),
(14, 'Mexican', 'Active', '2015-01-01 11:33:42', '2015-01-01 10:43:49'),
(15, 'Portuguese', 'Active', '2015-01-01 11:33:42', '2015-01-01 10:43:49'),
(16, 'Spanish', 'Active', '2015-01-01 11:33:42', '2015-01-01 10:43:49'),
(17, 'Thai', 'Active', '2015-01-01 11:33:42', '2015-01-01 10:43:49'),
(18, 'Turkish', 'Active', '2015-01-01 11:33:42', '2015-01-01 10:43:49'),
(19, 'Mediterranean', 'Active', '2015-01-01 11:33:42', '2015-01-01 10:43:49'),
(20, 'Cuban', 'Active', '2015-01-01 11:33:42', '2015-01-01 10:43:49'),
(21, 'Indonesian', 'Active', '2015-01-01 11:33:42', '2015-01-01 10:43:49'),
(22, 'Pakistani', 'Active', '2015-01-01 11:33:42', '2015-01-01 10:43:49'),
(23, 'South Indian', 'Active', '2015-01-01 11:33:42', '2015-01-01 10:43:49'),
(24, 'street food', 'Active', '2015-01-01 11:33:42', '2015-01-01 10:43:49');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_deals`
--

CREATE TABLE IF NOT EXISTS `tbl_deals` (
`iDealID` int(11) NOT NULL,
  `iRestaurantID` int(11) NOT NULL,
  `vOfferText` varchar(255) NOT NULL,
  `tTermsOfUse` text NOT NULL,
  `dtStartDate` datetime NOT NULL,
  `dtExpiryDate` datetime NOT NULL,
  `eStatus` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `tCreatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tModifiedAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `tbl_deals`
--

INSERT INTO `tbl_deals` (`iDealID`, `iRestaurantID`, `vOfferText`, `tTermsOfUse`, `dtStartDate`, `dtExpiryDate`, `eStatus`, `tCreatedAt`, `tModifiedAt`) VALUES
(6, 2, 'Buy 1 Get 10 Free', 'Enjoy', '2015-01-23 00:00:00', '2015-01-29 00:00:00', 'Active', '2015-01-20 06:13:44', '2015-01-20 10:52:49'),
(7, 7, 'Buy 1 Get 10 Free', 'Enjoy', '2015-01-22 00:00:00', '2015-01-23 00:00:00', 'Inactive', '2015-01-20 06:52:11', '0000-00-00 00:00:00'),
(8, 7, 'Buy 3 Get 3 Free', 'Enjoy', '2015-01-24 00:00:00', '2015-01-31 00:00:00', 'Inactive', '2015-01-20 08:44:24', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_deals_like`
--

CREATE TABLE IF NOT EXISTS `tbl_deals_like` (
`iDealLikeID` int(11) NOT NULL,
  `iRestaurantID` int(11) NOT NULL,
  `iDealID` int(11) NOT NULL,
  `iUserID` int(11) NOT NULL,
  `tCreatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tModifiedAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `tbl_deals_like`
--

INSERT INTO `tbl_deals_like` (`iDealLikeID`, `iRestaurantID`, `iDealID`, `iUserID`, `tCreatedAt`, `tModifiedAt`) VALUES
(1, 2, 6, 11, '2015-01-20 11:02:36', '0000-00-00 00:00:00'),
(2, 7, 7, 5, '2015-01-20 11:30:29', '0000-00-00 00:00:00'),
(3, 7, 7, 6, '2015-01-20 11:30:29', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_facility`
--

CREATE TABLE IF NOT EXISTS `tbl_facility` (
`iFacilityID` int(11) NOT NULL,
  `vFacilityName` varchar(255) NOT NULL,
  `eStatus` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `tCreatedAt` datetime NOT NULL,
  `tModifiedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Store Facility data' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `tbl_facility`
--

INSERT INTO `tbl_facility` (`iFacilityID`, `vFacilityName`, `eStatus`, `tCreatedAt`, `tModifiedAt`) VALUES
(1, 'Jazz', 'Inactive', '2015-01-01 14:39:50', '2015-01-19 12:39:33'),
(2, 'AC', 'Active', '2015-01-01 14:39:50', '2015-01-01 13:39:50'),
(3, 'NON-AC', 'Active', '2015-01-01 14:39:50', '2015-01-01 13:39:50');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_hmac`
--

CREATE TABLE IF NOT EXISTS `tbl_hmac` (
`iHmacID` int(11) NOT NULL,
  `iUserID` int(11) NOT NULL,
  `vHmac` varchar(255) NOT NULL,
  `key_id` int(11) NOT NULL,
  `dtCreated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_music`
--

CREATE TABLE IF NOT EXISTS `tbl_music` (
`iMusicID` int(11) NOT NULL,
  `vMusicName` varchar(255) NOT NULL,
  `eStatus` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `tCreatedAt` datetime NOT NULL,
  `tModifiedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Store Music Category data' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `tbl_music`
--

INSERT INTO `tbl_music` (`iMusicID`, `vMusicName`, `eStatus`, `tCreatedAt`, `tModifiedAt`) VALUES
(1, 'Jazz12', 'Active', '2015-01-01 14:40:13', '2015-01-01 13:40:59'),
(2, 'Rock', 'Active', '2015-01-01 14:40:13', '2015-01-01 13:40:59'),
(3, 'Sufi', 'Active', '2015-01-01 14:40:13', '2015-01-01 13:40:59');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_pagecontent`
--

CREATE TABLE IF NOT EXISTS `tbl_pagecontent` (
`iPageID` int(11) NOT NULL,
  `vPageTitle` varchar(255) NOT NULL,
  `tContent` text NOT NULL,
  `tCreatedAt` datetime NOT NULL,
  `tModifiedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `tbl_pagecontent`
--

INSERT INTO `tbl_pagecontent` (`iPageID`, `vPageTitle`, `tContent`, `tCreatedAt`, `tModifiedAt`) VALUES
(1, 'Terms & Use', '<p><strong>Terms &amp; Use Content Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry&#39;s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</strong></p>\r\n', '2014-12-04 02:35:38', '2015-01-19 11:03:28'),
(2, 'Privacy Policy 2 ', '<p><strong>Privacy Policy</strong></p>\r\n', '2014-12-16 00:00:00', '2015-01-19 11:04:13');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_restaurant`
--

CREATE TABLE IF NOT EXISTS `tbl_restaurant` (
`iRestaurantID` int(11) NOT NULL,
  `vRestaurantName` varchar(255) NOT NULL,
  `vEmail` varchar(255) NOT NULL,
  `vEmailSecondary` varchar(255) NOT NULL,
  `vPassword` varchar(255) NOT NULL,
  `tAddress` text NOT NULL,
  `tSpecialty` text NOT NULL,
  `vCityName` varchar(255) NOT NULL,
  `vStateName` varchar(255) NOT NULL,
  `vCountryName` varchar(255) NOT NULL,
  `vRestaurantLogo` varchar(255) NOT NULL,
  `vContactNo` varchar(255) NOT NULL,
  `tDescription` text NOT NULL,
  `vDaysOpen` varchar(20) NOT NULL,
  `iMinTime` int(11) NOT NULL,
  `iMaxTime` int(11) NOT NULL,
  `iMinPrice` int(11) NOT NULL,
  `iMaxPrice` int(11) NOT NULL,
  `vFbLink` varchar(255) NOT NULL,
  `vInstagramLink` varchar(255) NOT NULL,
  `vQRCode` varchar(255) NOT NULL,
  `eStatus` enum('Active','Inactive') NOT NULL,
  `tCreatedAt` datetime NOT NULL,
  `tModifiedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Only Admin can add Restaurant /Venues ' AUTO_INCREMENT=8 ;

--
-- Dumping data for table `tbl_restaurant`
--

INSERT INTO `tbl_restaurant` (`iRestaurantID`, `vRestaurantName`, `vEmail`, `vEmailSecondary`, `vPassword`, `tAddress`, `tSpecialty`, `vCityName`, `vStateName`, `vCountryName`, `vRestaurantLogo`, `vContactNo`, `tDescription`, `vDaysOpen`, `iMinTime`, `iMaxTime`, `iMinPrice`, `iMaxPrice`, `vFbLink`, `vInstagramLink`, `vQRCode`, `eStatus`, `tCreatedAt`, `tModifiedAt`) VALUES
(1, 'Restaurant Name', 'girish@openxcell.info', '', '', 'asd asd', '', 'Ahmedabad2', 'Country', 'Country', '02ca6a039d57d9b547d34dc743b5eba3.jpg', '9510335254', 'asd asdasd', '', 0, 0, 0, 0, 'Facebook Link', 'Instgram Link', '', 'Active', '2014-12-29 14:53:05', '2014-12-30 10:41:09'),
(2, 'Surbhi', 'girish123@gmail.com', '', '', 'test address', '', 'city name', 'street name', 'Country name', '57669af5f4d2aa3ff68db84f4ce10d3b.jpg', '45454', 'test', '', 0, 0, 0, 0, 'test', 'test', '', 'Active', '2014-12-30 11:54:21', '2014-12-30 10:54:50'),
(3, '3 Start', 'start3@gmail.com', '', '21232f297a57a5a743894a0e4a801fc3', '3 Star , Ahmedabad', '', 'Ahmedabad', 'Gujarat', 'India', '056513c866e9e715e6b6ce38dc4d9833.jpg', '9999999', 'Nice Res', '', 0, 0, 0, 0, '', '', '', 'Active', '2015-01-10 13:46:42', '2015-01-10 12:46:43'),
(7, 'Chintan Restaurant', 'chintan@techtimetea.com', '', '21232f297a57a5a743894a0e4a801fc3', 'Address', 'very Good Quality', 'Gandhinagar', 'Gujarat', 'India', '052557a56e17bced1df2a13d595ada60.gif', '9876543210,7894561230,4561237890,1234567890', 'Description', '1,2,4,5,6,7', 6, 22, 500, 3000, '', '', '', 'Active', '2015-01-19 15:14:22', '2015-01-19 14:14:22');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_restaurant_category`
--

CREATE TABLE IF NOT EXISTS `tbl_restaurant_category` (
`iRestaurantCategoryID` int(11) NOT NULL,
  `iRestaurantID` int(11) NOT NULL,
  `iCategoryID` int(11) NOT NULL,
  `tCreatedAt` datetime NOT NULL,
  `tModifiedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf32 COMMENT='Store data which  restaurant belong to category' AUTO_INCREMENT=78 ;

--
-- Dumping data for table `tbl_restaurant_category`
--

INSERT INTO `tbl_restaurant_category` (`iRestaurantCategoryID`, `iRestaurantID`, `iCategoryID`, `tCreatedAt`, `tModifiedAt`) VALUES
(26, 2, 4, '2014-12-30 11:54:50', '2014-12-30 10:54:50'),
(27, 2, 6, '2014-12-30 11:54:50', '2014-12-30 10:54:50'),
(28, 2, 8, '2014-12-30 11:54:50', '2014-12-30 10:54:50'),
(38, 1, 2, '2015-01-10 13:45:03', '2015-01-10 12:45:03'),
(39, 1, 3, '2015-01-10 13:45:03', '2015-01-10 12:45:03'),
(40, 1, 6, '2015-01-10 13:45:03', '2015-01-10 12:45:03'),
(41, 3, 3, '2015-01-10 13:46:42', '2015-01-10 12:46:42'),
(42, 3, 9, '2015-01-10 13:46:42', '2015-01-10 12:46:42'),
(43, 3, 20, '2015-01-10 13:46:42', '2015-01-10 12:46:42'),
(76, 7, 6, '2015-01-20 13:35:54', '2015-01-20 12:35:54'),
(77, 7, 21, '2015-01-20 13:35:54', '2015-01-20 12:35:54');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_restaurant_cuisine`
--

CREATE TABLE IF NOT EXISTS `tbl_restaurant_cuisine` (
`iRestaurantCuisineID` int(11) NOT NULL,
  `iRestaurantID` int(11) NOT NULL,
  `iCuisineID` int(11) NOT NULL,
  `tCreatedAt` datetime NOT NULL,
  `tModifiedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Store data which  restaurant belong to Cuisine' AUTO_INCREMENT=33 ;

--
-- Dumping data for table `tbl_restaurant_cuisine`
--

INSERT INTO `tbl_restaurant_cuisine` (`iRestaurantCuisineID`, `iRestaurantID`, `iCuisineID`, `tCreatedAt`, `tModifiedAt`) VALUES
(8, 1, 4, '2015-01-10 13:45:03', '2015-01-10 12:45:03'),
(9, 1, 9, '2015-01-10 13:45:03', '2015-01-10 12:45:03'),
(10, 3, 4, '2015-01-10 13:46:42', '2015-01-10 12:46:42'),
(11, 3, 18, '2015-01-10 13:46:42', '2015-01-10 12:46:42'),
(32, 7, 6, '2015-01-20 13:35:54', '2015-01-20 12:35:54');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_restaurant_facility`
--

CREATE TABLE IF NOT EXISTS `tbl_restaurant_facility` (
`iRestaurantFacilityID` int(11) NOT NULL,
  `iRestaurantID` int(11) NOT NULL,
  `iFacilityID` int(11) NOT NULL,
  `tCreatedAt` datetime NOT NULL,
  `tModifiedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Store data which  restaurant provide facility' AUTO_INCREMENT=25 ;

--
-- Dumping data for table `tbl_restaurant_facility`
--

INSERT INTO `tbl_restaurant_facility` (`iRestaurantFacilityID`, `iRestaurantID`, `iFacilityID`, `tCreatedAt`, `tModifiedAt`) VALUES
(1, 1, 2, '2015-01-10 13:45:03', '2015-01-10 12:45:03'),
(2, 1, 3, '2015-01-10 13:45:03', '2015-01-10 12:45:03'),
(3, 3, 1, '2015-01-10 13:46:42', '2015-01-10 12:46:42'),
(4, 3, 2, '2015-01-10 13:46:42', '2015-01-10 12:46:42'),
(23, 7, 2, '2015-01-20 13:35:54', '2015-01-20 12:35:54'),
(24, 7, 3, '2015-01-20 13:35:54', '2015-01-20 12:35:54');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_restaurant_image`
--

CREATE TABLE IF NOT EXISTS `tbl_restaurant_image` (
`iPictureID` int(11) NOT NULL,
  `iRestaurantID` int(11) NOT NULL,
  `vPictureName` varchar(255) NOT NULL,
  `eStatus` enum('Active','Inactive') NOT NULL,
  `tCreatedAt` datetime NOT NULL,
  `tModifiedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Store Restaurant Images' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `tbl_restaurant_image`
--

INSERT INTO `tbl_restaurant_image` (`iPictureID`, `iRestaurantID`, `vPictureName`, `eStatus`, `tCreatedAt`, `tModifiedAt`) VALUES
(3, 1, '57669af5f4d2aa3ff68db84f4ce10d3b.jpg', 'Active', '2014-12-09 00:00:00', '2015-01-01 05:48:58');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_restaurant_menu_image`
--

CREATE TABLE IF NOT EXISTS `tbl_restaurant_menu_image` (
`iMenuPictureID` int(11) NOT NULL,
  `iRestaurantID` int(11) NOT NULL,
  `vPictureName` varchar(255) NOT NULL,
  `eStatus` enum('Active','Inactive') NOT NULL,
  `tCreatedAt` datetime NOT NULL,
  `tModifiedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Store Restaurant Menu Images' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `tbl_restaurant_menu_image`
--

INSERT INTO `tbl_restaurant_menu_image` (`iMenuPictureID`, `iRestaurantID`, `vPictureName`, `eStatus`, `tCreatedAt`, `tModifiedAt`) VALUES
(2, 2, '57669af5f4d2aa3ff68db84f4ce10d3b.jpg', 'Active', '2014-12-11 05:25:14', '2014-12-30 13:31:31');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_restaurant_music`
--

CREATE TABLE IF NOT EXISTS `tbl_restaurant_music` (
`iRestaurantCategoryID` int(11) NOT NULL,
  `iRestaurantID` int(11) NOT NULL,
  `iMusicID` int(11) NOT NULL,
  `tCreatedAt` datetime NOT NULL,
  `tModifiedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf32 COMMENT='Store data which  restaurant belong to category' AUTO_INCREMENT=65 ;

--
-- Dumping data for table `tbl_restaurant_music`
--

INSERT INTO `tbl_restaurant_music` (`iRestaurantCategoryID`, `iRestaurantID`, `iMusicID`, `tCreatedAt`, `tModifiedAt`) VALUES
(63, 7, 2, '2015-01-20 13:35:54', '2015-01-20 12:35:54'),
(64, 7, 3, '2015-01-20 13:35:54', '2015-01-20 12:35:54');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_setting`
--

CREATE TABLE IF NOT EXISTS `tbl_setting` (
`iSettingID` int(11) NOT NULL,
  `vContactmail` varchar(255) NOT NULL,
  `vCompanymail` varchar(255) NOT NULL,
  `tCreatedAt` datetime NOT NULL,
  `tModifiedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `eStatus` enum('Active','Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tbl_setting`
--

INSERT INTO `tbl_setting` (`iSettingID`, `vContactmail`, `vCompanymail`, `tCreatedAt`, `tModifiedAt`, `eStatus`) VALUES
(1, 'test@gmail.com', 'test@gmail.com', '2014-12-15 06:20:24', '2014-12-15 06:20:24', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE IF NOT EXISTS `tbl_user` (
`iUserID` int(11) NOT NULL,
  `vFullName` varchar(255) NOT NULL,
  `vEmail` varchar(255) NOT NULL,
  `vUserName` varchar(255) NOT NULL,
  `vMobileNo` varchar(255) NOT NULL,
  `vPassword` varchar(255) NOT NULL,
  `vProfilePicture` varchar(255) NOT NULL,
  `eGender` enum('Male','Female','Notdisclose') NOT NULL DEFAULT 'Notdisclose',
  `ePlatform` varchar(255) NOT NULL,
  `vDeviceToken` varchar(255) NOT NULL,
  `eSubscriptionType` enum('Paid','Free') NOT NULL,
  `eStatus` enum('Active','Inactive') NOT NULL,
  `tCreatedAt` datetime NOT NULL,
  `tModifiedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`iUserID`, `vFullName`, `vEmail`, `vUserName`, `vMobileNo`, `vPassword`, `vProfilePicture`, `eGender`, `ePlatform`, `vDeviceToken`, `eSubscriptionType`, `eStatus`, `tCreatedAt`, `tModifiedAt`) VALUES
(1, 'Ruchi', 'ruchi@openxcelltechnolabs.com', 'ruchi', '4544545', '21232f297a57a5a743894a0e4a801fc3', '', 'Notdisclose', '', '', 'Free', 'Active', '2014-12-15 11:49:37', '2015-01-10 11:33:07'),
(3, 'Andreea85', 'andreea@votegram.com', 'ande', '7878', '6c48441b0d58474e829857663a937488', '', 'Notdisclose', '', '', 'Free', 'Active', '2014-12-16 05:24:24', '2015-01-10 11:33:11'),
(4, 'John', 'john@asdasd.in', 'jonh', '98989', '9fac6799b5827bb87ec6b080792ea19d', '', 'Notdisclose', '', '', 'Free', 'Active', '2014-12-16 05:27:01', '2015-01-10 11:33:16'),
(5, 'Examplenove', 'examplenove@asd.in', 'exanplenove', '954595', '1a79a4d60de6718e8e5b326e338ae533', '', 'Notdisclose', '', '', 'Free', 'Active', '2014-12-16 05:32:16', '2015-01-10 11:33:19'),
(6, 'Susan', 'susan@asd.inasdasd', 'sudan', '9636584', 'ac575e3eecf0fa410518c2d3a2e7209f', '', 'Notdisclose', '', '', 'Free', 'Active', '2014-12-16 05:33:09', '2015-01-10 11:33:22'),
(7, 'Paul', 'paul@asd.in', 'paul', '78887878', '508aa207b9cb97c14bac7b2474391660', '', 'Notdisclose', '', '', 'Free', 'Active', '2014-12-16 05:33:53', '2015-01-10 11:33:25'),
(8, 'Jonny', 'jonny@gmail.com', 'jonny', '98536254', '17f1df9f24dcdbbad02ae0f620e4ca53', '', 'Notdisclose', '', '', 'Paid', 'Active', '2014-12-19 13:08:40', '2015-01-10 11:33:57'),
(9, 'aa', 'aaa@aaa.aaa', 'aaa', '895675', '0cc175b9c0f1b6a831c399e269772661', '', 'Notdisclose', 'IOS', '', 'Free', 'Active', '2014-12-26 07:04:08', '2015-01-10 11:33:31'),
(11, 'Ketan', 'k1@mail.com', 'solankigirish25', '9510335254', '0cc175b9c0f1b6a831c399e269772661', '', 'Notdisclose', 'IOS', '', 'Paid', 'Active', '2014-12-26 07:36:33', '2015-01-10 11:34:19'),
(12, 'Girish Solanki', 'girish@openxcell.info', 'solankigirish525', '9510334254', 'dabe0d16e465745eb3108c9598d07860', '5d98e73b34d076bbbece8dd949448e23.jpg', 'Notdisclose', '', '', 'Free', 'Active', '2014-12-29 07:32:10', '2015-01-10 11:33:35'),
(13, 'test girish', 'girish123456@gmail.com', 'girish123456', 'girish123456', 'cbb0979418415e469dd210de5a8c260b', '', 'Notdisclose', '', '', 'Free', 'Active', '2015-01-06 12:06:07', '2015-01-10 11:33:38'),
(14, 'test girish', 'girish12345@gmail.com', 'girish12345', 'girish12345', 'cbb0979418415e469dd210de5a8c260b', '72cd174bb1f8f1cc01827e92b52d6c7d.jpg', 'Male', '', '', 'Free', 'Active', '2015-01-06 12:11:26', '2015-01-10 11:33:40');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_cuisine`
--

CREATE TABLE IF NOT EXISTS `tbl_user_cuisine` (
`iUserCuisineID` int(11) NOT NULL,
  `iUserID` int(11) NOT NULL,
  `iCuisineID` int(11) NOT NULL,
  `tCreatedAt` datetime NOT NULL,
  `tModifiedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf32 COMMENT='Store data which  user cuisine choice' AUTO_INCREMENT=14 ;

--
-- Dumping data for table `tbl_user_cuisine`
--

INSERT INTO `tbl_user_cuisine` (`iUserCuisineID`, `iUserID`, `iCuisineID`, `tCreatedAt`, `tModifiedAt`) VALUES
(12, 14, 3, '2015-01-06 12:30:33', '2015-01-06 11:30:33'),
(13, 14, 5, '2015-01-06 12:30:33', '2015-01-06 11:30:33');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_interest`
--

CREATE TABLE IF NOT EXISTS `tbl_user_interest` (
`iUserInterestID` int(11) NOT NULL,
  `iUserID` int(11) NOT NULL,
  `iInterestID` int(11) NOT NULL,
  `tCreatedAt` datetime NOT NULL,
  `tModifiedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf32 COMMENT='Store data which  user Interest from tbl_facility' AUTO_INCREMENT=13 ;

--
-- Dumping data for table `tbl_user_interest`
--

INSERT INTO `tbl_user_interest` (`iUserInterestID`, `iUserID`, `iInterestID`, `tCreatedAt`, `tModifiedAt`) VALUES
(11, 14, 1, '2015-01-06 12:30:33', '2015-01-06 11:30:33'),
(12, 14, 2, '2015-01-06 12:30:33', '2015-01-06 11:30:33');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_music`
--

CREATE TABLE IF NOT EXISTS `tbl_user_music` (
`iUserMusicID` int(11) NOT NULL,
  `iUserID` int(11) NOT NULL,
  `iMusicID` int(11) NOT NULL,
  `tCreatedAt` datetime NOT NULL,
  `tModifiedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf32 COMMENT='Store data which  user like Music' AUTO_INCREMENT=13 ;

--
-- Dumping data for table `tbl_user_music`
--

INSERT INTO `tbl_user_music` (`iUserMusicID`, `iUserID`, `iMusicID`, `tCreatedAt`, `tModifiedAt`) VALUES
(11, 14, 2, '2015-01-06 12:30:33', '2015-01-06 11:30:33'),
(12, 14, 3, '2015-01-06 12:30:33', '2015-01-06 11:30:33');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_restaurant_favorite`
--

CREATE TABLE IF NOT EXISTS `tbl_user_restaurant_favorite` (
`iFavoriteID` int(11) NOT NULL,
  `iUserID` int(11) NOT NULL,
  `iRestaurantID` int(11) NOT NULL,
  `tCreatedAt` datetime NOT NULL,
  `tModifiedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Store data which  user set as Favorite Restaurant' AUTO_INCREMENT=5 ;

--
-- Dumping data for table `tbl_user_restaurant_favorite`
--

INSERT INTO `tbl_user_restaurant_favorite` (`iFavoriteID`, `iUserID`, `iRestaurantID`, `tCreatedAt`, `tModifiedAt`) VALUES
(2, 13, 2, '2015-01-01 10:00:00', '2015-01-06 12:25:02'),
(3, 11, 1, '2015-01-03 07:04:10', '2015-01-06 12:25:37'),
(4, 13, 2, '2014-12-16 03:10:10', '2015-01-06 12:25:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access`
--
ALTER TABLE `access`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `keys`
--
ALTER TABLE `keys`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `limits`
--
ALTER TABLE `limits`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
 ADD PRIMARY KEY (`iAdminID`);

--
-- Indexes for table `tbl_category`
--
ALTER TABLE `tbl_category`
 ADD PRIMARY KEY (`iCategoryID`);

--
-- Indexes for table `tbl_comment`
--
ALTER TABLE `tbl_comment`
 ADD PRIMARY KEY (`iCommentID`), ADD KEY `iUserID` (`iUserID`), ADD KEY `iPostID` (`iDealID`);

--
-- Indexes for table `tbl_cuisine`
--
ALTER TABLE `tbl_cuisine`
 ADD PRIMARY KEY (`iCuisineID`);

--
-- Indexes for table `tbl_deals`
--
ALTER TABLE `tbl_deals`
 ADD PRIMARY KEY (`iDealID`), ADD KEY `fk_iRestaurantID` (`iRestaurantID`);

--
-- Indexes for table `tbl_deals_like`
--
ALTER TABLE `tbl_deals_like`
 ADD PRIMARY KEY (`iDealLikeID`), ADD KEY `index_deal_like` (`iRestaurantID`,`iDealID`,`iUserID`), ADD KEY `fk_iDealID` (`iDealID`), ADD KEY `fk_iUserID` (`iUserID`);

--
-- Indexes for table `tbl_facility`
--
ALTER TABLE `tbl_facility`
 ADD PRIMARY KEY (`iFacilityID`);

--
-- Indexes for table `tbl_hmac`
--
ALTER TABLE `tbl_hmac`
 ADD PRIMARY KEY (`iHmacID`), ADD KEY `iUserID` (`iUserID`), ADD KEY `key_id` (`key_id`);

--
-- Indexes for table `tbl_music`
--
ALTER TABLE `tbl_music`
 ADD PRIMARY KEY (`iMusicID`);

--
-- Indexes for table `tbl_pagecontent`
--
ALTER TABLE `tbl_pagecontent`
 ADD PRIMARY KEY (`iPageID`);

--
-- Indexes for table `tbl_restaurant`
--
ALTER TABLE `tbl_restaurant`
 ADD PRIMARY KEY (`iRestaurantID`);

--
-- Indexes for table `tbl_restaurant_category`
--
ALTER TABLE `tbl_restaurant_category`
 ADD PRIMARY KEY (`iRestaurantCategoryID`), ADD KEY `iRestaurantID` (`iRestaurantID`), ADD KEY `iCategoryID` (`iCategoryID`);

--
-- Indexes for table `tbl_restaurant_cuisine`
--
ALTER TABLE `tbl_restaurant_cuisine`
 ADD PRIMARY KEY (`iRestaurantCuisineID`), ADD KEY `iRestaurantID` (`iRestaurantID`), ADD KEY `iCategoryID` (`iCuisineID`);

--
-- Indexes for table `tbl_restaurant_facility`
--
ALTER TABLE `tbl_restaurant_facility`
 ADD PRIMARY KEY (`iRestaurantFacilityID`), ADD KEY `iRestaurantID` (`iRestaurantID`), ADD KEY `iCategoryID` (`iFacilityID`);

--
-- Indexes for table `tbl_restaurant_image`
--
ALTER TABLE `tbl_restaurant_image`
 ADD PRIMARY KEY (`iPictureID`), ADD KEY `iRestaurantID` (`iRestaurantID`);

--
-- Indexes for table `tbl_restaurant_menu_image`
--
ALTER TABLE `tbl_restaurant_menu_image`
 ADD PRIMARY KEY (`iMenuPictureID`), ADD KEY `iRestaurantID` (`iRestaurantID`);

--
-- Indexes for table `tbl_restaurant_music`
--
ALTER TABLE `tbl_restaurant_music`
 ADD PRIMARY KEY (`iRestaurantCategoryID`), ADD KEY `iRestaurantID` (`iRestaurantID`), ADD KEY `iCategoryID` (`iMusicID`);

--
-- Indexes for table `tbl_setting`
--
ALTER TABLE `tbl_setting`
 ADD PRIMARY KEY (`iSettingID`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
 ADD PRIMARY KEY (`iUserID`);

--
-- Indexes for table `tbl_user_cuisine`
--
ALTER TABLE `tbl_user_cuisine`
 ADD PRIMARY KEY (`iUserCuisineID`), ADD KEY `iRestaurantID` (`iUserID`), ADD KEY `iCategoryID` (`iCuisineID`);

--
-- Indexes for table `tbl_user_interest`
--
ALTER TABLE `tbl_user_interest`
 ADD PRIMARY KEY (`iUserInterestID`), ADD KEY `iRestaurantID` (`iUserID`), ADD KEY `iCategoryID` (`iInterestID`);

--
-- Indexes for table `tbl_user_music`
--
ALTER TABLE `tbl_user_music`
 ADD PRIMARY KEY (`iUserMusicID`), ADD KEY `iRestaurantID` (`iUserID`), ADD KEY `iCategoryID` (`iMusicID`);

--
-- Indexes for table `tbl_user_restaurant_favorite`
--
ALTER TABLE `tbl_user_restaurant_favorite`
 ADD PRIMARY KEY (`iFavoriteID`), ADD KEY `iRestaurantID` (`iUserID`), ADD KEY `iCategoryID` (`iRestaurantID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access`
--
ALTER TABLE `access`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `keys`
--
ALTER TABLE `keys`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `limits`
--
ALTER TABLE `limits`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=460;
--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
MODIFY `iAdminID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=102;
--
-- AUTO_INCREMENT for table `tbl_category`
--
ALTER TABLE `tbl_category`
MODIFY `iCategoryID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `tbl_comment`
--
ALTER TABLE `tbl_comment`
MODIFY `iCommentID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tbl_cuisine`
--
ALTER TABLE `tbl_cuisine`
MODIFY `iCuisineID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT for table `tbl_deals`
--
ALTER TABLE `tbl_deals`
MODIFY `iDealID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `tbl_deals_like`
--
ALTER TABLE `tbl_deals_like`
MODIFY `iDealLikeID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `tbl_facility`
--
ALTER TABLE `tbl_facility`
MODIFY `iFacilityID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `tbl_hmac`
--
ALTER TABLE `tbl_hmac`
MODIFY `iHmacID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tbl_music`
--
ALTER TABLE `tbl_music`
MODIFY `iMusicID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `tbl_pagecontent`
--
ALTER TABLE `tbl_pagecontent`
MODIFY `iPageID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `tbl_restaurant`
--
ALTER TABLE `tbl_restaurant`
MODIFY `iRestaurantID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `tbl_restaurant_category`
--
ALTER TABLE `tbl_restaurant_category`
MODIFY `iRestaurantCategoryID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=78;
--
-- AUTO_INCREMENT for table `tbl_restaurant_cuisine`
--
ALTER TABLE `tbl_restaurant_cuisine`
MODIFY `iRestaurantCuisineID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT for table `tbl_restaurant_facility`
--
ALTER TABLE `tbl_restaurant_facility`
MODIFY `iRestaurantFacilityID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT for table `tbl_restaurant_image`
--
ALTER TABLE `tbl_restaurant_image`
MODIFY `iPictureID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `tbl_restaurant_menu_image`
--
ALTER TABLE `tbl_restaurant_menu_image`
MODIFY `iMenuPictureID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `tbl_restaurant_music`
--
ALTER TABLE `tbl_restaurant_music`
MODIFY `iRestaurantCategoryID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=65;
--
-- AUTO_INCREMENT for table `tbl_setting`
--
ALTER TABLE `tbl_setting`
MODIFY `iSettingID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
MODIFY `iUserID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `tbl_user_cuisine`
--
ALTER TABLE `tbl_user_cuisine`
MODIFY `iUserCuisineID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `tbl_user_interest`
--
ALTER TABLE `tbl_user_interest`
MODIFY `iUserInterestID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `tbl_user_music`
--
ALTER TABLE `tbl_user_music`
MODIFY `iUserMusicID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `tbl_user_restaurant_favorite`
--
ALTER TABLE `tbl_user_restaurant_favorite`
MODIFY `iFavoriteID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_comment`
--
ALTER TABLE `tbl_comment`
ADD CONSTRAINT `tbl_comment_ibfk_1` FOREIGN KEY (`iUserID`) REFERENCES `tbl_user` (`iUserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_deals`
--
ALTER TABLE `tbl_deals`
ADD CONSTRAINT `index_iRestaurantID` FOREIGN KEY (`iRestaurantID`) REFERENCES `tbl_restaurant` (`iRestaurantID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_deals_like`
--
ALTER TABLE `tbl_deals_like`
ADD CONSTRAINT `fk_iDealID_like` FOREIGN KEY (`iDealID`) REFERENCES `tbl_deals` (`iDealID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_iRestaurantID_like` FOREIGN KEY (`iRestaurantID`) REFERENCES `tbl_restaurant` (`iRestaurantID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_iUserID_like` FOREIGN KEY (`iUserID`) REFERENCES `tbl_user` (`iUserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_hmac`
--
ALTER TABLE `tbl_hmac`
ADD CONSTRAINT `tbl_hmac_ibfk_1` FOREIGN KEY (`iUserID`) REFERENCES `tbl_user` (`iUserID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `tbl_hmac_ibfk_2` FOREIGN KEY (`key_id`) REFERENCES `keys` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_restaurant_category`
--
ALTER TABLE `tbl_restaurant_category`
ADD CONSTRAINT `tbl_restaurant_category_ibfk_1` FOREIGN KEY (`iRestaurantID`) REFERENCES `tbl_restaurant` (`iRestaurantID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `tbl_restaurant_category_ibfk_2` FOREIGN KEY (`iCategoryID`) REFERENCES `tbl_category` (`iCategoryID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_restaurant_cuisine`
--
ALTER TABLE `tbl_restaurant_cuisine`
ADD CONSTRAINT `tbl_restaurant_cuisine_ibfk_1` FOREIGN KEY (`iRestaurantID`) REFERENCES `tbl_restaurant` (`iRestaurantID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `tbl_restaurant_cuisine_ibfk_2` FOREIGN KEY (`iCuisineID`) REFERENCES `tbl_cuisine` (`iCuisineID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_restaurant_facility`
--
ALTER TABLE `tbl_restaurant_facility`
ADD CONSTRAINT `tbl_restaurant_facility_ibfk_1` FOREIGN KEY (`iRestaurantID`) REFERENCES `tbl_restaurant` (`iRestaurantID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `tbl_restaurant_facility_ibfk_2` FOREIGN KEY (`iFacilityID`) REFERENCES `tbl_facility` (`iFacilityID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_restaurant_image`
--
ALTER TABLE `tbl_restaurant_image`
ADD CONSTRAINT `tbl_restaurant_image_ibfk_1` FOREIGN KEY (`iRestaurantID`) REFERENCES `tbl_restaurant` (`iRestaurantID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_restaurant_menu_image`
--
ALTER TABLE `tbl_restaurant_menu_image`
ADD CONSTRAINT `tbl_restaurant_menu_image_ibfk_1` FOREIGN KEY (`iRestaurantID`) REFERENCES `tbl_restaurant` (`iRestaurantID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_restaurant_music`
--
ALTER TABLE `tbl_restaurant_music`
ADD CONSTRAINT `fk_iMusicID` FOREIGN KEY (`iMusicID`) REFERENCES `tbl_music` (`iMusicID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_iRestaurantID` FOREIGN KEY (`iRestaurantID`) REFERENCES `tbl_restaurant` (`iRestaurantID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_user_cuisine`
--
ALTER TABLE `tbl_user_cuisine`
ADD CONSTRAINT `tbl_user_cuisine_ibfk_2` FOREIGN KEY (`iCuisineID`) REFERENCES `tbl_cuisine` (`iCuisineID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `tbl_user_cuisine_ibfk_3` FOREIGN KEY (`iUserID`) REFERENCES `tbl_user` (`iUserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_user_interest`
--
ALTER TABLE `tbl_user_interest`
ADD CONSTRAINT `tbl_user_interest_ibfk_2` FOREIGN KEY (`iInterestID`) REFERENCES `tbl_facility` (`iFacilityID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `tbl_user_interest_ibfk_3` FOREIGN KEY (`iUserID`) REFERENCES `tbl_user` (`iUserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_user_music`
--
ALTER TABLE `tbl_user_music`
ADD CONSTRAINT `tbl_user_music_ibfk_1` FOREIGN KEY (`iUserID`) REFERENCES `tbl_user` (`iUserID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `tbl_user_music_ibfk_2` FOREIGN KEY (`iMusicID`) REFERENCES `tbl_music` (`iMusicID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_user_restaurant_favorite`
--
ALTER TABLE `tbl_user_restaurant_favorite`
ADD CONSTRAINT `tbl_user_restaurant_favorite_ibfk_1` FOREIGN KEY (`iUserID`) REFERENCES `tbl_user` (`iUserID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `tbl_user_restaurant_favorite_ibfk_2` FOREIGN KEY (`iRestaurantID`) REFERENCES `tbl_restaurant` (`iRestaurantID`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
