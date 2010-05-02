-- phpMyAdmin SQL Dump
-- version 3.2.2.1deb1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Ноя 05 2009 г., 14:04
-- Версия сервера: 5.1.37
-- Версия PHP: 5.2.10-2ubuntu6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `wpiernewdb`
--

-- --------------------------------------------------------

--
-- Структура таблицы `indexes`
--

CREATE TABLE IF NOT EXISTS `indexes` (
  `wrd` varchar(50) NOT NULL,
  `url` varchar(255) NOT NULL,
  `txt` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  KEY `wrd` (`wrd`),
  KEY `url` (`url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `indexeslinks`
--

CREATE TABLE IF NOT EXISTS `indexeslinks` (
  `url` varchar(32) NOT NULL,
  KEY `url` (`url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
