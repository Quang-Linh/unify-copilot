-- =============================================
-- ProActiv - Schéma de base de données
-- Version: 1.0.0
-- Compatible: MySQL 5.7+ / MariaDB 10.3+
-- =============================================

SET FOREIGN_KEY_CHECKS = 0;
SET sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';

-- =============================================
-- Table des utilisateurs
-- =============================================
CREATE TABLE IF NOT EXISTS `users` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,
    `firstname` varchar(100) NOT NULL,
    `lastname` varchar(100) NOT NULL,
    `phone` varchar(20) DEFAULT NULL,
    `siret` varchar(14) DEFAULT NULL,
    `activity_type` enum('liberal','commercial','artisanal','mixte') DEFAULT 'liberal',
    `activity_description` text DEFAULT NULL,
    `registration_date` date DEFAULT NULL,
    `email_verified` tinyint(1) DEFAULT 0,
    `email_verification_token` varchar(255) DEFAULT NULL,
    `active` tinyint(1) DEFAULT 1,
    `role` enum('user','admin','moderator') DEFAULT 'user',
    `last_login` datetime DEFAULT NULL,
    `failed_login_attempts` int(3) DEFAULT 0,
    `locked_until` datetime DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`),
    KEY `idx_active` (`active`),
    KEY `idx_email_verified` (`email_verified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table des profils utilisateurs
-- =============================================
CREATE TABLE IF NOT EXISTS `user_profiles` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(11) unsigned NOT NULL,
    `address` text DEFAULT NULL,
    `postal_code` varchar(5) DEFAULT NULL,
    `city` varchar(100) DEFAULT NULL,
    `country` varchar(2) DEFAULT 'FR',
    `business_name` varchar(255) DEFAULT NULL,
    `business_address` text DEFAULT NULL,
    `website` varchar(255) DEFAULT NULL,
    `bio` text DEFAULT NULL,
    `avatar` varchar(255) DEFAULT NULL,
    `preferences` json DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_id` (`user_id`),
    CONSTRAINT `fk_user_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table des documents
-- =============================================
CREATE TABLE IF NOT EXISTS `documents` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(11) unsigned NOT NULL,
    `type` enum('invoice','quote','receipt','certificate','report') NOT NULL,
    `title` varchar(255) NOT NULL,
    `description` text DEFAULT NULL,
    `data` json NOT NULL,
    `file_path` varchar(500) DEFAULT NULL,
    `file_size` int(11) DEFAULT NULL,
    `status` enum('draft','finalized','sent','archived') DEFAULT 'draft',
    `reference_number` varchar(50) DEFAULT NULL,
    `amount` decimal(10,2) DEFAULT NULL,
    `currency` varchar(3) DEFAULT 'EUR',
    `due_date` date DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_type` (`type`),
    KEY `idx_status` (`status`),
    KEY `idx_reference` (`reference_number`),
    CONSTRAINT `fk_documents_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table des calculs de charges
-- =============================================
CREATE TABLE IF NOT EXISTS `charge_calculations` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(11) unsigned NOT NULL,
    `period_year` int(4) NOT NULL,
    `period_month` int(2) DEFAULT NULL,
    `revenue` decimal(10,2) NOT NULL,
    `activity_type` enum('liberal','commercial','artisanal','mixte') NOT NULL,
    `social_charges` decimal(10,2) NOT NULL,
    `tax_amount` decimal(10,2) DEFAULT NULL,
    `net_income` decimal(10,2) NOT NULL,
    `calculation_data` json NOT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_period` (`user_id`, `period_year`, `period_month`),
    CONSTRAINT `fk_charges_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table des catégories de forum
-- =============================================
CREATE TABLE IF NOT EXISTS `forum_categories` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `description` text DEFAULT NULL,
    `icon` varchar(50) DEFAULT NULL,
    `color` varchar(7) DEFAULT '#007bff',
    `sort_order` int(3) DEFAULT 0,
    `active` tinyint(1) DEFAULT 1,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_active_sort` (`active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table des sujets de forum
-- =============================================
CREATE TABLE IF NOT EXISTS `forum_topics` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `category_id` int(11) unsigned NOT NULL,
    `user_id` int(11) unsigned NOT NULL,
    `title` varchar(255) NOT NULL,
    `content` text NOT NULL,
    `views` int(11) DEFAULT 0,
    `replies_count` int(11) DEFAULT 0,
    `last_reply_at` timestamp NULL DEFAULT NULL,
    `last_reply_user_id` int(11) unsigned DEFAULT NULL,
    `pinned` tinyint(1) DEFAULT 0,
    `locked` tinyint(1) DEFAULT 0,
    `active` tinyint(1) DEFAULT 1,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_category` (`category_id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_active_pinned` (`active`, `pinned`, `last_reply_at`),
    CONSTRAINT `fk_topics_category` FOREIGN KEY (`category_id`) REFERENCES `forum_categories` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_topics_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table des messages de forum
-- =============================================
CREATE TABLE IF NOT EXISTS `forum_posts` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `topic_id` int(11) unsigned NOT NULL,
    `user_id` int(11) unsigned NOT NULL,
    `content` text NOT NULL,
    `likes_count` int(11) DEFAULT 0,
    `active` tinyint(1) DEFAULT 1,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_topic` (`topic_id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_active_created` (`active`, `created_at`),
    CONSTRAINT `fk_posts_topic` FOREIGN KEY (`topic_id`) REFERENCES `forum_topics` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_posts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table des notifications
-- =============================================
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(11) unsigned NOT NULL,
    `type` varchar(50) NOT NULL,
    `title` varchar(255) NOT NULL,
    `message` text NOT NULL,
    `data` json DEFAULT NULL,
    `read_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_read` (`user_id`, `read_at`),
    KEY `idx_created` (`created_at`),
    CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table des sessions utilisateur
-- =============================================
CREATE TABLE IF NOT EXISTS `user_sessions` (
    `id` varchar(128) NOT NULL,
    `user_id` int(11) unsigned DEFAULT NULL,
    `ip_address` varchar(45) NOT NULL,
    `user_agent` text DEFAULT NULL,
    `data` text NOT NULL,
    `last_activity` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_last_activity` (`last_activity`),
    CONSTRAINT `fk_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table des logs d'activité
-- =============================================
CREATE TABLE IF NOT EXISTS `activity_logs` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(11) unsigned DEFAULT NULL,
    `action` varchar(100) NOT NULL,
    `description` text DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` text DEFAULT NULL,
    `data` json DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_action` (`user_id`, `action`),
    KEY `idx_created` (`created_at`),
    CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Données de base
-- =============================================

-- Catégories de forum par défaut
INSERT INTO `forum_categories` (`name`, `description`, `icon`, `color`, `sort_order`) VALUES
('Questions générales', 'Questions sur le statut auto-entrepreneur', 'fas fa-question-circle', '#007bff', 1),
('Fiscalité & Charges', 'Discussions sur les impôts et charges sociales', 'fas fa-calculator', '#28a745', 2),
('Administratif', 'Démarches et formalités administratives', 'fas fa-file-alt', '#ffc107', 3),
('Outils & Ressources', 'Partage d\'outils et ressources utiles', 'fas fa-tools', '#17a2b8', 4),
('Entraide', 'Entraide entre auto-entrepreneurs', 'fas fa-hands-helping', '#dc3545', 5);

-- Utilisateur administrateur par défaut
INSERT INTO `users` (`email`, `password`, `firstname`, `lastname`, `role`, `email_verified`, `active`) VALUES
('admin@proactiv.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'ProActiv', 'admin', 1, 1);

SET FOREIGN_KEY_CHECKS = 1;
