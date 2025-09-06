<?php
/**
 * Modèle User - Gestion des utilisateurs ProActiv
 */

class User
{
    private $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    /**
     * Crée un nouvel utilisateur
     */
    public function create(array $data)
    {
        if (!$this->db) {
            return ['success' => false, 'error' => 'Base de données non disponible'];
        }
        
        try {
            // Vérifier si l'email existe déjà
            if ($this->emailExists($data['email'])) {
                return ['success' => false, 'error' => 'Cet email est déjà utilisé'];
            }
            
            // Hacher le mot de passe
            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Générer un token de vérification
            $verificationToken = bin2hex(random_bytes(32));
            
            $stmt = $this->db->prepare("
                INSERT INTO users (
                    email, password_hash, first_name, last_name, 
                    company_name, siret, phone, status, 
                    email_verification_token
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?)
            ");
            
            $result = $stmt->execute([
                $data['email'],
                $passwordHash,
                $data['first_name'] ?? '',
                $data['last_name'] ?? '',
                $data['company_name'] ?? '',
                $data['siret'] ?? '',
                $data['phone'] ?? '',
                $verificationToken
            ]);
            
            if ($result) {
                $userId = $this->db->lastInsertId();
                return [
                    'success' => true, 
                    'user_id' => $userId,
                    'verification_token' => $verificationToken
                ];
            }
            
            return ['success' => false, 'error' => 'Erreur lors de la création du compte'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Erreur base de données: ' . $e->getMessage()];
        }
    }
    
    /**
     * Authentifie un utilisateur
     */
    public function authenticate($email, $password)
    {
        if (!$this->db) {
            return ['success' => false, 'error' => 'Base de données non disponible'];
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT id, email, password_hash, first_name, last_name, 
                       role, status, last_login
                FROM users 
                WHERE email = ? AND status IN ('active', 'trial')
            ");
            
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return ['success' => false, 'error' => 'Utilisateur non trouvé ou inactif'];
            }
            
            if (!password_verify($password, $user['password_hash'])) {
                return ['success' => false, 'error' => 'Mot de passe incorrect'];
            }
            
            // Mettre à jour la dernière connexion
            $this->updateLastLogin($user['id']);
            
            unset($user['password_hash']); // Ne pas retourner le hash
            
            return ['success' => true, 'user' => $user];
            
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Erreur base de données'];
        }
    }
    
    /**
     * Récupère un utilisateur par ID
     */
    public function findById($id)
    {
        if (!$this->db) {
            return null;
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT id, email, first_name, last_name, company_name, 
                       siret, phone, role, status, email_verified, 
                       last_login, created_at
                FROM users 
                WHERE id = ?
            ");
            
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Vérifie si un email existe
     */
    public function emailExists($email)
    {
        if (!$this->db) {
            return false;
        }
        
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetchColumn() > 0;
            
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Met à jour la dernière connexion
     */
    public function updateLastLogin($userId)
    {
        if (!$this->db) {
            return true;
        }
        
        try {
            $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            return $stmt->execute([$userId]);
            
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Vérifie l'email d'un utilisateur
     */
    public function verifyEmail($token)
    {
        if (!$this->db) {
            return ['success' => true];
        }
        
        try {
            $stmt = $this->db->prepare("
                UPDATE users 
                SET email_verified = 1, status = 'active', email_verification_token = NULL
                WHERE email_verification_token = ?
            ");
            
            $result = $stmt->execute([$token]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true];
            }
            
            return ['success' => false, 'error' => 'Token de vérification invalide'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Erreur base de données'];
        }
    }
    
    /**
     * Génère un token de réinitialisation de mot de passe
     */
    public function generatePasswordResetToken($email)
    {
        if (!$this->db) {
            return ['success' => false, 'error' => 'Fonctionnalité non disponible'];
        }
        
        try {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $stmt = $this->db->prepare("
                UPDATE users 
                SET password_reset_token = ?, password_reset_expires = ?
                WHERE email = ? AND status IN ('active', 'trial')
            ");
            
            $result = $stmt->execute([$token, $expires, $email]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'token' => $token];
            }
            
            return ['success' => false, 'error' => 'Email non trouvé'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Erreur base de données'];
        }
    }
    
    /**
     * Réinitialise le mot de passe
     */
    public function resetPassword($token, $newPassword)
    {
        if (!$this->db) {
            return ['success' => false, 'error' => 'Fonctionnalité non disponible'];
        }
        
        try {
            // Vérifier le token et sa validité
            $stmt = $this->db->prepare("
                SELECT id FROM users 
                WHERE password_reset_token = ? 
                AND password_reset_expires > NOW()
            ");
            
            $stmt->execute([$token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return ['success' => false, 'error' => 'Token invalide ou expiré'];
            }
            
            // Mettre à jour le mot de passe
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare("
                UPDATE users 
                SET password_hash = ?, password_reset_token = NULL, password_reset_expires = NULL
                WHERE id = ?
            ");
            
            $result = $stmt->execute([$passwordHash, $user['id']]);
            
            return ['success' => $result];
            
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Erreur base de données'];
        }
    }
    
    /**
     * Met à jour le profil utilisateur
     */
    public function updateProfile($userId, array $data)
    {
        if (!$this->db) {
            return ['success' => false, 'error' => 'Fonctionnalité non disponible'];
        }
        
        try {
            $stmt = $this->db->prepare("
                UPDATE users 
                SET first_name = ?, last_name = ?, company_name = ?, 
                    siret = ?, phone = ?, updated_at = NOW()
                WHERE id = ?
            ");
            
            $result = $stmt->execute([
                $data['first_name'] ?? '',
                $data['last_name'] ?? '',
                $data['company_name'] ?? '',
                $data['siret'] ?? '',
                $data['phone'] ?? '',
                $userId
            ]);
            
            return ['success' => $result];
            
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Erreur base de données'];
        }
    }
    
    /**
     * Change le mot de passe d'un utilisateur
     */
    public function changePassword($userId, $currentPassword, $newPassword)
    {
        if (!$this->db) {
            return ['success' => false, 'error' => 'Fonctionnalité non disponible'];
        }
        
        try {
            // Vérifier le mot de passe actuel
            $stmt = $this->db->prepare("SELECT password_hash FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
                return ['success' => false, 'error' => 'Mot de passe actuel incorrect'];
            }
            
            // Mettre à jour le mot de passe
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $result = $stmt->execute([$passwordHash, $userId]);
            
            return ['success' => $result];
            
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Erreur base de données'];
        }
    }
    
    /**
     * Suspend ou active un utilisateur (admin)
     */
    public function updateStatus($userId, $status)
    {
        if (!$this->db) {
            return ['success' => false, 'error' => 'Fonctionnalité non disponible'];
        }
        
        $validStatuses = ['active', 'suspended', 'expired', 'pending'];
        if (!in_array($status, $validStatuses)) {
            return ['success' => false, 'error' => 'Statut invalide'];
        }
        
        try {
            $stmt = $this->db->prepare("UPDATE users SET status = ? WHERE id = ?");
            $result = $stmt->execute([$status, $userId]);
            
            return ['success' => $result];
            
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Erreur base de données'];
        }
    }
    
    /**
     * Force le changement de mot de passe (admin)
     */
    public function forcePasswordChange($userId, $newPassword)
    {
        if (!$this->db) {
            return ['success' => false, 'error' => 'Fonctionnalité non disponible'];
        }
        
        try {
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $result = $stmt->execute([$passwordHash, $userId]);
            
            return ['success' => $result];
            
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Erreur base de données'];
        }
    }
    
    /**
     * Récupère tous les utilisateurs (admin)
     */
    public function getAll($limit = 50, $offset = 0, $search = '')
    {
        if (!$this->db) {
            return [];
        }
        
        try {
            $whereClause = '';
            $params = [];
            
            if (!empty($search)) {
                $whereClause = "WHERE email LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR company_name LIKE ?";
                $searchTerm = "%$search%";
                $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
            }
            
            $stmt = $this->db->prepare("
                SELECT id, email, first_name, last_name, company_name, 
                       role, status, email_verified, last_login, created_at
                FROM users 
                $whereClause
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?
            ");
            
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Compte le nombre total d'utilisateurs
     */
    public function count($search = '')
    {
        if (!$this->db) {
            return 2; // Mode démo
        }
        
        try {
            $whereClause = '';
            $params = [];
            
            if (!empty($search)) {
                $whereClause = "WHERE email LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR company_name LIKE ?";
                $searchTerm = "%$search%";
                $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
            }
            
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users $whereClause");
            $stmt->execute($params);
            
            return (int)$stmt->fetchColumn();
            
        } catch (PDOException $e) {
            return 0;
        }
    }
}
?>

