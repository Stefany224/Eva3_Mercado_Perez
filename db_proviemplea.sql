-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-05-2026 a las 08:38:35
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
-- Base de datos: `db_proviemplea`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cv_perfil`
--

CREATE TABLE `cv_perfil` (
  `id_cv` int(11) NOT NULL,
  `rut_persona` int(11) NOT NULL,
  `codigo_ciego` varchar(20) NOT NULL,
  `resumen_laboral` text DEFAULT NULL,
  `nivel_educacional` varchar(50) DEFAULT NULL,
  `carrera` varchar(100) DEFAULT NULL,
  `renta_deseada` int(11) DEFAULT NULL,
  `jornada_deseada` varchar(50) DEFAULT NULL,
  `modalidad_deseada` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

CREATE TABLE `empresas` (
  `rut_empresa` varchar(15) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `nombre_empresa` varchar(100) NOT NULL,
  `rubro` varchar(100) NOT NULL,
  `tipo_empresa` varchar(50) NOT NULL,
  `presentacion` text DEFAULT NULL,
  `beneficios` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `nombre_contacto` varchar(100) DEFAULT NULL,
  `telefono_contacto` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados`
--

CREATE TABLE `estados` (
  `id_estado` int(11) NOT NULL,
  `nombre_estado` varchar(50) NOT NULL,
  `tipo_estado` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estados`
--

INSERT INTO `estados` (`id_estado`, `nombre_estado`, `tipo_estado`) VALUES
(1, 'Activo / Validado', 'usuario'),
(2, 'Inactivo', 'usuario'),
(3, 'Pendiente Validación', 'usuario'),
(4, 'Contactado', 'proceso'),
(5, 'Entrevista', 'proceso'),
(6, 'Seleccionado', 'proceso'),
(7, 'No seleccionado', 'proceso');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login_usuario`
--

CREATE TABLE `login_usuario` (
  `id_usuario` int(11) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL DEFAULT 3,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `login_usuario`
--

INSERT INTO `login_usuario` (`id_usuario`, `correo`, `contrasena`, `id_rol`, `id_estado`, `fecha_registro`) VALUES
(4, 'juan.perez@correo.cl', '$2y$10$CF4Of1v/2D6yOCS67Thc6uyKt/WpB4DFp7D4b/pjk8PyhlhqiWWSy', 2, 3, '2026-05-31 06:37:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas_seguimiento`
--

CREATE TABLE `notas_seguimiento` (
  `id_nota` int(11) NOT NULL,
  `id_solicitud` int(11) NOT NULL,
  `autor_rol` varchar(30) NOT NULL,
  `comentario` text NOT NULL,
  `fecha_nota` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`) VALUES
(1, 'administrador'),
(3, 'empresa'),
(2, 'persona');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_contacto`
--

CREATE TABLE `solicitudes_contacto` (
  `id_solicitud` int(11) NOT NULL,
  `rut_empresa` varchar(15) NOT NULL,
  `id_cv` int(11) NOT NULL,
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_estado_proceso` int(11) NOT NULL DEFAULT 4
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `rut` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido_paterno` varchar(50) NOT NULL,
  `apellido_materno` varchar(50) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `comuna` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cv_perfil`
--
ALTER TABLE `cv_perfil`
  ADD PRIMARY KEY (`id_cv`),
  ADD UNIQUE KEY `rut_persona` (`rut_persona`),
  ADD UNIQUE KEY `codigo_ciego` (`codigo_ciego`);

--
-- Indices de la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`rut_empresa`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `login_usuario`
--
ALTER TABLE `login_usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `id_rol` (`id_rol`),
  ADD KEY `id_estado` (`id_estado`);

--
-- Indices de la tabla `notas_seguimiento`
--
ALTER TABLE `notas_seguimiento`
  ADD PRIMARY KEY (`id_nota`),
  ADD KEY `id_solicitud` (`id_solicitud`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `nombre_rol` (`nombre_rol`);

--
-- Indices de la tabla `solicitudes_contacto`
--
ALTER TABLE `solicitudes_contacto`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `rut_empresa` (`rut_empresa`),
  ADD KEY `id_cv` (`id_cv`),
  ADD KEY `id_estado_proceso` (`id_estado_proceso`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`rut`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cv_perfil`
--
ALTER TABLE `cv_perfil`
  MODIFY `id_cv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `estados`
--
ALTER TABLE `estados`
  MODIFY `id_estado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `login_usuario`
--
ALTER TABLE `login_usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `notas_seguimiento`
--
ALTER TABLE `notas_seguimiento`
  MODIFY `id_nota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `solicitudes_contacto`
--
ALTER TABLE `solicitudes_contacto`
  MODIFY `id_solicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cv_perfil`
--
ALTER TABLE `cv_perfil`
  ADD CONSTRAINT `fk_cv_usuario` FOREIGN KEY (`rut_persona`) REFERENCES `usuario` (`rut`) ON DELETE CASCADE;

--
-- Filtros para la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD CONSTRAINT `fk_empresa_login` FOREIGN KEY (`id_usuario`) REFERENCES `login_usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `login_usuario`
--
ALTER TABLE `login_usuario`
  ADD CONSTRAINT `fk_login_estado` FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id_estado`),
  ADD CONSTRAINT `fk_login_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);

--
-- Filtros para la tabla `notas_seguimiento`
--
ALTER TABLE `notas_seguimiento`
  ADD CONSTRAINT `fk_nota_solicitud` FOREIGN KEY (`id_solicitud`) REFERENCES `solicitudes_contacto` (`id_solicitud`) ON DELETE CASCADE;

--
-- Filtros para la tabla `solicitudes_contacto`
--
ALTER TABLE `solicitudes_contacto`
  ADD CONSTRAINT `fk_solicitud_cv` FOREIGN KEY (`id_cv`) REFERENCES `cv_perfil` (`id_cv`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_solicitud_empresa` FOREIGN KEY (`rut_empresa`) REFERENCES `empresas` (`rut_empresa`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_solicitud_estado` FOREIGN KEY (`id_estado_proceso`) REFERENCES `estados` (`id_estado`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_login` FOREIGN KEY (`id_usuario`) REFERENCES `login_usuario` (`id_usuario`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
