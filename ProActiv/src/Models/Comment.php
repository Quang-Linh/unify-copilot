<?php

class Comment {
    private $config;
    private $db;
    
    public function __construct($config) {
        $this->config = $config;
        $this->initDatabase();
    }
    
    /**
     * Initialise la connexion à la base de données
     */
    private function initDatabase() {
        if ($this->config['database']['enabled']) {
            try {
                $dsn = "mysql:host={$this->config['database']['host']};dbname={$this->config['database']['name']};charset=utf8mb4";
                $this->db = new PDO($dsn, $this->config['database']['user'], $this->config['database']['password']);
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Erreur de connexion à la base de données: " . $e->getMessage());
                $this->db = null;
            }
        }
    }
    
    /**
     * Crée un nouveau commentaire
     */
    public function create($data) {
        if (!$this->db) {
            throw new Exception("Base de données non disponible");
        }
        
        try {
            $sql = "INSERT INTO user_comments (
                user_id, user_name, user_email, user_activity, user_location, 
                rating, comment, ip_address, user_agent, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['user_id'] ?? null,
                $data['user_name'],
                $data['user_email'],
                $data['user_activity'] ?? null,
                $data['user_location'] ?? null,
                $data['rating'],
                $data['comment'],
                $data['ip_address'] ?? $_SERVER['REMOTE_ADDR'] ?? null,
                $data['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
            
            return $result ? $this->db->lastInsertId() : false;
        } catch (PDOException $e) {
            error_log("Erreur lors de la création du commentaire: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtient les commentaires approuvés pour affichage public
     */
    public function getApproved($limit = 10) {
        if (!$this->db) {
            return []; // Retourner un tableau vide au lieu de données factices
        }
        
        try {
            $sql = "SELECT * FROM user_comments 
                    WHERE status = 'approved' 
                    ORDER BY is_featured DESC, created_at DESC 
                    LIMIT ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des commentaires: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtient les commentaires en vedette
     */
    public function getFeatured($limit = 5) {
        if (!$this->db) {
            return []; // Retourner un tableau vide au lieu de données factices
        }
        
        try {
            $sql = "SELECT * FROM user_comments 
                    WHERE status = 'approved' AND is_featured = 1 
                    ORDER BY created_at DESC 
                    LIMIT ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des commentaires en vedette: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtient tous les commentaires (pour l'admin)
     */
    public function getAll($status = null) {
        if (!$this->db) {
            return []; // Retourner un tableau vide au lieu de données factices
        }
        
        try {
            $sql = "SELECT * FROM user_comments";
            $params = [];
            
            if ($status) {
                $sql .= " WHERE status = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de tous les commentaires: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtient un commentaire par son ID
     */
    public function getById($id) {
        if (!$this->db) {
            return null;
        }
        
        try {
            $sql = "SELECT * FROM user_comments WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération du commentaire: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Met à jour le statut d'un commentaire
     */
    public function updateStatus($id, $status) {
        if (!$this->db) {
            return false;
        }
        
        try {
            $sql = "UPDATE user_comments SET status = ?, moderated_at = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([$status, $id]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du statut: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Met en vedette ou retire de la vedette un commentaire
     */
    public function toggleFeatured($id) {
        if (!$this->db) {
            return false;
        }
        
        try {
            $sql = "UPDATE user_comments SET is_featured = NOT is_featured WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour de la vedette: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprime un commentaire
     */
    public function delete($id) {
        if (!$this->db) {
            return false;
        }
        
        try {
            $sql = "DELETE FROM user_comments WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression du commentaire: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtient les statistiques des commentaires
     */
    public function getStats() {
        if (!$this->db) {
            return [
                'total' => 0,
                'approved' => 0,
                'pending' => 0,
                'rejected' => 0,
                'average_rating' => 0,
                'rating_distribution' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]
            ];
        }
        
        try {
            // Statistiques générales
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                        AVG(CASE WHEN status = 'approved' THEN rating ELSE NULL END) as average_rating
                    FROM user_comments";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $stats = $stmt->fetch();
            
            // Distribution des notes
            $sql = "SELECT rating, COUNT(*) as count 
                    FROM user_comments 
                    WHERE status = 'approved' 
                    GROUP BY rating";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $ratings = $stmt->fetchAll();
            
            $ratingDistribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
            foreach ($ratings as $rating) {
                $ratingDistribution[$rating['rating']] = (int)$rating['count'];
            }
            
            return [
                'total' => (int)$stats['total'],
                'approved' => (int)$stats['approved'],
                'pending' => (int)$stats['pending'],
                'rejected' => (int)$stats['rejected'],
                'average_rating' => round((float)$stats['average_rating'], 1),
                'rating_distribution' => $ratingDistribution
            ];
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des statistiques: " . $e->getMessage());
            return [
                'total' => 0,
                'approved' => 0,
                'pending' => 0,
                'rejected' => 0,
                'average_rating' => 0,
                'rating_distribution' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]
            ];
        }
    }
    
    /**
     * Valide les données d'un commentaire
     */
    public function validate($data) {
        $errors = [];
        
        if (empty($data['user_name']) || strlen(trim($data['user_name'])) < 2) {
            $errors[] = "Le nom doit contenir au moins 2 caractères";
        }
        
        if (empty($data['user_email']) || !filter_var($data['user_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse email n'est pas valide";
        }
        
        if (empty($data['rating']) || !in_array($data['rating'], [1, 2, 3, 4, 5])) {
            $errors[] = "La note doit être comprise entre 1 et 5";
        }
        
        if (empty($data['comment']) || strlen(trim($data['comment'])) < 10) {
            $errors[] = "Le commentaire doit contenir au moins 10 caractères";
        }
        
        if (strlen($data['comment']) > 1000) {
            $errors[] = "Le commentaire ne peut pas dépasser 1000 caractères";
        }
        
        return $errors;
    }
    
    /**
     * Vérifie si un utilisateur peut poster un commentaire (anti-spam)
     */
    public function canPost($email, $ipAddress) {
        if (!$this->db) {
            return true; // En mode sans BDD, autoriser
        }
        
        try {
            // Vérifier si l'utilisateur a déjà posté dans les dernières 24h
            $sql = "SELECT COUNT(*) as count 
                    FROM user_comments 
                    WHERE (user_email = ? OR ip_address = ?) 
                    AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email, $ipAddress]);
            $result = $stmt->fetch();
            
            return $result['count'] < 3; // Maximum 3 commentaires par 24h
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification anti-spam: " . $e->getMessage());
            return true; // En cas d'erreur, autoriser
        }
    }
}
?>

