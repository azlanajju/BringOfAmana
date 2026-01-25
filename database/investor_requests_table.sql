-- Add investor_requests table for "Request to Be Investor" form submissions
-- Run this if you have an existing bright_of_amana database.

USE `bright_of_amana`;

CREATE TABLE IF NOT EXISTS `investor_requests` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(20) NULL DEFAULT NULL,
  `message` TEXT NULL DEFAULT NULL,
  `status` ENUM('new','contacted','rejected') NOT NULL DEFAULT 'new',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `investor_requests_status_idx` (`status`),
  KEY `investor_requests_created_at_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
