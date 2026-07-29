<?php

declare(strict_types=1);

final class DatabaseVerifier
{
    public const LOCK_NAME = 'easyparte:test:avisos-me:9201-9272';

    private const EMPRESA_IDS = [9201, 9202];
    private const DEPARTAMENTO_IDS = [9203, 9204];
    private const EMPLEADO_IDS = [9211, 9212, 9221, 9222];
    private const CLIENTE_IDS = [9231, 9232];
    private const USUARIO_IDS = [9241, 9242, 9243, 9251, 9252];
    private const TAREA_IDS = [
        9261, 9262, 9263, 9264, 9265, 9266, 9267, 9268, 9271, 9272,
    ];

    private PDO $connection;

    public function __construct(Config $config)
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $config->dbHost(),
            $config->dbPort(),
            $config->dbName()
        );

        $this->connection = new PDO(
            $dsn,
            $config->dbUser(),
            $config->dbPassword(),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }

    public function validateSchemaAndRoles(): void
    {
        $requiredTables = [
            'empresa',
            'departamento',
            'empleado',
            'cliente',
            'usuario',
            'usuario_rol',
            'rol',
            'tarea',
            'auditoria_evento',
        ];

        $placeholders = implode(',', array_fill(0, count($requiredTables), '?'));
        $statement = $this->connection->prepare(
            "SELECT table_name AS table_name
             FROM information_schema.tables
             WHERE table_schema = DATABASE()
               AND table_name IN ($placeholders)"
        );
        $statement->execute($requiredTables);
        $found = array_map(
            'strtolower',
            array_column($statement->fetchAll(), 'table_name')
        );

        $missing = array_values(array_diff($requiredTables, $found));
        if ($missing !== []) {
            throw new RuntimeException(
                'Faltan tablas requeridas para la matriz: ' . implode(', ', $missing) . '.'
            );
        }

        $this->assertBaseRolesIntact();
    }

    public function acquireLock(): void
    {
        $statement = $this->connection->prepare('SELECT GET_LOCK(:lock_name, 0)');
        $statement->execute(['lock_name' => self::LOCK_NAME]);
        if ((int)$statement->fetchColumn() !== 1) {
            throw new RuntimeException(
                'Ya existe otra ejecucion activa del arnes multiempresa.'
            );
        }
    }

    public function releaseLock(): void
    {
        $statement = $this->connection->prepare('SELECT RELEASE_LOCK(:lock_name)');
        $statement->execute(['lock_name' => self::LOCK_NAME]);
    }

    public function collisionCount(): int
    {
        $queries = [
            "SELECT COUNT(*) FROM empresa
             WHERE id_empresa IN (9201, 9202)
                OR nif IN ('T9201001A', 'T9202001B')
                OR email IN (
                    'empresa-a.avisos-multiempresa@test.local',
                    'empresa-b.avisos-multiempresa@test.local'
                )",
            'SELECT COUNT(*) FROM departamento WHERE id_departamento IN (9203, 9204)',
            "SELECT COUNT(*) FROM empleado
             WHERE id_empleado IN (9211, 9212, 9221, 9222)
                OR nif IN ('T9211001A', 'T9212001A', 'T9221001B', 'T9222001B')",
            "SELECT COUNT(*) FROM cliente
             WHERE id_cliente IN (9231, 9232)
                OR nif IN ('T9231001A', 'T9232001B')
                OR email IN (
                    'cliente-a.avisos-multiempresa@test.local',
                    'cliente-b.avisos-multiempresa@test.local'
                )",
            "SELECT COUNT(*) FROM usuario
             WHERE id_usuario IN (9241, 9242, 9243, 9251, 9252)
                OR email IN (
                    'admin-a.avisos-multiempresa@test.local',
                    'tecnico-a.avisos-multiempresa@test.local',
                    'atencion-a.avisos-multiempresa@test.local',
                    'admin-b.avisos-multiempresa@test.local',
                    'tecnico-b.avisos-multiempresa@test.local'
                )",
            'SELECT COUNT(*) FROM usuario_rol WHERE id_usuario IN (9241, 9242, 9243, 9251, 9252)',
            'SELECT COUNT(*) FROM tarea
             WHERE id_tarea IN (9261, 9262, 9263, 9264, 9265, 9266, 9267, 9268, 9271, 9272)',
            "SELECT COUNT(*) FROM auditoria_evento
             WHERE id_empresa IN (9201, 9202)
                OR id_usuario IN (9241, 9242, 9243, 9251, 9252)
                OR (
                    entidad = 'aviso'
                    AND entidad_id IN (
                        9261, 9262, 9263, 9264, 9265,
                        9266, 9267, 9268, 9271, 9272
                    )
                )",
        ];

        $count = 0;
        foreach ($queries as $query) {
            $count += (int)$this->connection->query($query)->fetchColumn();
        }

        return $count;
    }

    public function fixtureCounts(): array
    {
        return [
            'empresa' => $this->countByIds('empresa', 'id_empresa', self::EMPRESA_IDS),
            'departamento' => $this->countByIds(
                'departamento',
                'id_departamento',
                self::DEPARTAMENTO_IDS
            ),
            'empleado' => $this->countByIds('empleado', 'id_empleado', self::EMPLEADO_IDS),
            'cliente' => $this->countByIds('cliente', 'id_cliente', self::CLIENTE_IDS),
            'usuario' => $this->countByIds('usuario', 'id_usuario', self::USUARIO_IDS),
            'usuario_rol' => $this->countByIds(
                'usuario_rol',
                'id_usuario',
                self::USUARIO_IDS
            ),
            'tarea' => $this->countByIds('tarea', 'id_tarea', self::TAREA_IDS),
        ];
    }

    public function assertFixtureCounts(): void
    {
        $expected = [
            'empresa' => 2,
            'departamento' => 2,
            'empleado' => 4,
            'cliente' => 2,
            'usuario' => 5,
            'usuario_rol' => 5,
            'tarea' => 10,
        ];
        $actual = $this->fixtureCounts();

        if ($actual !== $expected) {
            throw new RuntimeException(
                'Los recuentos del fixture no coinciden con la matriz aprobada.'
            );
        }
    }

    public function auditBaseline(): int
    {
        return (int)$this->connection
            ->query(
                'SELECT COALESCE(MAX(id_auditoria_evento), 0)
                 FROM auditoria_evento'
            )
            ->fetchColumn();
    }

    public function fixtureEventCountAfter(int $baseline): int
    {
        $statement = $this->connection->prepare(
            "SELECT COUNT(*)
             FROM auditoria_evento
             WHERE id_auditoria_evento > :baseline
               AND (
                   id_empresa IN (9201, 9202)
                   OR id_usuario IN (9241, 9242, 9243, 9251, 9252)
                   OR (
                       entidad = 'aviso'
                       AND entidad_id IN (
                           9261, 9262, 9263, 9264, 9265,
                           9266, 9267, 9268, 9271, 9272
                       )
                   )
               )"
        );
        $statement->execute(['baseline' => $baseline]);

        return (int)$statement->fetchColumn();
    }

    public function taskState(int $idTask): array
    {
        $statement = $this->connection->prepare(
            'SELECT id_tarea, id_empresa, estado, id_empleado, fecha_fin
             FROM tarea
             WHERE id_tarea = :id_tarea'
        );
        $statement->execute(['id_tarea' => $idTask]);
        $row = $statement->fetch();

        if (!is_array($row)) {
            throw new RuntimeException('No se encontro un aviso fixture esperado.');
        }

        return $this->normalizeTaskState($row);
    }

    public function allTaskStates(): array
    {
        $statement = $this->connection->query(
            'SELECT id_tarea, id_empresa, estado, id_empleado, fecha_fin
             FROM tarea
             WHERE id_tarea IN (
                 9261, 9262, 9263, 9264, 9265,
                 9266, 9267, 9268, 9271, 9272
             )
             ORDER BY id_tarea'
        );

        $states = [];
        foreach ($statement->fetchAll() as $row) {
            $normalized = $this->normalizeTaskState($row);
            $states[$normalized['id_tarea']] = $normalized;
        }

        return $states;
    }

    public function positiveAuditEvents(int $baseline): array
    {
        $statement = $this->connection->prepare(
            "SELECT
                id_auditoria_evento,
                id_empresa,
                id_usuario,
                entidad,
                entidad_id,
                accion,
                valores_anteriores,
                valores_nuevos
             FROM auditoria_evento
             WHERE id_auditoria_evento > :baseline
               AND entidad = 'aviso'
               AND entidad_id IN (9261, 9262, 9265, 9271)
             ORDER BY id_auditoria_evento"
        );
        $statement->execute(['baseline' => $baseline]);

        return $statement->fetchAll();
    }

    public function crossTenantCounts(int $baseline): array
    {
        $queries = [
            'evento_aviso' => "
                SELECT COUNT(*)
                FROM auditoria_evento AS ae
                JOIN tarea AS t ON t.id_tarea = ae.entidad_id
                WHERE ae.id_auditoria_evento > :baseline
                  AND ae.entidad = 'aviso'
                  AND ae.entidad_id IN (9261, 9262, 9265, 9271)
                  AND ae.id_empresa <> t.id_empresa",
            'evento_usuario' => "
                SELECT COUNT(*)
                FROM auditoria_evento AS ae
                JOIN usuario AS u ON u.id_usuario = ae.id_usuario
                WHERE ae.id_auditoria_evento > :baseline
                  AND ae.entidad = 'aviso'
                  AND ae.entidad_id IN (9261, 9262, 9265, 9271)
                  AND ae.id_empresa <> u.id_empresa",
            'evento_empleado' => "
                SELECT COUNT(*)
                FROM auditoria_evento AS ae
                JOIN empleado AS e
                  ON e.id_empleado = CAST(
                      JSON_UNQUOTE(JSON_EXTRACT(ae.valores_nuevos, '$.id_empleado'))
                      AS UNSIGNED
                  )
                WHERE ae.id_auditoria_evento > :baseline
                  AND ae.entidad = 'aviso'
                  AND ae.entidad_id IN (9261, 9262, 9265, 9271)
                  AND ae.id_empresa <> e.id_empresa",
        ];

        $counts = [];
        foreach ($queries as $name => $query) {
            $statement = $this->connection->prepare($query);
            $statement->execute(['baseline' => $baseline]);
            $counts[$name] = (int)$statement->fetchColumn();
        }

        return $counts;
    }

    public function rollbackFixture(): void
    {
        $this->connection->beginTransaction();

        try {
            $this->connection->exec(
                "DELETE FROM auditoria_evento
                 WHERE id_empresa IN (9201, 9202)
                    OR id_usuario IN (9241, 9242, 9243, 9251, 9252)
                    OR (
                        entidad = 'aviso'
                        AND entidad_id IN (
                            9261, 9262, 9263, 9264, 9265,
                            9266, 9267, 9268, 9271, 9272
                        )
                    )"
            );
            $this->connection->exec(
                'DELETE FROM tarea
                 WHERE id_tarea IN (
                     9261, 9262, 9263, 9264, 9265,
                     9266, 9267, 9268, 9271, 9272
                 )'
            );
            $this->connection->exec(
                'DELETE FROM cliente WHERE id_cliente IN (9231, 9232)'
            );
            $this->connection->exec(
                'DELETE FROM usuario_rol
                 WHERE id_usuario IN (9241, 9242, 9243, 9251, 9252)'
            );
            $this->connection->exec(
                'DELETE FROM usuario
                 WHERE id_usuario IN (9241, 9242, 9243, 9251, 9252)'
            );
            $this->connection->exec(
                'DELETE FROM empleado WHERE id_empleado IN (9211, 9212, 9221, 9222)'
            );
            $this->connection->exec(
                'DELETE FROM departamento WHERE id_departamento IN (9203, 9204)'
            );
            $this->connection->exec(
                'DELETE FROM empresa WHERE id_empresa IN (9201, 9202)'
            );

            $this->connection->commit();
        } catch (Throwable $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }
            throw $exception;
        }
    }

    public function assertClean(): void
    {
        $counts = $this->fixtureCounts();
        foreach ($counts as $count) {
            if ($count !== 0) {
                throw new RuntimeException(
                    'La limpieza final dejo filas del fixture.'
                );
            }
        }

        $events = (int)$this->connection
            ->query(
                "SELECT COUNT(*)
                 FROM auditoria_evento
                 WHERE id_empresa IN (9201, 9202)
                    OR id_usuario IN (9241, 9242, 9243, 9251, 9252)
                    OR (
                        entidad = 'aviso'
                        AND entidad_id IN (
                            9261, 9262, 9263, 9264, 9265,
                            9266, 9267, 9268, 9271, 9272
                        )
                    )"
            )
            ->fetchColumn();

        if ($events !== 0) {
            throw new RuntimeException(
                'La limpieza final dejo eventos de auditoria relacionados.'
            );
        }

        $this->assertBaseRolesIntact();
    }

    public function assertBaseRolesIntact(): void
    {
        $statement = $this->connection->query(
            'SELECT id_rol, nombre
             FROM rol
             WHERE id_rol IN (1, 2, 3)
             ORDER BY id_rol'
        );
        $roles = $statement->fetchAll();

        if (count($roles) !== 3) {
            throw new RuntimeException(
                'No estan disponibles los tres roles base requeridos.'
            );
        }

        $normalized = [];
        foreach ($roles as $role) {
            $normalized[(int)$role['id_rol']] = $this->normalizeRole((string)$role['nombre']);
        }

        if (
            ($normalized[1] ?? '') !== 'administrador'
            || ($normalized[2] ?? '') !== 'tecnico'
            || ($normalized[3] ?? '') !== 'atencion al cliente'
        ) {
            throw new RuntimeException(
                'Los roles base no coinciden con Administrador, Tecnico y Atencion al Cliente.'
            );
        }
    }

    private function countByIds(string $table, string $column, array $ids): int
    {
        $allowed = [
            'empresa.id_empresa',
            'departamento.id_departamento',
            'empleado.id_empleado',
            'cliente.id_cliente',
            'usuario.id_usuario',
            'usuario_rol.id_usuario',
            'tarea.id_tarea',
        ];

        if (!in_array($table . '.' . $column, $allowed, true)) {
            throw new LogicException('Consulta de recuento no permitida.');
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $statement = $this->connection->prepare(
            sprintf('SELECT COUNT(*) FROM %s WHERE %s IN (%s)', $table, $column, $placeholders)
        );
        $statement->execute($ids);

        return (int)$statement->fetchColumn();
    }

    private function normalizeTaskState(array $row): array
    {
        return [
            'id_tarea' => (int)$row['id_tarea'],
            'id_empresa' => (int)$row['id_empresa'],
            'estado' => (string)$row['estado'],
            'id_empleado' => $row['id_empleado'] === null ? null : (int)$row['id_empleado'],
            'fecha_fin' => $row['fecha_fin'] === null ? null : (string)$row['fecha_fin'],
        ];
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
