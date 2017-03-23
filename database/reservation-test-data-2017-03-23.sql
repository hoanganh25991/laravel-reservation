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

-- Dumping structure for table hoipos_v2.brand_credit
CREATE TABLE IF NOT EXISTS `brand_credit` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `brand_id` bigint(20) DEFAULT NULL,
  `sms_credit_balance` bigint(20) DEFAULT '0',
  `sms_credit_consumed` bigint(20) DEFAULT NULL,
  `email_credit_balance` bigint(20) DEFAULT '0',
  `email_credit_consumed` bigint(20) DEFAULT NULL,
  `unlimited_sms` tinyint(4) DEFAULT NULL,
  `unlimited_email` tinyint(4) DEFAULT NULL,
  `created_timestamp` timestamp NULL DEFAULT NULL,
  `modified_timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Dumping data for table hoipos_v2.brand_credit: ~1 rows (approximately)
/*!40000 ALTER TABLE `brand_credit` DISABLE KEYS */;
INSERT INTO `brand_credit` (`id`, `brand_id`, `sms_credit_balance`, `sms_credit_consumed`, `email_credit_balance`, `email_credit_consumed`, `unlimited_sms`, `unlimited_email`, `created_timestamp`, `modified_timestamp`) VALUES
	(1, 1, -15, 115, 0, NULL, 0, NULL, '2017-03-20 08:37:58', '2017-03-22 13:22:42');
/*!40000 ALTER TABLE `brand_credit` ENABLE KEYS */;

-- Dumping structure for table hoipos_v2.customer
CREATE TABLE IF NOT EXISTS `customer` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `salutation` varchar(32) DEFAULT NULL,
  `first_name` text,
  `last_name` text,
  `email` text,
  `phone_country_code` varchar(32) DEFAULT NULL,
  `phone` text,
  `address` text,
  `gender` tinyint(4) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  `barcode` varchar(128) DEFAULT NULL,
  `language` varchar(45) DEFAULT NULL,
  `brand` bigint(20) DEFAULT NULL,
  `modified_timestamp` timestamp NULL DEFAULT NULL,
  `created_timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table hoipos_v2.customer: ~0 rows (approximately)
/*!40000 ALTER TABLE `customer` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer` ENABLE KEYS */;

-- Dumping structure for table hoipos_v2.customer_authentication
CREATE TABLE IF NOT EXISTS `customer_authentication` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `fb_uid` text,
  `password_hash` text,
  `modified_timestamp` timestamp NULL DEFAULT NULL,
  `created_timestamp` timestamp NULL DEFAULT NULL,
  `last_login_timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table hoipos_v2.customer_authentication: ~0 rows (approximately)
/*!40000 ALTER TABLE `customer_authentication` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_authentication` ENABLE KEYS */;

-- Dumping structure for table hoipos_v2.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hoipos_v2.failed_jobs: ~0 rows (approximately)
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;

