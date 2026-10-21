-- Migration: add_ssn_to_kyc_submissions_table (DOWN)
-- Target: MySQL / MariaDB

ALTER TABLE `kyc_submissions`
  DROP COLUMN `ssn`;
