<?php

declare(strict_types=1);

final class Config
{
    public const CONFIRMATION = 'AVISOS_MULTIEMPRESA_9201_9272';

    private string $mode;
    private string $projectRoot;
    private string $environment;
    private string $apiUrl;
    private string $dbHost;
    private int $dbPort;
    private string $dbName;
    private string $dbUser;
    private string $dbPassword;
    private string $fixturePassword;
    private ?string $backupDir;
    private string $phpBinary;
    private string $mysqlBinary;
    private string $mysqldumpBinary;

    private function __construct(string $mode, string $projectRoot)
    {
        $this->mode = $mode;
        $this->projectRoot = rtrim($projectRoot, DIRECTORY_SEPARATOR);
        $this->environment = strtolower($this->requiredEnvironment('EASYPARTE_TEST_ENV'));
        $this->dbHost = $this->requiredEnvironment('EASYPARTE_TEST_DB_HOST');
        $this->dbPort = $this->positiveIntegerEnvironment('EASYPARTE_TEST_DB_PORT');
        $this->dbName = $this->requiredEnvironment('EASYPARTE_TEST_DB_NAME');
        $this->dbUser = $this->requiredEnvironment('EASYPARTE_TEST_DB_USER');
        $dbPassword = getenv('EASYPARTE_TEST_DB_PASSWORD');
        $this->dbPassword = $dbPassword === false ? '' : trim((string)$dbPassword);
        $this->apiUrl = '';
        $this->fixturePassword = '';
        $this->backupDir = null;

        if ($mode !== '--rollback-only') {
            $this->apiUrl = rtrim($this->requiredEnvironment('EASYPARTE_API_URL'), '/');
            $this->fixturePassword = $this->requiredEnvironment('EASYPARTE_FIXTURE_PASSWORD');
        }

        if ($this->environment === 'local' && $mode !== '--rollback-only') {
            $this->backupDir = $this->requiredEnvironment('EASYPARTE_TEST_BACKUP_DIR');
        } else {
            $value = getenv('EASYPARTE_TEST_BACKUP_DIR');
            $this->backupDir = $value !== false && trim($value) !== '' ? trim($value) : null;
        }

        $this->phpBinary = $this->binaryEnvironment(
            'EASYPARTE_PHP_BIN',
            PHP_BINARY
        );
        $this->mysqlBinary = $this->binaryEnvironment(
            'EASYPARTE_MYSQL_BIN',
            self::windowsXamppDefault('mysql.exe', 'mysql' . DIRECTORY_SEPARATOR . 'bin')
        );
        $this->mysqldumpBinary = $this->binaryEnvironment(
            'EASYPARTE_MYSQLDUMP_BIN',
            self::windowsXamppDefault('mysqldump.exe', 'mysql' . DIRECTORY_SEPARATOR . 'bin')
        );

        $this->validate();
    }

    public static function fromEnvironment(string $mode, string $projectRoot): self
    {
        if (!in_array($mode, ['--preflight', '--run', '--rollback-only'], true)) {
            throw new InvalidArgumentException(
                'Modo no valido. Use --preflight, --run o --rollback-only.'
            );
        }

        return new self($mode, $projectRoot);
    }

    public static function parseMode(array $arguments): string
    {
        if (count($arguments) !== 2) {
            throw new InvalidArgumentException(
                'Debe indicar exactamente un modo: --preflight, --run o --rollback-only.'
            );
        }

        $mode = (string)$arguments[1];
        if (strpos($mode, '=') !== false) {
            throw new InvalidArgumentException(
                'No se aceptan valores ni secretos mediante argumentos CLI.'
            );
        }

        if (!in_array($mode, ['--preflight', '--run', '--rollback-only'], true)) {
            throw new InvalidArgumentException(
                'Modo no valido. Use --preflight, --run o --rollback-only.'
            );
        }

        return $mode;
    }