-- Dumping structure for table hoipos_v2.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_reserved_at_index` (`queue`,`reserved_at`)
) ENGINE=InnoDB AUTO_INCREMENT=2314 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hoipos_v2.jobs: ~1 rows (approximately)
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
	(2313, 'default', '{"displayName":"App\\\\Jobs\\\\HoiJobs","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"timeout":null,"data":{"commandName":"App\\\\Jobs\\\\HoiJobs","command":"O:16:\\"App\\\\Jobs\\\\HoiJobs\\":4:{s:6:\\"\\u0000*\\u0000job\\";N;s:10:\\"connection\\";N;s:5:\\"queue\\";N;s:5:\\"delay\\";O:13:\\"Carbon\\\\Carbon\\":3:{s:4:\\"date\\";s:26:\\"2017-03-22 12:13:54.000000\\";s:13:\\"timezone_type\\";i:3;s:8:\\"timezone\\";s:16:\\"Asia\\/Ho_Chi_Minh\\";}}"}}', 0, NULL, 1490159634, 1490159574);
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;

-- Dumping structure for table hoipos_v2.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hoipos_v2.migrations: ~3 rows (approximately)
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(8, '2017_02_24_083546_create_tables_for_reservation', 1),
	(9, '2017_03_17_170723_create_jobs_table', 2),
	(10, '2017_03_18_231652_create_failed_jobs_table', 3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;

-- Dumping structure for table hoipos_v2.outlet
CREATE TABLE IF NOT EXISTS `outlet` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `brand_id` bigint(20) DEFAULT NULL,
  `outlet_name` varchar(256) DEFAULT NULL,
  `outlet_address` varchar(256) DEFAULT NULL,
  `outlet_receipt_footer` varchar(256) DEFAULT NULL,
  `outlet_logo` text,
  `outlet_receipt_logo` text,
  `outlet_receipt_footer_logo` varchar(256) DEFAULT NULL,
  `flow_type` tinyint(4) NOT NULL DEFAULT '0',
  `enabled` tinyint(4) DEFAULT NULL,
  `license_key` text,
  `modified_timestamp` timestamp NULL DEFAULT NULL,
  `created_timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- Dumping data for table hoipos_v2.outlet: ~15 rows (approximately)
