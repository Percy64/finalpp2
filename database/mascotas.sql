/*
SQLyog Ultimate v12.09 (32 bit)
MySQL - 10.4.32-MariaDB : Database - mascotas_db
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`mascotas_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `mascotas_db`;

/*Table structure for table `codigos_qr` */

DROP TABLE IF EXISTS `codigos_qr`;

CREATE TABLE `codigos_qr` (
  `id_qr` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(255) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_qr`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `codigos_qr` */

insert  into `codigos_qr`(`id_qr`,`codigo`,`fecha_creacion`,`activo`) values (1,'http://localhost/login/vistas/mascotas/perfil.php?id=72','2025-10-14 21:50:33',1);

/*Table structure for table `fotos_mascotas` */

DROP TABLE IF EXISTS `fotos_mascotas`;

CREATE TABLE `fotos_mascotas` (
  `id_foto` int(11) NOT NULL AUTO_INCREMENT,
  `id_mascota` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `fecha_subida` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_foto`),
  KEY `id_mascota` (`id_mascota`),
  CONSTRAINT `fotos_mascotas_ibfk_1` FOREIGN KEY (`id_mascota`) REFERENCES `mascotas` (`id_mascota`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `fotos_mascotas` */

insert  into `fotos_mascotas`(`id_foto`,`id_mascota`,`url`,`descripcion`,`fecha_subida`) values (1,72,'assets/images/mascotas/mascota_1760488784_819b820b.jfif',NULL,'2025-10-14 21:39:51'),(2,77,'assets/images/mascotas/mascota_1761929890_6904eaa21d2e0.jpg',NULL,'2025-10-31 13:58:10'),(3,77,'assets/images/mascotas/mascota_1761930001_6904eb11b9b94.jpg',NULL,'2025-10-31 14:00:01'),(5,77,'assets/images/mascotas/mascota_1761932577_6904f521b942b.jpg',NULL,'2025-10-31 14:42:57'),(6,75,'assets/images/mascotas/mascota_1762210105_69093139abe4f.jpg',NULL,'2025-11-03 19:48:25');

/*Table structure for table `historial_medico` */

DROP TABLE IF EXISTS `historial_medico`;

    CREATE TABLE `historial_medico` (
      `id_historial` int(11) NOT NULL AUTO_INCREMENT,
      `id_mascota` int(11) NOT NULL,
      `fecha` date NOT NULL,
      `descripcion` text DEFAULT NULL,
      `veterinario` varchar(100) DEFAULT NULL,
      PRIMARY KEY (`id_historial`),
      KEY `id_mascota` (`id_mascota`),
      CONSTRAINT `historial_medico_ibfk_1` FOREIGN KEY (`id_mascota`) REFERENCES `mascotas` (`id_mascota`)
    ) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `historial_medico` */

/*Table structure for table `mascotas` */

DROP TABLE IF EXISTS `mascotas`;

CREATE TABLE `mascotas` (
  `id_mascota` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `especie` varchar(50) NOT NULL,
  `raza` varchar(100) DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `genero` enum('macho','hembra') DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `id` int(11) NOT NULL,
  `id_qr` int(11) DEFAULT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `estado` enum('normal','perdida','encontrada') DEFAULT 'normal',
  `fecha_estado` datetime DEFAULT NULL,
  `descripcion_estado` text DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `perdido` tinyint(1) DEFAULT 0,
  `ultima_ubicacion` varchar(255) DEFAULT NULL,
  `ultima_lat` decimal(10,7) DEFAULT NULL,
  `ultima_lng` decimal(10,7) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_mascota`),
  UNIQUE KEY `id_qr` (`id_qr`),
  KEY `id_dueño` (`id`),
  KEY `idx_ultima_coords` (`ultima_lat`,`ultima_lng`),
  CONSTRAINT `mascotas_ibfk_1` FOREIGN KEY (`id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `mascotas_ibfk_2` FOREIGN KEY (`id_qr`) REFERENCES `codigos_qr` (`id_qr`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `mascotas` */

insert  into `mascotas`(`id_mascota`,`nombre`,`especie`,`raza`,`edad`,`genero`,`color`,`id`,`id_qr`,`foto_url`,`estado`,`fecha_estado`,`descripcion_estado`,`fecha_registro`,`perdido`,`ultima_ubicacion`,`ultima_lat`,`ultima_lng`,`descripcion`) values (72,'botitas','gato',NULL,4,NULL,NULL,13,1,'assets/images/mascotas/mascota_1760488784_819b820b.jfif','normal',NULL,NULL,'2025-10-25 23:10:52',0,NULL,NULL,NULL,NULL),(75,'simba','Gato','gris',2,'macho','atigrado',15,NULL,'assets/images/mascotas/mascota_1762209641_69092f69f3136.jpg','normal',NULL,NULL,'2025-10-28 22:09:57',0,NULL,'-32.9406310','-60.6410990','es gay'),(76,'nala','Gato','calico',4,'hembra','vaios',16,NULL,'assets/images/mascotas/mascota_1761865256_6903ee28a205e.jpg','perdida','2025-12-09 12:21:37',NULL,'2025-10-30 20:00:56',1,NULL,NULL,NULL,NULL),(77,'nala','Gato','calico',4,'hembra','varios',16,NULL,'assets/images/mascotas/mascota_1761930001_6904eb11b9b94.jpg','perdida','2025-12-09 12:21:37',NULL,'2025-10-31 11:04:11',1,NULL,NULL,NULL,NULL),(78,'luci','Perro','border coli',0,'hembra','blanca',18,NULL,'assets/images/mascotas/mascota_1762470622_690d2ade03d57.jpg','normal',NULL,NULL,'2025-11-06 20:10:22',0,NULL,NULL,NULL,'es muy inquieta'),(79,'luci','Perro','golden',0,'macho','blanco y negro',20,NULL,'assets/images/mascotas/mascota_1763073371_69165d5b24854.jpg','perdida','2025-12-09 12:21:37',NULL,'2025-11-13 19:36:11',1,NULL,NULL,NULL,'es muy grande');

/*Table structure for table `mascotas_perdidas` */

DROP TABLE IF EXISTS `mascotas_perdidas`;

CREATE TABLE `mascotas_perdidas` (
  `id_perdida` int(11) NOT NULL AUTO_INCREMENT,
  `id_mascota` int(11) NOT NULL,
  `fecha_perdida` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_encontrada` timestamp NULL DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `detalles` text DEFAULT NULL,
  `estado` enum('activo','encontrado','cancelado') NOT NULL DEFAULT 'activo',
  `usuario_reporta` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_perdida`),
  KEY `idx_mascota` (`id_mascota`),
  KEY `idx_estado` (`estado`),
  KEY `idx_coords` (`lat`,`lng`),
  KEY `idx_fecha_perdida` (`fecha_perdida`),
  KEY `mascotas_perdidas_ibfk_2` (`usuario_reporta`),
  CONSTRAINT `mascotas_perdidas_ibfk_1` FOREIGN KEY (`id_mascota`) REFERENCES `mascotas` (`id_mascota`) ON DELETE CASCADE,
  CONSTRAINT `mascotas_perdidas_ibfk_2` FOREIGN KEY (`usuario_reporta`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `mascotas_perdidas` */

insert  into `mascotas_perdidas`(`id_perdida`,`id_mascota`,`fecha_perdida`,`fecha_encontrada`,`ubicacion`,`lat`,`lng`,`detalles`,`estado`,`usuario_reporta`) values (1,77,'2025-11-06 19:39:04','2025-11-06 19:43:44','rosario','0.0000000','0.0000000','','encontrado',16),(2,77,'2025-11-06 19:44:19','2025-11-06 19:52:19','rosario','-32.9407760','-60.6411740','es negra','encontrado',16),(3,77,'2025-11-06 19:53:26','2025-11-06 20:30:37','en la florida','-32.9408020','-60.6411730','en negra mansita ','encontrado',16),(4,77,'2025-11-06 20:31:19',NULL,'en la florida','-32.9408020','-60.6411730','jryururu','activo',16),(5,79,'2025-11-13 19:42:02',NULL,'instituto','-32.9406450','-60.6410910','','activo',20);

/*Table structure for table `reportes_encontradas` */

DROP TABLE IF EXISTS `reportes_encontradas`;

CREATE TABLE `reportes_encontradas` (
  `id_reporte` int(11) NOT NULL AUTO_INCREMENT,
  `id_mascota` int(11) NOT NULL,
  `usuario_reporta` int(11) DEFAULT NULL,
  `fecha_reporte` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_reporte` varchar(45) DEFAULT NULL,
  `procesado` tinyint(1) DEFAULT 0,
  `ubicacion` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `contacto` varchar(255) DEFAULT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  PRIMARY KEY (`id_reporte`),
  KEY `id_mascota` (`id_mascota`),
  KEY `idx_coords` (`lat`,`lng`),
  KEY `reportes_encontradas_ibfk_2` (`usuario_reporta`),
  CONSTRAINT `reportes_encontradas_ibfk_1` FOREIGN KEY (`id_mascota`) REFERENCES `mascotas` (`id_mascota`) ON DELETE CASCADE,
  CONSTRAINT `reportes_encontradas_ibfk_2` FOREIGN KEY (`usuario_reporta`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `reportes_encontradas` */

/*Table structure for table `usuarios` */

DROP TABLE IF EXISTS `usuarios`;

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `codigo_pais` varchar(5) DEFAULT '+54',
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `direccion` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `foto_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*Data for the table `usuarios` */

insert  into `usuarios`(`id`,`nombre`,`apellido`,`telefono`,`codigo_pais`,`email`,`direccion`,`password`,`fecha_creacion`,`foto_url`) values (13,'cristian','merlo',NULL,'+54','cristian@gmail.com',NULL,'$2y$10$qe2TzM3GqXQgI36ylqYyRumGZzUmylPh5/e1PVOR.a4xqFzRr2Hp2','2025-10-14 21:37:33','http://localhost/login/assets/images/usuarios/user_1760488652_2ebf6e17.png'),(14,'Test','User',NULL,'+54','test@test.com',NULL,'\\.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2025-10-23 19:59:05',NULL),(15,'emanuel','merlo','3417208555','+54','emanuel@gmail.com','','$2y$10$qD4TTCo.c5w6/1H7KrIowu1jNjXwu4gdhLamDKxSwVdxi9YjqW7Q6','2025-10-23 20:05:59','assets/images/usuarios/usuario_15_1761699153.jpeg'),(16,'Emanuel','Merlo','03417208555','+54','emanuel1@gmail.com','rosario','$2y$10$XF3z8hAI16va1j59hWfycublHyTft5f85jLUcQ8ZPKKZlALAVhbrK','2025-10-29 21:32:44','assets/usuarios/usuario_1762388641_690beaa1650d0.jpg'),(17,'emanuel','test','+543417208555','+54','test@gmail.com','test123','$2y$10$GKBTqt38rhlRkuSuC7z44emwG6iJ1IW5U64OkVT9Boq3FE1CPw6QW','2025-11-06 20:08:46',NULL),(18,'emanuel','test','+543417208555','+54','test@gmail.com','test123','$2y$10$PH1RuNYPApKZZissWdHkSe7t6Z6LKLIGgkFblgDzlKKlgIMncHVky','2025-11-06 20:08:46',NULL),(19,'xxx','xxx','+541234567890','+54','xxx@gmail.com',NULL,'$2y$10$4W0bKlwYZUkyiXR3mIK0Y.PAwCJ/Apx8r3WvJJ.HvrhHYBuNds8yG','2025-11-11 21:32:48',NULL),(20,'emanuel','merlo','+543413613207','+54','emanuelmerlo150@gmail.com','corriente 353','$2y$10$YXQHxp4wn4FvtRv5Wt.HGOPwQH8bWbYqTAOCajr0hQGNwhlDXHjiW','2025-11-13 19:32:24',NULL),(21,'emanuel merlo','','3417208555','+54','emanuelmerlo15@gmail.com','pasco 123','$2y$10$3.AGJ/4dZl4SlpNSSg1bWu3y9KHIkZn1oP12v9hKtrrG2/Ho1/lau','2025-12-09 12:53:27','/assets/images/usuarios/usuario_1765295607_1765295607.jpeg');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
