<?php

class JWT {
    private static function env($key, $default = null) {
        $value = getenv($key);

        if ($value === false && isset($_ENV[$key])) {
            $value = $_ENV[$key];
        }

        if ($value === false && isset($_SERVER[$key])) {
            $value = $_SERVER[$key];
        }

        return ($value === false || $value === '') ? $default : $value;
    }

    private static function isLocalEnv() {
        $appEnv = strtolower(self::env('APP_ENV', 'local'));
        return in_array($appEnv, ['local', 'development', 'dev', 'testing'], true);
    }

    private static function failConfiguration($message) {
        error_log('[EasyParte][JWT_CONFIG] ' . $message);
        http_response_code(500);
        echo json_encode(["error" => "Error interno de configuracion."]);
        exit();
    }

    private static function getSecret() {
        $secret = self::env('JWT_SECRET', null);

        if ($secret !== null) {
            return $secret;
        }

        if (self::isLocalEnv()) {
            return 'clave_local_para_desarrollo_easyparte_2026';
        }

        self::failConfiguration('Missing required environment variable: JWT_SECRET');
    }

    public static function encode($payload) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::getSecret(), true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public static function decode($token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $parts;

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::getSecret(), true);
        $expectedSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        if (hash_equals($expectedSignature, $base64UrlSignature)) {
            $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlPayload)));
            
            // Verificar si el token ha expirado
            if (isset($payload->exp) && $payload->exp < time()) {
                return false;
            }
            return $payload;
        }

        return false;
    }
}
?>
