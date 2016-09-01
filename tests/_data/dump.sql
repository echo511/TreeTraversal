-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `tree`;
CREATE TABLE `tree` (
  `title` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `dpt` int(11) NOT NULL,
  `prt` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `additional` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `tree` (`title`, `lft`, `rgt`, `dpt`, `prt`) VALUES
('A',	1,	16,	0,	NULL),
('B',	2,	3,	1,	'A'),
('C',	4,	13,	1,	'A'),
('D',	5,	6,	2,	'C'),
('E',	7,	12,	2,	'C'),
('F',	8,	9,	3,	'E'),
('G',	10,	11,	3,	'E'),
('H',	14,	15,	1,	'A');

-- 2016-08-23 20:50:55