CREATE TABLE ms_company(
company_id INT NOT NULL AUTO_INCREMENT,
company_name varchar(150) NULL DEFAULT NULL,
company_address varchar(255) NULL DEFAULT NULL,
company_phone int(50) NULL DEFAULT NULL,
company_pic varchar(150) NULL DEFAULT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(company_id)
) COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE tr_transaction (
transaction_id varchar(100)	NOT NULL,
site_id	varchar(4) NOT NULL,
user_id	varchar(4) NOT NULL,
article	varchar(100) NULL DEFAULT NULL,
customer_article varchar(100) NULL DEFAULT NULL,
description	varchar(200) NULL DEFAULT NULL,
qty	int NULL DEFAULT NULL,
`value` int NULL DEFAULT NULL,
status_in_out varchar(10) NULL DEFAULT NULL COMMENT 'in or out',
reason_id int NULL DEFAULT NULL,
wo_wbs varchar(100) NULL DEFAULT NULL,
remark varchar(200) NULL DEFAULT NULL,
chamber_sync_flag tinyint NULL DEFAULT 0,
field_sync tinyint NULL DEFAULT 0,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(transaction_id)
) COLLATE='utf8_general_ci' 
ENGINE=InnoDB
AUTO_INCREMENT=1;

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

CREATE TABLE ms_reason_type(
reason_type_id int NOT NULL AUTO_INCREMENT,
attribute_id INT NOT NULL,
attribute_value varchar(100) NULL DEFAULT NULL,
site_id varchar(4) NULL DEFAULT NULL,
chamber_sync_flag tinyint NULL DEFAULT 0,
field_sync tinyint NULL DEFAULT 0,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(reason_type_id)
) COLLATE='utf8_general_ci' 
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE ms_reason_type_mapping(
reason_type_mapping_id int NOT NULL AUTO_INCREMENT,
reason_type_id int NOT NULL,
reason_id INT NOT NULL,
chamber_sync_flag tinyint NULL DEFAULT 0,
field_sync tinyint NULL DEFAULT 0,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(reason_type_mapping_id)
) COLLATE='utf8_general_ci' 
ENGINE=InnoDB
AUTO_INCREMENT=1;

--------

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
outbound_delivery int NOT NULL,
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

status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(article_logistic_site_detail_id,outbound_delivery,article)
) COLLATE='utf8_general_ci' 
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE ms_article_stock(
site_id varchar(4) NOT NULL,
article int(10) NOT NULL,
customer_article varchar(100) NULL DEFAULT NULL,
description varchar(200) NULL DEFAULT NULL,
stock_qty int NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(site_id,article)
) COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE ms_article(
article_id int NOT NULL AUTO_INCREMENT,
site_id varchar(4) NOT NULL,
article varchar(100) NULL DEFAULT NULL,
customer_article varchar(100) NULL DEFAULT NULL COMMENT '',
description varchar(200) NULL DEFAULT NULL,
uom varchar(100) NULL DEFAULT NULL,
conversion_value int NULL DEFAULT NULL,
safety_stock int NULL DEFAULT NULL,
`column` varchar(100) NULL DEFAULT NULL,
rack varchar(100) NULL DEFAULT NULL,
`row` varchar(100) NULL DEFAULT NULL,
price int NULL DEFAULT NULL,
chamber_sync_flag tinyint NULL DEFAULT 0,
field_sync tinyint NULL DEFAULT 0,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(article_id)
) COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;


CREATE TABLE ms_article_attribute(
article_attribute_id int NOT NULL AUTO_INCREMENT,
attribute_name varchar(100) NOT NULL ,
chamber_sync_flag tinyint NULL DEFAULT 0,
field_sync tinyint NULL DEFAULT 0,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(article_attribute_id)
) COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE ms_article_attribute_value(
article_attribute_value_id int NOT NULL AUTO_INCREMENT,
article_id int NOT NULL,
article_attribute_id int NOT NULL ,
attribute_value varchar(100) NULL DEFAULT NULL,
chamber_sync_flag tinyint NULL DEFAULT 0,
field_sync tinyint NULL DEFAULT 0,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(article_attribute_value_id)
) COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE ms_role(
role_id int NOT NULL AUTO_INCREMENT,
role_name varchar(100) NULL,
chamber_sync_flag tinyint NULL DEFAULT 0,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(role_id)
) COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE ms_capability(
capability_id int NOT NULL AUTO_INCREMENT,
capability varchar(100) NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(capability_id)
) COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;


