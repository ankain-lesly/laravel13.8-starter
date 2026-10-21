-- Migration: create_withdrawals_table (UP)
-- Target: MySQL / MariaDB

CREATE TABLE IF NOT EXISTS `withdrawals` (
    `id` CHAR(26) NOT NULL,
    `user_id` CHAR(26) NOT NULL,
    `payment_method_id` CHAR(26) NOT NULL,
    `amount` DECIMAL(18, 8) NOT NULL,
    `currency` VARCHAR(16) NOT NULL DEFAULT 'USD',
    `payout_details` JSON NULL DEFAULT NULL,
    `status` VARCHAR(32) NOT NULL DEFAULT 'processing',
    `note` TEXT NULL DEFAULT NULL,
    `admin_notes` TEXT NULL DEFAULT NULL,
    `rejection_reason` TEXT NULL DEFAULT NULL,
    `reviewed_by` CHAR(26) NULL DEFAULT NULL,
    `reviewed_at` TIMESTAMP NULL DEFAULT NULL,
    `transaction_id` CHAR(26) NULL DEFAULT NULL,
    `completed_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `withdrawals_status_index` (`status`),
    INDEX `withdrawals_created_at_index` (`created_at`),
    CONSTRAINT `withdrawals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `withdrawals_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `withdrawals_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `withdrawals_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
