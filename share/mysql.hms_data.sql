-- Create syntax for TABLE '201205'
CREATE TABLE `201205` (
  `Interface` varchar(255) NOT NULL DEFAULT '',
  `address` varchar(255) NOT NULL DEFAULT '',
  `key` varchar(255) NOT NULL DEFAULT '',
  `value` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `module` varchar(255) NOT NULL DEFAULT 'modules\\system\\Homematic\\Homematic',
  KEY `Interface` (`Interface`),
  KEY `address` (`address`),
  KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for TABLE '201206'
CREATE TABLE `201206` (
  `Interface` varchar(255) NOT NULL DEFAULT '',
  `address` varchar(255) NOT NULL DEFAULT '',
  `key` varchar(255) NOT NULL DEFAULT '',
  `value` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `module` varchar(255) NOT NULL DEFAULT 'modules\\system\\Homematic\\Homematic',
  KEY `Interface` (`Interface`),
  KEY `address` (`address`),
  KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'TEMPLATE'
CREATE TABLE `TEMPLATE` (
  `Interface` varchar(255) NOT NULL DEFAULT '',
  `address` varchar(255) NOT NULL DEFAULT '',
  `key` varchar(255) NOT NULL DEFAULT '',
  `value` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `module` varchar(255) NOT NULL DEFAULT 'modules\\system\\Homematic\\Homematic',
  KEY `Interface` (`Interface`),
  KEY `address` (`address`),
  KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
