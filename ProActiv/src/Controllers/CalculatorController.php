<?php

require_once 'BaseController.php';
require_once __DIR__ . '/../Services/CountryService.php';

class CalculatorController extends BaseController {
    private $countryService;
    
    public function __construct($config) {
        parent::__construct($config);
        $this->countryService = new CountryService();
    }
    
    public function index(Request $request) {
        $this->requireAuth();
        $this->logAction('calculators_view');
        
        // Obtenir le pays sélectionné
        $selectedCountry = $_SESSION['selected_country'] ?? $this->countryService->detectCountry();
        $country = $this->countryService->getCountry($selectedCountry);
        
        $this->render('calculators/index', [
            'title' => 'Calculateurs',
            'calculators' => $this->getAvailableCalculators(),
            'current_country' => $country
        ]);
    }
    
    /**
     * Calculateur de charges sociales
     */
    public function chargesSociales(Request $request) {
        $this->requireAuth();
        
        // Obtenir le pays sélectionné
        $selectedCountry = $_SESSION['selected_country'] ?? $this->countryService->detectCountry();
        $country = $this->countryService->getCountry($selectedCountry);
        $socialCharges = $this->countryService->getSocialChargesRates($selectedCountry);
        
        if ($request->isPost()) {
            return $this->calculateCharges($request);
        }
        
        $this->render('calculators/charges-sociales', [
            'title' => 'Calculateur de charges sociales',
            'csrf_token' => $this->generateCSRFToken(),
            'activity_types' => $this->getActivityTypes(),
            'regimes' => $this->getRegimes(),
            'current_country' => $country,
            'social_charges' => $socialCharges
        ]);
    }
    
    /**
     * Calculateur d'impôts
     */
    public function impots(Request $request) {
        $this->requireAuth();
        
        // Obtenir le pays sélectionné
        $selectedCountry = $_SESSION['selected_country'] ?? $this->countryService->detectCountry();
        $country = $this->countryService->getCountry($selectedCountry);
        $taxBrackets = $this->countryService->getTaxBrackets($selectedCountry);
        
        if ($request->isPost()) {
            return $this->calculateImpots($request);
        }
        
        $this->render('calculators/impots', [
            'title' => 'Calculateur d\'impôts',
            'csrf_token' => $this->generateCSRFToken(),
            'activity_types' => $this->getActivityTypes(),
            'tax_options' => $this->getTaxOptions(),
            'current_country' => $country,
            'tax_brackets' => $taxBrackets
        ]);
    }
    
    /**
     * Calculateur de revenus nets
     */
    public function revenus(Request $request) {
        $this->requireAuth();
        
        if ($request->isPost()) {
            return $this->calculateRevenus($request);
        }
        
        $this->render('calculators/revenus', [
            'title' => 'Calculateur de revenus nets',
            'csrf_token' => $this->generateCSRFToken(),
            'activity_types' => $this->getActivityTypes()
        ]);
    }
    
    /**
     * Aide à la tarification
     */
    public function tarification(Request $request) {
        $this->requireAuth();
        
        if ($request->isPost()) {
            return $this->calculateTarification($request);
        }
        
        $this->render('calculators/tarification', [
            'title' => 'Aide à la tarification',
            'csrf_token' => $this->generateCSRFToken(),
            'activity_types' => $this->getActivityTypes()
        ]);
    }
    
