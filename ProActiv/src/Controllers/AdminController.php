<?php
require_once 'BaseController.php';

class AdminController extends BaseController {
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    /**
     * Tableau de bord administrateur
     */
    public function index(Request $request) {
        $this->requireAuth();
        $this->requireAdmin();
        
        $stats = $this->getAdminStats();
        $recentActivity = $this->getRecentActivity();
        $systemInfo = $this->getSystemInfo();
        
        $this->render('admin/index', [
            'title' => 'Administration - Tableau de bord',
            'stats' => $stats,
            'recent_activity' => $recentActivity,
            'system_info' => $systemInfo
        ]);
    }
    
    /**
     * Gestion des utilisateurs
     */
    public function users(Request $request) {
        $this->requireAuth();
        $this->requireAdmin();
        
        $users = $this->getAllUsers();
        $userStats = $this->getUsersStats();
        
        $this->render('admin/users', [
            'title' => 'Administration - Utilisateurs',
            'users' => $users,
            'user_stats' => $userStats
        ]);
    }
    
    /**
     * Gestion du forum
     */
    public function forum(Request $request) {
        $this->requireAuth();
        $this->requireAdmin();
        
        $categories = $this->getForumCategories();
        $recentTopics = $this->getRecentTopics();
        $moderationQueue = $this->getModerationQueue();
        
        $this->render('admin/forum', [
            'title' => 'Administration - Forum',
            'categories' => $categories,
            'recent_topics' => $recentTopics,
            'moderation_queue' => $moderationQueue
        ]);
    }
    
    /**
     * Configuration système
     */
    public function settings(Request $request) {
        $this->requireAuth();
        $this->requireAdmin();
        
        if ($request->getMethod() === 'POST') {
            $this->updateSettings($request);
            $this->redirect('/admin/settings?success=1');
        }
        
        $settings = $this->getSystemSettings();
        
        $this->render('admin/settings', [
            'title' => 'Administration - Configuration',
            'settings' => $settings,
            'success' => $request->getParam('success')
        ]);
    }
    
    /**
     * Analytics et rapports
     */
    public function analytics(Request $request) {
        $this->requireAuth();
        $this->requireAdmin();
        
        $period = $request->getParam('period', '30days');
        $analytics = $this->getAnalytics($period);
        $charts = $this->getAnalyticsCharts($period);
        
        $this->render('admin/analytics', [
            'title' => 'Administration - Analytics',
            'analytics' => $analytics,
            'charts' => $charts,
            'period' => $period
        ]);
    }
    
    /**
     * Gestion des documents
     */
    public function documents(Request $request) {
        $this->requireAuth();
        $this->requireAdmin();
        
        $documents = $this->getAllDocuments();
        $documentStats = $this->getDocumentStats();
        
        $this->render('admin/documents', [
            'title' => 'Administration - Documents',
            'documents' => $documents,
            'document_stats' => $documentStats
        ]);
    }
    
    /**
     * Logs système
     */
    public function logs(Request $request) {
        $this->requireAuth();
        $this->requireAdmin();
        
        $logs = $this->getSystemLogs();
        $logStats = $this->getLogStats();
        
        $this->render('admin/logs', [
            'title' => 'Administration - Logs',
            'logs' => $logs,
            'log_stats' => $logStats
        ]);
    }
    
    // Méthodes privées pour récupérer les données
    
    private function getAdminStats() {
        return [
            'total_users' => 1247,
            'active_users_today' => 89,
            'total_documents' => 3456,
            'documents_today' => 23,
            'total_calculations' => 8901,
            'calculations_today' => 156,
            'forum_topics' => 234,
            'forum_posts' => 1567,
            'server_uptime' => '15 jours 8h 32min',
            'disk_usage' => '2.3 GB / 10 GB',
            'memory_usage' => '512 MB / 2 GB'
        ];
    }
    
    private function getRecentActivity() {
        return [
            [
                'type' => 'user_registration',
                'message' => 'Nouvel utilisateur inscrit: marie.dupont@email.com',
                'timestamp' => '2025-08-29 14:32:15',
                'icon' => 'fas fa-user-plus',
                'color' => 'success'
            ],
            [
                'type' => 'document_generated',
                'message' => 'Facture générée par jean.martin@email.com',
                'timestamp' => '2025-08-29 14:28:42',
                'icon' => 'fas fa-file-invoice',
                'color' => 'primary'
            ],
            [
                'type' => 'forum_post',
                'message' => 'Nouveau sujet créé dans "Charges sociales"',
                'timestamp' => '2025-08-29 14:15:33',
                'icon' => 'fas fa-comments',
                'color' => 'info'
            ],
            [
                'type' => 'calculation',
                'message' => '25 calculs de charges effectués dans la dernière heure',
                'timestamp' => '2025-08-29 14:00:00',
                'icon' => 'fas fa-calculator',
                'color' => 'warning'
            ]
        ];
    }
    
