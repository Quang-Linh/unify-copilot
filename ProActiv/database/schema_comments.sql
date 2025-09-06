-- =============================================
-- ProActiv - Schéma des commentaires utilisateurs
-- Version: 1.0.0
-- Compatible: MySQL 5.7+ / MariaDB 10.3+
-- =============================================

-- Table des commentaires/témoignages utilisateurs
CREATE TABLE IF NOT EXISTS `user_comments` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(11) unsigned DEFAULT NULL,
    `user_name` varchar(100) NOT NULL,
    `user_email` varchar(255) NOT NULL,
    `user_activity` varchar(255) DEFAULT NULL,
    `user_location` varchar(255) DEFAULT NULL,
    `rating` tinyint(1) NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
    `comment` text NOT NULL,
    `status` enum('pending','approved','rejected') DEFAULT 'pending',
    `moderated_by` int(11) unsigned DEFAULT NULL,
    `moderated_at` timestamp NULL DEFAULT NULL,
    `moderation_note` text DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` text DEFAULT NULL,
    `is_featured` tinyint(1) DEFAULT 0,
    `display_order` int(11) DEFAULT 0,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_status` (`status`),
    KEY `idx_rating` (`rating`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_featured` (`is_featured`, `display_order`),
    KEY `idx_created` (`created_at`),
    CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_comments_moderator` FOREIGN KEY (`moderated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des réponses aux commentaires (optionnel pour le futur)
CREATE TABLE IF NOT EXISTS `comment_replies` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `comment_id` int(11) unsigned NOT NULL,
    `user_id` int(11) unsigned DEFAULT NULL,
    `user_name` varchar(100) NOT NULL,
    `user_email` varchar(255) NOT NULL,
    `reply` text NOT NULL,
    `status` enum('pending','approved','rejected') DEFAULT 'pending',
    `moderated_by` int(11) unsigned DEFAULT NULL,
    `moderated_at` timestamp NULL DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_comment` (`comment_id`),
    KEY `idx_status` (`status`),
    KEY `idx_user_id` (`user_id`),
    CONSTRAINT `fk_replies_comment` FOREIGN KEY (`comment_id`) REFERENCES `user_comments` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_replies_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_replies_moderator` FOREIGN KEY (`moderated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des votes/likes sur les commentaires (optionnel)
CREATE TABLE IF NOT EXISTS `comment_votes` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `comment_id` int(11) unsigned NOT NULL,
    `user_id` int(11) unsigned DEFAULT NULL,
    `ip_address` varchar(45) NOT NULL,
    `vote_type` enum('like','dislike') NOT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_vote` (`comment_id`, `user_id`, `ip_address`),
    KEY `idx_comment` (`comment_id`),
    KEY `idx_user` (`user_id`),
    CONSTRAINT `fk_votes_comment` FOREIGN KEY (`comment_id`) REFERENCES `user_comments` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_votes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index pour optimiser les performances
CREATE INDEX idx_comments_approved_featured ON user_comments(status, is_featured, display_order, created_at);
CREATE INDEX idx_comments_user_email ON user_comments(user_email);

-- Vue pour les statistiques des commentaires
CREATE VIEW comment_stats AS
SELECT 
    COUNT(*) as total_comments,
    COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_comments,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_comments,
    COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_comments,
    ROUND(AVG(CASE WHEN status = 'approved' THEN rating END), 1) as average_rating,
    COUNT(CASE WHEN status = 'approved' AND rating = 5 THEN 1 END) as five_star_count,
    COUNT(CASE WHEN status = 'approved' AND rating = 4 THEN 1 END) as four_star_count,
    COUNT(CASE WHEN status = 'approved' AND rating = 3 THEN 1 END) as three_star_count,
    COUNT(CASE WHEN status = 'approved' AND rating = 2 THEN 1 END) as two_star_count,
    COUNT(CASE WHEN status = 'approved' AND rating = 1 THEN 1 END) as one_star_count
FROM user_comments;

-- Quelques commentaires d'exemple pour démarrer (à supprimer en production)
INSERT INTO `user_comments` (`user_name`, `user_email`, `user_activity`, `user_location`, `rating`, `comment`, `status`, `created_at`) VALUES
('Marie Dubois', 'marie.d@email.com', 'Consultante en marketing', 'Lyon, France', 5, 'ProActiv m\'a vraiment simplifié la gestion de mon activité d\'auto-entrepreneur. Les calculateurs sont précis et l\'interface est très intuitive.', 'approved', '2024-12-15 14:30:00'),
('Thomas Martin', 'thomas.m@email.com', 'Développeur web', 'Paris, France', 5, 'Excellent outil pour calculer mes charges sociales. Je recommande vivement, surtout pour le prix de l\'abonnement Standard.', 'approved', '2024-12-14 09:15:00'),
('Sophie Leroy', 'sophie.l@email.com', 'Graphiste freelance', 'Marseille, France', 4, 'Très pratique pour générer mes factures. Le forum est également une excellente ressource pour échanger avec d\'autres auto-entrepreneurs.', 'approved', '2024-12-13 16:45:00'),
('Pierre Durand', 'pierre.d@email.com', 'Formateur indépendant', 'Toulouse, France', 5, 'Interface moderne et fonctionnalités complètes. ProActiv couvre tous mes besoins d\'auto-entrepreneur.', 'approved', '2024-12-12 11:20:00'),
('Camille Rousseau', 'camille.r@email.com', 'Photographe', 'Nantes, France', 4, 'Bon rapport qualité-prix. Les calculateurs m\'aident beaucoup pour anticiper mes charges et optimiser mes revenus.', 'approved', '2024-12-11 13:10:00');

