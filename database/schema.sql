-- ============================================================
-- Bright of Amana Business Group - Investment Management System
-- Database Schema
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- Create database
-- ------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `bright_of_amana` 
  DEFAULT CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `bright_of_amana`;

-- ------------------------------------------------------------
-- Table: users
-- Core user accounts (Admin + Investor)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(20) NULL DEFAULT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'investor', 'super_admin', 'staff') NOT NULL DEFAULT 'investor',
  `status` ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `remember_token` VARCHAR(100) NULL DEFAULT NULL,
  `last_login_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_idx` (`role`),
  KEY `users_status_idx` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: investors
-- Extended investor profile (links to users)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `investors`;
CREATE TABLE `investors` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `investor_code` VARCHAR(20) NOT NULL,
  `join_date` DATE NOT NULL,
  `notes` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `investors_user_id_unique` (`user_id`),
  UNIQUE KEY `investors_code_unique` (`investor_code`),
  CONSTRAINT `investors_user_fk` FOREIGN KEY (`user_id`) 
    REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: investments
-- Monthly investment submissions (investor upload → admin approve)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `investments`;
CREATE TABLE `investments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `investor_id` INT UNSIGNED NOT NULL,
  `month` TINYINT UNSIGNED NOT NULL COMMENT '1-12',
  `year` SMALLINT UNSIGNED NOT NULL,
  `amount` DECIMAL(15, 2) NOT NULL,
  `payment_mode` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Bank transfer, UPI, Cash, etc.',
  `transaction_ref` VARCHAR(100) NULL DEFAULT NULL,
  `payment_proof_path` VARCHAR(255) NULL DEFAULT NULL,
  `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  `admin_id` INT UNSIGNED NULL DEFAULT NULL COMMENT 'Admin who approved/rejected',
  `admin_remark` TEXT NULL DEFAULT NULL,
  `submitted_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `processed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `investments_investor_month_year_unique` (`investor_id`, `month`, `year`),
  KEY `investments_status_idx` (`status`),
  KEY `investments_month_year_idx` (`month`, `year`),
  KEY `investments_submitted_at_idx` (`submitted_at`),
  CONSTRAINT `investments_investor_fk` FOREIGN KEY (`investor_id`) 
    REFERENCES `investors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `investments_admin_fk` FOREIGN KEY (`admin_id`) 
    REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: admin_actions_log
-- Audit trail for admin actions (approve, reject, edit, etc.)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `admin_actions_log`;
CREATE TABLE `admin_actions_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` INT UNSIGNED NOT NULL,
  `investment_id` INT UNSIGNED NOT NULL,
  `action` VARCHAR(50) NOT NULL COMMENT 'approved, rejected, viewed, edited',
  `remark` TEXT NULL DEFAULT NULL,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `admin_actions_log_admin_idx` (`admin_id`),
  KEY `admin_actions_log_investment_idx` (`investment_id`),
  KEY `admin_actions_log_created_at_idx` (`created_at`),
  CONSTRAINT `admin_actions_admin_fk` FOREIGN KEY (`admin_id`) 
    REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `admin_actions_investment_fk` FOREIGN KEY (`investment_id`) 
    REFERENCES `investments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Optional: password_reset_tokens (for forgot password)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` VARCHAR(150) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Optional: sessions (if using DB sessions)
-- ------------------------------------------------------------
-- DROP TABLE IF EXISTS `sessions`;
-- CREATE TABLE `sessions` (
--   `id` VARCHAR(255) NOT NULL,
--   `user_id` INT UNSIGNED NULL,
--   `ip_address` VARCHAR(45) NULL,
--   `user_agent` TEXT NULL,
--   `payload` TEXT NOT NULL,
--   `last_activity` INT NOT NULL,
--   PRIMARY KEY (`id`),
--   KEY `sessions_user_id_idx` (`user_id`),
--   KEY `sessions_last_activity_idx` (`last_activity`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- Seed: Default Super Admin (change password after first login!)
-- Password: Admin@123 (use bcrypt in app)
-- ============================================================
-- INSERT INTO `users` (`name`, `email`, `password_hash`, `role`, `status`) VALUES
-- ('Super Admin', 'admin@brightofamana.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', 'active');
