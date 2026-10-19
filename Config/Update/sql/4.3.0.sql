# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `credit_note` ADD `lang_id` INTEGER AFTER `allow_partial_use`;
ALTER TABLE `credit_note` ADD INDEX `idx_lang_id_fk` (`lang_id`);
ALTER TABLE `credit_note` ADD CONSTRAINT `credit_note_FK_8`
    FOREIGN KEY (`lang_id`)
    REFERENCES `lang` (`id`)
    ON UPDATE RESTRICT
    ON DELETE SET NULL;

# The existing credit notes keep the language they were printed in until now: the one of their
# customer, or of their order when the customer has none.
UPDATE `credit_note`
    LEFT JOIN `customer` ON `customer`.`id` = `credit_note`.`customer_id`
    LEFT JOIN `order` ON `order`.`id` = `credit_note`.`order_id`
SET `credit_note`.`lang_id` = COALESCE(`customer`.`lang_id`, `order`.`lang_id`)
WHERE `credit_note`.`lang_id` IS NULL;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
