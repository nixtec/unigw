-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3314
-- Generation Time: Jul 06, 2021 at 05:44 PM
-- Server version: 10.2.22-MariaDB
-- PHP Version: 7.4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `unigw_dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `ext_ep_config`
--

CREATE TABLE `ext_ep_config` (
  `id` int(11) NOT NULL,
  `ep_id` int(11) NOT NULL,
  `ep_baseurl` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `need_auth` tinyint(4) NOT NULL DEFAULT 0,
  `req_proto` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'http',
  `req_method` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
  `req_datatype` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'json',
  `resp_datatype` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'json',
  `conn_timeout_msec` int(11) NOT NULL DEFAULT 10000,
  `resp_timeout_msec` int(11) NOT NULL DEFAULT 60000,
  `retry_after_conn_timeout` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'If nonzero, it means that many times to retry until successful',
  `wait_before_retry_msec` int(11) NOT NULL DEFAULT 5000 COMMENT 'Wait/sleep this miliseconds before the retry (if retry is enabled, nonzero)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ext_ep_config`
--

INSERT INTO `ext_ep_config` (`id`, `ep_id`, `ep_baseurl`, `need_auth`, `req_proto`, `req_method`, `req_datatype`, `resp_datatype`, `conn_timeout_msec`, `resp_timeout_msec`, `retry_after_conn_timeout`, `wait_before_retry_msec`) VALUES
(1, 1, 'https://api.dev.name.com/v4', 1, 'http/1.1', 'post', 'json', 'json', 10000, 60000, 0, 5000);

-- --------------------------------------------------------

--
-- Table structure for table `ext_ep_func`
--

