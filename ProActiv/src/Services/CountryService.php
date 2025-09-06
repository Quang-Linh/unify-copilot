<?php

class CountryService {
    private $countries;
    
    public function __construct() {
        $this->initializeCountries();
    }
    
    /**
     * Obtient tous les pays supportés
     */
    public function getAllCountries() {
        return $this->countries;
    }
    
    /**
     * Obtient un pays par son code
     */
    public function getCountry($code) {
        return $this->countries[$code] ?? null;
    }
    
    /**
     * Obtient le pays par défaut (France)
     */
    public function getDefaultCountry() {
        return $this->countries['FR'];
    }
    
    /**
     * Détecte le pays depuis l'IP ou la langue du navigateur
     */
    public function detectCountry() {
        // Détection par langue du navigateur
        $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        
        if (strpos($acceptLanguage, 'fr') !== false) {
            return 'FR';
        } elseif (strpos($acceptLanguage, 'de') !== false) {
            return 'DE';
        } elseif (strpos($acceptLanguage, 'es') !== false) {
            return 'ES';
        } elseif (strpos($acceptLanguage, 'it') !== false) {
            return 'IT';
        } elseif (strpos($acceptLanguage, 'en') !== false) {
            return 'GB';
        }
        
        return 'FR'; // Par défaut
    }
    
    /**
     * Obtient les taux de charges sociales par pays
     */
    public function getSocialChargesRates($countryCode) {
        $country = $this->getCountry($countryCode);
        return $country ? $country['social_charges'] : $this->getDefaultCountry()['social_charges'];
    }
    
    /**
     * Obtient les tranches d'imposition par pays
     */
    public function getTaxBrackets($countryCode) {
        $country = $this->getCountry($countryCode);
        return $country ? $country['tax_brackets'] : $this->getDefaultCountry()['tax_brackets'];
    }
    
    /**
     * Obtient la devise du pays
     */
    public function getCurrency($countryCode) {
        $country = $this->getCountry($countryCode);
        return $country ? $country['currency'] : '€';
    }
    
    /**
     * Vérifie si un pays est supporté
     */
    public function isCountrySupported($countryCode) {
        return isset($this->countries[$countryCode]);
    }
    
