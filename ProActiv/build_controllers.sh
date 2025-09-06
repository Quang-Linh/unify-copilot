#!/bin/bash

echo "🎮 Création des contrôleurs complets..."

# Contrôleur d'authentification
cat > src/Controllers/AuthController.php << 'AUTH_END'
<?php
require_once 'BaseController.php';

class AuthController extends BaseController {
    private $jwtService;
    private $auditService;
    
    public function __construct($app) {
        parent::__construct($app);
        $this->jwtService = new JWTService($this->config);
        $this->auditService = new AuditService($this->db, $this->config);
    }
    
    public function login(Request $request) {
        if ($request->isPost()) {
            return $this->processLogin($request);
        }
        
        $this->render('auth/login', [
            'title' => $this->lang->translate('login'),
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }
    
    public function register(Request $request) {
        if ($request->isPost()) {
            return $this->processRegister($request);
        }
        
        $this->render('auth/register', [
            'title' => $this->lang->translate('register'),
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }
    
    public function logout(Request $request) {
        $this->auditService->logEvent('user_logout', [], $this->getCurrentUserId());
        session_destroy();
        $this->redirect('/');
    }
    
    private function processLogin(Request $request) {
        $data = $request->getPostData();
        
        if (!$this->verifyCSRFToken($data['csrf_token'] ?? '')) {
            $this->setFlashMessage('error', 'Token de sécurité invalide');
            return $this->redirect('/login');
        }
        
        // Simulation de connexion (sans DB)
        if ($data['email'] === 'demo@proactiv.fr' && $data['password'] === 'demo123') {
            $_SESSION['user_id'] = 1;
            $_SESSION['user_email'] = $data['email'];
            $_SESSION['user_role'] = 'user';
            
            $token = $this->jwtService->generateToken(['user_id' => 1, 'email' => $data['email']]);
            $_SESSION['jwt_token'] = $token;
            
            $this->auditService->logEvent('user_login', ['email' => $data['email']], 1);
            $this->setFlashMessage('success', 'Connexion réussie !');
            return $this->redirect('/dashboard');
        }
        
        $this->auditService->logEvent('login_failed', ['email' => $data['email'] ?? '']);
        $this->setFlashMessage('error', 'Identifiants incorrects');
        return $this->redirect('/login');
    }
    
    private function processRegister(Request $request) {
        $data = $request->getPostData();
        
        if (!$this->verifyCSRFToken($data['csrf_token'] ?? '')) {
            $this->setFlashMessage('error', 'Token de sécurité invalide');
            return $this->redirect('/register');
        }
        
        // Validation basique
        $errors = [];
        if (empty($data['email'])) $errors[] = 'Email requis';
        if (empty($data['password'])) $errors[] = 'Mot de passe requis';
        if (strlen($data['password']) < 8) $errors[] = 'Mot de passe trop court';
        
        if (!empty($errors)) {
            $this->setFlashMessage('error', implode('<br>', $errors));
            return $this->redirect('/register');
        }
        
        $this->auditService->logEvent('user_registered', ['email' => $data['email']]);
        $this->setFlashMessage('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
        return $this->redirect('/login');
    }
}
AUTH_END

# Contrôleur du tableau de bord
cat > src/Controllers/DashboardController.php << 'DASHBOARD_END'
<?php
require_once 'BaseController.php';

class DashboardController extends BaseController {
    private $auditService;
    
    public function __construct($app) {
        parent::__construct($app);
        $this->auditService = new AuditService($this->db, $this->config);
    }
    
    public function index(Request $request) {
        $this->requireAuth();
        
        $stats = $this->getUserStats();
        $recentActivities = $this->getRecentActivities();
        $notifications = $this->getNotifications();
        
        $this->auditService->logEvent('dashboard_view', [], $this->getCurrentUserId());
        
        $this->render('dashboard/index', [
            'title' => $this->lang->translate('dashboard'),
            'stats' => $stats,
            'activities' => $recentActivities,
            'notifications' => $notifications
        ]);
    }
    
    private function getUserStats() {
        return [
            'documents_count' => 12,
            'calculations_count' => 45,
            'forum_posts' => 8,
            'last_login' => date('Y-m-d H:i:s')
        ];
    }
    
    private function getRecentActivities() {
        return [
            ['action' => 'Document généré', 'date' => '2025-08-27 10:30:00'],
            ['action' => 'Calcul charges sociales', 'date' => '2025-08-27 09:15:00'],
            ['action' => 'Post forum publié', 'date' => '2025-08-26 16:45:00']
        ];
    }
    
    private function getNotifications() {
        return [
            ['type' => 'info', 'message' => 'Nouvelle mise à jour disponible'],
            ['type' => 'warning', 'message' => 'Déclaration URSSAF à faire avant le 31/08']
        ];
    }
}
DASHBOARD_END

echo "✅ Contrôleurs Auth et Dashboard créés"