CREATE TABLE `ext_ep_func` (
  `id` int(11) NOT NULL,
  `ep_id` int(11) NOT NULL,
  `func_id` int(11) NOT NULL,
  `func_name_ns` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `func_name_ep` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `has_args` tinyint(4) NOT NULL DEFAULT 0,
  `has_headers` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ext_ep_func`
--

INSERT INTO `ext_ep_func` (`id`, `ep_id`, `func_id`, `func_name_ns`, `func_name_ep`, `has_args`, `has_headers`) VALUES
(1, 1, 1, 'noop', 'hello', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ext_ep_func_arg`
--

CREATE TABLE `ext_ep_func_arg` (
  `id` int(11) NOT NULL,
  `func_id` int(11) NOT NULL,
  `arg_key` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `arg_value` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ext_ep_func_header`
--

CREATE TABLE `ext_ep_func_header` (
  `id` int(11) NOT NULL,
  `func_id` int(11) NOT NULL,
  `header_key` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `header_value` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ls_auth_arg`
--

CREATE TABLE `ls_auth_arg` (
  `id` int(11) NOT NULL,
  `auth_id` int(11) NOT NULL DEFAULT 0,
  `auth_key` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auth_value` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auth_desc` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ls_auth_arg`
--

INSERT INTO `ls_auth_arg` (`id`, `auth_id`, `auth_key`, `auth_value`, `auth_desc`) VALUES
(1, 1, 'username', 'nixtec.systems-test', 'Authentication Username'),
(2, 1, 'password', '2f95a2ca5fb473595c3d472b0878ed719e2e0023', 'Authentication Password (Token)'),
(3, 1, '__cfg_auth_type', 'http_auth_basic', '');

-- --------------------------------------------------------

--
-- Table structure for table `ls_ep`
--

CREATE TABLE `ls_ep` (
  `id` int(11) NOT NULL,
  `ep_id` int(11) NOT NULL,
  `ep_keyword` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ep_desc` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ep_type` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ls_ep`
--

INSERT INTO `ls_ep` (`id`, `ep_id`, `ep_keyword`, `ep_desc`, `ep_type`) VALUES
(1, 1, 'namecom.dev', 'name.com domain Registrar', 1),
(2, 2, 'namecheap', 'namecheap.com Domain Registrar', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ls_ep_auth`
--

CREATE TABLE `ls_ep_auth` (
  `id` int(11) NOT NULL,
  `ep_id` int(11) NOT NULL,
  `auth_id` int(11) NOT NULL,
  `auth_keyword` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auth_desc` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ls_ep_auth`
--

INSERT INTO `ls_ep_auth` (`id`, `ep_id`, `auth_id`, `auth_keyword`, `auth_desc`) VALUES
(1, 1, 1, 'nixtec.namecom1', 'Name.com account 1 for Nixtec Systems');

-- --------------------------------------------------------

--
-- Table structure for table `ls_ns`
--

CREATE TABLE `ls_ns` (
  `id` int(11) NOT NULL,
  `ns_id` int(11) NOT NULL,
  `ns_keyword` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ns_desc` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ls_ns`
--

INSERT INTO `ls_ns` (`id`, `ns_id`, `ns_keyword`, `ns_desc`) VALUES
(1, 1, 'default', 'Default Namespace'),
(2, 2, 'domain', 'Domain registrars'),
(3, 3, 'sms', 'Short Message Service');

-- --------------------------------------------------------

--
-- Table structure for table `ls_ns_ep`
--

CREATE TABLE `ls_ns_ep` (
  `id` int(11) NOT NULL,
  `ns_id` int(11) NOT NULL,
  `ep_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ls_ns_ep`
--

INSERT INTO `ls_ns_ep` (`id`, `ns_id`, `ep_id`) VALUES
(1, 2, 1),
(2, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `ls_ns_func`
--

CREATE TABLE `ls_ns_func` (
  `id` int(11) NOT NULL,
  `ns_id` int(11) NOT NULL DEFAULT 1,
  `ep_id` int(11) NOT NULL,
  `func_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ls_ns_func`
--

INSERT INTO `ls_ns_func` (`id`, `ns_id`, `ep_id`, `func_name`) VALUES
(1, 2, 1, 'noop'),
(2, 1, 0, 'noop');

-- --------------------------------------------------------

--
-- Table structure for table `ls_type_ep`
--

CREATE TABLE `ls_type_ep` (
  `id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `type_keyword` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_desc` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ep_tbl_prefix` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ls_type_ep`
--

INSERT INTO `ls_type_ep` (`id`, `type_id`, `type_keyword`, `type_desc`, `ep_tbl_prefix`) VALUES
(1, 1, 'external', 'External Service Endpoint', 'ext_'),
(2, 2, 'internal', 'Internal Service Endpoint', 'int_');

-- --------------------------------------------------------

--
-- Table structure for table `ls_vauth`
--

CREATE TABLE `ls_vauth` (
  `id` int(11) NOT NULL,
  `vauth_id` int(11) NOT NULL,
  `vauth_keyword` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vauth_desc` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ls_vauth`
--

INSERT INTO `ls_vauth` (`id`, `vauth_id`, `vauth_keyword`, `vauth_desc`) VALUES
(1, 1, 'nixtec', 'Nixtec Systems Virtual Authentication');

-- --------------------------------------------------------

--
-- Table structure for table `ls_vauth_ep`
--

CREATE TABLE `ls_vauth_ep` (
  `id` int(11) NOT NULL,
  `vauth_id` int(11) NOT NULL DEFAULT 0,
  `ep_id` int(11) NOT NULL,
  `auth_id` int(11) NOT NULL DEFAULT 0,
  `auth_desc` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ls_vauth_ep`
--

INSERT INTO `ls_vauth_ep` (`id`, `vauth_id`, `ep_id`, `auth_id`, `auth_desc`) VALUES
(1, 1, 1, 1, 'Name.com Nixtec Systems Account');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ext_ep_config`
--
ALTER TABLE `ext_ep_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ext_ep_func`
--
ALTER TABLE `ext_ep_func`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ext_ep_func_arg`
--
ALTER TABLE `ext_ep_func_arg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ext_ep_func_header`
--
ALTER TABLE `ext_ep_func_header`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ls_auth_arg`
--
ALTER TABLE `ls_auth_arg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ls_ep`
--
ALTER TABLE `ls_ep`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ls_ep_auth`
--
ALTER TABLE `ls_ep_auth`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ls_ns`
--
ALTER TABLE `ls_ns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ls_ns_ep`
--
ALTER TABLE `ls_ns_ep`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ls_ns_func`
--
ALTER TABLE `ls_ns_func`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ls_type_ep`
--
ALTER TABLE `ls_type_ep`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ls_vauth`
--
ALTER TABLE `ls_vauth`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ls_vauth_ep`
--
ALTER TABLE `ls_vauth_ep`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ext_ep_config`
--
ALTER TABLE `ext_ep_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ext_ep_func`
--
ALTER TABLE `ext_ep_func`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ext_ep_func_arg`
--
ALTER TABLE `ext_ep_func_arg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ext_ep_func_header`
--
ALTER TABLE `ext_ep_func_header`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ls_auth_arg`
--
ALTER TABLE `ls_auth_arg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ls_ep`
--
ALTER TABLE `ls_ep`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ls_ep_auth`
--
ALTER TABLE `ls_ep_auth`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ls_ns`
--
ALTER TABLE `ls_ns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ls_ns_ep`
--
ALTER TABLE `ls_ns_ep`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ls_ns_func`
--
ALTER TABLE `ls_ns_func`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ls_type_ep`
--
ALTER TABLE `ls_type_ep`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ls_vauth`
--
ALTER TABLE `ls_vauth`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ls_vauth_ep`
--
ALTER TABLE `ls_vauth_ep`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