CREATE TABLE ms_role_capability(
role_capability_id int NOT NULL AUTO_INCREMENT,
role_id varchar(100) NULL,
capability_id int NOT NULL,
`create` tinyint NULL DEFAULT 0,
`read` tinyint NULL DEFAULT 0,
`update` tinyint NULL DEFAULT 0,
`delete` tinyint NULL DEFAULT 0,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(role_capability_id)
) COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE ms_user_role(
user_role_id int NOT NULL AUTO_INCREMENT,
role_id varchar(100) NULL,
user_id int NOT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(user_role_id)
) COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;


CREATE TABLE ms_config(
config_id int NOT NULL AUTO_INCREMENT,
site_id varchar(4) NOT NULL,
config_name varchar(100) NULL,
config_value tinyint NULL,
chamber_sync_flag tinyint NULL DEFAULT 0,
field_sync tinyint NULL DEFAULT 0,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(config_id)
) COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE ms_rfid_article(
rfid_article_id int NOT NULL AUTO_INCREMENT,
site_id varchar(4) NOT NULL,
outbound_delivery int(10) NOT NULL,
article varchar(100) NOT NULL,
description varchar(200) NOT NULL,
rfid varchar(200) NULL,
picktime datetime,
user_id int NULL,
sync_date datetime NULL,
chamber_sync_flag tinyint NULL DEFAULT 0,
field_sync tinyint NULL DEFAULT 0,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(rfid_article_id)
) COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;


CREATE TABLE ms_article_po(
article_po_id int NOT NULL AUTO_INCREMENT,
site_id varchar(4) NOT NULL,
article varchar(100) NULL,
customer_article varchar(100) NULL,
description varchar(200) NULL,
po_blanket_number varchar(200) NULL,
po_blanket_qty int(10) NULL,
po_created_date datetime NULL,
chamber_sync_flag tinyint NULL DEFAULT 0,
field_sync tinyint NULL DEFAULT 0,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(article_po_id)
) COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE tr_article_po_history(
article_po_history_id int NOT NULL AUTO_INCREMENT,
article_po_id int NOT NULL,
po_usage_qty int NULL,
po_remaining_qty int NULL,
po_created_date datetime NULL,
status_in_out varchar(4)  NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(article_po_history_id)
) COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE tr_prepack_bundling_header(
prepack_id int NOT NULL AUTO_INCREMENT,
outbound_delivery int NOT NULL,
site_created_on varchar(4) NULL,
status_prepack varchar(100) NULL,
conv_uom varchar(100) NULL,
combine_qty int NULL,
user_id int NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(prepack_id)
) COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE tr_prepack_bundling_detail(
prepack_bundling_detail int NOT NULL AUTO_INCREMENT,
prepack_id int NOT NULL,
outbound_delivery int NOT NULL,
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
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(prepack_bundling_detail)
) COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE ms_user(
user_id int NOT NULL AUTO_INCREMENT,
site_id varchar(10) NULL,
parent_user_id int NULL,
level_id int NULL,
user_code varchar(100) NULL DEFAULT NULL,
firstname varchar(100) NULL DEFAULT NULL,
lastname varchar(100) NULL DEFAULT NULL,
quota_initial int NULL DEFAULT NULL,
quota_additional int NULL DEFAULT NULL,
quota_remaining int NULL DEFAULT NULL,
job_title varchar(100) NULL DEFAULT NULL,
division varchar(100) NULL DEFAULT NULL,
email varchar(100) NULL DEFAULT NULL,
user_category varchar(100) NULL DEFAULT NULL,
password varchar(100) NULL DEFAULT NULL,
counter_wrong_pass tinyint NULL DEFAULT 0,
status_lock tinyint NULL DEFAULT 0,
locked_time datetime NULL DEFAULT NULL,
reset_by varchar(100) NULL DEFAULT NULL,
reset_time datetime NULL DEFAULT NULL,

status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(user_id)
) COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;


