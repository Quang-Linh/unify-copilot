<?php
/**
 * SuperAdminController - Backoffice d'administration ProActiv
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Subscription.php';

class SuperAdminController extends BaseController
{
    private $userModel;
    private $subscriptionModel;
    
    public function __construct($config)
    {
        parent::__construct($config);
        
        $db = $this->config['database']['enabled'] ? $this->getDatabase() : null;
        $this->userModel = new User($db);
        $this->subscriptionModel = new Subscription($db);
        
        // Vérifier les droits super admin
        $this->requireSuperAdmin();
    }
    
    /**
     * Tableau de bord super admin
     */
    public function index()
    {
        $stats = $this->getDashboardStats();
        
        $this->render('superadmin/dashboard', [
            'title' => 'Super Administration - ProActiv',
            'stats' => $stats,
            'recent_users' => $this->getRecentUsers(),
            'subscription_stats' => $this->subscriptionModel->getStats(),
            'expiring_subscriptions' => $this->subscriptionModel->getExpiringSubscriptions(7)
        ]);
    }
    
    /**
     * Gestion des utilisateurs
     */
    public function users()
    {
        $page = (int)($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        $limit = 25;
        $offset = ($page - 1) * $limit;
        
        $users = $this->userModel->getAll($limit, $offset, $search);
        $totalUsers = $this->userModel->count($search);
        $totalPages = ceil($totalUsers / $limit);
        
        // Enrichir avec les données d'abonnement
        foreach ($users as &$user) {
            $subscription = $this->subscriptionModel->getUserSubscription($user['id']);
            $user['subscription'] = $subscription;
        }
        
        $this->render('superadmin/users', [
            'title' => 'Gestion des utilisateurs - Super Admin',
            'users' => $users,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalUsers,
                'search' => $search
            ]
        ]);
    }
    
    /**
     * Détails d'un utilisateur
     */
    public function userDetails($userId)
    {
        $user = $this->userModel->findById($userId);
        if (!$user) {
            $this->redirect('/superadmin/users?error=user_not_found');
            return;
        }
        
        $subscription = $this->subscriptionModel->getUserSubscription($userId);
        $availablePlans = $this->subscriptionModel->getAllPlans();
        
        $this->render('superadmin/user_details', [
            'title' => 'Détails utilisateur - ' . $user['email'],
            'user' => $user,
            'subscription' => $subscription,
            'available_plans' => $availablePlans
        ]);
    }
    
    /**
     * Suspend/Active un utilisateur
     */
    public function toggleUserStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Méthode non autorisée']);
            return;
        }
        
        $userId = $_POST['user_id'] ?? null;
        $newStatus = $_POST['status'] ?? null;
        
        if (!$userId || !$newStatus) {
            $this->jsonResponse(['success' => false, 'error' => 'Paramètres manquants']);
            return;
        }
        
        $result = $this->userModel->updateStatus($userId, $newStatus);
        
        if ($result['success']) {
            $this->jsonResponse([
                'success' => true,
                'message' => 'Statut utilisateur mis à jour'
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'error' => $result['error'] ?? 'Erreur inconnue'
            ]);
        }
    }
    
    /**
     * Force le changement de mot de passe
     */
    public function forcePasswordChange()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Méthode non autorisée']);
            return;
        }
        
        $userId = $_POST['user_id'] ?? null;
        $newPassword = $_POST['new_password'] ?? null;
        
        if (!$userId || !$newPassword) {
            $this->jsonResponse(['success' => false, 'error' => 'Paramètres manquants']);
            return;
        }
        
        if (strlen($newPassword) < 6) {
            $this->jsonResponse(['success' => false, 'error' => 'Le mot de passe doit contenir au moins 6 caractères']);
            return;
        }
        
        $result = $this->userModel->forcePasswordChange($userId, $newPassword);
        
        if ($result['success']) {
            $this->jsonResponse([
                'success' => true,
                'message' => 'Mot de passe modifié avec succès'
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'error' => $result['error'] ?? 'Erreur inconnue'
            ]);
        }
    }
    
    /**
     * Gestion des abonnements
     */
    public function subscriptions()
    {
        $stats = $this->subscriptionModel->getStats();
        $expiringSubscriptions = $this->subscriptionModel->getExpiringSubscriptions(30);
        $availablePlans = $this->subscriptionModel->getAllPlans();
        
        $this->render('superadmin/subscriptions', [
            'title' => 'Gestion des abonnements - Super Admin',
            'stats' => $stats,
            'expiring_subscriptions' => $expiringSubscriptions,
            'available_plans' => $availablePlans
        ]);
    }
    
    /**
     * Change le plan d'un utilisateur
     */
    public function changeUserPlan()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Méthode non autorisée']);
            return;
        }
        
        $userId = $_POST['user_id'] ?? null;
        $newPlanId = $_POST['plan_id'] ?? null;
        
        if (!$userId || !$newPlanId) {
            $this->jsonResponse(['success' => false, 'error' => 'Paramètres manquants']);
            return;
        }
        
        // Récupérer l'abonnement actuel
        $currentSubscription = $this->subscriptionModel->getUserSubscription($userId);
        
        if ($currentSubscription) {
            // Changer le plan existant
            $result = $this->subscriptionModel->changePlan($currentSubscription['id'], $newPlanId, 'admin_change');
        } else {
            // Créer un nouvel abonnement
            $result = $this->subscriptionModel->create($userId, $newPlanId, 'admin_assign');
        }
        
        if ($result['success']) {
            $this->jsonResponse([
                'success' => true,
                'message' => 'Plan d\'abonnement modifié avec succès'
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'error' => $result['error'] ?? 'Erreur inconnue'
            ]);
        }
    }
    
    /**
     * Prolonge un abonnement
     */
    public function extendSubscription()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Méthode non autorisée']);
            return;
        }
        
        $subscriptionId = $_POST['subscription_id'] ?? null;
        $extensionDays = (int)($_POST['extension_days'] ?? 30);
        
        if (!$subscriptionId) {
            $this->jsonResponse(['success' => false, 'error' => 'ID d\'abonnement manquant']);
            return;
        }
        
        // Pour l'instant, on utilise la fonction de renouvellement
        $result = $this->subscriptionModel->renew($subscriptionId, 'admin_extension');
        
        if ($result['success']) {
            $this->jsonResponse([
                'success' => true,
                'message' => 'Abonnement prolongé avec succès',
                'new_expires_at' => $result['new_expires_at']
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'error' => $result['error'] ?? 'Erreur inconnue'
            ]);
        }
    }
    
    /**
     * Gestion des plans d'abonnement
     */
    public function plans()
    {
        $plans = $this->subscriptionModel->getAllPlans();
        
        $this->render('superadmin/plans', [
            'title' => 'Gestion des plans - Super Admin',
            'plans' => $plans
        ]);
    }
    
    /**
     * Statistiques et analytics
     */
    public function analytics()
    {
        $stats = $this->getAnalyticsData();
        
        $this->render('superadmin/analytics', [
            'title' => 'Analytics - Super Admin',
            'stats' => $stats
        ]);
    }
    
    /**
     * Paramètres système
     */
    public function settings()
    {
        $this->render('superadmin/settings', [
            'title' => 'Paramètres système - Super Admin',
            'config' => $this->config
        ]);
    }
    
    /**
     * Logs système
     */
    public function logs()
    {
        $logFile = $this->config['app']['log_file'] ?? '/tmp/proactiv.log';
        $logs = $this->getRecentLogs($logFile);
        
        $this->render('superadmin/logs', [
            'title' => 'Logs système - Super Admin',
            'logs' => $logs
        ]);
    }
    
    /**
     * Maintenance
     */
    public function maintenance()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'expire_subscriptions':
                    $result = $this->subscriptionModel->expireOverdueSubscriptions();
                    $this->jsonResponse([
                        'success' => true,
                        'message' => $result['expired_count'] . ' abonnements expirés'
                    ]);
                    break;
                    
                case 'clear_cache':
                    $this->clearCache();
                    $this->jsonResponse([
                        'success' => true,
                        'message' => 'Cache vidé avec succès'
                    ]);
                    break;
                    
                default:
                    $this->jsonResponse(['success' => false, 'error' => 'Action inconnue']);
            }
            return;
        }
        
        $this->render('superadmin/maintenance', [
            'title' => 'Maintenance - Super Admin'
        ]);
    }
    
    /**
     * Vérification des droits super admin
     */
    private function requireSuperAdmin()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            return;
        }
        
        $user = $this->userModel->findById($_SESSION['user_id']);
        if (!$user || $user['role'] !== 'admin') {
            $this->redirect('/dashboard?error=access_denied');
            return;
        }
    }
    
    /**
     * Statistiques du tableau de bord
     */
    private function getDashboardStats()
    {
        return [
            'total_users' => $this->userModel->count(),
            'active_users' => $this->getActiveUsersCount(),
            'total_subscriptions' => $this->subscriptionModel->getStats()['total_subscriptions'],
            'monthly_revenue' => $this->subscriptionModel->getStats()['monthly_revenue'],
            'trial_users' => $this->subscriptionModel->getStats()['trial_subscriptions'],
            'expiring_soon' => count($this->subscriptionModel->getExpiringSubscriptions(7))
        ];
    }
    
    /**
     * Utilisateurs récents
     */
    private function getRecentUsers($limit = 10)
    {
        return $this->userModel->getAll($limit, 0);
    }
    
    /**
     * Nombre d'utilisateurs actifs
     */
    private function getActiveUsersCount()
    {
        // Simulation pour le mode démo
        return $this->userModel->count() - 1;
    }
    
    /**
     * Données analytics
     */
    private function getAnalyticsData()
    {
        return [
            'user_growth' => $this->getUserGrowthData(),
            'revenue_trend' => $this->getRevenueTrendData(),
            'subscription_distribution' => $this->subscriptionModel->getStats()['by_plan'],
            'churn_rate' => $this->getChurnRate()
        ];
    }
    
    /**
     * Données de croissance des utilisateurs
     */
    private function getUserGrowthData()
    {
        // Données simulées pour le graphique
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $data[] = [
                'date' => $date,
                'users' => rand(1, 5) + $i
            ];
        }
        return $data;
    }
    
    /**
     * Données de tendance des revenus
     */
    private function getRevenueTrendData()
    {
        // Données simulées
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $data[] = [
                'date' => $date,
                'revenue' => rand(0, 50) + ($i * 5)
            ];
        }
        return $data;
    }
    
    /**
     * Taux de désabonnement
     */
    private function getChurnRate()
    {
        return 2.5; // Simulation
    }
    
    /**
     * Lecture des logs récents
     */
    private function getRecentLogs($logFile, $lines = 100)
    {
        if (!file_exists($logFile)) {
            return ['Aucun log disponible'];
        }
        
        $logs = [];
        $handle = fopen($logFile, 'r');
        
        if ($handle) {
            // Lire les dernières lignes
            $buffer = '';
            fseek($handle, -1, SEEK_END);
            
            while (ftell($handle) > 0 && count($logs) < $lines) {
                $char = fgetc($handle);
                if ($char === "\n") {
                    if (!empty(trim($buffer))) {
                        array_unshift($logs, trim($buffer));
                    }
                    $buffer = '';
                } else {
                    $buffer = $char . $buffer;
                }
                fseek($handle, -2, SEEK_CUR);
            }
            
            fclose($handle);
        }
        
        return empty($logs) ? ['Aucun log récent'] : $logs;
    }
    
    /**
     * Vide le cache
     */
    private function clearCache()
    {
        $cacheDir = $this->config['app']['cache_dir'] ?? '/tmp/proactiv_cache';
        
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
        
        return true;
    }
    
    /**
     * Réponse JSON
     */
    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>


    
    public function users($request) {
        // Vérification des droits admin
        if (!$this->isAdmin()) {
            return $this->redirect('/ProActiv/login');
        }

        // Pagination et filtres
        $page = max(1, intval($request->getParam('page', 1)));
        $limit = 20;
        $search = $request->getParam('search', '');
        $status = $request->getParam('status', '');
        $plan = $request->getParam('plan', '');

        // Simulation des données utilisateurs avec pagination
        $allUsers = $this->generateUsersData();
        
        // Filtrage
        $filteredUsers = array_filter($allUsers, function($user) use ($search, $status, $plan) {
            $matchSearch = empty($search) || 
                stripos($user['email'], $search) !== false ||
                stripos($user['first_name'] . ' ' . $user['last_name'], $search) !== false;
            
            $matchStatus = empty($status) || $user['status'] === $status;
            
            $matchPlan = empty($plan) || 
                (isset($user['subscription']['plan_slug']) && $user['subscription']['plan_slug'] === $plan);
            
            return $matchSearch && $matchStatus && $matchPlan;
        });

        // Pagination
        $totalItems = count($filteredUsers);
        $totalPages = ceil($totalItems / $limit);
        $offset = ($page - 1) * $limit;
        $users = array_slice($filteredUsers, $offset, $limit);

        $pagination = [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalItems,
            'search' => $search
        ];

        return $this->render('superadmin/users', [
            'title' => 'Gestion des utilisateurs - Super Admin',
            'users' => $users,
            'pagination' => $pagination
        ]);
    }

    public function subscriptions($request) {
        // Vérification des droits admin
        if (!$this->isAdmin()) {
            return $this->redirect('/ProActiv/login');
        }

        // Filtres
        $filters = [
            'plan' => $request->getParam('plan', ''),
            'status' => $request->getParam('status', ''),
            'expiring' => $request->getParam('expiring', '')
        ];

        // Simulation des données d'abonnements
        $subscriptions = $this->generateSubscriptionsData($filters);
        
        // Statistiques
        $stats = [
            'community_count' => count(array_filter($subscriptions, fn($s) => $s['plan_slug'] === 'community')),
            'standard_count' => count(array_filter($subscriptions, fn($s) => $s['plan_slug'] === 'standard')),
            'monthly_revenue' => array_sum(array_map(fn($s) => $s['plan_slug'] === 'standard' ? 5 : 0, $subscriptions)),
            'expiring_soon' => count(array_filter($subscriptions, function($s) {
                return $s['expires_at'] && strtotime($s['expires_at']) < strtotime('+7 days') && strtotime($s['expires_at']) > time();
            }))
        ];

        // Données pour les graphiques
        $chartData = [
            'revenue' => [
                'labels' => ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'],
                'data' => [120, 150, 180, 220, 250, 280]
            ]
        ];

        return $this->render('superadmin/subscriptions', [
            'title' => 'Gestion des abonnements - Super Admin',
            'subscriptions' => $subscriptions,
            'stats' => $stats,
            'filters' => $filters,
            'chart_data' => $chartData
        ]);
    }

    public function toggleUserStatus($request) {
        if (!$this->isAdmin()) {
            return $this->jsonResponse(['success' => false, 'error' => 'Accès refusé']);
        }

        $userId = $request->getParam('user_id');
        $newStatus = $request->getParam('status');

        // Simulation de la mise à jour du statut
        // En production, ici on mettrait à jour la base de données
        
        return $this->jsonResponse(['success' => true]);
    }

    public function forcePassword($request) {
        if (!$this->isAdmin()) {
            return $this->jsonResponse(['success' => false, 'error' => 'Accès refusé']);
        }

        $userId = $request->getParam('user_id');
        $newPassword = $request->getParam('new_password');

        // Validation
        if (strlen($newPassword) < 6) {
            return $this->jsonResponse(['success' => false, 'error' => 'Mot de passe trop court']);
        }

        // Simulation du changement de mot de passe
        // En production, ici on hasherait et sauvegarderait le nouveau mot de passe
        
        return $this->jsonResponse(['success' => true]);
    }

    private function generateUsersData() {
        $users = [];
        $firstNames = ['Marie', 'Jean', 'Sophie', 'Pierre', 'Camille', 'Thomas', 'Julie', 'Nicolas', 'Emma', 'Lucas'];
        $lastNames = ['Martin', 'Bernard', 'Dubois', 'Thomas', 'Robert', 'Richard', 'Petit', 'Durand', 'Leroy', 'Moreau'];
        $statuses = ['active', 'suspended', 'pending'];
        $plans = ['community', 'standard'];

        for ($i = 1; $i <= 100; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $status = $statuses[array_rand($statuses)];
            $planSlug = $plans[array_rand($plans)];
            
            $users[] = [
                'id' => $i,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => strtolower($firstName . '.' . $lastName . $i . '@example.com'),
                'status' => $status,
                'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 365) . ' days')),
                'documents_count' => rand(0, 25),
                'calculations_count' => rand(0, 50),
                'subscription' => [
                    'plan_slug' => $planSlug,
                    'plan_name' => $planSlug === 'standard' ? 'Standard' : 'Communautaire',
                    'expires_at' => $planSlug === 'community' ? date('Y-m-d H:i:s', strtotime('+' . rand(1, 30) . ' days')) : null
                ]
            ];
        }

        return $users;
    }

    private function generateSubscriptionsData($filters = []) {
        $subscriptions = [];
        $users = $this->generateUsersData();
        
        foreach (array_slice($users, 0, 50) as $user) {
            if (!isset($user['subscription'])) continue;
            
            $sub = $user['subscription'];
            $subscription = [
                'id' => $user['id'],
                'user' => $user,
                'plan_slug' => $sub['plan_slug'],
                'plan_name' => $sub['plan_name'],
                'price' => $sub['plan_slug'] === 'standard' ? 5 : 0,
                'status' => rand(0, 10) > 1 ? 'active' : (rand(0, 1) ? 'expired' : 'cancelled'),
                'starts_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 365) . ' days')),
                'expires_at' => $sub['expires_at'],
                'total_revenue' => $sub['plan_slug'] === 'standard' ? rand(5, 60) : 0,
                'payments_count' => $sub['plan_slug'] === 'standard' ? rand(1, 12) : 0
            ];
            
            // Application des filtres
            if (!empty($filters['plan']) && $subscription['plan_slug'] !== $filters['plan']) continue;
            if (!empty($filters['status']) && $subscription['status'] !== $filters['status']) continue;
            if (!empty($filters['expiring'])) {
                $days = intval($filters['expiring']);
                if (!$subscription['expires_at'] || strtotime($subscription['expires_at']) > strtotime("+{$days} days")) continue;
            }
            
            $subscriptions[] = $subscription;
        }

        return $subscriptions;
    }
    
    /**
     * Gestion des commentaires/témoignages
     */
    public function comments()
    {
        require_once __DIR__ . '/../Models/Comment.php';
        $commentModel = new Comment($this->config);
        
        $status = $_GET['status'] ?? null;
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        
        $comments = $commentModel->getAll($status);
        $stats = $commentModel->getStats();
        
        // Pagination simple
        $total = count($comments);
        $offset = ($page - 1) * $limit;
        $comments = array_slice($comments, $offset, $limit);
        
        $this->render('superadmin/comments', [
            'title' => 'Gestion des commentaires - ProActiv',
            'comments' => $comments,
            'stats' => $stats,
            'current_status' => $status,
            'current_page' => $page,
            'total_pages' => ceil($total / $limit),
            'total_comments' => $total
        ]);
    }
    
    /**
     * Modération des commentaires (AJAX)
     */
    public function moderateComment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Méthode non autorisée']);
            return;
        }
        
        require_once __DIR__ . '/../Models/Comment.php';
        $commentModel = new Comment($this->config);
        
        $commentId = (int)($_POST['comment_id'] ?? 0);
        $action = $_POST['action'] ?? '';
        $note = $_POST['note'] ?? null;
        
        if (!$commentId || !in_array($action, ['approve', 'reject', 'delete', 'feature'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Paramètres invalides']);
            return;
        }
        
        $moderatorId = $_SESSION['user_id'] ?? null;
        
        switch ($action) {
            case 'delete':
                $result = $commentModel->delete($commentId);
                break;
            case 'feature':
                $featured = $_POST['featured'] === 'true';
                $order = (int)($_POST['order'] ?? 0);
                $result = $commentModel->setFeatured($commentId, $featured, $order);
                break;
            default:
                $status = $action === 'approve' ? 'approved' : 'rejected';
                $result = $commentModel->moderate($commentId, $status, $moderatorId, $note);
                break;
        }
        
        $this->jsonResponse($result);
    }

    private function isAdmin() {
        // Simulation - en production, vérifier la session et les droits
        return true; // Pour les tests
    }
    
    /**
     * Page d'analytics
     */
    public function analytics($request = null)
    {
        $analytics = $this->getAnalyticsData();
        
        return $this->render('superadmin/analytics', [
            'title' => 'Analytics - Super Administration',
            'analytics' => $analytics
        ]);
    }
    
    /**
     * Page de paramètres
     */
    public function settings($request = null)
    {
        $settings = $this->getSystemSettings();
        
        return $this->render('superadmin/settings', [
            'title' => 'Paramètres - Super Administration',
            'settings' => $settings
        ]);
    }
    
    /**
     * Sauvegarde des paramètres
     */
    public function saveSettings($request)
    {
        if ($request->getMethod() !== 'POST') {
            return $this->jsonResponse(['success' => false, 'error' => 'Méthode non autorisée']);
        }
        
        // Récupérer les données du formulaire
        $settings = [];
        foreach ($_POST as $key => $value) {
            $settings[$key] = $value;
        }
        
        // Sauvegarder les paramètres (simulation)
        $success = $this->saveSystemSettings($settings);
        
        return $this->jsonResponse([
            'success' => $success,
            'message' => $success ? 'Paramètres sauvegardés avec succès' : 'Erreur lors de la sauvegarde'
        ]);
    }
    
    /**
     * Récupère les paramètres système
     */
    private function getSystemSettings()
    {
        // En production, récupérer depuis la base de données ou fichier de config
        return [
            'site_name' => 'ProActiv',
            'site_url' => 'https://proactiv.example.com',
            'site_description' => 'Plateforme pour auto-entrepreneurs',
            'default_language' => 'fr',
            'default_timezone' => 'Europe/Paris',
            'registration_enabled' => true,
            'maintenance_mode' => false,
            'session_timeout' => 60,
            'max_login_attempts' => 5,
            'password_min_length' => 8,
            'password_complexity' => 'medium',
            'two_factor_enabled' => false,
            'email_verification' => true,
            'captcha_enabled' => false,
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 587,
            'smtp_username' => '',
            'smtp_password' => '',
            'from_email' => 'noreply@proactiv.com',
            'from_name' => 'ProActiv',
            'smtp_encryption' => true,
            'email_notifications' => true,
            'stripe_public_key' => '',
            'stripe_secret_key' => '',
            'paypal_client_id' => '',
            'paypal_secret' => '',
            'default_currency' => 'EUR',
            'tax_rate' => 20,
            'stripe_enabled' => true,
            'paypal_enabled' => true,
            'test_mode' => true,
            'calculator_charges' => true,
            'calculator_taxes' => true,
            'calculator_revenue' => true,
            'forum_enabled' => true,
            'documents_enabled' => true,
            'comments_enabled' => true,
            'max_file_size' => 10,
            'api_rate_limit' => 60,
            'auto_backup' => false
        ];
    }
    
    /**
     * Sauvegarde les paramètres système
     */
    private function saveSystemSettings($settings)
    {
        // En production, sauvegarder en base de données ou fichier de config
        // Pour la simulation, on retourne toujours true
        return true;
    }

    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

