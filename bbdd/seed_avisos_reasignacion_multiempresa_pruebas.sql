-- Fixture local dedicado para AVISOS-REASIGNACION-MULTIEMPRESA.
-- NO ejecutar en produccion.
-- NO sustituye a bbdd/export_base_datos.sql ni al seed multiempresa anterior.
-- No crea partes ni recursos ajenos al alcance.
--
-- Requisitos:
--   - esquema base de EasyParte disponible;
--   - migracion de auditoria_evento aplicada;
--   - roles base 1 (Administrador), 2 (Tecnico) y
--     3 (Atencion al Cliente) disponibles.
--
-- Seguridad:
--   - usa exclusivamente IDs de fixture reservados entre 9201 y 9272;
--   - no contiene contrasenas en claro;
--   - reutiliza un hash bcrypt tecnico del dump local solo para este fixture;
--   - no desactiva claves foraneas;
--   - si detecta una colision o falta una dependencia, todas las inserciones
--     quedan convertidas en no-op.

-- ---------------------------------------------------------------------------
-- 1. Preflight de colisiones y dependencias. Debe revisarse su salida antes de
--    confirmar una importacion manual.
-- ---------------------------------------------------------------------------

SET @fixture_colisiones := (
  (SELECT COUNT(*) FROM empresa
    WHERE id_empresa IN (9201, 9202)
       OR nif IN ('T9201001A', 'T9202001B')
       OR email IN (
         'empresa-a.avisos-multiempresa@test.local',
         'empresa-b.avisos-multiempresa@test.local'
       ))
  + (SELECT COUNT(*) FROM departamento
      WHERE id_departamento IN (9203, 9204))
  + (SELECT COUNT(*) FROM empleado
      WHERE id_empleado IN (9211, 9212, 9221, 9222)
         OR nif IN ('T9211001A', 'T9212001A', 'T9221001B', 'T9222001B'))
  + (SELECT COUNT(*) FROM cliente
      WHERE id_cliente IN (9231, 9232)
         OR nif IN ('T9231001A', 'T9232001B')
         OR email IN (
           'cliente-a.avisos-multiempresa@test.local',
           'cliente-b.avisos-multiempresa@test.local'
         ))
  + (SELECT COUNT(*) FROM usuario
      WHERE id_usuario IN (9241, 9242, 9243, 9251, 9252)
         OR email IN (
           'admin-a.avisos-multiempresa@test.local',
           'tecnico-a.avisos-multiempresa@test.local',
           'atencion-a.avisos-multiempresa@test.local',
           'admin-b.avisos-multiempresa@test.local',
           'tecnico-b.avisos-multiempresa@test.local'
         ))
  + (SELECT COUNT(*) FROM usuario_rol
      WHERE id_usuario IN (9241, 9242, 9243, 9251, 9252))
  + (SELECT COUNT(*) FROM tarea
      WHERE id_tarea IN (9261, 9262, 9263, 9264, 9265, 9266, 9267, 9268, 9271, 9272))
  + (SELECT COUNT(*) FROM auditoria_evento
      WHERE id_empresa IN (9201, 9202)
         OR id_usuario IN (9241, 9242, 9243, 9251, 9252)
         OR (
           entidad = 'aviso'
           AND entidad_id IN (9261, 9262, 9263, 9264, 9265, 9266, 9267, 9268, 9271, 9272)
         ))
);

SET @fixture_dependencias_faltantes := (
  SELECT 3 - COUNT(*)
  FROM rol
  WHERE (id_rol = 1 AND nombre IN ('Administrador'))
     OR (id_rol = 2 AND nombre IN ('Tecnico', 'Técnico'))
     OR (
       id_rol = 3
       AND nombre IN (
         'Atencion al Cliente',
         'Atención al Cliente',
         'AtenciÃ³n al Cliente'
       )
     )
);

SET @fixture_preflight_ok := (
  @fixture_colisiones = 0
  AND @fixture_dependencias_faltantes = 0
);

SELECT
  @fixture_colisiones AS colisiones_detectadas,
  @fixture_dependencias_faltantes AS dependencias_faltantes,
  CASE
    WHEN @fixture_preflight_ok = 1
      THEN 'OK: el fixture puede insertarse'
    ELSE 'ABORTADO: no se insertara ninguna fila'
  END AS resultado_preflight;

-- ---------------------------------------------------------------------------
-- 2. Insercion transaccional condicionada por el preflight.
-- Ejecutar con un cliente configurado para detenerse ante el primer error.
-- Si cualquier sentencia falla, no ejecutar COMMIT y emitir ROLLBACK.
-- ---------------------------------------------------------------------------

START TRANSACTION;

INSERT INTO empresa
  (id_empresa, nif, nombre, prefijo, telefono, email, pais, poblacion, direccion, activo)
