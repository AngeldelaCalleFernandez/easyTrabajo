<?php
require_once __DIR__ . '/env.php';

// Variables esperadas:
// APP_ENV=local|development|testing|staging|production
// CORS_ALLOWED_ORIGINS=http://localhost:4200,https://app.easyparte.com
// CORS_ALLOWED_METHODS=GET,POST,PUT,DELETE,OPTIONS
// CORS_ALLOWED_HEADERS=Content-Type,Authorization,X-Requested-With
// CORS_ALLOW_CREDENTIALS=false

function cors_env($key, $default = null) {
    return easyparte_env($key, $default);
}

$appEnv = strtolower(cors_env('APP_ENV', 'local'));
$isLocalEnv = in_array($appEnv, ['local', 'development', 'dev', 'testing'], true);

$allowedOriginsValue = cors_env('CORS_ALLOWED_ORIGINS', $isLocalEnv ? '*' : '');
$allowedOrigins = array_filter(array_map('trim', explode(',', $allowedOriginsValue)));
$requestOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowCredentials = strtolower(cors_env('CORS_ALLOW_CREDENTIALS', 'false')) === 'true';

if (in_array('*', $allowedOrigins, true)) {
    if ($isLocalEnv && !$allowCredentials) {
        header('Access-Control-Allow-Origin: *');
    } else {
        error_log('[EasyParte][CORS] Wildcard origin ignored outside local env or with credentials enabled.');
    }
} elseif ($requestOrigin !== '' && in_array($requestOrigin, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $requestOrigin);
    header('Vary: Origin');
} elseif ($requestOrigin !== '' && !$isLocalEnv) {
    error_log('[EasyParte][CORS] Origin not allowed: ' . $requestOrigin);
}

if ($allowCredentials && !in_array('*', $allowedOrigins, true)) {
    header('Access-Control-Allow-Credentials: true');
}

header('Access-Control-Allow-Methods: ' . cors_env('CORS_ALLOWED_METHODS', 'GET, POST, PUT, DELETE, OPTIONS'));
header('Access-Control-Allow-Headers: ' . cors_env('CORS_ALLOWED_HEADERS', 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With'));

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>
