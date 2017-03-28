-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.13-log - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table hoipos_v2.outlet_reservation_user
CREATE TABLE IF NOT EXISTS `outlet_reservation_user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_name` text,
  `email` text,
  `display_name` text,
  `outlet_ids` text,
  `permission_level` int(11) DEFAULT NULL COMMENT '0= full administrator, 500 = see and manage reservations only',
  `password_hash` text,
  `secret_token` varchar(200) DEFAULT NULL,
  `secret_expiry` timestamp NULL DEFAULT NULL,
  `pending_password_reset` tinyint(4) DEFAULT NULL,
  `last_login_timestamp` timestamp NULL DEFAULT NULL,
  `created_timestamp` timestamp NULL DEFAULT NULL,
  `modified_timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- Dumping data for table hoipos_v2.outlet_reservation_user: ~4 rows (approximately)
/*!40000 ALTER TABLE `outlet_reservation_user` DISABLE KEYS */;
INSERT INTO `outlet_reservation_user` (`id`, `user_name`, `email`, `display_name`, `outlet_ids`, `permission_level`, `password_hash`, `secret_token`, `secret_expiry`, `pending_password_reset`, `last_login_timestamp`, `created_timestamp`, `modified_timestamp`) VALUES
	(13, 'hoanganh25991', 'lehoanganh25991@gmail.com', 'Anh Le Hoang', '1,2,3,6,12', 0, '$2y$10$sHZ8DCL3C3BbyUxbgH.mpeRhzTpntkr5QLg89d0VHXNe6WpMjBSaq', 'HKwzwAklTuHfXR47ILu1fI7FMEkBRNidoMprpxSLa5nYA01H9Xuzd4l1dbmG', NULL, NULL, NULL, '2017-03-27 19:51:50', '2017-03-28 16:16:22'),
	(14, 'admin', 'lehoanganh25991@gmail.com', 'Admin', '1,2,3,6,12', 10, '$2y$10$wxgPoWPVPaUSJGBaI1r26OAMPtoWqZ9ga9glpQJGckYes9qy1GFiW', 'uOzBOiXBmSC4US21zZmshF3vA7xLRUCfSA7mXUMJH8GsJ2nSXrjBQOdbQQVj', NULL, NULL, NULL, '2017-03-28 13:29:54', '2017-03-28 13:29:54'),
	(15, 'admin2', 'lehoanganh25991@gmail.com', 'Admin 2', NULL, NULL, '$2y$10$wxgPoWPVPaUSJGBaI1r26OAMPtoWqZ9ga9glpQJGckYes9qy1GFiW', NULL, NULL, NULL, NULL, '2017-03-28 14:27:52', '2017-03-28 16:16:22'),
	(19, 'admin3', 'lehoanganh25991@gmail.com', 'Admin 3', NULL, NULL, '$2y$10$wxgPoWPVPaUSJGBaI1r26OAMPtoWqZ9ga9glpQJGckYes9qy1GFiW', 'H8knHED3ow2Q7gIezJytB9vRPuNGu4ggFuymVcdwP2cpyMCi2IDenDlCyrJM', NULL, NULL, NULL, '2017-03-28 14:57:20', '2017-03-28 16:16:22');
/*!40000 ALTER TABLE `outlet_reservation_user` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
