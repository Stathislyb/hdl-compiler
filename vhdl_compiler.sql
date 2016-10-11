-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 10, 2016 at 09:46 AM
-- Server version: 10.1.10-MariaDB
-- PHP Version: 5.5.33


--
-- Database: `vhdl_compiler`
--

-- --------------------------------------------------------

--
-- Table structure for table `libraries`
--

CREATE TABLE `libraries` (
  `id` int(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `owner_id` int(10) NOT NULL,
  `file_id` int(10) NOT NULL,
  `version` smallint(4) NOT NULL DEFAULT '1',
  `pending_suggestion` tinyint(1) NOT NULL DEFAULT '0',
  `downloads` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `library_updates`
--

CREATE TABLE `library_updates` (
  `id` int(10) NOT NULL,
  `lib_id` int(10) NOT NULL,
  `library` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `short_code` varchar(50) NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `projects_editors`
--

CREATE TABLE `projects_editors` (
  `id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `project_id` int(10) NOT NULL,
  `user_type` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `project_files`
--

CREATE TABLE `project_files` (
  `id` int(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `project_id` int(10) NOT NULL,
  `compiled` tinyint(1) NOT NULL DEFAULT '0',
  `component` int(10) NOT NULL DEFAULT '0',
  `version` smallint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `sid_files`
--

CREATE TABLE `sid_files` (
  `id` int(10) NOT NULL,
  `sid` int(10) NOT NULL,
  `name` varchar(25) NOT NULL,
  `compiled` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(32) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `email` varchar(50) NOT NULL,
  `telephone` bigint(10) DEFAULT NULL,
  `activated` tinyint(1) NOT NULL DEFAULT '0',
  `theme` tinyint(2) NOT NULL DEFAULT '0',
  `available_space` int(6) NOT NULL DEFAULT '20'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `type`, `email`, `telephone`, `activated`, `theme`, `available_space`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 1, ' ', NULL, 1, 0, 20);

-- --------------------------------------------------------

--
-- Table structure for table `user_activation`
--

CREATE TABLE `user_activation` (
  `id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `activ_code` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `libraries`
--
ALTER TABLE `libraries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `library_updates`
--
ALTER TABLE `library_updates`
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
-- Indexes for table `user_activation`
--
ALTER TABLE `user_activation`
  ADD PRIMARY KEY (`id`);
  
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
