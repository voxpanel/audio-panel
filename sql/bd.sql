-- phpMyAdmin SQL Dump
-- version 4.0.10.20
-- https://www.phpmyadmin.net
--
-- Máquina: localhost
-- Data de Criação: 27-Out-2018 às 11:14
-- Versão do servidor: 5.5.61
-- versão do PHP: 5.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de Dados: `audioaju`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `acessos_paginas`
--

CREATE TABLE IF NOT EXISTS `acessos_paginas` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `pagina` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `total` int(10) NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=418 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `administradores`
--

CREATE TABLE IF NOT EXISTS `administradores` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `usuario` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `senha` varchar(255) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=2 ;

--
-- Extraindo dados da tabela `administradores`
--

INSERT INTO `administradores` (`codigo`, `nome`, `usuario`, `senha`) VALUES
(1, 'Administrador', 'admin', '*4ACFE3202A5FF5CF467898FC58AAB1D615029441');

-- --------------------------------------------------------

--
-- Estrutura da tabela `apps`
--

CREATE TABLE IF NOT EXISTS `apps` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `codigo_stm` int(10) NOT NULL,
  `radio_nome` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `radio_email` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `radio_site` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `radio_facebook` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `radio_twitter` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `radio_descricao` text COLLATE latin1_general_ci NOT NULL,
  `play` char(3) COLLATE latin1_general_ci NOT NULL,
  `hash` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `apk` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `zip` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `package` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `tipo` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT 'android',
  `aviso` text COLLATE latin1_general_ci NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `data` datetime NOT NULL,
  `print` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `source` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT 'source1',
  `log_build` longtext COLLATE latin1_general_ci NOT NULL,
  `compilado` char(3) COLLATE latin1_general_ci DEFAULT 'nao',
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `atalhos`
--

CREATE TABLE IF NOT EXISTS `atalhos` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `codigo_stm` int(10) NOT NULL,
  `menu` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `lang` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `ordem` int(10) NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `avisos`
--

CREATE TABLE IF NOT EXISTS `avisos` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `codigo_servidor` int(10) NOT NULL,
  `area` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `titulo` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `descricao` longtext COLLATE latin1_general_ci NOT NULL,
  `data` date NOT NULL,
  `mensagem` longtext COLLATE latin1_general_ci NOT NULL,
  `status` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'sim',
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=3 ;

--
-- Extraindo dados da tabela `avisos`
--

INSERT INTO `avisos` (`codigo`, `codigo_servidor`, `area`, `titulo`, `descricao`, `data`, `mensagem`, `status`) VALUES
(2, 0, 'streaming', 'Painel DEMO', 'Esse é nosso novo painel stream.', '2018-06-26', 'Aqui poderá ver e testar as funções do nosso <br />\r\nsistema stream.', 'sim');

-- --------------------------------------------------------

--
-- Estrutura da tabela `avisos_desativados`
--

CREATE TABLE IF NOT EXISTS `avisos_desativados` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `codigo_aviso` int(10) NOT NULL,
  `codigo_usuario` int(10) NOT NULL,
  `area` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `data` date NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `bloqueios_login`
--

CREATE TABLE IF NOT EXISTS `bloqueios_login` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `codigo_cliente` int(10) NOT NULL,
  `codigo_stm` int(10) NOT NULL,
  `data` datetime NOT NULL,
  `ip` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `navegador` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `tentativas` int(10) NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `configuracoes`
--

