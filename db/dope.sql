-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Мар 20 2012 г., 23:06
-- Версия сервера: 5.1.40
-- Версия PHP: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `dope`
--

-- --------------------------------------------------------

--
-- Структура таблицы `dope_chat`
--

CREATE TABLE IF NOT EXISTS `dope_chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('private','place','event','profile_picture','picture','scopeout') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'private',
  `thread_start` tinyint(1) NOT NULL DEFAULT '1',
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `place_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `lon` double NOT NULL,
  `lat` double NOT NULL,
  `message` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `photo_attach_id` int(11) NOT NULL,
  `friend_request` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `dope_chat`
--

INSERT INTO `dope_chat` (`id`, `type`, `thread_start`, `sender_id`, `receiver_id`, `place_id`, `event_id`, `lon`, `lat`, `message`, `timestamp`, `photo_attach_id`, `friend_request`) VALUES
(1, 'private', 1, 50008388, 60069664, 0, 0, 0, 0, '342 54flg hfdshgsfd hgfds lk dfsg fdg', '2012-03-09 12:27:11', 0, 0),
(2, 'private', 1, 50008388, 60069664, 0, 0, 0, 0, '342 54flg hfdshgsfd hgfds lk dfsg fdg', '2012-03-09 12:27:24', 0, 0),
(3, 'private', 1, 50008388, 60069664, 0, 0, 0, 0, 'fsdg fgfds gfds', '2012-03-09 12:27:36', 0, 0),
(4, 'private', 1, 50008388, 60069664, 0, 0, 0, 0, 'fsdg fgfds gfds', '2012-03-09 12:28:01', 0, 0),
(5, 'private', 1, 50008388, 60069664, 0, 0, 0, 0, 'sgdfsgdsfg', '2012-03-10 08:59:26', 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `dope_events`
--

CREATE TABLE IF NOT EXISTS `dope_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `create_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `start_stamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_stamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `dope_follows`
--

CREATE TABLE IF NOT EXISTS `dope_follows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `place_id` int(11) NOT NULL DEFAULT '0',
  `event_id` int(11) NOT NULL DEFAULT '0',
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `dope_friends`
--

CREATE TABLE IF NOT EXISTS `dope_friends` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  `accepted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `dope_places`
--

CREATE TABLE IF NOT EXISTS `dope_places` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `lon` double NOT NULL,
  `lat` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `dope_sessions`
--

CREATE TABLE IF NOT EXISTS `dope_sessions` (
  `id` int(11) NOT NULL,
  `session_id` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `login` int(11) NOT NULL,
  `pass` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `hash_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `dope_users`
--

CREATE TABLE IF NOT EXISTS `dope_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `password_md5` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `verify_code` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `hash_id` bigint(20) NOT NULL,
  `photo_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `facebook_token` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `profile_data` longtext COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `major` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `gender` int(11) NOT NULL,
  `greek` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `grad_year` int(11) NOT NULL,
  `lon` double NOT NULL,
  `lat` double NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `privacy_location` enum('everyone','state','friends','none') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'everyone',
  `privacy_pictures` enum('everyone','state','friends','none') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'everyone',
  `privacy_friends` enum('everyone','state','friends','none') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'everyone',
  `privacy_info` enum('everyone','state','friends','none') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'everyone',
  `privacy_statuses` enum('everyone','state','friends','none') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'everyone',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=39 ;

--
-- Дамп данных таблицы `dope_users`
--

INSERT INTO `dope_users` (`id`, `email`, `password_md5`, `verify_code`, `hash_id`, `photo_id`, `facebook_token`, `profile_data`, `name`, `major`, `gender`, `greek`, `grad_year`, `lon`, `lat`, `create_time`, `update_time`, `privacy_location`, `privacy_pictures`, `privacy_friends`, `privacy_info`, `privacy_statuses`) VALUES
(14, 'david@fratlabs.com', '3c9490d10b621e240c5c5d1d4278c63c', '', 123213435232, '', '', '', '', '', 0, '', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'everyone', 'everyone', 'everyone', 'everyone', 'everyone'),
(13, 'smd5071@gmail.com', '74af75542447352dea60f1d1e24f64a7', '', 123213435231, '', '', '', '', '', 0, '', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'everyone', 'everyone', 'everyone', 'everyone', 'everyone'),
(33, 'ccc@ccc', '0b4e7a0e5fe84ad35fb5f95b9ceeac79', '', 44191614, '', '', '{"Name":"Alex Strelets","Gender":0,"Relationship":1,"Graduation Year":"","Major":"","Greek":""}', 'Alex Strelets', '', 0, '', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'everyone', 'everyone', 'everyone', 'everyone', 'everyone'),
(25, 'alex.strelets@gmail.com', '7e7576bde8baa58874dc2a8a752ee3dc', '', 123213435233, '', '', '{"Name":"Alex Strelets","Gender":0,"Relationship":1,"classes":[{"classSection":3,"class":"cvxb","classSubject":"gbvxc"},{"classSection":2,"class":"Zzz","classSubject":"zzz"},{"classSubject":"fsfdg","class":"Dfdsf","classSection":2}],"Graduation Year":"","Major":"","Greek":""}', 'Alex Strelets', '', 0, '', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'everyone', 'everyone', 'everyone', 'everyone', 'everyone'),
(37, '111111@11', '96e79218965eb72c92a549dd5a330112', '', 29699472, '', '', '{"Name":"","Gender":"","Graduation Year":"","Major":"","Greek":"","Relationship":""}', '', '', 0, '', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'everyone', 'everyone', 'everyone', 'everyone', 'everyone'),
(34, '111@111', '96e79218965eb72c92a549dd5a330112', '', 50008388, '', '', '{"Name":"John Doe","Gender":0,"Graduation Year":"2012","Major":"","Greek":"","Relationship":2,"classes":[{"classSection":0,"class":"123","classSubject":"123"}]}', 'John Doe', '', 0, '', 2012, 0, 0, '0000-00-00 00:00:00', '2012-03-07 11:57:02', 'everyone', 'everyone', 'everyone', 'everyone', 'everyone'),
(35, '222@222', 'e3ceb5881a0a1fdaad01296d7554868d', '', 60069664, '', '', '{"Name":"Two Two","Gender":0,"Graduation Year":"","Major":"","Greek":"","Relationship":2,"classes":[{"classSection":2,"class":"dfsgdf","classSubject":"dsafsda"},{"classSection":1,"class":"1234","classSubject":"1324"}]}', 'Two Two', '', 0, '', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'everyone', 'everyone', 'everyone', 'everyone', 'everyone'),
(36, '333@222', '731982a033a5cc815ac03c8504abb748', '', 76482091, '', '', '{"Name":"","Gender":"","Graduation Year":"","Major":"","Greek":"","Relationship":""}', '', '', 0, '', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'everyone', 'everyone', 'everyone', 'everyone', 'everyone'),
(38, 'www@www', 'd785c99d298a4e9e6e13fe99e602ef42', '', 54409499, '', '', '{"Name":"John Doe","Gender":0,"Graduation Year":"2012","Major":"","Greek":"","Relationship":2,"classes":[{"classSection":0,"class":"123","classSubject":"123"}]}', 'John Doe', '', 0, '', 2012, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'everyone', 'everyone', 'everyone', 'everyone', 'everyone');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