SELECT
  semilla.id_empresa,
  semilla.nif,
  semilla.nombre,
  semilla.prefijo,
  semilla.telefono,
  semilla.email,
  semilla.pais,
  semilla.poblacion,
  semilla.direccion,
  semilla.activo
FROM (
  SELECT
    9201 AS id_empresa,
    'T9201001A' AS nif,
    'Avisos Multiempresa A' AS nombre,
    '+34' AS prefijo,
    '600920101' AS telefono,
    'empresa-a.avisos-multiempresa@test.local' AS email,
    'Espana' AS pais,
    'Madrid' AS poblacion,
    'Calle Fixture A 1' AS direccion,
    1 AS activo
  UNION ALL
  SELECT
    9202,
    'T9202001B',
    'Avisos Multiempresa B',
    '+34',
    '600920201',
    'empresa-b.avisos-multiempresa@test.local',
    'Espana',
    'Madrid',
    'Calle Fixture B 1',
    1
) AS semilla
WHERE @fixture_preflight_ok = 1;

INSERT INTO departamento
  (id_departamento, nombre, id_empresa)
SELECT
  semilla.id_departamento,
  semilla.nombre,
  semilla.id_empresa
FROM (
  SELECT 9203 AS id_departamento, 'Servicio Tecnico A' AS nombre, 9201 AS id_empresa
  UNION ALL
  SELECT 9204, 'Servicio Tecnico B', 9202
) AS semilla
WHERE @fixture_preflight_ok = 1;

INSERT INTO empleado
  (id_empleado, nombre, apellido, apellido_2, extension_tel, prefijo, movil, nif, activo, id_departamento, id_empresa)
SELECT
  semilla.id_empleado,
  semilla.nombre,
  semilla.apellido,
  semilla.apellido_2,
  semilla.extension_tel,
  semilla.prefijo,
  semilla.movil,
  semilla.nif,
  semilla.activo,
  semilla.id_departamento,
  semilla.id_empresa
FROM (
  SELECT
    9211 AS id_empleado,
    'Tecnico' AS nombre,
    'Fixture A1' AS apellido,
    NULL AS apellido_2,
    NULL AS extension_tel,
    '+34' AS prefijo,
    '600921101' AS movil,
    'T9211001A' AS nif,
    1 AS activo,
    9203 AS id_departamento,
    9201 AS id_empresa
  UNION ALL
  SELECT 9212, 'Tecnico', 'Fixture A2', NULL, NULL, '+34', '600921201', 'T9212001A', 1, 9203, 9201
  UNION ALL
  SELECT 9221, 'Tecnico', 'Fixture B1', NULL, NULL, '+34', '600922101', 'T9221001B', 1, 9204, 9202
  UNION ALL
  SELECT 9222, 'Tecnico', 'Fixture B2', NULL, NULL, '+34', '600922201', 'T9222001B', 1, 9204, 9202
) AS semilla
WHERE @fixture_preflight_ok = 1;

INSERT INTO cliente
  (id_cliente, nif, nombre, poblacion, direccion, prefijo, contacto, email, id_empresa, cuota, activo)
SELECT
  semilla.id_cliente,
  semilla.nif,
  semilla.nombre,
  semilla.poblacion,
  semilla.direccion,
  semilla.prefijo,
  semilla.contacto,
  semilla.email,
  semilla.id_empresa,
  semilla.cuota,
  semilla.activo
FROM (
  SELECT
    9231 AS id_cliente,
    'T9231001A' AS nif,
    'Cliente Avisos A' AS nombre,
    'Madrid' AS poblacion,
    'Calle Cliente A 1' AS direccion,
    '+34' AS prefijo,
    '600923101' AS contacto,
    'cliente-a.avisos-multiempresa@test.local' AS email,
    9201 AS id_empresa,
    0 AS cuota,
    1 AS activo
  UNION ALL
  SELECT
    9232,
    'T9232001B',
    'Cliente Avisos B',
    'Madrid',
    'Calle Cliente B 1',
    '+34',
    '600923201',
    'cliente-b.avisos-multiempresa@test.local',
    9202,
    0,
    1
) AS semilla
WHERE @fixture_preflight_ok = 1;

-- Hash bcrypt tecnico reutilizado del dump local. Es unicamente un fixture
-- local: no documenta la contrasena asociada ni debe reutilizarse fuera de
-- este entorno. Puede sustituirse antes de importar por otro hash local.
INSERT INTO usuario
  (id_usuario, nombre, email, password_hash, activo, id_empresa, id_empleado, created_at)
SELECT
  semilla.id_usuario,
  semilla.nombre,
  semilla.email,
  semilla.password_hash,
  semilla.activo,
  semilla.id_empresa,
  semilla.id_empleado,
  semilla.created_at
