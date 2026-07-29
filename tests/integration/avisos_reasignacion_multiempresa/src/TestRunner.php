<?php

declare(strict_types=1);

final class TestRunner
{
    private ApiClient $api;
    private DatabaseVerifier $database;
    private string $fixturePassword;
    private array $sessions = [];
    private array $results = [];

    public function __construct(
        ApiClient $api,
        DatabaseVerifier $database,
        string $fixturePassword
    ) {
        $this->api = $api;
        $this->database = $database;
        $this->fixturePassword = $fixturePassword;
    }

    public function run(int $auditBaseline): array
    {
        $this->authenticateSessions();
        $this->runNegativeTests();
        $this->runPositiveTests($auditBaseline);

        return $this->results;
    }

    public function results(): array
    {
        return $this->results;
    }

    private function authenticateSessions(): void
    {
        $identities = [
            'admin_a' => [
                'test' => 'AUTH-01',
                'email' => 'admin-a.avisos-multiempresa@test.local',
                'id_usuario' => 9241,
                'id_empresa' => 9201,
                'id_empleado' => null,
                'rol' => 'administrador',
            ],
            'tecnico_a' => [
                'test' => 'AUTH-02',
                'email' => 'tecnico-a.avisos-multiempresa@test.local',
                'id_usuario' => 9242,
                'id_empresa' => 9201,
                'id_empleado' => 9211,
                'rol' => 'tecnico',
            ],
            'atencion_a' => [
                'test' => 'AUTH-05',
                'email' => 'atencion-a.avisos-multiempresa@test.local',
                'id_usuario' => 9243,
                'id_empresa' => 9201,
                'id_empleado' => null,
                'rol' => 'atencion al cliente',
            ],
            'admin_b' => [
                'test' => 'AUTH-03',
                'email' => 'admin-b.avisos-multiempresa@test.local',
                'id_usuario' => 9251,
                'id_empresa' => 9202,
                'id_empleado' => null,
                'rol' => 'administrador',
            ],
            'tecnico_b' => [
                'test' => 'AUTH-04',
                'email' => 'tecnico-b.avisos-multiempresa@test.local',
                'id_usuario' => 9252,
                'id_empresa' => 9202,
                'id_empleado' => 9221,
                'rol' => 'tecnico',
            ],
        ];

        foreach ($identities as $name => $expected) {
            $this->execute($expected['test'], function () use ($name, $expected): void {
                $session = $this->api->login($expected['email'], $this->fixturePassword);
                $user = $session['usuario'];

                $this->assertSameInteger($expected['id_usuario'], $user['id_usuario'] ?? null);
                $this->assertSameInteger($expected['id_empresa'], $user['id_empresa'] ?? null);
                $this->assertNullableInteger(
                    $expected['id_empleado'],
                    $user['id_empleado'] ?? null
                );

                $actualRole = $this->normalizeRole((string)($user['rol_nombre'] ?? ''));
                $this->assertTrue(
                    $actualRole === $expected['rol'],
                    'El rol de la sesion no coincide con el fixture.'
                );

                $this->sessions[$name] = $session;
            });
        }
    }

