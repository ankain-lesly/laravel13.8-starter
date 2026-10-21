-- Migration: create_account_activations_table (UP)
-- Target: MySQL / MariaDB

CREATE TABLE IF NOT EXISTS `account_activations` (
    `id` CHAR(26) NOT NULL,
    `user_id` CHAR(26) NOT NULL,
    `status` VARCHAR(32) NOT NULL DEFAULT 'pending',
    `fee_amount` DECIMAL(18, 2) NULL DEFAULT NULL,
    `currency` CHAR(3) NOT NULL DEFAULT 'USD',
    `auth_code` VARCHAR(6) NULL DEFAULT NULL,
    `auth_code_sent_at` TIMESTAMP NULL DEFAULT NULL,
    `completed_at` TIMESTAMP NULL DEFAULT NULL,
    `request_notes` TEXT NULL DEFAULT NULL,
    `admin_notes` TEXT NULL DEFAULT NULL,
    `reviewed_by` CHAR(26) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `account_activations_user_id_unique` (`user_id`),
    INDEX `account_activations_status_index` (`status`),
    INDEX `account_activations_created_at_index` (`created_at`),
    CONSTRAINT `account_activations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `account_activations_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
