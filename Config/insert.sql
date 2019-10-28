SET FOREIGN_KEY_CHECKS = 0;

INSERT INTO `credit_note_status` (`id`, `code`, `position`, `color`, `invoiced`, `used`, `created_at`, `updated_at`) VALUES
(1, 'proposed', 1, '#999966', 0, 0, NOW(), NOW()),
(2, 'refused', 2, '#e60000', 0, 0, NOW(), NOW()),
(3, 'accepted', 3, '#00cc00', 1, 0, NOW(), NOW()),
(4, 'used', 4, '#3399ff', 1, 1, NOW(), NOW());

INSERT INTO `credit_note_status_i18n` (`id`, `locale`, `title`, `description`, `chapo`, `postscriptum`) VALUES
(1, 'en_US', 'Proposed', '', '', ''),
(1, 'fr_FR', 'Proposé', '', '', ''),
(2, 'en_US', 'Refused', '', '', ''),
(2, 'fr_FR', 'Refusé', '', '', ''),
(3, 'en_US', 'Accepted', '', '', ''),
(3, 'fr_FR', 'Accepté', '', '', ''),
(4, 'en_US', 'Used', '', '', ''),
(4, 'fr_FR', 'Utilisé', '', '', '');

INSERT INTO `credit_note_status_flow` (`id`, `from_status_id`, `to_status_id`, `priority`, `root`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 1, 1, NOW(), NOW()),
(2, 1, 3, 1, 0, NOW(), NOW()),
(3, 2, 1, 1, 1, NOW(), NOW()),
(4, 3, 4, 1, 0, NOW(), NOW());

INSERT INTO `credit_note_type` (`id`, `code`, `position`, `color`, `required_order`, `created_at`, `updated_at`) VALUES
(1, 'order_full_refund', 1, '#1abc9c', 1, NOW(), NOW()),
(2, 'back_product', 2, '#40d47e', 1, NOW(), NOW()),
(3, 'billing_error', 3, '#3498db', 1, NOW(), NOW()),
(4, 'rebate', 4, '#9b59b6', 0, NOW(), NOW()),
(5, 'discount', 5, '#34495e', 0, NOW(), NOW()),
(6, 'difference_refund', 5, '#CF7973', 0, NOW(), NOW());

INSERT INTO `credit_note_type_i18n` (`id`, `locale`, `title`, `description`, `chapo`, `postscriptum`) VALUES
(1, 'en_US', 'Order Full refund', '', '', ''),
(1, 'fr_FR', 'Remboursement complet', '', '', ''),
(2, 'en_US', 'Back Product', '', '', ''),
(2, 'fr_FR', 'Retour produit', '', '', ''),
(3, 'en_US', 'Billing error', '', '', ''),
(3, 'fr_FR', 'Erreur de facturation', '', '', ''),
(4, 'en_US', 'A rebate', '', '', ''),
(4, 'fr_FR', 'Un rabais', '', '', ''),
(5, 'en_US', 'A discount', '', '', ''),
(5, 'fr_FR', 'Une remise', '', '', ''),
(6, 'en_US', 'Difference refund', '', '', ''),
(6, 'fr_FR', 'Remboursement de la différence', '', '', '');

SET FOREIGN_KEY_CHECKS = 1;
