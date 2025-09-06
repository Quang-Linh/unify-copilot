<?php
require_once 'BaseController.php';

class DashboardController extends BaseController {
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    public function index(Request $request) {
        $this->requireAuth();
        
        $stats = $this->getUserStats();
        $recentActivities = $this->getRecentActivities();
        $notifications = $this->getNotifications();
        $quickActions = $this->getQuickActions();
        $chartData = $this->getChartData();
        
        // Log de l'accès au dashboard
        $this->logAction('dashboard_view', ['user_id' => $this->getCurrentUserId()]);
        
        $this->render('dashboard/index', [
            'title' => 'Tableau de bord',
            'stats' => $stats,
            'activities' => $recentActivities,
            'notifications' => $notifications,
            'quick_actions' => $quickActions,
            'chart_data' => $chartData,
            'user_name' => $_SESSION['user_name'] ?? 'Utilisateur'
        ]);
    }
    
    private function getUserStats() {
        $userId = $this->getCurrentUserId();
        
        // Statistiques enrichies
        return [
            'revenue_month' => 4250.00,
            'revenue_previous_month' => 3850.00,
            'charges_month' => 850.00,
            'charges_previous_month' => 770.00,
            'net_income_month' => 3400.00,
            'net_income_previous_month' => 3080.00,
            'documents_count' => 18,
            'documents_previous_month' => 15,
            'calculations_count' => 67,
            'calculations_previous_month' => 52,
            'forum_posts' => 12,
            'forum_posts_previous_month' => 8,
            'last_login' => $_SESSION['login_time'] ? date('d/m/Y H:i', $_SESSION['login_time']) : 'Maintenant',
            'pending_invoices' => 3,
            'overdue_invoices' => 1,
            'next_declaration' => '31/08/2025'
        ];
    }
    
    private function getRecentActivities() {
        return [
            [
                'action' => 'Facture #2025-018 générée',
                'description' => 'Client: SARL Dupont - Montant: 1,200.00€',
                'date' => '29/08/2025 14:30',
                'type' => 'document',
                'icon' => 'fas fa-file-invoice',
                'color' => 'success'
            ],
            [
                'action' => 'Calcul charges sociales Q3 2025',
                'description' => 'CA: 12,750€ - Charges: 2,550€',
                'date' => '29/08/2025 10:15',
                'type' => 'calculator',
                'icon' => 'fas fa-calculator',
                'color' => 'primary'
            ],
            [
                'action' => 'Réponse publiée: "Nouveaux taux 2025"',
                'description' => 'Forum > Charges sociales',
                'date' => '28/08/2025 16:45',
                'type' => 'forum',
                'icon' => 'fas fa-comments',
                'color' => 'info'
            ],
            [
                'action' => 'Devis #2025-025 créé',
                'description' => 'Client: ETS Martin - Montant: 850.00€',
                'date' => '28/08/2025 09:20',
                'type' => 'document',
                'icon' => 'fas fa-file-alt',
                'color' => 'warning'
            ],
            [
                'action' => 'Calcul optimisation fiscale',
                'description' => 'Économie potentielle: 340€/an',
                'date' => '27/08/2025 15:30',
                'type' => 'calculator',
                'icon' => 'fas fa-percentage',
                'color' => 'success'
            ]
        ];
    }
    
    private function getNotifications() {
        return [
            [
                'type' => 'danger',
                'title' => 'Facture en retard',
                'message' => 'La facture #2025-012 est en retard de paiement (15 jours).',
                'date' => '29/08/2025',
                'icon' => 'fas fa-exclamation-circle',
                'action' => '/documents?filter=overdue'
            ],
            [
                'type' => 'warning',
                'title' => 'Déclaration URSSAF',
                'message' => 'Déclaration trimestrielle à effectuer avant le 31/08/2025.',
                'date' => '25/08/2025',
                'icon' => 'fas fa-calendar-exclamation',
                'action' => '/calculators/charges-sociales'
            ],
            [
                'type' => 'info',
                'title' => 'Nouveau sujet forum',
                'message' => '3 nouveaux sujets dans "Fiscalité" depuis votre dernière visite.',
                'date' => '28/08/2025',
                'icon' => 'fas fa-bell',
                'action' => '/forum/category/2'
            ],
            [
                'type' => 'success',
                'title' => 'Paiement reçu',
                'message' => 'Facture #2025-016 payée - Montant: 950.00€',
                'date' => '27/08/2025',
                'icon' => 'fas fa-check-circle',
                'action' => '/documents'
            ]
        ];
    }
    
    private function getQuickActions() {
        return [
            [
                'title' => 'Nouvelle facture',
                'description' => 'Créer une facture rapidement',
                'icon' => 'fas fa-file-invoice',
                'color' => 'primary',
                'url' => '/documents/create?type=invoice'
            ],
            [
                'title' => 'Calculer charges',
                'description' => 'Charges sociales du mois',
                'icon' => 'fas fa-calculator',
                'color' => 'success',
                'url' => '/calculators/charges-sociales'
            ],
            [
                'title' => 'Nouveau devis',
                'description' => 'Générer un devis client',
                'icon' => 'fas fa-file-alt',
                'color' => 'warning',
                'url' => '/documents/create?type=quote'
            ],
            [
                'title' => 'Poser une question',
                'description' => 'Forum communautaire',
                'icon' => 'fas fa-question-circle',
                'color' => 'info',
                'url' => '/forum/new-topic'
            ],
            [
                'title' => 'Aide tarification',
                'description' => 'Calculer vos prix',
                'icon' => 'fas fa-tags',
                'color' => 'secondary',
                'url' => '/calculators/tarification'
            ],
            [
                'title' => 'Mes documents',
                'description' => 'Gérer factures et devis',
                'icon' => 'fas fa-folder-open',
                'color' => 'dark',
                'url' => '/documents'
            ]
        ];
    }
    
    private function getChartData() {
        return [
            'revenue_evolution' => [
                'labels' => ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû'],
                'data' => [2800, 3200, 2950, 3400, 3100, 3650, 3850, 4250]
            ],
            'charges_evolution' => [
                'labels' => ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû'],
                'data' => [560, 640, 590, 680, 620, 730, 770, 850]
            ],
            'documents_by_type' => [
                'labels' => ['Factures', 'Devis', 'Avoir'],
                'data' => [12, 5, 1]
            ],
            'forum_activity' => [
                'labels' => ['Sujets créés', 'Réponses publiées', 'Sujets suivis'],
                'data' => [3, 9, 15]
            ]
        ];
    }
    
    /**
     * API pour récupérer les statistiques en AJAX
     */
    public function getStats(Request $request) {
        $this->requireAuth();
        
        if ($request->isAjax()) {
            $stats = $this->getUserStats();
            $this->jsonResponse($stats);
        } else {
            $this->redirect('/dashboard');
        }
    }
    
    /**
     * API pour récupérer les activités récentes
     */
    public function getActivities(Request $request) {
        $this->requireAuth();
        
        if ($request->isAjax()) {
            $activities = $this->getRecentActivities();
            $this->jsonResponse($activities);
        } else {
            $this->redirect('/dashboard');
        }
    }
    
    /**
     * API pour récupérer les données de graphiques
     */
    public function getChartDataApi(Request $request) {
        $this->requireAuth();
        
        if ($request->isAjax()) {
            $chartData = $this->getChartData();
            $this->jsonResponse($chartData);
        } else {
            $this->redirect('/dashboard');
        }
    }
}