    /**
     * Calcul des charges sociales
     */
    public function calculateCharges(Request $request) {
        if (!$this->verifyCSRFToken($request->getPost('csrf_token', ''))) {
            $this->jsonResponse(['error' => 'Token de sécurité invalide'], 400);
            return;
        }
        
        $ca = floatval($request->getPost('chiffre_affaires', 0));
        $activityType = $request->getPost('activity_type', 'liberal');
        $regime = $request->getPost('regime', 'micro');
        $period = $request->getPost('period', 'monthly');
        
        // Validation
        if ($ca <= 0) {
            $this->jsonResponse(['error' => 'Le chiffre d\'affaires doit être supérieur à 0'], 400);
            return;
        }
        
        $results = $this->computeChargesSociales($ca, $activityType, $regime, $period);
        
        // Sauvegarde du calcul
        $this->saveCalculation('charges_sociales', [
            'chiffre_affaires' => $ca,
            'activity_type' => $activityType,
            'regime' => $regime,
            'period' => $period,
            'results' => $results
        ]);
        
        $this->logAction('charges_calculated', [
            'ca' => $ca, 
            'activity_type' => $activityType, 
            'regime' => $regime
        ]);
        
        $this->jsonResponse(['success' => true, 'results' => $results]);
    }
    
    /**
     * Calcul des impôts
     */
    public function calculateImpots(Request $request) {
        if (!$this->verifyCSRFToken($request->getPost('csrf_token', ''))) {
            $this->jsonResponse(['error' => 'Token de sécurité invalide'], 400);
            return;
        }
        
        $ca = floatval($request->getPost('chiffre_affaires', 0));
        $activityType = $request->getPost('activity_type', 'liberal');
        $taxOption = $request->getPost('tax_option', 'bareme');
        $familyQuotient = floatval($request->getPost('family_quotient', 1));
        $otherIncome = floatval($request->getPost('other_income', 0));
        
        if ($ca <= 0) {
            $this->jsonResponse(['error' => 'Le chiffre d\'affaires doit être supérieur à 0'], 400);
            return;
        }
        
        $results = $this->computeImpots($ca, $activityType, $taxOption, $familyQuotient, $otherIncome);
        
        $this->saveCalculation('impots', [
            'chiffre_affaires' => $ca,
            'activity_type' => $activityType,
            'tax_option' => $taxOption,
            'family_quotient' => $familyQuotient,
            'other_income' => $otherIncome,
            'results' => $results
        ]);
        
        $this->logAction('impots_calculated', ['ca' => $ca, 'activity_type' => $activityType]);
        
        $this->jsonResponse(['success' => true, 'results' => $results]);
    }
    
    /**
     * Calcul des revenus nets
     */
    public function calculateRevenus(Request $request) {
        if (!$this->verifyCSRFToken($request->getPost('csrf_token', ''))) {
            $this->jsonResponse(['error' => 'Token de sécurité invalide'], 400);
            return;
        }
        
        $ca = floatval($request->getPost('chiffre_affaires', 0));
        $activityType = $request->getPost('activity_type', 'liberal');
        $charges = floatval($request->getPost('charges_deductibles', 0));
        
        if ($ca <= 0) {
            $this->jsonResponse(['error' => 'Le chiffre d\'affaires doit être supérieur à 0'], 400);
            return;
        }
        
        $results = $this->computeRevenus($ca, $activityType, $charges);
        
        $this->saveCalculation('revenus', [
            'chiffre_affaires' => $ca,
            'activity_type' => $activityType,
            'charges_deductibles' => $charges,
            'results' => $results
        ]);
        
        $this->logAction('revenus_calculated', ['ca' => $ca, 'activity_type' => $activityType]);
        
        $this->jsonResponse(['success' => true, 'results' => $results]);
    }
    
    /**
     * Calcul de tarification
     */
    public function calculateTarification(Request $request) {
        if (!$this->verifyCSRFToken($request->getPost('csrf_token', ''))) {
            $this->jsonResponse(['error' => 'Token de sécurité invalide'], 400);
            return;
        }
        
        $targetRevenu = floatval($request->getPost('target_revenu', 0));
        $activityType = $request->getPost('activity_type', 'liberal');
        $workingDays = intval($request->getPost('working_days', 220));
        $hoursPerDay = floatval($request->getPost('hours_per_day', 7));
        
        if ($targetRevenu <= 0) {
            $this->jsonResponse(['error' => 'Le revenu cible doit être supérieur à 0'], 400);
            return;
        }
        
        $results = $this->computeTarification($targetRevenu, $activityType, $workingDays, $hoursPerDay);
        
        $this->saveCalculation('tarification', [
            'target_revenu' => $targetRevenu,
            'activity_type' => $activityType,
            'working_days' => $workingDays,
            'hours_per_day' => $hoursPerDay,
            'results' => $results
        ]);
        
        $this->logAction('tarification_calculated', ['target_revenu' => $targetRevenu]);
        
        $this->jsonResponse(['success' => true, 'results' => $results]);
    }
    