/*!40000 ALTER TABLE `outlet` DISABLE KEYS */;
INSERT INTO `outlet` (`id`, `brand_id`, `outlet_name`, `outlet_address`, `outlet_receipt_footer`, `outlet_logo`, `outlet_receipt_logo`, `outlet_receipt_footer_logo`, `flow_type`, `enabled`, `license_key`, `modified_timestamp`, `created_timestamp`) VALUES
	(1, 1, 'HoiPOS Cafe (West)', '#02-21/AB/C Great World City\\n1 Kim Send Promenade\\nSingapore, 237994\\nTel No. 6732-3061\\nGST No: 19-880070-D', 'THANK YOU FOR SUPPORTING US\\nHAVE A WONDERFUL DAY', 'tunglok_logo', 'tunglok_logo_receipt', NULL, 1, 1, 'aaa', '2015-08-02 23:00:00', NULL),
	(2, 1, 'HoiPOS Cafe (East)', 'No. 2 Orchard Turn\\n#B2-51 Ion Orchard\\nSingapore 238801', 'THANK YOU FOR SUPPORTING US\\nHAVE A WONDERFUL DAY', '', '', NULL, 0, 1, NULL, '2015-08-02 23:00:00', NULL),
	(3, 1, 'HoiPOS Cafe (North)', 'Ang Mo Kio Ave. 3 #B2-26\\nSingapore 569933\\nTEL: 6453 2521/1976', 'THANK YOU FOR SUPPORTING US\\nHAVE A WONDERFUL DAY', '', '', NULL, 0, 1, NULL, '2015-08-02 23:00:00', NULL),
	(4, 3, 'VivoCity', '1 HarbourFront Walk, #02-150\\nSingapore 098585\\nGST Reg No: 201510863E', 'Flash this receipt to enjoy 10% off Skechers GoWalk 3 laced up collections at Skechers Vivo City #02-13/14 only. Valid till 14 July 2016. T&C apply.', '', '', NULL, 0, 1, NULL, '2015-11-11 08:30:00', NULL),
	(6, 1, 'Test Outlet (Duplication Test)', '101 HarbourFront Walk, #02-150\\nSingapore 098585\\nGST Reg No: 201510863E', 'Thank You\\nWe\'d love to hear from you!\\nwww.caffebene.com.sg/feedback', '', '', NULL, 0, 1, 'BBCC', '2016-05-23 12:14:15', '2016-05-23 12:14:15'),
	(13, 1, 'Happy Dine', '15 Kent Ridge Drive, #01-03\nS 119245', 'Thank You', '', '', '', 1, 1, 'TLTE', '2016-10-29 01:14:22', '2016-06-20 09:46:53'),
	(14, 4, 'Zzapi', '100AM Shopping Mall, 100 Tras Street\n#01-11, Singapore 079027 \nGST: 201217715M', 'Thank you for dining with us\n Follow us on Facebook:\nhttps://www.facebook.com/zzapi.com.sg/', 'http://pos.hoicard.com/cms/logos/zzapi_logo.png', 'http://pos.hoicard.com/cms/logos/zzapi_logo_receipt.png', NULL, 1, 1, 'zzapi', '2016-07-14 20:36:47', '2016-06-20 15:24:28'),
	(15, 5, 'Arteastiq', '333A Orchard Road \nMandarin Gallery Level 4-14/15\nSingapore 238867\nTel: 6235 8705\nGST Reg No: 201202807R', 'THANK YOU FOR SUPPORTING US\nHAVE A WONDERFUL DAY', 'http://pos.hoicard.com/cms/logos/arteastiq_logo.png', 'http://pos.hoicard.com/cms/logos/arteastiq_logo_receipt.png', '', 1, 1, 'arteastiq', '2016-12-06 14:23:33', '2016-06-20 15:24:28'),
	(16, 6, 'Tung Lok Signatures (Orchard Parade Hotel)', '1 Tanglin Rd, 2/F Orchard Parade Hotel, Singapore 247905', '', 'tunglok_logo', 'tunglok_logo_receipt', NULL, 1, 1, 'tloph', '2016-07-09 11:30:12', '2016-07-09 09:46:53'),
	(17, 7, 'Blanco Court Beef Noodles', '92 Guillemard Rd, 399716\nTel : 6348 1708', 'Thank you for dining with us\n Follow us on Facebook:\nhttps://www.facebook.com/BlancoCourtBeefNoodles/', '', '', NULL, 1, 1, 'BCBN', '2016-08-02 17:35:12', '2016-07-21 23:46:15'),
	(18, 8, 'Le Binchotan', '115 Amoy Street #01-04\nSingapore 069935\nTel: 6221 6065\nGST Reg No: 201610182C', '', 'http://pos.hoicard.com/cms/logos/le_binchotan_logo_m.png', 'http://pos.hoicard.com/cms/logos/le_binchotan_logo_no_svc.png', 'http://pos.hoicard.com/cms/logos/le_binchotan_receipt_footer.png', 1, 1, 'LBCT', '2016-08-11 10:25:50', '2016-07-28 23:46:15'),
	(19, 9, 'the SPREAD', '15 Kent Ridge Drive, #01-03\nS 119245', 'Thank you', 'http://pos.hoicard.com/cms/logos/chengs_transparent.png', 'http://pos.hoicard.com/cms/logos/chengs.png', '', 0, 1, 'CHENG', '2017-01-08 22:10:51', '2016-08-19 23:15:15'),
	(20, 10, 'Sun Lok', '45 Kent Ridge Drive, #01-03', '', '', '', '', 1, 1, 'SUNLOK', '2016-09-26 17:05:05', '2016-09-25 23:00:00'),
	(21, 11, 'Pharmex', '35 Kent Ridge Drive, #01-03', '', '', '', '', 0, 1, 'PHARMEX', '2016-09-26 17:05:35', '2016-09-25 23:00:00'),
	(22, 5, 'Arteastiq Plaza Singapura', '68 Orchard Road \nPlaza Singapura #03-70/72\nSingapore 238839\nTel: 6336 0952\nGST Reg No: 201202807R', 'THANK YOU FOR SUPPORTING US\nHAVE A WONDERFUL DAY', 'http://pos.hoicard.com/cms/logos/arteastiq_logo.png', 'http://pos.hoicard.com/cms/logos/arteastiq_logo_receipt.png', '', 1, 1, '5e527bd61210680369769941c69ca936', '2016-12-07 16:15:08', '2016-12-06 14:23:09');
/*!40000 ALTER TABLE `outlet` ENABLE KEYS */;

