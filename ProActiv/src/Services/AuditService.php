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
