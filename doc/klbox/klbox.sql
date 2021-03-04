CREATE DATABASE klbox;
USE klbox;

-- ALL MASTER 
---------------------------------------------------------
-- DONE - 15jan2019
CREATE TABLE ms_company(
company_id INT NOT NULL AUTO_INCREMENT,
company_name varchar(150) NULL DEFAULT NULL,
company_address varchar(255) NULL DEFAULT NULL,
company_phone varchar(50) NULL DEFAULT NULL,
company_pic varchar(150) NULL DEFAULT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(company_id)
) COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

---------------------------------------------------------



---------------------------------------------------------
-- DONE - 15jan2019
CREATE TABLE ms_user(
user_id int NOT NULL AUTO_INCREMENT,
user_code varchar(100) NULL DEFAULT NULL,
site_id int NULL,
parent_user_id int NULL,
level_id int NULL,
firstname varchar(100) NULL DEFAULT NULL,
lastname varchar(100) NULL DEFAULT NULL,

quota_initial 
quota_additional
quota_remaining
job_title varchar(120) NULL DEFAULT NULL,
division varchar(120) NULL DEFAULT NULL,
email varchar(120) NULL DEFAULT NULL,
user_category varchar(100) NULL DEFAULT NULL,
password varchar(120) NULL DEFAULT NULL,
counter_wrong_pass tinyint NULL DEFAULT 0,
status_lock tinyint NULL DEFAULT 0,
locked_time varchar(120) NULL DEFAULT NULL,
reset_by varchar(100) NULL DEFAULT NULL,
reset_time varchar(120) NULL DEFAULT NULL,

status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(user_id)
) COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;
---------------------------------------------------------
-- DONE - 15jan2019

CREATE TABLE ms_user_attribute(
user_attribute_id int NOT NULL AUTO_INCREMENT,
user_id int NOT NULL DEFAULT NULL,
value varchar(255) NULL DEFAULT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(user_attribute_id)
) COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

---------------------------------------------------------
-- DONE - 15jan2019

CREATE TABLE ms_user_company_site(
user_company_site_id int NOT NULL AUTO_INCREMENT,
user_id int NOT NULL DEFAULT NULL,
company_id int NOT NULL DEFAULT NULL,
site_id int NOT NULL DEFAULT NULL,
value varchar(255) NULL DEFAULT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(user_attribute_id)
) COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

---------------------------------------------------------
-- DONE - 15jan2019

CREATE TABLE ms_article(
article_id int NOT NULL AUTO_INCREMENT,
site_id int NOT NULL AUTO_INCREMENT,
title varchar(100) NULL DEFAULT NULL,
customer_article varchar(100) NULL DEFAULT NULL COMMENT '',
description text NULL DEFAULT NULL,
uom varchar(100) NULL DEFAULT NULL,
conversion_value varchar(100) NULL DEFAULT NULL,
safety_stock varchar(100) NULL DEFAULT NULL,
column varchar(100) NULL DEFAULT NULL,
rack varchar(100) NULL DEFAULT NULL,
row varchar(100) NULL DEFAULT NULL,
price varchar(100) NULL DEFAULT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(article_id)
) COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

---------------------------------------------------------
-- DONE - 15jan2019

CREATE TABLE ms_article_attribute(
article_attribute_id int NOT NULL AUTO_INCREMENT,
attribute_name varchar(100) NULL DEFAULT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(article_id)
) COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

---------------------------------------------------------
-- DONE - 15jan2019

CREATE TABLE ms_article_attribute_value(
article_attribute_value_id int NOT NULL AUTO_INCREMENT,
article_id int NOT NULL AUTO_INCREMENT,
article_attribute_id int NOT NULL AUTO_INCREMENT,
value varchar(150) NULL DEFAULT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(article_id)
) COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;


-- DONE - 15jan2019
CREATE TABLE ms_role(
role_id int NOT NULL AUTO_INCREMENT,
name varchar(100) NULL DEFAULT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(role_id)
) COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;