    private function runNegativeTests(): void
    {
        $this->negativeWrite(
            'FIN-01',
            'admin_a',
            9266,
            403,
            '/avisos/9266/asignar',
            ['id_empleado' => 9212]
        );
        $this->negativeWrite(
            'FIN-02',
            'atencion_a',
            9266,
            403,
            '/avisos/9266/asignar',
            ['id_empleado' => 9212]
        );
        $this->negativeWrite(
            'FIN-03',
            'tecnico_a',
            9266,
            403,
            '/avisos/9266/asignar',
            ['id_empleado' => 9212]
        );
        $this->negativeWrite(
            'FIN-04',
            'tecnico_a',
            9267,
            403,
            '/avisos/9267/coger',
            null
        );
        $this->negativeWrite(
            'FIN-05',
            'admin_a',
            9264,
            403,
            '/avisos/9264/asignar',
            ['id_empleado' => 9212]
        );
        $this->negativeWrite(
            'FIN-06',
            'atencion_a',
            9264,
            403,
            '/avisos/9264/asignar',
            ['id_empleado' => 9212]
        );
        $this->negativeWrite(
            'FIN-07',
            'tecnico_a',
            9264,
            403,
            '/avisos/9264/asignar',
            ['id_empleado' => 9212]
        );
        $this->negativeWrite(
            'FIN-08',
            'tecnico_a',
            9268,
            403,
            '/avisos/9268/coger',
            null
        );

        $this->negativeWrite(
            'AVISO-ME-N01',
            'admin_a',
            9271,
            404,
            '/avisos/9271/asignar',
            ['id_empleado' => 9212]
        );
        $this->negativeWrite(
            'AVISO-ME-N02',
            'admin_a',
            9262,
            403,
            '/avisos/9262/asignar',
            ['id_empleado' => 9221]
        );
        $this->negativeWrite(
            'AVISO-ME-N03',
            'tecnico_a',
            9271,
            404,
            '/avisos/9271/asignar',
            ['id_empleado' => 9212]
        );
        $this->negativeWrite(
            'AVISO-ME-N04',
            'tecnico_a',
            9264,
            403,
            '/avisos/9264/asignar',
            ['id_empleado' => 9212]
        );
        $this->negativeWrite(
            'AVISO-ME-N05',
            'tecnico_a',
            9261,
            403,
            '/avisos/9261/asignar',
            ['id_empleado' => 9211]
        );

        $this->execute('AVISO-ME-N06', function (): void {
            $before = $this->database->allTaskStates();
            $baseline = $this->database->auditBaseline();
            $notices = $this->getList('/avisos', 'admin_a');
            $employees = $this->getList('/avisos/empleados-asignables', 'admin_a');

            $this->assertIds(
                $notices,
                'id_tarea',
                [9261, 9262, 9263, 9264, 9265, 9266, 9267, 9268]
            );
            $this->assertIds($employees, 'id_empleado', [9211, 9212]);
            $this->assertTrue(
                $before === $this->database->allTaskStates(),
                'Los listados modificaron el estado de avisos.'
            );
            $this->assertTrue(
                $this->database->fixtureEventCountAfter($baseline) === 0,
                'Los listados crearon eventos de auditoria.'
            );
        });

        $this->execute('AVISO-ME-N07', function (): void {
            $before = $this->database->allTaskStates();
            $baseline = $this->database->auditBaseline();
            $notices = $this->getList('/avisos', 'tecnico_a');
            $employees = $this->getList('/avisos/empleados-asignables', 'tecnico_a');

            $this->assertIds(
                $notices,
                'id_tarea',
                [9261, 9262, 9264, 9265, 9266, 9267, 9268]
            );
            $this->assertIds($employees, 'id_empleado', [9212]);
            $this->assertTrue(
                $before === $this->database->allTaskStates(),
                'Los listados tecnicos modificaron el estado de avisos.'
            );
            $this->assertTrue(
                $this->database->fixtureEventCountAfter($baseline) === 0,
                'Los listados tecnicos crearon eventos de auditoria.'
            );
        });
    }

