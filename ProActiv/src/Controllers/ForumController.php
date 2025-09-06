<?php
require_once 'BaseController.php';

class ForumController extends BaseController {
    
    public function index(Request $request) {
        $this->requireAuth();
        $this->logAction('forum_view');
        
        $categories = $this->getCategories();
        $recentTopics = $this->getRecentTopics();
        
        $this->render('forum/index', [
            'title' => 'Forum communautaire',
            'categories' => $categories,
            'recent_topics' => $recentTopics
        ]);
    }
    
    public function category(Request $request) {
        $this->requireAuth();
        $categoryId = $request->getParam('id');
        
        $category = $this->getCategory($categoryId);
        $topics = $this->getCategoryTopics($categoryId);
        
        $this->render('forum/category', [
            'title' => $category['name'] ?? 'Catégorie',
            'category' => $category,
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
    
    public function newTopic(Request $request) {
        $this->requireAuth();
        
        if ($request->getMethod() === 'POST') {
            return $this->createTopic($request);
        }
        
        $categories = $this->getCategories();
        
        $this->render('forum/new-topic', [
            'title' => 'Nouveau sujet',
            'categories' => $categories,
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }
    
    public function createTopic(Request $request) {
        $this->requireAuth();
        
        if (!$this->verifyCSRFToken($request->getPost('csrf_token', ''))) {
            $this->jsonResponse(['error' => 'Token de sécurité invalide'], 400);
            return;
        }
        
        $title = trim($request->getPost('title', ''));
        $content = trim($request->getPost('content', ''));
        $categoryId = (int)$request->getPost('category_id', 0);
        
        if (empty($title) || empty($content)) {
            $this->jsonResponse(['error' => 'Titre et contenu requis'], 400);
            return;
        }
        
        $topicId = $this->saveNewTopic($title, $content, $categoryId);
        
        $this->logAction('forum_topic_created', ['topic_id' => $topicId]);
        
        $this->jsonResponse(['success' => true, 'topic_id' => $topicId]);
    }
    
    public function reply(Request $request) {
        $this->requireAuth();
        
        if (!$this->verifyCSRFToken($request->getPost('csrf_token', ''))) {
            $this->jsonResponse(['error' => 'Token de sécurité invalide'], 400);
            return;
        }
        
        $topicId = $request->getParam('id');
        $content = trim($request->getPost('content', ''));
        
        if (empty($content)) {
            $this->jsonResponse(['error' => 'Contenu requis'], 400);
            return;
        }
        
        $postId = $this->saveReply($topicId, $content);
        
        $this->logAction('forum_reply_created', ['topic_id' => $topicId, 'post_id' => $postId]);
        
        $this->jsonResponse(['success' => true, 'post_id' => $postId]);
    }
    
    public function search(Request $request) {
        $this->requireAuth();
        
        $query = trim($request->getQuery('q', ''));
        $results = [];
        
        if (!empty($query)) {
            $results = $this->searchTopics($query);
        }
        
        $this->render('forum/search', [
            'title' => 'Recherche dans le forum',
            'query' => $query,
            'results' => $results
        ]);
    }
    
    private function getCategories() {
        return [
            ['id' => 1, 'name' => 'Charges sociales', 'description' => 'Questions sur les cotisations et charges', 'topics_count' => 25, 'icon' => 'fas fa-calculator'],
            ['id' => 2, 'name' => 'Fiscalité', 'description' => 'Impôts, déclarations, optimisation fiscale', 'topics_count' => 18, 'icon' => 'fas fa-percentage'],
            ['id' => 3, 'name' => 'Juridique', 'description' => 'Statuts, contrats, aspects légaux', 'topics_count' => 12, 'icon' => 'fas fa-gavel'],
            ['id' => 4, 'name' => 'Gestion', 'description' => 'Comptabilité, facturation, organisation', 'topics_count' => 22, 'icon' => 'fas fa-chart-line'],
            ['id' => 5, 'name' => 'Développement', 'description' => 'Croissance, marketing, stratégie', 'topics_count' => 15, 'icon' => 'fas fa-rocket']
        ];
    }
    
    private function getRecentTopics() {
        return [
            ['id' => 1, 'title' => 'Nouveaux taux de charges sociales 2025', 'author' => 'Marie L.', 'category' => 'Charges sociales', 'replies' => 12, 'created_at' => '2025-08-29 14:30'],
            ['id' => 2, 'title' => 'Déclaration URSSAF en retard : que faire ?', 'author' => 'Pierre D.', 'category' => 'Charges sociales', 'replies' => 8, 'created_at' => '2025-08-29 10:15'],
            ['id' => 3, 'title' => 'Optimisation fiscale pour les consultants', 'author' => 'Sophie M.', 'category' => 'Fiscalité', 'replies' => 15, 'created_at' => '2025-08-28 16:45'],
            ['id' => 4, 'title' => 'Passage en SASU : avantages et inconvénients', 'author' => 'Thomas R.', 'category' => 'Juridique', 'replies' => 6, 'created_at' => '2025-08-28 09:20'],
            ['id' => 5, 'title' => 'Logiciel de facturation gratuit : recommandations', 'author' => 'Julie B.', 'category' => 'Gestion', 'replies' => 20, 'created_at' => '2025-08-27 18:30']
        ];
    }
    
    private function getCategory($id) {
        $categories = $this->getCategories();
        foreach ($categories as $category) {
            if ($category['id'] == $id) {
                return $category;
            }
        }
        return null;
    }
    
    private function getCategoryTopics($categoryId) {
        // Simulation de données par catégorie
        return [
            ['id' => 1, 'title' => 'Nouveaux taux de charges sociales 2025', 'author' => 'Marie L.', 'replies' => 12, 'views' => 245, 'created_at' => '2025-08-29 14:30', 'last_activity' => '2025-08-29 16:20'],
            ['id' => 2, 'title' => 'Déclaration URSSAF en retard : que faire ?', 'author' => 'Pierre D.', 'replies' => 8, 'views' => 156, 'created_at' => '2025-08-29 10:15', 'last_activity' => '2025-08-29 15:45']
        ];
    }
    
    private function getTopic($id) {
        return [
            'id' => $id, 
            'title' => 'Nouveaux taux de charges sociales 2025', 
            'content' => 'Bonjour à tous,\n\nJe viens de recevoir les nouveaux taux de charges sociales pour 2025. Y a-t-il des changements importants par rapport à 2024 ?\n\nMerci pour vos retours !',
            'author' => 'Marie L.',
            'created_at' => '2025-08-29 14:30',
            'views' => 245,
            'category' => 'Charges sociales'
        ];
    }
    
    private function getTopicPosts($topicId) {
        return [
            [
                'id' => 1,
                'author' => 'Pierre D.',
                'content' => 'Salut Marie ! Effectivement il y a quelques changements. Le taux pour les professions libérales passe de 21,2% à 22%. Pour les activités commerciales, ça reste stable à 12,8%.',
                'created_at' => '2025-08-29 15:15'
            ],
            [
                'id' => 2,
                'author' => 'Sophie M.',
                'content' => 'Merci Pierre pour l\'info ! J\'ai aussi remarqué que la CFE a légèrement augmenté dans ma commune. Il faut vraiment surveiller ces évolutions.',
                'created_at' => '2025-08-29 15:45'
            ],
            [
                'id' => 3,
                'author' => 'Thomas R.',
                'content' => 'Pour ma part, j\'utilise le calculateur de ProActiv qui est déjà à jour avec les nouveaux taux. Très pratique !',
                'created_at' => '2025-08-29 16:20'
            ]
        ];
    }
    
    private function saveNewTopic($title, $content, $categoryId) {
        // En mode démo, on retourne un ID fictif
        return rand(100, 999);
    }
    
    private function saveReply($topicId, $content) {
        // En mode démo, on retourne un ID fictif
        return rand(1000, 9999);
    }
    
    private function searchTopics($query) {
        // Simulation de résultats de recherche
        return [
            ['id' => 1, 'title' => 'Nouveaux taux de charges sociales 2025', 'excerpt' => 'Discussion sur les nouveaux taux...', 'category' => 'Charges sociales'],
            ['id' => 3, 'title' => 'Optimisation fiscale pour les consultants', 'excerpt' => 'Conseils pour optimiser sa fiscalité...', 'category' => 'Fiscalité']
        ];
    }
}
