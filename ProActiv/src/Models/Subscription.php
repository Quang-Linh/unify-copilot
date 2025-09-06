<?php
/**
 * Modèle Subscription - Gestion des abonnements ProActiv
 */

class Subscription
{
    private $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    /**
     * Crée un nouvel abonnement pour un utilisateur
     */
    public function create($userId, $planId, $paymentMethod = 'free')
    {
        if (!$this->db) {
            return ['success' => false, 'error' => 'Base de données non disponible'];
        }
        
        try {
            // Récupérer les détails du plan
            $plan = $this->getPlan($planId);
            if (!$plan) {
                return ['success' => false, 'error' => 'Plan non trouvé'];
            }
            
            // Calculer les dates
            $startsAt = date('Y-m-d H:i:s');
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 month'));
            $trialEndsAt = null;
            $status = 'active';
            
            // Si c'est un plan gratuit avec période d'essai
            if ($plan['price'] == 0 && $plan['trial_days'] > 0) {
                $expiresAt = date('Y-m-d H:i:s', strtotime('+' . $plan['trial_days'] . ' days'));
                $trialEndsAt = $expiresAt;
                $status = 'trial';
            }
            
            // Désactiver les anciens abonnements
            $this->deactivateUserSubscriptions($userId);
            
            // Créer le nouvel abonnement
            $stmt = $this->db->prepare("
                INSERT INTO user_subscriptions (
                    user_id, plan_id, status, starts_at, expires_at, 
                    trial_ends_at, payment_method
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $userId, $planId, $status, $startsAt, 
                $expiresAt, $trialEndsAt, $paymentMethod
            ]);
            
            if ($result) {
                $subscriptionId = $this->db->lastInsertId();
                return [
                    'success' => true,
                    'subscription_id' => $subscriptionId,
                    'expires_at' => $expiresAt,
                    'status' => $status
                ];
            }
            
            return ['success' => false, 'error' => 'Erreur lors de la création de l\'abonnement'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Erreur base de données: ' . $e->getMessage()];
        }
    }
    
    /**
     * Récupère l'abonnement actuel d'un utilisateur
     */
    public function getUserSubscription($userId)
    {
        if (!$this->db) {
            // Mode sans base de données - abonnement communautaire par défaut
            return [
                'id' => 1,
                'user_id' => $userId,
                'plan_id' => 1,
                'plan_name' => 'Communautaire',
                'plan_slug' => 'community',
                'status' => 'trial',
                'price' => 0.00,
                'max_users' => 1,
                'max_concurrent_sessions' => 1,
                'features' => [
                    'calculators' => 'basic',
                    'documents' => false,
                    'forum' => 'read_only',
                    'support' => false
                ],
                'starts_at' => date('Y-m-d H:i:s'),
                'expires_at' => date('Y-m-d H:i:s', strtotime('+30 days')),
                'trial_ends_at' => date('Y-m-d H:i:s', strtotime('+30 days')),
                'days_remaining' => 30,
                'is_trial' => true,
                'auto_renew' => false
            ];
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    us.*,
                    sp.name as plan_name,
                    sp.slug as plan_slug,
                    sp.price,
                    sp.max_users,
                    sp.max_concurrent_sessions,
                    sp.features,
                    DATEDIFF(us.expires_at, NOW()) as days_remaining
                FROM user_subscriptions us
                JOIN subscription_plans sp ON us.plan_id = sp.id
                WHERE us.user_id = ? AND us.status IN ('active', 'trial')
                ORDER BY us.created_at DESC
                LIMIT 1
            ");
            
            $stmt->execute([$userId]);
            $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($subscription) {
                $subscription['features'] = json_decode($subscription['features'], true);
                $subscription['is_trial'] = ($subscription['status'] === 'trial');
                return $subscription;
            }
            
            // Pas d'abonnement trouvé, créer un abonnement communautaire
            $communityPlan = $this->getPlanBySlug('community');
            if ($communityPlan) {
                $result = $this->create($userId, $communityPlan['id']);
                if ($result['success']) {
                    return $this->getUserSubscription($userId);
                }
            }
            
            return null;
            
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Récupère un plan par ID
     */
    public function getPlan($planId)
    {
        if (!$this->db) {
            $plans = [
                1 => [
                    'id' => 1,
                    'name' => 'Communautaire',
                    'slug' => 'community',
                    'price' => 0.00,
                    'max_users' => 1,
                    'max_concurrent_sessions' => 1,
                    'trial_days' => 30,
                    'features' => [
                        'calculators' => 'basic',
                        'documents' => false,
                        'forum' => 'read_only',
                        'support' => false
                    ]
                ],
                2 => [
                    'id' => 2,
                    'name' => 'Standard',
                    'slug' => 'standard',
                    'price' => 5.00,
                    'max_users' => 2,
                    'max_concurrent_sessions' => 2,
                    'trial_days' => 0,
                    'features' => [
                        'calculators' => 'full',
                        'documents' => true,
                        'forum' => 'full',
                        'support' => 'email'
                    ]
                ]
            ];
            
            if (isset($plans[$planId])) {
                return $plans[$planId];
            }
            
            return null;
        }
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM subscription_plans WHERE id = ? AND is_active = 1");
            $stmt->execute([$planId]);
            $plan = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($plan) {
                $plan['features'] = json_decode($plan['features'], true);
            }
            
            return $plan;
            
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Récupère un plan par slug
     */
    public function getPlanBySlug($slug)
    {
        if (!$this->db) {
            if ($slug === 'community') {
                return $this->getPlan(1);
            } elseif ($slug === 'standard') {
                return $this->getPlan(2);
            }
            return null;
        }
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM subscription_plans WHERE slug = ? AND is_active = 1");
            $stmt->execute([$slug]);
            $plan = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($plan) {
                $plan['features'] = json_decode($plan['features'], true);
            }
            
            return $plan;
            
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Récupère tous les plans disponibles
     */
    public function getAllPlans()
    {
        if (!$this->db) {
            return [
                $this->getPlan(1),
                $this->getPlan(2)
            ];
        }
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM subscription_plans WHERE is_active = 1 ORDER BY price ASC");
            $stmt->execute();
            $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($plans as &$plan) {
                $plan['features'] = json_decode($plan['features'], true);
            }
            
            return $plans;
            
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Désactive tous les abonnements actifs d'un utilisateur
     */
    private function deactivateUserSubscriptions($userId)
    {
        if (!$this->db) {
            return true;
        }
        
        try {
            $stmt = $this->db->prepare("
                UPDATE user_subscriptions 
                SET status = 'cancelled' 
                WHERE user_id = ? AND status IN ('active', 'trial')
            ");
            
            return $stmt->execute([$userId]);
            
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Renouvelle un abonnement
     */
    public function renew($subscriptionId, $paymentReference = null)
    {
        if (!$this->db) {
            return ['success' => false, 'error' => 'Fonctionnalité non disponible'];
        }
        
        try {
            // Récupérer l'abonnement actuel
            $stmt = $this->db->prepare("
                SELECT us.*, sp.billing_cycle 
                FROM user_subscriptions us
                JOIN subscription_plans sp ON us.plan_id = sp.id
                WHERE us.id = ?
            ");
            
            $stmt->execute([$subscriptionId]);
            $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$subscription) {
                return ['success' => false, 'error' => 'Abonnement non trouvé'];
            }
            
            // Calculer la nouvelle date d'expiration
            $cycle = $subscription['billing_cycle'] === 'yearly' ? '+1 year' : '+1 month';
            $newExpiresAt = date('Y-m-d H:i:s', strtotime($cycle));
            
            // Mettre à jour l'abonnement
            $stmt = $this->db->prepare("
                UPDATE user_subscriptions 
                SET expires_at = ?, status = 'active', payment_reference = ?
                WHERE id = ?
            ");
            
            $result = $stmt->execute([$newExpiresAt, $paymentReference, $subscriptionId]);
            
            return [
                'success' => $result,
                'new_expires_at' => $newExpiresAt
            ];
            
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Erreur base de données'];
        }
    }
    
    /**
     * Annule un abonnement
     */
    public function cancel($subscriptionId, $reason = '')
    {
        if (!$this->db) {
            return ['success' => false, 'error' => 'Fonctionnalité non disponible'];
        }
        
        try {
            $stmt = $this->db->prepare("
                UPDATE user_subscriptions 
                SET status = 'cancelled', auto_renew = 0
                WHERE id = ?
            ");
            
            $result = $stmt->execute([$subscriptionId]);
            
            return ['success' => $result];
            
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Erreur base de données'];
        }
    }
    
    /**
     * Expire les abonnements échus
     */
    public function expireOverdueSubscriptions()
    {
        if (!$this->db) {
            return ['success' => false, 'expired_count' => 0];
        }
        
        try {
            $stmt = $this->db->prepare("
                UPDATE user_subscriptions 
                SET status = 'expired' 
                WHERE status IN ('active', 'trial') 
                AND expires_at < NOW()
            ");
            
            $result = $stmt->execute();
            $expiredCount = $stmt->rowCount();
            
            return [
                'success' => $result,
                'expired_count' => $expiredCount
            ];
            
        } catch (PDOException $e) {
            return ['success' => false, 'expired_count' => 0];
        }
    }
    
    /**
     * Récupère les abonnements expirant bientôt
     */
    public function getExpiringSubscriptions($days = 7)
    {
        if (!$this->db) {
            return [];
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    us.*,
                    u.email,
                    u.first_name,
                    u.last_name,
                    sp.name as plan_name,
                    DATEDIFF(us.expires_at, NOW()) as days_remaining
                FROM user_subscriptions us
                JOIN users u ON us.user_id = u.id
                JOIN subscription_plans sp ON us.plan_id = sp.id
                WHERE us.status IN ('active', 'trial')
                AND us.expires_at <= DATE_ADD(NOW(), INTERVAL ? DAY)
                AND us.expires_at > NOW()
                ORDER BY us.expires_at ASC
            ");
            
            $stmt->execute([$days]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Statistiques des abonnements
     */
    public function getStats()
    {
        if (!$this->db) {
            return [
                'total_subscriptions' => 2,
                'active_subscriptions' => 2,
                'trial_subscriptions' => 1,
                'expired_subscriptions' => 0,
                'monthly_revenue' => 5.00,
                'by_plan' => [
                    'community' => 1,
                    'standard' => 1
                ]
            ];
        }
        
        try {
            // Statistiques générales
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN status = 'trial' THEN 1 ELSE 0 END) as trial,
                    SUM(CASE WHEN status = 'expired' THEN 1 ELSE 0 END) as expired
                FROM user_subscriptions
            ");
            
            $stmt->execute();
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Revenus mensuels
            $stmt = $this->db->prepare("
                SELECT SUM(sp.price) as monthly_revenue
                FROM user_subscriptions us
                JOIN subscription_plans sp ON us.plan_id = sp.id
                WHERE us.status = 'active' AND sp.billing_cycle = 'monthly'
            ");
            
            $stmt->execute();
            $revenue = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Répartition par plan
            $stmt = $this->db->prepare("
                SELECT sp.slug, sp.name, COUNT(*) as count
                FROM user_subscriptions us
                JOIN subscription_plans sp ON us.plan_id = sp.id
                WHERE us.status IN ('active', 'trial')
                GROUP BY sp.id, sp.slug, sp.name
            ");
            
            $stmt->execute();
            $byPlan = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'total_subscriptions' => (int)$stats['total'],
                'active_subscriptions' => (int)$stats['active'],
                'trial_subscriptions' => (int)$stats['trial'],
                'expired_subscriptions' => (int)$stats['expired'],
                'monthly_revenue' => (float)($revenue['monthly_revenue'] ?? 0),
                'by_plan' => $byPlan
            ];
            
        } catch (PDOException $e) {
            return [
                'total_subscriptions' => 0,
                'active_subscriptions' => 0,
                'trial_subscriptions' => 0,
                'expired_subscriptions' => 0,
                'monthly_revenue' => 0.00,
                'by_plan' => []
            ];
        }
    }
    
    /**
     * Upgrade/downgrade d'un abonnement
     */
    public function changePlan($subscriptionId, $newPlanId, $paymentReference = null)
    {
        if (!$this->db) {
            return ['success' => false, 'error' => 'Fonctionnalité non disponible'];
        }
        
        try {
            // Vérifier que le nouveau plan existe
            $newPlan = $this->getPlan($newPlanId);
            if (!$newPlan) {
                return ['success' => false, 'error' => 'Nouveau plan non trouvé'];
            }
            
            // Mettre à jour l'abonnement
            $stmt = $this->db->prepare("
                UPDATE user_subscriptions 
                SET plan_id = ?, payment_reference = ?, updated_at = NOW()
                WHERE id = ?
            ");
            
            $result = $stmt->execute([$newPlanId, $paymentReference, $subscriptionId]);
            
            return ['success' => $result];
            
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Erreur base de données'];
        }
    }
}
?>

