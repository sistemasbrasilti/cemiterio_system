-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql212.byetcluster.com
-- Tempo de geração: 12/02/2026 às 13:03
-- Versão do servidor: 11.4.10-MariaDB
-- Versão do PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `if0_41133984_cemiterio_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `cemeteries`
--

CREATE TABLE `cemeteries` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `lat` decimal(10,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cemeteries`
--

INSERT INTO `cemeteries` (`id`, `nome`, `endereco`, `cidade`, `created_at`, `lat`, `lng`) VALUES
(1, 'Cemitério Jardim da Paz', 'Cemitério Jardim da Paz - R. Reilly Duarte, S/N - Civit II, Serra - ES, 29165-680', 'Serra', '2026-02-09 17:15:16', NULL, NULL),
(2, 'Cemitério Serra Sede', 'R. do Cemitério, 2-150 - Santo Antonio, Serra - ES, 29178-681', 'Serra', '2026-02-09 17:46:44', NULL, NULL),
(3, 'Cemitério Parque da Serra', 'Av. Tal, 100 - Centro, Serra - ES', 'Serra', '2026-02-10 18:49:37', '-20.12345600', '-40.12345600'),
(4, 'Cemitério Municipal de Vila Velha', 'Rua Principal, S/N - Centro, Vila Velha - ES', 'Vila Velha', '2026-02-10 18:49:37', '-20.32900000', '-40.29200000');

-- --------------------------------------------------------

--
-- Estrutura para tabela `deceased`
--

CREATE TABLE `deceased` (
  `id` int(11) NOT NULL,
  `grave_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `data_nascimento` date DEFAULT NULL,
  `data_falecimento` date DEFAULT NULL,
  `data_sepultamento` date NOT NULL,
  `cpf` varchar(20) NOT NULL,
  `tel` varchar(50) NOT NULL,
  `cns` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `deceased`
--

INSERT INTO `deceased` (`id`, `grave_id`, `nome`, `data_nascimento`, `data_falecimento`, `data_sepultamento`, `cpf`, `tel`, `cns`, `created_at`) VALUES
(18, 12, 'Pedro Silva', '1987-03-17', '2021-09-23', '2021-09-24', '133.234.565-34', '27 99878-0987', '888888888', '2026-02-11 17:32:10'),
(20, 13, 'José alves', '1992-04-11', '2019-08-28', '2019-08-28', '142.563.779-08', '28 00000-0000', '0202939302828383', '2026-02-11 21:14:40');

-- --------------------------------------------------------

--
-- Estrutura para tabela `graves`
--

CREATE TABLE `graves` (
  `id` int(11) NOT NULL,
  `cemiterio_id` int(11) NOT NULL,
  `numero` varchar(50) NOT NULL,
  `tipo` varchar(50) DEFAULT 'NOT NULL',
  `capacidade_total` int(11) DEFAULT 1,
  `perpetuo` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `graves`
--

INSERT INTO `graves` (`id`, `cemiterio_id`, `numero`, `tipo`, `capacidade_total`, `perpetuo`, `created_at`) VALUES
(12, 1, 'A-2', 'Vertical', 5, 'Não', '2026-02-11 17:30:07'),
(13, 1, 'B-2', 'Horizontal', 4, 'Não', '2026-02-11 21:10:15');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `nome`, `email`, `senha`, `created_at`) VALUES
(2, 'Administrador', 'admin@gmail.com', '$2y$10$IJm5nswOgoTX.JK7s.M/6.8CYZanYcoDQHIOCiBEVJtP245X0f5oC', '2026-02-10 15:54:28');

--
-- Índices de tabelas apagadas
--

--
-- Índices de tabela `cemeteries`
--
ALTER TABLE `cemeteries`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `deceased`
--
ALTER TABLE `deceased`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grave_id` (`grave_id`);

--
-- Índices de tabela `graves`
--
ALTER TABLE `graves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cemiterio_id` (`cemiterio_id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de tabelas apagadas
--

--
-- AUTO_INCREMENT de tabela `cemeteries`
--
ALTER TABLE `cemeteries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `deceased`
--
ALTER TABLE `deceased`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `graves`
--
ALTER TABLE `graves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para dumps de tabelas
--

--
-- Restrições para tabelas `deceased`
--
ALTER TABLE `deceased`
  ADD CONSTRAINT `deceased_ibfk_1` FOREIGN KEY (`grave_id`) REFERENCES `graves` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `graves`
--
ALTER TABLE `graves`
  ADD CONSTRAINT `graves_ibfk_1` FOREIGN KEY (`cemiterio_id`) REFERENCES `cemeteries` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
