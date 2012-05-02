# ************************************************************
# Sequel Pro SQL dump
# Version 3604
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.5.23-1~dotdeb.0)
# Datenbank: HMS
# Erstellungsdauer: 2012-05-02 07:33:24 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Export von Tabelle hms_cache
# ------------------------------------------------------------

DROP TABLE IF EXISTS `hms_cache`;

CREATE TABLE `hms_cache` (
  `module` varchar(20) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `hms_cache` WRITE;
/*!40000 ALTER TABLE `hms_cache` DISABLE KEYS */;

INSERT INTO `hms_cache` (`module`, `timestamp`)
VALUES
    ('Homematic',1335940862),
    ('MAX',1328791148);

/*!40000 ALTER TABLE `hms_cache` ENABLE KEYS */;
UNLOCK TABLES;


# Export von Tabelle hms_devices
# ------------------------------------------------------------

DROP TABLE IF EXISTS `hms_devices`;

CREATE TABLE `hms_devices` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `read` tinyint(11) DEFAULT NULL,
  `write` tinyint(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Export von Tabelle hms_floors
# ------------------------------------------------------------

DROP TABLE IF EXISTS `hms_floors`;

CREATE TABLE `hms_floors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `hms_floors` WRITE;
/*!40000 ALTER TABLE `hms_floors` DISABLE KEYS */;

INSERT INTO `hms_floors` (`id`, `name`)
VALUES
    (0,'Erdgeschoss'),
    (1,'Obergeschoss'),
    (2,'Dachgeschoss'),
    (-1,'Untergeschoss');

/*!40000 ALTER TABLE `hms_floors` ENABLE KEYS */;
UNLOCK TABLES;


# Export von Tabelle hms_modules
# ------------------------------------------------------------

DROP TABLE IF EXISTS `hms_modules`;

CREATE TABLE `hms_modules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `hms_modules` WRITE;
/*!40000 ALTER TABLE `hms_modules` DISABLE KEYS */;

INSERT INTO `hms_modules` (`id`, `name`, `active`)
VALUES
    (1,'Homematic',1),
    (2,'MAX',1),
    (3,'VSX-921',0);

/*!40000 ALTER TABLE `hms_modules` ENABLE KEYS */;
UNLOCK TABLES;


# Export von Tabelle hms_rooms
# ------------------------------------------------------------

DROP TABLE IF EXISTS `hms_rooms`;

CREATE TABLE `hms_rooms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `floor` mediumint(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `hms_rooms` WRITE;
/*!40000 ALTER TABLE `hms_rooms` DISABLE KEYS */;

INSERT INTO `hms_rooms` (`id`, `name`, `floor`)
VALUES
    (1,'Kinderzimmer',1),
    (2,'Bad',1),
    (3,'Wohnzimmer',0),
    (4,'Büro',-1),
    (5,'Bad',2),
    (6,'Gäste WC',0),
    (7,'Küche',0),
    (8,'Flur',0),
    (9,'Gästezimmer',2),
    (10,'Flur',1),
    (11,'Waschraum',-1),
    (12,'Garten',0),
    (13,'Schlafzimmer',1),
    (14,'Flur',-1);

/*!40000 ALTER TABLE `hms_rooms` ENABLE KEYS */;
UNLOCK TABLES;


# Export von Tabelle hms_rooms_map
# ------------------------------------------------------------

DROP TABLE IF EXISTS `hms_rooms_map`;

CREATE TABLE `hms_rooms_map` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `hms_room` int(11) NOT NULL,
  `module_room` int(11) NOT NULL,
  `module` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `hms_room` (`hms_room`),
  KEY `module` (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `hms_rooms_map` WRITE;
/*!40000 ALTER TABLE `hms_rooms_map` DISABLE KEYS */;

INSERT INTO `hms_rooms_map` (`id`, `hms_room`, `module_room`, `module`)
VALUES
    (1,1,1413,'modules\\system\\Homematic\\Homematic'),
    (2,1,1,'modules\\system\\MAX\\MAX'),
    (3,5,1412,'modules\\system\\Homematic\\Homematic'),
    (4,2,1411,'modules\\system\\Homematic\\Homematic'),
    (5,6,1410,'modules\\system\\Homematic\\Homematic'),
    (6,9,1415,'modules\\system\\Homematic\\Homematic'),
    (7,8,1409,'modules\\system\\Homematic\\Homematic'),
    (8,10,1416,'modules\\system\\Homematic\\Homematic'),
    (9,12,1371,'modules\\system\\Homematic\\Homematic'),
    (10,7,1342,'modules\\system\\Homematic\\Homematic'),
    (11,13,1343,'modules\\system\\Homematic\\Homematic'),
    (12,11,1417,'modules\\system\\Homematic\\Homematic'),
    (13,3,1341,'modules\\system\\Homematic\\Homematic'),
    (14,2,2,'modules\\system\\MAX\\MAX');

/*!40000 ALTER TABLE `hms_rooms_map` ENABLE KEYS */;
UNLOCK TABLES;


# Export von Tabelle hms_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `hms_users`;

CREATE TABLE `hms_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Export von Tabelle homematic_device_channel
# ------------------------------------------------------------

DROP TABLE IF EXISTS `homematic_device_channel`;

CREATE TABLE `homematic_device_channel` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `deviceId` int(11) NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `index` int(11) DEFAULT NULL,
  `partnerId` int(11) DEFAULT NULL,
  `mode` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `category` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `isReady` int(11) DEFAULT NULL,
  `isUsable` int(11) DEFAULT NULL,
  `isVisible` int(11) DEFAULT NULL,
  `isLogged` int(11) DEFAULT NULL,
  `isLogable` int(11) DEFAULT NULL,
  `isWritable` int(11) DEFAULT NULL,
  `isEventable` int(11) DEFAULT NULL,
  `isAesAvailable` int(11) DEFAULT NULL,
  `isVirtual` int(11) DEFAULT NULL,
  `isReadable` int(11) DEFAULT NULL,
  KEY `deviceId` (`deviceId`),
  KEY `isReadable` (`isReadable`),
  KEY `isWritable` (`isWritable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


# Export von Tabelle homematic_devices
# ------------------------------------------------------------

DROP TABLE IF EXISTS `homematic_devices`;

CREATE TABLE `homematic_devices` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `interface` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `interface` (`interface`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


# Export von Tabelle homematic_room_device
# ------------------------------------------------------------

DROP TABLE IF EXISTS `homematic_room_device`;

CREATE TABLE `homematic_room_device` (
  `roomid` int(11) unsigned NOT NULL,
  `channelid` int(11) NOT NULL,
  KEY `roomid` (`roomid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

# Export von Tabelle homematic_rooms
# ------------------------------------------------------------

DROP TABLE IF EXISTS `homematic_rooms`;

CREATE TABLE `homematic_rooms` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


# Export von Tabelle max_device_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `max_device_log`;

CREATE TABLE `max_device_log` (
  `RFID` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `Position` int(11) DEFAULT NULL,
  `Temperature` char(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `initialized` int(11) DEFAULT NULL,
  `isAnswer` int(11) DEFAULT NULL,
  `Error` int(11) DEFAULT NULL,
  `Valid` int(11) DEFAULT NULL,
  `DST` int(11) DEFAULT NULL,
  `GatewayOK` int(11) DEFAULT NULL,
  `PanelLock` int(11) DEFAULT NULL,
  `LinkError` int(11) DEFAULT NULL,
  `LowBatt` int(11) DEFAULT NULL,
  `Mode` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `interface` (`RFID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Export von Tabelle max_devices
# ------------------------------------------------------------

DROP TABLE IF EXISTS `max_devices`;

CREATE TABLE `max_devices` (
  `id` varchar(11) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `serial` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'modules\\MAX\\MAX',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

# Export von Tabelle max_room_device
# ------------------------------------------------------------

DROP TABLE IF EXISTS `max_room_device`;

CREATE TABLE `max_room_device` (
  `roomid` varchar(11) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `channelid` varchar(11) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  KEY `roomid` (`roomid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

# Export von Tabelle max_rooms
# ------------------------------------------------------------

DROP TABLE IF EXISTS `max_rooms`;

CREATE TABLE `max_rooms` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'modules\\MAX\\MAX',
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

