-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 22/10/2025 às 01:13
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `cql_ifpe1`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `alertas`
--

CREATE TABLE `alertas` (
  `id_alerta` int(11) NOT NULL,
  `id_vaca` int(11) DEFAULT NULL,
  `mensagem` varchar(255) DEFAULT NULL,
  `lido` tinyint(1) NOT NULL DEFAULT 0,
  `data_criacao` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `alertas`
--

INSERT INTO `alertas` (`id_alerta`, `id_vaca`, `mensagem`, `lido`, `data_criacao`) VALUES
(0, 1, 'Alerta: Teste de mastite POSITIVO após um NEGATIVO.', 0, '2025-10-20 09:41:36'),
(0, 4, 'Alerta: Teste de mastite POSITIVO após um NEGATIVO.', 0, '2025-10-20 09:41:36');

-- --------------------------------------------------------

--
-- Estrutura para tabela `lote_leite`
--

CREATE TABLE `lote_leite` (
  `id_lote` int(11) NOT NULL,
  `data_lote` date NOT NULL,
  `quantidade_total` decimal(5,2) NOT NULL,
  `id_tanque` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `lote_manejo`
--

CREATE TABLE `lote_manejo` (
  `id_lote_manejo` int(11) NOT NULL,
  `nome_lote` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `producao_leite`
--

CREATE TABLE `producao_leite` (
  `id_producao` int(11) NOT NULL,
  `id_vaca` int(11) NOT NULL,
  `quantidade` decimal(5,2) NOT NULL,
  `data` date NOT NULL,
  `id_tanque` int(11) DEFAULT NULL,
  `batched` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `producao_leite`
--

INSERT INTO `producao_leite` (`id_producao`, `id_vaca`, `quantidade`, `data`, `id_tanque`, `batched`) VALUES
(0, 2, 12.00, '2025-10-21', 2, 0),
(1, 1, 7.00, '2025-02-10', NULL, 0),
(2, 3, 8.00, '2025-02-10', NULL, 0),
(3, 6, 8.00, '2025-02-10', NULL, 0),
(4, 4, 8.00, '2025-02-10', NULL, 0),
(5, 7, 10.00, '2025-02-10', NULL, 0),
(6, 1, 9.00, '2025-02-11', NULL, 0),
(7, 2, 6.00, '2025-02-11', NULL, 0),
(8, 6, 11.00, '2025-02-11', NULL, 0),
(9, 4, 7.00, '2025-02-11', NULL, 0),
(10, 7, 12.00, '2025-02-11', NULL, 0),
(11, 1, 8.00, '2025-02-12', NULL, 0),
(12, 2, 6.00, '2025-02-12', NULL, 0),
(13, 6, 10.00, '2025-02-12', NULL, 0),
(14, 4, 9.00, '2025-02-12', NULL, 0),
(15, 7, 11.00, '2025-02-12', NULL, 0),
(16, 1, 7.00, '2025-02-13', NULL, 0),
(17, 2, 9.00, '2025-02-13', NULL, 0),
(18, 6, 12.00, '2025-02-13', NULL, 0),
(19, 4, 6.00, '2025-02-13', NULL, 0),
(20, 7, 13.00, '2025-02-13', NULL, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `producao_lote`
--

CREATE TABLE `producao_lote` (
  `id_lote` int(11) NOT NULL,
  `id_producao` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `recuperacao_senha`
--

CREATE TABLE `recuperacao_senha` (
  `id_recuperação` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `data_expiracao` datetime NOT NULL,
  `usado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `relatorios`
--

CREATE TABLE `relatorios` (
  `id_relatorio` int(11) NOT NULL,
  `id_aluno` int(11) NOT NULL,
  `nome_arquivo` varchar(255) NOT NULL,
  `caminho_arquivo` varchar(255) NOT NULL,
  `data_upload` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tanque`
--

CREATE TABLE `tanque` (
  `id_tanque` int(11) NOT NULL,
  `localizacao` varchar(100) NOT NULL,
  `capacidade` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tanque`
--

INSERT INTO `tanque` (`id_tanque`, `localizacao`, `capacidade`) VALUES
(1, 'Tanque Principal - Leste', 5000.00),
(2, 'Tanque Auxiliar - Oeste', 2500.00),
(3, 'Tanque de Qualidade - Laboratório', 1000.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `teste_mastite`
--

CREATE TABLE `teste_mastite` (
  `id_teste` int(11) NOT NULL,
  `id_vaca` int(11) NOT NULL,
  `data` date NOT NULL,
  `resultado` enum('positivo','negativo') NOT NULL,
  `quantas_cruzes` tinyint(3) NOT NULL DEFAULT 0,
  `ubere` varchar(50) DEFAULT NULL,
  `tratamento` varchar(100) DEFAULT NULL,
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `teste_mastite`
--

INSERT INTO `teste_mastite` (`id_teste`, `id_vaca`, `data`, `resultado`, `quantas_cruzes`, `ubere`, `tratamento`, `observacoes`) VALUES
(1, 1, '2025-03-11', 'positivo', 1, 'P.E', 'Nenhum', ''),
(2, 2, '2025-03-11', 'negativo', 0, 'Especificado', 'Nenhum', ''),
(3, 3, '2025-03-11', 'positivo', 3, 'P.D P.E', 'Nenhum', ''),
(4, 4, '2025-03-11', 'negativo', 0, 'Especificado', 'Nenhum', ''),
(5, 5, '2025-03-11', 'negativo', 0, 'Especificado', 'Nenhum', ''),
(6, 6, '2025-03-11', 'negativo', 0, 'Especificado', 'Nenhum', ''),
(7, 7, '2025-03-11', 'negativo', 0, 'Especificado', 'Nenhum', ''),
(8, 1, '2025-03-14', 'negativo', 0, 'Especificado', 'Nenhum', ''),
(9, 2, '2025-03-14', 'negativo', 0, 'Especificado', 'Nenhum', ''),
(10, 3, '2025-03-14', 'positivo', 2, 'P.D A.D', 'Nenhum', ''),
(11, 4, '2025-03-14', 'negativo', 0, 'Especificado', 'Nenhum', ''),
(12, 5, '2025-03-14', 'negativo', 0, 'Especificado', 'Nenhum', ''),
(13, 6, '2025-03-14', 'negativo', 0, 'Especificado', 'Nenhum', ''),
(14, 7, '2025-03-14', 'negativo', 0, 'Especificado', 'Nenhum', ''),
(15, 1, '2025-03-18', 'positivo', 2, 'P.E A.D', 'Nenhum', ''),
(16, 2, '2025-03-18', 'negativo', 0, 'Especificado', 'Nenhum', ''),
(17, 3, '2025-03-18', 'negativo', 0, 'Especificado', 'Nenhum', ''),
(18, 4, '2025-03-18', 'positivo', 2, 'A.D P.D', 'Nenhum', ''),
(19, 5, '2025-03-18', 'negativo', 0, 'Especificado', 'Nenhum', ''),
(20, 6, '2025-03-18', 'negativo', 0, 'Especificado', 'Nenhum', ''),
(21, 7, '2025-03-18', 'negativo', 0, 'Especificado', 'Nenhum', ''),
(22, 1, '2025-03-25', 'positivo', 4, 'A.D P.D P.E A.E', 'Nenhum', ''),
(23, 2, '2025-03-25', 'negativo', 0, 'Especificado', 'Nenhum', ''),
(24, 3, '2025-03-25', 'negativo', 0, 'Especificado', 'Nenhum', ''),
(25, 4, '2025-03-25', 'negativo', 0, 'Especificado', 'Nenhum', ''),
(26, 5, '2025-03-25', 'negativo', 0, 'Especificado', 'Nenhum', ''),
(27, 6, '2025-03-25', 'negativo', 0, 'Especificado', 'Nenhum', ''),
(28, 7, '2025-03-25', 'negativo', 0, 'Especificado', 'Nenhum', '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo_usuario` enum('aluno','professor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nome`, `email`, `senha`, `tipo_usuario`) VALUES
(1, 'Cecília Helena', 'chsna@discente.ifpe.edu.br', '$2y$10$mVTmG2AISbQogZzbUjGRdO9Kh0j6P9NLTnja1xdZ6Iz37GfKw/4yq', 'aluno'),
(2, 'Isabela de França', 'ifl1@discente.ifpe.edu.br', '$2y$10$rkHPA4T1wncPzKEQlGBOneNENEJqyrmBEI03UUoZomR91NERHcH4.', 'aluno'),
(3, 'Vitória Melo', 'mvms4@discente.ifpe.edu.br', '$2y$10$WVPO7UaXCczfAODdvCt8m.i.OiylHGabbSHAYqzDXtYcLSaafw1mO', 'professor'),
(4, 'Alexia Alves', 'ajdsa@discente.ifpe.edu.br', '$2y$10$NimEkKa5fliewp/OpZcgPeSjQUAAjh/o/fYZGLPZV8C85EyggCDCS', 'aluno');

-- --------------------------------------------------------

--
-- Estrutura para tabela `vacas`
--

CREATE TABLE `vacas` (
  `id_vaca` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descarte` tinyint(1) NOT NULL DEFAULT 0,
  `id_lote_manejo` int(11) DEFAULT NULL,
  `health_score` decimal(5,2) DEFAULT 100.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `vacas`
--

INSERT INTO `vacas` (`id_vaca`, `nome`, `descarte`, `id_lote_manejo`, `health_score`) VALUES
(1, 'Alicate', 0, NULL, 0.00),
(2, 'Chuvisco', 0, NULL, 100.00),
(3, 'Chichita', 0, NULL, 0.00),
(4, 'Muriçoca', 0, NULL, 0.00),
(5, 'Morena', 0, NULL, 100.00),
(6, 'Mococa', 0, NULL, 0.00),
(7, 'Tanajura', 0, NULL, 0.00);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `lote_leite`
--
ALTER TABLE `lote_leite`
  ADD PRIMARY KEY (`id_lote`),
  ADD KEY `id_tanque` (`id_tanque`);

--
-- Índices de tabela `lote_manejo`
--
ALTER TABLE `lote_manejo`
  ADD PRIMARY KEY (`id_lote_manejo`);

--
-- Índices de tabela `producao_leite`
--
ALTER TABLE `producao_leite`
  ADD PRIMARY KEY (`id_producao`),
  ADD KEY `fk_producao_tanque` (`id_tanque`);

--
-- Índices de tabela `producao_lote`
--
ALTER TABLE `producao_lote`
  ADD PRIMARY KEY (`id_lote`,`id_producao`),
  ADD KEY `id_producao` (`id_producao`);

--
-- Índices de tabela `tanque`
--
ALTER TABLE `tanque`
  ADD PRIMARY KEY (`id_tanque`);

--
-- Índices de tabela `vacas`
--
ALTER TABLE `vacas`
  ADD KEY `fk_vaca_lote` (`id_lote_manejo`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `lote_leite`
--
ALTER TABLE `lote_leite`
  MODIFY `id_lote` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `lote_manejo`
--
ALTER TABLE `lote_manejo`
  MODIFY `id_lote_manejo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tanque`
--
ALTER TABLE `tanque`
  MODIFY `id_tanque` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `lote_leite`
--
ALTER TABLE `lote_leite`
  ADD CONSTRAINT `lote_leite_ibfk_1` FOREIGN KEY (`id_tanque`) REFERENCES `tanque` (`id_tanque`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `producao_leite`
--
ALTER TABLE `producao_leite`
  ADD CONSTRAINT `fk_producao_tanque` FOREIGN KEY (`id_tanque`) REFERENCES `tanque` (`id_tanque`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `producao_lote`
--
ALTER TABLE `producao_lote`
  ADD CONSTRAINT `producao_lote_ibfk_1` FOREIGN KEY (`id_lote`) REFERENCES `lote_leite` (`id_lote`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `producao_lote_ibfk_2` FOREIGN KEY (`id_producao`) REFERENCES `producao_leite` (`id_producao`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `vacas`
--
ALTER TABLE `vacas`
  ADD CONSTRAINT `fk_vaca_lote` FOREIGN KEY (`id_lote_manejo`) REFERENCES `lote_manejo` (`id_lote_manejo`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
