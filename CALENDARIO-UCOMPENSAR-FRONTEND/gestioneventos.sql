-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-11-2025 a las 02:49:58
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
-- Base de datos: `gestioneventos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaciones_profesores`
--

CREATE TABLE `asignaciones_profesores` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `evento_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asignaciones_profesores`
--

INSERT INTO `asignaciones_profesores` (`id`, `usuario_id`, `evento_id`) VALUES
(1, 2, 1),
(2, 3, 2),
(3, 2, 4),
(4, 3, 5),
(5, 2, 6),
(6, 3, 7),
(7, 2, 9),
(8, 3, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etiquetas`
--

CREATE TABLE `etiquetas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `etiquetas`
--

INSERT INTO `etiquetas` (`id`, `nombre`) VALUES
(1, 'Académico'),
(9, 'Cultural'),
(4, 'Deportivo'),
(10, 'Evaluación'),
(2, 'Importante'),
(3, 'Institucional'),
(8, 'Presencial'),
(6, 'Seminario'),
(5, 'Taller'),
(7, 'Virtual');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `hora` time DEFAULT NULL,
  `lugar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id`, `nombre`, `descripcion`, `imagen`, `tipo`, `fecha`, `hora`, `lugar`) VALUES
(1, 'Reunión Académica', 'Revisión de plan de estudios', NULL, 'Académico', '2025-10-15 09:00:00', NULL, NULL),
(2, 'Charla de Liderazgo', 'Charla motivacional para estudiantes', NULL, 'Institucional', '2025-10-22 14:00:00', NULL, NULL),
(3, 'Partido Intercarreras', 'Competencia deportiva anual', NULL, 'Deportivo', '2025-10-18 16:00:00', NULL, NULL),
(4, 'Taller de Programación', 'Aprende fundamentos de JavaScript', NULL, 'Taller', '2025-10-20 10:00:00', NULL, NULL),
(5, 'Evaluación Final', 'Examen del curso de bases de datos', NULL, 'Académico', '2025-11-02 08:00:00', NULL, NULL),
(6, 'Seminario de Innovación', 'Conferencias sobre nuevas tecnologías', NULL, 'Seminario', '2025-11-10 09:00:00', NULL, NULL),
(7, 'Festival Cultural', 'Presentaciones artísticas y música', NULL, 'Cultural', '2025-10-25 18:00:00', NULL, NULL),
(8, 'Jornada de Bienestar', 'Actividades de salud y recreación', NULL, 'Institucional', '2025-11-05 09:00:00', NULL, NULL),
(9, 'Sesión Virtual de Tutoría', 'Asesoría en línea de matemáticas', NULL, 'Virtual', '2025-10-28 17:00:00', NULL, NULL),
(10, 'Reunión de Profesores', 'Planificación académica 2025', NULL, 'Académico', '2025-10-30 11:00:00', NULL, NULL),
(12, 'Prueba Numero 1', 'Prueba', 'evento_69111242a8932.jpg', 'Gamer', '2025-10-13 07:50:00', NULL, NULL),
(13, 'pppppppp', '1121212', '', NULL, '2025-11-12 00:00:00', '08:50:00', '1212121');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento_etiquetas`
--

CREATE TABLE `evento_etiquetas` (
  `id` int(11) NOT NULL,
  `evento_id` int(11) NOT NULL,
  `etiqueta_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `evento_etiquetas`
--

