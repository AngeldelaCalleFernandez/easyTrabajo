<?php

declare(strict_types=1);

const EXIT_SUCCESS = 0;
const EXIT_HYGIENE_FAILURE = 1;
const EXIT_EXECUTION_FAILURE = 2;

/**
 * @param array<int, string> $command
 * @return array{output: string, exitCode: int}
 */
function runCommand(array $command): array
{
    $descriptorSpec = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = proc_open($command, $descriptorSpec, $pipes);

    if (!is_resource($process)) {
        return ['output' => '', 'exitCode' => EXIT_EXECUTION_FAILURE];
    }

    fclose($pipes[0]);
    $output = stream_get_contents($pipes[1]);
    $errorOutput = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);

    $exitCode = proc_close($process);

    if ($exitCode !== 0 && $errorOutput !== false && trim($errorOutput) !== '') {
        fwrite(STDERR, 'ERROR: no se pudo consultar el índice de Git.' . PHP_EOL);
    }

    return [
        'output' => $output === false ? '' : $output,
        'exitCode' => $exitCode,
    ];
}

function normalizedPath(string $path): string
{
    return str_replace('\\', '/', $path);
}

function forbiddenPathReason(string $path): ?string
{
    $normalized = normalizedPath($path);
    $lowercase = strtolower($normalized);
    $basename = strtolower(basename($normalized));

    if ($basename !== '.env.example' && preg_match('#(^|/)\.env($|\.)#i', $normalized) === 1) {
        return 'archivo de entorno no permitido';
    }

    if (preg_match('#\.(pem|key|p12|pfx)$#i', $normalized) === 1) {
        return 'archivo de clave o certificado privado';
    }

    if (preg_match('#\.(zip|rar|7z|tar|gz)$#i', $normalized) === 1) {
        return 'archivo comprimido no justificado';
    }

    if (preg_match('#\.(bak|backup)$#i', $normalized) === 1) {
        return 'archivo de backup';
    }

    if (preg_match('#(^|/)(backup|backups)(/|$)#i', $normalized) === 1) {
        return 'directorio de backup';
    }

    if (preg_match('#(^|/)node_modules(/|$)#i', $normalized) === 1) {
        return 'dependencias node_modules versionadas';
    }

    if (preg_match('#(^|/)dist(/|$)#i', $normalized) === 1) {
        return 'salida dist versionada';
    }

    if (preg_match('#(^|/)\.angular(/|$)#i', $normalized) === 1) {
        return 'caché .angular versionada';
    }

    if (preg_match('#(^|/)coverage(/|$)#i', $normalized) === 1) {
        return 'cobertura generada versionada';
    }

    if (
        preg_match('#(^|/)logs?(/|$)#i', $normalized) === 1 ||
        str_ends_with($lowercase, '.log')
    ) {
        return 'log versionado';
    }

    return null;
}

/**
 * The patterns intentionally target high-confidence formats only.
 *
 * @return array<string, string>
 */
function sensitiveContentPatterns(): array
{
    return [
        'marcador de clave privada' =>
            '/-----BEGIN(?: [A-Z0-9]+)? PRIVATE KEY-----/',
        'token GitHub de alta confianza' =>
            '/\b(?:gh[pousr]_[A-Za-z0-9]{36,255}|github_pat_[A-Za-z0-9_]{20,255})\b/',
        'clave de acceso AWS de alta confianza' =>
            '/\b(?:AKIA|ASIA)[0-9A-Z]{16}\b/',
    ];
}

$rootResult = runCommand(['git', 'rev-parse', '--show-toplevel']);

if ($rootResult['exitCode'] !== 0 || trim($rootResult['output']) === '') {
    fwrite(STDERR, 'ERROR: no se pudo determinar la raíz del repositorio.' . PHP_EOL);
    exit(EXIT_EXECUTION_FAILURE);
}

$repositoryRoot = rtrim(trim($rootResult['output']), "/\\");
$filesResult = runCommand(['git', 'ls-files', '-z', '--full-name']);

if ($filesResult['exitCode'] !== 0) {
    fwrite(STDERR, 'ERROR: git ls-files no pudo completarse.' . PHP_EOL);
    exit(EXIT_EXECUTION_FAILURE);
}

$trackedFiles = array_values(
    array_filter(explode("\0", $filesResult['output']), static fn (string $path): bool => $path !== '')
);
$findings = [];

foreach ($trackedFiles as $trackedFile) {
    $reason = forbiddenPathReason($trackedFile);

    if ($reason !== null) {
        $findings[] = normalizedPath($trackedFile) . ': ' . $reason;
        continue;
    }

    $absolutePath = $repositoryRoot . DIRECTORY_SEPARATOR .
        str_replace('/', DIRECTORY_SEPARATOR, normalizedPath($trackedFile));

    if (!is_file($absolutePath) || !is_readable($absolutePath)) {
        $findings[] = normalizedPath($trackedFile) . ': archivo versionado no legible';
        continue;
    }

    $contents = file_get_contents($absolutePath);

    if ($contents === false) {
        $findings[] = normalizedPath($trackedFile) . ': no se pudo inspeccionar';
        continue;
    }

    if (str_contains($contents, "\0")) {
        continue;
    }

    foreach (sensitiveContentPatterns() as $description => $pattern) {
        if (preg_match($pattern, $contents) === 1) {
            // Only the path and category are reported; the matched value is never printed.
            $findings[] = normalizedPath($trackedFile) . ': ' . $description;
        }
    }
}

if ($findings !== []) {
    fwrite(STDERR, 'ERROR: control básico de higiene no superado.' . PHP_EOL);

    foreach (array_unique($findings) as $finding) {
        fwrite(STDERR, ' - ' . $finding . PHP_EOL);
    }

    exit(EXIT_HYGIENE_FAILURE);
}

fwrite(
    STDOUT,
    'OK: control básico de higiene superado para ' . count($trackedFiles) .
    ' archivos versionados. No sustituye una herramienta profesional de secret scanning.' .
    PHP_EOL
);
exit(EXIT_SUCCESS);
