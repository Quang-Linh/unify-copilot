<?php
require_once 'BaseController.php';

class AuthController extends BaseController {
    private $jwtService;
    
    public function __construct($app) {
        parent::__construct($app);
        $this->jwtService = new JWTService($this->config);
    }
    
    public function login(Request $request) {
        // Si déjà connecté, rediriger vers le dashboard
        if ($this->isAuthenticated()) {
            $this->redirect('/dashboard');
            return;
        }
        
        if ($request->isPost()) {
            return $this->processLogin($request);
        }
        
        $this->render('auth/login', [
            'title' => 'Connexion',
            'csrf_token' => $this->generateCSRFToken(),
            'flash_messages' => $this->getFlashMessages()
        ]);
    }
    
    public function register(Request $request) {
        // Si déjà connecté, rediriger vers le dashboard
        if ($this->isAuthenticated()) {
            $this->redirect('/dashboard');
            return;
        }
        
        if ($request->isPost()) {
            return $this->processRegister($request);
        }
        
        $this->render('auth/register', [
            'title' => 'Inscription',
            'csrf_token' => $this->generateCSRFToken(),
            'flash_messages' => $this->getFlashMessages()
        ]);
    }
    
    public function logout(Request $request) {
        $userId = $this->getCurrentUserId();
        
        // Log de déconnexion
        if ($userId) {
            $this->logAction('user_logout', ['user_id' => $userId]);
        }
        
        // Destruction de la session
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        
        $this->setFlashMessage('success', 'Vous avez été déconnecté avec succès');
        $this->redirect('/login');
    }
    
    private function processLogin(Request $request) {
        // Vérification CSRF
        if (!$this->verifyCSRFToken($request->getPost('csrf_token', ''))) {
            $this->setFlashMessage('error', 'Token de sécurité invalide');
            $this->redirect('/login');
            return;
        }
        
        $email = trim($request->getPost('email', ''));
        $password = $request->getPost('password', '');
        $remember = $request->getPost('remember', false);
        
        // Validation des données
        $errors = $this->validateLoginData($email, $password);
        if (!empty($errors)) {
            $this->setFlashMessage('error', implode('<br>', $errors));
            $this->redirect('/login');
            return;
        }
        
        // Authentification
        $user = $this->authenticateUser($email, $password);
        if (!$user) {
            $this->logAction('login_failed', ['email' => $email]);
            $this->setFlashMessage('error', 'Email ou mot de passe incorrect');
            $this->redirect('/login');
            return;
        }
        
        // Connexion réussie
        $this->loginUser($user, $remember);
        $this->logAction('user_login', ['email' => $email, 'user_id' => $user['id']]);
        
        $this->setFlashMessage('success', 'Connexion réussie ! Bienvenue ' . htmlspecialchars($user['name']));
        
        // Redirection vers la page demandée ou dashboard
        $redirectTo = $_SESSION['redirect_after_login'] ?? '/dashboard';
        unset($_SESSION['redirect_after_login']);
        $this->redirect($redirectTo);
    }
    
