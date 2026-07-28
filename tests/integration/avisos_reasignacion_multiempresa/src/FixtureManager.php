<?php

declare(strict_types=1);

final class FixtureManager
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function validateSeedAndPassword(): void
    {
        $seed = $this->readSeed();
        preg_match_all(
            '/\$2[ayb]\$\d{2}\$[.\/A-Za-z0-9]{53}/',
            $seed,
            $matches
        );

        $hashes = array_values(array_unique($matches[0] ?? []));
        if (count($hashes) !== 1 || count($matches[0] ?? []) !== 4) {
            throw new RuntimeException(
                'El seed no contiene el unico hash bcrypt esperado para cuatro usuarios.'
            );
        }

        if (!password_verify($this->config->fixturePassword(), $hashes[0])) {
            throw new RuntimeException(
                'La contraseña de fixture no coincide con el hash del seed.'
            );
        }
    }

    public function assertSeedExists(): void
    {
        $path = $this->config->seedPath();
        if (!is_file($path) || !is_readable($path)) {
            throw new RuntimeException('No se encuentra el seed dedicado legible.');
        }
    }

    public function createBackup(): array
    {
        if ($this->config->environment() !== 'local') {
            return [];
        }

        $directory = $this->config->backupDir();
        if ($directory === null) {
            throw new RuntimeException('No se ha configurado el directorio de backup.');
        }

        $safeDatabaseName = (string)preg_replace(
            '/[^A-Za-z0-9_-]+/',
            '_',
            $this->config->dbName()
        );
        $fileName = sprintf(
            'easyparte_avisos_me_%s_%s_%s.sql',
            $safeDatabaseName,
            date('Ymd_His'),
            bin2hex(random_bytes(4))
        );
        $path = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fileName;
        $optionFile = $this->createClientOptionFile();

        try {
            $command = [
                $this->config->mysqldumpBinary(),
                '--defaults-extra-file=' . $optionFile,
                '--default-character-set=utf8mb4',
                '--single-transaction',
                '--routines',
                '--events',
                '--triggers',
                '--skip-comments',
                $this->config->dbName(),
            ];

            $result = $this->runProcess($command, null, $path);
            if ($result['exit_code'] !== 0) {
                @unlink($path);
                throw new RuntimeException(
                    $this->config->redact(
                        'No se pudo crear el backup: ' . $result['stderr']
                    )
                );
            }

            if (!is_file($path) || filesize($path) === 0) {
                @unlink($path);
                throw new RuntimeException('El backup generado esta vacio.');
            }

            $hash = hash_file('sha256', $path);
            if ($hash === false) {
                throw new RuntimeException('No se pudo calcular el SHA-256 del backup.');
            }

            return [
                'file_name' => $fileName,
                'sha256' => strtoupper($hash),
                'bytes' => (int)filesize($path),
            ];
        } finally {
            $this->deleteOptionFile($optionFile);
        }
    }

    public function applySeed(): void
    {
        $seed = $this->readSeed();
        $optionFile = $this->createClientOptionFile();

        try {
            $command = [
                $this->config->mysqlBinary(),
                '--defaults-extra-file=' . $optionFile,
                '--default-character-set=utf8mb4',
                '--batch',
                '--raw',
                '--skip-column-names',
                $this->config->dbName(),
            ];

            $result = $this->runProcess($command, $seed, null);
            if ($result['exit_code'] !== 0) {
                throw new RuntimeException(
                    $this->config->redact(
                        'La aplicacion del seed fallo: ' . $result['stderr']
                    )
                );
            }
        } finally {
            $this->deleteOptionFile($optionFile);
        }
    }

    private function readSeed(): string
    {
        $this->assertSeedExists();
        $content = file_get_contents($this->config->seedPath());
        if ($content === false || trim($content) === '') {
            throw new RuntimeException('El seed dedicado esta vacio o no se puede leer.');
        }

        return $content;
    }

    private function createClientOptionFile(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'easyparte_mysql_');
        if ($path === false) {
            throw new RuntimeException(
                'No se pudo crear el archivo temporal seguro para el cliente MySQL.'
            );
        }

        $contents = implode(PHP_EOL, [
            '[client]',
            'host=' . $this->quoteOptionValue($this->config->dbHost()),
            'port=' . $this->config->dbPort(),
            'user=' . $this->quoteOptionValue($this->config->dbUser()),
            'password=' . $this->quoteOptionValue($this->config->dbPassword()),
            'protocol=tcp',
            '',
        ]);

        if (file_put_contents($path, $contents, LOCK_EX) === false) {
            @unlink($path);
            throw new RuntimeException(
                'No se pudo escribir el archivo temporal del cliente MySQL.'
            );
        }

        @chmod($path, 0600);
        return $path;
    }

    private function deleteOptionFile(string $path): void
    {
        if (is_file($path)) {
            @unlink($path);
        }
    }

    private function quoteOptionValue(string $value): string
    {
        return '"' . str_replace(
            ['\\', '"', "\r", "\n"],
            ['\\\\', '\\"', '', ''],
            $value
        ) . '"';
    }

    private function runProcess(
        array $command,
        ?string $standardInput,
        ?string $standardOutputFile
    ): array {
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => $standardOutputFile === null
                ? ['pipe', 'w']
                : ['file', $standardOutputFile, 'wb'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open(
            $command,
            $descriptors,
            $pipes,
            null,
            null,
            ['bypass_shell' => true]
        );

        if (!is_resource($process)) {
            throw new RuntimeException('No se pudo iniciar una herramienta MySQL.');
        }

        if ($standardInput !== null) {
            fwrite($pipes[0], $standardInput);
        }
        fclose($pipes[0]);

        $stdout = '';
        if ($standardOutputFile === null) {
            $stdout = (string)stream_get_contents($pipes[1]);
            fclose($pipes[1]);
        }

        $stderr = (string)stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        return [
            'exit_code' => $exitCode,
            'stdout' => $stdout,
            'stderr' => $stderr,
        ];
    }
}
