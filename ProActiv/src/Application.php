<?php
/**
 * Classe principale de l'application ProActiv
 * Architecture MVC simple pour hébergement mutualisé
 */

class Application
{
    private $config;
    private $db = null;
    private $router;
    private $request;
    
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->initializeTimezone();
        $this->initializeDatabase();
        $this->initializeRouter();
        $this->initializeRequest();
    }
    
    /**
     * Lance l'application
     */
    public function run()
    {
        // Vérification de la maintenance
        if ($this->config['app']['maintenance']) {
            $this->showMaintenancePage();
            return;
        }
        
        // Headers de sécurité
        $this->setSecurityHeaders();
        
        // Routage de la requête
        $this->router->dispatch($this->request);
    }
    
    /**
     * Initialise le fuseau horaire
     */
    private function initializeTimezone()
    {
        date_default_timezone_set($this->config['app']['timezone']);
    }
    
    /**
     * Initialise la base de données (optionnelle)
     */
    private function initializeDatabase()
    {
        // Base de données optionnelle en développement
        if (isset($this->config['database']['enabled']) && !$this->config['database']['enabled']) {
            return;
        }
        
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $this->config['database']['host'],
                $this->config['database']['port'],
                $this->config['database']['name'],
                $this->config['database']['charset']
            );
            
            $this->db = new PDO(
                $dsn,
                $this->config['database']['username'],
                $this->config['database']['password'],
                $this->config['database']['options']
            );
            
        } catch (PDOException $e) {
            if ($this->config['app']['debug']) {
                throw new Exception("Erreur de connexion base de données: " . $e->getMessage());
            } else {
                throw new Exception("Erreur de connexion à la base de données");
            }
        }
    }
    
    /**
     * Initialise le routeur
     */
    private function initializeRouter()
    {
        $this->router = new Router($this);
    }
    
    /**
     * Initialise la requête
     */
    private function initializeRequest()
    {
        $this->request = new Request();
    }
    
    /**
     * Headers de sécurité
     */
    private function setSecurityHeaders()
    {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
    
    /**
     * Page de maintenance
     */
    private function showMaintenancePage()
    {
        http_response_code(503);
        include $this->config['app']['paths']['templates'] . '/maintenance.php';
        exit;
    }
    
    /**
     * Getter pour la base de données
     */
    public function getDatabase()
    {
        return $this->db;
    }
    
    /**
     * Getter pour la configuration
     */
    public function getConfig()
    {
        return $this->config;
    }
}
