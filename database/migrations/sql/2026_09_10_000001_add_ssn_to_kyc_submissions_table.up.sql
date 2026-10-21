-- Migration: add_ssn_to_kyc_submissions_table (UP)
-- Target: MySQL / MariaDB

ALTER TABLE `kyc_submissions`
  ADD COLUMN `ssn` TEXT NULL DEFAULT NULL AFTER `occupation`;