    /**
     * Initialise la liste des pays supportés
     */
    private function initializeCountries() {
        $this->countries = [
            'FR' => [
                'code' => 'FR',
                'name' => 'France',
                'flag' => '🇫🇷',
                'currency' => '€',
                'currency_symbol' => '€',
                'legal_status' => 'Auto-entrepreneur / Micro-entreprise',
                'social_charges' => [
                    'services_bic' => 0.22,      // 22% pour services BIC
                    'services_bnc' => 0.22,      // 22% pour services BNC
                    'commerce' => 0.128,         // 12.8% pour commerce
                    'artisanat' => 0.22,         // 22% pour artisanat
                    'liberal' => 0.22,           // 22% pour professions libérales
                    'cfe_min' => 227,            // CFE minimum
                    'formation' => 0.002         // 0.2% formation professionnelle
                ],
                'tax_brackets' => [
                    ['min' => 0, 'max' => 10777, 'rate' => 0],
                    ['min' => 10777, 'max' => 27478, 'rate' => 0.11],
                    ['min' => 27478, 'max' => 78570, 'rate' => 0.30],
                    ['min' => 78570, 'max' => 168994, 'rate' => 0.41],
                    ['min' => 168994, 'max' => null, 'rate' => 0.45]
                ],
                'abatement_rates' => [
                    'services_bic' => 0.50,      // 50% d'abattement
                    'services_bnc' => 0.34,      // 34% d'abattement
                    'commerce' => 0.71,          // 71% d'abattement
                    'artisanat' => 0.50,         // 50% d'abattement
                    'liberal' => 0.34            // 34% d'abattement
                ],
                'thresholds' => [
                    'services' => 77700,         // Seuil services
                    'commerce' => 188700         // Seuil commerce
                ]
            ],
            'DE' => [
                'code' => 'DE',
                'name' => 'Allemagne',
                'flag' => '🇩🇪',
                'currency' => '€',
                'currency_symbol' => '€',
                'legal_status' => 'Kleinunternehmer / Freiberufler',
                'social_charges' => [
                    'services_bic' => 0.195,     // ~19.5% charges sociales
                    'services_bnc' => 0.195,
                    'commerce' => 0.195,
                    'artisanat' => 0.195,
                    'liberal' => 0.195,
                    'cfe_min' => 0,              // Pas de CFE en Allemagne
                    'formation' => 0             // Inclus dans les charges
                ],
                'tax_brackets' => [
                    ['min' => 0, 'max' => 10908, 'rate' => 0],
                    ['min' => 10908, 'max' => 62810, 'rate' => 0.14],
                    ['min' => 62810, 'max' => 277826, 'rate' => 0.42],
                    ['min' => 277826, 'max' => null, 'rate' => 0.45]
                ],
                'abatement_rates' => [
                    'services_bic' => 0.30,
                    'services_bnc' => 0.30,
                    'commerce' => 0.30,
                    'artisanat' => 0.30,
                    'liberal' => 0.30
                ],
                'thresholds' => [
                    'services' => 22000,         // Seuil Kleinunternehmer
                    'commerce' => 22000
                ]
            ],
            'ES' => [
                'code' => 'ES',
                'name' => 'Espagne',
                'flag' => '🇪🇸',
                'currency' => '€',
                'currency_symbol' => '€',
                'legal_status' => 'Autónomo',
                'social_charges' => [
                    'services_bic' => 0.30,      // ~30% charges sociales
                    'services_bnc' => 0.30,
                    'commerce' => 0.30,
                    'artisanat' => 0.30,
                    'liberal' => 0.30,
                    'cfe_min' => 294,            // Cotisation minimale mensuelle
                    'formation' => 0
                ],
                'tax_brackets' => [
                    ['min' => 0, 'max' => 12450, 'rate' => 0.19],
                    ['min' => 12450, 'max' => 20200, 'rate' => 0.24],
                    ['min' => 20200, 'max' => 35200, 'rate' => 0.30],
                    ['min' => 35200, 'max' => 60000, 'rate' => 0.37],
                    ['min' => 60000, 'max' => null, 'rate' => 0.47]
                ],
                'abatement_rates' => [
                    'services_bic' => 0.20,
                    'services_bnc' => 0.20,
                    'commerce' => 0.20,
                    'artisanat' => 0.20,
                    'liberal' => 0.20
                ],
                'thresholds' => [
                    'services' => 30000,
                    'commerce' => 30000
                ]
            ],
            'IT' => [
                'code' => 'IT',
                'name' => 'Italie',
                'flag' => '🇮🇹',
                'currency' => '€',
                'currency_symbol' => '€',
                'legal_status' => 'Partita IVA / Regime forfettario',
                'social_charges' => [
                    'services_bic' => 0.25,      // ~25% charges sociales
                    'services_bnc' => 0.25,
                    'commerce' => 0.25,
                    'artisanat' => 0.25,
                    'liberal' => 0.25,
                    'cfe_min' => 0,
                    'formation' => 0
                ],
                'tax_brackets' => [
                    ['min' => 0, 'max' => 15000, 'rate' => 0.23],
                    ['min' => 15000, 'max' => 28000, 'rate' => 0.27],
                    ['min' => 28000, 'max' => 55000, 'rate' => 0.38],
                    ['min' => 55000, 'max' => 75000, 'rate' => 0.41],
                    ['min' => 75000, 'max' => null, 'rate' => 0.43]
                ],
                'abatement_rates' => [
                    'services_bic' => 0.67,      // Régime forfettaire
                    'services_bnc' => 0.78,
                    'commerce' => 0.40,
                    'artisanat' => 0.67,
                    'liberal' => 0.78
                ],
                'thresholds' => [
                    'services' => 65000,
                    'commerce' => 65000
                ]
            ],
            'GB' => [
                'code' => 'GB',
                'name' => 'Royaume-Uni',
                'flag' => '🇬🇧',
                'currency' => '£',
                'currency_symbol' => '£',
                'legal_status' => 'Sole Trader',
                'social_charges' => [
                    'services_bic' => 0.12,      // National Insurance
                    'services_bnc' => 0.12,
                    'commerce' => 0.12,
                    'artisanat' => 0.12,
                    'liberal' => 0.12,
                    'cfe_min' => 0,
                    'formation' => 0
                ],
                'tax_brackets' => [
                    ['min' => 0, 'max' => 12570, 'rate' => 0],
                    ['min' => 12570, 'max' => 50270, 'rate' => 0.20],
                    ['min' => 50270, 'max' => 125140, 'rate' => 0.40],
                    ['min' => 125140, 'max' => null, 'rate' => 0.45]
                ],
                'abatement_rates' => [
                    'services_bic' => 0,          // Pas d'abattement forfaitaire
                    'services_bnc' => 0,
                    'commerce' => 0,
                    'artisanat' => 0,
                    'liberal' => 0
                ],
                'thresholds' => [
                    'services' => 85000,          // VAT threshold
                    'commerce' => 85000
                ]
            ]
        ];
    }
    
    /**
     * Obtient les informations légales spécifiques au pays
     */
    public function getLegalInfo($countryCode) {
        $country = $this->getCountry($countryCode);
        
        if (!$country) {
            return null;
        }
        
        return [
            'legal_status' => $country['legal_status'],
            'thresholds' => $country['thresholds'],
            'currency' => $country['currency_symbol'],
            'social_system' => $this->getSocialSystemInfo($countryCode),
            'tax_system' => $this->getTaxSystemInfo($countryCode)
        ];
    }
    
    /**
     * Informations sur le système social du pays
     */
    private function getSocialSystemInfo($countryCode) {
        $info = [
            'FR' => 'Régime micro-social simplifié avec paiement mensuel ou trimestriel',
            'DE' => 'Système de sécurité sociale allemand avec cotisations obligatoires',
            'ES' => 'Régime spécial des travailleurs autonomes (RETA)',
            'IT' => 'Gestione Separata INPS pour les professionnels indépendants',
            'GB' => 'National Insurance contributions pour les sole traders'
        ];
        
        return $info[$countryCode] ?? '';
    }
    
    /**
     * Informations sur le système fiscal du pays
     */
    private function getTaxSystemInfo($countryCode) {
        $info = [
            'FR' => 'Régime micro-fiscal avec abattement forfaitaire selon l\'activité',
            'DE' => 'Einkommensteuer (impôt sur le revenu) avec progression par tranches',
            'ES' => 'IRPF (Impuesto sobre la Renta) avec déductions spécifiques aux autonomes',
            'IT' => 'Regime forfettario avec taux fixe de 15% (5% les 5 premières années)',
            'GB' => 'Income Tax avec Personal Allowance et Self Assessment'
        ];
        
        return $info[$countryCode] ?? '';
    }
}

