<?php

class CountryController extends BaseController {
    private $countryService;
    
    public function __construct($config) {
        parent::__construct($config);
        $this->countryService = new CountryService();
    }
    
    /**
     * Change le pays de l'utilisateur
     */
    public function change() {
        $countryCode = $this->request->getParam('country');
        
        if (!$countryCode || !$this->countryService->isCountrySupported($countryCode)) {
            // Redirection vers la page d'accueil si pays non supporté
            header('Location: /ProActiv/');
            exit;
        }
        
        // Stockage du pays en session
        $_SESSION['selected_country'] = $countryCode;
        
        // Redirection vers la page précédente ou l'accueil
        $referer = $_SERVER['HTTP_REFERER'] ?? '/ProActiv/';
        header('Location: ' . $referer);
        exit;
    }
    
    /**
     * API pour obtenir les informations d'un pays
     */
    public function getCountryInfo() {
        $countryCode = $this->request->getParam('country');
        
        if (!$countryCode || !$this->countryService->isCountrySupported($countryCode)) {
            http_response_code(404);
            echo json_encode(['error' => 'Pays non supporté']);
            exit;
        }
        
        $country = $this->countryService->getCountry($countryCode);
        $legalInfo = $this->countryService->getLegalInfo($countryCode);
        
        header('Content-Type: application/json');
        echo json_encode([
            'country' => $country,
            'legal_info' => $legalInfo,
            'social_charges' => $this->countryService->getSocialChargesRates($countryCode),
            'tax_brackets' => $this->countryService->getTaxBrackets($countryCode),
            'currency' => $this->countryService->getCurrency($countryCode)
        ]);
        exit;
    }
    
    /**
     * Page d'information sur les pays supportés
     */
    public function index() {
        $countries = $this->countryService->getAllCountries();
        $currentCountry = $_SESSION['selected_country'] ?? $this->countryService->detectCountry();
        
        $this->render('countries/index', [
            'countries' => $countries,
            'current_country' => $this->countryService->getCountry($currentCountry),
            'page_title' => 'Pays supportés'
        ]);
    }
}