-- DONE - 15jan2019
CREATE TABLE ms_capability(
capability_id int NOT NULL AUTO_INCREMENT,
description varchar(200) NULL DEFAULT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(capability_id)
) COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

-- DONE - 15jan2019
CREATE TABLE ms_role_capability(
role_capability_id int NOT NULL AUTO_INCREMENT,
role_id int NOT NULL,
capability_id int NOT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(capability_id)
) COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;


-- DONE - 15jan2019
CREATE TABLE ms_role_capability(
role_capability_id int NOT NULL AUTO_INCREMENT,
name varchar(100) NULL DEFAULT NULL,
slug varchar(100) NULL DEFAULT NULL,
description text NULL DEFAULT NULL,
notes text NULL DEFAULT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(role_capability_id)
) COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;


-- DONE - 15jan2019
CREATE TABLE ms_user_role_level(
user_role_level_id int NOT NULL AUTO_INCREMENT,
user_id int NOT NULL,
role_id int NOT NULL,
level_id int NOT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(user_role_level_id)
) COLLATE='utf8mb4_general_ci' 
ENGINE=InnoDB
AUTO_INCREMENT=1;

-- DONE - 15jan2019
CREATE TABLE ms_site(
site_id int NOT NULL AUTO_INCREMENT,
company_id int NOT NULL,
name varchar(100) NULL DEFAULT NULL,
address varchar(100) NULL DEFAULT NULL,
qty_value varchar(100) NULL DEFAULT NULL,
is_qty tinyint NULL DEFAULT 0 COMMENT 'if qty then 1 else 0',
method_calc varchar(100) NULL DEFAULT NULL,
start_date_count tinyint NULL DEFAULT NULL,
reset_days tinyint NULL DEFAULT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(site_id)
) COLLATE='utf8mb4_general_ci' 
ENGINE=InnoDB
AUTO_INCREMENT=1;

-- DONE - 15jan2019
CREATE TABLE ms_site_logo(
site_logo_id int NOT NULL AUTO_INCREMENT,
site_id int NOT NULL,
filename varchar(180) NULL DEFAULT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(site_logo_id)
) COLLATE='utf8mb4_general_ci' 
ENGINE=InnoDB
AUTO_INCREMENT=1;

-- DONE - 15jan2019
CREATE TABLE ms_site_pic(
site_pic_id int NOT NULL AUTO_INCREMENT,
site_id int NOT NULL,
name varchar(100) NULL DEFAULT NULL,
phone varchar(100) NULL DEFAULT NULL,
email varchar(100) NULL DEFAULT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(site_pic_id)
) COLLATE='utf8mb4_general_ci' 
ENGINE=InnoDB
AUTO_INCREMENT=1;

-- DONE - 15jan2019
-- SKIP BINGUNG
-- CREATE TABLE ms_reason(
-- reason_id int NOT NULL AUTO_INCREMENT,
-- value varchar(100) NULL DEFAULT NULL,
-- status tinyint NULL DEFAULT 1,
-- created_at datetime NULL DEFAULT NULL,
-- created_by varchar(25) NULL DEFAULT NULL,
-- created_ip varchar(25) NULL DEFAULT NULL,
-- updated_at datetime NULL DEFAULT NULL,
-- updated_by varchar(25) NULL DEFAULT NULL,
-- updated_ip varchar(25) NULL DEFAULT NULL,
-- PRIMARY KEY(article_id)
-- ) COLLATE='utf8mb4_general_ci' 
-- ENGINE=InnoDB
-- AUTO_INCREMENT=1;


-- DONE - 15jan2019
CREATE TABLE ms_level(
level_id int NOT NULL AUTO_INCREMENT,
running_no mediumint NULL DEFAULT 0,
parent_running_no mediumint NULL DEFAULT 0,
name varchar(100) NULL DEFAULT NULL,
flag_approval varchar(100) NULL DEFAULT NULL,
level_qty_val_init varchar(100) NULL DEFAULT NULL,
level_qty_val_calculation varchar(100) NULL DEFAULT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(level_id)
) COLLATE='utf8mb4_general_ci' 
ENGINE=InnoDB
AUTO_INCREMENT=1;

