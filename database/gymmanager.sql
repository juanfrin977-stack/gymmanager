-- =========================================================
-- GymManager - Base de datos
-- Compatible con MySQL / MariaDB (XAMPP)
--
-- Tablas:
--   usuarios  -> socios registrados
--   clases    -> actividades del gimnasio y su aforo
--   horarios  -> días y hora en que se imparte cada clase
--   reservas  -> plaza de un usuario en una clase y fecha
--   mensajes  -> mensajes del formulario de contacto
-- =========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `gymmanager`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `gymmanager`;

-- Se borran en orden inverso a las dependencias
DROP TABLE IF EXISTS `mensajes`;
DROP TABLE IF EXISTS `reservas`;
DROP TABLE IF EXISTS `horarios`;
DROP TABLE IF EXISTS `clases`;
DROP TABLE IF EXISTS `usuarios`;


-- ---------------------------------------------------------
-- USUARIOS
-- ---------------------------------------------------------

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`) VALUES
(1, 'Juan', 'juan@test.com', '$2y$10$rrQ1C4HBvmBZ1EdjMwQOOurksgo6lSu/DFoIAKdOBkzIy1NgaDCuy'),
(2, 'pepe', 'pepe@test.com', '$2y$10$fbCjvHofA4Dwmvo9ziDuN../RWGW/WZizZV.EnCD8KVVLG8iLqUoG'),
(3, 'Fran', 'Fran@test.com', '$2y$10$6IY0PZ/TyTWUxXCWla6R0eEVQJqZZSa8RwTV40xbU0Z8hW7XTW156'),
(4, 'sara', 'sara@test.com', '$2y$10$pdUDJoZIkFi6C5I0qM3sGeRN89fWaU6L2pnUneZsHL0m1BUaZRZfO');


-- ---------------------------------------------------------
-- CLASES
-- ---------------------------------------------------------

CREATE TABLE `clases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `icono` varchar(10) NOT NULL DEFAULT '',
  `descripcion_corta` varchar(150) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `plazas` int(11) NOT NULL DEFAULT 20,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  CONSTRAINT `chk_plazas` CHECK (`plazas` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `clases` (`id`, `nombre`, `icono`, `descripcion_corta`, `descripcion`, `plazas`) VALUES
(1, 'Musculación', '💪', 'Entrenamiento de fuerza y musculación.', 'Entrenamiento de fuerza para mejorar tu masa muscular y condición física.', 20),
(2, 'Yoga', '🧘', 'Mejora tu flexibilidad y bienestar.', 'Sesiones de yoga para mejorar la flexibilidad, equilibrio y relajación.', 20),
(3, 'CrossFit', '🔥', 'Entrenamientos intensos y dinámicos.', 'Entrenamiento de alta intensidad para mejorar fuerza, resistencia y condición física.', 20);


-- ---------------------------------------------------------
-- HORARIOS
-- dia_semana: 1 = lunes ... 7 = domingo
-- ---------------------------------------------------------

CREATE TABLE `horarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clase_id` int(11) NOT NULL,
  `dia_semana` tinyint(4) NOT NULL,
  `hora` time NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `horario_unico` (`clase_id`, `dia_semana`),
  CONSTRAINT `chk_dia_semana` CHECK (`dia_semana` BETWEEN 1 AND 7),
  CONSTRAINT `fk_horarios_clase` FOREIGN KEY (`clase_id`) REFERENCES `clases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `horarios` (`clase_id`, `dia_semana`, `hora`) VALUES
(1, 1, '18:00:00'),
(1, 3, '18:00:00'),
(2, 2, '17:00:00'),
(2, 4, '17:00:00'),
(3, 1, '19:00:00'),
(3, 5, '19:00:00');


-- ---------------------------------------------------------
-- RESERVAS
-- Un usuario no puede reservar dos veces la misma clase y fecha
-- ---------------------------------------------------------

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `clase_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `fecha_reserva` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `reserva_unica` (`usuario_id`, `clase_id`, `fecha`),
  KEY `clase_fecha` (`clase_id`, `fecha`),
  CONSTRAINT `fk_reservas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reservas_clase` FOREIGN KEY (`clase_id`) REFERENCES `clases` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `reservas` (`id`, `usuario_id`, `clase_id`, `fecha`, `fecha_reserva`) VALUES
(19, 1, 1, '2026-09-28', '2026-09-26 09:08:58'),
(20, 1, 2, '2026-09-29', '2026-09-26 09:13:26'),
(21, 1, 3, '2026-10-09', '2026-09-26 09:44:55'),
(22, 1, 1, '2026-10-05', '2026-09-27 16:06:07'),
(23, 2, 2, '2026-10-22', '2026-10-16 11:46:46'),
(24, 2, 3, '2026-11-06', '2026-10-16 12:59:16'),
(27, 3, 2, '2026-11-05', '2026-10-28 16:35:49'),
(28, 1, 2, '2026-11-12', '2026-10-28 16:55:33'),
(29, 2, 3, '2026-11-09', '2026-10-28 17:19:21'),
(30, 2, 1, '2026-11-04', '2026-10-28 17:49:21'),
(31, 2, 2, '2026-11-03', '2026-10-28 18:07:31'),
(32, 2, 1, '2026-11-09', '2026-10-28 18:11:11'),
(33, 2, 1, '2026-10-28', '2026-10-28 18:17:35');


-- ---------------------------------------------------------
-- MENSAJES
-- usuario_id queda vacío si el mensaje se envía sin sesión
-- ---------------------------------------------------------

CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_mensajes_usuario` (`usuario_id`),
  CONSTRAINT `fk_mensajes_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `mensajes` (`id`, `usuario_id`, `nombre`, `email`, `mensaje`, `fecha_envio`) VALUES
(1, NULL, 'pepe', 'pepe@test.com', 'no hay toallas', '2026-10-28 17:36:43'),
(2, 2, 'pepe', 'pepe@test.com', 'no hay pesas de 5 kg', '2026-10-28 17:37:10'),
(3, NULL, 'Juan', 'juan@test.com', 'hay agua en las mancuernas', '2026-10-28 18:24:59');
