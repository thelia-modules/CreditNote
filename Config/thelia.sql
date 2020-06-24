
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- credit_note
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `credit_note`;

CREATE TABLE `credit_note`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `ref` VARCHAR(45),
    `invoice_ref` VARCHAR(45),
    `invoice_address_id` INTEGER NOT NULL,
    `invoice_date` DATETIME,
    `order_id` INTEGER,
    `customer_id` INTEGER NOT NULL,
    `parent_id` INTEGER,
    `type_id` INTEGER NOT NULL,
    `status_id` INTEGER NOT NULL,
    `currency_id` INTEGER NOT NULL,
    `currency_rate` FLOAT,
    `total_price` DECIMAL(16,6) DEFAULT 0.000000,
    `total_price_with_tax` DECIMAL(16,6) DEFAULT 0.000000,
    `discount_without_tax` DECIMAL(16,6) DEFAULT 0.000000,
    `discount_with_tax` DECIMAL(16,6) DEFAULT 0.000000,
    `allow_partial_use` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `ref_UNIQUE` (`ref`),
    UNIQUE INDEX `invoice_ref_UNIQUE` (`invoice_ref`),
    INDEX `idx_order_id_fk` (`order_id`),
    INDEX `idx_customer_id_fk` (`customer_id`),
    INDEX `idx_parent_id_fk` (`parent_id`),
    INDEX `idx_type_id_fk` (`type_id`),
    INDEX `idx_status_id_fk` (`status_id`),
    INDEX `credit_note_FI_6` (`currency_id`),
    INDEX `credit_note_FI_7` (`invoice_address_id`),
    CONSTRAINT `credit_note_FK_1`
        FOREIGN KEY (`order_id`)
        REFERENCES `order` (`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT `credit_note_FK_2`
        FOREIGN KEY (`customer_id`)
        REFERENCES `customer` (`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT `credit_note_FK_3`
        FOREIGN KEY (`parent_id`)
        REFERENCES `credit_note` (`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT `credit_note_FK_4`
        FOREIGN KEY (`type_id`)
        REFERENCES `credit_note_type` (`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT `credit_note_FK_5`
        FOREIGN KEY (`status_id`)
        REFERENCES `credit_note_status` (`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT `credit_note_FK_6`
        FOREIGN KEY (`currency_id`)
        REFERENCES `currency` (`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT `credit_note_FK_7`
        FOREIGN KEY (`invoice_address_id`)
        REFERENCES `credit_note_address` (`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- credit_note_address
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `credit_note_address`;

CREATE TABLE `credit_note_address`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `customer_title_id` INTEGER,
    `company` VARCHAR(255),
    `firstname` VARCHAR(255) NOT NULL,
    `lastname` VARCHAR(255) NOT NULL,
    `address1` VARCHAR(255) NOT NULL,
    `address2` VARCHAR(255),
    `address3` VARCHAR(255),
    `zipcode` VARCHAR(10) NOT NULL,
    `city` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `cellphone` VARCHAR(20),
    `country_id` INTEGER NOT NULL,
    `state_id` INTEGER,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `FI_credit_note_address_customer_title_id` (`customer_title_id`),
    INDEX `FI_credit_note_address_country_id` (`country_id`),
    INDEX `FI_credit_note_address_state_id` (`state_id`),
    CONSTRAINT `fk_credit_note_address_customer_title_id`
        FOREIGN KEY (`customer_title_id`)
        REFERENCES `customer_title` (`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT `fk_credit_note_address_country_id`
        FOREIGN KEY (`country_id`)
        REFERENCES `country` (`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT `fk_credit_note_address_state_id`
        FOREIGN KEY (`state_id`)
        REFERENCES `state` (`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- order_credit_note
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `order_credit_note`;

CREATE TABLE `order_credit_note`
(
    `order_id` INTEGER NOT NULL,
    `credit_note_id` INTEGER NOT NULL,
    `amount_price` DECIMAL(16,6) DEFAULT 0.000000,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`order_id`,`credit_note_id`),
    INDEX `order_credit_note_FI_2` (`credit_note_id`),
    CONSTRAINT `order_credit_note_FK_1`
        FOREIGN KEY (`order_id`)
        REFERENCES `order` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT `order_credit_note_FK_2`
        FOREIGN KEY (`credit_note_id`)
        REFERENCES `credit_note` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- cart_credit_note
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `cart_credit_note`;

CREATE TABLE `cart_credit_note`
(
    `cart_id` INTEGER NOT NULL,
    `credit_note_id` INTEGER NOT NULL,
    `amount_price` DECIMAL(16,6) DEFAULT 0.000000,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`cart_id`,`credit_note_id`),
    INDEX `cart_credit_note_FI_2` (`credit_note_id`),
    CONSTRAINT `cart_credit_note_FK_1`
        FOREIGN KEY (`cart_id`)
        REFERENCES `cart` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT `cart_credit_note_FK_2`
        FOREIGN KEY (`credit_note_id`)
        REFERENCES `credit_note` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- credit_note_status
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `credit_note_status`;

CREATE TABLE `credit_note_status`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(45),
    `color` CHAR(7),
    `invoiced` TINYINT(1) DEFAULT 0 NOT NULL,
    `used` TINYINT(1) DEFAULT 0 NOT NULL,
    `position` INTEGER(11),
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- credit_note_status_flow
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `credit_note_status_flow`;

CREATE TABLE `credit_note_status_flow`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `from_status_id` INTEGER NOT NULL,
    `to_status_id` INTEGER NOT NULL,
    `priority` INTEGER(11),
    `root` TINYINT(1) DEFAULT 0 NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `FI_dit_note_status_flow_credit_note_status_from` (`from_status_id`),
    INDEX `FI_dit_note_status_flow_credit_note_status_to` (`to_status_id`),
    CONSTRAINT `credit_note_status_flow_credit_note_status_from`
        FOREIGN KEY (`from_status_id`)
        REFERENCES `credit_note_status` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT `credit_note_status_flow_credit_note_status_to`
        FOREIGN KEY (`to_status_id`)
        REFERENCES `credit_note_status` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- credit_note_type
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `credit_note_type`;

CREATE TABLE `credit_note_type`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(45),
    `color` CHAR(7),
    `position` INTEGER(11),
    `required_order` TINYINT(1) DEFAULT 0 NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- credit_note_detail
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `credit_note_detail`;

CREATE TABLE `credit_note_detail`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `credit_note_id` INTEGER NOT NULL,
    `price` DECIMAL(16,6) DEFAULT 0.000000,
    `price_with_tax` DECIMAL(16,6) DEFAULT 0.000000,
    `tax_rule_id` INTEGER,
    `order_product_id` INTEGER,
    `type` VARCHAR(55),
    `quantity` INTEGER DEFAULT 0 NOT NULL,
    `title` VARCHAR(255),
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `idx_credit_note_id_fk` (`credit_note_id`),
    INDEX `idx_order_product_id_fk` (`order_product_id`),
    INDEX `credit_note_detail_FI_3` (`tax_rule_id`),
    CONSTRAINT `credit_note_detail_FK_1`
        FOREIGN KEY (`credit_note_id`)
        REFERENCES `credit_note` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT `credit_note_detail_FK_2`
        FOREIGN KEY (`order_product_id`)
        REFERENCES `order_product` (`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT `credit_note_detail_FK_3`
        FOREIGN KEY (`tax_rule_id`)
        REFERENCES `tax_rule` (`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- credit_note_comment
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `credit_note_comment`;

CREATE TABLE `credit_note_comment`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `credit_note_id` INTEGER NOT NULL,
    `admin_id` INTEGER,
    `comment` LONGTEXT,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `idx_credit_note_id_fk` (`credit_note_id`),
    INDEX `idx_admin_id_fk` (`admin_id`),
    CONSTRAINT `credit_note_comment_FK_1`
        FOREIGN KEY (`credit_note_id`)
        REFERENCES `credit_note` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT `credit_note_comment_FK_2`
        FOREIGN KEY (`admin_id`)
        REFERENCES `admin` (`id`)
        ON UPDATE RESTRICT
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- credit_note_status_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `credit_note_status_i18n`;

CREATE TABLE `credit_note_status_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `title` VARCHAR(255),
    `description` LONGTEXT,
    `chapo` TEXT,
    `postscriptum` TEXT,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `credit_note_status_i18n_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `credit_note_status` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- credit_note_type_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `credit_note_type_i18n`;

CREATE TABLE `credit_note_type_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `title` VARCHAR(255),
    `description` LONGTEXT,
    `chapo` TEXT,
    `postscriptum` TEXT,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `credit_note_type_i18n_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `credit_note_type` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
