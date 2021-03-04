-- Monday 25 Feb 2019

-- USE : for fase 3
-- CREATED	: selasa 14 agustus 2018 by Rusdi
-- DEV		: 
-- QA		: 
-- PROD 	: 25 sept 2018 by Rusdi


--Monday 25 Feb 2019 (Ali)

CREATE TABLE `tr_prepack_bundling_header` in klbox_rev3.sql
CREATE TABLE `tr_prepack_bundling_detail` in klbox_rev3.sql
CREATE TABLE `tr_article_logistic_site` in klbox_rev3.sql
CREATE TABLE `tr_article_logistic_site_detail` in klbox_rev3.sql

--Tuesday 26 Feb 2019 (Ali)
ALTER TABLE `tr_prepack_bundling_detail` CHANGE COLUMN `prepack_bundling_detail` `prepack_bundling_detail_id` INT(11) NOT NULL AUTO_INCREMENT FIRST;

--wednesday 27 Feb 2019 (Ali)
CREATE TABLE `tr_movement_article` in klbox_rev3.sql
CREATE TABLE `tr_movement_quota_level` in klbox_rev3.sql
CREATE TABLE `tr_article_po_history` in dev klbox

--Tuesday 12 Mar 2019(Ali)
ALTER TABLE `tr_article_logistic_site_detail` CHANGE COLUMN `outbound_delivery` `outbound_delivery` VARCHAR(15) NOT NULL AFTER `article_logistic_site_detail_id`;
ALTER TABLE `tr_prepack_bundling_detail` CHANGE COLUMN `outbound_delivery` `outbound_delivery` VARCHAR(15) NOT NULL AFTER `prepack_id`;
ALTER TABLE `tr_prepack_bundling_header` CHANGE COLUMN `outbound_delivery` `outbound_delivery` VARCHAR(15) NOT NULL AFTER `prepack_id`;

-- Wednesday 13 maret
ALTER TABLE 'ms_article_po' ADD `remain_po_qty` ;
ALTER TABLE 'ms_article_po' ADD `line_id`;

-- thursday 14 maret
ALTER TABLE `ms_user`
	ADD COLUMN `reset_token` varchar(50) DEFAULT NULL AFTER `reset_time`;
ALTER TABLE `ms_user`
	ADD COLUMN `reset_token_expired` DATETIME NULL DEFAULT NULL AFTER `reset_token`;


--monday 18 maret
create table ms_movement_type
create table tr_transaction_in

--tuesday 26 maret
ALTER TABLE 'ms_user' ADD `article_attribute_reason` and 'attribute_value' ;
ALTER TABLE 'ms_reason' ADD `is_replenish`;
ALTER TABLE 'ms_reason_type' ADD `attribute`;
ALTER TABLE `tr_article_logistic_site_detail`
	ADD COLUMN `status_message` VARCHAR(50) NULL DEFAULT NULL AFTER `status`;
ALTER TABLE `tr_article_logistic_site_detail`
	ADD COLUMN `transaction_id` VARCHAR(25) NULL DEFAULT NULL AFTER `description`;

-- monday 1 april
-- DEV  : done
-- QA 	: 
-- PROD :  
ALTER TABLE `ms_rfid_article` ADD COLUMN `status_message` VARCHAR(25) NULL AFTER `field_sync`;

ALTER TABLE `tr_article_logistic_site`
	ADD COLUMN `status_message` VARCHAR(50) NULL DEFAULT NULL AFTER `status`;

-- tuesday 9 april by rusdi
-- DEV  : done
-- QA 	: done
-- PROD :  
ALTER TABLE `ms_article`
	DROP COLUMN `article_id`;

ALTER TABLE `tr_transaction`
	ADD COLUMN `status_message` VARCHAR(50) NULL AFTER `status`,
	ADD COLUMN `is_job_done` TINYINT NULL DEFAULT '0' COMMENT 'Jobs update article stock, user quota, article movement, article po success or not' AFTER `status_message`;

ALTER TABLE `ms_article_stock`
	DROP COLUMN `article_stock_id`,
	DROP PRIMARY KEY,
	DROP INDEX `site_id_article`,
	ADD PRIMARY KEY (`site_id`, `article`);

