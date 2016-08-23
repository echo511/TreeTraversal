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
  PRIMARY KEY (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `tree` (`title`, `lft`, `rgt`, `dpt`) VALUES
('A',	1,	16,	0),
('B',	2,	3,	1),
('C',	4,	13,	1),
('D',	5,	6,	2),
('E',	7,	12,	2),
('F',	8,	9,	3),
('G',	10,	11,	3),
('H',	14,	15,	1);

-- 2016-08-23 20:50:55