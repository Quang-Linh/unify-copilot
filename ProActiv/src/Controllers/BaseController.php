<?php
/**
 * Contrôleur de base ProActiv
 * Fonctionnalités communes à tous les contrôleurs
 */

class BaseController
{
    protected $config;
    protected $db;
    protected $lang;
    protected $auditService;
    protected $subscriptionService;
    
    public function __construct($app)
    {
        if (is_array($app)) {
            // Si on reçoit directement la config (mode de compatibilité)
            $this->config = $app;
            $this->db = null;
        } else {
            // Mode normal avec objet Application
            $this->config = $app->getConfig();
            $this->db = $app->getDatabase();
        }
        
        // Initialisation des services
        $this->lang = new LanguageService();
        $this->subscriptionService = new SubscriptionService($this->db, $this->config);
        
        // Gestion du changement de langue
        if (isset($_GET['lang'])) {
            $this->lang->setLanguage($_GET['lang']);
            header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
            exit;
        }
        
        // Initialisation de l'audit service seulement si la base de données est disponible
        if ($this->db) {
            $this->auditService = new AuditService($this->db, $this->config);
        }
    }
    
    /**
     * Rendu d'une vue avec le layout
     */
    protected function render(string $view, array $data = []): void
    {
        // Données disponibles dans toutes les vues
        $data['config'] = $this->config;
        $data['current_user_id'] = $this->getCurrentUserId();
        $data['is_authenticated'] = $this->isAuthenticated();
        $data['lang'] = $this->lang; // Ajout du service de langue
        
        // Données d'abonnement si utilisateur connecté
        if ($this->isAuthenticated()) {
            $userId = $this->getCurrentUserId();
            $data['user_subscription'] = $this->subscriptionService->getUserSubscription($userId);
            $data['theme'] = $this->subscriptionService->getTheme($userId);
        } else {
            // Utilisateur non connecté = thème communautaire
            $data['user_subscription'] = null;
            $data['theme'] = [
                'name' => 'community',
                'css_class' => 'theme-community',
                'primary_color' => '#ff6b35',
                'show_banner' => true,
                'banner_text' => 'You are in a Community version'
            ];
        }
        
        // Extraction des variables pour la vue
        extract($data);
        
        // Capture du contenu de la vue
        ob_start();
        $viewFile = $this->config['app']['paths']['templates'] . '/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new Exception("Vue non trouvée: $view");
        }
        
        $content = ob_get_clean();
        
        // Rendu avec le layout - le service de langue est disponible via $this
        include $this->config['app']['paths']['templates'] . '/layouts/main.php';
    }
    
    /**
     * Redirection
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Message flash
     */
    protected function setFlashMessage(string $type, string $message): void
    {
        $_SESSION['flash_type'] = $type;
        $_SESSION['flash_message'] = $message;
    }
    
    /**
     * Récupération des messages flash
     */
    protected function getFlashMessages(): array
    {
        $messages = [];
        if (isset($_SESSION['flash_message'])) {
            $messages[] = [
                'type' => $_SESSION['flash_type'] ?? 'info',
                'message' => $_SESSION['flash_message']
            ];
        }
        return $messages;
    }
    
    /**
     * Vérification d'authentification
     */
    protected function requireAuth(): void
    {
        if (!$this->isAuthenticated()) {
            $this->setFlashMessage('warning', 'Vous devez être connecté pour accéder à cette page');
            $this->redirect('/login');
        }
    }
    
    /**
     * Vérification si l'utilisateur est connecté
     */
    protected function isAuthenticated(): bool
    {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * ID de l'utilisateur actuel
     */
    protected function getCurrentUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Génération token CSRF
     */
    protected function generateCSRFToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Vérification token CSRF
     */
    protected function verifyCSRFToken(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Réponse JSON
     */
    protected function jsonResponse(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Validation des données
     */
    protected function validate(array $data, array $rules): array
    {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? '';
            
            foreach ($fieldRules as $rule) {
                switch ($rule) {
                    case 'required':
                        if (empty($value)) {
                            $errors[$field][] = "Le champ $field est requis";
                        }
                        break;
                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = "Le champ $field doit être un email valide";
                        }
                        break;
                    case 'min:8':
                        if (strlen($value) < 8) {
                            $errors[$field][] = "Le champ $field doit contenir au moins 8 caractères";
                        }
                        break;
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Log d'audit automatique
     */
    protected function logAction(string $action, array $data = []): void
    {
        $this->auditService->logEvent($action, $data, $this->getCurrentUserId());
    }
}
