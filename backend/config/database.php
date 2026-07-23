<?php
require_once __DIR__ . '/env.php';

class Database {
    public $conn;

    private function env($key, $default = null) {
        return easyparte_env($key, $default);
    }

    private function isLocalEnv() {
        $appEnv = strtolower($this->env('APP_ENV', 'local'));
        return in_array($appEnv, ['local', 'development', 'dev', 'testing'], true);
    }

    private function failConfiguration($message) {
        error_log('[EasyParte][DB_CONFIG] ' . $message);
        http_response_code(500);
        echo json_encode(["error" => "Error interno de configuracion."]);
        exit();
    }

    private function getRequiredEnv($key, $fallback = null) {
        $value = $this->env($key, null);

        if ($value !== null) {
            return $value;
        }

        if ($this->isLocalEnv()) {
            return $fallback;
        }

        $this->failConfiguration('Missing required environment variable: ' . $key);
    }

    public function getConnection() {
        $this->conn = null;

        $host = $this->getRequiredEnv('DB_HOST', 'localhost');
        $port = $this->env('DB_PORT', '3306');
        $dbName = $this->env('DB_NAME', $this->env('DB_DATABASE', null));
        $dbName = $dbName !== null ? $dbName : ($this->isLocalEnv() ? 'easyParte' : null);
        $username = $this->env('DB_USER', $this->env('DB_USERNAME', null));
        $username = $username !== null ? $username : ($this->isLocalEnv() ? 'root' : null);
        $password = $this->env('DB_PASSWORD', $this->isLocalEnv() ? '' : null);
        $charset = $this->env('DB_CHARSET', 'utf8');

        if ($dbName === null) {
            $this->failConfiguration('Missing required environment variable: DB_NAME');
        }

        if ($username === null) {
            $this->failConfiguration('Missing required environment variable: DB_USER');
        }

        if ($password === null && !$this->isLocalEnv()) {
            $this->failConfiguration('Missing required environment variable: DB_PASSWORD');
        }

        if (!$this->isLocalEnv() && strtolower($username) === 'root') {
            $this->failConfiguration('DB_USER cannot be root outside local/development environments.');
        }

        try {
            $dsn = "mysql:host=" . $host . ";port=" . $port . ";dbname=" . $dbName . ";charset=" . $charset;
            $this->conn = new PDO($dsn, $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            error_log('[EasyParte][DB_CONNECTION] ' . $exception->getMessage());
            http_response_code(500);
            echo json_encode(["error" => "No se ha podido conectar con la base de datos."]);
            exit();
        }

        return $this->conn;
    }
}
?>