INSERT INTO `evento_etiquetas` (`id`, `evento_id`, `etiqueta_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 3),
(4, 2, 2),
(5, 3, 4),
(6, 4, 5),
(7, 4, 7),
(8, 5, 10),
(9, 6, 6),
(10, 6, 7),
(11, 7, 9),
(12, 8, 3),
(13, 9, 7),
(14, 10, 1),
(15, 10, 2),
(35, 12, 2),
(36, 12, 3),
(37, 12, 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gestion_eventos`
--

CREATE TABLE `gestion_eventos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `evento_id` int(11) NOT NULL,
  `accion` varchar(100) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gestion_eventos`
--

INSERT INTO `gestion_eventos` (`id`, `usuario_id`, `evento_id`, `accion`, `fecha`) VALUES
(1, 1, 1, 'Creó evento', '2025-10-12 23:49:49'),
(2, 1, 2, 'Aprobó evento', '2025-10-12 23:49:49'),
(3, 2, 4, 'Asignado como profesor', '2025-10-12 23:49:49'),
(4, 3, 5, 'Asignado como profesor', '2025-10-12 23:49:49'),
(5, 1, 3, 'Publicó evento', '2025-10-12 23:49:49'),
(6, 1, 6, 'Publicó evento', '2025-10-12 23:49:49'),
(7, 2, 7, 'Editó evento', '2025-10-12 23:49:49'),
(8, 3, 8, 'Eliminó evento', '2025-10-12 23:49:49'),
(9, 1, 9, 'Aprobó evento', '2025-10-12 23:49:49'),
(10, 1, 10, 'Cerró evento', '2025-10-12 23:49:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones`
--

CREATE TABLE `inscripciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `evento_id` int(11) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `asistencia` varchar(20) DEFAULT 'Pendiente',
  `estado` varchar(20) DEFAULT 'Activo',
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inscripciones`
--

INSERT INTO `inscripciones` (`id`, `usuario_id`, `evento_id`, `fecha_registro`, `asistencia`, `estado`, `observaciones`) VALUES
(1, 4, 2, '2025-10-12 23:49:49', 'Asistió', 'Activo', 'Excelente participación'),
(2, 5, 2, '2025-10-12 23:49:49', 'Pendiente', 'Activo', 'Confirmará asistencia'),
(3, 6, 3, '2025-10-12 23:49:49', 'Asistió', 'Activo', 'Buen desempeño'),
(4, 7, 3, '2025-10-12 23:49:49', 'Asistió', 'Activo', 'Mostró interés'),
(5, 8, 4, '2025-10-12 23:49:49', 'Pendiente', 'Activo', 'Esperando confirmación'),
(6, 9, 4, '2025-10-12 23:49:49', 'Pendiente', 'Activo', 'Nuevo participante'),
(7, 10, 7, '2025-10-12 23:49:49', 'Asistió', 'Activo', 'Buena presentación'),
(8, 4, 6, '2025-10-12 23:49:49', 'Pendiente', 'Activo', 'Primera participación'),
(9, 5, 8, '2025-10-12 23:49:49', 'Asistió', 'Activo', 'Buen aporte'),
(10, 6, 9, '2025-10-12 23:49:49', 'Pendiente', 'Activo', 'Conectará desde casa'),
(11, 4, 12, '2025-11-09 16:16:46', '0', 'activo', NULL),
(12, 1, 1, '2025-11-09 16:49:00', '0', 'activo', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticias`
--

CREATE TABLE `noticias` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `contenido` text NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `es_urgente` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `noticias`
--

INSERT INTO `noticias` (`id`, `titulo`, `contenido`, `imagen`, `fecha`, `es_urgente`) VALUES
(1, 'PRUEBA1', 'PRUEBA1', '', '2025-11-10 16:19:55', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `clave`, `rol`) VALUES
(1, 'Administrador', 'admin@correo.com', '$2y$10$Y8kZFTDlcSaw0xqfPt/VlOMoUkOquUTQ5pzGP1x80FoS21U16280e', 'admin'),
(2, 'Profesor Juan', 'juan@correo.com', '$2y$10$V06q8vzby.KHpEiiXjb6qOsnMxyFqfu/mUlpTNva9TbQozd8Y4KKG', 'profesor'),
(3, 'Profesor María', 'maria@correo.com', '$2y$10$qkjwc1wEUm5g3Cr.H4EiaulD7Ox7gd2kcPxJ1ZfTahVZy7R7sQtdC', 'profesor'),
(4, 'Estudiante Pedro', 'pedro@correo.com', '$2y$10$Y8kZFTDlcSaw0xqfPt/VlOMoUkOquUTQ5pzGP1x80FoS21U16280e', 'estudiante'),
(5, 'Estudiante Laura', 'laura@correo.com', '$2y$10$bIwizkfiOYe7pz9r33ybYuap3Y5svIAz1fwbAWLfOEDUm38QPtWLG', 'estudiante'),
(6, 'Estudiante Carlos', 'carlos@correo.com', '$2y$10$Yf6MTG7WZD9crMpegYieTeujw92pPjBGyPD53FWneXEIZQ6CcZeDi', 'estudiante'),
(7, 'Estudiante Sofía', 'sofia@correo.com', '$2y$10$k5WLlLeEJEn8OEDQCE6vYuvH1Y78GbtDEFzmpxE2yl.6CUN3cz39y', 'estudiante'),
(8, 'Estudiante Andrés', 'andres@correo.com', '$2y$10$jKpLXCLM987f07PWXhP7sOd8ZUynmSwEirHua.zEr5PfpAXiyeLNu', 'estudiante'),
(9, 'Estudiante Valentina', 'valentina@correo.com', '$2y$10$bkoj/CfyWKRN2lsXA1WH9.9bWSIpj8577Vc4g1ic.GdJ0zeKYcfMa', 'estudiante'),
(10, 'Estudiante Luis', 'luis@correo.com', '$2y$10$G66ciVYNjjji5/OM8rSrBuTqF8S04CRs4ZxNhFAtsn3kr0tmObbXO', 'estudiante');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_etiquetas`
--

CREATE TABLE `usuario_etiquetas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `etiqueta_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asignaciones_profesores`
--
ALTER TABLE `asignaciones_profesores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `evento_id` (`evento_id`);

--
-- Indices de la tabla `etiquetas`
--
ALTER TABLE `etiquetas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `evento_etiquetas`
--
ALTER TABLE `evento_etiquetas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `evento_id` (`evento_id`),
  ADD KEY `etiqueta_id` (`etiqueta_id`);

--
-- Indices de la tabla `gestion_eventos`
--
ALTER TABLE `gestion_eventos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `evento_id` (`evento_id`);

--
-- Indices de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `evento_id` (`evento_id`);

--
-- Indices de la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `usuario_etiquetas`
--
ALTER TABLE `usuario_etiquetas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `etiqueta_id` (`etiqueta_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asignaciones_profesores`
--
ALTER TABLE `asignaciones_profesores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `etiquetas`
--
ALTER TABLE `etiquetas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `evento_etiquetas`
--
ALTER TABLE `evento_etiquetas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `gestion_eventos`
--
ALTER TABLE `gestion_eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `noticias`
--
ALTER TABLE `noticias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `usuario_etiquetas`
--
ALTER TABLE `usuario_etiquetas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asignaciones_profesores`
--
ALTER TABLE `asignaciones_profesores`
  ADD CONSTRAINT `asignaciones_profesores_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `asignaciones_profesores_ibfk_2` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `evento_etiquetas`
--
ALTER TABLE `evento_etiquetas`
  ADD CONSTRAINT `evento_etiquetas_ibfk_1` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evento_etiquetas_ibfk_2` FOREIGN KEY (`etiqueta_id`) REFERENCES `etiquetas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `gestion_eventos`
--
ALTER TABLE `gestion_eventos`
  ADD CONSTRAINT `gestion_eventos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gestion_eventos_ibfk_2` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD CONSTRAINT `inscripciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscripciones_ibfk_2` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario_etiquetas`
--
ALTER TABLE `usuario_etiquetas`
  ADD CONSTRAINT `usuario_etiquetas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuario_etiquetas_ibfk_2` FOREIGN KEY (`etiqueta_id`) REFERENCES `etiquetas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
