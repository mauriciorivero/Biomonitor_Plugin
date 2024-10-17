-- First, create tables without foreign keys
-- Create user types table
CREATE TABLE IF NOT EXISTS `bio_user_types` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) UNSIGNED NOT NULL,
    `user_type` enum('doctor', 'patient') NOT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create vital signs table
CREATE TABLE IF NOT EXISTS `bio_vital_signs` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `patient_id` bigint(20) UNSIGNED NOT NULL,
    `oximetry` decimal(5,2) NOT NULL,
    `heart_rate` int(11) NOT NULL,
    `reading_timestamp` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `patient_id` (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create diagnoses table
CREATE TABLE IF NOT EXISTS `bio_diagnoses` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `vital_sign_id` bigint(20) UNSIGNED NOT NULL,
    `doctor_id` bigint(20) UNSIGNED NOT NULL,
    `diagnosis` text NOT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `vital_sign_id` (`vital_sign_id`),
    KEY `doctor_id` (`doctor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Now add the foreign key constraints
ALTER TABLE `bio_user_types`
ADD CONSTRAINT `bio_user_types_ibfk_1` 
FOREIGN KEY (`user_id`) REFERENCES `wp_users` (`ID`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `bio_vital_signs`
ADD CONSTRAINT `bio_vital_signs_ibfk_1` 
FOREIGN KEY (`patient_id`) REFERENCES `wp_users` (`ID`)
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `bio_diagnoses`
ADD CONSTRAINT `bio_diagnoses_ibfk_1` 
FOREIGN KEY (`vital_sign_id`) REFERENCES `bio_vital_signs` (`id`)
ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `bio_diagnoses_ibfk_2` 
FOREIGN KEY (`doctor_id`) REFERENCES `wp_users` (`ID`)
ON DELETE CASCADE ON UPDATE CASCADE;