    private function runPositiveTests(int $auditBaseline): void
    {
        $this->execute('AVISO-ME-P01', function (): void {
            $this->assertIds(
                $this->getList('/avisos', 'admin_a'),
                'id_tarea',
                [9261, 9262, 9263, 9264, 9265, 9266, 9267, 9268]
            );
        });

        $this->execute('AVISO-ME-P02', function (): void {
            $this->assertIds(
                $this->getList('/avisos', 'tecnico_a'),
                'id_tarea',
                [9261, 9262, 9264, 9265, 9266, 9267, 9268]
            );
        });

        $this->execute('AVISO-ME-P03', function (): void {
            $this->assertIds(
                $this->getList('/avisos/empleados-asignables', 'admin_a'),
                'id_empleado',
                [9211, 9212]
            );
        });

        $this->execute('AVISO-ME-P04', function (): void {
            $this->assertIds(
                $this->getList('/avisos/empleados-asignables', 'tecnico_a'),
                'id_empleado',
                [9212]
            );
        });

        $this->positiveWrite(
            'AVISO-ME-P05',
            'tecnico_a',
            '/avisos/9261/asignar',
            ['id_empleado' => 9212],
            9261,
            9201,
            9212
        );
        $this->positiveWrite(
            'AVISO-ME-P06',
            'admin_a',
            '/avisos/9262/asignar',
            ['id_empleado' => 9212],
            9262,
            9201,
            9212
        );
        $this->positiveWrite(
            'AVISO-ME-P07',
            'tecnico_a',
            '/avisos/9265/coger',
            null,
            9265,
            9201,
            9211
        );
        $this->positiveWrite(
            'AVISO-ME-P08',
            'admin_b',
            '/avisos/9271/asignar',
            ['id_empleado' => 9222],
            9271,
            9202,
            9222
        );

        $this->execute('AVISO-ME-P09', function () use ($auditBaseline): void {
            $events = $this->database->positiveAuditEvents($auditBaseline);
            $this->assertTrue(count($events) === 4, 'No se generaron exactamente cuatro eventos.');
            $this->assertTrue(
                $this->database->fixtureEventCountAfter($auditBaseline) === 4,
                'Existen eventos fixture inesperados posteriores al baseline.'
            );

            $expected = [
                9261 => [9201, 9242, 'aviso_reasignado', 9211, 9212],
                9262 => [9201, 9241, 'aviso_asignado', null, 9212],
                9265 => [9201, 9242, 'aviso_autoasignado', null, 9211],
                9271 => [9202, 9251, 'aviso_reasignado', 9221, 9222],
            ];

            foreach ($events as $event) {
                $noticeId = (int)$event['entidad_id'];
                $this->assertTrue(isset($expected[$noticeId]), 'Se encontro un evento no esperado.');
                [$companyId, $userId, $action, $previousEmployee, $newEmployee]
                    = $expected[$noticeId];

                $this->assertSameInteger($companyId, $event['id_empresa']);
                $this->assertSameInteger($userId, $event['id_usuario']);
                $this->assertTrue(
                    (string)$event['entidad'] === 'aviso'
                    && (string)$event['accion'] === $action,
                    'La entidad o accion del evento no coincide.'
                );

                $previous = $this->decodeAuditValue($event['valores_anteriores']);
                $new = $this->decodeAuditValue($event['valores_nuevos']);
                $this->assertNullableInteger(
                    $previousEmployee,
                    $previous['id_empleado'] ?? null
                );
                $this->assertSameInteger($newEmployee, $new['id_empleado'] ?? null);
            }

            foreach ($this->database->crossTenantCounts($auditBaseline) as $count) {
                $this->assertTrue($count === 0, 'Se detecto un cruce multiempresa en auditoria.');
            }
        });
    }

    private function negativeWrite(
        string $testId,
        string $sessionName,
        int $taskId,
        int $expectedStatus,
        string $path,
        ?array $body
    ): void {
        $this->execute($testId, function () use (
            $sessionName,
            $taskId,
            $expectedStatus,
            $path,
            $body
        ): void {
            $before = $this->database->taskState($taskId);
            $baseline = $this->database->auditBaseline();
            $response = $this->api->put(
                $path,
                $this->token($sessionName),
                $body
            );

            $this->api->assertNoInternalDetails($response);
            $this->assertTrue(
                $response['status'] === $expectedStatus,
                sprintf('Se esperaba HTTP %d y se obtuvo HTTP %d.', $expectedStatus, $response['status'])
            );
            $this->assertTrue(
                $before === $this->database->taskState($taskId),
                'El aviso cambio tras una peticion rechazada.'
            );
            $this->assertTrue(
                $this->database->fixtureEventCountAfter($baseline) === 0,
                'Una peticion rechazada creo auditoria.'
            );
        });
    }

