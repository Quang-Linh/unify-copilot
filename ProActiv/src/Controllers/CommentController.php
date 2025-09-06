<?php

class CommentController extends BaseController {
    private $commentModel;
    
    public function __construct($config) {
        parent::__construct($config);
        $this->commentModel = new Comment($config);
    }
    
    /**
     * Affiche le formulaire de commentaire
     */
    public function index() {
        $comments = $this->commentModel->getApproved(5);
        $stats = $this->commentModel->getStats();
        
        $this->render('comments/index', [
            'comments' => $comments,
            'stats' => $stats,
            'page_title' => 'Témoignages clients'
        ]);
    }
    
    /**
     * Traite la soumission d'un nouveau commentaire
     */
    public function submit() {
        if ($this->request->getMethod() !== 'POST') {
            header('Location: /ProActiv/comments');
            exit;
        }
        
        $data = [
            'user_name' => trim($_POST['user_name'] ?? ''),
            'user_email' => trim($_POST['user_email'] ?? ''),
            'rating' => (int)($_POST['rating'] ?? 0),
            'comment' => trim($_POST['comment'] ?? ''),
            'activity' => trim($_POST['activity'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'user_id' => $_SESSION['user_id'] ?? null
        ];
        
        // Validation
        $errors = $this->commentModel->validate($data);
        
        if (empty($errors)) {
            $result = $this->commentModel->create($data);
            
            if ($result) {
                $_SESSION['success_message'] = 'Merci pour votre témoignage ! Il sera publié après modération.';
                header('Location: /ProActiv/comments');
                exit;
            } else {
                $errors[] = 'Erreur lors de l\'enregistrement du commentaire';
            }
        }
        
        // Affichage avec erreurs
        $comments = $this->commentModel->getApproved(5);
        $stats = $this->commentModel->getStats();
        
        $this->render('comments/index', [
            'comments' => $comments,
            'stats' => $stats,
            'errors' => $errors,
            'form_data' => $data,
            'page_title' => 'Témoignages clients'
        ]);
    }
    
    /**
     * API pour obtenir les commentaires (AJAX)
     */
    public function api() {
        $limit = (int)($_GET['limit'] ?? 10);
        $status = $_GET['status'] ?? 'approved';
        
        if ($status === 'approved') {
            $comments = $this->commentModel->getApproved($limit);
        } else {
            $comments = $this->commentModel->getAll($status);
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'comments' => $comments,
            'stats' => $this->commentModel->getStats()
        ]);
        exit;
    }
    
    /**
     * Modération des commentaires (admin)
     */
    public function moderate() {
        // Vérification des droits admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }
        
        if ($this->request->getMethod() !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
            exit;
        }
        
        $commentId = (int)($_POST['comment_id'] ?? 0);
        $action = $_POST['action'] ?? '';
        
        if (!$commentId || !in_array($action, ['approve', 'reject', 'delete'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Paramètres invalides']);
            exit;
        }
        
        if ($action === 'delete') {
            $result = $this->commentModel->delete($commentId);
        } else {
            $status = $action === 'approve' ? 'approved' : 'rejected';
            $moderatorId = $_SESSION['user_id'] ?? null;
            $note = $_POST['note'] ?? null;
            $result = $this->commentModel->moderate($commentId, $status, $moderatorId, $note);
        }
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
    
    /**
     * Widget des commentaires pour intégration
     */
    public function widget() {
        $limit = (int)($_GET['limit'] ?? 3);
        $comments = $this->commentModel->getApproved($limit);
        $stats = $this->commentModel->getStats();
        
        // Rendu sans layout pour intégration
        include PROACTIV_ROOT . '/templates/comments/widget.php';
        exit;
    }
}

