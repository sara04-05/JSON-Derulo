-- ElevUra Database Schema
-- Import via phpMyAdmin (XAMPP) — creates database + tables + demo seed data
-- Demo login: demo@elevura.ai / password123  (or username: demo)

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `elevura`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `elevura`;

DROP TABLE IF EXISTS `mock_interviews`;
DROP TABLE IF EXISTS `completed_courses`;
DROP TABLE IF EXISTS `applied_jobs`;
DROP TABLE IF EXISTS `cvs`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------------
-- users
-- ---------------------------------------------------------------------------
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `avatar` VARCHAR(512) DEFAULT NULL,
  `membership_tier` ENUM('Free', 'Pro', 'Elite') NOT NULL DEFAULT 'Free',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_username` (`username`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `idx_users_tier` (`membership_tier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- cvs
-- ---------------------------------------------------------------------------
CREATE TABLE `cvs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `cv_title` VARCHAR(255) NOT NULL,
  `ats_score` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `file_path` VARCHAR(512) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cvs_user_id` (`user_id`),
  KEY `idx_cvs_ats_score` (`ats_score`),
  CONSTRAINT `fk_cvs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- applied_jobs
-- ---------------------------------------------------------------------------
CREATE TABLE `applied_jobs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `company_name` VARCHAR(255) NOT NULL,
  `role_name` VARCHAR(255) NOT NULL,
  `status` ENUM('Applied', 'Interviewing', 'Accepted', 'Rejected') NOT NULL DEFAULT 'Applied',
  `applied_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_applied_jobs_user_id` (`user_id`),
  KEY `idx_applied_jobs_status` (`status`),
  CONSTRAINT `fk_applied_jobs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- completed_courses
-- ---------------------------------------------------------------------------
CREATE TABLE `completed_courses` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `course_name` VARCHAR(255) NOT NULL,
  `progress_percent` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `completed_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_completed_courses_user_id` (`user_id`),
  CONSTRAINT `fk_completed_courses_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- mock_interviews
-- ---------------------------------------------------------------------------
CREATE TABLE `mock_interviews` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `interview_score` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `confidence_score` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `communication_score` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `ai_feedback` TEXT,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_mock_interviews_user_id` (`user_id`),
  CONSTRAINT `fk_mock_interviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Demo user (password: password123)
-- ---------------------------------------------------------------------------
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `avatar`, `membership_tier`, `created_at`) VALUES
(1, 'demo', 'demo@elevura.ai', '$2y$10$A8DsAvWaPjqTxPk6m2YfI.aIZSnC5BPz.lNUFhhArJ0auvcoUWMyC',
 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=128&h=128&fit=crop&crop=faces',
 'Pro', '2026-01-15 10:00:00');

INSERT INTO `cvs` (`user_id`, `cv_title`, `ats_score`, `file_path`, `created_at`) VALUES
(1, 'Software Engineer — ATS Optimized', 92, 'uploads/cvs/demo_swe.pdf', '2026-05-12 14:30:00'),
(1, 'Product Designer Portfolio CV', 87, 'uploads/cvs/demo_design.pdf', '2026-05-03 09:15:00'),
(1, 'Graduate Data Analyst', 78, 'uploads/cvs/demo_analyst.pdf', '2026-04-28 16:45:00');

INSERT INTO `applied_jobs` (`user_id`, `company_name`, `role_name`, `status`, `applied_at`) VALUES
(1, 'NovaTech', 'Junior Developer', 'Interviewing', '2026-05-10 11:00:00'),
(1, 'Helix Health', 'Data Analyst Intern', 'Applied', '2026-05-02 08:30:00'),
(1, 'Orbit Labs', 'UX Research Associate', 'Rejected', '2026-04-18 13:20:00'),
(1, 'Pulse AI', 'ML Engineer Trainee', 'Accepted', '2026-04-05 10:00:00');

INSERT INTO `completed_courses` (`user_id`, `course_name`, `progress_percent`, `completed_at`) VALUES
(1, 'Advanced SQL for Analysts', 100, '2026-04-20 18:00:00'),
(1, 'System Design Fundamentals', 72, NULL),
(1, 'Behavioral Interview Mastery', 45, NULL);

INSERT INTO `mock_interviews` (`user_id`, `interview_score`, `confidence_score`, `communication_score`, `ai_feedback`, `created_at`) VALUES
(1, 87, 92, 88, 'Strong structure in behavioral answers. Reduce filler words in technical explanations. Lead with metrics when discussing project impact.', '2026-05-14 15:00:00'),
(1, 82, 85, 84, 'Good role alignment. Practice pacing under pressure for technical follow-ups.', '2026-05-08 12:30:00'),
(1, 78, 80, 79, 'Clear storytelling. Deepen technical depth on system design questions.', '2026-04-29 17:45:00');