    public function validateRuntime(): void
    {
        foreach (['curl', 'PDO', 'pdo_mysql'] as $extension) {
            if (!extension_loaded($extension)) {
                throw new RuntimeException(
                    sprintf('Falta la extension PHP obligatoria: %s.', $extension)
                );
            }
        }

        $this->assertBinaryExists($this->phpBinary, 'PHP');

        if ($this->mode !== '--rollback-only') {
            $this->assertBinaryExists($this->mysqlBinary, 'mysql');
        }

        if (
            $this->environment === 'local'
            && $this->mode !== '--rollback-only'
        ) {
            $this->assertBinaryExists($this->mysqldumpBinary, 'mysqldump');
            $this->validateBackupDirectory();
        }
    }

    public function redact(string $message): string
    {
        $redacted = $message;
        $values = [
            $this->dbPassword,
            $this->fixturePassword,
        ];

        foreach ($values as $value) {
            if ($value !== '') {
                $redacted = str_replace($value, '[REDACTADO]', $redacted);
            }
        }

        $patterns = [
            '/Bearer\s+[A-Za-z0-9._~+\/=-]+/i' => 'Bearer [REDACTADO]',
            '/\$2[ayb]\$\d{2}\$[.\/A-Za-z0-9]{53}/' => '[BCRYPT REDACTADO]',
            '/("?(?:password|token|authorization|db_password)"?\s*[:=]\s*)("[^"]*"|[^\s,;]+)/i'
                => '$1[REDACTADO]',
            '/mysql:[^\s]+/i' => 'mysql:[DSN REDACTADO]',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $redacted = (string)preg_replace($pattern, $replacement, $redacted);
        }

        return $redacted;
    }

    public function mode(): string
    {
        return $this->mode;
    }

    public function projectRoot(): string
    {
        return $this->projectRoot;
    }

    public function environment(): string
    {
        return $this->environment;
    }

    public function apiUrl(): string
    {
        return $this->apiUrl;
    }

    public function dbHost(): string
    {
        return $this->dbHost;
    }

    public function dbPort(): int
    {
        return $this->dbPort;
    }

    public function dbName(): string
    {
        return $this->dbName;
    }

    public function dbUser(): string
    {
        return $this->dbUser;
    }

    public function dbPassword(): string
    {
        return $this->dbPassword;
    }

    public function fixturePassword(): string
    {
        return $this->fixturePassword;
    }

    public function backupDir(): ?string
    {
        return $this->backupDir;
    }

    public function mysqlBinary(): string
    {
        return $this->mysqlBinary;
    }

    public function mysqldumpBinary(): string
    {
        return $this->mysqldumpBinary;
    }

    public function seedPath(): string
    {
        return $this->projectRoot
            . DIRECTORY_SEPARATOR
            . 'bbdd'
            . DIRECTORY_SEPARATOR
            . 'seed_avisos_reasignacion_multiempresa_pruebas.sql';
    }

    private function validate(): void
    {
        if (!in_array($this->environment, ['local', 'test'], true)) {
            throw new RuntimeException(
                'EASYPARTE_TEST_ENV debe ser exactamente local o test.'
            );
        }

        if ($this->requiredEnvironment('EASYPARTE_TEST_CONFIRM') !== self::CONFIRMATION) {
            throw new RuntimeException('La confirmacion de seguridad no coincide.');
        }

        $this->validateDatabaseTarget();

        if ($this->mode !== '--rollback-only') {
            $this->validateApiTarget();
        }
    }

    private function validateApiTarget(): void
    {
        $parts = parse_url($this->apiUrl);
        if (
            !is_array($parts)
            || !isset($parts['scheme'], $parts['host'])
            || !in_array(strtolower((string)$parts['scheme']), ['http', 'https'], true)
        ) {
            throw new RuntimeException('EASYPARTE_API_URL no es una URL HTTP valida.');
        }

        if (isset($parts['user']) || isset($parts['pass'])) {
            throw new RuntimeException(
                'La URL de API no puede contener credenciales.'
            );
        }

        $host = strtolower((string)$parts['host']);
        $isLocal = in_array($host, ['localhost', '127.0.0.1', '::1'], true);
        $isTest = preg_match('/(?:^|[.-])(test|testing|qa|local)(?:[.-]|$)/i', $host) === 1;

        if (!$isLocal && !$isTest) {
            throw new RuntimeException(
                'La URL de API no parece pertenecer a un entorno local o de pruebas.'
            );
        }
    }

