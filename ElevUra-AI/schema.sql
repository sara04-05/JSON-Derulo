-- ============================================================
-- ElevUra AI — MySQL Database Schema
-- Run this in phpMyAdmin or MySQL CLI to set up the database.
-- ============================================================

CREATE DATABASE IF NOT EXISTS `elevura_ai`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `elevura_ai`;

-- ─── Study Sessions ──────────────────────────────────────────
-- Tracks each study chat session
CREATE TABLE IF NOT EXISTS `study_sessions` (
  `id` VARCHAR(64) NOT NULL,
  `title` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Study Messages ──────────────────────────────────────────
-- Individual chat messages within a study session
CREATE TABLE IF NOT EXISTS `study_messages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` VARCHAR(64) NOT NULL,
  `role` ENUM('user','model') NOT NULL,
  `message` LONGTEXT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_session_id` (`session_id`),
  INDEX `idx_created_at` (`created_at`),
  CONSTRAINT `fk_study_messages_session`
    FOREIGN KEY (`session_id`) REFERENCES `study_sessions` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Research Sessions ───────────────────────────────────────
-- Tracks each research / job-search session
CREATE TABLE IF NOT EXISTS `research_sessions` (
  `id` VARCHAR(64) NOT NULL,
  `title` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Research Results ────────────────────────────────────────
-- Stores AI-generated results for research queries
CREATE TABLE IF NOT EXISTS `research_results` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` VARCHAR(64) DEFAULT NULL,
  `query` TEXT NOT NULL,
  `result_type` VARCHAR(50) NOT NULL DEFAULT 'job_search',
  `result_text` LONGTEXT NOT NULL,
  `metadata` JSON DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_session_id` (`session_id`),
  INDEX `idx_result_type` (`result_type`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Saved Searches ──────────────────────────────────────────
-- User-bookmarked searches and comparisons
CREATE TABLE IF NOT EXISTS `saved_searches` (
  `id` VARCHAR(64) NOT NULL,
  `search_type` VARCHAR(50) NOT NULL DEFAULT 'job_search',
  `query` TEXT NOT NULL,
  `location` VARCHAR(255) DEFAULT NULL,
  `level` VARCHAR(100) DEFAULT NULL,
  `result_data` LONGTEXT DEFAULT NULL,
  `metadata` JSON DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_search_type` (`search_type`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── User Notes (Optional) ──────────────────────────────────
-- General notes a user can save from any module
CREATE TABLE IF NOT EXISTS `user_notes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) DEFAULT NULL,
  `content` LONGTEXT NOT NULL,
  `module` VARCHAR(50) NOT NULL DEFAULT 'general',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_module` (`module`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