-- Dumping structure for table hoipos_v2.outlet_reservation_setting
CREATE TABLE IF NOT EXISTS `outlet_reservation_setting` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `outlet_id` bigint(20) DEFAULT NULL,
  `setting_group` int(11) DEFAULT NULL COMMENT '1=buffers,2=notifications,3=others',
  `setting_key` text,
  `setting_value` text,
  `setting_type` tinyint(4) DEFAULT NULL,
  `created_timestamp` timestamp NULL DEFAULT NULL,
  `modified_timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- Dumping data for table hoipos_v2.outlet_reservation_setting: ~12 rows (approximately)
/*!40000 ALTER TABLE `outlet_reservation_setting` DISABLE KEYS */;
INSERT INTO `outlet_reservation_setting` (`id`, `outlet_id`, `setting_group`, `setting_key`, `setting_value`, `setting_type`, `created_timestamp`, `modified_timestamp`) VALUES
	(1, 1, 0, 'MIN_HOURS_IN_ADVANCE_SLOT_TIME', '2', 1, '2017-03-19 09:54:16', '2017-03-19 09:54:16'),
	(2, 1, 0, 'MIN_HOURS_IN_ADVANCE_SESSION_TIME', '2', 1, '2017-03-19 09:54:16', '2017-03-19 09:54:16'),
	(3, 1, 1, 'BRAND_ID', '1', 1, '2017-03-19 09:54:16', '2017-03-19 09:54:16'),
	(4, 1, 0, 'MAX_DAYS_IN_ADVANCE', '7', 1, '2017-03-19 09:54:16', '2017-03-19 09:54:16'),
	(5, 1, 1, 'SMS_SENDER_NAME', 'AFRED', 0, '2017-03-19 09:54:16', '2017-03-19 09:54:16'),
	(6, 1, 2, 'HOURS_BEFORE_RESERVATION_TIME_TO_SEND_CONFIRM', '2', 1, '2017-03-19 09:54:16', '2017-03-19 09:54:16'),
	(7, 1, 2, 'SEND_SMS_ON_BOOKING', '1', 1, '2017-03-19 09:54:16', '2017-03-19 09:54:16'),
	(8, 1, 2, 'SEND_SMS_CONFIRMATION', '1', 1, '2017-03-19 09:54:16', '2017-03-19 09:54:16'),
	(9, 1, 3, 'REQUIRE_DEPOSIT', '1', 1, '2017-03-19 09:54:16', '2017-03-19 09:54:16'),
	(10, 1, 3, 'DEPOSIT_THRESHOLD_PAX', '10', 1, '2017-03-19 09:54:16', '2017-03-19 09:54:16'),
	(11, 1, 3, 'DEPOSIT_TYPE', '1', 1, '2017-03-19 09:54:16', '2017-03-19 09:54:16'),
	(12, 1, 3, 'FIXED_SUM_VALUE', '25', 1, '2017-03-19 09:54:16', '2017-03-19 09:54:16'),
	(13, 1, 3, 'PER_PAX_VALUE', '5', 1, '2017-03-19 09:54:16', '2017-03-19 09:54:16');
/*!40000 ALTER TABLE `outlet_reservation_setting` ENABLE KEYS */;

-- Dumping structure for table hoipos_v2.outlet_reservation_user
CREATE TABLE IF NOT EXISTS `outlet_reservation_user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `outlet_ids` text,
  `user_name` text,
  `password_hash` text,
  `email` text,
  `display_name` text,
  `permission_level` int(11) DEFAULT NULL COMMENT '0= full administrator, 500 = see and manage reservations only',
  `secret_token` varchar(200) DEFAULT NULL,
  `secret_expiry` timestamp NULL DEFAULT NULL,
  `pending_password_reset` tinyint(4) DEFAULT NULL,
  `last_login_timestamp` timestamp NULL DEFAULT NULL,
  `created_timestamp` timestamp NULL DEFAULT NULL,
  `modified_timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- Dumping data for table hoipos_v2.outlet_reservation_user: ~4 rows (approximately)
/*!40000 ALTER TABLE `outlet_reservation_user` DISABLE KEYS */;
INSERT INTO `outlet_reservation_user` (`id`, `outlet_ids`, `user_name`, `password_hash`, `email`, `display_name`, `permission_level`, `secret_token`, `secret_expiry`, `pending_password_reset`, `last_login_timestamp`, `created_timestamp`, `modified_timestamp`) VALUES
	(1, NULL, 'krista25', '$2y$10$pJt8xofv7HVUDzpQTLewqeRS.S9Jq/z.5hRynI9gQi5d90DQImTHi', 'boris72@example.com', 'Darrin Kassulke', NULL, 'eLe1pGKzkA', NULL, NULL, NULL, '2017-02-26 14:50:48', '2017-02-26 14:50:48'),
	(5, NULL, 'aa', '$2y$10$IM/Wtr0o1PRQ3ltCf/86nu2qw02dGSweiOp8rHZ1Gk5yQUnjifWAe', 'aaa@a.com', NULL, NULL, '7bBvDaPViUqdQbfce4m05xWBYtiKyUPtGIdUkmypkozmU9H9lmD4cvLmeQ3k', NULL, NULL, NULL, '2017-03-01 02:27:57', '2017-03-01 02:27:57'),
	(6, NULL, 'anh', '$2y$10$0UwgM8RL99e3qn3JnnhNtO.CljOjpHXJR1cg5pf7juyvNTcaOTdou', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2017-03-01 02:50:06', '2017-03-01 02:50:06'),
	(7, NULL, 'anh', '$2y$10$fVddLP/dkdfGlFMmyd4Z4.M1PuzirwNyFLmfLUqEORYhE2nD8bzwi', 'anh', NULL, NULL, 'aDrP846VFle3W64VBd6Grxqs8347Uplzst02beKJiaGhUTcIe6pkpqE7IX66', NULL, NULL, NULL, '2017-03-01 02:53:22', '2017-03-01 02:53:22');
/*!40000 ALTER TABLE `outlet_reservation_user` ENABLE KEYS */;

-- Dumping structure for table hoipos_v2.outlet_reservation_user_reset_password
CREATE TABLE IF NOT EXISTS `outlet_reservation_user_reset_password` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `created_timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table hoipos_v2.outlet_reservation_user_reset_password: ~0 rows (approximately)
/*!40000 ALTER TABLE `outlet_reservation_user_reset_password` DISABLE KEYS */;
/*!40000 ALTER TABLE `outlet_reservation_user_reset_password` ENABLE KEYS */;

