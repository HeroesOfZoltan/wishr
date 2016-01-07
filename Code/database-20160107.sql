SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(30) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `item`;
CREATE TABLE `item` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `list_unique_string` varchar(5) COLLATE utf8_bin NOT NULL,
  `wish` varchar(50) COLLATE utf8_bin NOT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  `description` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `checked_by` varchar(150) COLLATE utf8_bin DEFAULT NULL,
  `isChecked` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `list`;
CREATE TABLE `list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `listName` varchar(30) COLLATE utf8_bin NOT NULL,
  `password` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `paid_list` tinyint(4) DEFAULT '1',
  `unique_string` varchar(5) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `list_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `lastname` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(60) COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `role` tinyint(1) DEFAULT NULL,
  `listIdGuest` tinyint(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;