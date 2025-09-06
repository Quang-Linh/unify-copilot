<?php
/**
 * Contrôleur pour la gestion des langues
 */

class LanguageController extends BaseController
{
    /**
     * Change la langue de l'utilisateur
     */
    public function setLanguage()
    {
        $lang = $this->request->getParam('lang');
        
        if ($lang && in_array($lang, array_keys($this->app->getLanguageService()->getSupportedLanguages()))) {
            $this->app->getLanguageService()->setLanguage($lang);
        }
        
        // Redirection vers la page précédente ou l'accueil
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header('Location: ' . $referer);
        exit;
    }
}
?>

