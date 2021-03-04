CREATE DATABASE chamber_sz24 ;
USE chamber_sz24;

CREATE TABLE IF NOT EXISTS `ms_article` (
  `article_id` BIGINT(20) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`article_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

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

CREATE TABLE IF NOT EXISTS `ms_article_stock` (
  `article_stock_id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` BIGINT(20) DEFAULT NULL,
  `site_id` varchar(4) NOT NULL,
  `article` varchar(100) NOT NULL,
  `customer_article` varchar(100) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `stock_qty` int(11) DEFAULT NULL,
  `stock_cc` int(11) DEFAULT NULL,
  `stock_damaged` int(11) DEFAULT NULL,
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
  PRIMARY KEY (`article_stock_id`),
  UNIQUE KEY `site_id_article` (`site_id`,`article`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

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

CREATE TABLE `ms_level` (
	`level_id` INT(11) NOT NULL AUTO_INCREMENT,
	`level_hierarchy` INT(11) NULL DEFAULT '1',
	`level_name` VARCHAR(100) NULL DEFAULT NULL,
	`chamber_sync_flag` TINYINT(4) NULL DEFAULT '0',
	`field_sync` TINYINT(4) NULL DEFAULT '0',
	`status` TINYINT(4) NULL DEFAULT '1',
	`created_at` DATETIME NULL DEFAULT NULL,
	`created_by` VARCHAR(25) NULL DEFAULT NULL,
	`created_ip` VARCHAR(25) NULL DEFAULT NULL,
	`updated_at` DATETIME NULL DEFAULT NULL,
	`updated_by` VARCHAR(25) NULL DEFAULT NULL,
	`updated_ip` VARCHAR(25) NULL DEFAULT NULL,
	PRIMARY KEY (`level_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=3
;

CREATE TABLE ms_emergency_log(
emergency_log_id BIGINT(11) NOT NULL AUTO_INCREMENT,
site_id VARCHAR(4) NULL DEFAULT NULL,
emergency_button VARCHAR(100) NULL DEFAULT NULL,
pressed VARCHAR(100) NULL DEFAULT NULL,
  `status` TINYINT(4) DEFAULT '1',
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `field_sync` tinyint(4) DEFAULT '0',
  `last_sync` datetime DEFAULT NULL,
created_at DATETIME NULL DEFAULT NULL,
created_by VARCHAR(100) NULL DEFAULT NULL,
created_ip VARCHAR(100) NULL DEFAULT NULL,
updated_at DATETIME NULL DEFAULT NULL,
updated_by VARCHAR(100) NULL DEFAULT NULL,
updated_ip VARCHAR(100) NULL DEFAULT NULL,
PRIMARY KEY (`emergency_log_id`)
) COLLATE='utf8_general_ci' 
ENGINE=INNODB
AUTO_INCREMENT=3;



CREATE TABLE ms_power_log(
power_log_id BIGINT(11) NOT NULL AUTO_INCREMENT,
site_id VARCHAR(4) NULL DEFAULT NULL,
pin_ups VARCHAR(100) NULL DEFAULT NULL,
active VARCHAR(20) NULL DEFAULT NULL,
  `status` TINYINT(4) DEFAULT '1',
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `field_sync` tinyint(4) DEFAULT '0',
  `last_sync` datetime DEFAULT NULL,
created_at DATETIME NULL DEFAULT NULL,
created_by VARCHAR(100) NULL DEFAULT NULL,
created_ip VARCHAR(100) NULL DEFAULT NULL,
updated_at DATETIME NULL DEFAULT NULL,
updated_by VARCHAR(100) NULL DEFAULT NULL,
updated_ip VARCHAR(100) NULL DEFAULT NULL,
PRIMARY KEY (`power_log_id`)
) COLLATE='utf8_general_ci' 
ENGINE=INNODB;


CREATE TABLE IF NOT EXISTS `ms_reason` (
  `reason_id` int(11) NOT NULL AUTO_INCREMENT,
  `reason_value` varchar(100) DEFAULT NULL,
  `is_replenish` tinyint(4) DEFAULT '0',
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
  PRIMARY KEY (`reason_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `ms_reason_type` (
  `reason_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `attribute` varchar(100) NOT NULL,
  `attribute_value` varchar(100) NOT NULL,
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
  PRIMARY KEY (`reason_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ms_reason_type_mapping` (
  `reason_type_mapping_id` int(11) NOT NULL AUTO_INCREMENT,
  `reason_type_id` int(11) NOT NULL,
  `reason_id` int(11) NOT NULL,
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
  PRIMARY KEY (`reason_type_mapping_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

CREATE TABLE `tr_transaction_in` (
  transaction_id VARCHAR(25) DEFAULT '',
  site_id VARCHAR(4) NOT NULL,
  outbound_delivery VARCHAR(10) NOT NULL,
  article VARCHAR(100) NOT NULL,
  rfid VARCHAR(200) NOT NULL,
  description VARCHAR(200) DEFAULT NULL,
  picktime DATETIME DEFAULT NULL,
  user_id int(11) DEFAULT NULL,
  flag_used TINYINT(4) DEFAULT '1',
  movement_type	TINYINT(3) DEFAULT '101',
  site_chamber_gr VARCHAR(4) DEFAULT NULL,
  `status` TINYINT(4) DEFAULT '1',
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `field_sync` tinyint(4) DEFAULT '0',
  `last_sync` datetime DEFAULT NULL,
  status_message VARCHAR(25) DEFAULT NULL,
  conversion_value int(11) DEFAULT NULL,
  price int(11) DEFAULT NULL,
  qty int(11) DEFAULT NULL,
  created_at DATETIME DEFAULT NULL,
  created_by VARCHAR(25) DEFAULT NULL,
  created_ip VARCHAR(25) DEFAULT NULL,
  updated_at DATETIME DEFAULT NULL,
  updated_by VARCHAR(25) DEFAULT NULL,
  updated_ip VARCHAR(25) DEFAULT NULL,
  PRIMARY KEY (transaction_id, site_id, outbound_delivery, article, rfid)
) COLLATE='utf8_general_ci'  
ENGINE=INNODB DEFAULT CHARSET=utf8;


CREATE TABLE `tr_transaction_out` (
  transaction_id VARCHAR(25) NOT NULL,
  site_id VARCHAR(4) NOT NULL,
  outbound_delivery VARCHAR(10) NOT NULL,
  wo_wbs VARCHAR(100) NOT NULL,
  article VARCHAR(100) NOT NULL,
  rfid VARCHAR(200) NOT NULL,
  description VARCHAR(200) DEFAULT NULL,
  picktime DATETIME DEFAULT NULL,
  user_id int(11) NOT NULL,
  reason_id TINYINT(11) NOT NULL,  
  conversion_value int(11) DEFAULT NULL,
  price int(11) DEFAULT NULL,
  qty int(11) DEFAULT NULL,
  customer_article VARCHAR(25) DEFAULT NULL,
  flag_used TINYINT(4) DEFAULT '1',
  movement_type	TINYINT(3) DEFAULT '101',
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
  PRIMARY KEY (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `ms_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` varchar(10) DEFAULT NULL,
  `parent_user_id` int(11) DEFAULT NULL,
  `level_id` int(11) DEFAULT NULL,
  `division_id` INT(11) DEFAULT NULL,
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
  `reset_token` VARCHAR(50) DEFAULT NULL,
  `reset_token_expired` DATETIME DEFAULT NULL,  
  `status` tinyint(4) DEFAULT '1',
  `chamber_sync_flag` TINYINT(4) DEFAULT NULL,
  `field_sync` TEXT DEFAULT NULL,
  `last_sync` DATETIME DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

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
