<?php
/**
 * Contrôleur d'accueil ProActiv
 * Gère la page d'accueil et les pages d'information
 */

require_once 'BaseController.php';

class HomeController extends BaseController
{
    /**
     * Page d'accueil
     */
    public function index(Request $request)
    {
        // Statistiques pour la page d'accueil
        $stats = $this->getStats();
        
        // Derniers articles du forum
        $latestTopics = $this->getLatestForumTopics();
        
        // Témoignages
        $testimonials = $this->getTestimonials();
        
        $this->render('home/index', [
            'title' => 'Accueil - Plateforme Auto-Entrepreneur',
            'stats' => $stats,
            'latest_topics' => $latestTopics,
            'testimonials' => $testimonials,
            'flash_messages' => $this->getFlashMessages()
        ]);
    }
    
    /**
     * Récupère les statistiques de la plateforme
     */
    private function getStats()
    {
        // En développement sans base de données, utilisons des données statiques
        if (!$this->db) {
            return [
                'users_count' => 1250,
                'documents_count' => 3420,
                'forum_posts_count' => 890,
                'calculators_used' => 15600
            ];
        }
        
        try {
            // Nombre d'utilisateurs actifs
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM users WHERE active = 1");
            $usersCount = $stmt->fetch()['count'];
            
            // Nombre de documents générés
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM documents");
            $documentsCount = $stmt->fetch()['count'];
            
            // Nombre de posts du forum
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM forum_posts");
            $postsCount = $stmt->fetch()['count'];
            
            // Nombre d'utilisations des calculateurs
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM calculations");
            $calculationsCount = $stmt->fetch()['count'];
            
            return [
                'users_count' => $usersCount,
                'documents_count' => $documentsCount,
                'forum_posts_count' => $postsCount,
                'calculators_used' => $calculationsCount
            ];
            
        } catch (Exception $e) {
            error_log("Erreur récupération stats: " . $e->getMessage());
            return [
                'users_count' => 0,
                'documents_count' => 0,
                'forum_posts_count' => 0,
                'calculators_used' => 0
            ];
        }
    }
    
    /**
     * Récupère les derniers sujets du forum
     */
    private function getLatestForumTopics()
    {
        // En développement sans base de données
        if (!$this->db) {
            return [
                [
                    'id' => 1,
                    'title' => 'Comment calculer ses charges sociales en 2025 ?',
                    'author' => 'Marie D.',
                    'created_at' => '2025-08-26 15:30:00',
                    'replies_count' => 12
                ],
                [
                    'id' => 2,
                    'title' => 'Déclaration URSSAF : les nouveautés',
                    'author' => 'Pierre L.',
                    'created_at' => '2025-08-26 14:15:00',
                    'replies_count' => 8
                ],
                [
                    'id' => 3,
                    'title' => 'Optimisation fiscale pour auto-entrepreneur',
                    'author' => 'Sophie M.',
                    'created_at' => '2025-08-26 10:45:00',
                    'replies_count' => 15
                ]
            ];
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT t.id, t.title, u.username as author, t.created_at,
                       COUNT(p.id) as replies_count
                FROM forum_topics t
                LEFT JOIN users u ON t.author_id = u.id
                LEFT JOIN forum_posts p ON t.id = p.topic_id
                WHERE t.active = 1
                GROUP BY t.id
                ORDER BY t.created_at DESC
                LIMIT 5
            ");
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log("Erreur récupération topics: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupère les témoignages
     */
    private function getTestimonials()
    {
        // Témoignages statiques pour le développement
        return [
            [
                'name' => 'Sophie Martin',
                'business' => 'Graphiste freelance',
                'content' => 'ProActiv m\'a fait gagner un temps précieux dans la gestion de mon activité. Les calculateurs sont très précis !',
                'rating' => 5
            ],
            [
                'name' => 'Thomas Dubois',
                'business' => 'Consultant IT',
                'content' => 'Interface claire et fonctionnalités complètes. Je recommande vivement cette plateforme.',
                'rating' => 5
            ],
            [
                'name' => 'Marie Leroy',
                'business' => 'Coach bien-être',
                'content' => 'La communauté est très active et les conseils sont précieux pour débuter en auto-entrepreneur.',
                'rating' => 4
            ]
        ];
    }
    
    /**
     * Page à propos
     */
    public function about(Request $request)
    {
        $this->render('pages/about', [
            'title' => 'À propos - ProActiv'
        ]);
    }
    
    /**
     * Page contact
     */
    public function contact(Request $request)
    {
        if ($request->isPost()) {
            return $this->handleContactForm($request);
        }
        
        $this->render('pages/contact', [
            'title' => 'Contact - ProActiv'
        ]);
    }
    
    /**
     * Traitement du formulaire de contact
     */
    private function handleContactForm(Request $request)
    {
        $data = $request->getPostData();
        
        // Validation
        $errors = [];
        if (empty($data['name'])) $errors[] = 'Le nom est requis';
        if (empty($data['email'])) $errors[] = 'L\'email est requis';
        if (empty($data['message'])) $errors[] = 'Le message est requis';
        
        if (!empty($errors)) {
            $this->setFlashMessage('error', implode('<br>', $errors));
            return $this->redirect('/contact');
        }
        
        // Envoi de l'email (simulation en développement)
        $this->setFlashMessage('success', 'Votre message a été envoyé avec succès !');
        return $this->redirect('/contact');
    }
}
