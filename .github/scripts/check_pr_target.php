<?php

declare(strict_types=1);

const EXIT_SUCCESS = 0;
const EXIT_POLICY_FAILURE = 1;
const EVENT_PULL_REQUEST = 'pull_request';
const EVENT_WORKFLOW_DISPATCH = 'workflow_dispatch';

/**
 * Returns the first non-empty CLI argument or environment variable.
 *
 * @param array<int, string> $argv
 * @param array<int, string> $environmentNames
 */
function inputValue(array $argv, int $argumentIndex, array $environmentNames): string
{
    if (isset($argv[$argumentIndex]) && trim($argv[$argumentIndex]) !== '') {
        return trim($argv[$argumentIndex]);
    }

    foreach ($environmentNames as $name) {
        $value = getenv($name);

        if ($value !== false && trim($value) !== '') {
            return trim($value);
        }
    }

    return '';
}

function safeForOutput(string $value): string
{
    return preg_replace('/[\x00-\x1F\x7F]/', '?', $value) ?? '';
}

function failPolicy(string $message): never
{
    fwrite(STDERR, 'ERROR: ' . $message . PHP_EOL);
    exit(EXIT_POLICY_FAILURE);
}

$baseBranch = inputValue($argv, 1, ['BASE_BRANCH', 'GITHUB_BASE_REF']);
$headBranch = inputValue($argv, 2, ['HEAD_BRANCH', 'GITHUB_HEAD_REF']);
$headRepository = inputValue($argv, 3, ['HEAD_REPOSITORY']);
$currentRepository = inputValue($argv, 4, ['CURRENT_REPOSITORY', 'GITHUB_REPOSITORY']);
$eventName = inputValue($argv, 5, ['EVENT_NAME', 'GITHUB_EVENT_NAME']);

if ($eventName === '' && ($baseBranch !== '' || $headBranch !== '')) {
    $eventName = EVENT_PULL_REQUEST;
}

if ($eventName === EVENT_WORKFLOW_DISPATCH) {
    fwrite(
        STDOUT,
        'INFO: workflow_dispatch no tiene contexto de Pull Request; política de destino omitida.' .
        PHP_EOL
    );
    exit(EXIT_SUCCESS);
}

if ($eventName !== EVENT_PULL_REQUEST) {
    failPolicy('evento no soportado o ausente.');
}

if ($baseBranch === '' || $headBranch === '') {
    failPolicy('faltan la rama base o la rama de origen.');
}

$ordinaryBranchPattern = '#^(feature|fix|refactor|security|docs|testing|ci)/.+$#';
$masterBranchPattern = '#^(release|hotfix)/.+$#';

if ($baseBranch === 'develop') {
    if (preg_match($ordinaryBranchPattern, $headBranch) !== 1) {
        failPolicy(
            'la rama "' . safeForOutput($headBranch) .
            '" no sigue un prefijo autorizado para Pull Requests hacia develop.'
        );
    }

    fwrite(
        STDOUT,
        'OK: destino develop permitido para "' . safeForOutput($headBranch) . '".' . PHP_EOL
    );
    exit(EXIT_SUCCESS);
}

if ($baseBranch === 'master') {
    if ($headBranch === 'develop') {
        if ($headRepository === '' || $currentRepository === '') {
            failPolicy(
                'la promoción develop -> master requiere identificar ambos repositorios.'
            );
        }

        if (strcasecmp($headRepository, $currentRepository) !== 0) {
            failPolicy('develop solo puede promocionarse a master desde el mismo repositorio.');
        }

        fwrite(STDOUT, 'OK: promoción develop -> master desde el mismo repositorio.' . PHP_EOL);
        exit(EXIT_SUCCESS);
    }

    if (preg_match($masterBranchPattern, $headBranch) === 1) {
        fwrite(
            STDOUT,
            'OK: destino master permitido para "' . safeForOutput($headBranch) . '".' . PHP_EOL
        );
        exit(EXIT_SUCCESS);
    }

    failPolicy(
        'master solo acepta Pull Requests desde release/*, hotfix/* o develop; recibido "' .
        safeForOutput($headBranch) . '".'
    );
}

failPolicy(
    'rama base no autorizada para este quality-gate: "' . safeForOutput($baseBranch) . '".'
);
