-- Adminer 4.2.2 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE `category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(30) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `category` (`id`, `categoryName`) VALUES
(0,	'Select category'),
(1,	'bord'),
(2,	'sport');

CREATE TABLE `deletedItem` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `list_unique_string` varchar(5) COLLATE utf8_bin NOT NULL,
  `wish` varchar(50) COLLATE utf8_bin NOT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  `description` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `checked_by` varchar(150) COLLATE utf8_bin DEFAULT NULL,
  `isChecked` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


CREATE TABLE `item` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `list_unique_string` varchar(5) COLLATE utf8_bin NOT NULL,
  `wish` varchar(50) COLLATE utf8_bin NOT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  `description` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `checked_by` varchar(150) COLLATE utf8_bin DEFAULT NULL,
  `isChecked` tinyint(1) DEFAULT NULL,
  `prio` int(11) DEFAULT NULL,
  `cost` int(10) DEFAULT NULL,
  `blacklist` tinyint(2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


CREATE TABLE `list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `firstName` varchar(30) COLLATE utf8_bin NOT NULL,
  `secondName` varchar(30) COLLATE utf8_bin NOT NULL,
  `password` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `paid_list` tinyint(4) DEFAULT '1',
  `unique_string` varchar(5) COLLATE utf8_bin NOT NULL,
  `imageUrl` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `list_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


CREATE TABLE `permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(50) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `permission` (`id`, `description`) VALUES
(1,	'Get all'),
(2,	'Blacklist'),
(3,	'fler än 20 önskningar'),
(4,	'Donedidit');

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


CREATE TABLE `user_permission` (
  `user_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- 2016-01-19 08:19:19