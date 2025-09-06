#!/bin/bash

echo "🚀 Construction automatique de ProActiv - Version complète"
echo "==========================================================="

# Créer tous les fichiers du système JWT
cat > src/Services/JWTService.php << 'JWT_END'
<?php
class JWTService {
    private $secretKey;
    private $algorithm = 'HS256';
    private $issuer = 'ProActiv';
    private $expiration = 3600;
    
    public function __construct($config) {
        $this->secretKey = $config['security']['jwt_secret'] ?? JWT_SECRET;
    }
    
    public function generateToken(array $payload, int $expiration = null): string {
        $expiration = $expiration ?? $this->expiration;
        $header = ['typ' => 'JWT', 'alg' => $this->algorithm];
        $payload = array_merge($payload, [
            'iss' => $this->issuer, 'iat' => time(), 'exp' => time() + $expiration, 'jti' => uniqid()
        ]);
        
        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));
        $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, $this->secretKey, true);
        $signatureEncoded = $this->base64UrlEncode($signature);
        
        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }
    
    public function verifyToken(string $token): array {
        $parts = explode('.', $token);
        if (count($parts) !== 3) throw new Exception('Token JWT invalide');
        
        [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;
        $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, $this->secretKey, true);
        $expectedSignature = $this->base64UrlEncode($signature);
        
        if (!hash_equals($expectedSignature, $signatureEncoded)) {
            throw new Exception('Signature JWT invalide');
        }
        
        $payload = json_decode($this->base64UrlDecode($payloadEncoded), true);
        if ($payload['exp'] < time()) throw new Exception('Token JWT expiré');
        
        return $payload;
    }
    
    private function base64UrlEncode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    private function base64UrlDecode(string $data): string {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}
JWT_END

# Système d'audit
cat > src/Services/AuditService.php << 'AUDIT_END'
<?php
class AuditService {
    private $db;
    private $config;
    
    public function __construct($db, $config) {
        $this->db = $db;
        $this->config = $config;
    }
    
    public function logEvent(string $action, array $data = [], int $userId = null): void {
        if (!$this->db) {
            error_log("AUDIT: $action - " . json_encode($data));
            return;
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO audit_events (user_id, action, data, ip_address, user_agent, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $userId,
                $action,
                json_encode($data),
                $_SERVER['REMOTE_ADDR'] ?? 'CLI',
                $_SERVER['HTTP_USER_AGENT'] ?? 'CLI'
            ]);
        } catch (Exception $e) {
            error_log("Audit error: " . $e->getMessage());
        }
    }
    
    public function getEvents(int $limit = 100, int $offset = 0): array {
        if (!$this->db) return [];
        
        try {
            $stmt = $this->db->prepare("
                SELECT ae.*, u.username 
                FROM audit_events ae 
                LEFT JOIN users u ON ae.user_id = u.id 
                ORDER BY ae.created_at DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$limit, $offset]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Audit read error: " . $e->getMessage());
            return [];
        }
    }
}
AUDIT_END

# Service multilingue
cat > src/Services/LanguageService.php << 'LANG_END'
<?php
class LanguageService {
    private $currentLang = 'fr';
    private $translations = [];
    private $config;
    
    public function __construct($config) {
        $this->config = $config;
        $this->loadTranslations();
    }
    
    public function setLanguage(string $lang): void {
        if (in_array($lang, ['fr', 'en', 'es'])) {
            $this->currentLang = $lang;
            $_SESSION['language'] = $lang;
        }
    }
    
    public function getCurrentLanguage(): string {
        return $_SESSION['language'] ?? $this->currentLang;
    }
    
    public function translate(string $key, array $params = []): string {
        $lang = $this->getCurrentLanguage();
        $translation = $this->translations[$lang][$key] ?? $key;
        
        foreach ($params as $placeholder => $value) {
            $translation = str_replace('{' . $placeholder . '}', $value, $translation);
        }
        
        return $translation;
    }
    
    private function loadTranslations(): void {
        $this->translations = [
            'fr' => [
                'welcome' => 'Bienvenue sur ProActiv',
                'login' => 'Connexion',
                'register' => 'Inscription',
                'dashboard' => 'Tableau de bord',
                'calculators' => 'Calculateurs',
                'forum' => 'Forum',
                'documents' => 'Documents',
                'profile' => 'Profil',
                'logout' => 'Déconnexion',
                'home' => 'Accueil',
                'about' => 'À propos',
                'contact' => 'Contact',
                'admin' => 'Administration'
            ],
            'en' => [
                'welcome' => 'Welcome to ProActiv',
                'login' => 'Login',
                'register' => 'Register',
                'dashboard' => 'Dashboard',
                'calculators' => 'Calculators',
                'forum' => 'Forum',
                'documents' => 'Documents',
                'profile' => 'Profile',
                'logout' => 'Logout',
                'home' => 'Home',
                'about' => 'About',
                'contact' => 'Contact',
                'admin' => 'Admin'
            ],
            'es' => [
                'welcome' => 'Bienvenido a ProActiv',
                'login' => 'Iniciar sesión',
                'register' => 'Registrarse',
                'dashboard' => 'Panel',
                'calculators' => 'Calculadoras',
                'forum' => 'Foro',
                'documents' => 'Documentos',
                'profile' => 'Perfil',
                'logout' => 'Cerrar sesión',
                'home' => 'Inicio',
                'about' => 'Acerca de',
                'contact' => 'Contacto',
                'admin' => 'Admin'
            ]
        ];
    }
}
LANG_END

echo "✅ Services JWT, Audit et Multilingue créés"

