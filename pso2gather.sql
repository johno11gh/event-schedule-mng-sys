-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: 2021 年 11 月 22 日 16:13
-- サーバのバージョン： 5.7.23
-- PHP Version: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pso2gather`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `gatherData`
--

CREATE TABLE `gatherData` (
  `id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `finish_time` datetime NOT NULL,
  `ship` int(11) NOT NULL,
  `block` int(11) NOT NULL,
  `gather_title` varchar(255) NOT NULL,
  `organizer` int(11) NOT NULL,
  `tag` varchar(255) NOT NULL,
  `others` varchar(255) DEFAULT NULL,
  `tw_u_id_bot_input` varchar(255) DEFAULT NULL,
  `organizer_confirm_flg` tinyint(1) NOT NULL DEFAULT '0',
  `tweet_url` varchar(255) DEFAULT NULL,
  `delete_flg` tinyint(1) NOT NULL DEFAULT '0',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `twitter_account` varchar(255) NOT NULL,
  `twitter_user_id` varchar(255) NOT NULL,
  `ban_flg` tinyint(1) NOT NULL DEFAULT '0',
  `ban_flg_date` datetime DEFAULT NULL,
  `login_time` datetime NOT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `delete_flg` tinyint(1) NOT NULL DEFAULT '0',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gatherData`
--
ALTER TABLE `gatherData`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gatherData`
--
ALTER TABLE `gatherData`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
