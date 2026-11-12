# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

# The payment part of the order a use of a credit note settled: what the module gives back when
# the core releases that part. Empty on the uses written before the credit note paid orders.
ALTER TABLE `order_credit_note` ADD `order_payment_part_id` INTEGER AFTER `amount_price`;
ALTER TABLE `order_credit_note` ADD INDEX `idx_order_credit_note_order_payment_part_id_fk` (`order_payment_part_id`);
ALTER TABLE `order_credit_note` ADD CONSTRAINT `order_credit_note_FK_3`
    FOREIGN KEY (`order_payment_part_id`)
    REFERENCES `order_payment_part` (`id`)
    ON UPDATE RESTRICT
    ON DELETE SET NULL;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
