
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE credit_note
DROP COLUMN version;

ALTER TABLE credit_note
DROP COLUMN version_created_at;

ALTER TABLE credit_note
DROP COLUMN version_created_by;

DROP TABLE IF EXISTS `credit_note_version`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
