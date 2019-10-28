
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `credit_note` ADD `discount_without_tax` DECIMAL(16,6) DEFAULT 0.000000 AFTER `total_price_with_tax`, ADD `discount_with_tax` DECIMAL(16,6) DEFAULT 0.000000 AFTER `discount_without_tax`;
ALTER TABLE `credit_note_version` ADD `discount_without_tax` DECIMAL(16,6) DEFAULT 0.000000 AFTER `total_price_with_tax`, ADD `discount_with_tax` DECIMAL(16,6) DEFAULT 0.000000 AFTER `discount_without_tax`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