    /**
     * Historique des calculs
     */
    public function history(Request $request) {
        $this->requireAuth();
        
        $calculations = $this->getUserCalculations();
        
        $this->render('calculators/history', [
            'title' => 'Historique des calculs',
            'calculations' => $calculations
        ]);
    }
    
    // ===== MÉTHODES DE CALCUL =====
    
    private function computeChargesSociales($ca, $activityType, $regime, $period) {
        $results = [];
        
        // Obtenir le pays sélectionné et ses taux
        $selectedCountry = $_SESSION['selected_country'] ?? $this->countryService->detectCountry();
        $socialCharges = $this->countryService->getSocialChargesRates($selectedCountry);
        $country = $this->countryService->getCountry($selectedCountry);
        
        // Mapping des types d'activité
        $activityMapping = [
            'liberal' => 'services_bnc',
            'commercial' => 'commerce', 
            'artisanal' => 'artisanat',
            'services' => 'services_bic'
        ];
        
        $chargeKey = $activityMapping[$activityType] ?? 'services_bnc';
        $tauxCotisations = $socialCharges[$chargeKey] ?? 0.22;
        
        // Cotisations sociales
        $results['cotisations_sociales'] = $ca * $tauxCotisations;
        
        // CFE (Cotisation Foncière des Entreprises) - varie selon le pays
        $results['cfe'] = $ca > 5000 ? ($socialCharges['cfe_min'] ?? 0) : 0;
        
        // Formation professionnelle
        $results['formation'] = $ca * ($socialCharges['formation'] ?? 0);
        
        // Total des charges
        $results['total_charges'] = $results['cotisations_sociales'] + $results['cfe'] + $results['formation'];
        
        // Pourcentage du CA
        $results['pourcentage_ca'] = ($results['total_charges'] / $ca) * 100;
        
        // Informations sur le pays
        $results['country_info'] = [
            'name' => $country['name'],
            'currency' => $country['currency_symbol'],
            'legal_status' => $country['legal_status'],
            'rate_used' => $tauxCotisations * 100
        ];
        
        // Détails par période
        if ($period === 'monthly') {
            $results['cotisations_sociales_mensuel'] = $results['cotisations_sociales'] / 12;
            $results['total_charges_mensuel'] = $results['total_charges'] / 12;
        }
        
        return $results;
    }
    