-- Dumping structure for table hoipos_v2.reservation
CREATE TABLE IF NOT EXISTS `reservation` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `outlet_id` bigint(20) DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `salutation` varchar(32) DEFAULT NULL,
  `first_name` text,
  `last_name` text,
  `email` text,
  `phone_country_code` varchar(32) DEFAULT NULL,
  `phone` text,
  `status` int(11) DEFAULT NULL COMMENT '100=reserved,200=reminder sent,300=confirmed,400=arrived,-100=user cancelled,-200=staff cancelled,-300=no show',
  `adult_pax` int(11) DEFAULT NULL,
  `children_pax` int(11) DEFAULT NULL,
  `reservation_timestamp` timestamp NULL DEFAULT NULL,
  `customer_remarks` text,
  `is_outdoor` tinyint(4) DEFAULT NULL,
  `send_confirmation_by_timestamp` timestamp NULL DEFAULT NULL,
  `send_sms_confirmation` tinyint(4) DEFAULT NULL,
  `send_email_confirmation` tinyint(4) DEFAULT NULL,
  `table_layout_id` bigint(20) DEFAULT NULL,
  `table_layout_name` varchar(16) DEFAULT NULL,
  `table_name` varchar(16) DEFAULT NULL,
  `staff_remarks` text,
  `staff_read_state` tinyint(4) DEFAULT NULL,
  `payment_required` tinyint(4) DEFAULT NULL,
  `payment_id` text,
  `payment_timestamp` timestamp NULL DEFAULT NULL,
  `payment_amount` decimal(32,2) DEFAULT NULL,
  `created_timestamp` timestamp NULL DEFAULT NULL,
  `modified_timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8;

-- Dumping data for table hoipos_v2.reservation: ~5 rows (approximately)
/*!40000 ALTER TABLE `reservation` DISABLE KEYS */;
INSERT INTO `reservation` (`id`, `outlet_id`, `customer_id`, `salutation`, `first_name`, `last_name`, `email`, `phone_country_code`, `phone`, `status`, `adult_pax`, `children_pax`, `reservation_timestamp`, `customer_remarks`, `is_outdoor`, `send_confirmation_by_timestamp`, `send_sms_confirmation`, `send_email_confirmation`, `table_layout_id`, `table_layout_name`, `table_name`, `staff_remarks`, `staff_read_state`, `payment_required`, `payment_id`, `payment_timestamp`, `payment_amount`, `created_timestamp`, `modified_timestamp`) VALUES
	(64, 1, NULL, 'Mr.', 'Anh', 'Le Hoang', 'lehoanganh25991@gmail.com', '+84', '903865657', 200, 1, 0, '2017-03-23 05:00:00', 'hello world', NULL, '2017-03-22 12:05:05', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2017-03-22 12:04:05', '2017-03-22 12:08:51'),
	(66, 1, NULL, 'Mr.', 'Anh', 'Le Hoang', 'lehoanganh25991@gmail.com', '+84', '903865657', 200, 1, 0, '2017-03-23 05:30:00', 'hello world', NULL, '2017-03-22 12:09:34', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2017-03-22 12:08:34', '2017-03-22 12:09:52'),
	(67, 1, NULL, 'Mr.', 'Anh', 'Le Hoang', 'lehoanganh25991@gmail.com', '+84', '903865657', 100, 1, 0, '2017-03-23 06:30:00', 'hello world', NULL, '2017-03-22 13:16:16', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2017-03-22 13:15:16', '2017-03-22 13:15:16'),
	(68, 1, NULL, 'Mr.', 'Anh', 'Le Hoang', 'lehoanganh25991@gmail.com', '+84', '903865657', 100, 1, 0, '2017-03-23 07:00:00', 'hello world', NULL, '2017-03-22 13:23:30', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2017-03-22 13:22:30', '2017-03-22 13:22:30'),
	(69, 1, NULL, 'Mr.', 'Anh', 'Le Hoang', 'lehoanganh25991@gmail.com', '+84', '903865657', 100, 1, 0, '2017-03-23 06:00:00', 'hello world', NULL, '2017-03-22 13:23:42', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2017-03-22 13:22:42', '2017-03-22 13:22:42');
/*!40000 ALTER TABLE `reservation` ENABLE KEYS */;

-- Dumping structure for table hoipos_v2.session
CREATE TABLE IF NOT EXISTS `session` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `outlet_id` bigint(20) DEFAULT NULL,
  `session_name` text,
  `on_mondays` tinyint(4) DEFAULT NULL,
  `on_tuesdays` tinyint(4) DEFAULT NULL,
  `on_wednesdays` tinyint(4) DEFAULT NULL,
  `on_thursdays` tinyint(4) DEFAULT NULL,
  `on_fridays` tinyint(4) DEFAULT NULL,
  `on_saturdays` tinyint(4) DEFAULT NULL,
  `on_sundays` tinyint(4) DEFAULT NULL,
  `created_timestamp` timestamp NULL DEFAULT NULL,
  `modified_timestamp` timestamp NULL DEFAULT NULL,
  `one_off` tinyint(4) DEFAULT NULL,
  `one_off_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Dumping data for table hoipos_v2.session: ~3 rows (approximately)
