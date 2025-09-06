<?php
/**
 * Classe Request pour gérer les requêtes HTTP
 * Simplifie l'accès aux données de requête
 */

class Request
{
    private $method;
    private $path;
    private $params = [];
    private $query = [];
    private $post = [];
    private $files = [];
    
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->path = $this->parsePath();
        $this->query = $_GET ?? [];
        $this->post = $_POST ?? [];
        $this->files = $_FILES ?? [];
    }
    
    /**
     * Parse le chemin de l'URL
     */
    private function parsePath()
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        
        // Retire le préfixe du script (ex: /ProActiv/public)
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $scriptDir = dirname($scriptName);
        
        if ($scriptDir !== '/' && strpos($path, $scriptDir) === 0) {
            $path = substr($path, strlen($scriptDir));
        }
        
        // Nettoie le chemin
        $path = '/' . trim($path, '/');
        
        // Si le path est juste le nom du script (index.php), on retourne /
        if ($path === '/index.php' || $path === '/') {
            return '/';
        }
        
        return $path;
    }
    
    /**
     * Getters
     */
    public function getMethod()
    {
        return $this->method;
    }
    
    public function getPath()
    {
        return $this->path;
    }
    
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
    }
    
    public function setParams(array $params)
    {
        $this->params = $params;
    }
    
    public function getParam($key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }
    
    public function getQuery($key = null, $default = null)
    {
        if ($key === null) {
            return $this->query;
        }
        return $this->query[$key] ?? $default;
    }
    
    public function getPost($key = null, $default = null)
    {
        if ($key === null) {
            return $this->post;
        }
        return $this->post[$key] ?? $default;
    }
    
    public function getFile($key)
    {
        return $this->files[$key] ?? null;
    }
    
    public function isPost()
    {
        return $this->method === 'POST';
    }
    
    public function isGet()
    {
        return $this->method === 'GET';
    }
    
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Validation et nettoyage des données
     */
    public function validate($rules)
    {
        $errors = [];
        $data = array_merge($this->post, $this->query);
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            
            if (strpos($rule, 'required') !== false && empty($value)) {
                $errors[$field] = "Le champ $field est requis";
                continue;
            }
            
            if (strpos($rule, 'email') !== false && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "Le champ $field doit être un email valide";
            }
            
            if (preg_match('/min:(\d+)/', $rule, $matches) && strlen($value) < $matches[1]) {
                $errors[$field] = "Le champ $field doit contenir au moins {$matches[1]} caractères";
            }
            
            if (preg_match('/max:(\d+)/', $rule, $matches) && strlen($value) > $matches[1]) {
                $errors[$field] = "Le champ $field ne peut pas dépasser {$matches[1]} caractères";
            }
        }
        
        return $errors;
    }
    
    /**
     * Protection CSRF
     */
    public function generateCsrfToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public function verifyCsrfToken()
    {
        $token = $this->getPost('csrf_token');
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
