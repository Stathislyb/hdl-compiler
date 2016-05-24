-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 24, 2016 at 12:42 PM
-- Server version: 10.1.10-MariaDB
-- PHP Version: 5.5.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vhdl_compiler`
--

-- --------------------------------------------------------

--
-- Table structure for table `libraries`
--

CREATE TABLE `libraries` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `approved` int(1) NOT NULL DEFAULT '0',
  `owner_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `rating` float NOT NULL DEFAULT '0',
  `rating_count` int(10) NOT NULL DEFAULT '0',
  `downloads` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `short_code` varchar(50) NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `name`, `description`, `short_code`, `public`) VALUES
(41, '3 : 8 Decoder using basic logic gates', 'Here is the code for 3 : 8 Decoder using basic logic gates such as AND,NOT,OR etc.The module has one 3-bit input which is decoded as a 8-bit output.', '3-8-decoder-using-basic-logic-gates', 1),
(42, 'first project', 'My first project', 'first-project', 1),
(45, 'Full Adder', '', 'full-adder', 1);

-- --------------------------------------------------------

--
-- Table structure for table `projects_editors`
--

CREATE TABLE `projects_editors` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_type` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `projects_editors`
--

INSERT INTO `projects_editors` (`id`, `user_id`, `project_id`, `user_type`) VALUES
(76, 1001, 41, 1),
(77, 1010, 42, 1),
(80, 1001, 45, 1);

-- --------------------------------------------------------

--
-- Table structure for table `project_files`
--

CREATE TABLE `project_files` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `project_id` int(11) NOT NULL,
  `compiled` int(1) NOT NULL DEFAULT '0',
  `component` int(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `project_files`
--

INSERT INTO `project_files` (`id`, `name`, `project_id`, `compiled`, `component`) VALUES
(99, 'decoder.vhdl', 41, 1, 0),
(100, 'testbench.vhdl', 41, 2, 0),
(103, 'main.vhdl', 42, 0, 0),
(118, 'adder.vhdl', 45, 1, 0),
(119, 'testbench.vhdl', 45, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `sid_files`
--

CREATE TABLE `sid_files` (
  `id` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `compiled` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sid_files`
--

INSERT INTO `sid_files` (`id`, `sid`, `name`, `compiled`) VALUES
(7, 74839, 'compare.vhdl', 1),
(8, 74839, 'testbench.vhdl', 1),
(9, 13659, 'test.vhdl', 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(32) NOT NULL,
  `type` int(1) NOT NULL DEFAULT '0',
  `email` varchar(50) NOT NULL,
  `telephone` int(10) DEFAULT NULL,
  `activated` tinyint(1) NOT NULL DEFAULT '0',
  `activ_code` int(6) NOT NULL,
  `theme` tinyint(1) NOT NULL DEFAULT '0',
  `available_space` int(6) NOT NULL DEFAULT '20'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `type`, `email`, `telephone`, `activated`, `activ_code`, `theme`, `available_space`) VALUES
(1001, 'user1', 'a722c63db8ec8625af6cf71cb8c2d939', 0, 'user1@mymail.me', 2147483647, 1, 0, 5, 20),
(1004, 'user2', 'c1572d05424d0ecb2a65ec6a82aeacbf', 1, 'user2@mymail.me', NULL, 1, 1, 0, 20),
(1007, 'user3', '3afc79b597f88a72528e864cf81856d2', 0, 'user3@mymail.me', NULL, 1, 2, 0, 20),
(1008, 'user4', 'fc2921d9057ac44e549efaf0048b2512', 0, 'user4@mail.com', NULL, 0, 3, 0, 20),
(1010, 'talsiber', '5f4dcc3b5aa765d61d8327deb882cf99', 0, 'e.l.d@live.com', 2147483647, 1, 0, 16, 20);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `libraries`
--
ALTER TABLE `libraries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects_editors`
--
ALTER TABLE `projects_editors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_files`
--
ALTER TABLE `project_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sid_files`
--
ALTER TABLE `sid_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `libraries`
--
ALTER TABLE `libraries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;
--
-- AUTO_INCREMENT for table `projects_editors`
--
ALTER TABLE `projects_editors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;
--
-- AUTO_INCREMENT for table `project_files`
--
ALTER TABLE `project_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;
--
-- AUTO_INCREMENT for table `sid_files`
--
ALTER TABLE `sid_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1011;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
