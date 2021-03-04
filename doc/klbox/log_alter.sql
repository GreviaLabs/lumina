
-----------------------------------------------------------
/*
DEV QA	: 	done rabu 16 oct 19 by rusdi
PROD 	: 	done rabu 16 oct 19 by harvei
*/
ALTER TABLE `ms_article`
	ADD COLUMN `price_mf` BIGINT(20) NULL DEFAULT '0' COMMENT 'harga khusus article MF (kepala 7) price*6' AFTER `price`;
	
	UPDATE ms_article a
SET a.price_mf = (6 * a.price)
WHERE a.price_mf = 0;
-------------------------------------------------------------


-- wo_wbs
ALTER TABLE `tr_transaction`
	CHANGE COLUMN `wo_wbs` `wo_wbs` VARCHAR(450) NULL DEFAULT NULL AFTER `reason_id`;

-- new column
ALTER TABLE `tr_article_logistic_site_detail`
	ADD COLUMN `balance_qty_for_po` INT(11) NULL DEFAULT NULL AFTER `conversion_diff`;

-- Update new column
UPDATE tr_article_logistic_site_detail
	SET balance_qty_for_po = qty_receive_actual
	WHERE balance_qty_for_po IS NULL AND od = 'init';