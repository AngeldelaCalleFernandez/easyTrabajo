<?php

declare(strict_types=1);

ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once __DIR__ . '/src/Config.php';
require_once __DIR__ . '/src/ApiClient.php';
require_once __DIR__ . '/src/DatabaseVerifier.php';
require_once __DIR__ . '/src/FixtureManager.php';
require_once __DIR__ . '/src/TestResult.php';
require_once __DIR__ . '/src/TestRunner.php';

const EXIT_SUCCESS = 0;
const EXIT_TEST_FAILURE_CLEAN = 1;
const EXIT_PREFLIGHT_FAILURE = 2;
const EXIT_CLEANUP_FAILURE = 3;

/**
 * @return never
 */
function finish(int $exitCode): void
{
    exit($exitCode);
}

function outputLine(string $message): void
{
    fwrite(STDOUT, $message . PHP_EOL);
}

function outputError(Config $config, string $message): void
{
    fwrite(STDERR, $config->redact($message) . PHP_EOL);
}

function runPreflight(
    Config $config,
    ApiClient $api,
    DatabaseVerifier $database,
    FixtureManager $fixture
): void {
    $fixture->assertSeedExists();
    $api->assertReachable();
    $database->validateSchemaAndRoles();

    if ($database->collisionCount() !== 0) {
        throw new RuntimeException(
            'El preflight detecto colisiones o residuos del fixture.'
        );
    }

    $fixture->validateSeedAndPassword();
}

function printResults(Config $config, array $results): void
{
    foreach ($results as $result) {
        if (!$result instanceof TestResult) {
            continue;
        }

        $state = $result->passed() ? 'OK' : 'FALLO';
        outputLine(sprintf(
            '[%s] %s - %s',
            $state,
            $result->id(),
            $config->redact($result->message())
        ));
    }
}

function executePreflight(Config $config): int
{
    try {
        $config->validateRuntime();
        $api = new ApiClient($config);
        $database = new DatabaseVerifier($config);
        $fixture = new FixtureManager($config);
        runPreflight($config, $api, $database, $fixture);
        outputLine('Preflight correcto. No se han modificado datos.');
        return EXIT_SUCCESS;
    } catch (Throwable $exception) {
        outputError($config, 'Preflight bloqueado: ' . $exception->getMessage());
        return EXIT_PREFLIGHT_FAILURE;
    }
}

function executeRun(Config $config): int
{
    $database = null;
    $lockAcquired = false;
    $cleanupRequired = false;
    $cleanupSucceeded = true;
    $failure = null;
    $runner = null;

    try {
        $config->validateRuntime();
        $api = new ApiClient($config);
        $database = new DatabaseVerifier($config);
        $fixture = new FixtureManager($config);

        $database->acquireLock();
        $lockAcquired = true;
        runPreflight($config, $api, $database, $fixture);
        outputLine('Preflight correcto.');

        $backup = $fixture->createBackup();
        if ($backup !== []) {
            outputLine(sprintf(
                'Backup verificado: %s (%d bytes), SHA-256 %s.',
                $backup['file_name'],
                $backup['bytes'],
                $backup['sha256']
            ));
        }

        // Desde este punto un fallo del cliente podria haber insertado o
        // confirmado filas. El finally debe intentar siempre el rollback.
        $cleanupRequired = true;
        $fixture->applySeed();
        $database->assertFixtureCounts();
        outputLine('Fixture aplicado con los recuentos esperados.');

        $auditBaseline = $database->auditBaseline();
        $runner = new TestRunner(
            $api,
            $database,
            $config->fixturePassword()
        );
        $results = $runner->run($auditBaseline);
        printResults($config, $results);
    } catch (Throwable $exception) {
        $failure = $exception;
        if ($runner instanceof TestRunner) {
            printResults($config, $runner->results());
        }
        outputError($config, 'Ejecucion detenida: ' . $exception->getMessage());
    } finally {
        if ($cleanupRequired && $database instanceof DatabaseVerifier) {
            try {
                $database->rollbackFixture();
                $database->assertClean();
                outputLine('Rollback y limpieza final correctos.');
            } catch (Throwable $cleanupException) {
                $cleanupSucceeded = false;
                outputError(
                    $config,
                    'Fallo critico de rollback o limpieza: '
                    . $cleanupException->getMessage()
                );
            }
        }

        if ($lockAcquired && $database instanceof DatabaseVerifier) {
            try {
                $database->releaseLock();
            } catch (Throwable $lockException) {
                $cleanupSucceeded = false;
                outputError(
                    $config,
                    'No se pudo liberar el bloqueo de ejecucion: '
                    . $lockException->getMessage()
                );
            }
        }
    }

    if (!$cleanupSucceeded) {
        return EXIT_CLEANUP_FAILURE;
    }

    if ($failure !== null) {
        return $cleanupRequired
            ? EXIT_TEST_FAILURE_CLEAN
            : EXIT_PREFLIGHT_FAILURE;
    }

    outputLine('Matriz correcta y base de datos limpia.');
    return EXIT_SUCCESS;
}

function executeRollbackOnly(Config $config): int
{
    $database = null;
    $lockAcquired = false;
    $rollbackStarted = false;
    $exitCode = EXIT_SUCCESS;

    try {
        $config->validateRuntime();
        $database = new DatabaseVerifier($config);
        $database->validateSchemaAndRoles();
        $database->acquireLock();
        $lockAcquired = true;
        $rollbackStarted = true;
        $database->rollbackFixture();
        $database->assertClean();
        outputLine('Rollback-only correcto: no quedan filas ni eventos del fixture.');
    } catch (Throwable $exception) {
        outputError($config, 'Rollback-only bloqueado: ' . $exception->getMessage());
        $exitCode = $rollbackStarted
            ? EXIT_CLEANUP_FAILURE
            : EXIT_PREFLIGHT_FAILURE;
    } finally {
        if ($lockAcquired && $database instanceof DatabaseVerifier) {
            try {
                $database->releaseLock();
            } catch (Throwable $lockException) {
                $exitCode = EXIT_CLEANUP_FAILURE;
                outputError(
                    $config,
                    'No se pudo liberar el bloqueo de ejecucion: '
                    . $lockException->getMessage()
                );
            }
        }
    }

    return $exitCode;
}

try {
    $mode = Config::parseMode($argv);
    $projectRoot = dirname(__DIR__, 3);
    $config = Config::fromEnvironment($mode, $projectRoot);
} catch (Throwable $exception) {
    fwrite(
        STDERR,
        'Configuracion rechazada. Revise variables de entorno y modo CLI.' . PHP_EOL
    );
    finish(EXIT_PREFLIGHT_FAILURE);
}

if ($mode === '--preflight') {
    finish(executePreflight($config));
}

if ($mode === '--rollback-only') {
    finish(executeRollbackOnly($config));
}

finish(executeRun($config));
