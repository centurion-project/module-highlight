-- phpMyAdmin SQL Dump
-- version 3.3.2
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Mer 16 Juin 2010 à 16:47
-- Version du serveur: 5.1.41
-- Version de PHP: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET foreign_key_checks = 0;

--
-- Base de données: `sam`
--

-- --------------------------------------------------------

--
-- Structure de la table `highlight_container`
--
drop table IF EXISTS highlight_container;
CREATE TABLE IF NOT EXISTS `highlight_container` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `feed_id` int(11) unsigned DEFAULT NULL,
  `content_type_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `hightlight_flux` (`feed_id`),
  KEY `content_type_id` (`content_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `highlight_feed`
--
drop table IF EXISTS highlight_feed;
CREATE TABLE IF NOT EXISTS `highlight_feed` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `hightlight_container_id` int(11) unsigned DEFAULT NULL,
  `proxy_content_type_id` int(11) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `where` varchar(255) DEFAULT NULL,
  `order` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hightlight_container_id` (`hightlight_container_id`,`proxy_content_type_id`),
  KEY `proxy_content_type_id` (`proxy_content_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Structure de la table `highlight_row`
--
drop table IF EXISTS highlight_row;
CREATE TABLE IF NOT EXISTS `highlight_row` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `proxy_content_type_id` int(11) unsigned DEFAULT NULL,
  `proxy_pk` varchar(255) NOT NULL,
  `container_id` int(11) unsigned NOT NULL,
  `position` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hightlight_container` (`container_id`),
  KEY `proxy_model` (`proxy_content_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `highlight_container`
--
ALTER TABLE `highlight_container`
  ADD CONSTRAINT `highlight_container_ibfk_1` FOREIGN KEY (`feed_id`) REFERENCES `highlight_feed` (`id`),
  ADD CONSTRAINT `highlight_contenttype_ibfk_1` FOREIGN KEY (`content_type_id`) REFERENCES `centurion_content_type` (`id`);

--
-- Contraintes pour la table `highlight_feed`
--
ALTER TABLE `highlight_feed`
  ADD CONSTRAINT `highlight_feed_ibfk_1` FOREIGN KEY (`hightlight_container_id`) REFERENCES `highlight_container` (`id`),
  ADD CONSTRAINT `highlight_feed_ibfk_2` FOREIGN KEY (`proxy_content_type_id`) REFERENCES `centurion_content_type` (`id`);

--
-- Contraintes pour la table `highlight_row`
--
ALTER TABLE `highlight_row`
  ADD CONSTRAINT `highlight_row_ibfk_2` FOREIGN KEY (`container_id`) REFERENCES `highlight_container` (`id`),
  ADD CONSTRAINT `highlight_row_ibfk_3` FOREIGN KEY (`proxy_content_type_id`) REFERENCES `centurion_content_type` (`id`);
