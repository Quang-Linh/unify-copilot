<?php
class LanguageService {
    private $currentLang = 'fr';
    private $translations = [];
    private $config;
    
       public function __construct()
    {
        $this->loadTranslations();
        $this->addMissingTranslations();
        $this->detectLanguage();
    }
    
    /**
     * Détecte la langue du navigateur
     */
    private function detectLanguage(): void
    {
        // Si une langue est déjà en session, l'utiliser
        if (isset($_SESSION['language'])) {
            $this->currentLang = $_SESSION['language'];
            return;
        }
        
        // Détection via l'en-tête HTTP Accept-Language
        $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        $supportedLangs = array_keys($this->getSupportedLanguages());
        
        // Parse des langues acceptées
        preg_match_all('/([a-z]{2})(?:-[A-Z]{2})?(?:;q=([0-9.]+))?/', $acceptLanguage, $matches);
        
        if (!empty($matches[1])) {
            foreach ($matches[1] as $lang) {
                if (in_array($lang, $supportedLangs)) {
                    $this->currentLang = $lang;
                    $_SESSION['language'] = $lang;
                    return;
                }
            }
        }
        
        // Langue par défaut : français
        $this->currentLang = 'fr';
        $_SESSION['language'] = 'fr';
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
    
    /**
     * Retourne les langues supportées
     */
    public function getSupportedLanguages()
    {
        return [
            'fr' => ['name' => 'Français', 'flag' => '🇫🇷'],
            'en' => ['name' => 'English', 'flag' => '🇬🇧'],
            'es' => ['name' => 'Español', 'flag' => '🇪🇸'],
            'de' => ['name' => 'Deutsch', 'flag' => '🇩🇪'],
            'it' => ['name' => 'Italiano', 'flag' => '🇮🇹'],
            'pt' => ['name' => 'Português', 'flag' => '🇵🇹'],
            'nl' => ['name' => 'Nederlands', 'flag' => '🇳🇱'],
            'pl' => ['name' => 'Polski', 'flag' => '🇵🇱'],
            'zh' => ['name' => '中文', 'flag' => '🇨🇳'],
            'vi' => ['name' => 'Tiếng Việt', 'flag' => '🇻🇳'],
            'he' => ['name' => 'עברית', 'flag' => '🇮🇱'],
            'ar' => ['name' => 'العربية', 'flag' => '🇸🇦'],
            'hi' => ['name' => 'हिन्दी', 'flag' => '🇮🇳']
        ];
    }
    
    /**
     * Ajoute les traductions manquantes
     */
    private function addMissingTranslations()
    {
        // Traductions françaises manquantes
        $this->translations['fr']['support'] = 'Support';
        $this->translations['fr']['designed_by'] = 'Pensé et conçu par';
        $this->translations['fr']['all_rights_reserved'] = 'Tous droits réservés';
        $this->translations['fr']['dashboard'] = 'Tableau de bord';
        
        // Traductions anglaises manquantes
        $this->translations['en']['support'] = 'Support';
        $this->translations['en']['designed_by'] = 'Designed and developed by';
        $this->translations['en']['all_rights_reserved'] = 'All rights reserved';
        $this->translations['en']['dashboard'] = 'Dashboard';
        
        // Traductions espagnoles manquantes
        $this->translations['es']['support'] = 'Soporte';
        $this->translations['es']['designed_by'] = 'Diseñado y desarrollado por';
        $this->translations['es']['all_rights_reserved'] = 'Todos los derechos reservados';
        $this->translations['es']['dashboard'] = 'Panel de control';
    }
}
