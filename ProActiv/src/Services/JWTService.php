<?php
class JWTService {
    private $secretKey;
    private $algorithm = 'HS256';
    private $issuer = 'ProActiv';
    private $expiration = 3600;
    
    public function __construct($config) {
        $this->secretKey = $config['security']['jwt_secret'] ?? JWT_SECRET;
    }
    
    public function generateToken(array $payload, int $expiration = null): string {
        $expiration = $expiration ?? $this->expiration;
        $header = ['typ' => 'JWT', 'alg' => $this->algorithm];
        $payload = array_merge($payload, [
            'iss' => $this->issuer, 'iat' => time(), 'exp' => time() + $expiration, 'jti' => uniqid()
        ]);
        
        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));
        $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, $this->secretKey, true);
        $signatureEncoded = $this->base64UrlEncode($signature);
        
        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }
    
    public function verifyToken(string $token): array {
        $parts = explode('.', $token);
        if (count($parts) !== 3) throw new Exception('Token JWT invalide');
        
        [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;
        $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, $this->secretKey, true);
        $expectedSignature = $this->base64UrlEncode($signature);
        
        if (!hash_equals($expectedSignature, $signatureEncoded)) {
            throw new Exception('Signature JWT invalide');
        }
        
        $payload = json_decode($this->base64UrlDecode($payloadEncoded), true);
        if ($payload['exp'] < time()) throw new Exception('Token JWT expiré');
        
        return $payload;
    }
    
    private function base64UrlEncode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    private function base64UrlDecode(string $data): string {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}
