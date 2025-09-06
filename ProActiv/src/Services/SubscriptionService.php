<?php
/**
 * Service de gestion des abonnements ProActiv
 */

class SubscriptionService
{
    private $db;
    private $config;
    
    public function __construct($db, $config)
    {
        $this->db = $db;
        $this->config = $config;
    }
    
    /**
     * Récupère l'abonnement actuel d'un utilisateur
     */
    public function getUserSubscription($userId)
    {
        if (!$this->db) {
            // Mode sans base de données - retourne un abonnement communautaire par défaut
            return [
                'plan_slug' => 'community',
                'plan_name' => 'Communautaire',
                'status' => 'active',
                'expires_at' => date('Y-m-d H:i:s', strtotime('+30 days')),
                'is_trial' => true,
                'max_users' => 1,
                'max_concurrent_sessions' => 1,
                'features' => [
                    'calculators' => 'basic',
                    'documents' => false,
                    'forum' => 'read_only',
                    'support' => false
                ]
            ];
        }
        
        $stmt = $this->db->prepare("
            SELECT 
                us.*,
                sp.name as plan_name,
                sp.slug as plan_slug,
                sp.max_users,
                sp.max_concurrent_sessions,
                sp.features,
                sp.price
            FROM user_subscriptions us
            JOIN subscription_plans sp ON us.plan_id = sp.id
            WHERE us.user_id = ? AND us.status IN ('active', 'trial')
            ORDER BY us.created_at DESC
            LIMIT 1
        ");
        
        $stmt->execute([$userId]);
        $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$subscription) {
            // Pas d'abonnement trouvé, créer un abonnement communautaire
            return $this->createCommunitySubscription($userId);
        }
        
        // Vérifier si l'abonnement est expiré
        if (strtotime($subscription['expires_at']) < time()) {
            $this->expireSubscription($subscription['id']);
            return $this->createCommunitySubscription($userId);
        }
        
        $subscription['features'] = json_decode($subscription['features'], true);
        $subscription['is_trial'] = ($subscription['status'] === 'trial');
        
        return $subscription;
    }
    
    /**
     * Crée un abonnement communautaire par défaut
     */
    private function createCommunitySubscription($userId)
    {
        if (!$this->db) {
            return [
                'plan_slug' => 'community',
                'plan_name' => 'Communautaire',
                'status' => 'trial',
                'expires_at' => date('Y-m-d H:i:s', strtotime('+30 days')),
                'is_trial' => true,
                'max_users' => 1,
                'max_concurrent_sessions' => 1,
                'features' => [
                    'calculators' => 'basic',
                    'documents' => false,
                    'forum' => 'read_only',
                    'support' => false
                ]
            ];
        }
        
        // Récupérer le plan communautaire
        $stmt = $this->db->prepare("SELECT * FROM subscription_plans WHERE slug = 'community'");
        $stmt->execute();
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$plan) {
            throw new Exception("Plan communautaire non trouvé");
        }
        
        // Créer l'abonnement
        $expiresAt = date('Y-m-d H:i:s', strtotime('+' . $plan['trial_days'] . ' days'));
        
        $stmt = $this->db->prepare("
            INSERT INTO user_subscriptions (user_id, plan_id, status, starts_at, expires_at, trial_ends_at)
            VALUES (?, ?, 'trial', NOW(), ?, ?)
        ");
        
        $stmt->execute([$userId, $plan['id'], $expiresAt, $expiresAt]);
        
        return [
            'plan_slug' => $plan['slug'],
            'plan_name' => $plan['name'],
            'status' => 'trial',
            'expires_at' => $expiresAt,
            'is_trial' => true,
            'max_users' => $plan['max_users'],
            'max_concurrent_sessions' => $plan['max_concurrent_sessions'],
            'features' => json_decode($plan['features'], true)
        ];
    }
    
    /**
     * Vérifie si un utilisateur peut accéder à une fonctionnalité
     */
    public function canAccess($userId, $feature)
    {
        $subscription = $this->getUserSubscription($userId);
        
        switch ($feature) {
            case 'documents':
                return $subscription['features']['documents'] === true;
                
            case 'forum_write':
                return $subscription['features']['forum'] === 'full';
                
            case 'advanced_calculators':
                return $subscription['features']['calculators'] === 'full';
                
            case 'support':
                return $subscription['features']['support'] !== false;
                
            default:
                return true; // Accès par défaut
        }
    }
    
