/*
SQLyog Ultimate v12.5.1 (64 bit)
MySQL - 10.4.28-MariaDB : Database - db_toko_roti_enak
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`db_toko_roti_enak` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `db_toko_roti_enak`;

/*Table structure for table `barang` */

DROP TABLE IF EXISTS `barang`;

CREATE TABLE `barang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_barang` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL,
  `klasifikasi_id` int(11) DEFAULT NULL,
  `photo_product` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `klasifikasi_id` (`klasifikasi_id`),
  CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`klasifikasi_id`) REFERENCES `klasifikasi_barang` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `barang` */

insert  into `barang`(`id`,`nama_barang`,`deskripsi`,`harga`,`stok`,`klasifikasi_id`,`photo_product`,`created_at`,`updated_at`) values 
(3,'Laptop ASUS ROG','Laptop Gaming Asus',17000000.00,10,1,'laptop_rog_strix.png','2024-07-12 23:53:04','2024-07-14 23:39:54'),
(4,'Kemeja Pria','Kemeja lengan panjang untuk pria',250000.00,50,2,'kemeja pria.jpg','2024-07-12 23:53:04','2024-07-14 23:25:24'),
(5,'Blender Philips','Blender multifungsi untuk kebutuhan dapur',500000.00,20,3,'blender philips.jpg','2024-07-12 23:53:04','2024-07-14 23:26:15');

/*Table structure for table `klasifikasi_barang` */

DROP TABLE IF EXISTS `klasifikasi_barang`;

CREATE TABLE `klasifikasi_barang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_klasifikasi` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `klasifikasi_barang` */

insert  into `klasifikasi_barang`(`id`,`nama_klasifikasi`,`deskripsi`,`created_at`,`updated_at`) values 
(1,'Elektronik','Kategori barang elektronik','2024-07-12 23:27:02','2024-07-12 23:27:02'),
(2,'Pakaian','Kategori pakaian dan fashion','2024-07-12 23:27:21','2024-07-12 23:27:21'),
(3,'Alat Rumah Tangga','Kategori peralatan rumah tangga','2024-07-12 23:27:37','2024-07-12 23:27:37');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `tokenize` varchar(255) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `phto_profile` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `login_date` datetime DEFAULT NULL,
  `logout_date` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`userid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `users` */

insert  into `users`(`userid`,`username`,`password`,`fullname`,`tokenize`,`alamat`,`no_hp`,`phto_profile`,`created_at`,`updated_at`,`login_date`,`logout_date`,`status`) values 
(14,'amirul007','$2y$10$NRkn4nZ13cJ4oZSH2v2.Hu9OGkVLi8WnI6WGFHBwZQmJRn6WzzwCm','Amirul Putra Justicia','eb835a2240266f09736e330ad63cc34545bc32369a659fb20715321102b8d29e',NULL,NULL,NULL,'2024-07-12 16:41:36',NULL,'2024-07-15 00:11:20','2024-07-15 00:11:23','Admin');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
