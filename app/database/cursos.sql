-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 04-Nov-2020 às 14:11
-- Versão do servidor: 10.1.37-MariaDB
-- versão do PHP: 7.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cursos`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `aluno`
--

CREATE TABLE `aluno` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(50) DEFAULT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `nivel` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `aluno`
--

INSERT INTO `aluno` (`id`, `nome`, `email`, `senha`, `celular`, `nivel`) VALUES
(1, 'francisco wanderly', 'wanderley3101@gmail.com', 'sobral123', '(88) 996935721', 1),
(2, 'João da Silva Gomes', 'joaodasilva@gmail.com', 'joao1234', '(88)99448-2162', 0),
(5, 'hamilton milton', 'jhamarin@gmail.com', 'teste123', '(11)99449-4321', 0),
(8, 'Washington Ribeiro', 'santoswash@hotmail.com', NULL, NULL, 0),
(9, 'Silvia Cristina', 'silviaconsultoria.rh@gmail.com', NULL, NULL, 0),
(10, 'Yane', 'psicologa.yane@gmail.com', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `cursos`
--

CREATE TABLE `cursos` (
  `id` int(11) NOT NULL,
  `nomedocurso` varchar(100) NOT NULL,
  `professor` varchar(100) NOT NULL,
  `cargahoraria` int(10) UNSIGNED NOT NULL,
  `fotodocurso` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `cursos`
--

INSERT INTO `cursos` (`id`, `nomedocurso`, `professor`, `cargahoraria`, `fotodocurso`) VALUES
(1, 'Desenvolvimento de aplicativos mobile', 'Francisco Wanderley', 120, 'app/images/cursos-example.jpg'),
(2, 'desenvolvimento para com nodejs + reactjs', 'Lucas Gonçalves', 120, 'app/images/curso-nodejs.jpg'),
(3, 'curso de Excel avançado', 'Antônio Carlos ', 70, 'app/images/excel.jpg'),
(7, 'Como construir aplicações web com react native', 'Antônio Carlos Silva', 120, 'app/images//reactnative.jpg'),
(47, 'Logica de Programação', 'Carlos Nougué', 120, 'app/images/47/logica.jpg'),
(48, 'marketing digital', 'Henrique Cardoso', 100, 'app/images/48/marketing.jpg');

-- --------------------------------------------------------

--
-- Estrutura da tabela `curso_aluno`
--

CREATE TABLE `curso_aluno` (
  `id` int(11) NOT NULL,
  `idcursos` int(11) NOT NULL,
  `idaluno` int(11) NOT NULL,
  `comeco` varchar(20) DEFAULT NULL,
  `fim` varchar(20) DEFAULT NULL,
  `situacao` varchar(20) NOT NULL DEFAULT 'Concluido',
  `codigo` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `curso_aluno`
--

INSERT INTO `curso_aluno` (`id`, `idcursos`, `idaluno`, `comeco`, `fim`, `situacao`, `codigo`) VALUES
(1, 1, 1, '21/03/2020', '22/05/2020', 'verificado', 268611),
(2, 2, 1, '30/05/2020', '15/08/2020', 'verificado', 611112),
(6, 3, 1, '15/08/2020', '1/10/2020', 'verificado', 140713),
(7, 3, 2, ' 20/09/2020', ' 25/10/2020', 'verificado', 511923),
(8, 3, 8, ' 20/07/2020', ' 25/08/2020', ' Concluido', 0),
(9, 3, 9, ' 20/07/2019', ' 10/01/2020', ' Concluido', 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `palestrante` varchar(100) NOT NULL,
  `foto` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `eventos`
--

INSERT INTO `eventos` (`id`, `nome`, `palestrante`, `foto`) VALUES
(1, 'IX evento de desenvolvimento mobile', 'Professor Sergio Teixeira', 'app/images/eventomobile.jpg'),
(4, 'Evento de PHP moderno', 'Ricardo Gonçalves', 'app/images/4/eventodephp.jpg'),
(8, 'II Evento de Rest APi', 'Diego Fernandes', 'app/images/8/restapi.jpg');

-- --------------------------------------------------------

--
-- Estrutura da tabela `eventos_aluno`
--

CREATE TABLE `eventos_aluno` (
  `id` int(11) NOT NULL,
  `idevento` int(11) NOT NULL,
  `idaluno` int(11) NOT NULL,
  `comeco` varchar(20) DEFAULT NULL,
  `fim` varchar(20) DEFAULT NULL,
  `situacao` varchar(20) DEFAULT NULL,
  `codigo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `eventos_aluno`
--

INSERT INTO `eventos_aluno` (`id`, `idevento`, `idaluno`, `comeco`, `fim`, `situacao`, `codigo`) VALUES
(1, 1, 1, '21/03/2020', '22/05/2020', 'verificado', 43701),
(2, 4, 1, '15/03/2020', '20/03/2020', 'Concluido', 0),
(4, 1, 2, ' 20/09/2020', ' 25/10/2020', ' Concluido', NULL),
(6, 4, 2, ' 20/09/2020', ' 25/10/2020', ' Concluido', 42462),
(7, 4, 10, ' 20/09/2020', ' 25/10/2020', ' Concluido', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `palavra_chave`
--

CREATE TABLE `palavra_chave` (
  `id` int(11) NOT NULL,
  `chave` varchar(50) NOT NULL,
  `idcursos` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `palavra_chave`
--

INSERT INTO `palavra_chave` (`id`, `chave`, `idcursos`) VALUES
(1, 'Android Studio', 1),
(2, 'Object-C', 1),
(3, 'Excel', 3),
(4, 'Planilha', 3),
(5, 'nodejs', 2),
(6, ' paradigmas de programação', 47),
(7, ' if e else', 47),
(8, ' laço de repeticão', 47),
(9, ' landing page', 48),
(10, ' SEO', 48),
(11, ' redes sociais', 48);

-- --------------------------------------------------------

--
-- Estrutura da tabela `palavra_chave_eventos`
--

CREATE TABLE `palavra_chave_eventos` (
  `id` int(11) NOT NULL,
  `chave` varchar(50) NOT NULL,
  `ideventos` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `palavra_chave_eventos`
--

INSERT INTO `palavra_chave_eventos` (`id`, `chave`, `ideventos`) VALUES
(2, 'Android Studio', 1),
(3, 'PHP Nova Atualização', 4),
(4, ' JSON', 8),
(5, 'JWT_token', 8);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aluno`
--
ALTER TABLE `aluno`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `senha` (`senha`);

--
-- Indexes for table `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `curso_aluno`
--
ALTER TABLE `curso_aluno`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `eventos_aluno`
--
ALTER TABLE `eventos_aluno`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `palavra_chave`
--
ALTER TABLE `palavra_chave`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `palavra_chave_eventos`
--
ALTER TABLE `palavra_chave_eventos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aluno`
--
ALTER TABLE `aluno`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `curso_aluno`
--
ALTER TABLE `curso_aluno`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `eventos_aluno`
--
ALTER TABLE `eventos_aluno`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `palavra_chave`
--
ALTER TABLE `palavra_chave`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `palavra_chave_eventos`
--
ALTER TABLE `palavra_chave_eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