    private function processRegister(Request $request) {
        // Vérification CSRF
        if (!$this->verifyCSRFToken($request->getPost('csrf_token', ''))) {
            $this->setFlashMessage('error', 'Token de sécurité invalide');
            $this->redirect('/register');
            return;
        }
        
        $data = [
            'name' => trim($request->getPost('name', '')),
            'email' => trim($request->getPost('email', '')),
            'password' => $request->getPost('password', ''),
            'password_confirm' => $request->getPost('password_confirm', ''),
            'activity_type' => $request->getPost('activity_type', ''),
            'terms' => $request->getPost('terms', false)
        ];
        
        // Validation des données
        $errors = $this->validateRegisterData($data);
        if (!empty($errors)) {
            $this->setFlashMessage('error', implode('<br>', $errors));
            $this->redirect('/register');
            return;
        }
        
        // Vérification si l'email existe déjà
        if ($this->emailExists($data['email'])) {
            $this->setFlashMessage('error', 'Un compte existe déjà avec cet email');
            $this->redirect('/register');
            return;
        }
        
        // Création du compte
        $userId = $this->createUser($data);
        if (!$userId) {
            $this->setFlashMessage('error', 'Erreur lors de la création du compte');
            $this->redirect('/register');
            return;
        }
        
        $this->logAction('user_registered', ['email' => $data['email'], 'user_id' => $userId]);
        $this->setFlashMessage('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
        $this->redirect('/login');
    }
    
    private function validateLoginData($email, $password) {
        $errors = [];
        
        if (empty($email)) {
            $errors[] = 'L\'email est requis';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format d\'email invalide';
        }
        
        if (empty($password)) {
            $errors[] = 'Le mot de passe est requis';
        }
        
        return $errors;
    }
    
    private function validateRegisterData($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'Le nom est requis';
        } elseif (strlen($data['name']) < 2) {
            $errors[] = 'Le nom doit contenir au moins 2 caractères';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'L\'email est requis';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format d\'email invalide';
        }
        
        if (empty($data['password'])) {
            $errors[] = 'Le mot de passe est requis';
        } elseif (strlen($data['password']) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères';
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $data['password'])) {
            $errors[] = 'Le mot de passe doit contenir au moins une minuscule, une majuscule et un chiffre';
        }
        
        if ($data['password'] !== $data['password_confirm']) {
            $errors[] = 'Les mots de passe ne correspondent pas';
        }
        
        if (empty($data['activity_type'])) {
            $errors[] = 'Le type d\'activité est requis';
        }
        
        if (!$data['terms']) {
            $errors[] = 'Vous devez accepter les conditions d\'utilisation';
        }
        
        return $errors;
    }
    
    private function authenticateUser($email, $password) {
        // Mode démonstration sans base de données
        if (!$this->db) {
            $demoUsers = [
                'demo@proactiv.fr' => [
                    'id' => 1,
                    'name' => 'Utilisateur Démo',
                    'email' => 'demo@proactiv.fr',
                    'password' => password_hash('demo123', PASSWORD_DEFAULT),
                    'role' => 'user',
                    'activity_type' => 'liberal'
                ],
                'admin@proactiv.fr' => [
                    'id' => 2,
                    'name' => 'Administrateur',
                    'email' => 'admin@proactiv.fr',
                    'password' => password_hash('admin123', PASSWORD_DEFAULT),
                    'role' => 'admin',
                    'activity_type' => 'liberal'
                ]
            ];
            
            if (isset($demoUsers[$email]) && password_verify($password, $demoUsers[$email]['password'])) {
                return $demoUsers[$email];
            }
            return false;
        }
        
        // Avec base de données
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    private function emailExists($email) {
        // Mode démonstration
        if (!$this->db) {
            return in_array($email, ['demo@proactiv.fr', 'admin@proactiv.fr']);
        }
        
        // Avec base de données
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }
    
    private function createUser($data) {
        // Mode démonstration
        if (!$this->db) {
            return rand(100, 999); // ID fictif
        }
        
        // Avec base de données
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password, activity_type, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        if ($stmt->execute([$data['name'], $data['email'], $hashedPassword, $data['activity_type']])) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    private function loginUser($user, $remember = false) {
        // Régénération de l'ID de session pour sécurité
        session_regenerate_id(true);
        
        // Stockage des informations utilisateur
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'] ?? 'user';
        $_SESSION['user_activity_type'] = $user['activity_type'] ?? 'liberal';
        $_SESSION['login_time'] = time();
        
        // Génération du token JWT
        $tokenData = [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'] ?? 'user'
        ];
        $_SESSION['jwt_token'] = $this->jwtService->generateToken($tokenData);
        
        // Cookie "Se souvenir de moi"
        if ($remember) {
            $rememberToken = bin2hex(random_bytes(32));
            $_SESSION['remember_token'] = $rememberToken;
            setcookie('remember_token', $rememberToken, time() + (30 * 24 * 60 * 60), '/', '', true, true);
        }
    }
}
