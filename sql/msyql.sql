-- phpMyAdmin SQL Dump
-- version 4.4.13.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 05, 2016 at 04:17 PM
-- Server version: 5.6.27-0ubuntu1
-- PHP Version: 5.6.11-1ubuntu3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zend`
--

-- --------------------------------------------------------

--
-- Table structure for table `zf_clientes`
--

CREATE TABLE IF NOT EXISTS `zf_clientes` (
  `id` int(11) unsigned NOT NULL,
  `cpf` varchar(255) NOT NULL,
  `dados_cliente` text NOT NULL,
  `created_user_id` int(11) NOT NULL,
  `last_user_id` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  `locked_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `zf_perfis`
--

CREATE TABLE IF NOT EXISTS `zf_perfis` (
  `id` int(11) NOT NULL,
  `role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `zf_propostas`
--

CREATE TABLE IF NOT EXISTS `zf_propostas` (
  `id` int(11) unsigned NOT NULL,
  `id_cliente` int(11) unsigned NOT NULL,
  `anexos` text NOT NULL,
  `state` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '2',
  `descricao` text NOT NULL,
  `dados_extras` text NOT NULL,
  `locked` tinyint(1) DEFAULT '0',
  `locked_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `zf_propostas_valores`
--

CREATE TABLE IF NOT EXISTS `zf_propostas_valores` (
  `id` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `parcelas` text NOT NULL,
  `sinal` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `zf_usuarios`
--

CREATE TABLE IF NOT EXISTS `zf_usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `salt` varchar(255) NOT NULL,
  `acesso` tinyint(1) NOT NULL DEFAULT '0',
  `id_perfil` int(2) NOT NULL DEFAULT '1',
  `state` tinyint(1) NOT NULL DEFAULT '1',
  `parent_id` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `zf_clientes`
--
ALTER TABLE `zf_clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `zf_perfis`
--
ALTER TABLE `zf_perfis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `zf_propostas`
--
ALTER TABLE `zf_propostas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_zf_propostas_1` (`id_cliente`);

--
-- Indexes for table `zf_propostas_valores`
--
ALTER TABLE `zf_propostas_valores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `zf_usuarios`
--
ALTER TABLE `zf_usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `zf_clientes`
--
ALTER TABLE `zf_clientes`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `zf_perfis`
--
ALTER TABLE `zf_perfis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `zf_propostas`
--
ALTER TABLE `zf_propostas`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `zf_propostas_valores`
--
ALTER TABLE `zf_propostas_valores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `zf_usuarios`
--
ALTER TABLE `zf_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `zf_propostas`
--
ALTER TABLE `zf_propostas`
  ADD CONSTRAINT `fk_zf_propostas_1` FOREIGN KEY (`id_cliente`) REFERENCES `zf_clientes` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