    private function computeImpots($ca, $activityType, $taxOption, $familyQuotient, $otherIncome) {
        $results = [];
        
        // Obtenir le pays sélectionné et ses données fiscales
        $selectedCountry = $_SESSION['selected_country'] ?? $this->countryService->detectCountry();
        $country = $this->countryService->getCountry($selectedCountry);
        $taxBrackets = $this->countryService->getTaxBrackets($selectedCountry);
        
        // Mapping des types d'activité pour les abattements
        $activityMapping = [
            'liberal' => 'services_bnc',
            'commercial' => 'commerce', 
            'artisanal' => 'artisanat',
            'services' => 'services_bic'
        ];
        
        $abatementKey = $activityMapping[$activityType] ?? 'services_bnc';
        $abattement = $country['abatement_rates'][$abatementKey] ?? 0.34;
        
        // Bénéfice imposable
        $beneficeImposable = $ca * (1 - $abattement);
        $results['benefice_imposable'] = $beneficeImposable;
        $results['abattement'] = $ca * $abattement;
        $results['abattement_rate'] = $abattement * 100;
        
        // Revenu total imposable
        $revenuTotal = $beneficeImposable + $otherIncome;
        $results['revenu_total'] = $revenuTotal;
        
        // Informations sur le pays
        $results['country_info'] = [
            'name' => $country['name'],
            'currency' => $country['currency_symbol'],
            'legal_status' => $country['legal_status']
        ];
        
        if ($taxOption === 'bareme') {
            // Barème progressif selon le pays
            $results['impot_bareme'] = $this->calculateBaremeProgressif($revenuTotal, $familyQuotient, $taxBrackets);
            $results['tax_brackets_used'] = $taxBrackets;
        } else {
            // Versement libératoire (option micro) - taux français par défaut
            $tauxVersement = [
                'liberal' => 0.022,     // 2.2%
                'commercial' => 0.01,   // 1%
                'artisanal' => 0.018    // 1.8%
            ];
            
            $taux = $tauxVersement[$activityType] ?? 0.022;
            $results['versement_liberatoire'] = $ca * $taux;
        }
        
        return $results;
    }
    
    private function computeRevenus($ca, $activityType, $chargesDeductibles) {
        $results = [];
        
        // Calcul des charges sociales
        $chargesSociales = $this->computeChargesSociales($ca, $activityType, 'micro', 'annual');
        
        // Calcul des impôts
        $impots = $this->computeImpots($ca, $activityType, 'bareme', 1, 0);
        
        // Revenu brut
        $results['revenu_brut'] = $ca;
        
        // Charges totales
        $results['charges_sociales'] = $chargesSociales['total_charges'];
        $results['impots'] = $impots['impot_bareme'] ?? 0;
        $results['charges_deductibles'] = $chargesDeductibles;
        $results['total_charges'] = $results['charges_sociales'] + $results['impots'] + $chargesDeductibles;
        
        // Revenu net
        $results['revenu_net'] = $ca - $results['total_charges'];
        $results['revenu_net_mensuel'] = $results['revenu_net'] / 12;
        $results['revenu_net_horaire'] = $results['revenu_net'] / (220 * 7); // 220 jours, 7h/jour
        
        // Taux de charges
        $results['taux_charges'] = ($results['total_charges'] / $ca) * 100;
        
        return $results;
    }
    
    private function computeTarification($targetRevenu, $activityType, $workingDays, $hoursPerDay) {
        $results = [];
        
        // Heures travaillées par an
        $heuresAnnuelles = $workingDays * $hoursPerDay;
        $results['heures_annuelles'] = $heuresAnnuelles;
        
        // Estimation du CA nécessaire (en tenant compte des charges)
        $tauxCharges = [
            'liberal' => 0.45,      // ~45% de charges totales
            'commercial' => 0.35,   // ~35% de charges totales
            'artisanal' => 0.42     // ~42% de charges totales
        ];
        
        $taux = $tauxCharges[$activityType] ?? 0.45;
        $caNecessaire = $targetRevenu / (1 - $taux);
        
        $results['ca_necessaire'] = $caNecessaire;
        $results['taux_charges_estime'] = $taux * 100;
        
        // Tarifs recommandés
        $results['tarif_horaire'] = $caNeccessaire / $heuresAnnuelles;
        $results['tarif_journalier'] = $results['tarif_horaire'] * $hoursPerDay;
        $results['tarif_mensuel'] = $caNeccessaire / 12;
        
        // Objectifs par période
        $results['objectif_ca_mensuel'] = $caNeccessaire / 12;
        $results['objectif_ca_hebdomadaire'] = $caNeccessaire / 52;
        $results['objectif_ca_journalier'] = $caNeccessaire / $workingDays;
        
        return $results;
    }
    
