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

-- Dumping structure for table hoipos_staging.outlet
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

-- Dumping data for table hoipos_staging.outlet: ~15 rows (approximately)
/*!40000 ALTER TABLE `outlet` DISABLE KEYS */;
INSERT INTO `outlet` (`id`, `brand_id`, `outlet_name`, `outlet_address`, `outlet_receipt_footer`, `outlet_logo`, `outlet_receipt_logo`, `outlet_receipt_footer_logo`, `flow_type`, `enabled`, `license_key`, `modified_timestamp`, `created_timestamp`) VALUES
	(1, 1, 'HoiPOS Cafe (West)', '#02-21/AB/C Great World City\\n1 Kim Send Promenade\\nSingapore, 237994\\nTel No. 6732-3061\\nGST No: 19-880070-D', 'THANK YOU FOR SUPPORTING US\\nHAVE A WONDERFUL DAY', 'tunglok_logo', 'tunglok_logo_receipt', NULL, 1, 1, 'aaa', '2015-08-02 23:00:00', NULL),
	(2, 1, 'HoiPOS Cafe (East)', 'No. 2 Orchard Turn\\n#B2-51 Ion Orchard\\nSingapore 238801', 'THANK YOU FOR SUPPORTING US\\nHAVE A WONDERFUL DAY', '', '', NULL, 0, 1, NULL, '2015-08-02 23:00:00', NULL),
	(3, 1, 'HoiPOS Cafe (North)', 'Ang Mo Kio Ave. 3 #B2-26\\nSingapore 569933\\nTEL: 6453 2521/1976', 'THANK YOU FOR SUPPORTING US\\nHAVE A WONDERFUL DAY', '', '', NULL, 0, 1, NULL, '2015-08-02 23:00:00', NULL),
	(4, 3, 'VivoCity', '1 HarbourFront Walk, #02-150\\nSingapore 098585\\nGST Reg No: 201510863E', 'Flash this receipt to enjoy 10% off Skechers GoWalk 3 laced up collections at Skechers Vivo City #02-13/14 only. Valid till 14 July 2016. T&C apply.', '', '', NULL, 0, 1, NULL, '2015-11-11 08:30:00', NULL),
	(6, 1, 'Test Outlet (Duplication Test)', '', 'Thank You\\nWe\'d love to hear from you!\\nwww.caffebene.com.sg/feedback', '', '', NULL, 0, 1, 'BBCC', '2016-05-23 12:14:15', '2016-05-23 12:14:15'),
	(13, 1, 'Happy Dine', '15 Kent Ridge Drive, #01-03\nS 119245', 'Thank You', '', '', '', 1, 1, 'TLTE', '2016-10-29 01:14:22', '2016-06-20 09:46:53'),
	(14, 4, 'Zzapi', '100AM Shopping Mall, 100 Tras Street\n#01-11, Singapore 079027 \nGST: 201217715M', 'Thank you for dining with us\n Follow us on Facebook:\nhttps://www.facebook.com/zzapi.com.sg/', 'http://pos.hoicard.com/cms/logos/zzapi_logo.png', 'http://pos.hoicard.com/cms/logos/zzapi_logo_receipt.png', NULL, 1, 1, 'zzapi', '2016-07-14 20:36:47', '2016-06-20 15:24:28'),
	(15, 5, 'Arteastiq', '333A Orchard Road \nMandarin Gallery Level 4-14/15\nSingapore 238867\nTel: 6235 8705\nGST Reg No: 201202807R', 'THANK YOU FOR SUPPORTING US\nHAVE A WONDERFUL DAY', 'http://pos.hoicard.com/cms/logos/arteastiq_logo.png', 'http://pos.hoicard.com/cms/logos/arteastiq_logo_receipt.png', '', 1, 1, 'arteastiq', '2016-12-06 14:23:33', '2016-06-20 15:24:28'),
	(16, 6, 'Tung Lok Signatures (Orchard Parade Hotel)', '1 Tanglin Rd, 2/F Orchard Parade Hotel, Singapore 247905', '', 'tunglok_logo', 'tunglok_logo_receipt', NULL, 1, 1, 'tloph', '2016-07-09 11:30:12', '2016-07-09 09:46:53'),
	(17, 7, 'Blanco Court Beef Noodles', '92 Guillemard Rd, 399716\nTel : 6348 1708', 'Thank you for dining with us\n Follow us on Facebook:\nhttps://www.facebook.com/BlancoCourtBeefNoodles/', '', '', NULL, 1, 1, 'BCBN', '2016-08-02 17:35:12', '2016-07-21 23:46:15'),
	(18, 8, 'Le Binchotan', '115 Amoy Street #01-04\nSingapore 069935\nTel: 6221 6065\nGST Reg No: 201610182C', '', 'http://pos.hoicard.com/cms/logos/le_binchotan_logo_m.png', 'http://pos.hoicard.com/cms/logos/le_binchotan_logo_no_svc.png', 'http://pos.hoicard.com/cms/logos/le_binchotan_receipt_footer.png', 1, 1, 'LBCT', '2016-08-11 10:25:50', '2016-07-28 23:46:15'),
	(19, 9, 'the SPREAD', '15 Kent Ridge Drive, #01-03\nS 119245', 'Thank you', 'http://pos.hoicard.com/cms/logos/chengs_transparent.png', 'http://pos.hoicard.com/cms/logos/chengs.png', '', 0, 1, 'CHENG', '2017-01-08 22:10:51', '2016-08-19 23:15:15'),
	(20, 10, 'Sun Lok', '', '', '', '', '', 1, 1, 'SUNLOK', '2016-09-26 17:05:05', '2016-09-25 23:00:00'),
	(21, 11, 'Pharmex', '', '', '', '', '', 0, 1, 'PHARMEX', '2016-09-26 17:05:35', '2016-09-25 23:00:00'),
	(22, 5, 'Arteastiq Plaza Singapura', '68 Orchard Road \nPlaza Singapura #03-70/72\nSingapore 238839\nTel: 6336 0952\nGST Reg No: 201202807R', 'THANK YOU FOR SUPPORTING US\nHAVE A WONDERFUL DAY', 'http://pos.hoicard.com/cms/logos/arteastiq_logo.png', 'http://pos.hoicard.com/cms/logos/arteastiq_logo_receipt.png', '', 1, 1, '5e527bd61210680369769941c69ca936', '2016-12-07 16:15:08', '2016-12-06 14:23:09');
/*!40000 ALTER TABLE `outlet` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
