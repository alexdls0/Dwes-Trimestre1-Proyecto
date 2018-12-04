CREATE TABLE `usuario` (
 `id` bigint(20) NOT NULL AUTO_INCREMENT,
 `correo` varchar(60) NOT NULL,
 `alias` varchar(30) DEFAULT NULL,
 `nombre` varchar(50) NOT NULL,
 `clave` varchar(255) NOT NULL,
 `activo` tinyint(1) NOT NULL DEFAULT '0',
 `admin` tinyint(1) NOT NULL DEFAULT '0',
 `fechaalta` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`),
 UNIQUE KEY `correo` (`correo`),
 UNIQUE KEY `alias` (`alias`)
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8