    private function calculateBaremeProgressif($revenu, $quotientFamilial, $taxBrackets = null) {
        // Utiliser les tranches du pays ou les tranches françaises par défaut
        $tranches = $taxBrackets ?? [
            ['min' => 0, 'max' => 11294, 'rate' => 0],
            ['min' => 11294, 'max' => 28797, 'rate' => 0.11],
            ['min' => 28797, 'max' => 82341, 'rate' => 0.30],
            ['min' => 82341, 'max' => 177106, 'rate' => 0.41],
            ['min' => 177106, 'max' => null, 'rate' => 0.45]
        ];
        
        $revenuParPart = $revenu / $quotientFamilial;
        $impotParPart = 0;
        
        foreach ($tranches as $tranche) {
            if ($revenuParPart > $tranche['min']) {
                $maxTranche = $tranche['max'] ?? PHP_INT_MAX;
                $baseImposable = min($revenuParPart, $maxTranche) - $tranche['min'];
                $impotParPart += $baseImposable * $tranche['rate'];
            }
        }
        
        return $impotParPart * $quotientFamilial;
    }
    
    // ===== MÉTHODES UTILITAIRES =====
    
    private function getAvailableCalculators() {
        return [
            [
                'id' => 'charges-sociales',
                'title' => 'Charges sociales',
                'description' => 'Calculez vos cotisations sociales selon votre activité',
                'icon' => 'fas fa-calculator',
                'url' => '/calculators/charges-sociales'
            ],
            [
                'id' => 'impots',
                'title' => 'Impôts sur le revenu',
                'description' => 'Estimez vos impôts avec les abattements forfaitaires',
                'icon' => 'fas fa-percentage',
                'url' => '/calculators/impots'
            ],
            [
                'id' => 'revenus',
                'title' => 'Revenus nets',
                'description' => 'Calculez votre revenu net après charges et impôts',
                'icon' => 'fas fa-euro-sign',
                'url' => '/calculators/revenus'
            ],
            [
                'id' => 'tarification',
                'title' => 'Aide à la tarification',
                'description' => 'Déterminez vos tarifs pour atteindre vos objectifs',
                'icon' => 'fas fa-tags',
                'url' => '/calculators/tarification'
            ]
        ];
    }
    
    private function getActivityTypes() {
        return [
            'liberal' => 'Profession libérale',
            'commercial' => 'Activité commerciale',
            'artisanal' => 'Activité artisanale'
        ];
    }
    
    private function getRegimes() {
        return [
            'micro' => 'Micro-entreprise',
            'reel' => 'Régime réel'
        ];
    }
    
    private function getTaxOptions() {
        return [
            'bareme' => 'Barème progressif',
            'versement' => 'Versement libératoire'
        ];
    }
    
    private function saveCalculation($type, $data) {
        // En mode démonstration, on ne sauvegarde pas en base
        if (!$this->db) {
            return true;
        }
        
        // Avec base de données
        $stmt = $this->db->prepare("
            INSERT INTO calculations (user_id, type, data, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        
        return $stmt->execute([
            $this->getCurrentUserId(),
            $type,
            json_encode($data)
        ]);
    }
    
    private function getUserCalculations() {
        // Mode démonstration
        if (!$this->db) {
            return [
                [
                    'id' => 1,
                    'type' => 'charges_sociales',
                    'created_at' => '2025-08-27 10:30:00',
                    'data' => ['chiffre_affaires' => 5000, 'activity_type' => 'liberal']
                ],
                [
                    'id' => 2,
                    'type' => 'impots',
                    'created_at' => '2025-08-26 15:20:00',
                    'data' => ['chiffre_affaires' => 4500, 'activity_type' => 'liberal']
                ]
            ];
        }
        
        // Avec base de données
        $stmt = $this->db->prepare("
            SELECT * FROM calculations 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT 50
        ");
        $stmt->execute([$this->getCurrentUserId()]);
        
        return $stmt->fetchAll();
    }
}