CREATE TABLE ms_user_attribute(
user_attribute_id int NOT NULL AUTO_INCREMENT,
user_id int NOT NULL,
attribute varchar(255) NULL DEFAULT NULL,
value varchar(255) NULL DEFAULT NULL,
chamber_sync_flag tinyint NULL DEFAULT 0,
field_sync tinyint NULL DEFAULT 0,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(user_attribute_id)
) COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE ms_site(
site_id varchar(10) NOT NULL,
company_id int NOT NULL,
site_name varchar(100) NULL DEFAULT NULL,
site_address varchar(200) NULL DEFAULT NULL,
site_qty_value int NULL DEFAULT NULL,
flag_qty_value varchar(100) NULL DEFAULT NULL,
method_calc varchar(25) NULL DEFAULT NULL,
start_date_counting datetime NULL DEFAULT NULL,
reset_days int NULL DEFAULT NULL,
logo_file_name text NULL DEFAULT NULL,
chamber_sync_flag tinyint NULL DEFAULT 0,
field_sync tinyint NULL DEFAULT 0,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(site_id)
) COLLATE='utf8_general_ci' 
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE ms_pic(
pic_id int NOT NULL AUTO_INCREMENT,
site_id varchar(4) NOT NULL,
pic_name varchar(100) NULL DEFAULT NULL,
pic_phone varchar(100) NULL DEFAULT NULL,
pic_email varchar(100) NULL DEFAULT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(pic_id)
) COLLATE='utf8_general_ci' 
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE ms_reason(
reason_id int NOT NULL AUTO_INCREMENT,
reason_value varchar(100) NULL DEFAULT NULL,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(reason_id)
) COLLATE='utf8_general_ci' 
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE ms_level(
level_id int NOT NULL AUTO_INCREMENT,
level_hierarchy int NULL DEFAULT 1,
level_name varchar(100) NULL DEFAULT NULL,
chamber_sync_flag tinyint NULL DEFAULT 0,
field_sync tinyint NULL DEFAULT 0,
status tinyint NULL DEFAULT 1,
created_at datetime NULL DEFAULT NULL,
created_by varchar(25) NULL DEFAULT NULL,
created_ip varchar(25) NULL DEFAULT NULL,
updated_at datetime NULL DEFAULT NULL,
updated_by varchar(25) NULL DEFAULT NULL,
updated_ip varchar(25) NULL DEFAULT NULL,
PRIMARY KEY(level_id)
) COLLATE='utf8_general_ci' 
ENGINE=InnoDB
AUTO_INCREMENT=1;

-- thursday 21 feb 10:54
ALTER TABLE `ms_role_capability`
	ADD UNIQUE INDEX `role_id_capability_id` (`role_id`, `capability_id`);
ALTER TABLE `ms_capability`
	ADD UNIQUE INDEX `capability` (`capability`);
ALTER TABLE `ms_pic`
	CHANGE COLUMN `site_id` `site_id` VARCHAR(4) NOT NULL AFTER `pic_id`;

-- friday 22 feb 9:45
ALTER TABLE `ms_article_attribute`
	CHANGE COLUMN `attribute_name` `attribute_name` VARCHAR(100) NOT NULL AFTER `article_attribute_id`;

ALTER TABLE `ms_pic`
	CHANGE COLUMN `site_id` `site_id` VARCHAR(4) NOT NULL AFTER `pic_id`;

ALTER TABLE `ms_article_attribute_value`
	CHANGE COLUMN `value` `attribute_value` VARCHAR(100) NULL DEFAULT NULL AFTER `article_attribute_id`;

--------
- NOTES BUDI
- tr_movement_article
created_on dihapus
add new PK movement_article_id

- tr_article_logistic_site
rename table from tr_article_logistic_site_header to tr_article_logistic_site
rename column outbound_delivery int to outbound_delivery varchar(15) due to value 0000022151 will be saved 22151 when integer
created_on not used same as created_at

- ms_user_role 
level_id ga bisa dipakai karena table bisa duplicate record


- 13/02/2019 ali
rename field attribute_id to article_attribute_id in table ms_reason_type 
rename field value to attribute_value in table ms_article_attribute_value
