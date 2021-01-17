CREATE DATABASE `userApi`;

CREATE TABLE `userApi`.`users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `firstName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `lastName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `dateCreated` TIMESTAMP NOT NULL,
  `darkMode` TINYINT DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
