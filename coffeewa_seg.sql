-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-01-2025 a las 18:56:20
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
-- Base de datos: `coffeewa_seg`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `rut` varchar(20) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_sitio`
--

CREATE TABLE `configuracion_sitio` (
  `id` int(11) NOT NULL,
  `logo` blob DEFAULT NULL,
  `color_primario` varchar(7) DEFAULT NULL,
  `color_secundario` varchar(7) DEFAULT NULL,
  `color_fondo` varchar(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(20) DEFAULT NULL,
  `current_status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `rut` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `numero_factura` varchar(255) NOT NULL,
  `prod_o_serv` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `current_status`, `created_at`, `rut`, `nombre`, `apellido`, `numero_factura`, `prod_o_serv`) VALUES
(17, '2177202974', 'Firma documentos financiación ', '2024-11-22 05:38:06', '10422557-8', 'Carlos ', 'Castillo ', '123456', ''),
(18, '1348313382', 'Pago pie ', '2024-11-22 05:39:20', '15610957-6', 'Makarena ', 'Romero ', '654321', ''),
(19, '7317819679', 'Inicio compra ', '2024-12-02 07:18:07', '9652704-7', 'edith', 'romero', '23456', ''),
(23, '5582301290', 'Salida de bodega', '2024-12-03 07:28:03', '13717122-8', 'Ricardo ', 'Castillo ', '456778', ''),
(26, '3484610666', 'Firma documentos financiación ', '2024-12-10 05:43:31', '11628429-4', 'Mauricio', 'Gomez', '123456', 'DRON XQB2-500'),
(27, '6264885253', 'Inicio compra ', '2024-12-16 23:53:06', '15218494-8', 'Alexis', 'Munoz', '1001', 'Test'),
(28, '9644862199', 'Pago pie ', '2024-12-17 05:41:38', '10422557-8', 'Carlos', 'Castillo', '9876543', 'pc hp full'),
(29, '4943848255', 'Inicio compra ', '2024-12-23 18:46:57', '10422557-8', 'carlos', 'castillo', '123568', 'nuevo prod'),
(30, '3427438430', 'Inicio compra ', '2024-12-23 19:04:28', '10422557-8', 'carlos', 'castillo', '235346456', 'test2'),
(31, '5333416667', 'Inicio compra ', '2024-12-23 19:07:58', '10422557-8', 'carlos', 'castillo', '0979886875', 'test3'),
(32, '6077585185', 'Inicio compra ', '2024-12-23 19:09:43', '10422557-8', 'carlos', 'castillo', '097986875', 'test4'),
(33, '6586550969', 'Inicio compra ', '2024-12-23 19:19:58', '10422557-8', 'carlos', 'castillo', '983459347', 'test5'),
(34, '3456728006', 'Inicio compra ', '2024-12-25 19:04:05', '10422557-8', 'carlos', 'castillo', '9836459835', 'test6'),
(35, '8469959368', 'Pago pie ', '2024-12-25 23:18:25', '10422557-8', 'carlos', 'castillo', '453465348', 'test7'),
(36, '8381957009', 'Inicio compra', '2024-12-26 04:45:29', '10422557-8', 'carlos', 'castillo', '878', 'test8'),
(37, '7917765277', 'Inicio compra ', '2024-12-26 05:13:08', '10422557-8', 'carlos', 'castillo', '8998', 'test10'),
(38, '3703778778', 'Pago pie ', '2025-01-01 22:41:33', '10422557-8', 'Carlos ', 'Castillo ', '78575', 'test11'),
(39, '3596319493', 'Pago pie ', '2025-01-02 00:38:58', '10422557-8', 'Carlos ', 'Castillo ', '7686', 'test12'),
(40, '8596420640', 'Inicio compra ', '2025-01-04 20:20:28', '10422557-8', 'Carlos ', 'Castillo ', '75788578', 'perro');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_status`
--

CREATE TABLE `order_status` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `order_status`
--

INSERT INTO `order_status` (`id`, `order_id`, `status`, `timestamp`) VALUES
(67, NULL, 'Inicio compra ', '2024-11-22 05:36:19'),
(68, NULL, 'Pago pie ', '2024-11-22 05:36:27'),
(69, NULL, 'Firma documentos financiación ', '2024-11-22 05:36:37'),
(70, NULL, 'Salida de bodega', '2024-11-22 05:36:47'),
(71, NULL, 'En tránsito ', '2024-11-22 05:36:56'),
(72, NULL, 'En consecionario ', '2024-11-22 05:37:04'),
(73, NULL, 'Equipamiento ', '2024-11-22 05:37:13'),
(74, NULL, 'Listo para entrega ', '2024-11-22 05:37:26'),
(75, 17, 'Inicio compra ', '2024-11-22 05:38:06'),
(77, 17, 'Pago pie ', '2024-11-22 05:40:29'),
(79, 18, 'Inicio compra ', '2024-11-22 17:17:07'),
(81, NULL, 'Entregado', '2024-12-02 06:06:24'),
(82, NULL, 'Cancelado', '2024-12-02 06:26:18'),
(83, 19, 'Inicio compra ', '2024-12-02 07:18:07'),
(88, NULL, 'En espera', '2024-12-04 03:35:32'),
(89, 23, 'Pago pie ', '2024-12-04 03:36:05'),
(90, 17, 'Firma documentos financiación ', '2024-12-04 18:10:25'),
(91, 18, 'Pago pie ', '2024-12-07 04:35:01'),
(92, 23, 'Salida de bodega', '2024-12-08 17:30:20'),
(97, 26, 'Inicio compra ', '2024-12-10 05:43:31'),
(98, 26, 'Pago pie ', '2024-12-10 05:44:01'),
(99, 26, 'Firma documentos financiación ', '2024-12-11 18:37:16'),
(100, 27, 'Inicio compra ', '2024-12-16 23:53:06'),
(101, NULL, 'Reembolsado', '2024-12-17 05:39:32'),
(102, 28, 'Inicio compra ', '2024-12-17 05:41:38'),
(103, 28, 'Pago pie ', '2024-12-17 05:41:57'),
(104, 29, 'Inicio compra ', '2024-12-23 18:46:57'),
(105, 30, 'Inicio compra ', '2024-12-23 19:04:28'),
(106, 31, 'Inicio compra ', '2024-12-23 19:07:58'),
(107, 32, 'Inicio compra ', '2024-12-23 19:09:43'),
(108, 33, 'Inicio compra ', '2024-12-23 19:19:58'),
(109, 34, 'Inicio compra ', '2024-12-25 19:04:05'),
(110, 35, 'Inicio compra ', '2024-12-25 23:18:25'),
(111, 36, 'Inicio compra', '2024-12-26 04:45:29'),
(112, 37, 'Inicio compra ', '2024-12-26 05:13:08'),
(113, 38, 'Inicio compra ', '2025-01-01 22:41:33'),
(114, 35, 'Pago pie ', '2025-01-01 22:42:22'),
(115, 38, 'Pago pie ', '2025-01-01 22:43:51'),
(116, 39, 'Inicio compra ', '2025-01-02 00:38:58'),
(117, 39, 'Pago pie ', '2025-01-02 00:39:33'),
(118, 40, 'Inicio compra ', '2025-01-04 20:20:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`) VALUES
(1, 'carlock', '$2y$10$qhJAW/zDwgT.CAwQ8pqpBOuSTjHA/dMWpMSPBv7te0M.Jd4kArHyO'),
(2, 'carlos', '$2y$10$peN0Z/8lE1FKwYdRFvkZZuusyq0e8i9BIqR1rFdCO7.oySkpnujnW'),
(3, 'Mgomez', '$2y$10$0h7njQr8cldWltgkoDHbXOjfbsb/Fxiz25QFDkLPWI5WbjEQReIFW'),
(4, 'ricardo', '$2y$10$5d4oALg0EuKxxggFIt6IkeTZXL5SXiXTxb4QtODUK.fZohz.nIM.O');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indices de la tabla `configuracion_sitio`
--
ALTER TABLE `configuracion_sitio`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`);

--
-- Indices de la tabla `order_status`
--
ALTER TABLE `order_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuracion_sitio`
--
ALTER TABLE `configuracion_sitio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `order_status`
--
ALTER TABLE `order_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