FROM (
  SELECT
    9241 AS id_usuario,
    'Administrador Fixture A' AS nombre,
    'admin-a.avisos-multiempresa@test.local' AS email,
    '$2y$12$AU2.nuSbbnKgfcdPA2Twu.8z/oY4HesHaXoBuNjxPq8QEKYMxi7RW' AS password_hash,
    1 AS activo,
    9201 AS id_empresa,
    NULL AS id_empleado,
    NOW() AS created_at
  UNION ALL
  SELECT
    9242,
    'Tecnico Fixture A',
    'tecnico-a.avisos-multiempresa@test.local',
    '$2y$12$AU2.nuSbbnKgfcdPA2Twu.8z/oY4HesHaXoBuNjxPq8QEKYMxi7RW',
    1,
    9201,
    9211,
    NOW()
  UNION ALL
  SELECT
    9243,
    'Atencion al Cliente Fixture A',
    'atencion-a.avisos-multiempresa@test.local',
    '$2y$12$AU2.nuSbbnKgfcdPA2Twu.8z/oY4HesHaXoBuNjxPq8QEKYMxi7RW',
    1,
    9201,
    NULL,
    NOW()
  UNION ALL
  SELECT
    9251,
    'Administrador Fixture B',
    'admin-b.avisos-multiempresa@test.local',
    '$2y$12$AU2.nuSbbnKgfcdPA2Twu.8z/oY4HesHaXoBuNjxPq8QEKYMxi7RW',
    1,
    9202,
    NULL,
    NOW()
  UNION ALL
  SELECT
    9252,
    'Tecnico Fixture B',
    'tecnico-b.avisos-multiempresa@test.local',
    '$2y$12$AU2.nuSbbnKgfcdPA2Twu.8z/oY4HesHaXoBuNjxPq8QEKYMxi7RW',
    1,
    9202,
    9221,
    NOW()
) AS semilla
WHERE @fixture_preflight_ok = 1;

INSERT INTO usuario_rol
  (id_usuario, id_rol)
SELECT
  semilla.id_usuario,
  semilla.id_rol
FROM (
  SELECT 9241 AS id_usuario, 1 AS id_rol
  UNION ALL
  SELECT 9242, 2
  UNION ALL
  SELECT 9243, 3
  UNION ALL
  SELECT 9251, 1
  UNION ALL
  SELECT 9252, 2
) AS semilla
WHERE @fixture_preflight_ok = 1;

INSERT INTO tarea
  (id_tarea, descripcion, fecha_alta, fecha_fin, importancia, estado, persona_contacto, telefono_contacto, id_empleado, id_cliente, id_departamento, id_empresa, id_usuario_creador)
SELECT
  semilla.id_tarea,
  semilla.descripcion,
  semilla.fecha_alta,
  semilla.fecha_fin,
  semilla.importancia,
  semilla.estado,
  semilla.persona_contacto,
  semilla.telefono_contacto,
  semilla.id_empleado,
  semilla.id_cliente,
  semilla.id_departamento,
  semilla.id_empresa,
  semilla.id_usuario_creador
FROM (
  SELECT
    9261 AS id_tarea,
    'Fixture A: aviso propio para reasignar' AS descripcion,
    NOW() AS fecha_alta,
    NULL AS fecha_fin,
    'Normal' AS importancia,
    'Pendiente' AS estado,
    'Contacto Fixture A' AS persona_contacto,
    '600926101' AS telefono_contacto,
    9211 AS id_empleado,
    9231 AS id_cliente,
    9203 AS id_departamento,
    9201 AS id_empresa,
    9241 AS id_usuario_creador
  UNION ALL
  SELECT
    9262,
    'Fixture A: aviso libre para asignacion de administrador',
    NOW(),
    NULL,
    'Alta',
    'Pendiente',
    'Contacto Fixture A',
    '600926201',
    NULL,
    9231,
    9203,
    9201,
    9241
  UNION ALL
  SELECT
    9263,
    'Fixture A: aviso ajeno al Tecnico A',
    NOW(),
    NULL,
    'Normal',
    'Pendiente',
    'Contacto Fixture A',
    '600926301',
    9212,
    9231,
    9203,
    9201,
    9241
  UNION ALL
  SELECT
    9264,
    'Fixture A: aviso cancelado del Tecnico A',
    NOW(),
    NOW(),
    'Normal',
    'Cancelada',
    'Contacto Fixture A',
    '600926401',
    9211,
    9231,
    9203,
    9201,
    9241
  UNION ALL
  SELECT
    9265,
    'Fixture A: aviso libre para coger',
    NOW(),
    NULL,
    'Urgente',
    'Pendiente',
    'Contacto Fixture A',
    '600926501',
    NULL,
    9231,
    9203,
    9201,
    9241
  UNION ALL
  SELECT
    9266,
    'Fixture A: aviso finalizado asignado del Tecnico A',
    NOW(),
    NOW(),
    'Normal',
    'Finalizada',
    'Contacto Fixture A',
    '600926601',
    9211,
    9231,
    9203,
    9201,
    9241
  UNION ALL
  SELECT
    9267,
    'Fixture A: aviso finalizado libre',
    NOW(),
    NOW(),
    'Normal',
    'Finalizada',
    'Contacto Fixture A',
    '600926701',
    NULL,
    9231,
    9203,
    9201,
    9241
  UNION ALL
  SELECT
    9268,
    'Fixture A: aviso cancelado libre',
    NOW(),
    NOW(),
    'Normal',
    'Cancelada',
    'Contacto Fixture A',
    '600926801',
    NULL,
    9231,
    9203,
    9201,
    9241
  UNION ALL
  SELECT
    9271,
    'Fixture B: aviso propio para reasignar',
    NOW(),
    NULL,
    'Normal',
    'Pendiente',
    'Contacto Fixture B',
    '600927101',
    9221,
    9232,
    9204,
    9202,
    9251
  UNION ALL
  SELECT
    9272,
    'Fixture B: aviso libre para aislamiento de listados',
    NOW(),
    NULL,
    'Baja',
    'Pendiente',
    'Contacto Fixture B',
    '600927201',
    NULL,
    9232,
    9204,
    9202,
    9251
) AS semilla
WHERE @fixture_preflight_ok = 1;

