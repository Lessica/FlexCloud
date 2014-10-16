/*
MySQL Backup
Source Server Version: 5.5.16
Source Database: test
Date: 2014-7-13 09:00:00
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
--  Table structure for `applist`
-- ----------------------------
DROP TABLE IF EXISTS `applist`;
CREATE TABLE `applist` (
  `ID` int(8) NOT NULL AUTO_INCREMENT,
  `Name` varchar(512) CHARACTER SET gb2312 NOT NULL,
  `Identifier` varchar(512) CHARACTER SET gb2312 NOT NULL,
  `ToShow` int(8) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=3341 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `info`
-- ----------------------------
DROP TABLE IF EXISTS `info`;
CREATE TABLE `info` (
  `Name` varchar(16) CHARACTER SET gb2312 NOT NULL,
  `Counts` bigint(20) NOT NULL,
  `Switch` int(1) unsigned zerofill NOT NULL,
  `Contents` varchar(512) CHARACTER SET gb2312 NOT NULL,
  PRIMARY KEY (`Name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `patches`
-- ----------------------------
DROP TABLE IF EXISTS `patches`;
CREATE TABLE `patches` (
  `ID` int(8) NOT NULL AUTO_INCREMENT,
  `Name` varchar(512) CHARACTER SET gb2312 NOT NULL,
  `Author` varchar(512) CHARACTER SET gb2312 NOT NULL,
  `Description` varchar(4096) CHARACTER SET gb2312 NOT NULL,
  `UploadStamp` timestamp NULL DEFAULT NULL,
  `DownloadTimes` int(8) NOT NULL,
  `AverageRating` float NOT NULL DEFAULT '0',
  `AuthorID` int(8) NOT NULL,
  `iOS` varchar(16) CHARACTER SET gb2312 NOT NULL DEFAULT '6.1.2',
  `appVersion` varchar(16) CHARACTER SET gb2312 NOT NULL,
  `Units` longtext CHARACTER SET gb2312 NOT NULL,
  `Control` int(1) NOT NULL,
  `Identifier` varchar(512) CHARACTER SET gb2312 NOT NULL,
  `appID` int(8) NOT NULL,
  `UUID` varchar(512) CHARACTER SET gb2312 NOT NULL,
  `ToShow` int(8) NOT NULL,
  `iosLong` int(11) NOT NULL,
  `Version` varchar(8) CHARACTER SET gb2312 NOT NULL,
  `appTargetVersion` varchar(16) CHARACTER SET gb2312 NOT NULL,
  `SwitchedOn` varchar(16) CHARACTER SET gb2312 NOT NULL DEFAULT 'false',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=8174 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `purchase`
-- ----------------------------
DROP TABLE IF EXISTS `purchase`;
CREATE TABLE `purchase` (
  `ID` int(8) NOT NULL AUTO_INCREMENT,
  `UDID` varchar(512) CHARACTER SET gb2312 NOT NULL,
  `PurchaseStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `rates`
-- ----------------------------
DROP TABLE IF EXISTS `rates`;
CREATE TABLE `rates` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` varchar(512) CHARACTER SET gb2312 NOT NULL,
  `PatchID` int(11) NOT NULL,
  `Stars` int(11) NOT NULL,
  `UpdateStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=20926 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `ID` int(8) NOT NULL AUTO_INCREMENT,
  `Username` varchar(512) CHARACTER SET gb2312 NOT NULL,
  `Password` varchar(512) CHARACTER SET gb2312 NOT NULL,
  `Email` varchar(512) CHARACTER SET gb2312 NOT NULL,
  `CreateStamp` timestamp NULL DEFAULT NULL,
  `Loginkey` varchar(512) CHARACTER SET gb2312 NOT NULL,
  `Rights` int(8) NOT NULL DEFAULT '0',
  `Money` int(16) NOT NULL DEFAULT '0',
  `PresentTimes` int(8) NOT NULL DEFAULT '0',
  `LastLoginStamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ToShow` int(8) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=4909 DEFAULT CHARSET=utf8;