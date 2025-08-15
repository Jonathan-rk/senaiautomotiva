-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 17/07/2025 às 21:07
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
-- Banco de dados: `senai_automotivo`
--
CREATE DATABASE IF NOT EXISTS `senai_automotivo` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `senai_automotivo`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `carrocerias`
--

DROP TABLE IF EXISTS `carrocerias`;
CREATE TABLE `carrocerias` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `carrocerias`
--

INSERT INTO `carrocerias` (`id`, `nome`) VALUES
(1, 'Coupé'),
(2, 'Hatchback'),
(3, 'Mini carro'),
(8, 'Minivan'),
(4, 'Monovolume'),
(5, 'Picape'),
(6, 'Sedan'),
(7, 'SUV'),
(9, 'SUV compacto');

-- --------------------------------------------------------

--
-- Estrutura para tabela `fichas_tecnicas`
--

DROP TABLE IF EXISTS `fichas_tecnicas`;
CREATE TABLE `fichas_tecnicas` (
  `id` int(11) NOT NULL,
  `montadoras_id` int(11) NOT NULL,
  `modelo` varchar(255) NOT NULL,
  `ano` int(11) NOT NULL,
  `lugares` varchar(255) NOT NULL,
  `portas` varchar(255) NOT NULL,
  `identificacaomotor` varchar(255) DEFAULT NULL,
  `materialconstrucao` varchar(255) DEFAULT NULL,
  `instalacao` varchar(255) DEFAULT NULL,
  `disposicao` varchar(255) DEFAULT NULL,
  `combustivel` varchar(255) DEFAULT NULL,
  `cilindros` varchar(255) DEFAULT NULL,
  `valvulasporcilindro` varchar(255) DEFAULT NULL,
  `aspiracao` varchar(255) DEFAULT NULL,
  `alimentacao` varchar(255) DEFAULT NULL,
  `potencia` varchar(255) DEFAULT NULL,
  `cilindrada` varchar(255) DEFAULT NULL,
  `torque` varchar(255) DEFAULT NULL,
  `rotacao` varchar(255) DEFAULT NULL,
  `tracao` varchar(255) DEFAULT NULL,
  `cambio` varchar(255) DEFAULT NULL,
  `numero_marchas` varchar(255) DEFAULT NULL,
  `embreagem` varchar(255) DEFAULT NULL,
  `dianteira` varchar(255) DEFAULT NULL,
  `traseira` varchar(255) DEFAULT NULL,
  `dianteirosfreios` varchar(255) DEFAULT NULL,
  `traseirosfreios` varchar(255) DEFAULT NULL,
  `assistencia` varchar(255) DEFAULT NULL,
  `dianteira_pressao_enchimento` varchar(255) DEFAULT NULL,
  `traseira_pressao_enchimento` varchar(255) DEFAULT NULL,
  `dimensao_estepe` varchar(255) DEFAULT NULL,
  `material_rodas` varchar(255) DEFAULT NULL,
  `comprimento` varchar(255) DEFAULT NULL,
  `distancia_eixos` varchar(255) DEFAULT NULL,
  `largura` varchar(255) DEFAULT NULL,
  `altura` varchar(255) DEFAULT NULL,
  `peso_bruto` varchar(255) DEFAULT NULL,
  `porta_malas` varchar(255) DEFAULT NULL,
  `velocidade_maxima` varchar(255) DEFAULT NULL,
  `aceleracao` varchar(255) DEFAULT NULL,
  `capacidade_tanque` varchar(255) DEFAULT NULL,
  `consumo_urbano` varchar(255) DEFAULT NULL,
  `consumo_rodovia` varchar(255) DEFAULT NULL,
  `autonomia_urbana` varchar(255) DEFAULT NULL,
  `autonomia_rodovia` varchar(255) DEFAULT NULL,
  `oleo_motor` varchar(255) DEFAULT NULL,
  `oleo_transmissao` varchar(255) DEFAULT NULL,
  `fluido_freio` varchar(255) DEFAULT NULL,
  `carroceria_id` int(11) DEFAULT NULL,
  `imagem_path` longblob DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `versao` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `fichas_tecnicas`
--

INSERT INTO `fichas_tecnicas` (`id`, `montadoras_id`, `modelo`, `ano`, `lugares`, `portas`, `identificacaomotor`, `materialconstrucao`, `instalacao`, `disposicao`, `combustivel`, `cilindros`, `valvulasporcilindro`, `aspiracao`, `alimentacao`, `potencia`, `cilindrada`, `torque`, `rotacao`, `tracao`, `cambio`, `numero_marchas`, `embreagem`, `dianteira`, `traseira`, `dianteirosfreios`, `traseirosfreios`, `assistencia`, `dianteira_pressao_enchimento`, `traseira_pressao_enchimento`, `dimensao_estepe`, `material_rodas`, `comprimento`, `distancia_eixos`, `largura`, `altura`, `peso_bruto`, `porta_malas`, `velocidade_maxima`, `aceleracao`, `capacidade_tanque`, `consumo_urbano`, `consumo_rodovia`, `autonomia_urbana`, `autonomia_rodovia`, `oleo_motor`, `oleo_transmissao`, `fluido_freio`, `carroceria_id`, `imagem_path`, `descricao`, `versao`) VALUES
(1, 2, 'Livina', 2010, '5', '4', 'HR16DE', 'Alumínio', 'Dianteira', 'Transversal', 'Flex', '4', '4 Válvulas / Total 16', 'Natural', 'Injeção multiponto', 'Gasolina 104 cv / Álcool 108 cv', '1598 cm³', 'Gasolina 14,5 kgfm / Álcool 15,1 kgfm', '6500 a 7000 rpm', 'Dianteira', 'Automático', '5', 'Embreagem de acionamento hidráulico', 'McPherson', 'Suspensão eixo de torção e traseira com barra estabilizadora', 'Disco ventilado', 'Tambor', 'Elétrica', '165/65 R15 / 26 PSI', '165/65 R15 / 33 PSI', '185/65 R15 - 80 km/h', 'Rodas de liga leve R15', '4180 mm', '2600 mm', '1690 mm', '1570 mm', 'Aproximadamente 1159 kg', '449 L', '183 km/h', '11,7s', '50 L', '10,2 km/L', '11,9 km/L', '360 km e 595 km', '595 km e 696 km', '3,7 litros quando troca o óleo e o filtro', '2,2 L', '300 a 400 ml para o sistema completo', 4, 0x75706c6f6164732f76656963756c6f732f363836633134663564346565632e706e67, '', 'SL 1.6'),
(2, 2, 'March', 2016, '5', '4', 'HR16DE', 'Alumínio', 'Dianteira', 'Transversal', 'Flex', '4 em linha', '4', 'Natural', 'Injeção multiponto', '111cv (G) (A) 5600 rpm', '1598 cm³', '15,1 kgfm (A) (G) 4000 rpm', '6750 rpm', 'Dianteira', 'Manual', '5', 'Embreagem monodisco a seco', 'McPherson', 'Eixo de torção', 'A disco', 'A disco', 'Elétrica', '175/60 R15', '175/60 R15', '165/70 R14  170 km/h', 'Rodas de liga leve', '3827 mm', '2450 mm', '1675 mm', '1528 mm', '925kg e 982kg', '265 L', '191 km/h', '9,5s', '41 L', 'Etanol 8,1 km/l 8,5 km/l, Gasolina 12,6 km/l 14,4 km/l', 'Etanol 9,9 km/l , gasolina 14,4 km/l', 'Etanol 8,5 km/l, gasolina 12,6 km/l', 'Etanol 9,9 km/l, gasolina 14,4 km/l', '5W-30, 4,3 L', 'SAE 75W-80, 2,67 L', 'DOT-4', 2, 0x75706c6f6164732f76656963756c6f732f363836633134653562303464612e706e67, '', 'SL 1.6'),
(3, 1, 'Virtus', 2020, '5', '4', 'EA211', 'Alumínio', 'Dianteira', 'Transversal', 'Flex', '4', '16 válvulas (4 por cilindro)', 'Turboalimentado', 'Injeção direta', '150 cv a 4.500 rpm', '1395 cm³', '25,5 kgfm (A) / (G) 1500 rpm', '6300 rpm', 'Dianteira', 'Automático', '6', 'Conversor de torque', 'Independente, McPherson', 'Eixo de torção', 'Disco Ventilado', 'Disco Sólido', 'Elétrica', '205/50/R17 - 32 PSI', '205/50/R17 - 32 PSI', '185/60 R15 / 80 km/h', 'Roda liga leve Aro 17', '4482 mm', '2651 mm', '1752 mm', '1467 mm', '1680 kg', '521 litros', '210 km/h', '8,7 s', '52 L', '7,6 km/L (A) 11,1km/L (G)', '9,8 km/L (A) 14 km/L (G)', '395 km (A) 577 km (G)', '510 km (A) 728 km (G)', '4,5 Litros - Óleo 5w40 Maxi', '1,9 Litros - MOTUL ATF VI', 'DOT 4', 6, 0x75706c6f6164732f76656963756c6f732f363836633134643439663630632e706e67, '', 'GTS 1.4'),
(4, 1, 'T-cross', 2022, '5', '4', 'EA211', 'Alumínio e ferro fundido', 'Dianteira', 'Transversal', 'Flex', '3 em linha', '4', 'Turboalimentado', 'Injeção direta', '116cv', '999 cm³', '20,4 kgfm (A) / (G) a 2000 rpm', '6500 rpm', 'Dianteira', 'Automático', '6', 'Conversor de torque', 'Independente McPherson', 'Eixo de torção', 'Disco ventilado', 'Disco sólido', 'Elétrica', '205/60 R16 33 psi', '205/60 R16 33 psi', '195/65 R15 80 km/h', 'Rodas de liga leve', '4199 mm', '2651 mm', '1760 mm', '-', '1252 kg', '373 L', '184 km/h', '10,4 s', '52 L', '8,3 km/L (A) 12 km/L (G)', '10 km/L (A) 14,4 km/L (G)', '432 km (A) 624 km (G)', '520 km (A) 749 km (G)', 'VW 508 88 - 4 L', '2 L', 'DOT 4 - 500 ml', 9, 0x75706c6f6164732f76656963756c6f732f363836633134633461663931372e706e67, '', '1.0 200TSI'),
(5, 6, 'Fiesta', 2008, '5', '4', 'Zetec Rocam 1.6', 'Ferro', 'Dianteira', 'Transversal', 'Flex', '4 em linha', '2', 'Natural', 'Injeção Eletrônica Multiponto', '111 cv (E) a 5500 rpm, 105 cv (G) a 5500 rpm', '1598 cm³', '15,8 kgfm (E) a 4250 rpm / 14,8 kgfm (G) a 4250 rpm', '6500 rpm a 6700 rpm', 'Dianteira', 'Manual', '5', 'Embreagem monodisco a seco', 'McPherson', 'Eixo de torção', 'Disco', 'Tambor', 'Hidráulica', '185/60 R14 / 32 psi', '185/60 R14 / 30 psi', 'T115/70 R15 / 80 km/h', 'Rodas - Liga leve; Estepe - Ferro, aro 14', '2490 mm', '2486 mm', '1683 mm', '1437 mm', '1065 kg', '268 L', '185 km/h', '10,8s', '45 L', 'Etanol - 8 km/L ; Gasolina - 11,5 km/L', 'Etanol - 10,5km/L ; Gasolina - 15km/L', 'Etanol - 360 km ; Gasolina - 517,5 km', 'Etanol - 472,5 km ; Gasolina - 675 km', 'Óleo 5W-30 ou 10W-40, capacidade 4,5 L', 'Óleo 75W90 (API GL-4), capacidade de 2,0 L', 'DOT 4', 2, 0x75706c6f6164732f76656963756c6f732f363836633134616533306632382e706e67, '', '1.6'),
(6, 7, '320i GT', 2015, '5', '4', 'N20B20', 'Alumínio', 'Dianteira', 'Longitudinal', 'Flex', '4 em linha', '4 por cilindro / 16 válvulas', 'Turboalimentado', 'Injeção direta', '184cv a 6250 rpm', '1998 cm³', '27,5kg a 1250 rpm', '6250 rpm', 'Traseira', 'Automático', '8', 'Conversor de torque', 'Independente McPherson', 'Independente Multibraço', 'Disco ventilado', 'Disco ventilado', 'Elétrica', '245/45 R18', '245/45 R18', 'Não possui', 'Alumínio', '4824 mm', '2920 mm', '1828 mm', '1508 mm', '1560 kg', '520 L', '229 km/h', '7,9s', '60 L', '8.9 km/L', '14,2 km/L', '534 km', '852 km', 'SAE 0w30 - 5 L', 'MTF LT-3 - 8.5 L', 'DOT 4', 1, 0x75706c6f6164732f76656963756c6f732f363836633134383631326330662e706e67, '', '2.0'),
(7, 8, 'Fusquinha', 2020, '2', 'Não possui', 'LIFAN 1p52fmh', 'Alumínio', 'Traseira', 'Longitudinal', 'Gasolina', '1 Cilindro', '2 Válvulas', 'Natural', 'Carburador', 'Cerca de 0,8 a 1,0 kgfm', '110 cm³', '0,98 kgf.m a 3500 RPM', '8000 a 9000 RPM', 'Traseira', 'Manual', '3 marchas (frente, neutro, ré)', 'Centrífuga automática', 'Independente com braços oscilantes molas e barras estabilizadoras', 'Eixo flutuante e amortecedores hidráulicos', 'Disco ventilado', 'Disco ventilado', 'Mecânica', '3/75 R8  /  18 a 28 psi', '3/75 R8  /  18 a 28 psi', 'Não possui', 'Ferro', '2100 mm', '1650 mm', '1000 mm', '90 cm', '140 kg', 'Não possui', '45 km/h', '---', '2 L', '---', '---', '---', '---', '700 ml', '700 a 800 ml', 'DOT 4', 3, 0x75706c6f6164732f76656963756c6f732f363836633139623438303431662e706e67, 'Construído por @ROGERSGARAGEOFICIAL', 'Senai'),
(8, 1, 'Fox', 2018, '5', '4', 'EA11', 'Liga de ferro fundido', 'Dianteira', 'Transversal', 'Flex', '4 em linha', '2 / total 8 válvulas', 'Natural', 'Injeção multiponto', '101 cv (G) 104 cv (E)', '1598 cm³', '9,8 kgfm  cv/litro', '6500 rpm', 'Dianteira', 'Automático', '5', 'Monodisco a seco', 'McPherson independente', 'Eixo de torção', 'Disco ventilado', 'Disco sólido', 'Elétrica', '195/55 R15 - 29 psi', '195/55 R15 - 29 psi', 'Possui 195/55 R15', 'Alumínio', '3868 mm', '2467 mm', '1660 mm', '1552 mm', '1070 kg', '270 L', '183 km', '10,6s', '50 L', '7,8 km/l [a] 11,6 km/l [g]', '9,7 km/l [a] 13,9 km [g]', '390 km [a] 580 km [g]', '485 km [a] 695 km [g]', '5W40 sintético - 4 Litros', 'Dexron e mercon - 2 Litros', 'Dexron e mercon - 2 Litros', 2, 0x75706c6f6164732f76656963756c6f732f363836656130643665626666622e706e67, '', '1.6 MSI'),
(9, 9, 'Peugeot', 2022, '5', '4', 'EC5 (ou EC5JP4)', 'Aço', 'Dianteira', 'Transversal', 'Flex', '4 em linha', '4 (total de 16 válvulas – 16V)', 'Natural Aspirado', 'Injeção eletrônica multiponto', '118cv a 6000 rpm', '1598 cm³', '16,00 kgfm torque máximo', '4250 rpm', 'Dianteira', 'Manual / Automático', '5', 'Monodisco a seco', 'McPherson', 'Eixo de torção', 'Disco sólido', 'Disco sólido', 'Elétrica', '205/60 R16 / 32 psi', '205/60 R16 / 32 psi', 'T125/70 R16 / 80 km/h', 'Liga leve / 16 polegadas', '4.297 mm', '2542 mm', '1739 mm', '1583 mm', '1730kg (em ordem de marcha 1190 a 1230)', '402 L', '190 km/h', '12,4s', '55 L', 'Gasolina 10,5 km/l - Álcool: 7,3 km/l', 'Gasolina: 12,8 km/l - Álcool 8,7 km/l', 'Gasolina: 578 km - Álcool 402km', 'Gasolina: 704 km/ Álcool 479 km', '5W30 - 4,5 litros de óleo para uma troca completa, incluindo o filtro', 'Total 7 litros', 'DOT 4 LV Classe 6', 2, 0x75706c6f6164732f76656963756c6f732f363836656139623439376565362e706e67, '', '1.6'),
(10, 10, 'Traker', 2023, '5', '4', 'LUV', 'Bloco e cabeçote de alumínio', 'Dianteira', 'Transversal', 'Flex', '3 cilindros', '4 Válvulas por cilindros', 'Turboalimentado', 'Injeção multiponto ( indireta)', '116 cv de potência a 5.500 rpm', '999 cm³', '15,1 kgfm (G) (A) 4000 rpm', '6.000 rpm', 'Dianteira', 'Automático', '6', 'Monodisco a seco', 'Independente McPherson', 'Eixo de Torção', 'Disco ventilado', 'Tambor', 'Elétrica', '215/60 R16', '215/60 R16', '115/70 R16', 'Rodas de liga leve', '4270 mm', '2570 mm', '1791 mm', '1624 mm', '1228 kg', '393 L', '177 km/h', '10,9s', '44 L', '7,8 km/l [a] 11,2 km/l [g]', '9,6 km/l [a] 13,6 km [g]', '343 km [a] 493 km [g]', '422 km [a] 598 km [g]', '5W30 - 4 L', 'DERON VI - 10 L', 'DOT 4 LV', 7, 0x75706c6f6164732f76656963756c6f732f363836656235363539653630392e706e67, '', '1.0 Turbo LTZ'),
(11, 11, 'HB20', 2012, '5', '4', 'Kappa', 'Alumínio', 'Dianteira', 'Transversal', 'Flex', '3 em linha', '4 / total 12 válvulas', 'Natural', 'Injeção multiponto', '80 cv (a) 75cv (g) e 6000 rpm', '998 cm³', '10, 2 kgfm (A) / 9,4 kgfm (G) 4500 rpm', '6500 rpm', 'Dianteira', 'Manual', '5', 'Monodisco a seco', 'McPherson', 'Eixo de torção', 'Disco ventilado', 'Tambor', 'Elétrica', '195/55 R16 / 33 psi', '195/55 R16 / 33 psi', '125/80 R15', 'Liga leve / 16 polegadas', '3940 mm', '2530 mm', '1720 mm', '1470 mm', '1.083 kg a 1.091 kg', '300 L', '190 km/h', '10,7s', '50 L', '9,6 km/L a 14,6 km/L', '10,4 km/L a 14 km/L', '480 km (A) /  675 km (G)', '520 km (A) / 730 km (G)', 'SAE 10w-30', '1,5 litros', 'Fluido Genuíno Hyundai DOT 4', 2, 0x75706c6f6164732f76656963756c6f732f363836656264343831663636652e706e67, '', 'Sport TGDI'),
(12, 6, 'Focus', 2013, '5', '4', 'Duratec HE 2.0L', 'Alumínio fundido e aço', 'Dianteira', 'Transversal', 'Flex', '4 em linha', '4', 'Natural', 'Injeção multiponto', '143 cv [G] 148 cv [E]', '1998 cm³', '18,6 kgfm (G) 4500 rpm', '6500 rpm', 'Dianteira', 'Automático', '4', 'Monodisco a seco', 'McPherson', 'Eixo de torção', 'Disco ventilado', 'Tambor', 'Hidráulica', '205/55 R16', '205/55 R16', 'T115/70 R15 - 80 km/h', 'Liga leve / 16 polegadas', '4534 mm', '2694 mm', '1823 mm', '1469 mm', '1320 kg', '452 L', '190-195 km/h', '10,5-11s', '55 L', '7,5 km/L a 8,5km/L', '11 km/L a 13 km/L', '440 km', '660 km', '4,3 L', '7,5 L', '700 ml', 6, 0x75706c6f6164732f76656963756c6f732f363836656330353435663934312e706e67, '', '2.0 Duratec-HE'),
(13, 2, 'Versa', 2016, '5', '4', 'HR16DE', 'Alumínio', 'Dianteira', 'Transversal', 'Flex', '4 em linha', '4', 'Natural', 'Injeção multiponto', '111 cv (G)', '1598 cm³', '15,1 kgfm (G)', '6750 rpm', 'Dianteira', 'Automático', '5', 'CVT (Transmissão Continuamente Variável)', 'McPherson', 'Eixo de Torção', 'Disco ventilado', 'Tambor', 'Elétrica', '185/60 R15 - 33 psi', '185/60 R15 - 33 psi', '165/70 R14 - 33 psi', 'Rodas de liga leve, geralmente feitas de alumínio', '4492 mm', '2620 mm', '1605 mm', '1465 mm', '1052 kg', '460 L', '187km/h', '10,4s', '41 L', '12,6 km/L (G)', '14,4 km/L (G)', '517 km (G)', '590 km (G)', '4,2 L', '2,7 L', 'DOT 4', 6, 0x75706c6f6164732f76656963756c6f732f363836656332346563643935612e706e67, '', 'SL 1.6'),
(14, 11, 'HB20', 2022, '5', '4', 'G3LC', 'Alumínio', 'Dianteira', 'Transversal', 'Flex', '3 em linha', '4 / total 12 válvulas', 'Turboalimentado', 'Injeção Direta', '120 Cv [A] [G] 6000 Rpm', '998 cm', '17,5 Kgfm [A] [G] A 1500 Rpm', '6000 Rpm', 'Dianteira', 'Automático', '6', 'Monodisco a seco', 'McPherson independente', 'Eixo de torção', 'Disco ventilado', 'Tambor', 'Elétrica', '185/60 R15 - 33 PSI', '185/60 R15 - 33 PSI', '125/85 R15, 80 Km', 'Liga-Leve,185/60 R15', '3940 mm', '2530 mm', '1720 mm', '1470 mm', '989 kg', '300 L', '190 km/h', '10,7 s', '50 L', '8,2 km/L (A)  11,8km/L (G)', '10,2 km/L (A) 14,2 km/L (G)', '410 km (A) 590 km (G)', '525 km (A) 745 km (G)', '3,5 L', '2 L', 'DOT 4', 2, 0x75706c6f6164732f76656963756c6f732f363836656336393038623337632e706e67, '', 'Evolution 1.0 Turbo'),
(15, 12, 'Doblò', 2012, '7', '6', 'VR249', 'Alumínio', 'Dianteira', 'Transversal', 'Flex', '4 em linha', '4 Válvulas por cilindros', 'Natural', 'Injeção eletronica', '132 cv', '1747 cm³', 'Álcool 18,9 kgf.m / Gasolina 18,4 kgf.m', '6200 rpm', 'Dianteira', 'Manual', '5', 'Monodisco', 'McPherson', 'Tipo eixo transversal', 'Disco ventilado', 'Tambor', 'Hidráulica', '175/70 R14 - 33 psi', '175/70 R14 - 36 psi', 'Aro 15 / 80 km/h', 'Liga leve', '4252 mm', '2566 mm', '1722 mm', '1935 mm', '1330 kg', '665 L', '165 km/h (G) e 167 km/h (A)', '12,7s', '60 L', '7,6 km/L', '10,1km/L', '444 km', '600 km', '4,3 L', '1,9 L', '500 ml', 8, 0x75706c6f6164732f76656963756c6f732f363836656361336637303137342e706e67, '', '1.8'),
(16, 13, 'Griffe', 2020, '5', '4', '1.6 THP Flex', 'Alumínio', 'Dianteira', 'Transversal', 'Flex', '4 em linha', '4', 'Turbo compressor', 'Injeção direta', '173 cv (A) 165 cv (G) a 6000 rpm', '400 cm³', '24,5 kgfm (A) 24,5 kgfm (G) a 1400 rpm', '6800 rpm', 'Dianteira', 'Automático', '6', 'Monodisco a seco', 'Independente, McPherson', 'Eixo de torção', 'Disco ventilado', 'Disco ventilado', 'Elétrica', '205/60R16 . 33 psi. Feito de borracha, nylon ou poliéster e aço', '205/60R16 . 33 psi. Feito de borracha, nylon ou poliéster e aço', 'Medida: 185/60 R15 Uso: Temporário Velocidade máxima: 80 km/h', 'Liga leve (alumínio) e têm dimensão de 16 polegadas', '4159 mm', '2542 mm', '1739 mm', '1583 mm', '1246 kg', '355 L', '209 km/h', '8,1s', '55 L', '7,8 km/l com etanol; 11,2 km/l com gasolina', '12,9 km/l etanol; 13,5 km/l com gasolina', '429 km com etanol e 616 km com gasolina', '12,9 km/l na estrada com gasolina e 9,1 km/l com etanol', '5W-30 - 4,25 L', 'Sintético 5W30', 'DOT-4', 7, 0x75706c6f6164732f76656963756c6f732f363837366131316566313033632e706e67, '', '2008 1.6'),
(17, 1, 'Fox I-Motion', 2010, '5', '4', 'EA211', 'Ferro fundido', 'Dianteira', 'Transversal', 'Flex', '4 em linha', '2', 'Natural', 'Injeção multiponto', '101 cv (G) 5250 rpm', '1598 cm³', '15,4 kgfm (G) 2500 rpm', '6500 rpm', 'Dianteira', 'Automático', '5', 'Embreagem monodisco a seco', 'McPherson', 'Eixo de torção', 'Disco ventilado', 'Tambor', 'Hidráulica', '195/55 R15 ; 30 psi', '195/55 R15 ; 30 psi', '195/55 R15', 'Aço / Aro 15', '3823 mm', '2465 mm', '1641 mm', '1543 mm', '1054 kg', '260 L', '184 km/h', '11s', '50 L', '(Álcool) 6,6 km/L (Gasolina) 9,8 km/L', '(Álcool) 8,3 km/L (Gasolina) 12,3 km/L', '(Álcool) 330 km (Gasolina) 490 km', '(Álcool) 415 km (Gasolina) 615 km', '5W40 sintético / 3,6 litros', 'Titan CHF 202 / 2,0 litros', 'Dot 4 / 0,8 a 1 litro', 2, 0x75706c6f6164732f76656963756c6f732f363837366134613834616233642e706e67, '', '1.6 8v'),
(18, 2, 'Versa', 2011, '5', '4', 'HR10DE', 'Alumínio', 'Dianteira', 'Transversal', 'Flex', '4 em linha', '4 por cilindro / 16 válvulas', 'Natural', 'Injeção eletrônica com multiponto sequencial', '111 cv', '1598 cm³', '114 lb-ft', '6750 rpm', 'Dianteira', 'Manual', '5', 'Monodisco', 'Independente tipo McPherson com barra estabilizadora e molas helicoidais.', 'Semi-independente com eixo de torção e molas helicoidais', 'Disco ventilado', 'Tambor', 'Elétrica', '185/65 R15', '185/65 R15', '185/65 R15 (largura do pneu de 185 mm, perfil de 65% da largura, e aro de 15 polegadas).', 'Aro 15&amp;quot; (aço com calotas ou liga leve, dependendo da versão)', '4455 mm', '2600 mm', '1695 mm', '1514 mm', '1052 kg', '460 L', '189 km/h', '10.7s', '41 L', 'Com Etanol: 7,8 km/l e Com Gasolina: 11,7 km/l', 'Com Etanol: 9,3 km/l e com Gasolina: 13,9 km/l', 'Com Etanol: 319,8 km e com Gasolina: 479,7 km', 'Com Etanol: 381,3 km e com Gasolina: 569,9 km', 'Viscosidade: 5W-30 , capacidade de 4,4 litros.', 'Nissan NS-2 CVT Fluid ou Nissan NS-3 CVT Fluid,capacidade de até 7 a 8 litros', 'DOT 3 ou DOT 4', 6, 0x75706c6f6164732f76656963756c6f732f363837366162663262656231332e706e67, '', '1.6'),
(19, 12, 'Doblò', 2000, '2', '4', 'GM Family I 1.8 (8V)', 'Ferro fundido / Alumínio (cabeçote)', 'Dianteira', 'Transversal', 'Gasolina', '4 em linha', '2 válvulas por cilindro / 8 válvulas', 'Natural', 'Injeção Eletrônica Multiponto', '103 cv 5200 rpm', '1796 cm³', '17,7 kgfm (G) 2800 rpm', '6000 rpm', 'Dianteira', 'Manual', '5', 'Monodisco a seco, comando mecânico', 'Independente McPherson', 'Eixo de torção com molas helicoidais', 'Disco ventilado', 'Tambor', 'Hidráulica', '185/60 R15 / 30 psi', '185/60 R15 / 30 psi', 'Estepe integral / até 180 km/h', 'Aço (liga leve como opcional) / 15&quot;', '4252 mm', '2582 mm', '1714 mm', '1830 mm', '1860 kg / 1320 kg', '530 L', '170 km/h', '12,5s', '60 L', '7,5 km/l', '10,5 km/l', '450 km', '630 km', '4,0 L com filtro (5W30, 10W40)', '2,0 L (óleo 75W80)', 'DOT 3 ou DOT 4', 8, 0x75706c6f6164732f76656963756c6f732f363837366139393763643830642e706e67, '', '1.8'),
(20, 1, 'Jetta', 2025, '5', '4', 'EA888', 'Alumínio', 'Dianteira', 'Transversal', 'Gasolina', '4 em linha', '4 por cilindro / 16 válvulas', 'Turbo compressor', 'Injeção direta', '231cv a 5000 rpm', '1984 cm³', '35,7 kgfm a 1500 rpm', '6800 rpm', 'Dianteira', 'Manual', '7', 'Embreagem dupla banhada a óleo', 'Independente McPherson', 'Independente Multibraço', 'Disco Ventilado', 'Disco Sólido', 'Elétrica', '225/45 R18 - 39 psi', '225/45 R18 - 39 psi', 'T125/70 R18 99M - 420kPa/4,2bar/60psi', 'Liga leve 18 polegadas', '4747 mm', '2680 mm', '1799 mm', '1478 mm', '1950 Kg / 1432 Kg', '510 L', '249 km/h', '6,7s', '50 L', '9,9 Km/l', '12,2 Km/l', '495 km', '610 km', 'Viscosidade 5W-40; Capacidade 5,7litros', 'Motul Dctf Dsg (Transmissões de dupla embreagem)', 'DOT-4', 6, 0x75706c6f6164732f76656963756c6f732f363837366165323061366434382e706e67, '', '2.0 GLI'),
(21, 1, 'Amarok', 2025, '5', '4', 'EA897 TD', 'Alumínio e ferro fundido', 'Dianteira', 'Longitudinal', 'Diesel', '6', '4 por cilindro / 24 válvulas', 'Turboalimentado', 'Injeção direta', '258 cv a 3250rpm', '495 cm³', '59,1kgfm a 1400 rpm', '5000 rpm', 'Integral', 'Automático', '8', 'Automática', 'Independente McPherson com barra estabilizadora', 'Eixo rígido com feixe de molas', 'Disco ventilado', 'Disco ventilado', 'Hidráulica', '255/60 R20 - 32 libras', '255/60 R20 - 44 libras', '255/60 R20 - 32 libras', 'Liga de alumínio/ 20 polegadas', '5350 mm', '3097 mm', '1954 mm', '1851 mm', '2191 kg', '1280 L', '190 km/h', '8s', '80 L', '8,7 Km/l', '9,7 Km/l', '696 Km', '744 Km', 'VW 507 00 e ACEA C3, viscosidade SAE 0W-30 / 8,0 litros', 'G 060 162 A2 / 12 litros', 'DOT-4', 5, 0x75706c6f6164732f76656963756c6f732f363837366166636531346439362e706e67, '', 'Extreme 3.0 V6');

-- --------------------------------------------------------

--
-- Estrutura para tabela `montadoras`
--

DROP TABLE IF EXISTS `montadoras`;
CREATE TABLE `montadoras` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `imagem_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `montadoras`
--

INSERT INTO `montadoras` (`id`, `nome`, `imagem_path`) VALUES
(1, 'Volkswagen', 'uploads/montadoras/6877f12e88e66.png'),
(2, 'Nissan', 'uploads/montadoras/6869515e9263d.png'),
(6, 'Ford', 'uploads/montadoras/6877f157a2e13.png'),
(7, 'BMW', 'uploads/montadoras/6877eeae8c71a.png'),
(8, 'SENAI', 'uploads/montadoras/6877efff77aed.png'),
(9, 'Stellantis', 'uploads/montadoras/6877f0d8c99e4.png'),
(10, 'Chevrolet', 'uploads/montadoras/6877eed4a581f.png'),
(11, 'Hyundai', 'uploads/montadoras/6877efb1304a7.png'),
(12, 'Fiat', 'uploads/montadoras/6877ef67a100e.png'),
(13, 'Peugeot', 'uploads/montadoras/6877f1cf9a9a2.png');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `role`) VALUES
(1, 'prof@senai.com', 'senha123', 'professor');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `carrocerias`
--
ALTER TABLE `carrocerias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `fichas_tecnicas`
--
ALTER TABLE `fichas_tecnicas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `montadoras_id` (`montadoras_id`),
  ADD KEY `carroceria_id` (`carroceria_id`);

--
-- Índices de tabela `montadoras`
--
ALTER TABLE `montadoras`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `carrocerias`
--
ALTER TABLE `carrocerias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `fichas_tecnicas`
--
ALTER TABLE `fichas_tecnicas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `montadoras`
--
ALTER TABLE `montadoras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `fichas_tecnicas`
--
ALTER TABLE `fichas_tecnicas`
  ADD CONSTRAINT `fichas_tecnicas_ibfk_1` FOREIGN KEY (`montadoras_id`) REFERENCES `montadoras` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fichas_tecnicas_ibfk_2` FOREIGN KEY (`carroceria_id`) REFERENCES `carrocerias` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
