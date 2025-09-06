<?php
require_once 'BaseController.php';

class DocumentController extends BaseController {
    
    public function index(Request $request) {
        $this->requireAuth();
        $this->logAction('documents_view');
        
        $documents = $this->getUserDocuments();
        
        $this->render('documents/index', [
            'title' => 'Gestion des documents',
            'documents' => $documents
        ]);
    }
    
    public function create(Request $request) {
        $this->requireAuth();
        
        $type = $request->getQuery('type', 'invoice');
        
        $this->render('documents/create', [
            'title' => 'Créer un nouveau document',
            'type' => $type,
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }
    
    public function generate(Request $request) {
        $this->requireAuth();
        
        if (!$this->verifyCSRFToken($request->getPost('csrf_token', ''))) {
            $this->jsonResponse(['error' => 'Token de sécurité invalide'], 400);
            return;
        }
        
        $type = $request->getPost('type', 'invoice');
        $data = $request->getPostData();
        
        // Validation des données (à implémenter)
        
        // Génération du PDF
        $pdfPath = $this->generatePdf($type, $data);
        
        // Sauvegarde du document
        $this->saveDocument($type, $data, $pdfPath);
        
        $this->logAction('document_generated', ['type' => $type]);
        
        $this->jsonResponse(['success' => true, 'path' => $pdfPath]);
    }
    
    private function generatePdf($type, $data) {
        require_once("/usr/share/php/fpdf/fpdf.php");

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont("Arial", "B", 16);

        $pdf->Cell(40, 10, "ProActiv");
        $pdf->Ln(20);

        $pdf->SetFont("Arial", "B", 12);
        $pdf->Cell(0, 10, strtoupper($type) . " #" . uniqid(), 0, 1, "C");
        $pdf->Ln(10);

        $pdf->SetFont("Arial", "", 12);
        $pdf->Cell(50, 10, "Client:");
        $pdf->Cell(0, 10, $data["client_name"]);
        $pdf->Ln();
        $pdf->Cell(50, 10, "Email:");
        $pdf->Cell(0, 10, $data["client_email"]);
        $pdf->Ln(20);

        $pdf->SetFont("Arial", "B", 12);
        $pdf->Cell(100, 10, "Description", 1);
        $pdf->Cell(30, 10, "Quantite", 1);
        $pdf->Cell(30, 10, "Prix U.", 1);
        $pdf->Cell(30, 10, "Total", 1);
        $pdf->Ln();

        $pdf->SetFont("Arial", "", 12);
        $subtotal = 0;
        if (isset($data["items"])) {
            foreach ($data["items"] as $item) {
                $quantity = (float)($item["quantity"] ?? 0);
                $price = (float)($item["price"] ?? 0);
                $total = $quantity * $price;
                $subtotal += $total;

                $pdf->Cell(100, 10, $item["description"], 1);
                $pdf->Cell(30, 10, $quantity, 1);
                $pdf->Cell(30, 10, number_format($price, 2, ",", " ") . " EUR", 1);
                $pdf->Cell(30, 10, number_format($total, 2, ",", " ") . " EUR", 1);
                $pdf->Ln();
            }
        }

        $vat = $subtotal * 0.20;
        $total = $subtotal + $vat;

        $pdf->Ln(10);
        $pdf->Cell(130, 10, "");
        $pdf->Cell(30, 10, "Sous-total");
        $pdf->Cell(30, 10, number_format($subtotal, 2, ",", " ") . " EUR", 1);
        $pdf->Ln();
        $pdf->Cell(130, 10, "");
        $pdf->Cell(30, 10, "TVA (20%)");
        $pdf->Cell(30, 10, number_format($vat, 2, ",", " ") . " EUR", 1);
        $pdf->Ln();
        $pdf->Cell(130, 10, "");
        $pdf->SetFont("Arial", "B", 12);
        $pdf->Cell(30, 10, "Total TTC");
        $pdf->Cell(30, 10, number_format($total, 2, ",", " ") . " EUR", 1);

        $dir = PROACTIV_PUBLIC . "/documents";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $filename = $type . "_" . uniqid() . ".pdf";
        $path = $dir . "/" . $filename;
        $pdf->Output("F", $path);

        return "/documents/" . $filename;
    }
    
    private function saveDocument($type, $data, $path) {
        // En mode démo, on ne sauvegarde pas en base
        if (!$this->db) {
            return true;
        }
        
        $stmt = $this->db->prepare("
            INSERT INTO documents (user_id, type, data, path, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        return $stmt->execute([
            $this->getCurrentUserId(),
            $type,
            json_encode($data),
            $path
        ]);
    }
    
    private function getUserDocuments() {
        // Mode démo
        if (!$this->db) {
            return [
                [
                    'id' => 1,
                    'type' => 'invoice',
                    'created_at' => '2025-08-27 10:30:00',
                    'data' => ['client_name' => 'Client A', 'amount' => 1200],
                    'path' => '/documents/invoice_demo1.pdf'
                ],
                [
                    'id' => 2,
                    'type' => 'quote',
                    'created_at' => '2025-08-26 15:20:00',
                    'data' => ['client_name' => 'Client B', 'amount' => 800],
                    'path' => '/documents/quote_demo1.pdf'
                ]
            ];
        }
        
        $stmt = $this->db->prepare("
            SELECT * FROM documents 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT 50
        ");
        $stmt->execute([$this->getCurrentUserId()]);
        
        return $stmt->fetchAll();
    }
}


