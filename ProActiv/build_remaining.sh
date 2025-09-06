#!/bin/bash

echo "🚀 Finalisation de ProActiv..."

# Contrôleur des calculateurs
cat > src/Controllers/CalculatorController.php << 'CALC_END'
<?php
require_once 'BaseController.php';

class CalculatorController extends BaseController {
    
    public function index(Request $request) {
        $this->requireAuth();
        $this->logAction('calculators_view');
        
        $this->render('calculators/index', [
            'title' => $this->lang->translate('calculators')
        ]);
    }
    
    public function chargesSociales(Request $request) {
        $this->requireAuth();
        
        if ($request->isPost()) {
            return $this->calculateCharges($request);
        }
        
        $this->render('calculators/charges-sociales', [
            'title' => 'Calculateur Charges Sociales',
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }
    
    public function calculateCharges(Request $request) {
        $data = $request->getPostData();
        
        if (!$this->verifyCSRFToken($data['csrf_token'] ?? '')) {
            $this->jsonResponse(['error' => 'Token invalide'], 400);
        }
        
        $ca = floatval($data['chiffre_affaires'] ?? 0);
        $regime = $data['regime'] ?? 'micro';
        
        $results = [];
        
        if ($regime === 'micro') {
            $results['cotisations_sociales'] = $ca * 0.22; // 22% pour services
            $results['cfe'] = $ca > 5000 ? 227 : 0;
            $results['formation'] = $ca * 0.002;
        }
        
        $results['total'] = ($results['cotisations_sociales'] ?? 0) + 
                           ($results['cfe'] ?? 0) + 
                           ($results['formation'] ?? 0);
        
        $this->logAction('charges_calculated', ['ca' => $ca, 'regime' => $regime]);
        
        $this->jsonResponse(['success' => true, 'results' => $results]);
    }
    
    public function impots(Request $request) {
        $this->requireAuth();
        
        $this->render('calculators/impots', [
            'title' => 'Calculateur Impôts',
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }
}
CALC_END

# Contrôleur du forum
cat > src/Controllers/ForumController.php << 'FORUM_END'
<?php
require_once 'BaseController.php';

class ForumController extends BaseController {
    
    public function index(Request $request) {
        $this->requireAuth();
        $this->logAction('forum_view');
        
        $topics = $this->getTopics();
        
        $this->render('forum/index', [
            'title' => $this->lang->translate('forum'),
            'topics' => $topics
        ]);
    }
    
    public function topic(Request $request) {
        $this->requireAuth();
        $topicId = $request->getParam('id');
        
        $topic = $this->getTopic($topicId);
        $posts = $this->getTopicPosts($topicId);
        
        $this->logAction('forum_topic_view', ['topic_id' => $topicId]);
        
        $this->render('forum/topic', [
            'title' => $topic['title'] ?? 'Sujet',
            'topic' => $topic,
            'posts' => $posts,
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }
    
    private function getTopics() {
        // Données simulées
        return [
            ['id' => 1, 'title' => 'Charges sociales 2025', 'author' => 'Marie', 'replies' => 12, 'created_at' => '2025-08-27'],
            ['id' => 2, 'title' => 'Déclaration URSSAF', 'author' => 'Pierre', 'replies' => 8, 'created_at' => '2025-08-26'],
            ['id' => 3, 'title' => 'Optimisation fiscale', 'author' => 'Sophie', 'replies' => 15, 'created_at' => '2025-08-25']
        ];
    }
    
    private function getTopic($id) {
        return ['id' => $id, 'title' => 'Charges sociales 2025', 'content' => 'Contenu du sujet...'];
    }
    
    private function getTopicPosts($topicId) {
        return [
            ['author' => 'Marie', 'content' => 'Premier message...', 'created_at' => '2025-08-27 10:00'],
            ['author' => 'Pierre', 'content' => 'Réponse intéressante...', 'created_at' => '2025-08-27 11:30']
        ];
    }
}
FORUM_END

# API Controller
cat > src/Controllers/APIController.php << 'API_END'
<?php
require_once 'BaseController.php';

class APIController extends BaseController {
    
    public function stats(Request $request) {
        $stats = [
            'users_count' => 1250,
            'documents_count' => 3420,
            'calculations_count' => 15600,
            'forum_posts' => 890,
            'last_update' => date('Y-m-d H:i:s')
        ];
        
        $this->logAction('api_stats_accessed');
        $this->jsonResponse(['success' => true, 'data' => $stats]);
    }
    
    public function calculate(Request $request) {
        $data = $request->getPostData();
        $type = $data['type'] ?? '';
        
        switch ($type) {
            case 'charges':
                $result = $this->calculateChargesSociales($data);
                break;
            case 'impots':
                $result = $this->calculateImpots($data);
                break;
            default:
                $this->jsonResponse(['error' => 'Type de calcul non supporté'], 400);
        }
        
        $this->logAction('api_calculation', ['type' => $type]);
        $this->jsonResponse(['success' => true, 'result' => $result]);
    }
    
    private function calculateChargesSociales($data) {
        $ca = floatval($data['ca'] ?? 0);
        return [
            'cotisations' => $ca * 0.22,
            'cfe' => $ca > 5000 ? 227 : 0,
            'formation' => $ca * 0.002
        ];
    }
    
    private function calculateImpots($data) {
        $revenu = floatval($data['revenu'] ?? 0);
        $abattement = $revenu * 0.34; // 34% d'abattement
        $revenu_imposable = $revenu - $abattement;
        
        return [
            'revenu_imposable' => $revenu_imposable,
            'impot_estime' => $revenu_imposable * 0.11 // Taux moyen estimé
        ];
    }
}
API_END

echo "✅ Contrôleurs Calculateur, Forum et API créés"

