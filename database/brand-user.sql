-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.17-0ubuntu0.16.10.1 - (Ubuntu)
-- Server OS:                    Linux
-- HeidiSQL Version:             9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table hoipos_dev.brand
CREATE TABLE IF NOT EXISTS `brand` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `brand_name` text,
  `modified_timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- Dumping data for table hoipos_dev.brand: ~12 rows (approximately)
/*!40000 ALTER TABLE `brand` DISABLE KEYS */;
INSERT INTO `brand` (`id`, `brand_name`, `modified_timestamp`) VALUES
	(1, 'HoiPOS Group', '2015-08-02 16:00:00'),
	(2, 'Originally Cafe', NULL),
	(3, 'CaffeBene', '2015-11-11 01:30:00'),
	(4, 'Zzapi', '2016-07-01 16:00:00'),
	(5, 'Arteastiq', '2016-07-01 16:00:00'),
	(6, 'TungLok', '2016-07-08 16:00:00'),
	(7, 'BlancoCourtBeefNoodles', '2016-07-21 16:00:00'),
	(8, 'Coco Ichibanya', '2016-07-28 16:00:00'),
	(9, 'Cheng n Cheng', '2016-08-19 16:00:00'),
	(10, 'SunLok', '2016-09-25 16:00:00'),
	(11, 'Pharmex', '2016-09-25 16:00:00'),
	(12, 'The Beer Factory', '2017-03-03 00:00:00');
/*!40000 ALTER TABLE `brand` ENABLE KEYS */;

-- Dumping structure for table hoipos_dev.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` text,
  `password` text,
  `brand_id` bigint(20) DEFAULT NULL,
  `outlet_ids` text,
  `user_level` int(11) NOT NULL DEFAULT '0' COMMENT '0:readonly, 10:admin',
  `modified_timestamp` timestamp NULL DEFAULT NULL,
  `last_login_timestamp` timestamp NULL DEFAULT NULL,
  `created_timestamp` timestamp NULL DEFAULT NULL,
  `enabled` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- Dumping data for table hoipos_dev.user: ~14 rows (approximately)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `username`, `password`, `brand_id`, `outlet_ids`, `user_level`, `modified_timestamp`, `last_login_timestamp`, `created_timestamp`, `enabled`) VALUES
	(1, 'test', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 1, '1,2,4,6,13,14,15,19', 10, '2015-08-02 16:00:00', '2015-08-02 16:00:00', '2015-08-02 16:00:00', 1),
	(5, 'zzapi', '4c3f5c89418840dda2affe261a0a0b2e014a0f5c', 4, '14', 10, '2016-07-01 16:00:00', '2016-07-01 16:00:00', '2016-07-01 16:00:00', 1),
	(6, 'arteastiq', '86c7dc38c483d4b72c9ba298a70b1b88102ca7be', 5, '15,22', 10, '2016-07-01 16:00:00', '2016-07-01 16:00:00', '2016-07-01 16:00:00', 1),
	(7, 'tunglokoph', '68b1617b5a798b6fd40dfa65a5360a0c18c75326', 6, '16', 10, '2016-07-08 16:00:00', '2016-07-08 16:00:00', '2016-07-08 16:00:00', 1),
	(8, 'wm@hoipos.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 7, '17', 10, '2016-07-08 16:00:00', '2016-07-08 16:00:00', '2016-07-08 16:00:00', 1),
	(9, 'blanco', '07a067cc949f01088017f4b19c6209737aae710b', 7, '17', 10, '2016-07-21 16:00:00', '2016-07-21 16:00:00', '2016-07-21 16:00:00', 1),
	(10, 'caroline', '2cfc032d86053983a39e985d2d557b93c7bc4730', 8, '18', 10, '2016-07-28 16:00:00', '2016-07-28 16:00:00', '2016-07-28 16:00:00', 1),
	(11, 'binchotan', '2cfc032d86053983a39e985d2d557b93c7bc4730', 8, '18', 10, '2016-07-28 16:00:00', '2016-07-28 16:00:00', '2016-07-28 16:00:00', 1),
	(12, 'samcheng', '118b87f617bc475589e85fa6aed08c57aded8298', 9, '19', 10, '2016-08-19 16:00:00', '2016-08-19 16:00:00', '2016-08-19 16:00:00', 1),
	(13, 'sunlok', '7cf51ee2b946a83b20fc87b9082309fcd8d076bb', 10, '20', 10, NULL, NULL, '2016-09-25 16:00:00', 1),
	(14, 'pharmex', 'c508dc8f070d0e57e6937207919fcdfa8d5cfc76', 11, '21', 10, NULL, NULL, '2016-09-25 16:00:00', 1),
	(15, 'kianhong', 'e194e06f7ebd263b3756821903bd389f3cd054f1', 5, '22', 10, '2016-07-01 16:00:00', '2016-07-01 16:00:00', '2016-07-01 16:00:00', 1),
	(16, 'calixto', '91e94fe5d1e640c95256b6bac5013ccc57de0a30', 4, '14', 0, NULL, NULL, NULL, NULL),
	(17, 'tbfxadmin', 'e06c9b157c566a2265d058da858d7370ff2afb31', 12, '23', 10, '2017-03-14 16:00:00', '2017-03-14 16:00:00', '2017-03-14 16:00:00', 1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
