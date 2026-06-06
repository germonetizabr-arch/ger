-- PALMED Clinic v1.0 - Database Schema
-- MySQL 8.0+ | UTF8MB4

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- System settings
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `setting_key` VARCHAR(100) NOT NULL,
    `setting_value` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_settings_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Roles & permissions
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `slug` VARCHAR(50) NOT NULL,
    `description` VARCHAR(255) NULL,
    `is_system` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_roles_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permissions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `module` VARCHAR(50) NOT NULL,
    `description` VARCHAR(255) NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_permissions_slug` (`slug`),
    KEY `idx_permissions_module` (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `role_permissions` (
    `role_id` INT UNSIGNED NOT NULL,
    `permission_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`role_id`, `permission_id`),
    CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Users
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `role_id` INT UNSIGNED NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(30) NULL,
    `professional_license` VARCHAR(50) NULL,
    `signature_path` VARCHAR(255) NULL,
    `avatar_path` VARCHAR(255) NULL,
    `language` ENUM('es','en') NOT NULL DEFAULT 'es',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `last_login_at` DATETIME NULL,
    `remember_token` VARCHAR(100) NULL,
    `password_reset_token` VARCHAR(100) NULL,
    `password_reset_expires` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_users_email` (`email`),
    KEY `idx_users_role` (`role_id`),
    KEY `idx_users_active` (`is_active`),
    CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Specialties
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `specialties` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_specialties_slug` (`slug`),
    KEY `idx_specialties_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `user_specialties` (
    `user_id` INT UNSIGNED NOT NULL,
    `specialty_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`user_id`, `specialty_id`),
    CONSTRAINT `fk_us_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_us_specialty` FOREIGN KEY (`specialty_id`) REFERENCES `specialties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Patients
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `patients` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `document_type` VARCHAR(20) NOT NULL DEFAULT 'CC',
    `document_number` VARCHAR(50) NOT NULL,
    `date_of_birth` DATE NULL,
    `age` TINYINT UNSIGNED NULL,
    `sex` ENUM('M','F','O') NOT NULL DEFAULT 'O',
    `phone` VARCHAR(30) NULL,
    `email` VARCHAR(255) NULL,
    `address` TEXT NULL,
    `occupation` VARCHAR(100) NULL,
    `emergency_contact_name` VARCHAR(150) NULL,
    `emergency_contact_phone` VARCHAR(30) NULL,
    `notes` TEXT NULL,
    `created_by` INT UNSIGNED NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_patients_document` (`document_type`, `document_number`),
    KEY `idx_patients_name` (`last_name`, `first_name`),
    KEY `idx_patients_phone` (`phone`),
    KEY `idx_patients_email` (`email`),
    KEY `idx_patients_active` (`is_active`),
    CONSTRAINT `fk_patients_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- ICD-10 codes
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `icd10_codes` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(10) NOT NULL,
    `description_es` VARCHAR(500) NOT NULL,
    `description_en` VARCHAR(500) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_icd10_code` (`code`),
    KEY `idx_icd10_desc_es` (`description_es`(100)),
    KEY `idx_icd10_desc_en` (`description_en`(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Appointments
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `appointments` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `patient_id` INT UNSIGNED NOT NULL,
    `physician_id` INT UNSIGNED NOT NULL,
    `specialty_id` INT UNSIGNED NULL,
    `appointment_date` DATE NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NULL,
    `status` ENUM('scheduled','confirmed','completed','cancelled','no_show') NOT NULL DEFAULT 'scheduled',
    `reason` VARCHAR(255) NULL,
    `notes` TEXT NULL,
    `telemedicine_link` VARCHAR(500) NULL,
    `telemedicine_type` ENUM('google_meet','zoom','teams','whatsapp','other') NULL,
    `created_by` INT UNSIGNED NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_appointments_date` (`appointment_date`),
    KEY `idx_appointments_physician` (`physician_id`, `appointment_date`),
    KEY `idx_appointments_patient` (`patient_id`),
    KEY `idx_appointments_status` (`status`),
    CONSTRAINT `fk_appointments_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
    CONSTRAINT `fk_appointments_physician` FOREIGN KEY (`physician_id`) REFERENCES `users` (`id`),
    CONSTRAINT `fk_appointments_specialty` FOREIGN KEY (`specialty_id`) REFERENCES `specialties` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_appointments_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Consultations (core module)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `consultations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `patient_id` INT UNSIGNED NOT NULL,
    `physician_id` INT UNSIGNED NOT NULL,
    `appointment_id` INT UNSIGNED NULL,
    `specialty_id` INT UNSIGNED NULL,
    `consultation_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('draft','completed','cancelled') NOT NULL DEFAULT 'draft',
    `reason_for_consultation` TEXT NULL,
    `current_illness` TEXT NULL,
    `past_medical_history` TEXT NULL,
    `surgical_history` TEXT NULL,
    `family_history` TEXT NULL,
    `allergies` TEXT NULL,
    `current_medications` TEXT NULL,
    `blood_pressure_systolic` SMALLINT UNSIGNED NULL,
    `blood_pressure_diastolic` SMALLINT UNSIGNED NULL,
    `heart_rate` SMALLINT UNSIGNED NULL,
    `respiratory_rate` SMALLINT UNSIGNED NULL,
    `temperature` DECIMAL(4,1) NULL,
    `weight` DECIMAL(5,2) NULL,
    `height` DECIMAL(5,2) NULL,
    `bmi` DECIMAL(5,2) NULL,
    `physical_examination` TEXT NULL,
    `assessment` TEXT NULL,
    `management_plan` TEXT NULL,
    `medications_prescribed` TEXT NULL,
    `medical_orders` TEXT NULL,
    `recommendations` TEXT NULL,
    `follow_up_plan` TEXT NULL,
    `medical_leave_days` SMALLINT UNSIGNED NULL,
    `medical_leave_from` DATE NULL,
    `medical_leave_to` DATE NULL,
    `medical_leave_reason` TEXT NULL,
    `digital_signature` TEXT NULL,
    `signed_at` DATETIME NULL,
    `created_by` INT UNSIGNED NULL,
    `updated_by` INT UNSIGNED NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_consultations_patient` (`patient_id`),
    KEY `idx_consultations_physician` (`physician_id`),
    KEY `idx_consultations_date` (`consultation_date`),
    KEY `idx_consultations_status` (`status`),
    CONSTRAINT `fk_consultations_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
    CONSTRAINT `fk_consultations_physician` FOREIGN KEY (`physician_id`) REFERENCES `users` (`id`),
    CONSTRAINT `fk_consultations_appointment` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_consultations_specialty` FOREIGN KEY (`specialty_id`) REFERENCES `specialties` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_consultations_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_consultations_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `consultation_diagnoses` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `consultation_id` INT UNSIGNED NOT NULL,
    `icd10_id` INT UNSIGNED NULL,
    `icd10_code` VARCHAR(10) NULL,
    `description` VARCHAR(500) NOT NULL,
    `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_cd_consultation` (`consultation_id`),
    CONSTRAINT `fk_cd_consultation` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cd_icd10` FOREIGN KEY (`icd10_id`) REFERENCES `icd10_codes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Documents
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `documents` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `patient_id` INT UNSIGNED NOT NULL,
    `consultation_id` INT UNSIGNED NULL,
    `uploaded_by` INT UNSIGNED NULL,
    `title` VARCHAR(255) NOT NULL,
    `document_type` ENUM('pdf','image','lab_report','external','generated') NOT NULL DEFAULT 'external',
    `file_path` VARCHAR(500) NOT NULL,
    `file_size` INT UNSIGNED NULL,
    `mime_type` VARCHAR(100) NULL,
    `notes` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_documents_patient` (`patient_id`),
    KEY `idx_documents_consultation` (`consultation_id`),
    CONSTRAINT `fk_documents_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_documents_consultation` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_documents_uploaded_by` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Audit log
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NULL,
    `action` VARCHAR(50) NOT NULL,
    `entity_type` VARCHAR(50) NULL,
    `entity_id` INT UNSIGNED NULL,
    `description` TEXT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(500) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_audit_user` (`user_id`),
    KEY `idx_audit_action` (`action`),
    KEY `idx_audit_entity` (`entity_type`, `entity_id`),
    KEY `idx_audit_created` (`created_at`),
    CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