CREATE TABLE IF NOT EXISTS `configuracoes` (
  `dominio_cdn` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `dominio_padrao` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `codigo_servidor_atual` int(10) NOT NULL,
  `codigo_servidor_aacplus_atual` int(10) NOT NULL,
  `usar_cdn` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'sim',
  `manutencao` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'nao'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Extraindo dados da tabela `configuracoes`
--

INSERT INTO `configuracoes` (`dominio_cdn`, `dominio_padrao`, `codigo_servidor_atual`, `codigo_servidor_aacplus_atual`, `usar_cdn`, `manutencao`) VALUES
('', 'cdrpainel.ml', 1, 2, 'nao', 'nao');

-- --------------------------------------------------------

--
-- Estrutura da tabela `dicas_rapidas`
--

CREATE TABLE IF NOT EXISTS `dicas_rapidas` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `mensagem` text COLLATE latin1_general_ci NOT NULL,
  `exibir` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'sim',
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `dicas_rapidas_acessos`
--

CREATE TABLE IF NOT EXISTS `dicas_rapidas_acessos` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `codigo_stm` int(10) NOT NULL,
  `codigo_dica` int(10) NOT NULL,
  `total` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `djs`
--

CREATE TABLE IF NOT EXISTS `djs` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `codigo_stm` int(10) NOT NULL,
  `login` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `senha` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `hora_inicio` char(5) COLLATE latin1_general_ci NOT NULL,
  `hora_fim` char(5) COLLATE latin1_general_ci NOT NULL,
  `dias_semana` int(3) NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `djs_restricoes`
--

CREATE TABLE IF NOT EXISTS `djs_restricoes` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `codigo_stm` int(10) NOT NULL,
  `codigo_dj` int(10) NOT NULL,
  `hora_inicio` char(5) COLLATE latin1_general_ci NOT NULL,
  `hora_fim` char(5) COLLATE latin1_general_ci NOT NULL,
  `dias_semana` int(3) NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `dominios_bloqueados`
--

CREATE TABLE IF NOT EXISTS `dominios_bloqueados` (
  `dominio` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `estatisticas`
--

CREATE TABLE IF NOT EXISTS `estatisticas` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `codigo_stm` int(10) NOT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `ip` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '000.000.000.000',
  `pais` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `tempo_conectado` int(20) NOT NULL DEFAULT '0',
  `player` varchar(255) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`codigo`),
  KEY `indice_stm` (`codigo_stm`),
  KEY `indice_pais` (`codigo_stm`,`pais`(10)),
  KEY `indice_data` (`codigo_stm`,`data`),
  KEY `indice_tempo_conectado` (`codigo_stm`,`tempo_conectado`),
  KEY `indice_ip` (`codigo_stm`,`ip`(15)),
  KEY `indice_robot` (`codigo_stm`,`data`,`ip`(12)),
  KEY `player` (`player`),
  KEY `codigo_stm` (`codigo_stm`,`data`,`hora`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `estatisticas_redessociais`
--

CREATE TABLE IF NOT EXISTS `estatisticas_redessociais` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `codigo_stm` int(10) NOT NULL,
  `data` date NOT NULL,
  `ip` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `pais` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `player` varchar(255) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `data` datetime NOT NULL,
  `host` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT 'http://',
  `ip` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `navegador` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `log` text COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=75 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `logs_acessos`
--

CREATE TABLE IF NOT EXISTS `logs_acessos` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(255) NOT NULL,
  `host` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `data` datetime NOT NULL,
  `referer` text NOT NULL,
  `painel` varchar(255) NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `logs_streamings`
--

CREATE TABLE IF NOT EXISTS `logs_streamings` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `codigo_stm` int(10) NOT NULL,
  `data` datetime NOT NULL,
  `host` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `ip` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `navegador` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `log` text COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=166 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `multipoint`
--

CREATE TABLE IF NOT EXISTS `multipoint` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `codigo_stm` int(10) NOT NULL,
  `id` int(10) NOT NULL,
  `ponto` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `ouvintes` int(10) NOT NULL,
  `bitrate` int(10) NOT NULL,
  `encoder` char(5) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedidos_musicais`
--

CREATE TABLE IF NOT EXISTS `pedidos_musicais` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `codigo_stm` int(10) NOT NULL,
  `nome` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `email` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `data` datetime NOT NULL,
  `musica` varchar(255) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `playlists`
--

CREATE TABLE IF NOT EXISTS `playlists` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `codigo_stm` int(10) NOT NULL,
  `nome` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `arquivo` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `data` datetime NOT NULL,
  `hora_certa` char(3) COLLATE latin1_general_ci DEFAULT 'nao',
  `vinhetas_comerciais` char(3) COLLATE latin1_general_ci DEFAULT 'nao',
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `playlists_agendamentos`
--

CREATE TABLE IF NOT EXISTS `playlists_agendamentos` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `codigo_stm` int(10) NOT NULL,
  `codigo_playlist` int(10) NOT NULL,
  `frequencia` int(1) NOT NULL,
  `data` date NOT NULL,
  `hora` char(2) COLLATE latin1_general_ci NOT NULL,
  `minuto` char(2) COLLATE latin1_general_ci NOT NULL,
  `dias` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `ultima_execussao` datetime NOT NULL,
  PRIMARY KEY (`codigo`),
  KEY `indice_data` (`data`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `playlists_agendamentos_logs`
--

CREATE TABLE IF NOT EXISTS `playlists_agendamentos_logs` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `codigo_agendamento` int(10) NOT NULL,
  `codigo_stm` int(10) NOT NULL,
  `data` datetime NOT NULL,
  `playlist` varchar(255) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `playlists_musicas`
--

CREATE TABLE IF NOT EXISTS `playlists_musicas` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `codigo_playlist` int(10) NOT NULL,
  `path_musica` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `musica` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `duracao` varchar(10) COLLATE latin1_general_ci NOT NULL DEFAULT '00:00:00',
  `duracao_segundos` int(10) NOT NULL DEFAULT '0',
  `tipo` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT 'musica',
  `ordem` char(10) COLLATE latin1_general_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`codigo`),
  KEY `indice_playlist` (`codigo_playlist`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=192 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `programetes`
--

CREATE TABLE IF NOT EXISTS `programetes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) DEFAULT NULL,
  `porta` varchar(50) DEFAULT NULL,
  `senha` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `relay_agendamentos`
--

CREATE TABLE IF NOT EXISTS `relay_agendamentos` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `codigo_stm` int(10) NOT NULL,
  `servidor` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `frequencia` int(1) NOT NULL,
  `data` date NOT NULL,
  `hora` char(2) COLLATE latin1_general_ci NOT NULL,
  `minuto` char(2) COLLATE latin1_general_ci NOT NULL,
  `duracao_hora` char(2) COLLATE latin1_general_ci NOT NULL DEFAULT '00',
  `duracao_minuto` char(2) COLLATE latin1_general_ci NOT NULL DEFAULT '00',
  `dias` varchar(50) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `revendas`
--

CREATE TABLE IF NOT EXISTS `revendas` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `codigo_revenda` int(10) NOT NULL DEFAULT '0',
  `id` char(6) COLLATE latin1_general_ci NOT NULL,
  `nome` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `email` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `senha` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `streamings` int(10) NOT NULL,
  `ouvintes` int(10) NOT NULL,
  `bitrate` int(10) NOT NULL,
  `espaco` int(20) NOT NULL,
  `subrevendas` int(10) NOT NULL DEFAULT '0',
  `url_logo` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `chave_api` longtext COLLATE latin1_general_ci NOT NULL,
  `chave_api_google_maps` longtext COLLATE latin1_general_ci NOT NULL,
  `servidor` int(10) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '1',
  `avisos` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `aacplus` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'nao',
  `url_suporte` text COLLATE latin1_general_ci,
  `data_cadastro` datetime NOT NULL,
  `alterar_senha` int(1) NOT NULL DEFAULT '1',
  `dominio_padrao` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `smtp_servidor` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `smtp_porta` int(10) NOT NULL,
  `smtp_email` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `smtp_senha` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `stm_exibir_tutoriais` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'sim',
  `url_tutoriais` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT 'http://',
  `url_downloads` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `stm_exibir_app_android` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'nao',
  `stm_exibir_downloads` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'sim',
  `stm_exibir_mini_site` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'nao',
  `stm_exibir_app_android_painel` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'sim',
  `idioma_painel` char(10) COLLATE latin1_general_ci NOT NULL DEFAULT 'pt-br',
  `tipo` int(1) NOT NULL DEFAULT '1',
  `ultimo_acesso_data` datetime NOT NULL,
  `ultimo_acesso_ip` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '000.000.000.000',
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `screen_size`
--

CREATE TABLE IF NOT EXISTS `screen_size` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `width` int(10) NOT NULL,
  `height` int(10) NOT NULL,
  `data` datetime NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=940 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `servidores`
--

CREATE TABLE IF NOT EXISTS `servidores` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT 'Stm',
  `ip` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `senha` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `porta_ssh` int(6) NOT NULL DEFAULT '6985',
  `status` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'on',
  `limite_streamings` int(10) NOT NULL DEFAULT '150',
  `tipo` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT 'streaming',
  `load` float NOT NULL,
  `trafego` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `trafego_out` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `ordem` int(10) NOT NULL,
  `mensagem_manutencao` text COLLATE latin1_general_ci NOT NULL,
  `grafico_trafego` text COLLATE latin1_general_ci NOT NULL,
  `portapro` varchar(15) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `streamings`
--

CREATE TABLE IF NOT EXISTS `streamings` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `codigo_cliente` int(10) NOT NULL,
  `codigo_servidor` int(10) NOT NULL,
  `codigo_servidor_aacplus` int(10) NOT NULL,
  `porta` int(10) NOT NULL,
  `porta_dj` int(10) NOT NULL,
  `ouvintes` int(10) NOT NULL,
  `bitrate` int(10) NOT NULL,
  `bitrate_autodj` int(10) NOT NULL,
  `encoder_mp3` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'sim',
  `encoder_aacplus` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'sim',
  `encoder` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT 'mp3',
  `espaco` int(10) NOT NULL,
  `espaco_usado` int(10) NOT NULL DEFAULT '0',
  `senha` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `senha_admin` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `streamtitle` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT 'Web Radio',
  `streamurl` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT 'http://www.seusite.com',
  `genre` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT 'Rock',
  `showlastsongs` int(10) NOT NULL DEFAULT '5',
  `publicserver` char(6) COLLATE latin1_general_ci NOT NULL DEFAULT 'always',
  `allowrelay` int(1) NOT NULL DEFAULT '1',
  `descricao` text COLLATE latin1_general_ci NOT NULL,
  `xfade` int(10) NOT NULL DEFAULT '0',
  `ftp_dir` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `identificacao` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT 'Não cadastrada',
  `data_cadastro` date NOT NULL,
  `hora_cadastro` time NOT NULL,
  `local_cadastro` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT 'painel',
  `ip_cadastro` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '000.000.000.000',
  `pid` int(10) NOT NULL,
  `pid_autodj` int(10) NOT NULL DEFAULT '0',
  `protecao` int(1) NOT NULL DEFAULT '0',
  `ultima_playlist` int(10) NOT NULL DEFAULT '0',
  `aacplus` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'nao',
  `relay` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'nao',
  `relay_ip` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT 'empty',
  `relay_porta` int(10) NOT NULL DEFAULT '0',
  `relay_monitorar` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'nao',
  `pagina_inicial` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '/informacoes',
  `autodj` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'sim',
  `autodj_channels` int(2) NOT NULL DEFAULT '2',
  `autodj_samplerate` int(6) NOT NULL DEFAULT '44100',
  `autodj_shuffle` int(1) NOT NULL DEFAULT '0',
  `autodj_prog_aovivo` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'nao',
  `autodj_prog_aovivo_msg` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT 'Programação ao vivo',
  `idioma_painel` char(10) COLLATE latin1_general_ci NOT NULL DEFAULT 'pt-br',
  `email` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `app_url_logo` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT 'http://',
  `app_url_background` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT 'http://',
  `exibir_app_android` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'sim',
  `exibir_atalhos` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'nao',
  `player_autoplay` char(5) COLLATE latin1_general_ci NOT NULL DEFAULT 'true',
  `player_exibir_chat` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'nao',
  `player_exibir_pedido_musical` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'nao',
  `player_volume_inicial` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT '1.0',
  `permitir_alterar_senha` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'sim',
  `timezone` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT 'America/Sao_Paulo',
  `formato_data` char(11) COLLATE latin1_general_ci NOT NULL DEFAULT 'd/m/Y H:i:s',
  `exibir_mini_site` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'nao',
  `mini_site_dominio` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `mini_site_cor_fundo` char(6) COLLATE latin1_general_ci NOT NULL DEFAULT 'FFFFFF',
  `mini_site_cor_topo` char(6) COLLATE latin1_general_ci NOT NULL DEFAULT 'E9E9E9',
  `mini_site_cor_texto_topo` char(6) COLLATE latin1_general_ci NOT NULL DEFAULT '000000',
  `mini_site_cor_texto_padrao` char(6) COLLATE latin1_general_ci NOT NULL DEFAULT '000000',
  `mini_site_cor_texto_rodape` char(6) COLLATE latin1_general_ci NOT NULL DEFAULT '000000',
  `mini_site_exibir_chat` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'sim',
  `mini_site_exibir_xat_id` int(10) NOT NULL,
  `mini_site_url_facebook` text COLLATE latin1_general_ci NOT NULL,
  `mini_site_url_twitter` text COLLATE latin1_general_ci NOT NULL,
  `arquivo_intro` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `arquivo_backup` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `aparencia_exibir_stats_ouvintes` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'sim',
  `aparencia_exibir_stats_ftp` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'sim',
  `aparencia_exibir_musica_atual` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'sim',
  `aparencia_exibir_player` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT 'sim',
  `ultimo_acesso_data` datetime NOT NULL,
  `ultimo_acesso_ip` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '000.000.000.000',
  `data_bloqueio` datetime NOT NULL,
  `programetes` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`codigo`),
  KEY `indice_porta` (`porta`),
  KEY `indice_porta_dj` (`porta_dj`),
  FULLTEXT KEY `idioma_painel` (`idioma_painel`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=50 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tutoriais`
--

CREATE TABLE IF NOT EXISTS `tutoriais` (
  `codigo` int(10) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `data` date NOT NULL,
  `vizualizacoes` int(10) NOT NULL,
  `tutorial` longtext COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=2 ;

--
-- Extraindo dados da tabela `tutoriais`
--

INSERT INTO `tutoriais` (`codigo`, `titulo`, `data`, `vizualizacoes`, `tutorial`) VALUES
(1, 'teste', '2018-09-04', 0, '<p>testerser</p>');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