ALTER TABLE `ms_article_po`
	ADD COLUMN `remaining_qty` INT(10) NULL DEFAULT NULL COMMENT 'qty * uom decrease every transaction occured' AFTER `po_blanket_qty`,
	ADD COLUMN `line_id` INT NULL DEFAULT NULL AFTER `po_created_date`;
ALTER TABLE `ms_article_po`
	ADD COLUMN `status_message` VARCHAR(50) NULL AFTER `status`;

ALTER TABLE `ms_article`
	ADD COLUMN `article_id` BIGINT NOT NULL AUTO_INCREMENT FIRST,
	DROP PRIMARY KEY,
	ADD PRIMARY KEY (`article_id`);
ALTER TABLE `tr_prepack_bundling_header`
	ADD COLUMN `status_message` VARCHAR(50) NULL DEFAULT NULL AFTER `status`;

ALTER TABLE `ms_user`
	ADD COLUMN `chamber_sync_flag` TINYINT(4) NULL DEFAULT NULL AFTER `status`,
	ADD COLUMN `field_sync` TEXT NULL DEFAULT NULL AFTER `chamber_sync_flag`,
	ADD COLUMN `last_sync` DATETIME NULL DEFAULT NULL AFTER `field_sync`;

---
ALTER TABLE `tr_transaction`
	DROP PRIMARY KEY;
ALTER TABLE `tr_transaction`
	ADD COLUMN `rfid` VARCHAR(20) NULL AFTER `user_id`;
ALTER TABLE `tr_transaction`
	CHANGE COLUMN `qty` `conversion_value` INT(11) NULL DEFAULT NULL AFTER `description`,
	CHANGE COLUMN `value` `price` INT(11) NULL DEFAULT NULL AFTER `conversion_value`;

--- 4/11/2019
ALTER TABLE `tr_prepack_bundling_header` DROP COLUMN `prepack_id`, DROP COLUMN `status_prepack`, CHANGE `site_created_on` `site_id` VARCHAR(4) CHARSET utf8 COLLATE utf8_general_ci NULL, CHANGE `conv_uom` `conversion_value` INT(11) NULL, ADD COLUMN `selisih_conversion` INT(11) NULL AFTER `combine_qty`, DROP PRIMARY KEY; 
ALTER TABLE `tr_prepack_bundling_detail` DROP COLUMN `prepack_bundling_detail_id`, DROP COLUMN `prepack_id`, DROP PRIMARY KEY; 
ALTER TABLE `tr_prepack_bundling_header`
	CHANGE COLUMN `selisih_conversion` `conversion_diff` INT(11) NULL DEFAULT NULL AFTER `combine_qty`;

ALTER TABLE `tr_transaction`
	CHANGE COLUMN `article` `article` VARCHAR(100) NOT NULL AFTER `rfid`,
	ADD PRIMARY KEY (`transaction_id`, `site_id`, `article`);


--- 4/22/2019
CREATE TABLE IF NOT EXISTS `ms_division` ( 
	`division_id` INT(11) NOT NULL AUTO_INCREMENT, 
	`division_name` VARCHAR(100) DEFAULT NULL, 
	`chamber_sync_flag` TINYINT(4) DEFAULT '0', 
	`field_sync` TINYINT(4) DEFAULT '0', 
	`status` TINYINT(4) DEFAULT '1', 
	`created_at` DATETIME DEFAULT NULL, 
	`created_by` VARCHAR(25) DEFAULT NULL, 
	`created_ip` VARCHAR(25) DEFAULT NULL, 
	`updated_at` DATETIME DEFAULT NULL, 
	`updated_by` VARCHAR(25) DEFAULT NULL, 
	`updated_ip` VARCHAR(25) DEFAULT NULL, 
	PRIMARY KEY (`division_id`) ) 
	ENGINE=INNODB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1; 

