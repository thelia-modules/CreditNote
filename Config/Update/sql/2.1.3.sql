
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

SELECT @max_id := IFNULL(MAX(`id`),0) FROM `credit_note_type`;

INSERT INTO `credit_note_type` (`id`, `code`, `position`, `color`, `required_order`, `created_at`, `updated_at`) VALUES
(@max_id+1, 'difference_refund', @max_id+1, '#CF7973', 0, NOW(), NOW());

INSERT INTO `credit_note_type_i18n` (`id`, `locale`, `title`, `description`, `chapo`, `postscriptum`) VALUES
(@max_id+1, 'en_US', 'Difference refund', '', '', ''),
(@max_id+1, 'fr_FR', 'Remboursement de la différence', '', '', '');

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
