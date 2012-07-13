-- phpMyAdmin SQL Dump
-- version 3.3.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 12, 2012 at 06:58 PM
-- Server version: 5.1.63
-- PHP Version: 5.3.5-1ubuntu7.10

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;

--
-- Database: `afdel`
--

-- --------------------------------------------------------

--
-- Table structure for table `highlight_container`
--

CREATE TABLE IF NOT EXISTS `highlight_container` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `proxy_content_type_id` int(11) unsigned DEFAULT NULL,
  `proxy_pk` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `proxy_model` (`proxy_content_type_id`,`proxy_pk`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

-- --------------------------------------------------------


--
-- Table structure for table `highlight_row`
--

CREATE TABLE IF NOT EXISTS `highlight_row` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `proxy_content_type_id` int(11) unsigned DEFAULT NULL,
  `proxy_pk` varchar(255) NOT NULL,
  `container_id` int(11) unsigned NOT NULL,
  `position` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hightlight_container` (`container_id`),
  KEY `proxy_model` (`proxy_content_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `highlight_row`
--
ALTER TABLE `highlight_row`
  ADD CONSTRAINT `highlight_row_ibfk_2` FOREIGN KEY (`container_id`) REFERENCES `highlight_container` (`id`),
  ADD CONSTRAINT `highlight_row_ibfk_3` FOREIGN KEY (`proxy_content_type_id`) REFERENCES `centurion_content_type` (`id`);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