-- 4/23/2019
CREATE TABLE IF NOT EXISTS `ms_article_stock` ( `article_stock_id` INT(11) NOT NULL AUTO_INCREMENT, `article_id` INT(11) NOT NULL, `site_id` VARCHAR(4) NOT NULL, `article` VARCHAR(100) NOT NULL, `customer_article` VARCHAR(100) DEFAULT NULL, `description` VARCHAR(200) DEFAULT NULL, `stock_qty` INT(11) DEFAULT NULL, `status` TINYINT(4) DEFAULT '1', `created_at` DATETIME DEFAULT NULL, `created_by` VARCHAR(25) DEFAULT NULL, `created_ip` VARCHAR(25) DEFAULT NULL, `updated_at` DATETIME DEFAULT NULL, `updated_by` VARCHAR(25) DEFAULT NULL, `updated_ip` VARCHAR(25) DEFAULT NULL, PRIMARY KEY (`article_stock_id`), UNIQUE KEY `site_id_article` (`site_id`,`article`) ) ENGINE=INNODB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8; 
-- 22 april 2019
ALTER TABLE `tr_transaction`
	ADD COLUMN `conversion_value` INT NULL DEFAULT NULL AFTER `description`,
	CHANGE COLUMN `is_job_done` `is_job_order` TINYINT(4) NULL DEFAULT '0' COMMENT 'Jobs update article stock, user quota, article movement success or not' AFTER `status_message`,
	ADD COLUMN `is_job_artpo` TINYINT(4) NULL DEFAULT '0' COMMENT 'article po' AFTER `is_job_order`;
ALTER TABLE `tr_transaction`
	ADD COLUMN `movement_type` INT NOT NULL COMMENT 'movement_type_id' AFTER `user_id`;

ALTER TABLE `ms_user`
	CHANGE COLUMN `division` `division_id` INT NULL DEFAULT NULL AFTER `level_id`;
ALTER TABLE `ms_user`
	ADD COLUMN `attribute` VARCHAR(100) NULL DEFAULT NULL AFTER `job_title`,
	ADD COLUMN `attribute_value` VARCHAR(100) NULL DEFAULT NULL AFTER `attribute`;

ALTER TABLE `ms_article_stock`
	ADD COLUMN `stock_cc` INT(11) NULL DEFAULT NULL COMMENT 'stock cycle count' AFTER `stock_qty`,
	ADD COLUMN `stock_damaged` INT(11) NULL DEFAULT NULL COMMENT 'stock damaged goods' AFTER `stock_cc`;



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
ALTER TABLE `tr_transaction` CHANGE `chamber_sync_flag` `chamber_sync_flag` TINYINT(4) DEFAULT 0 NULL AFTER `is_job_artpo`, CHANGE `field_sync` `field_sync` TINYINT(4) DEFAULT 0 NULL AFTER `chamber_sync_flag`, ADD COLUMN `last_sync` DATETIME NULL AFTER `field_sync`; 


// 25-04-2019
ALTER TABLE `ms_rfid_article` DROP COLUMN `sync_date`, CHANGE `chamber_sync_flag` `chamber_sync_flag` TINYINT(4) DEFAULT 0 NULL AFTER `status`, CHANGE `field_sync` `field_sync` TINYINT(4) DEFAULT 0 NULL AFTER `chamber_sync_flag`, ADD COLUMN `last_sync` DATETIME NULL AFTER `field_sync`; 
ALTER TABLE `tr_transaction` ADD COLUMN `outbound_delivery` VARCHAR(10) NULL AFTER `remark`, ADD COLUMN `rfid` VARCHAR(200) NULL AFTER `outbound_delivery`, ADD COLUMN `picktime` DATETIME NULL AFTER `rfid`, ADD COLUMN `flag_used` TINYINT(4) NULL AFTER `picktime`, ADD COLUMN `price` INT(11) NULL AFTER `flag_used`, ADD COLUMN `site_chamber_gr` VARCHAR(4) NULL AFTER `price`; 

-- 7 mei 2019
ALTER TABLE ms_article_po ADD is_created_sc_order tinyint NULL DEFAULT 0 AFTER status_message;
ALTER TABLE ms_article_po ADD issue_qty tinyint NULL DEFAULT 0 AFTER is_created_sc_order;
ALTER TABLE ms_article_po ADD issue_date datetime NULL AFTER issue_qty;

