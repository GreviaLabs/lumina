
DROP TABLE IF EXISTS `log_activity_chamber`;
CREATE TABLE `log_activity_chamber` (
  `log_id` bigint(30) NOT NULL AUTO_INCREMENT,
  `site_id` varchar(4) NOT NULL,
  `menu` varchar(20) DEFAULT NULL,
  `data` text,
  `remark` text,
  `chamber_sync_flag` tinyint(4) DEFAULT NULL,
  `dashboard_sync_flag` tinyint(4) DEFAULT NULL,
  `last_sync` datetime DEFAULT NULL,
  `sync_date` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`log_id`,`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `log_app_error`;
CREATE TABLE `log_app_error` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `log_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `app_name` varchar(100) DEFAULT NULL,
  `url` varchar(100) DEFAULT NULL,
  `method` varchar(10) DEFAULT NULL,
  `message` varchar(4000) NOT NULL,
  `sync_date` datetime DEFAULT NULL,
  `request` text,
  `response` varchar(4000) NOT NULL DEFAULT '',
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `log_user_quota`;
CREATE TABLE `log_user_quota` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `log_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` bigint(20) NOT NULL,
  `quota_initial` float NOT NULL,
  `quota_additional` float NOT NULL,
  `quota_remaining` float NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `ms_article`;
CREATE TABLE `ms_article` (
  `article_id` bigint(20) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`article_id`),
  UNIQUE KEY `UNIQUE` (`site_id`,`article`)
) ENGINE=InnoDB AUTO_INCREMENT=354 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ms_article_attribute`;
CREATE TABLE `ms_article_attribute` (
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


DROP TABLE IF EXISTS `ms_article_attribute_value`;
CREATE TABLE `ms_article_attribute_value` (
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ms_article_stock`;
CREATE TABLE `ms_article_stock` (
  `article_stock_id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` bigint(20) DEFAULT NULL,
  `site_id` varchar(4) NOT NULL,
  `article` varchar(100) NOT NULL,
  `customer_article` varchar(100) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `stock_qty` int(11) DEFAULT NULL,
  `stock_cc` int(11) DEFAULT NULL,
  `stock_damaged` int(11) DEFAULT NULL,
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
  `dashboard_sync_flag` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`article_stock_id`),
  UNIQUE KEY `site_id_article` (`site_id`,`article`)
) ENGINE=InnoDB AUTO_INCREMENT=345 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ms_capability`;
CREATE TABLE `ms_capability` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ms_config`;
CREATE TABLE `ms_config` (
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ms_division`;
CREATE TABLE `ms_division` (
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `ms_emergency_log`;
CREATE TABLE `ms_emergency_log` (
  `emergency_log_id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` varchar(4) DEFAULT NULL,
  `emergency_button` varchar(100) DEFAULT NULL,
  `pressed` varchar(100) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `dashboard_sync_flag` tinyint(4) DEFAULT NULL,
  `field_sync` tinyint(4) DEFAULT '0',
  `last_sync` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_ip` varchar(100) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(100) DEFAULT NULL,
  `updated_ip` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`emergency_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ms_level`;
CREATE TABLE `ms_level` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ms_power_log`;
CREATE TABLE `ms_power_log` (
  `power_log_id` bigint(11) NOT NULL AUTO_INCREMENT,
  `site_id` varchar(4) DEFAULT NULL,
  `pin_ups` varchar(100) DEFAULT NULL,
  `active` varchar(20) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `dashboard_sync_flag` tinyint(4) DEFAULT NULL,
  `field_sync` tinyint(4) DEFAULT '0',
  `last_sync` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_ip` varchar(100) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(100) DEFAULT NULL,
  `updated_ip` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`power_log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ms_reason`;
CREATE TABLE `ms_reason` (
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
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `ms_reason_type`;
CREATE TABLE `ms_reason_type` (
  `reason_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `site_id` varchar(4) NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ms_reason_type_mapping`;
CREATE TABLE `ms_reason_type_mapping` (
  `reason_type_mapping_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` varchar(4) DEFAULT NULL,
  `reason_type_id` int(11) NOT NULL,
  `reason_id` int(11) NOT NULL,
  `reason_value` varchar(100) DEFAULT NULL,
  `article` varchar(100) DEFAULT NULL,
  `attribute_id` int(11) DEFAULT NULL,
  `attribute` varchar(100) DEFAULT NULL,
  `attribute_value_id` int(11) DEFAULT NULL,
  `attribute_value` varchar(100) DEFAULT NULL,
  `is_replenish` tinyint(4) DEFAULT '1',
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
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ms_role`;
CREATE TABLE `ms_role` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ms_role_capability`;
CREATE TABLE `ms_role_capability` (
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
  PRIMARY KEY (`role_capability_id`),
  UNIQUE KEY `role_capability` (`role_id`,`capability_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ms_site`;
CREATE TABLE `ms_site` (
  `site_id` varchar(4) NOT NULL,
  `company_id` varchar(10) NOT NULL,
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
  `sync_date` datetime DEFAULT NULL,
  PRIMARY KEY (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `ms_user`;
CREATE TABLE `ms_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` varchar(4) DEFAULT NULL,
  `parent_user_id` int(11) DEFAULT NULL,
  `level_id` int(11) DEFAULT NULL,
  `division_id` int(11) DEFAULT NULL,
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
  `reset_token` varchar(50) DEFAULT NULL,
  `reset_token_expired` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `chamber_sync_flag` tinyint(4) DEFAULT NULL,
  `field_sync` text,
  `last_sync` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  `dashboard_sync_flag` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `ms_user_attribute`;
CREATE TABLE `ms_user_attribute` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `ms_user_attribute_value`;
CREATE TABLE `ms_user_attribute_value` (
  `user_attribute_value_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `attribute_id` int(11) DEFAULT NULL,
  `attribute_value` varchar(100) DEFAULT NULL,
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`user_attribute_value_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `ms_user_role`;
CREATE TABLE `ms_user_role` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tr_transaction_cc`;
CREATE TABLE `tr_transaction_cc` (
  `cc_id` varchar(25) NOT NULL,
  `ref_no` varchar(25) NOT NULL,
  `site_id` varchar(4) NOT NULL,
  `rfid` varchar(200) NOT NULL,
  `article` varchar(100) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `movement_type` int(3) DEFAULT NULL,
  `stock_qty` int(11) DEFAULT NULL,
  `stock_cc` int(11) DEFAULT NULL,
  `status_message` varchar(25) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `dashboard_sync_flag` tinyint(4) DEFAULT '0',
  `last_sync` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`cc_id`,`ref_no`,`site_id`,`rfid`,`article`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tr_transaction_in`;
CREATE TABLE `tr_transaction_in` (
  `transaction_id` varchar(25) NOT NULL DEFAULT '',
  `site_id` varchar(4) NOT NULL,
  `outbound_delivery` varchar(20) NOT NULL,
  `article` varchar(100) NOT NULL,
  `rfid` varchar(200) NOT NULL,
  `description` varchar(200) DEFAULT NULL,
  `picktime` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `conversion_value` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `ref_cc` varchar(25) DEFAULT '',
  `flag_used` tinyint(4) DEFAULT '1',
  `movement_type` int(11) DEFAULT '101',
  `site_chamber_gr` varchar(4) DEFAULT NULL,
  `field_sync` tinyint(4) DEFAULT '0',
  `last_sync` datetime DEFAULT NULL,
  `sync_date` datetime DEFAULT NULL,
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `dashboard_sync_flag` tinyint(4) DEFAULT NULL,
  `status_message` varchar(25) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`transaction_id`,`site_id`,`outbound_delivery`,`article`,`rfid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tr_transaction_out`;
CREATE TABLE `tr_transaction_out` (
  `transaction_id` varchar(25) NOT NULL DEFAULT '',
  `site_id` varchar(4) NOT NULL,
  `outbound_delivery` varchar(20) NOT NULL,
  `article` varchar(100) NOT NULL,
  `rfid` varchar(200) NOT NULL,
  `description` varchar(200) DEFAULT NULL,
  `picktime` datetime DEFAULT NULL,
  `wo_wbs` varchar(100) DEFAULT NULL,
  `reason_id` tinyint(11) DEFAULT NULL,
  `customer_article` varchar(25) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `conversion_value` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `ref_cc` varchar(25) DEFAULT '',
  `flag_used` tinyint(4) DEFAULT '1',
  `movement_type` int(11) DEFAULT '101',
  `field_sync` tinyint(4) DEFAULT '0',
  `last_sync` datetime DEFAULT NULL,
  `chamber_sync_flag` tinyint(4) DEFAULT '0',
  `dashboard_sync_flag` tinyint(4) DEFAULT NULL,
  `status_message` varchar(25) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `created_ip` varchar(25) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(25) DEFAULT NULL,
  `updated_ip` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`transaction_id`,`site_id`,`outbound_delivery`,`article`,`rfid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
