-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2017 年 6 月 05 日 01:40
-- サーバのバージョン： 10.1.16-MariaDB
-- PHP Version: 5.6.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Re_Books`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `books`
--

CREATE TABLE `books` (
  `book_id` int(10) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(20) NOT NULL,
  `reasons` text NOT NULL,
  `book_picture` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `books`
--

INSERT INTO `books` (`book_id`, `user_id`, `title`, `reasons`, `book_picture`, `created`, `modified`) VALUES
(10, 2, 'イーロンマスク', '資産8000億円', '20170603080904elon.jpg', '2017-06-03 14:09:05', '2017-06-03 06:09:05'),
(12, 2, 'ハリーポッターと賢者の石', '誰もが知ってるベストセラーなため', '20170603081555harry1.jpg', '2017-06-03 14:15:56', '2017-06-03 06:15:56'),
(13, 2, 'ハリーポッターの秘密の部屋', '世界的ベストセラー', '20170603124524harry2.jpg', '2017-06-03 18:45:24', '2017-06-03 10:45:24'),
(14, 2, 'ハリーポッター', '世界的ベストセラー', '20170603125550harry3.jpg', '2017-06-03 18:55:53', '2017-06-03 10:55:53'),
(15, 4, 'ハリーポッターと炎のゴブレット', '世界的ベストセラー', '20170603133144harrt4.jpg', '2017-06-03 19:31:44', '2017-06-03 11:31:44'),
(16, 4, 'ハリーポッター', '世界的ベストセラー', '20170603132651harry5.jpg', '2017-06-03 19:26:52', '2017-06-03 11:26:52'),
(17, 4, 'ハリーポッターと謎のプリンス', '謎のプリンス', '20170603160015harry6.jpg', '2017-06-03 22:00:15', '2017-06-03 14:00:15'),
(18, 5, 'ハリーポッターと死の秘宝', '世界的ベストセラー', '20170604054526harry7.jpg', '2017-06-04 11:47:19', '2017-06-04 03:47:19'),
(21, 5, 'うんこ漢字ドリル', 'Amazonで5位だったため', '20170604064231book3.jpg', '2017-06-04 12:43:38', '2017-06-04 04:43:38'),
(31, 5, 'うんこ漢字ドリル', 'Amazonで5位だったため', '20170604064231book3.jpg', '2017-06-04 12:43:38', '2017-06-04 04:43:38'),
(32, 5, 'ハリーポッターと死の秘宝', '世界的ベストセラー', '20170604054526harry7.jpg', '2017-06-04 11:47:19', '2017-06-04 03:47:19');

-- --------------------------------------------------------

--
-- テーブルの構造 `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `categories`
--

INSERT INTO `categories` (`category_id`, `name`) VALUES
(1, '小説'),
(2, '漫画'),
(3, 'レシピ本'),
(4, '歴史'),
(5, '教材'),
(6, '経済'),
(7, '聖書'),
(8, 'その他');

-- --------------------------------------------------------

--
-- テーブルの構造 `contact`
--

CREATE TABLE `contact` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contents` varchar(255) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- テーブルの構造 `likes`
--

CREATE TABLE `likes` (
  `member_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `likes`
--

INSERT INTO `likes` (`member_id`, `book_id`) VALUES
(2, 18),
(4, 26),
(4, 18),
(2, 26);

-- --------------------------------------------------------

--
-- テーブルの構造 `members`
--

CREATE TABLE `members` (
  `member_id` int(11) NOT NULL,
  `nick_name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `password` varchar(100) CHARACTER SET utf8 NOT NULL,
  `picture_path` varchar(255) CHARACTER SET utf8 NOT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- テーブルのデータのダンプ `members`
--

INSERT INTO `members` (`member_id`, `nick_name`, `email`, `password`, `picture_path`, `created`, `modified`) VALUES
(2, 'Shun', 'shun@gmail.com', '989bb98d81d3af9547ba4831dad0851e01ec1233', '20170603091602shun.jpg', '2017-06-03 11:31:03', '2017-06-03 07:16:02'),
(4, 'Imajin', 'Imajin@gmail.com', '989bb98d81d3af9547ba4831dad0851e01ec1233', '20170603131509imagin.jpg', '2017-06-03 19:15:13', '2017-06-03 11:15:13'),
(5, 'saki', 'saki1@gmail.com', '989bb98d81d3af9547ba4831dad0851e01ec1233', '20170604053350saki.jpg', '2017-06-04 11:14:06', '2017-06-04 03:33:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`member_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