CREATE TABLE tr_sc_order(
sc_order_id varchar(15) NOT NULL,
po_number varchar(10) NULL,
site_id varchar(4),
issue_date datetime,
is_csv_created tinyint NULL DEFAULT 0,
is_sent tinyint NULL DEFAULT 0,
remark varchar(255) NULL,
so_sap varchar(50) NULL COMMENT 'callback from sap',
log_staging text NULL,
log_sap text NULL,
`created_at` DATETIME NULL DEFAULT NULL,
`created_by` VARCHAR(25) NULL DEFAULT NULL,
`created_ip` VARCHAR(25) NULL DEFAULT NULL,
`updated_at` DATETIME NULL DEFAULT NULL,
`updated_by` VARCHAR(25) NULL DEFAULT NULL,
`updated_ip` VARCHAR(25) NULL DEFAULT NULL,
PRIMARY KEY(sc_order_id)
);

CREATE TABLE tr_sc_order_detail(
sc_order_id varchar(15) NOT NULL,
article varchar(100) NULL,
customer_article varchar(100) NULL,
issue_qty mediumint NULL DEFAULT 0,
price int NULL DEFAULT 0,
`created_at` DATETIME NULL DEFAULT NULL,
`created_by` VARCHAR(25) NULL DEFAULT NULL,
`created_ip` VARCHAR(25) NULL DEFAULT NULL,
`updated_at` DATETIME NULL DEFAULT NULL,
`updated_by` VARCHAR(25) NULL DEFAULT NULL,
`updated_ip` VARCHAR(25) NULL DEFAULT NULL,
PRIMARY KEY(sc_order_id),
UNIQUE(sc_order_id,article,price)
);

-- 08 mei 2019
ALTER TABLE `tr_sc_order_detail`
	CHANGE COLUMN `article` `article` VARCHAR(100) NOT NULL AFTER `sc_order_id`,
	CHANGE COLUMN `price` `price` INT(11) NOT NULL DEFAULT '0' AFTER `issue_qty`,
	DROP INDEX `sc_order_id`,
	DROP PRIMARY KEY,
	ADD PRIMARY KEY (`sc_order_id`, `article`, `price`);

-- thursday 23 mei 2019 - 15.04
-- alter for article_po &  by rusdi
-- DEV  : DONE
-- QA   : notyet
-- PROD : notyet
ALTER TABLE tr_article_logistic_site_detail ADD actual_receive_quantity_for_art_po mediumint NULL AFTER chamber_disc_plus;

ALTER TABLE `ms_company`
	ALTER `company_id` DROP DEFAULT;
ALTER TABLE `ms_company`
	CHANGE COLUMN `company_id` `company_id` VARCHAR(10) NOT NULL FIRST;
ALTER TABLE `ms_site`
	ALTER `company_id` DROP DEFAULT;
ALTER TABLE `ms_site`
	CHANGE COLUMN `company_id` `company_id` VARCHAR(10) NOT NULL AFTER `site_id`;

-- log changes for all master
CREATE TABLE tr_log_master(
log_master_id bigint NOT NULL AUTO_INCREMENT,
menu varchar(255) NULL,
action varchar(255) NULL,
url varchar(600) NULL,
remarks text NULL,
postdata text NULL,
is_notif tinyint NULL DEFAULT 0,
sent_count tinyint NULL DEFAULT 0,
`created_at` DATETIME NULL DEFAULT NULL,
`created_by` VARCHAR(25) NULL DEFAULT NULL,
`created_ip` VARCHAR(25) NULL DEFAULT NULL,
PRIMARY KEY(log_master_id)
)

-- friday 14 june add table cronjob mapping
CREATE TABLE ms_cronjob(
cronjob_id int auto_increment NOT NULL,
name varchar(200) NULL,
notes varchar(200) NULL,
url varchar(255) NULL,
url_method varchar(15) NULL COMMENT 'get / post',
url_data varchar(255) NULL COMMENT 'data hit in json',
url_timer varchar(50) NULL COMMENT 'Duration cron runs. ex: 5 * * * *',
status tinyint NULL DEFAULT 0,
`created_at` DATETIME NULL DEFAULT NULL,
`created_by` VARCHAR(25) NULL DEFAULT NULL,
`created_ip` VARCHAR(25) NULL DEFAULT NULL,
`updated_at` DATETIME NULL DEFAULT NULL,
`updated_by` VARCHAR(25) NULL DEFAULT NULL,
`updated_ip` VARCHAR(25) NULL DEFAULT NULL,
PRIMARY KEY(cronjob_id)
)