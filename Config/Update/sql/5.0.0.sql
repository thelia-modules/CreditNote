# The payment part of the order a use of a credit note settled: what the module gives back when
# the core releases that part. Empty on the uses written before the credit note paid orders.
#
# Each step is skipped when the shop already has it, so the script can be played again after an
# interrupted update or on a shop installed at this version.

SET @credit_note_step = (
    SELECT IF(COUNT(*) = 0, 'ALTER TABLE `order_credit_note` ADD `order_payment_part_id` INTEGER AFTER `amount_price`', 'SELECT 1')
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'order_credit_note' AND COLUMN_NAME = 'order_payment_part_id'
);
PREPARE credit_note_statement FROM @credit_note_step;
EXECUTE credit_note_statement;
DEALLOCATE PREPARE credit_note_statement;

SET @credit_note_step = (
    SELECT IF(COUNT(*) = 0, 'ALTER TABLE `order_credit_note` ADD INDEX `idx_order_credit_note_order_payment_part_id_fk` (`order_payment_part_id`)', 'SELECT 1')
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'order_credit_note' AND INDEX_NAME = 'idx_order_credit_note_order_payment_part_id_fk'
);
PREPARE credit_note_statement FROM @credit_note_step;
EXECUTE credit_note_statement;
DEALLOCATE PREPARE credit_note_statement;

SET @credit_note_step = (
    SELECT IF(COUNT(*) = 0, 'ALTER TABLE `order_credit_note` ADD CONSTRAINT `order_credit_note_FK_3` FOREIGN KEY (`order_payment_part_id`) REFERENCES `order_payment_part` (`id`) ON UPDATE RESTRICT ON DELETE SET NULL', 'SELECT 1')
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'order_credit_note' AND CONSTRAINT_NAME = 'order_credit_note_FK_3'
);
PREPARE credit_note_statement FROM @credit_note_step;
EXECUTE credit_note_statement;
DEALLOCATE PREPARE credit_note_statement;
