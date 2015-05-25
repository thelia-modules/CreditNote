
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- order_credit_note
-- ---------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `order_credit_note`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `order_id` INTEGER NOT NULL,
    `amount` FLOAT NOT NULL,
    `message` TEXT,
    `coupon_id` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `order_credit_note_FI_1` (`order_id`),
    INDEX `order_credit_note_FI_2` (`coupon_id`),
    CONSTRAINT `order_credit_note_FK_1`
        FOREIGN KEY (`order_id`)
        REFERENCES `order` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT `order_credit_note_FK_2`
        FOREIGN KEY (`coupon_id`)
        REFERENCES `coupon` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
