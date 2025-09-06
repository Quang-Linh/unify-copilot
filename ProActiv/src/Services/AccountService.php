<?php

class AccountService {
    private $subscriptionService;
    
    public function __construct() {
        $this->subscriptionService = new SubscriptionService();
    }
    
    /**
     * Vérifie si un compte a expiré
     */
    public function isAccountExpired($userId) {
        $subscription = $this->subscriptionService->getUserSubscription($userId);
        
        if (!$subscription || $subscription['plan_slug'] === 'standard') {
            return false; // Les comptes standard n'expirent pas
        }
        
        if ($subscription['plan_slug'] === 'community' && $subscription['expires_at']) {
            return strtotime($subscription['expires_at']) < time();
        }
        
        return false;
    }
    
    /**
     * Obtient le nombre de jours restants avant expiration
     */
    public function getDaysUntilExpiration($userId) {
        $subscription = $this->subscriptionService->getUserSubscription($userId);
        
        if (!$subscription || !$subscription['expires_at']) {
            return null; // Pas d'expiration
        }
        
        $expirationTime = strtotime($subscription['expires_at']);
        $currentTime = time();
        
        if ($expirationTime <= $currentTime) {
            return 0; // Déjà expiré
        }
        
        return ceil(($expirationTime - $currentTime) / (24 * 60 * 60));
    }
    
    /**
     * Suspend un compte
     */
    public function suspendAccount($userId, $reason = '') {
        // En mode démo, simulation
        return [
            'success' => true,
            'message' => 'Compte suspendu avec succès',
            'suspended_at' => date('Y-m-d H:i:s'),
            'reason' => $reason
        ];
    }
    
    /**
     * Réactive un compte suspendu
     */
    public function reactivateAccount($userId) {
        // En mode démo, simulation
        return [
            'success' => true,
            'message' => 'Compte réactivé avec succès',
            'reactivated_at' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Prolonge la durée de vie d'un compte
     */
    public function extendAccount($userId, $days) {
        $subscription = $this->subscriptionService->getUserSubscription($userId);
        
        if (!$subscription) {
            return ['success' => false, 'error' => 'Abonnement non trouvé'];
        }
        
        $currentExpiration = $subscription['expires_at'] ? strtotime($subscription['expires_at']) : time();
        $newExpiration = $currentExpiration + ($days * 24 * 60 * 60);
        
        // En mode démo, simulation
        return [
            'success' => true,
            'message' => "Compte prolongé de {$days} jours",
            'new_expiration' => date('Y-m-d H:i:s', $newExpiration),
            'days_added' => $days
        ];
    }
    
    /**
     * Force le changement de mot de passe
     */
    public function forcePasswordChange($userId, $newPassword) {
        // Validation du mot de passe
        if (strlen($newPassword) < 6) {
            return ['success' => false, 'error' => 'Le mot de passe doit contenir au moins 6 caractères'];
        }
        
        // En mode démo, simulation
        return [
            'success' => true,
            'message' => 'Mot de passe changé avec succès',
            'changed_at' => date('Y-m-d H:i:s'),
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT)
        ];
    }
    
    /**
     * Obtient les comptes expirant bientôt
     */
    public function getExpiringAccounts($days = 7) {
        $expiringAccounts = [];
        
        // Simulation de comptes expirant bientôt
        $users = $this->generateExpiringUsersData($days);
        
        foreach ($users as $user) {
            $subscription = $this->subscriptionService->getUserSubscription($user['id']);
            
            if ($subscription && $subscription['expires_at']) {
                $expirationTime = strtotime($subscription['expires_at']);
                $daysUntilExpiration = ceil(($expirationTime - time()) / (24 * 60 * 60));
                
                if ($daysUntilExpiration <= $days && $daysUntilExpiration > 0) {
                    $expiringAccounts[] = [
                        'user' => $user,
                        'subscription' => $subscription,
                        'days_until_expiration' => $daysUntilExpiration,
                        'expiration_date' => date('Y-m-d', $expirationTime)
                    ];
                }
            }
        }
        
        return $expiringAccounts;
    }
    
    /**
     * Envoie des notifications d'expiration
     */
    public function sendExpirationNotifications() {
        $expiringAccounts = $this->getExpiringAccounts(7);
        $notificationsSent = 0;
        
        foreach ($expiringAccounts as $account) {
            $days = $account['days_until_expiration'];
            $email = $account['user']['email'];
            
            // Simulation d'envoi d'email
            $this->sendExpirationEmail($email, $days);
            $notificationsSent++;
        }
        
        return [
            'success' => true,
            'notifications_sent' => $notificationsSent,
            'accounts_notified' => count($expiringAccounts)
        ];
    }
    
    /**
     * Désactive automatiquement les comptes expirés
     */
    public function deactivateExpiredAccounts() {
        $deactivatedCount = 0;
        
        // Simulation de désactivation des comptes expirés
        $users = $this->generateExpiringUsersData(0); // Comptes déjà expirés
        
        foreach ($users as $user) {
            if ($this->isAccountExpired($user['id'])) {
                $this->suspendAccount($user['id'], 'Expiration automatique');
                $deactivatedCount++;
            }
        }
        
        return [
            'success' => true,
            'deactivated_count' => $deactivatedCount,
            'processed_at' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Obtient les statistiques des comptes
     */
    public function getAccountStats() {
        return [
            'total_accounts' => 1247,
            'active_accounts' => 1156,
            'suspended_accounts' => 91,
            'community_accounts' => 1089,
            'standard_accounts' => 158,
            'expiring_soon' => count($this->getExpiringAccounts(7)),
            'expired_today' => 3,
            'new_today' => 12,
            'revenue_this_month' => 790, // 158 * 5€
            'average_account_age' => 45 // jours
        ];
    }
    
    /**
     * Génère des données d'utilisateurs pour les tests
     */
    private function generateExpiringUsersData($maxDays) {
        $users = [];
        $firstNames = ['Marie', 'Jean', 'Sophie', 'Pierre', 'Camille'];
        $lastNames = ['Martin', 'Bernard', 'Dubois', 'Thomas', 'Robert'];
        
        for ($i = 1; $i <= 20; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            
            $users[] = [
                'id' => $i,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => strtolower($firstName . '.' . $lastName . $i . '@example.com'),
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'))
            ];
        }
        
        return $users;
    }
    
    /**
     * Simule l'envoi d'un email d'expiration
     */
    private function sendExpirationEmail($email, $days) {
        // En production, ici on enverrait un vrai email
        error_log("Email d'expiration envoyé à {$email} - {$days} jours restants");
        return true;
    }
    
    /**
     * Obtient l'historique des actions sur un compte
     */
    public function getAccountHistory($userId) {
        // Simulation d'historique
        return [
            [
                'action' => 'account_created',
                'date' => date('Y-m-d H:i:s', strtotime('-30 days')),
                'details' => 'Création du compte communautaire'
            ],
            [
                'action' => 'login',
                'date' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'details' => 'Connexion depuis 192.168.1.1'
            ],
            [
                'action' => 'document_generated',
                'date' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'details' => 'Génération de facture #001'
            ]
        ];
    }
}

