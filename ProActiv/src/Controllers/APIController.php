<?php
require_once 'BaseController.php';

class APIController extends BaseController {
    
    public function stats(Request $request) {
        $stats = [
            'users_count' => 1250,
            'documents_count' => 3420,
            'calculations_count' => 15600,
            'forum_posts' => 890,
            'last_update' => date('Y-m-d H:i:s')
        ];
        
        $this->logAction('api_stats_accessed');
        $this->jsonResponse(['success' => true, 'data' => $stats]);
    }
    
    public function calculate(Request $request) {
        $data = $request->getPostData();
        $type = $data['type'] ?? '';
        
        switch ($type) {
            case 'charges':
                $result = $this->calculateChargesSociales($data);
                break;
            case 'impots':
                $result = $this->calculateImpots($data);
                break;
            default:
                $this->jsonResponse(['error' => 'Type de calcul non supporté'], 400);
        }
        
        $this->logAction('api_calculation', ['type' => $type]);
        $this->jsonResponse(['success' => true, 'result' => $result]);
    }
    
    private function calculateChargesSociales($data) {
        $ca = floatval($data['ca'] ?? 0);
        return [
            'cotisations' => $ca * 0.22,
            'cfe' => $ca > 5000 ? 227 : 0,
            'formation' => $ca * 0.002
        ];
    }
    
    private function calculateImpots($data) {
        $revenu = floatval($data['revenu'] ?? 0);
        $abattement = $revenu * 0.34; // 34% d'abattement
        $revenu_imposable = $revenu - $abattement;
        
        return [
            'revenu_imposable' => $revenu_imposable,
            'impot_estime' => $revenu_imposable * 0.11 // Taux moyen estimé
        ];
    }
}
