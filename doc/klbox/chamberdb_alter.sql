//monday 8 april 2019
ALTER TABLE `tr_transaction_in` CHANGE `user_id` `user_id` INT(11) NULL; 
ALTER TABLE `tr_transaction_in` CHANGE `status_message` `status_message` VARCHAR(25) CHARSET utf8 COLLATE utf8_general_ci NULL AFTER `STATUS`; 
ALTER TABLE `tr_transaction_in` ADD COLUMN `conversion_value` INT(11) NULL AFTER `status_message`, ADD COLUMN `price` INT(11) NULL AFTER `conversion_value`; 

//10 april 2019
ALTER TABLE `tr_transaction_out` ADD COLUMN `qty` INT(10) NULL FIRST, ADD COLUMN `customer_article` VARCHAR(100) NULL AFTER `qty`; 
ALTER TABLE `tr_transaction_out` CHANGE `transaction_id` `transaction_id` VARCHAR(30) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL FIRST, CHANGE `wo_wbs` `wo_wbs` VARCHAR(100) CHARSET utf8 COLLATE utf8_general_ci NULL AFTER `rfid`, CHANGE `qty` `qty` INT(10) NULL AFTER `wo_wbs`, CHANGE `customer_article` `customer_article` VARCHAR(100) CHARSET utf8 COLLATE utf8_general_ci NULL AFTER `qty`; 

//11 april 2019
ALTER TABLE `tr_transaction_in` CHANGE `transaction_id` `transaction_id` VARCHAR(25) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL; 
ALTER TABLE `tr_transaction_out` CHANGE `transaction_id` `transaction_id` VARCHAR(25) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL; 

// 24-04-2019
ALTER TABLE `ms_article` ADD COLUMN `last_sync` DATETIME NULL AFTER `field_sync`; 
ALTER TABLE `ms_article` CHANGE `chamber_sync_flag` `chamber_sync_flag` TINYINT(4) DEFAULT 0 NULL AFTER `status`, CHANGE `field_sync` `field_sync` TINYINT(4) DEFAULT 0 NULL AFTER `chamber_sync_flag`, CHANGE `last_sync` `last_sync` DATETIME NULL AFTER `field_sync`; 
ALTER TABLE `ms_article_stock` ADD COLUMN `chamber_sync_flag` TINYINT(4) NULL AFTER `status`, ADD COLUMN `field_sync` TINYINT(4) NULL AFTER `chamber_sync_flag`, ADD COLUMN `last_sync` DATETIME NULL AFTER `field_sync`; 
ALTER TABLE `ms_division` CHANGE `chamber_sync_flag` `chamber_sync_flag` TINYINT(4) DEFAULT 0 NULL AFTER `status`, CHANGE `field_sync` `field_sync` TINYINT(4) DEFAULT 0 NULL AFTER `chamber_sync_flag`, ADD COLUMN `last_sync` DATETIME NULL AFTER `field_sync`; 
ALTER TABLE `ms_reason` ADD COLUMN `chamber_sync_flag` TINYINT(4) NULL AFTER `status`, ADD COLUMN `field_sync` TINYINT(4) NULL AFTER `chamber_sync_flag`, ADD COLUMN `last_sync` DATETIME NULL AFTER `field_sync`; 
ALTER TABLE `ms_reason_type` CHANGE `chamber_sync_flag` `chamber_sync_flag` TINYINT(4) DEFAULT 0 NULL AFTER `status`, CHANGE `field_sync` `field_sync` TINYINT(4) DEFAULT 0 NULL AFTER `chamber_sync_flag`, ADD COLUMN `last_sync` DATETIME NULL AFTER `field_sync`; 
ALTER TABLE `ms_reason_type_mapping` CHANGE `chamber_sync_flag` `chamber_sync_flag` TINYINT(4) DEFAULT 0 NULL AFTER `status`, CHANGE `field_sync` `field_sync` TINYINT(4) DEFAULT 0 NULL AFTER `chamber_sync_flag`, ADD COLUMN `last_sync` DATETIME NULL AFTER `field_sync`; 
ALTER TABLE `ms_site` CHANGE `chamber_sync_flag` `chamber_sync_flag` TINYINT(4) DEFAULT 0 NULL AFTER `status`, CHANGE `field_sync` `field_sync` TINYINT(4) DEFAULT 0 NULL AFTER `chamber_sync_flag`, ADD COLUMN `last_sync` DATETIME NULL AFTER `field_sync`; 
ALTER TABLE `tr_transaction_in` CHANGE `status` `status` TINYINT(4) DEFAULT 1 NULL AFTER `qty`, CHANGE `chamber_sync_flag` `chamber_sync_flag` TINYINT(4) DEFAULT 0 NULL AFTER `status`, CHANGE `field_sync` `field_sync` TINYINT(4) DEFAULT 0 NULL AFTER `chamber_sync_flag`, ADD COLUMN `last_sync` DATETIME NULL AFTER `field_sync`; 
ALTER TABLE `tr_transaction_out` CHANGE `chamber_sync_flag` `chamber_sync_flag` TINYINT(4) DEFAULT 0 NULL AFTER `status`, CHANGE `field_sync` `field_sync` TINYINT(4) DEFAULT 0 NULL AFTER `chamber_sync_flag`, ADD COLUMN `last_sync` DATETIME NULL AFTER `field_sync`; 
//25-04-2019
ALTER TABLE `tr_transaction_in` DROP COLUMN `sync_date`; 
ALTER TABLE `tr_transaction_out` DROP COLUMN `sync_date`; 

//20-5-2019
ALTER TABLE `chamber_dev`.`ms_article` CHANGE `article_id` `article_id` BIGINT(20) NOT NULL AUTO_INCREMENT; 
ALTER TABLE `chamber_dev`.`ms_article_stock` ADD COLUMN `article_id` BIGINT(20) NULL AFTER `article_stock_id`, ADD COLUMN `stock_cc` INT(11) NULL AFTER `stock_qty`, ADD COLUMN `stock_damaged` INT(11) NULL AFTER `stock_cc`; 
ALTER TABLE `chamber_dev`.`ms_user` CHANGE `division` `division_id` INT(11) NULL AFTER `level_id`, ADD COLUMN `reset_token` VARCHAR(50) NULL AFTER `reset_time`, ADD COLUMN `reset_token_expired` DATETIME NULL AFTER `reset_token`, ADD COLUMN `chamber_sync_flag` TINYINT(4) NULL AFTER `status`, ADD COLUMN `field_sync` TEXT NULL AFTER `chamber_sync_flag`, ADD COLUMN `last_sync` DATETIME NULL AFTER `field_sync`; 