    /**
     * Récupère le thème CSS selon l'abonnement
     */
    public function getTheme($userId)
    {
        $subscription = $this->getUserSubscription($userId);
        
        return [
            'name' => $subscription['plan_slug'],
            'css_class' => 'theme-' . $subscription['plan_slug'],
            'primary_color' => $subscription['plan_slug'] === 'community' ? '#ff6b35' : '#007bff',
            'show_banner' => $subscription['plan_slug'] === 'community',
            'banner_text' => $subscription['plan_slug'] === 'community' ? 'You are in a Community version' : ''
        ];
    }
    
    /**
     * Compte les sessions actives d'un utilisateur
     */
    public function getActiveSessionsCount($userId)
    {
        if (!$this->db) {
            return 1; // Mode sans BDD
        }
        
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM active_sessions 
            WHERE user_id = ? AND expires_at > NOW()
        ");
        
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int)$result['count'];
    }
    
    /**
     * Vérifie si un utilisateur peut se connecter (limite de sessions)
     */
    public function canLogin($userId)
    {
        $subscription = $this->getUserSubscription($userId);
        $activeSessions = $this->getActiveSessionsCount($userId);
        
        return $activeSessions < $subscription['max_concurrent_sessions'];
    }
    
    /**
     * Enregistre une nouvelle session
     */
    public function registerSession($userId, $sessionId, $ipAddress, $userAgent)
    {
        if (!$this->db) {
            return true;
        }
        
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $stmt = $this->db->prepare("
            INSERT INTO active_sessions (user_id, session_id, ip_address, user_agent, expires_at)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                last_activity = NOW(),
                expires_at = ?
        ");
        
        return $stmt->execute([$userId, $sessionId, $ipAddress, $userAgent, $expiresAt, $expiresAt]);
    }
    
    /**
     * Supprime une session
     */
    public function removeSession($sessionId)
    {
        if (!$this->db) {
            return true;
        }
        
        $stmt = $this->db->prepare("DELETE FROM active_sessions WHERE session_id = ?");
        return $stmt->execute([$sessionId]);
    }
    
    /**
     * Nettoie les sessions expirées
     */
    public function cleanExpiredSessions()
    {
        if (!$this->db) {
            return true;
        }
        
        $stmt = $this->db->prepare("DELETE FROM active_sessions WHERE expires_at < NOW()");
        return $stmt->execute();
    }
    
    /**
     * Expire un abonnement
     */
    private function expireSubscription($subscriptionId)
    {
        if (!$this->db) {
            return true;
        }
        
        $stmt = $this->db->prepare("UPDATE user_subscriptions SET status = 'expired' WHERE id = ?");
        return $stmt->execute([$subscriptionId]);
    }
    
    /**
     * Récupère tous les plans disponibles
     */
    public function getAvailablePlans()
    {
        if (!$this->db) {
            return [
                [
                    'id' => 1,
                    'name' => 'Communautaire',
                    'slug' => 'community',
                    'price' => 0.00,
                    'max_users' => 1,
                    'trial_days' => 30,
                    'features' => [
                        'calculators' => 'basic',
                        'documents' => false,
                        'forum' => 'read_only',
                        'support' => false
                    ]
                ],
                [
                    'id' => 2,
                    'name' => 'Standard',
                    'slug' => 'standard',
                    'price' => 5.00,
                    'max_users' => 2,
                    'trial_days' => 0,
                    'features' => [
                        'calculators' => 'full',
                        'documents' => true,
                        'forum' => 'full',
                        'support' => 'email'
                    ]
                ]
            ];
        }
        
        $stmt = $this->db->prepare("SELECT * FROM subscription_plans WHERE is_active = 1 ORDER BY price ASC");
        $stmt->execute();
        $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($plans as &$plan) {
            $plan['features'] = json_decode($plan['features'], true);
        }
        
        return $plans;
    }
    
    /**
     * Récupère un plan spécifique par son slug
     */
    public function getPlan($slug)
    {
        $plans = $this->getAvailablePlans();
        
        foreach ($plans as $plan) {
            if ($plan['slug'] === $slug) {
                return $plan;
            }
        }
        
        return null;
    }
}
?>

