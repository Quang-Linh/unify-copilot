-- Schéma de base de données ProActiv - Système d'abonnements
-- Adapté pour les auto-entrepreneurs

-- Table des plans d'abonnement
CREATE TABLE subscription_plans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    currency VARCHAR(3) DEFAULT 'EUR',
    billing_cycle ENUM('monthly', 'yearly') DEFAULT 'monthly',
    max_users INT NOT NULL DEFAULT 1,
    max_concurrent_sessions INT NOT NULL DEFAULT 1,
    trial_days INT DEFAULT 0,
    features JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertion des plans par défaut
INSERT INTO subscription_plans (name, slug, price, max_users, max_concurrent_sessions, trial_days, features) VALUES
('Communautaire', 'community', 0.00, 1, 1, 30, '{"calculators": "basic", "documents": false, "forum": "read_only", "support": false}'),
('Standard', 'standard', 5.00, 2, 2, 0, '{"calculators": "full", "documents": true, "forum": "full", "support": "email"}');

-- Table des utilisateurs étendue
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    company_name VARCHAR(255),
    siret VARCHAR(14),
    phone VARCHAR(20),
    role ENUM('user', 'admin', 'super_admin') DEFAULT 'user',
    status ENUM('active', 'suspended', 'expired', 'pending') DEFAULT 'pending',
    email_verified BOOLEAN DEFAULT FALSE,
    email_verification_token VARCHAR(255),
    password_reset_token VARCHAR(255),
    password_reset_expires TIMESTAMP NULL,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des abonnements utilisateurs
CREATE TABLE user_subscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    status ENUM('active', 'cancelled', 'expired', 'trial') DEFAULT 'trial',
    starts_at TIMESTAMP NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    trial_ends_at TIMESTAMP NULL,
    auto_renew BOOLEAN DEFAULT TRUE,
    payment_method ENUM('card', 'paypal', 'bank_transfer', 'free') DEFAULT 'free',
    payment_reference VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(id)
);

-- Table des sessions actives (pour contrôler les connexions simultanées)
CREATE TABLE active_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_id VARCHAR(255) UNIQUE NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des paiements
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    subscription_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'EUR',
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_method ENUM('card', 'paypal', 'bank_transfer') NOT NULL,
    payment_reference VARCHAR(255),
    gateway_transaction_id VARCHAR(255),
    gateway_response JSON,
    processed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subscription_id) REFERENCES user_subscriptions(id) ON DELETE CASCADE
);

-- Table des notifications d'expiration
CREATE TABLE expiration_notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    subscription_id INT NOT NULL,
    notification_type ENUM('7_days', '3_days', '1_day', 'expired') NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subscription_id) REFERENCES user_subscriptions(id) ON DELETE CASCADE
);

-- Table des logs d'audit
CREATE TABLE audit_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    admin_id INT,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Index pour optimiser les performances
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_status ON users(status);
CREATE INDEX idx_subscriptions_user_status ON user_subscriptions(user_id, status);
CREATE INDEX idx_subscriptions_expires ON user_subscriptions(expires_at);
CREATE INDEX idx_sessions_user ON active_sessions(user_id);
CREATE INDEX idx_sessions_expires ON active_sessions(expires_at);
CREATE INDEX idx_payments_user ON payments(user_id);
CREATE INDEX idx_audit_user ON audit_logs(user_id);
CREATE INDEX idx_audit_created ON audit_logs(created_at);

-- Vues utiles pour l'administration
CREATE VIEW active_users AS
SELECT 
    u.id,
    u.email,
    u.first_name,
    u.last_name,
    u.company_name,
    u.status as user_status,
    sp.name as plan_name,
    us.status as subscription_status,
    us.expires_at,
    DATEDIFF(us.expires_at, NOW()) as days_remaining,
    COUNT(ase.id) as active_sessions
FROM users u
LEFT JOIN user_subscriptions us ON u.id = us.user_id AND us.status = 'active'
LEFT JOIN subscription_plans sp ON us.plan_id = sp.id
LEFT JOIN active_sessions ase ON u.id = ase.user_id AND ase.expires_at > NOW()
WHERE u.status IN ('active', 'trial')
GROUP BY u.id, us.id, sp.id;

-- Vue des abonnements expirant bientôt
CREATE VIEW expiring_subscriptions AS
SELECT 
    u.id as user_id,
    u.email,
    u.first_name,
    u.last_name,
    sp.name as plan_name,
    us.expires_at,
    DATEDIFF(us.expires_at, NOW()) as days_remaining
FROM users u
JOIN user_subscriptions us ON u.id = us.user_id
JOIN subscription_plans sp ON us.plan_id = sp.id
WHERE us.status = 'active' 
AND us.expires_at <= DATE_ADD(NOW(), INTERVAL 7 DAY)
ORDER BY us.expires_at ASC;

