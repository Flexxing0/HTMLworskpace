-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 23-06-2025 a las 03:35:00
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `productos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciudades`
--

DROP TABLE IF EXISTS `ciudades`;
CREATE TABLE IF NOT EXISTS `ciudades` (
  `id_ciudad` int NOT NULL AUTO_INCREMENT,
  `nombre_ciudad` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `id_pais` int NOT NULL,
  PRIMARY KEY (`id_ciudad`),
  UNIQUE KEY `nombre_ciudad_UNIQUE` (`nombre_ciudad`),
  KEY `fk_id_pais_idx` (`id_pais`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ciudades`
--

INSERT INTO `ciudades` (`id_ciudad`, `nombre_ciudad`, `id_pais`) VALUES
(1, 'Buenos Aires', 1),
(2, 'Comodoro Rivadavia', 1),
(3, 'Trelew', 1),
(4, 'París', 2),
(5, 'Marsella', 2),
(6, 'Lyon', 2),
(7, 'Madrid', 3),
(8, 'Barcelona', 3),
(9, 'Sevilla', 3),
(10, 'Ciudad de México', 4),
(11, 'Guadalajara', 4),
(12, 'Monterrey', 4),
(13, 'Berlín', 5),
(14, 'Múnich', 5),
(15, 'Hamburgo', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paises`
--

DROP TABLE IF EXISTS `paises`;
CREATE TABLE IF NOT EXISTS `paises` (
  `id_pais` int NOT NULL AUTO_INCREMENT,
  `nombre_pais` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_pais`),
  UNIQUE KEY `nombre_pais_UNIQUE` (`nombre_pais`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paises`
--

INSERT INTO `paises` (`id_pais`, `nombre_pais`) VALUES
(5, 'Alemania'),
(1, 'Argentina'),
(3, 'España'),
(2, 'Francia'),
(4, 'México');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

DROP TABLE IF EXISTS `productos`;
CREATE TABLE IF NOT EXISTS `productos` (
  `id_producto` int NOT NULL AUTO_INCREMENT,
  `nombre_producto` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  `precio` decimal(10,2) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_producto`),
  UNIQUE KEY `nombre_producto` (`nombre_producto`),
  UNIQUE KEY `nombre_producto_UNIQUE` (`nombre_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre_producto`, `descripcion`, `precio`, `stock`) VALUES
(1, 'Laptop Ultrabook Pro', 'Potente laptop con procesador i7, 16GB RAM y 512GB SSD.', 1250.00, 15),
(2, 'Monitor Curvo 27\"', 'Monitor LED curvo de alta resolución para una inmersión total.', 350.50, 8),
(3, 'Teclado Mecánico RGB', 'Teclado mecánico con retroiluminación RGB, switches azules.', 89.99, 25),
(4, 'Mouse Gaming Inalámbrico', 'Mouse ergonómico con sensor de alta precisión, inalámbrico.', 49.99, 30),
(5, 'Auriculares Noise Cancelling', 'Auriculares con cancelación de ruido activa, ideal para viajes.', 120.00, 12),
(6, 'Disco Duro Externo 2TB', 'Disco duro portátil de 2TB con conexión USB 3.0.', 70.25, 20),
(7, 'Webcam Full HD', 'Cámara web con resolución 1080p y micrófono integrado.', 30.00, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarifas_envio`
--

DROP TABLE IF EXISTS `tarifas_envio`;
CREATE TABLE IF NOT EXISTS `tarifas_envio` (
  `id_tarifa` int NOT NULL AUTO_INCREMENT,
  `id_ciudad` int NOT NULL,
  `costo_envio` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_tarifa`),
  UNIQUE KEY `id_ciudad` (`id_ciudad`),
  UNIQUE KEY `id_ciudad_UNIQUE` (`id_ciudad`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tarifas_envio`
--

INSERT INTO `tarifas_envio` (`id_tarifa`, `id_ciudad`, `costo_envio`) VALUES
(1, 1, 150.00),
(2, 2, 300.00),
(3, 3, 250.00),
(4, 4, 15.00),
(5, 5, 18.00),
(6, 7, 12.00),
(7, 10, 80.00),
(8, 13, 20.00);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ciudades`
--
ALTER TABLE `ciudades`
  ADD CONSTRAINT `fk_id_pais` FOREIGN KEY (`id_pais`) REFERENCES `paises` (`id_pais`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tarifas_envio`
--
ALTER TABLE `tarifas_envio`
  ADD CONSTRAINT `fk_ciudad_tarifa` FOREIGN KEY (`id_ciudad`) REFERENCES `ciudades` (`id_ciudad`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
