-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-03-2026 a las 20:28:15
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `easyparte`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL,
  `nif` varchar(9) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `poblacion` varchar(100) NOT NULL,
  `direccion` varchar(150) NOT NULL,
  `prefijo` varchar(5) DEFAULT NULL,
  `contacto` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `id_empresa` int(11) NOT NULL,
  `cuota` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `nif`, `nombre`, `poblacion`, `direccion`, `prefijo`, `contacto`, `email`, `id_empresa`, `cuota`, `activo`) VALUES
(10, 'xxxxxxxxx', 'Clientes Varios', 'Prueba', 'Calle Prueba', '34', NULL, NULL, 1, 0, 1),
(14, 'b10508929', 'Empresa Prueba, S.L.', 'Prueba', 'Calle Prueba', '34', NULL, NULL, 1, 1, 1),
(15, 'B10102145', 'Prueba2', 'Prueba', 'Calle Prueba', '34', NULL, NULL, 1, 1, 0),
(16, 'B12342132', 'Prueba3', 'prueba', 'Calle ', '34', NULL, NULL, 1, 1, 0),
(18, 'x13131313', 'Taller Prueba', 'Prueba', 'Prueba', '+34', NULL, NULL, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamento`
--

CREATE TABLE `departamento` (
  `id_departamento` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `id_empresa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado`
--

CREATE TABLE `empleado` (
  `id_empleado` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `apellido` varchar(45) NOT NULL,
  `apellido_2` varchar(45) DEFAULT NULL,
  `extension_tel` varchar(10) DEFAULT NULL,
  `prefijo` varchar(5) DEFAULT NULL,
  `movil` varchar(20) DEFAULT NULL,
  `nif` varchar(9) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `id_departamento` int(11) DEFAULT NULL,
  `id_empresa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleado`
--

INSERT INTO `empleado` (`id_empleado`, `nombre`, `apellido`, `apellido_2`, `extension_tel`, `prefijo`, `movil`, `nif`, `activo`, `id_departamento`, `id_empresa`) VALUES
(19, 'Prueba', 'Prueba', '', NULL, '+34', '', '', 1, NULL, 1),
(21, 'Beatriz', 'Gordo', '', NULL, '+34', '', '4441099c', 1, NULL, 1),
(22, 'Juan', 'Pérez', NULL, NULL, NULL, NULL, NULL, 1, NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `id_empresa` int(11) NOT NULL,
  `nif` varchar(9) DEFAULT NULL,
  `nombre` varchar(150) NOT NULL,
  `prefijo` varchar(5) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `pais` varchar(100) DEFAULT NULL,
  `poblacion` varchar(100) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresa`
--

INSERT INTO `empresa` (`id_empresa`, `nif`, `nombre`, `prefijo`, `telefono`, `email`, `pais`, `poblacion`, `direccion`, `activo`) VALUES
(1, 'B12345678', 'Empresa Demo S.L.', NULL, NULL, NULL, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parte_trabajo`
--

CREATE TABLE `parte_trabajo` (
  `id_parte_trabajo` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `descripcion` varchar(500) NOT NULL,
  `fecha_inicio` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_fin` datetime DEFAULT NULL,
  `estado` enum('Abierto','En curso','Cerrado','Pausado') NOT NULL DEFAULT 'Abierto',
  `id_cliente` int(11) NOT NULL,
  `id_tarea` int(11) DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `horas` decimal(5,2) DEFAULT 0.00,
  `material` text DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `firma_cliente` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `parte_trabajo`
--

INSERT INTO `parte_trabajo` (`id_parte_trabajo`, `id_empresa`, `descripcion`, `fecha_inicio`, `fecha_fin`, `estado`, `id_cliente`, `id_tarea`, `id_empleado`, `horas`, `material`, `observaciones`, `firma_cliente`, `activo`) VALUES
(1, 1, '', '2026-03-26 20:11:47', '2026-03-26 20:11:57', 'Cerrado', 10, 19, NULL, NULL, NULL, NULL, NULL, 1),
(2, 1, 'adasdasdadasdasdasd', '2026-03-26 20:13:31', '2026-03-26 20:22:43', 'Cerrado', 10, NULL, NULL, 12.00, 'asdsadasdasd', 'sadasdasd', NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `nombre`) VALUES
(1, 'Administrador'),
(3, 'Atención al Cliente'),
(2, 'Técnico');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarea`
--

CREATE TABLE `tarea` (
  `id_tarea` int(11) NOT NULL,
  `descripcion` varchar(500) NOT NULL,
  `fecha_alta` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_fin` datetime DEFAULT NULL,
  `importancia` enum('Baja','Normal','Alta','Urgente') NOT NULL DEFAULT 'Normal',
  `estado` enum('Pendiente','En proceso','Finalizada','Cancelada') NOT NULL DEFAULT 'Pendiente',
  `persona_contacto` varchar(100) DEFAULT NULL,
  `telefono_contacto` varchar(20) DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_departamento` int(11) DEFAULT NULL,
  `id_empresa` int(11) NOT NULL,
  `id_usuario_creador` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tarea`
--

INSERT INTO `tarea` (`id_tarea`, `descripcion`, `fecha_alta`, `fecha_fin`, `importancia`, `estado`, `persona_contacto`, `telefono_contacto`, `id_empleado`, `id_cliente`, `id_departamento`, `id_empresa`, `id_usuario_creador`) VALUES
(18, 'Prueba aviso', '2026-03-24 12:56:54', '2026-03-25 19:16:46', 'Normal', 'Finalizada', NULL, NULL, 19, 10, NULL, 1, 5),
(19, 'Existe un problema con la clonación de GitHub', '2026-03-25 19:14:50', '2026-03-26 20:11:57', 'Urgente', 'Finalizada', 'Angel', '65487931', 19, 15, NULL, 1, 9),
(20, 'Aviso de prueba ', '2026-03-25 19:17:14', '2026-03-25 19:21:32', 'Alta', 'Cancelada', NULL, NULL, NULL, 10, NULL, 1, 12);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `id_empresa` int(11) NOT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `email`, `password_hash`, `activo`, `id_empresa`, `id_empleado`, `created_at`) VALUES
(5, 'Admin', 'angel@easyparte.es', '$2y$10$lS3nUlgTuDzKEcHsTkHt8uBU1wX8CicGr0GwXoFOqM5b6POtxHXye', 1, 1, NULL, '2026-03-20 12:26:31'),
(9, 'Profesor', 'profesor@easyparte.es', '$2y$10$m54YJGGrnSEMl5KRoyrBEe.WBbLNDoFAHk7J0zjGm2g1ToGIdPWOm', 1, 1, NULL, '2026-03-20 18:17:34'),
(12, 'prueba', 'prueba@easyparte.es', '$2y$10$jK4QtCrM9eFsU7JUVY6g7OEhb6MrLttBiPvdw7.a9fhAVHfKtNO.e', 1, 1, 19, '2026-03-24 11:57:30'),
(13, 'Beatriz', 'prueba2@easyparte.es', '$2y$10$CB7snvEvPwZSLAzy3vktxeJ7XjjPNcFQRmgoIC/pA9y3So7tJvKKy', 1, 1, 21, '2026-03-25 18:18:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_rol`
--

CREATE TABLE `usuario_rol` (
  `id_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario_rol`
--

INSERT INTO `usuario_rol` (`id_usuario`, `id_rol`) VALUES
(5, 1),
(9, 1),
(12, 2),
(13, 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `nif` (`nif`),
  ADD KEY `fk_cliente_empresa` (`id_empresa`);

--
-- Indices de la tabla `departamento`
--
ALTER TABLE `departamento`
  ADD PRIMARY KEY (`id_departamento`),
  ADD KEY `fk_departamento_empresa` (`id_empresa`);

--
-- Indices de la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD PRIMARY KEY (`id_empleado`),
  ADD UNIQUE KEY `nif` (`nif`),
  ADD KEY `fk_empleado_departamento` (`id_departamento`),
  ADD KEY `fk_empleado_empresa` (`id_empresa`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id_empresa`),
  ADD UNIQUE KEY `nif` (`nif`);

--
-- Indices de la tabla `parte_trabajo`
--
ALTER TABLE `parte_trabajo`
  ADD PRIMARY KEY (`id_parte_trabajo`),
  ADD KEY `fk_parte_trabajo_cliente` (`id_cliente`),
  ADD KEY `fk_parte_trabajo_empleado` (`id_empleado`),
  ADD KEY `fk_parte_trabajo_tarea` (`id_tarea`),
  ADD KEY `fk_parte_trabajo_empresa` (`id_empresa`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `tarea`
--
ALTER TABLE `tarea`
  ADD PRIMARY KEY (`id_tarea`),
  ADD KEY `fk_tarea_empleado` (`id_empleado`),
  ADD KEY `fk_tarea_cliente` (`id_cliente`),
  ADD KEY `fk_tarea_departamento` (`id_departamento`),
  ADD KEY `fk_tarea_empresa` (`id_empresa`),
  ADD KEY `fk_tarea_usuario_creador` (`id_usuario_creador`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `id_empleado` (`id_empleado`),
  ADD KEY `fk_usuario_empresa` (`id_empresa`);

--
-- Indices de la tabla `usuario_rol`
--
ALTER TABLE `usuario_rol`
  ADD PRIMARY KEY (`id_usuario`,`id_rol`),
  ADD KEY `fk_usuario_rol_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `departamento`
--
ALTER TABLE `departamento`
  MODIFY `id_departamento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empleado`
--
ALTER TABLE `empleado`
  MODIFY `id_empleado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id_empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `parte_trabajo`
--
ALTER TABLE `parte_trabajo`
  MODIFY `id_parte_trabajo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tarea`
--
ALTER TABLE `tarea`
  MODIFY `id_tarea` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `fk_cliente_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`);

--
-- Filtros para la tabla `departamento`
--
ALTER TABLE `departamento`
  ADD CONSTRAINT `fk_departamento_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD CONSTRAINT `fk_empleado_departamento` FOREIGN KEY (`id_departamento`) REFERENCES `departamento` (`id_departamento`),
  ADD CONSTRAINT `fk_empleado_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`);

--
-- Filtros para la tabla `parte_trabajo`
--
ALTER TABLE `parte_trabajo`
  ADD CONSTRAINT `fk_parte_trabajo_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`),
  ADD CONSTRAINT `fk_parte_trabajo_empleado` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id_empleado`),
  ADD CONSTRAINT `fk_parte_trabajo_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`),
  ADD CONSTRAINT `fk_parte_trabajo_tarea` FOREIGN KEY (`id_tarea`) REFERENCES `tarea` (`id_tarea`);

--
-- Filtros para la tabla `tarea`
--
ALTER TABLE `tarea`
  ADD CONSTRAINT `fk_tarea_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`),
  ADD CONSTRAINT `fk_tarea_departamento` FOREIGN KEY (`id_departamento`) REFERENCES `departamento` (`id_departamento`),
  ADD CONSTRAINT `fk_tarea_empleado` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id_empleado`),
  ADD CONSTRAINT `fk_tarea_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`),
  ADD CONSTRAINT `fk_tarea_usuario_creador` FOREIGN KEY (`id_usuario_creador`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_empleado` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id_empleado`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_usuario_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`);

--
-- Filtros para la tabla `usuario_rol`
--
ALTER TABLE `usuario_rol`
  ADD CONSTRAINT `fk_usuario_rol_rol` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`),
  ADD CONSTRAINT `fk_usuario_rol_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