    private function validateDatabaseTarget(): void
    {
        $host = strtolower(trim($this->dbHost));
        $name = strtolower(trim($this->dbName));
        $isLocalHost = in_array($host, ['localhost', '127.0.0.1', '::1'], true);
        $isTestName = preg_match('/(?:test|testing|qa|local)/i', $name) === 1;

        if (preg_match('/(?:prod|production|live)/i', $host . ' ' . $name) === 1) {
            throw new RuntimeException('La base de datos parece ser de produccion.');
        }

        if ($this->environment === 'local' && !$isLocalHost) {
            throw new RuntimeException(
                'El entorno local solo admite una base de datos en localhost.'
            );
        }

        if (!$isLocalHost && !$isTestName) {
            throw new RuntimeException(
                'La base de datos no parece pertenecer a un entorno local o de pruebas.'
            );
        }

        if (
            $this->environment === 'test'
            && $this->dbPassword === ''
        ) {
            throw new RuntimeException(
                'La contraseña DB no puede estar vacia en el entorno test.'
            );
        }
    }

    private function validateBackupDirectory(): void
    {
        if ($this->backupDir === null || !is_dir($this->backupDir)) {
            throw new RuntimeException(
                'EASYPARTE_TEST_BACKUP_DIR debe ser un directorio existente en local.'
            );
        }

        if (!is_writable($this->backupDir)) {
            throw new RuntimeException('El directorio de backup no permite escritura.');
        }

        $backupReal = realpath($this->backupDir);
        $publicReal = realpath(
            $this->projectRoot . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'public'
        );

        if ($backupReal === false) {
            throw new RuntimeException('No se puede resolver el directorio de backup.');
        }

        if (
            $publicReal !== false
            && self::isPathInside($backupReal, $publicReal)
        ) {
            throw new RuntimeException(
                'El backup no puede guardarse dentro del directorio publico.'
            );
        }
    }

    private function assertBinaryExists(string $path, string $label): void
    {
        if (!is_file($path)) {
            throw new RuntimeException(
                sprintf('No se encuentra el binario %s configurado.', $label)
            );
        }
    }

    private function requiredEnvironment(string $name): string
    {
        $value = $this->environmentValue($name, false);
        if ($value === '') {
            throw new RuntimeException(sprintf('Falta la variable obligatoria %s.', $name));
        }

        return $value;
    }

    private function environmentValue(string $name, bool $allowEmpty): string
    {
        $value = getenv($name);
        if ($value === false) {
            throw new RuntimeException(sprintf('Falta la variable obligatoria %s.', $name));
        }

        $normalized = trim((string)$value);
        if (!$allowEmpty && $normalized === '') {
            throw new RuntimeException(sprintf('La variable %s no puede estar vacia.', $name));
        }

        return $normalized;
    }

    private function positiveIntegerEnvironment(string $name): int
    {
        $value = $this->requiredEnvironment($name);
        if (!ctype_digit($value) || (int)$value <= 0 || (int)$value > 65535) {
            throw new RuntimeException(sprintf('%s debe ser un puerto valido.', $name));
        }

        return (int)$value;
    }

    private function binaryEnvironment(string $name, string $default): string
    {
        $value = getenv($name);
        return $value !== false && trim((string)$value) !== ''
            ? trim((string)$value)
            : $default;
    }

    private static function windowsXamppDefault(string $binary, string $subdirectory): string
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            return 'C:\\xampp\\' . $subdirectory . '\\' . $binary;
        }

        return $binary;
    }

    private static function isPathInside(string $candidate, string $parent): bool
    {
        $candidate = strtolower(rtrim(str_replace('\\', '/', $candidate), '/'));
        $parent = strtolower(rtrim(str_replace('\\', '/', $parent), '/'));

        return $candidate === $parent || strpos($candidate . '/', $parent . '/') === 0;
    }
}
