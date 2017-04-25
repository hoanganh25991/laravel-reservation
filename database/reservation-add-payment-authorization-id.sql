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

-- Dumping structure for table hoipos_v2.brand
CREATE TABLE IF NOT EXISTS `brand` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `brand_name` text,
  `paypal_token` varchar(255) DEFAULT NULL,
  `created_timestamp` timestamp NULL DEFAULT NULL,
  `modified_timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table hoipos_v2.customer
CREATE TABLE IF NOT EXISTS `customer` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `brand_id` varchar(32) DEFAULT NULL,
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

-- Data exporting was unselected.
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

-- Data exporting was unselected.
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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table hoipos_v2.res_outlet_reservation_setting
CREATE TABLE IF NOT EXISTS `res_outlet_reservation_setting` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `outlet_id` bigint(20) DEFAULT NULL,
  `setting_group` int(11) DEFAULT NULL COMMENT '1=buffers,2=notifications,3=others',
  `setting_key` text,
  `setting_value` text,
  `setting_type` tinyint(4) DEFAULT NULL,
  `created_timestamp` timestamp NULL DEFAULT NULL,
  `modified_timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table hoipos_v2.res_outlet_reservation_user
CREATE TABLE IF NOT EXISTS `res_outlet_reservation_user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_name` text,
  `email` text,
  `display_name` text,
  `brand_id` text,
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
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table hoipos_v2.res_reservation
CREATE TABLE IF NOT EXISTS `res_reservation` (
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
  `send_sms_confirmation` tinyint(4) DEFAULT NULL,
  `send_email_confirmation` tinyint(4) DEFAULT NULL,
  `table_layout_id` bigint(20) DEFAULT NULL,
  `table_layout_name` varchar(16) DEFAULT NULL,
  `table_name` varchar(16) DEFAULT NULL,
  `staff_read_state` tinyint(4) DEFAULT NULL,
  `staff_remarks` text,
  `payment_required` tinyint(4) DEFAULT NULL,
  `payment_authorization_id` varchar(255) DEFAULT NULL,
  `payment_id` text,
  `payment_amount` decimal(32,2) DEFAULT NULL,
  `payment_status` int(11) DEFAULT NULL COMMENT 'PAID, UNPAID, REFUNED, CHARGED',
  `payment_timestamp` timestamp NULL DEFAULT NULL,
  `created_timestamp` timestamp NULL DEFAULT NULL,
  `modified_timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table hoipos_v2.res_session
CREATE TABLE IF NOT EXISTS `res_session` (
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table hoipos_v2.res_timing
CREATE TABLE IF NOT EXISTS `res_timing` (
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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
