-- Monday 25 Feb 2019
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE IF NOT EXISTS `klbox` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `klbox`;

CREATE TABLE IF NOT EXISTS `ms_article` (
  `article_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` varchar(4) NOT NULL,
  `article` varchar(100) DEFAULT NULL,
  `customer_article` varchar(100) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `uom` varchar(100) DEFAULT NULL,
  `conversion_value` int(11) DEFAULT NULL,
  `safety_stock` int(11) DEFAULT NULL,
  `column` varchar(100) DEFAULT NULL,
  `rack` varchar(100) DEFAULT NULL,
  `row` varchar(100) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,  
  `status` tinyint(4) DEFAULT '1',
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `field_sync` tinyint(4) DEFAULT '0',
  `last_sync` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`article_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `ms_article` DISABLE KEYS */;
INSERT INTO `ms_article` (`article_id`, `site_id`, `article`, `customer_article`, `description`, `uom`, `conversion_value`, `safety_stock`, `column`, `rack`, `row`, `price`, `chamber_sync_flag`, `field_sync`, `status`, `created_at`, `created_by`, `created_ip`, `updated_at`, `updated_by`, `updated_ip`) VALUES
	(1, '', '888888', '1', '1', '1', 1, 1, '1', '1', '1', 1, 0, 0, 1, '2019-02-13 10:12:16', '1', '::1', '2019-02-13 11:01:04', '1', '::1'),
	(2, '', 'asdadads', 'kjhkjhkhj', 'kjhkjhkjhk', 'kjhkjhkjh', 1111, 1111, 'jhkjhkjh', 'jkhkjhkj', 'kjhkjhkj', 1131231, 0, 0, 1, '2019-02-13 10:55:19', '1', '::1', NULL, NULL, NULL),
	(3, '', 'pepsodent', 'unilever', 'test1', 'EA', 1, 1, 'a', 'a', 'a', 1, 0, 0, 1, '2019-02-14 01:47:49', '1', '::1', NULL, NULL, NULL),
	(4, '', 'test1', '1', '1', '1', 1, 1, '1', '1', '1', 1, 0, 0, -1, '2019-02-14 02:36:09', '1', '::1', '2019-02-14 02:36:22', '1', '::1'),
	(5, 'SZ20', 'asdadads', 'unilever', 'klkjhjkhjhkjhk', 'EA', 1, 1, 'jhkjhkjh', 'jkhkjhkj', 'kjhkjhkj', 999, 0, 0, 1, '2019-02-18 07:51:02', '1', '::1', NULL, NULL, NULL),
	(6, 'SZ20', '2', '2', '2', '2', 2, 2, '2', '2', '2', 2, 0, 0, 1, '2019-02-19 02:56:30', '1', '::1', '2019-02-19 02:59:47', '1', '::1'),
	(7, 'SZ20', 'pepsodent1111111111111111', 'unilever1', 'kkkklklkj1', 'EA1', 11, 11, 'kkkk1', 'kkkk1', 'kkkk1', 1, 0, 0, -1, '2019-02-22 02:27:50', '1', '::1', '2019-02-22 02:29:26', '1', '::1');
/*!40000 ALTER TABLE `ms_article` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `ms_article_attribute` (
  `article_attribute_id` int(11) NOT NULL AUTO_INCREMENT,
  `attribute_name` varchar(100) NOT NULL,
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `field_sync` tinyint(4) DEFAULT '0',
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`article_attribute_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `ms_article_attribute` DISABLE KEYS */;
INSERT INTO `ms_article_attribute` (`article_attribute_id`, `attribute_name`, `chamber_sync_flag`, `field_sync`, `status`, `created_at`, `created_by`, `created_ip`, `updated_at`, `updated_by`, `updated_ip`) VALUES
	(1, '0', 0, 0, 1, '2019-02-20 09:59:23', '1', '::1', NULL, NULL, NULL),
	(2, '0', 0, 0, 1, '2019-02-20 09:59:34', '1', '::1', NULL, NULL, NULL),
	(3, '888', 0, 0, 1, '2019-02-20 10:00:44', '1', '::1', '2019-02-20 10:15:37', '1', '::1'),
	(4, '1234', 0, 0, -1, '2019-02-22 02:31:05', '1', '::1', '2019-02-22 02:31:36', '1', '::1'),
	(5, '0', 0, 0, -1, '2019-02-22 02:43:42', '1', '::1', '2019-02-22 02:44:14', '1', '::1'),
	(6, '0', 0, 0, -1, '2019-02-22 02:44:27', '1', '::1', '2019-02-22 02:44:57', '1', '::1'),
	(7, 'test', 0, 0, -1, '2019-02-22 02:45:05', '1', '::1', '2019-02-22 02:45:15', '1', '::1');
/*!40000 ALTER TABLE `ms_article_attribute` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `ms_article_attribute_value` (
  `article_attribute_value_id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `article_attribute_id` int(11) NOT NULL,
  `attribute_value` varchar(100) DEFAULT NULL,
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `field_sync` tinyint(4) DEFAULT '0',
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`article_attribute_value_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `ms_article_attribute_value` DISABLE KEYS */;
INSERT INTO `ms_article_attribute_value` (`article_attribute_value_id`, `article_id`, `article_attribute_id`, `attribute_value`, `chamber_sync_flag`, `field_sync`, `status`, `created_at`, `created_by`, `created_ip`, `updated_at`, `updated_by`, `updated_ip`) VALUES
	(1, 2, 3, NULL, 0, 0, 1, '2019-02-22 03:13:33', '1', '::1', NULL, NULL, NULL),
	(2, 2, 3, NULL, 0, 0, 1, '2019-02-22 03:13:55', '1', '::1', NULL, NULL, NULL),
	(3, 5, 2, 'test1', 0, 0, 1, '2019-02-22 03:14:18', '1', '::1', '2019-02-22 03:38:01', '1', '::1'),
	(4, 3, 3, 'test1', 0, 0, -1, '2019-02-22 03:38:18', '1', '::1', '2019-02-22 03:38:46', '1', '::1');
/*!40000 ALTER TABLE `ms_article_attribute_value` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `ms_article_po` (
  `article_po_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` varchar(4) NOT NULL,
  `article` varchar(100) DEFAULT NULL,
  `customer_article` varchar(100) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `po_blanket_number` varchar(200) DEFAULT NULL,
  `po_blanket_qty` int(10) DEFAULT NULL,
  `po_created_date` datetime DEFAULT NULL,
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `field_sync` tinyint(4) DEFAULT '0',
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`article_po_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `ms_article_po` DISABLE KEYS */;
INSERT INTO `ms_article_po` (`article_po_id`, `site_id`, `article`, `customer_article`, `description`, `po_blanket_number`, `po_blanket_qty`, `po_created_date`, `chamber_sync_flag`, `field_sync`, `status`, `created_at`, `created_by`, `created_ip`, `updated_at`, `updated_by`, `updated_ip`) VALUES
	(1, 'SZ03', 'pepsodent', 'ddd5', 'ddd5', 'ddd5', 1115555, '0000-00-00 00:00:00', 0, 0, -1, '2019-02-25 04:01:36', '1', '::1', '2019-02-25 04:02:29', '1', '::1');
/*!40000 ALTER TABLE `ms_article_po` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `ms_article_stock` (
  `article_stock_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` varchar(4) NOT NULL,
  `article` varchar(100) NOT NULL,
  `customer_article` varchar(100) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `stock_qty` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `field_sync` tinyint(4) DEFAULT '0',
  `last_sync` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`article_stock_id`),
  UNIQUE KEY `site_id_article` (`site_id`,`article`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `ms_article_stock` DISABLE KEYS */;
INSERT INTO `ms_article_stock` (`article_stock_id`, `site_id`, `article`, `customer_article`, `description`, `stock_qty`, `status`, `created_at`, `created_by`, `created_ip`, `updated_at`, `updated_by`, `updated_ip`) VALUES
	(1, '', 'asdadads', 'unilever', 'test1', 100, -1, '2019-02-22 06:33:06', '1', '::1', '2019-02-22 08:00:46', '1', '::1'),
	(2, 'SZ20', '2', 'unilever', 'test1', 100, -1, '2019-02-22 06:43:10', '1', '::1', '2019-02-22 08:00:40', '1', '::1'),
	(3, 'SZ20', '888888', 'unilever', '1', 1, 1, '2019-02-22 08:03:06', '1', '::1', NULL, NULL, NULL),
	(4, 'SZ20', 'asdadads', 'unilever', 'test1', 100, 1, '2019-02-22 08:01:10', '1', '::1', NULL, NULL, NULL),
	(7, 'SZ20', 'pepsodent', 'unilever', 'test1', 100, 1, '2019-02-22 08:29:58', '1', '::1', NULL, NULL, NULL);
/*!40000 ALTER TABLE `ms_article_stock` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `ms_capability` (
  `capability_id` int(11) NOT NULL AUTO_INCREMENT,
  `capability` varchar(100) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`capability_id`),
  UNIQUE KEY `capability` (`capability`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `ms_capability` DISABLE KEYS */;
INSERT INTO `ms_capability` (`capability_id`, `capability`, `status`, `created_at`, `created_by`, `created_ip`, `updated_at`, `updated_by`, `updated_ip`) VALUES
	(1, 'site', 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(2, 'pic', 1, NULL, NULL, NULL, '2019-02-18 07:35:34', '1', '::1'),
	(3, 'company', 1, NULL, NULL, NULL, '2019-02-18 07:35:11', '1', '::1'),
	(4, 'transaction', 1, '2019-02-18 07:35:21', '1', '::1', NULL, NULL, NULL),
	(5, 'dashboard', 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(6, 'role', 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(7, 'capability', 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(8, 'role-capability', 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(9, 'user', 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(10, 'user-attribute', 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(11, 'user-role', 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(12, 'article', 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(13, 'article-attribute', 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(14, 'article-stock', 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(15, 'article-attribute-value', 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(16, 'article-po', 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(17, 'rfid-article', 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(18, 'level', 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(19, 'reason', 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(20, 'reason-type', 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(21, 'reason-type-mapping', 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(22, 'movement-article', 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(23, 'movement-quota-level', 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(24, 'article-logistic-site', 1, NULL, NULL, NULL, NULL, NULL, NULL);
/*!40000 ALTER TABLE `ms_capability` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `ms_company` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(150) DEFAULT NULL,
  `company_address` varchar(255) DEFAULT NULL,
  `company_phone` varchar(50) DEFAULT NULL,
  `company_pic` varchar(150) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `ms_company` DISABLE KEYS */;
INSERT INTO `ms_company` (`company_id`, `company_name`, `company_address`, `company_phone`, `company_pic`, `status`, `created_at`, `created_by`, `created_ip`, `updated_at`, `updated_by`, `updated_ip`) VALUES
	(1, 'PT Yamaha', 'Jl PRJ', '021123456', 'Bejo', 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(2, 'PT Honda Indah Pertama', 'Jl bidara no 9', '11021312', 'Astra', 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(3, '02/02/2019', 'Jl laksa tes', '2250212312', 'Saiful', 1, NULL, NULL, NULL, '2019-02-02 13:52:15', '1', '::1'),
	(4, 'armora', 'aa', 'aa', 'aa', -1, '2019-01-24 11:15:40', '1', '103.44.39.159', '2019-02-02 14:03:04', '1', '::1'),
	(5, 'PT A', 'jln a', '082138123', 'Andrea', 1, '2019-01-24 11:20:58', '1', '202.51.117.186', NULL, NULL, NULL),
	(6, 'PT Unilever', 'Jl antah berantah', '11021013232', 'Pak Ujang a', 1, '2019-01-29 11:27:10', '1', '115.178.212.30', '2019-01-31 10:33:25', '1', '115.178.217.88'),
	(7, 'PT. UNILEVER TBK', 'JL. JABABEKA V BLOK U NO. 14-16, KARANG BARU BEKASI', '021-28638090', 'SALESMAN CIKARANG', 1, '2019-02-08 02:52:16', '1', '202.51.117.186', NULL, NULL, NULL),
	(8, 'manga', 'yes ', '220202', 'donuts aa', 1, '2019-02-11 05:54:41', '1', '115.178.204.21', '2019-02-17 05:55:51', '1', '::1'),
	(9, 'PT Serba Bisa Apa Aja', 'Mall Pondok Indah ', '021 222221sssss', 'Ali Akbar', 1, '2019-02-21 02:52:02', '1', '::1', '2019-02-21 03:22:12', '1', '::1'),
	(10, 'PT Apa Aja bisa', 'Mall Taman Anggrek', '021 2222223', 'Sehu Ali Akbar ', -1, '2019-02-21 03:29:50', '1', '::1', '2019-02-22 02:12:03', '1', '::1');
/*!40000 ALTER TABLE `ms_company` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `ms_config` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` varchar(4) NOT NULL,
  `config_name` varchar(100) DEFAULT NULL,
  `config_value` tinyint(4) DEFAULT NULL,
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `field_sync` tinyint(4) DEFAULT '0',
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `ms_config` DISABLE KEYS */;
INSERT INTO `ms_config` (`config_id`, `site_id`, `config_name`, `config_value`, `chamber_sync_flag`, `field_sync`, `status`, `created_at`, `created_by`, `created_ip`, `updated_at`, `updated_by`, `updated_ip`) VALUES
	(1, 'SZ03', 'test2', 123, 0, 0, -1, '2019-02-22 10:23:02', '1', '::1', '2019-02-22 10:23:45', '1', '::1'),
	(2, 'SZ20', 'test1', 0, 0, 0, 1, '2019-02-22 10:27:52', '1', '::1', '2019-02-22 10:29:09', '1', '::1');
/*!40000 ALTER TABLE `ms_config` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `ms_level` (
  `level_id` int(11) NOT NULL AUTO_INCREMENT,
  `level_hierarchy` int(11) DEFAULT '1',
  `level_name` varchar(100) DEFAULT NULL,
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `field_sync` tinyint(4) DEFAULT '0',
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`level_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*!40000 ALTER TABLE `ms_level` DISABLE KEYS */;
INSERT INTO `ms_level` (`level_id`, `level_hierarchy`, `level_name`, `chamber_sync_flag`, `field_sync`, `status`, `created_at`, `created_by`, `created_ip`, `updated_at`, `updated_by`, `updated_ip`) VALUES
	(1, 1, 'Product Manager aa', 0, 0, 1, NULL, NULL, NULL, '2019-02-06 11:09:26', '1', '::1'),
	(2, 2, 'Assistant Director', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(3, 3, 'bos', 0, 0, 1, '2019-02-13 03:04:10', '1', '202.51.117.186', NULL, NULL, NULL),
	(4, 11, 'sdfsfs', 0, 0, 1, '2019-02-21 07:00:06', '1', '::1', '2019-02-21 07:00:24', '1', '::1'),
	(5, 11114335, 'sdfsfsdfdsdfgdg', 0, 0, -1, '2019-02-21 07:02:48', '1', '::1', '2019-02-21 07:03:05', '1', '::1');
/*!40000 ALTER TABLE `ms_level` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `ms_pic` (
  `pic_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` varchar(4) NOT NULL,
  `pic_name` varchar(100) DEFAULT NULL,
  `pic_phone` varchar(100) DEFAULT NULL,
  `pic_email` varchar(100) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`pic_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*!40000 ALTER TABLE `ms_pic` DISABLE KEYS */;
INSERT INTO `ms_pic` (`pic_id`, `site_id`, `pic_name`, `pic_phone`, `pic_email`, `status`, `created_at`, `created_by`, `created_ip`, `updated_at`, `updated_by`, `updated_ip`) VALUES
	(1, '1100', 'selena gwena', '20121111', 'ese@gmail.com', 1, NULL, NULL, NULL, '2019-02-06 08:35:24', '1', '::1'),
	(2, '0', 'Maria', '112313131', 'mari@gmail.com', 1, NULL, NULL, NULL, '2019-02-22 02:11:50', '1', '::1'),
	(3, '0', 'ali', '087785624965', 'ali@gmail.com', -1, '2019-02-21 02:59:38', '1', '::1', '2019-02-21 03:00:07', '1', '::1'),
	(4, '0', 'ali akbar', '08778562496500', 'ali.akbar@gmail.com', -1, '2019-02-21 04:57:18', '1', '::1', '2019-02-21 05:00:48', '1', '::1'),
	(5, 'SZ03', 'ali akbar', '08778562496500', 'ali.@gmail.com', -1, '2019-02-22 02:45:42', '1', '::1', '2019-02-22 02:46:23', '1', '::1');
/*!40000 ALTER TABLE `ms_pic` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `ms_reason` (
  `reason_id` int(11) NOT NULL AUTO_INCREMENT,
  `reason_value` varchar(100) DEFAULT NULL,
  `is_replenish` tinyint(4) DEFAULT '0',
  `status` tinyint(4) DEFAULT '1',
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `field_sync` tinyint(4) DEFAULT '0',
  `last_sync` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`reason_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

/*!40000 ALTER TABLE `ms_reason` DISABLE KEYS */;
INSERT INTO `ms_reason` (`reason_id`, `reason_value`, `status`, `created_at`, `created_by`, `created_ip`, `updated_at`, `updated_by`, `updated_ip`) VALUES
	(1, 'No money dollar', 1, NULL, NULL, NULL, '2019-02-06 08:51:11', '1', '::1'),
	(2, 'machine error', 1, NULL, NULL, NULL, NULL, NULL, NULL),
	(3, 'Testing Reasin', 1, '2019-02-06 08:51:43', '1', '::1', NULL, NULL, NULL),
	(4, '1', 1, '2019-02-13 03:03:51', '1', '202.51.117.186', NULL, NULL, NULL),
	(5, 'abc', 1, '2019-02-13 03:04:32', '1', '202.51.117.186', NULL, NULL, NULL),
	(6, 'testqaaa', 1, '2019-02-20 09:44:48', '1', '::1', '2019-02-20 09:45:00', '1', '::1'),
	(7, 'ali', 1, '2019-02-21 03:01:11', '1', '::1', NULL, NULL, NULL),
	(8, '2', -1, '2019-02-21 06:59:10', '1', '::1', '2019-02-21 06:59:26', '1', '::1');
/*!40000 ALTER TABLE `ms_reason` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `ms_reason_type` (
  `reason_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `attribute` varchar(100) NOT NULL,
  `attribute_value` varchar(100) NOT NULL,
  `status` tinyint(4) DEFAULT '1',
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `field_sync` tinyint(4) DEFAULT '0',
  `last_sync` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`reason_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `ms_reason_type` DISABLE KEYS */;
INSERT INTO `ms_reason_type` (`reason_type_id`, `article_attribute_id`, `attribute_value`, `site_id`, `chamber_sync_flag`, `field_sync`, `status`, `created_at`, `created_by`, `created_ip`, `updated_at`, `updated_by`, `updated_ip`) VALUES
	(1, 2, 'test1', NULL, 0, 0, 1, '2019-02-20 09:45:32', '1', '::1', '2019-02-20 09:49:10', '1', '::1'),
	(2, 1, 'test1', NULL, 0, 0, 1, '2019-02-20 09:55:31', '1', '::1', '2019-02-20 10:16:40', '1', '::1'),
	(3, 2, 'test1', NULL, 0, 0, 1, '2019-02-22 03:39:17', '1', '::1', '2019-02-22 03:41:25', '1', '::1'),
	(4, 3, 'test1', NULL, 0, 0, 1, '2019-02-22 03:45:12', '1', '::1', NULL, NULL, NULL),
	(5, 3, 'test1', NULL, 0, 0, 1, '2019-02-22 03:48:29', '1', '::1', NULL, NULL, NULL),
	(6, 3, '1', 'SZ20', 0, 0, 1, '2019-02-22 03:55:34', '1', '::1', '2019-02-22 04:05:07', '1', '::1'),
	(7, 3, 'test1', 'SZ20', 0, 0, 1, '2019-02-22 04:05:23', '1', '::1', NULL, NULL, NULL),
	(8, 1, 'test123', 'SZ03', 0, 0, -1, '2019-02-22 04:05:54', '1', '::1', '2019-02-22 04:06:22', '1', '::1');
/*!40000 ALTER TABLE `ms_reason_type` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `ms_reason_type_mapping` (
  `reason_type_mapping_id` int(11) NOT NULL AUTO_INCREMENT,
  `reason_type_id` int(11) NOT NULL,
  `reason_id` int(11) NOT NULL,
  `status` tinyint(4) DEFAULT '1',
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `field_sync` tinyint(4) DEFAULT '0',
  `last_sync` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`reason_type_mapping_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `ms_reason_type_mapping` DISABLE KEYS */;
INSERT INTO `ms_reason_type_mapping` (`reason_type_mapping_id`, `reason_type_id`, `reason_id`, `chamber_sync_flag`, `field_sync`, `status`, `created_at`, `created_by`, `created_ip`, `updated_at`, `updated_by`, `updated_ip`) VALUES
	(1, 2, 7, 0, 0, 1, '2019-02-21 10:19:27', '1', '::1', NULL, NULL, NULL),
	(2, 2, 7, 0, 0, 1, '2019-02-21 10:20:49', '1', '::1', NULL, NULL, NULL),
	(3, 1, 4, 0, 0, -1, '2019-02-22 02:18:14', '1', '::1', '2019-02-22 02:25:48', '1', '::1');
/*!40000 ALTER TABLE `ms_reason_type_mapping` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `ms_rfid_article` (
  `rfid_article_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` varchar(4) NOT NULL,
  `outbound_delivery` varchar(15) NOT NULL,
  `article` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `rfid` varchar(200) DEFAULT NULL,
  `picktime` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  'status_message' VARCHAR(25) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `field_sync` tinyint(4) DEFAULT '0',
  `last_sync` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`rfid_article_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `ms_rfid_article` DISABLE KEYS */;
INSERT INTO `ms_rfid_article` (`rfid_article_id`, `site_id`, `outbound_delivery`, `article`, `description`, `rfid`, `picktime`, `user_id`,  `chamber_sync_flag`, `field_sync`, `status`, `created_at`, `created_by`, `created_ip`, `updated_at`, `updated_by`, `updated_ip`) VALUES
	(1, 'MZ10', '1', 'pepsodent', 'test11111', 'sddsss', '0000-00-00 00:00:00', 11, NULL, 0, 1, '2019-02-25 03:04:12', '1', '::1', '2019-02-25 03:29:01', '1', '::1');
/*!40000 ALTER TABLE `ms_rfid_article` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `ms_role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) DEFAULT NULL,
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `ms_role` DISABLE KEYS */;
INSERT INTO `ms_role` (`role_id`, `role_name`, `chamber_sync_flag`, `status`, `created_at`, `created_by`, `created_ip`, `updated_at`, `updated_by`, `updated_ip`) VALUES
	(1, 'admin', 0, 1, '2019-02-15 06:57:39', '1', '::1', '2019-02-15 06:58:02', '1', '::1'),
	(2, 'replenish', 0, 1, '2019-02-15 07:42:29', '1', '::1', NULL, NULL, NULL),
	(3, 'capability', 0, 1, '2019-02-15 07:42:43', '1', '::1', NULL, NULL, NULL),
	(4, 'role-capability', 0, -1, '2019-02-19 03:52:10', '1', '::1', '2019-02-19 03:52:30', '1', '::1'),
	(5, 'company', 0, -1, '2019-02-22 04:03:43', '1', '::1', '2019-02-22 04:04:00', '1', '::1');
/*!40000 ALTER TABLE `ms_role` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `ms_role_capability` (
  `role_capability_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` varchar(100) DEFAULT NULL,
  `capability_id` int(11) NOT NULL,
  `create` tinyint(4) DEFAULT '0',
  `read` tinyint(4) DEFAULT '0',
  `update` tinyint(4) DEFAULT '0',
  `delete` tinyint(4) DEFAULT '0',
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  UNIQUE KEY `role_capability` (`role_id`,`capability_id`),
  PRIMARY KEY (`role_capability_id`)
) ENGINE=InnoDB AUTO_INCREMENT=374 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `ms_role_capability` DISABLE KEYS */;
INSERT INTO `ms_role_capability` (`role_capability_id`, `role_id`, `capability_id`, `create`, `read`, `update`, `delete`, `status`, `created_at`, `created_by`, `created_ip`, `updated_at`, `updated_by`, `updated_ip`) VALUES
	(64, '1', 1, 1, 0, 0, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL),
	(65, '1', 2, 0, 0, 0, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL),
	(66, '1', 3, 0, 1, 0, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL),
	(67, '1', 4, 0, 0, 0, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL),
	(68, '1', 5, 0, 0, 0, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL),
	(69, '1', 6, 0, 0, 0, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL),
	(70, '1', 7, 0, 1, 0, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL),
	(71, '1', 8, 0, 0, 0, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL),
	(72, '1', 9, 0, 0, 0, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL),
	(73, '1', 10, 0, 0, 0, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL),
	(74, '1', 11, 0, 0, 0, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL),
	(75, '1', 12, 0, 0, 0, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL),
	(76, '1', 13, 0, 0, 0, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL),
	(77, '1', 14, 0, 0, 0, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL),
	(78, '1', 15, 0, 0, 0, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL),
	(79, '1', 16, 0, 0, 1, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL),
	(80, '1', 17, 0, 0, 0, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL),
	(81, '1', 18, 0, 0, 0, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL),
	(82, '1', 19, 0, 0, 0, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL),
	(83, '1', 20, 0, 0, 0, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL),
	(84, '1', 21, 0, 0, 0, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL),
	(85, '1', 22, 0, 0, 0, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL),
	(86, '1', 23, 0, 0, 0, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL),
	(87, '1', 24, 0, 0, 0, 0, 1, '2019-02-22 21:22:20', NULL, NULL, NULL, NULL, NULL);


CREATE TABLE IF NOT EXISTS `ms_site` (
  `site_id` varchar(10) NOT NULL,
  `company_id` int(11) NOT NULL,
  `site_name` varchar(100) DEFAULT NULL,
  `site_address` varchar(200) DEFAULT NULL,
  `site_qty_value` int(11) DEFAULT NULL,
  `flag_qty_value` varchar(100) DEFAULT NULL,
  `method_calc` varchar(25) DEFAULT NULL,
  `start_date_counting` datetime DEFAULT NULL,
  `reset_days` int(11) DEFAULT NULL,
  `logo_file_name` text,
  `status` tinyint(4) DEFAULT '1',
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `field_sync` tinyint(4) DEFAULT '0',
  `last_sync` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40000 ALTER TABLE `ms_site` DISABLE KEYS */;
INSERT INTO `ms_site` (`site_id`, `company_id`, `site_name`, `site_address`, `site_qty_value`, `flag_qty_value`, `method_calc`, `start_date_counting`, `reset_days`, `logo_file_name`, `chamber_sync_flag`, `field_sync`, `status`, `created_at`, `created_by`, `created_ip`, `updated_at`, `updated_by`, `updated_ip`) VALUES
	('', 0, 'SZ10', 'test1', 0, 'test1', 'test1', '0000-00-00 00:00:00', 0, 'Capture.PNG', 0, 0, -1, '2019-02-21 02:55:19', '1', '::1', '2019-02-21 04:05:03', '1', '::1'),
	('MZ10', 0, 'test', 'aaaa', 0, 'aaaaa', 'aaaaa', '0000-00-00 00:00:00', 0, '', 0, 0, 1, '2019-02-22 09:39:20', '1', '::1', NULL, NULL, NULL),
	('SZ02', 8, 'test1l', 'test1l', 11, 'test1l', 'test1l', '0000-00-00 00:00:00', 19, '', 0, 0, -1, '2019-02-21 06:34:05', '1', '::1', '2019-02-21 06:58:20', '1', '::1'),
	('SZ03', 9, 'test2', 'tes2', 11, '11', 'abc', '0000-00-00 00:00:00', 1, 'Save-Button.jpg', 0, 0, 1, '2019-02-21 06:55:44', '1', '::1', NULL, NULL, NULL),
	('SZ20', 2, 'KL0021', 'Jl mesin no 9 AB', 11, '11', 'abc', '0000-00-00 00:00:00', 4, '', 0, 0, 1, NULL, NULL, NULL, '2019-02-21 04:34:44', '1', '::1'),
	('SZ24', 1100000204, 'KL001', 'Jl antara no 1', 5, NULL, NULL, '2019-02-06 10:39:44', 4, NULL, 0, 0, -1, NULL, NULL, NULL, '2019-02-06 11:00:20', '1', '::1');
/*!40000 ALTER TABLE `ms_site` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `ms_token` (
  `token_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `token` varchar(100) DEFAULT NULL,
  `expired_at` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`token_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `ms_token` DISABLE KEYS */;
INSERT INTO `ms_token` (`token_id`, `name`, `token`, `expired_at`, `status`, `created_at`, `created_by`, `created_ip`, `updated_at`, `updated_by`, `updated_ip`) VALUES
	(1, 'key', 'macbook', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL);
/*!40000 ALTER TABLE `ms_token` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `ms_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` varchar(10) DEFAULT NULL,
  `parent_user_id` int(11) DEFAULT NULL,
  `level_id` int(11) DEFAULT NULL,
  `user_code` varchar(100) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `quota_initial` int(11) DEFAULT NULL,
  `quota_additional` int(11) DEFAULT NULL,
  `quota_remaining` int(11) DEFAULT NULL,
  `job_title` varchar(100) DEFAULT NULL,
  `attribute` varchar(100) DEFAULT NULL,
  `attribute_value` varchar(100) DEFAULT NULL,
  `division` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `user_category` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `counter_wrong_pass` tinyint(4) DEFAULT '0',
  `status_lock` tinyint(4) DEFAULT '0',
  `locked_time` datetime DEFAULT NULL,
  `reset_by` varchar(100) DEFAULT NULL,
  `reset_time` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

/*!40000 ALTER TABLE `ms_user` DISABLE KEYS */;
INSERT INTO `ms_user` (`user_id`, `site_id`, `parent_user_id`, `level_id`, `user_code`, `firstname`, `lastname`, `quota_initial`, `quota_additional`, `quota_remaining`, `job_title`, `division`, `email`, `user_category`, `password`, `counter_wrong_pass`, `status_lock`, `locked_time`, `reset_by`, `reset_time`, `status`, `created_at`, `created_by`, `created_ip`, `updated_at`, `updated_by`, `updated_ip`) VALUES
	(1, 'SZ20', NULL, 1, 'admin', 'mr admin', 'test', NULL, NULL, NULL, 'Presdir', 'IT', 'aa', 'client', 'eyJpdiI6IlN5S3BXQmlmOGdOY2VPaDVocGVIYUE9PSIsInZhbHVlIjoiQ3BQRTE3SVVXK2d4ZTRrVjRcL3dFK2c9PSIsIm1hYyI6ImRlMmM3ZjUxZTc5Y2RlMDE1YTI5Y2Y4YTQzNzI1YTI1MmIxYWM5MmMyMjU2NDA4ODkzYjIzZjdhMmFlM2U3NjQifQ==', 0, 0, NULL, NULL, NULL, 1, NULL, NULL, NULL, '2019-02-07 05:02:18', '1', '::1'),
	(2, NULL, NULL, NULL, 'andreas.bk', NULL, NULL, NULL, NULL, NULL, NULL, 'Kawanlama', 'budi_pioneer@yahoo.com', 'head', NULL, 0, 0, NULL, NULL, NULL, 1, '2019-02-13 02:38:34', '1', '202.51.117.186', NULL, NULL, NULL),
	(3, 'SZ20', 2, 2, 'asli2', '2', '2', 0, 2, 2, '2', '2', '2', '2', '2', 2, 0, '0000-00-00 00:00:00', '2ppoo', NULL, -1, '2019-02-14 08:02:41', '1', '::1', '2019-02-15 14:21:11', '1', '36.70.235.71'),
	(4, 'SZ20', 0, 3, 'a', 'a', 'a', 0, 0, 0, 'a', 'a', 'a', 'a', 'a', 0, 0, '0000-00-00 00:00:00', 'a', NULL, 1, '2019-02-20 09:40:16', '1', '::1', NULL, NULL, NULL),
	(5, '', 0, 4, 'asli2', 'a', 's', 0, 0, 0, 's', 's', 's', 'dashboard', 'ssss', 0, 0, '0000-00-00 00:00:00', 'ssss', '0000-00-00 00:00:00', 1, '2019-02-21 07:11:25', '1', '::1', '2019-02-21 09:33:10', '1', '::1'),
	(6, 'SZ20', 1, 4, 'p', 'p', 'p', 1, 1, 1, 'p', 'p', 'p', 'chamber', 'p', 0, 0, '0000-00-00 00:00:00', 'p', '0000-00-00 00:00:00', -1, '2019-02-21 09:34:08', '1', '::1', '2019-02-21 10:03:00', '1', '::1'),
	(7, 'SZ03', 0, 4, 'test20', 'test20', 'test20', 0, 0, 0, 'test20', 'test20', 'test20', 'chamber', 'test20', 0, 0, '0000-00-00 00:00:00', 'test20', '0000-00-00 00:00:00', -1, '2019-02-21 09:58:44', '1', '::1', '2019-02-21 10:00:28', '1', '::1');
/*!40000 ALTER TABLE `ms_user` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `ms_user_attribute` (
  `user_attribute_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `attribute` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `field_sync` tinyint(4) DEFAULT '0',
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`user_attribute_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

/*!40000 ALTER TABLE `ms_user_attribute` DISABLE KEYS */;
INSERT INTO `ms_user_attribute` (`user_attribute_id`, `user_id`, `attribute`, `value`, `chamber_sync_flag`, `field_sync`, `status`, `created_at`, `created_by`, `created_ip`, `updated_at`, `updated_by`, `updated_ip`) VALUES
	(1, 0, 'test', 'test', 0, 0, 1, '2019-02-07 10:22:00', '1', '::1', NULL, NULL, NULL),
	(2, 123, 'test2', '2', 0, 0, 1, '2019-02-12 03:41:35', '1', '::1', NULL, NULL, NULL),
	(3, 3, '1sdfsfs', '3sdfsdfdsf', 0, 0, 1, '2019-02-12 03:42:03', '1', '::1', '2019-02-18 07:34:31', '1', '::1'),
	(4, 2, 'assss', 'sssssss', 0, 0, -1, '2019-02-18 07:33:44', '1', '::1', '2019-02-18 07:34:00', '1', '::1'),
	(5, 3, 'test5', '5', 0, 0, 1, '2019-02-18 07:46:13', '1', '::1', NULL, NULL, NULL),
	(6, 5, 'test1kjkjkjk', '5sdsfsdfsd9999', 0, 0, -1, '2019-02-21 10:14:33', '1', '::1', '2019-02-21 10:15:02', '1', '::1');
/*!40000 ALTER TABLE `ms_user_attribute` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `ms_user_role` (
  `user_role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` varchar(100) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`user_role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `ms_user_role` DISABLE KEYS */;
INSERT INTO `ms_user_role` (`user_role_id`, `role_id`, `user_id`, `status`, `created_at`, `created_by`, `created_ip`, `updated_at`, `updated_by`, `updated_ip`) VALUES
	(1, '1', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL);
/*!40000 ALTER TABLE `ms_user_role` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `tr_article_logistic_site` (
  `article_logistic_site_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` varchar(4) NOT NULL,
  `outbound_delivery` varchar(15) NOT NULL,
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`article_logistic_site_id`,`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tr_article_logistic_site_detail` (
  `article_logistic_site_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `outbound_delivery` varchar(15) NOT NULL,
  `article` varchar(100) NOT NULL,
  `customer_article` varchar(100) NOT NULL,
  `description` varchar(200) DEFAULT NULL,
  `qty_issue` int(11) DEFAULT NULL,
  `conv_uom` varchar(10) DEFAULT NULL,
  `qty_receive_actual` int(11) DEFAULT NULL,
  `qty_receive` int(11) DEFAULT NULL,
  `disc_minus` int(11) DEFAULT NULL,
  `disc_plus` int(11) DEFAULT NULL,
  `conversion_diff` int(11) DEFAULT NULL COMMENT 'selisih conversion',
  `dashboard_received_date` datetime DEFAULT NULL,
  `qty_chamber` varchar(100) DEFAULT NULL,
  `chamber_disc_minus` int(11) DEFAULT NULL,
  `chamber_disc_plus` int(11) DEFAULT NULL,
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `field_sync` tinyint(4) DEFAULT '0',
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`article_logistic_site_detail_id`,`outbound_delivery`,`article`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `tr_article_logistic_site_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `tr_article_logistic_site_detail` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `tr_article_po_history` (
  `article_po_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `article_po_id` int(11) NOT NULL,
  `po_usage_qty` int(11) DEFAULT NULL,
  `po_remaining_qty` int(11) DEFAULT NULL,
  `po_created_date` datetime DEFAULT NULL,
  `status_in_out` varchar(4) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`article_po_history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40000 ALTER TABLE `tr_article_po_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `tr_article_po_history` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `tr_log` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `data` text,
  `json` text,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*!40000 ALTER TABLE `tr_log` DISABLE KEYS */;
INSERT INTO `tr_log` (`log_id`, `name`, `url`, `data`, `json`, `created_at`, `created_by`, `created_ip`) VALUES
	(1, '/kalibri/client/company', '/kalibri/client/company', '{"name":"ujang"}', '{"name":"ujang"}', '2019-02-01 11:35:56', 'BOT', '::1');
/*!40000 ALTER TABLE `tr_log` ENABLE KEYS */;

CREATE TABLE IF NOT EXISTS `tr_transaction` (
  `transaction_id` varchar(100) NOT NULL,
  `site_id` varchar(4) NOT NULL,
  `user_id` int(11) NOT NULL,
  `article` varchar(100) DEFAULT NULL,
  `customer_article` varchar(100) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  `status_in_out` varchar(10) DEFAULT NULL COMMENT 'in or out',
  `reason_id` int(11) DEFAULT NULL,
  `wo_wbs` varchar(100) DEFAULT NULL,
  `remark` varchar(200) DEFAULT NULL,  
  `outbound_delivery` VARCHAR(10) NOT NULL,
  `rfid` VARCHAR(200) NOT NULL,
  `picktime` DATETIME DEFAULT NULL,
  `flag_used` TINYINT(4) DEFAULT '1',
  `price` int(11) DEFAULT NULL,
  `site_chamber_gr` VARCHAR(4) DEFAULT NULL,
  `status` TINYINT(4) DEFAULT '1',
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `field_sync` tinyint(4) DEFAULT '0',
  `last_sync` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE tr_prepack_bundling_header(
outbound_delivery varchar(15) NOT NULL,
site_id varchar(4) NULL,
conversion_value int(11) NULL,
combine_qty int NULL,
selisih_conversion int NULL,
user_id int(11) NULL,
status tinyint NULL DEFAULT 1,
status_message varchar(50) NULL DEFAULT NULL
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL
) COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE tr_prepack_bundling_detail(
outbound_delivery varchar(15) NOT NULL,
article varchar(100) NULL,
line_id int NULL,
qty_dashboard int NULL,
reference varchar(20) NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL
) COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE tr_article_logistic_site(
article_logistic_site_id int NOT NULL AUTO_INCREMENT,
site_id varchar(4) NOT NULL,
outbound_delivery varchar(15) NOT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(article_logistic_site_id,site_id)
) COLLATE='utf8_general_ci' 
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE tr_article_logistic_site_detail(
article_logistic_site_detail_id int NOT NULL AUTO_INCREMENT,
outbound_delivery varchar(15) NOT NULL,
article varchar(100) NOT NULL,
customer_article varchar(100) NOT NULL,
description varchar(200) NULL DEFAULT NULL,
qty_issue int NULL DEFAULT NULL,
conv_uom varchar(10) NULL DEFAULT NULL,
qty_receive_actual int NULL DEFAULT NULL,
qty_receive int NULL DEFAULT NULL,
disc_minus int NULL DEFAULT NULL,
disc_plus int NULL DEFAULT NULL,
conversion_diff int NULL DEFAULT NULL COMMENT 'selisih conversion',
dashboard_received_date datetime NULL DEFAULT NULL,
qty_chamber varchar(100) NULL DEFAULT NULL,
chamber_disc_minus int NULL DEFAULT NULL,
chamber_disc_plus int NULL DEFAULT NULL,
chamber_sync_flag tinyint NULL DEFAULT 0,
field_sync tinyint NULL DEFAULT 0,


CREATE TABLE tr_movement_article (
movement_article_id int	NOT NULL AUTO_INCREMENT,
receiving_site_id varchar(4) NULL,
article varchar(100) NULL,
description varchar(200) NULL,
qty int NULL,
movement_type varchar(10) NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(movement_article_id)
) COLLATE='utf8_general_ci' 
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE tr_movement_quota_level (
movement_quota_level_id int NOT NULL AUTO_INCREMENT,
user_id int	NOT NULL,
site_id varchar(4) NULL ,
transaction_id varchar(100) NULL,
transaction_code varchar(100) NULL,
qty int NULL,
`value` int NULL,
balance_qty int NULL,
balance_value int NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(movement_quota_level_id)
) COLLATE='utf8_general_ci' 
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE ms_movement_type (
movement_type_id INT(3)	NOT NULL,
movement_type_desc VARCHAR(20) NULL,  
chamber_sync_flag TINYINT(4) DEFAULT '0',
field_sync TINYINT(4) DEFAULT '0',
STATUS TINYINT NULL DEFAULT 1,
created_at DATETIME NULL DEFAULT NULL,
created_by VARCHAR(25) NULL DEFAULT NULL,
created_ip VARCHAR(25) NULL DEFAULT NULL,
updated_at DATETIME NULL DEFAULT NULL,
updated_by VARCHAR(25) NULL DEFAULT NULL,
updated_ip VARCHAR(25) NULL DEFAULT NULL,
PRIMARY KEY(movement_type_id)
) COLLATE='utf8_general_ci' 
ENGINE=INNODB

CREATE TABLE `tr_transaction_in` (
  transaction_id VARCHAR(20) NOT NULL,
  site_id VARCHAR(4) NOT NULL,
  outbound_delivery VARCHAR(10) NOT NULL,
  article VARCHAR(100) NOT NULL,
  rfid VARCHAR(200) NOT NULL,
  description VARCHAR(200) DEFAULT NULL,
  picktime DATETIME DEFAULT NULL,
  user_id int(11) NOT NULL,
  flag_used TINYINT(4) DEFAULT '1',
  movement_type	TINYINT(3) DEFAULT '101',
  site_chamber_gr VARCHAR(4) DEFAULT NULL,
  status_message VARCHAR(25) DEFAULT NULL,
  `status` TINYINT(4) DEFAULT '1',
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `field_sync` tinyint(4) DEFAULT '0',
  `last_sync` datetime DEFAULT NULL,
  created_at DATETIME DEFAULT NULL,
  created_by VARCHAR(25) DEFAULT NULL,
  created_ip VARCHAR(25) DEFAULT NULL,
  updated_at DATETIME DEFAULT NULL,
  updated_by VARCHAR(25) DEFAULT NULL,
  updated_ip VARCHAR(25) DEFAULT NULL,
  PRIMARY KEY (transaction_id)
) COLLATE='utf8_general_ci'  
ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE ms_emergency_log(
emergency_log_id BIGINT NOT NULL AUTO_INCREMENT,
site_id VARCHAR(4) NULL DEFAULT NULL,
emergency_button varchar(100) NULL DEFAULT NULL,
pressed varchar(100) NULL DEFAULT NULL,
status TINYINT NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(100) NULL DEFAULT NULL,
created_ip varchar(100) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(100) NULL DEFAULT NULL,
updated_ip varchar(100) NULL DEFAULT NULL,
) COLLATE='utf8_general_ci' 
ENGINE=INNODB;

CREATE TABLE ms_power_log(
power_log_id BIGINT NOT NULL AUTO_INCREMENT,
site_id VARCHAR(4) NULL DEFAULT NULL,
pin_ups VARCHAR(100) NULL DEFAULT NULL,
active VARCHAR NULL DEFAULT NULL,
status TINYINT NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(100) NULL DEFAULT NULL,
created_ip varchar(100) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(100) NULL DEFAULT NULL,
updated_ip varchar(100) NULL DEFAULT NULL,
) COLLATE='utf8_general_ci' 
ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS `ms_division` (
  `division_id` int(11) NOT NULL AUTO_INCREMENT,
  `division_name` varchar(100) DEFAULT NULL,
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `field_sync` tinyint(4) DEFAULT '0',
  `last_sync` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`division_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tr_transaction_cc` (
  `cc_id` VARCHAR(25) NOT NULL,
  `ref_no` VARCHAR(25) NOT NULL,
  `site_id` VARCHAR(4) NOT NULL,
  `rfid` VARCHAR(200) NOT NULL,
  `article` VARCHAR(100) NOT NULL,
  `description` VARCHAR(100) NOT NULL,
  `movement_type` INT(3) DEFAULT NULL,
  `stock_qty` INT(11) DEFAULT NULL,
  `stock_cc` INT(11) DEFAULT NULL,
  `status_message` VARCHAR(25) DEFAULT NULL,
  `status` TINYINT(4) DEFAULT '1',
  `dashboard_sync_flag` TINYINT(4) DEFAULT '0',
  `last_sync` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `created_by` VARCHAR(25) DEFAULT NULL,
  `created_ip` VARCHAR(25) DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  `updated_by` VARCHAR(25) DEFAULT NULL,
  `updated_ip` VARCHAR(25) DEFAULT NULL,
  PRIMARY KEY (`cc_id`,`ref_no`,'site_id','rfid','article')
) ENGINE=INNODB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;


/*!40000 ALTER TABLE `tr_transaction` DISABLE KEYS */;
/*!40000 ALTER TABLE `tr_transaction` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