/*!40000 ALTER TABLE `session` DISABLE KEYS */;
INSERT INTO `session` (`id`, `outlet_id`, `session_name`, `on_mondays`, `on_tuesdays`, `on_wednesdays`, `on_thursdays`, `on_fridays`, `on_saturdays`, `on_sundays`, `created_timestamp`, `modified_timestamp`, `one_off`, `one_off_date`) VALUES
	(1, 1, 'Special Dine', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2017-03-03 21:40:06', '2017-03-03 21:40:11', 1, '2017-03-06'),
	(2, 1, 'Lunch time', 1, 1, 1, 1, 1, 1, 1, '2017-03-03 21:39:39', '2017-03-06 21:39:33', 0, NULL),
	(3, 1, 'Speical Hotdeal', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2017-03-03 21:40:15', '2017-03-06 21:40:19', 1, '2017-03-06'),
	(4, 1, 'Hack time', 0, 1, 1, 1, 1, 1, 1, '2017-03-03 21:39:39', '2017-03-06 21:39:33', 0, NULL);
/*!40000 ALTER TABLE `session` ENABLE KEYS */;

-- Dumping structure for table hoipos_v2.timing
CREATE TABLE IF NOT EXISTS `timing` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `session_id` bigint(20) DEFAULT NULL,
  `timing_name` text,
  `disabled` tinyint(4) DEFAULT NULL,
  `first_arrival_time` time DEFAULT NULL,
  `last_arrival_time` time DEFAULT NULL,
  `interval_minutes` int(11) DEFAULT NULL,
  `capacity_1` int(11) DEFAULT NULL,
  `capacity_2` int(11) DEFAULT NULL,
  `capacity_3_4` int(11) DEFAULT NULL,
  `capacity_5_6` int(11) DEFAULT NULL,
  `capacity_7_x` int(11) DEFAULT NULL,
  `max_pax` int(11) DEFAULT NULL,
  `children_allowed` tinyint(4) DEFAULT NULL,
  `is_outdoor` tinyint(4) DEFAULT NULL COMMENT 'to indicate alternative slots for outdoor',
  `created_timestamp` timestamp NULL DEFAULT NULL,
  `modified_timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- Dumping data for table hoipos_v2.timing: ~4 rows (approximately)
/*!40000 ALTER TABLE `timing` DISABLE KEYS */;
INSERT INTO `timing` (`id`, `session_id`, `timing_name`, `disabled`, `first_arrival_time`, `last_arrival_time`, `interval_minutes`, `capacity_1`, `capacity_2`, `capacity_3_4`, `capacity_5_6`, `capacity_7_x`, `max_pax`, `children_allowed`, `is_outdoor`, `created_timestamp`, `modified_timestamp`) VALUES
	(1, 2, '12-16', 0, '05:00:00', '08:00:00', 30, 1, 1, 1, 1, 1, 20, 1, NULL, '2017-03-02 20:11:45', '2017-03-02 21:51:41'),
	(2, 1, '13-14', 1, '11:00:00', '18:00:00', 20, 100, 100, 3, 100, 100, 20, NULL, NULL, '2017-03-02 20:11:50', '2017-03-02 20:11:53'),
	(3, 3, '14-30', 1, '18:00:00', '18:30:00', 15, 1000, 1000, 1000, 1000, 1000, 20, NULL, NULL, '2017-02-28 14:28:35', '2017-02-28 14:28:36'),
	(4, 4, '12-16', 1, '15:00:00', '23:30:00', 30, 1, 1, 1, 1, 1, 20, 1, NULL, '2017-03-02 20:11:45', '2017-03-02 21:51:41'),
	(5, 2, '12-16', 1, '09:00:00', '12:00:00', 30, 1, 1, 1, 1, 1, 20, 1, NULL, '2017-03-02 20:11:45', '2017-03-02 21:51:41');
/*!40000 ALTER TABLE `timing` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
