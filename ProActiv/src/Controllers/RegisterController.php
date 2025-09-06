<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Subscription.php';
require_once __DIR__ . '/../Services/SubscriptionService.php';

class RegisterController extends BaseController {
    private $userModel;
    private $subscriptionModel;
    private $subscriptionService;
    
    public function __construct($config = []) {
        parent::__construct($config);
        $this->userModel = new User();
        $this->subscriptionModel = new Subscription();
        $this->subscriptionService = new SubscriptionService();
    }
    
    /**
     * Affiche le formulaire d'inscription avec choix d'abonnement
     */
    public function index($request) {
        // Si l'utilisateur est déjà connecté, rediriger vers le dashboard
        if (isset($_SESSION['user_id'])) {
            return $this->redirect('/ProActiv/dashboard');
        }
        
        $selectedPlan = $request->getParam('plan', 'community');
        $plans = $this->subscriptionService->getAvailablePlans();
        
        return $this->render('register/index', [
            'title' => 'Inscription - ProActiv',
            'plans' => $plans,
            'selected_plan' => $selectedPlan,
            'current_step' => 1
        ]);
    }
    
    /**
     * Traite l'inscription
     */
    public function process($request) {
        if ($request->getMethod() !== 'POST') {
            return $this->redirect('/ProActiv/register');
        }
        
        // Récupération des données
        $data = [
            'first_name' => trim($request->getParam('first_name', '')),
            'last_name' => trim($request->getParam('last_name', '')),
            'email' => trim($request->getParam('email', '')),
            'password' => $request->getParam('password', ''),
            'password_confirm' => $request->getParam('password_confirm', ''),
            'plan' => $request->getParam('plan', 'community'),
            'terms_accepted' => $request->getParam('terms_accepted', false)
        ];
        
        // Validation
        $errors = $this->validateRegistration($data);
        
        if (!empty($errors)) {
            $plans = $this->subscriptionService->getAvailablePlans();
            return $this->render('register/index', [
                'title' => 'Inscription - ProActiv',
                'plans' => $plans,
                'selected_plan' => $data['plan'],
                'errors' => $errors,
                'form_data' => $data,
                'current_step' => 1
            ]);
        }
        
        // Création de l'utilisateur
        $userId = $this->userModel->create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'status' => 'active',
            'email_verified' => false
        ]);
        
        if (!$userId) {
            $plans = $this->subscriptionService->getAvailablePlans();
            return $this->render('register/index', [
                'title' => 'Inscription - ProActiv',
                'plans' => $plans,
                'selected_plan' => $data['plan'],
                'errors' => ['Erreur lors de la création du compte. Veuillez réessayer.'],
                'form_data' => $data,
                'current_step' => 1
            ]);
        }
        
        // Création de l'abonnement
        $subscriptionData = [
            'user_id' => $userId,
            'plan_slug' => $data['plan'],
            'status' => 'active'
        ];
        
        // Pour les comptes communautaires, définir une date d'expiration
        if ($data['plan'] === 'community') {
            $subscriptionData['expires_at'] = date('Y-m-d H:i:s', strtotime('+30 days'));
        }
        
        $subscriptionId = $this->subscriptionModel->create($subscriptionData);
        
        // Connexion automatique
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $data['email'];
        $_SESSION['user_name'] = $data['first_name'] . ' ' . $data['last_name'];
        $_SESSION['user_plan'] = $data['plan'];
        
        // Redirection selon le plan choisi
        if ($data['plan'] === 'standard') {
            // Pour les comptes payants, rediriger vers la page de paiement
            return $this->redirect('/ProActiv/register/payment?subscription_id=' . $subscriptionId);
        } else {
            // Pour les comptes communautaires, rediriger vers l'onboarding
            return $this->redirect('/ProActiv/register/welcome');
        }
    }
    
    /**
     * Page de paiement pour les abonnements payants
     */
    public function payment($request) {
        if (!isset($_SESSION['user_id'])) {
            return $this->redirect('/ProActiv/register');
        }
        
        $subscriptionId = $request->getParam('subscription_id');
        $subscription = $this->subscriptionModel->getById($subscriptionId);
        
        if (!$subscription || $subscription['user_id'] != $_SESSION['user_id']) {
            return $this->redirect('/ProActiv/register');
        }
        
        $plan = $this->subscriptionService->getPlan($subscription['plan_slug']);
        
        return $this->render('register/payment', [
            'title' => 'Paiement - ProActiv',
            'subscription' => $subscription,
            'plan' => $plan,
            'current_step' => 2
        ]);
    }
    
    /**
     * Traite le paiement
     */
    public function processPayment($request) {
        if ($request->getMethod() !== 'POST') {
            return $this->redirect('/ProActiv/register');
        }
        
        if (!isset($_SESSION['user_id'])) {
            return $this->redirect('/ProActiv/register');
        }
        
        $subscriptionId = $request->getParam('subscription_id');
        $paymentMethod = $request->getParam('payment_method', 'card');
        
        // Simulation du traitement du paiement
        $paymentResult = $this->processPaymentSimulation($subscriptionId, $paymentMethod);
        
        if ($paymentResult['success']) {
            // Activer l'abonnement
            $this->subscriptionModel->update($subscriptionId, [
                'status' => 'active',
                'starts_at' => date('Y-m-d H:i:s'),
                'next_billing_date' => date('Y-m-d H:i:s', strtotime('+1 month'))
            ]);
            
            return $this->redirect('/ProActiv/register/welcome?payment=success');
        } else {
            return $this->redirect('/ProActiv/register/payment?subscription_id=' . $subscriptionId . '&error=' . urlencode($paymentResult['error']));
        }
    }
    
    /**
     * Page de bienvenue après inscription
     */
    public function welcome($request) {
        if (!isset($_SESSION['user_id'])) {
            return $this->redirect('/ProActiv/register');
        }
        
        $paymentSuccess = $request->getParam('payment') === 'success';
        $userPlan = $_SESSION['user_plan'] ?? 'community';
        
        return $this->render('register/welcome', [
            'title' => 'Bienvenue sur ProActiv !',
            'user_name' => $_SESSION['user_name'] ?? 'Utilisateur',
            'user_plan' => $userPlan,
            'payment_success' => $paymentSuccess,
            'current_step' => 3
        ]);
    }
    
    /**
     * Vérification de la disponibilité d'un email (AJAX)
     */
    public function checkEmail($request) {
        $email = $request->getParam('email');
        
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->jsonResponse(['available' => false, 'error' => 'Email invalide']);
        }
        
        $exists = $this->userModel->findByEmail($email);
        
        return $this->jsonResponse(['available' => !$exists]);
    }
    
    /**
     * Validation des données d'inscription
     */
    private function validateRegistration($data) {
        $errors = [];
        
        // Prénom
        if (empty($data['first_name'])) {
            $errors[] = 'Le prénom est obligatoire.';
        } elseif (strlen($data['first_name']) < 2) {
            $errors[] = 'Le prénom doit contenir au moins 2 caractères.';
        }
        
        // Nom
        if (empty($data['last_name'])) {
            $errors[] = 'Le nom est obligatoire.';
        } elseif (strlen($data['last_name']) < 2) {
            $errors[] = 'Le nom doit contenir au moins 2 caractères.';
        }
        
        // Email
        if (empty($data['email'])) {
            $errors[] = 'L\'email est obligatoire.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'L\'email n\'est pas valide.';
        } elseif ($this->userModel->findByEmail($data['email'])) {
            $errors[] = 'Cet email est déjà utilisé.';
        }
        
        // Mot de passe
        if (empty($data['password'])) {
            $errors[] = 'Le mot de passe est obligatoire.';
        } elseif (strlen($data['password']) < 6) {
            $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
        }
        
        // Confirmation du mot de passe
        if ($data['password'] !== $data['password_confirm']) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }
        
        // Plan
        $validPlans = ['community', 'standard'];
        if (!in_array($data['plan'], $validPlans)) {
            $errors[] = 'Plan d\'abonnement invalide.';
        }
        
        // Conditions d'utilisation
        if (!$data['terms_accepted']) {
            $errors[] = 'Vous devez accepter les conditions d\'utilisation.';
        }
        
        return $errors;
    }
    
    /**
     * Simulation du traitement du paiement
     */
    private function processPaymentSimulation($subscriptionId, $paymentMethod) {
        // Simulation - en production, intégrer Stripe, PayPal, etc.
        
        // 90% de succès pour la simulation
        if (rand(1, 10) <= 9) {
            return [
                'success' => true,
                'transaction_id' => 'sim_' . uniqid(),
                'amount' => 5.00,
                'currency' => 'EUR'
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Paiement refusé. Veuillez vérifier vos informations bancaires.'
            ];
        }
    }
    
    /**
     * Réponse JSON
     */
    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