-- DONE - 15jan2019
CREATE TABLE ms_site_config(
site_config_id int NOT NULL AUTO_INCREMENT,
site_id int NOT NULL,
name varchar(100) NULL DEFAULT NULL,
value varchar(100) NULL DEFAULT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(site_config_id)
) COLLATE='utf8mb4_general_ci' 
ENGINE=InnoDB
AUTO_INCREMENT=1;

-- DONE - 15jan2019
CREATE TABLE tr_article_logistic_site(
article_logistic_site_id int NOT NULL AUTO_INCREMENT,
name varchar(100) NULL DEFAULT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(article_id)
) COLLATE='utf8mb4_general_ci' 
ENGINE=InnoDB
AUTO_INCREMENT=1;

-- DONE - 15jan2019
CREATE TABLE tr_article_logistic_site_detail(
article_logistic_site_detail_id int NOT NULL AUTO_INCREMENT,
article_logistic_site_id int NOT NULL,
article_id int NOT NULL,
customer_article int NOT NULL,
description varchar(100) NULL DEFAULT NULL,
od_no varchar(100) NULL DEFAULT NULL,
qty_issue varchar(100) NULL DEFAULT NULL,
convert_uom varchar(100) NULL DEFAULT NULL,
qty_actual_received varchar(100) NULL DEFAULT NULL,
qty_receive varchar(100) NULL DEFAULT NULL,
disc_minus varchar(100) NULL DEFAULT NULL,
disc_plus varchar(100) NULL DEFAULT NULL,
conversion_diff varchar(100) NULL DEFAULT NULL COMMENT 'selisih conversion',
qty_chamber varchar(100) NULL DEFAULT NULL,
qty_chamber_disc_minus varchar(100) NULL DEFAULT NULL,
qty_chamber_disc_plus varchar(100) NULL DEFAULT NULL,
chamber_sync_date datetime NULL DEFAULT NULL,

status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(article_logistic_site_detail_id,article_logistic_site_id)
) COLLATE='utf8mb4_general_ci' 
ENGINE=InnoDB
AUTO_INCREMENT=1;


----------------------------------------------------------------------------
-- DONE - 15jan2019
CREATE TABLE ms_(
_id int NOT NULL AUTO_INCREMENT,
name varchar(100) NULL DEFAULT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(article_id)
) COLLATE='utf8mb4_general_ci' 
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE ms_(
_id int NOT NULL AUTO_INCREMENT,
name varchar(100) NULL DEFAULT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(article_id)
) COLLATE='utf8mb4_general_ci' 
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE tr_log (
log_id bigint not null auto_increment,
name varchar(100) NULL,
url varchar(200) NULL,
data text NULL,
json text NULL,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
primary key(log_id)
)COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

INSERT INTO `klbox`.`ms_article` (`name`, `slug`, `description`, `notes`, `created_at`, `created_by`) VALUES ('lorem ipsum', 'lorem-ipsum-dolor', 'Ini adalah artikel pertama untuk produk klbox', 'yaya', '2018-12-31 18:14:39', '1');
INSERT INTO `klbox`.`ms_article` (`name`, `slug`, `description`, `created_at`) VALUES ('Bebas mantap jiwa', 'bebas-mantap', 'Masih deskripsi dan lembur', '2018-12-31 18:15:05');


CREATE TABLE `ms_token` (
	`token_id` INT(11) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(100) NULL DEFAULT NULL,
	`token` VARCHAR(100) NULL DEFAULT NULL,
	`expired_at` DATETIME NULL DEFAULT NULL,
    status tinyint NULL DEFAULT 1,
    created_at datetime NULL DEFAULT NULL,
    created_by varchar(25) NULL DEFAULT NULL,
    created_ip varchar(25) NULL DEFAULT NULL,
    updated_at datetime NULL DEFAULT NULL,
    updated_by varchar(25) NULL DEFAULT NULL,
    updated_ip varchar(25) NULL DEFAULT NULL,
	PRIMARY KEY (`token_id`)
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;
INSERT INTO `klbox`.`ms_token` (`name`, `token`) VALUES ('key', 'macbook');