COMMIT;

SELECT
  CASE
    WHEN @fixture_preflight_ok = 1
      THEN 'FIXTURE INSERTADO: verificar los recuentos'
    ELSE 'FIXTURE NO INSERTADO: resolver colisiones o dependencias'
  END AS resultado_final;

SELECT 'empresa' AS recurso, COUNT(*) AS filas
FROM empresa
WHERE id_empresa IN (9201, 9202)
UNION ALL
SELECT 'departamento', COUNT(*)
FROM departamento
WHERE id_departamento IN (9203, 9204)
UNION ALL
SELECT 'empleado', COUNT(*)
FROM empleado
WHERE id_empleado IN (9211, 9212, 9221, 9222)
UNION ALL
SELECT 'cliente', COUNT(*)
FROM cliente
WHERE id_cliente IN (9231, 9232)
UNION ALL
SELECT 'usuario', COUNT(*)
FROM usuario
WHERE id_usuario IN (9241, 9242, 9243, 9251, 9252)
UNION ALL
SELECT 'usuario_rol', COUNT(*)
FROM usuario_rol
WHERE id_usuario IN (9241, 9242, 9243, 9251, 9252)
UNION ALL
SELECT 'tarea', COUNT(*)
FROM tarea
WHERE id_tarea IN (9261, 9262, 9263, 9264, 9265, 9266, 9267, 9268, 9271, 9272);

-- ---------------------------------------------------------------------------
-- 3. Rollback manual exacto.
-- Descomentar y ejecutar el bloque completo solo en local, tras verificar los
-- IDs, con el cliente configurado para detenerse ante el primer error. Si una
-- sentencia falla, no ejecutar COMMIT y emitir ROLLBACK. No requiere
-- desactivar claves foraneas.
-- ---------------------------------------------------------------------------

-- START TRANSACTION;
--
-- -- 1) Eventos de auditoria relacionados.
-- DELETE FROM auditoria_evento
-- WHERE id_empresa IN (9201, 9202)
--    OR id_usuario IN (9241, 9242, 9243, 9251, 9252)
--    OR (
--      entidad = 'aviso'
--      AND entidad_id IN (9261, 9262, 9263, 9264, 9265, 9266, 9267, 9268, 9271, 9272)
--    );
--
-- -- 2) Avisos.
-- DELETE FROM tarea
-- WHERE id_tarea IN (9261, 9262, 9263, 9264, 9265, 9266, 9267, 9268, 9271, 9272);
--
-- -- 3) Clientes.
-- DELETE FROM cliente
-- WHERE id_cliente IN (9231, 9232);
--
-- -- 4) Usuarios, incluidas primero sus relaciones de rol.
-- DELETE FROM usuario_rol
-- WHERE id_usuario IN (9241, 9242, 9243, 9251, 9252);
--
-- DELETE FROM usuario
-- WHERE id_usuario IN (9241, 9242, 9243, 9251, 9252);
--
-- -- 5) Empleados.
-- DELETE FROM empleado
-- WHERE id_empleado IN (9211, 9212, 9221, 9222);
--
-- -- 6) Departamentos.
-- DELETE FROM departamento
-- WHERE id_departamento IN (9203, 9204);
--
-- -- 7) Empresas.
-- DELETE FROM empresa
-- WHERE id_empresa IN (9201, 9202);
--
-- COMMIT;