    private function getSystemInfo() {
        return [
            'php_version' => phpversion(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database_version' => '8.0.33',
            'app_version' => '2.1.0',
            'last_backup' => '2025-08-29 02:00:00',
            'maintenance_mode' => false
        ];
    }
    
    private function getAllUsers() {
        // Données de démonstration
        return [
            [
                'id' => 1,
                'name' => 'Marie Dupont',
                'email' => 'marie.dupont@email.com',
                'role' => 'user',
                'status' => 'active',
                'last_login' => '2025-08-29 14:30:00',
                'documents_count' => 12,
                'calculations_count' => 45
            ],
            [
                'id' => 2,
                'name' => 'Jean Martin',
                'email' => 'jean.martin@email.com',
                'role' => 'user',
                'status' => 'active',
                'last_login' => '2025-08-29 13:15:00',
                'documents_count' => 8,
                'calculations_count' => 23
            ],
            [
                'id' => 3,
                'name' => 'Sophie Bernard',
                'email' => 'sophie.bernard@email.com',
                'role' => 'user',
                'status' => 'inactive',
                'last_login' => '2025-08-25 09:45:00',
                'documents_count' => 3,
                'calculations_count' => 7
            ]
        ];
    }
    
    private function getUsersStats() {
        return [
            'total' => 1247,
            'active' => 1156,
            'inactive' => 91,
            'new_this_month' => 67,
            'premium' => 234
        ];
    }
    
    private function requireAdmin() {
        // Vérification des droits administrateur
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $this->redirect('/dashboard?error=access_denied');
        }
    }
    
    private function getForumCategories() {
        return [
            ['id' => 1, 'name' => 'Charges sociales', 'topics' => 45, 'posts' => 234],
            ['id' => 2, 'name' => 'Fiscalité', 'topics' => 38, 'posts' => 189],
            ['id' => 3, 'name' => 'Juridique', 'topics' => 29, 'posts' => 156],
            ['id' => 4, 'name' => 'Gestion', 'topics' => 33, 'posts' => 201],
            ['id' => 5, 'name' => 'Développement', 'topics' => 21, 'posts' => 98]
        ];
    }
    
    private function getRecentTopics() {
        return [
            [
                'title' => 'Nouveaux taux de charges sociales 2025',
                'author' => 'Marie L.',
                'category' => 'Charges sociales',
                'replies' => 12,
                'created_at' => '2025-08-29 14:30:00'
            ],
            [
                'title' => 'Déclaration URSSAF en retard : que faire ?',
                'author' => 'Pierre D.',
                'category' => 'Charges sociales',
                'replies' => 8,
                'created_at' => '2025-08-29 10:15:00'
            ]
        ];
    }
    
    private function getModerationQueue() {
        return [
            [
                'type' => 'post',
                'content' => 'Message signalé pour contenu inapproprié...',
                'author' => 'user123@email.com',
                'reported_by' => 'moderator@proactiv.fr',
                'created_at' => '2025-08-29 12:00:00'
            ]
        ];
    }
    
    private function getSystemSettings() {
        return [
            'site_name' => 'ProActiv',
            'site_description' => 'Plateforme complète pour auto-entrepreneurs',
            'maintenance_mode' => false,
            'registration_enabled' => true,
            'forum_enabled' => true,
            'email_notifications' => true,
            'backup_frequency' => 'daily',
            'max_file_size' => '10MB'
        ];
    }
    
    private function updateSettings(Request $request) {
        // Mise à jour des paramètres système
        $settings = [
            'site_name' => $request->getParam('site_name'),
            'site_description' => $request->getParam('site_description'),
            'maintenance_mode' => $request->getParam('maintenance_mode') === 'on',
            'registration_enabled' => $request->getParam('registration_enabled') === 'on',
            'forum_enabled' => $request->getParam('forum_enabled') === 'on'
        ];
        
        // Sauvegarde des paramètres (simulation)
        $this->logAction('settings_updated', $settings);
    }
    
    private function getAnalytics($period) {
        return [
            'page_views' => 15678,
            'unique_visitors' => 3456,
            'bounce_rate' => 23.4,
            'avg_session_duration' => '4m 32s',
            'top_pages' => [
                '/dashboard' => 2345,
                '/calculators/charges-sociales' => 1876,
                '/documents' => 1234,
                '/forum' => 987
            ]
        ];
    }
    
    private function getAnalyticsCharts($period) {
        return [
            'visitors' => [
                'labels' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                'data' => [120, 145, 167, 189, 201, 156, 134]
            ],
            'documents' => [
                'labels' => ['Factures', 'Devis', 'Avoir'],
                'data' => [456, 234, 67]
            ]
        ];
    }
    
    private function getAllDocuments() {
        return [
            [
                'id' => 1,
                'type' => 'Facture',
                'number' => '2025-001',
                'user' => 'marie.dupont@email.com',
                'amount' => 1200.00,
                'created_at' => '2025-08-29 14:30:00'
            ],
            [
                'id' => 2,
                'type' => 'Devis',
                'number' => '2025-015',
                'user' => 'jean.martin@email.com',
                'amount' => 850.00,
                'created_at' => '2025-08-29 10:15:00'
            ]
        ];
    }
    
    private function getDocumentStats() {
        return [
            'total' => 3456,
            'invoices' => 2345,
            'quotes' => 987,
            'credits' => 124,
            'this_month' => 234
        ];
    }
    
    private function getSystemLogs() {
        return [
            [
                'level' => 'INFO',
                'message' => 'User login: marie.dupont@email.com',
                'timestamp' => '2025-08-29 14:30:15',
                'ip' => '192.168.1.100'
            ],
            [
                'level' => 'WARNING',
                'message' => 'Failed login attempt: unknown@email.com',
                'timestamp' => '2025-08-29 14:25:33',
                'ip' => '192.168.1.200'
            ],
            [
                'level' => 'ERROR',
                'message' => 'Database connection timeout',
                'timestamp' => '2025-08-29 14:20:45',
                'ip' => 'localhost'
            ]
        ];
    }
    
    private function getLogStats() {
        return [
            'total' => 15678,
            'info' => 12345,
            'warning' => 2876,
            'error' => 457,
            'today' => 234
        ];
    }
}