    private function positiveWrite(
        string $testId,
        string $sessionName,
        string $path,
        ?array $body,
        int $taskId,
        int $companyId,
        int $employeeId
    ): void {
        $this->execute($testId, function () use (
            $sessionName,
            $path,
            $body,
            $taskId,
            $companyId,
            $employeeId
        ): void {
            $response = $this->api->put($path, $this->token($sessionName), $body);
            $this->api->assertNoInternalDetails($response);
            $this->assertTrue(
                $response['status'] === 200,
                sprintf('La operacion positiva devolvio HTTP %d.', $response['status'])
            );

            $state = $this->database->taskState($taskId);
            $this->assertTrue(
                $state['id_empresa'] === $companyId
                && $state['id_empleado'] === $employeeId
                && $state['estado'] === 'Pendiente'
                && $state['fecha_fin'] === null,
                'El estado persistente del aviso no coincide con el esperado.'
            );
        });
    }

    private function getList(string $path, string $sessionName): array
    {
        $response = $this->api->get($path, $this->token($sessionName));
        $this->api->assertNoInternalDetails($response);
        $this->assertTrue(
            $response['status'] === 200,
            sprintf('El listado devolvio HTTP %d.', $response['status'])
        );
        $this->assertTrue(is_array($response['data']), 'El listado no devolvio JSON valido.');

        return $response['data'];
    }

    private function token(string $sessionName): string
    {
        if (!isset($this->sessions[$sessionName]['token'])) {
            throw new LogicException('La sesion solicitada no esta disponible.');
        }

        return (string)$this->sessions[$sessionName]['token'];
    }

    private function assertIds(array $rows, string $column, array $expected): void
    {
        $actual = [];
        foreach ($rows as $row) {
            if (!is_array($row) || !array_key_exists($column, $row)) {
                throw new RuntimeException('El listado no contiene el identificador esperado.');
            }
            $actual[] = (int)$row[$column];
        }

        sort($actual);
        sort($expected);
        $this->assertTrue($actual === $expected, 'El conjunto de IDs del listado no coincide.');
    }

    private function decodeAuditValue($json): array
    {
        if (!is_string($json) || $json === '') {
            throw new RuntimeException('Un evento no contiene valores JSON validos.');
        }

        try {
            $value = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Un evento contiene JSON no valido.');
        }

        if (!is_array($value)) {
            throw new RuntimeException('Los valores del evento no son un objeto JSON.');
        }

        return $value;
    }

    private function execute(string $testId, callable $test): void
    {
        try {
            $test();
            $this->results[] = new TestResult($testId, true, 'Correcta');
        } catch (Throwable $exception) {
            $this->results[] = new TestResult($testId, false, $exception->getMessage());
            throw new RuntimeException(
                sprintf('%s fallo: %s', $testId, $exception->getMessage()),
                0,
                $exception
            );
        }
    }

    private function assertSameInteger(int $expected, $actual): void
    {
        $this->assertTrue(
            filter_var($actual, FILTER_VALIDATE_INT) !== false
            && (int)$actual === $expected,
            'Un identificador no coincide con el fixture.'
        );
    }

    private function assertNullableInteger(?int $expected, $actual): void
    {
        if ($expected === null) {
            $this->assertTrue($actual === null || $actual === '', 'Se esperaba un valor nulo.');
            return;
        }

        $this->assertSameInteger($expected, $actual);
    }

    private function assertTrue(bool $condition, string $message): void
    {
        if (!$condition) {
            throw new RuntimeException($message);
        }
    }

    private function normalizeRole(string $role): string
    {
        $normalized = strtr(
            trim($role),
            ['Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
             'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
             'Ã©' => 'e', 'Ã³' => 'o', 'Ã‰' => 'E', 'Ã“' => 'O']
        );

        return strtolower($normalized);
    }
}
