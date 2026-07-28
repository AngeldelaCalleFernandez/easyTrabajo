# Validación manual — avisos y reasignación auditada multiempresa

Fecha de preparación: 2026-07-28
Entorno previsto: local/XAMPP
Fase: AVISOS-REASIGNACION-MULTIEMPRESA
Estado: fixtures y matriz preparados; ejecución pendiente

## Objetivo

Validar con dos empresas reales de prueba que:

- los listados de avisos y empleados asignables respetan la empresa
  autenticada;
- un usuario de Empresa A no puede consultar ni reasignar avisos de Empresa B;
- las asignaciones, reasignaciones y tomas correctas registran el
  `id_empresa` esperado en `auditoria_evento`;
- no aparecen eventos cruzados entre empresas;
- las peticiones rechazadas no modifican el aviso ni crean eventos.

Este documento prepara la ejecución. No acredita resultados hasta que una
persona complete la columna `Resultado obtenido` y adjunte evidencia local no
sensible.

## Alcance y contratos

```txt
GET /api/avisos
GET /api/avisos/empleados-asignables
PUT /api/avisos/{id}/asignar
PUT /api/avisos/{id}/coger
```

No se usa `DELETE`. La cancelación, las partes, los clientes como funcionalidad
y los endpoints generales quedan fuera de las operaciones de esta matriz.

## Fixture dedicado

Archivo:

```txt
bbdd/seed_avisos_reasignacion_multiempresa_pruebas.sql
```

El fixture no modifica el seed multiempresa anterior ni
`bbdd/export_base_datos.sql`. No desactiva claves foráneas, no crea partes y no
contiene contraseñas en claro. Los roles `1` y `2` son dependencias existentes
del catálogo; todos los identificadores de filas creadas por este fixture están
reservados entre `9201` y `9272`.

### Inventario de IDs

| Recurso | Empresa A | Empresa B |
|---|---:|---:|
| Empresa | 9201 | 9202 |
| Departamento | 9203 | 9204 |
| Empleado vinculado al Técnico | 9211 | 9221 |
| Segundo empleado activo | 9212 | 9222 |
| Cliente | 9231 | 9232 |
| Administrador | 9241 | 9251 |
| Técnico | 9242, vinculado a 9211 | 9252, vinculado a 9221 |
| Avisos | 9261–9265 | 9271–9272 |

### Avisos iniciales

| ID | Empresa | Estado | Empleado inicial | Uso |
|---:|---:|---|---:|---|
| 9261 | 9201 | Pendiente | 9211 | Reasignación del Técnico A a 9212 |
| 9262 | 9201 | Pendiente | Libre | Asignación del Administrador A a 9212 |
| 9263 | 9201 | Pendiente | 9212 | Aviso ajeno al Técnico A |
| 9264 | 9201 | Cancelada | 9211 | Rechazo de reasignación por estado |
| 9265 | 9201 | Pendiente | Libre | Toma por el Técnico A |
| 9271 | 9202 | Pendiente | 9221 | Reasignación del Administrador B a 9222 |
| 9272 | 9202 | Pendiente | Libre | Aislamiento de listados |

## Preparación segura

1. Trabajar únicamente en una base local o de pruebas aislada. No usar
   producción.
2. Confirmar que la migración de `auditoria_evento` está aplicada.
3. Hacer una copia de seguridad verificable antes de importar el fixture.
4. Revisar el script completo, especialmente el preflight y el rollback.
5. Ejecutar primero las consultas de preflight del propio archivo y comprobar:
   `colisiones_detectadas = 0`, `dependencias_faltantes = 0` y mensaje `OK`.
6. Importar el archivo completo solo si el preflight es correcto. Si detecta
   una colisión o una dependencia ausente, el script no inserta ninguna fila.
7. Confirmar los recuentos finales esperados: 2 empresas, 2 departamentos,
   4 empleados, 2 clientes, 4 usuarios, 4 relaciones de rol y 7 avisos.
8. Preparar cuatro sesiones separadas: Administrador A, Técnico A,
   Administrador B y Técnico B. Referenciar las credenciales o tokens como
   `Sesión A/B`; no copiarlos en este documento.
9. Ejecutar primero las pruebas negativas y después las positivas. Así los
   avisos conservan su estado inicial durante la validación de rechazos.

El hash incluido sigue el patrón técnico/local ya usado en el seed de pruebas
existente. Si no se dispone de la clave local correspondiente, se debe sustituir
antes de importar por un hash bcrypt generado localmente. No documentar la
contraseña.

## Backup mínimo

La copia debe incluir, como mínimo, estas tablas:

```txt
empresa
departamento
empleado
cliente
usuario
usuario_rol
tarea
auditoria_evento
```

Registrar de forma externa y no sensible:

```txt
Fecha/hora del backup:
Base local:
Ubicación protegida:
Restauración de prueba realizada: sí/no
Responsable:
```

## Baseline de auditoría y estado

Antes de cualquier petición, guardar el valor devuelto:

```sql
SELECT COALESCE(MAX(id_auditoria_evento), 0) AS baseline_auditoria
FROM auditoria_evento;
```

Guardar también el estado inicial de los avisos:

```sql
SELECT id_tarea, id_empresa, estado, id_empleado, fecha_fin
FROM tarea
WHERE id_tarea IN (9261, 9262, 9263, 9264, 9265, 9271, 9272)
ORDER BY id_tarea;
```

Para cada prueba negativa se debe tomar un baseline adicional inmediatamente
antes de la petición:

```sql
SELECT COALESCE(MAX(id_auditoria_evento), 0) AS baseline_rechazo
FROM auditoria_evento;
```

No reutilizar un baseline antiguo si otras sesiones pueden generar eventos.

## Pruebas negativas

En los rechazos entre empresas se admite `403` o `404` genérico según la
política de no revelar la existencia del recurso. En todos los casos el aviso
debe conservarse sin cambios y no debe aparecer un evento nuevo.

| ID | Sesión | Petición o comprobación | Resultado esperado | Resultado obtenido | Estado |
|---|---|---|---|---|---|
| AVISO-ME-N01 | Administrador A | `PUT /api/avisos/9271/asignar` con `{"id_empleado":9212}` | Rechazo genérico; 9271 sigue en 9221; cero eventos nuevos | Pendiente | Pendiente |
| AVISO-ME-N02 | Administrador A | `PUT /api/avisos/9262/asignar` con `{"id_empleado":9221}` | 403 genérico; 9262 sigue libre; cero eventos nuevos | Pendiente | Pendiente |
| AVISO-ME-N03 | Técnico A | `PUT /api/avisos/9271/asignar` con `{"id_empleado":9212}` | Rechazo genérico; 9271 sigue en 9221; cero eventos nuevos | Pendiente | Pendiente |
| AVISO-ME-N04 | Técnico A | `PUT /api/avisos/9264/asignar` con `{"id_empleado":9212}` | 403; 9264 sigue cancelado y asignado a 9211; cero eventos nuevos | Pendiente | Pendiente |
| AVISO-ME-N05 | Técnico A | `PUT /api/avisos/9261/asignar` con `{"id_empleado":9211}` | 403; 9261 sigue en 9211; cero eventos nuevos | Pendiente | Pendiente |
| AVISO-ME-N06 | Administrador A | `GET /api/avisos` y `GET /api/avisos/empleados-asignables` | No aparecen avisos 9271/9272 ni empleados 9221/9222 | Pendiente | Pendiente |
| AVISO-ME-N07 | Técnico A | Mismos listados | No aparecen avisos ni empleados de Empresa B; tampoco aparece 9211 entre asignables | Pendiente | Pendiente |

### Comprobación posterior a cada rechazo

Sustituir `__BASELINE_RECHAZO__` por el valor capturado justo antes de la
petición:

```sql
SELECT COUNT(*) AS eventos_creados_por_rechazo
FROM auditoria_evento
WHERE id_auditoria_evento > __BASELINE_RECHAZO__
  AND (
    id_usuario IN (9241, 9242)
    OR entidad_id IN (9261, 9262, 9264, 9271)
  );
```

Resultado esperado: `0`.

Comprobar el aviso implicado con la consulta de estado inicial y comparar
`id_empresa`, `estado`, `id_empleado` y `fecha_fin`. No basta con revisar solo
la respuesta HTTP.

## Pruebas positivas

| ID | Sesión | Petición o comprobación | Resultado esperado | Resultado obtenido | Estado |
|---|---|---|---|---|---|
| AVISO-ME-P01 | Administrador A | `GET /api/avisos` | Solo avisos 9261–9265; ningún aviso B | Pendiente | Pendiente |
| AVISO-ME-P02 | Técnico A | `GET /api/avisos` | Avisos propios o libres de A: 9261, 9262, 9264 y 9265; no 9263 ni avisos B | Pendiente | Pendiente |
| AVISO-ME-P03 | Administrador A | `GET /api/avisos/empleados-asignables` | Empleados 9211 y 9212; ningún empleado B | Pendiente | Pendiente |
| AVISO-ME-P04 | Técnico A | `GET /api/avisos/empleados-asignables` | Solo 9212; no 9211, 9221 ni 9222 | Pendiente | Pendiente |
| AVISO-ME-P05 | Técnico A | `PUT /api/avisos/9261/asignar` con `{"id_empleado":9212}` | 200; 9261 pasa de 9211 a 9212; evento `aviso_reasignado`, empresa 9201, usuario 9242 | Pendiente | Pendiente |
| AVISO-ME-P06 | Administrador A | `PUT /api/avisos/9262/asignar` con `{"id_empleado":9212}` | 200; 9262 pasa de libre a 9212; evento `aviso_asignado`, empresa 9201, usuario 9241 | Pendiente | Pendiente |
| AVISO-ME-P07 | Técnico A | `PUT /api/avisos/9265/coger` sin `id_empleado` | 200; 9265 pasa de libre a 9211; evento `aviso_autoasignado`, empresa 9201, usuario 9242 | Pendiente | Pendiente |
| AVISO-ME-P08 | Administrador B | `PUT /api/avisos/9271/asignar` con `{"id_empleado":9222}` | 200; 9271 pasa de 9221 a 9222; evento `aviso_reasignado`, empresa 9202, usuario 9251 | Pendiente | Pendiente |
| AVISO-ME-P09 | SQL de comprobación | Revisar eventos A/B posteriores al baseline | Cada evento conserva empresa, usuario, aviso y empleado de su tenant | Pendiente | Pendiente |

Nota: 9264 es propio del Técnico A pero está cancelado. Puede aparecer en el
listado general actual; la regla que se valida es que no pueda reasignarse.

## Consultas SQL de comprobación

Estas consultas son para una ejecución manual posterior. No sustituyen las
pruebas por API.

### Inventario y pertenencia

```sql
SELECT id_empresa, nombre, activo
FROM empresa
WHERE id_empresa IN (9201, 9202)
ORDER BY id_empresa;

SELECT id_empleado, id_empresa, id_departamento, activo
FROM empleado
WHERE id_empleado IN (9211, 9212, 9221, 9222)
ORDER BY id_empleado;

SELECT id_usuario, id_empresa, id_empleado, activo
FROM usuario
WHERE id_usuario IN (9241, 9242, 9251, 9252)
ORDER BY id_usuario;

SELECT id_tarea, id_empresa, estado, id_empleado, id_cliente, id_departamento
FROM tarea
WHERE id_tarea IN (9261, 9262, 9263, 9264, 9265, 9271, 9272)
ORDER BY id_tarea;
```

### Estado final esperado de los avisos positivos

```sql
SELECT id_tarea, id_empresa, estado, id_empleado
FROM tarea
WHERE id_tarea IN (9261, 9262, 9265, 9271)
ORDER BY id_tarea;
```

Resultado esperado:

| Aviso | Empresa | Estado | Empleado final |
|---:|---:|---|---:|
| 9261 | 9201 | Pendiente | 9212 |
| 9262 | 9201 | Pendiente | 9212 |
| 9265 | 9201 | Pendiente | 9211 |
| 9271 | 9202 | Pendiente | 9222 |

### Eventos creados por la matriz

Sustituir `__BASELINE_AUDITORIA__`:

```sql
SELECT
  id_auditoria_evento,
  id_empresa,
  id_usuario,
  entidad,
  entidad_id,
  accion,
  valores_anteriores,
  valores_nuevos,
  fecha
FROM auditoria_evento
WHERE id_auditoria_evento > __BASELINE_AUDITORIA__
  AND entidad = 'aviso'
  AND entidad_id IN (9261, 9262, 9265, 9271)
ORDER BY id_auditoria_evento;
```

Resultado esperado: cuatro eventos, uno por operación positiva:

| Aviso | Empresa | Usuario | Acción | Anterior | Nuevo |
|---:|---:|---:|---|---:|---:|
| 9261 | 9201 | 9242 | aviso_reasignado | 9211 | 9212 |
| 9262 | 9201 | 9241 | aviso_asignado | null | 9212 |
| 9265 | 9201 | 9242 | aviso_autoasignado | null | 9211 |
| 9271 | 9202 | 9251 | aviso_reasignado | 9221 | 9222 |

### Detección de eventos cruzados

Cada consulta debe devolver cero filas.

Evento y aviso con empresas distintas:

```sql
SELECT
  ae.id_auditoria_evento,
  ae.id_empresa AS empresa_evento,
  t.id_empresa AS empresa_aviso,
  ae.entidad_id AS id_aviso
FROM auditoria_evento AS ae
JOIN tarea AS t
  ON t.id_tarea = ae.entidad_id
WHERE ae.id_auditoria_evento > __BASELINE_AUDITORIA__
  AND ae.entidad = 'aviso'
  AND ae.entidad_id IN (9261, 9262, 9265, 9271)
  AND ae.id_empresa <> t.id_empresa;
```

Evento y usuario actor con empresas distintas:

```sql
SELECT
  ae.id_auditoria_evento,
  ae.id_empresa AS empresa_evento,
  u.id_empresa AS empresa_usuario,
  ae.id_usuario
FROM auditoria_evento AS ae
JOIN usuario AS u
  ON u.id_usuario = ae.id_usuario
WHERE ae.id_auditoria_evento > __BASELINE_AUDITORIA__
  AND ae.entidad = 'aviso'
  AND ae.entidad_id IN (9261, 9262, 9265, 9271)
  AND ae.id_empresa <> u.id_empresa;
```

Empleado nuevo perteneciente a otra empresa:

```sql
SELECT
  ae.id_auditoria_evento,
  ae.id_empresa AS empresa_evento,
  e.id_empresa AS empresa_empleado,
  JSON_UNQUOTE(JSON_EXTRACT(ae.valores_nuevos, '$.id_empleado')) AS id_empleado_nuevo
FROM auditoria_evento AS ae
JOIN empleado AS e
  ON e.id_empleado = CAST(
    JSON_UNQUOTE(JSON_EXTRACT(ae.valores_nuevos, '$.id_empleado'))
    AS UNSIGNED
  )
WHERE ae.id_auditoria_evento > __BASELINE_AUDITORIA__
  AND ae.entidad = 'aviso'
  AND ae.entidad_id IN (9261, 9262, 9265, 9271)
  AND ae.id_empresa <> e.id_empresa;
```

Eventos inesperados de las pruebas negativas:

```sql
SELECT id_auditoria_evento, id_empresa, id_usuario, entidad_id, accion
FROM auditoria_evento
WHERE id_auditoria_evento > __BASELINE_AUDITORIA__
  AND entidad = 'aviso'
  AND entidad_id IN (9263, 9264, 9272)
ORDER BY id_auditoria_evento;
```

Resultado esperado: cero filas. Para 9261, 9262 y 9271, que participan también
en pruebas positivas, la ausencia de eventos de los rechazos debe demostrarse
con sus baselines individuales, no con esta última consulta global.

## Criterios de éxito

La matriz se considera correcta solo si se cumplen todos:

- Administrador A recibe exclusivamente avisos y empleados de Empresa A.
- Técnico A recibe únicamente avisos propios o libres de Empresa A.
- Administrador A recibe como asignables 9211 y 9212.
- Técnico A recibe como asignable 9212 y no recibe 9211 ni empleados B.
- Las cuatro operaciones positivas dejan el aviso en el empleado esperado.
- Cada operación positiva crea exactamente un evento con la empresa, usuario,
  aviso, acción y valores anterior/nuevo esperados.
- Las tres consultas de cruce entre evento, aviso, usuario y empleado devuelven
  cero filas.
- Cada petición negativa mantiene el aviso sin cambios y crea cero eventos.
- No se han documentado tokens, contraseñas ni secretos.

Un resultado parcial o una evidencia basada solo en la interfaz no permite
marcar la prueba como superada.

## Rollback

El seed contiene al final un bloque comentado de rollback. Debe ejecutarse
completo, dentro de su transacción y con un cliente configurado para detenerse
ante el primer error, en este orden:

1. eventos relacionados de `auditoria_evento`;
2. avisos de `tarea`;
3. clientes;
4. relaciones `usuario_rol` y usuarios;
5. empleados;
6. departamentos;
7. empresas.

No se desactivan claves foráneas. Si durante las pruebas se crean dependencias
adicionales fuera del alcance, no se debe ejecutar `COMMIT`: emitir `ROLLBACK`,
revisar y retirar esas dependencias explícitamente, sin forzar
`FOREIGN_KEY_CHECKS=0`.

Después del rollback:

```sql
SELECT COUNT(*) AS filas_restantes
FROM (
  SELECT id_empresa AS id FROM empresa WHERE id_empresa IN (9201, 9202)
  UNION ALL
  SELECT id_departamento FROM departamento WHERE id_departamento IN (9203, 9204)
  UNION ALL
  SELECT id_empleado FROM empleado WHERE id_empleado IN (9211, 9212, 9221, 9222)
  UNION ALL
  SELECT id_cliente FROM cliente WHERE id_cliente IN (9231, 9232)
  UNION ALL
  SELECT id_usuario FROM usuario WHERE id_usuario IN (9241, 9242, 9251, 9252)
  UNION ALL
  SELECT id_tarea FROM tarea WHERE id_tarea IN (9261, 9262, 9263, 9264, 9265, 9271, 9272)
) AS fixture;
```

Resultado esperado: `0`.

Comprobar aparte que no quedan eventos:

```sql
SELECT COUNT(*) AS eventos_restantes
FROM auditoria_evento
WHERE id_empresa IN (9201, 9202)
   OR id_usuario IN (9241, 9242, 9251, 9252)
   OR (
     entidad = 'aviso'
     AND entidad_id IN (9261, 9262, 9263, 9264, 9265, 9271, 9272)
   );
```

Resultado esperado: `0`.

## Pendientes

- Ejecutar por API la prueba de reasignación de un aviso finalizado.
- Repetir esta matriz en el futuro mediante automatización.
- Implementar un endpoint protegido y filtrado por empresa para consultar
  auditoría; las consultas SQL de este documento son solo verificación local.
- Ejecutar y registrar esta matriz antes de dar por validado el aislamiento
  multiempresa de auditoría.

INC-0013 permanece abierta. PEN-0006 y PEN-0009 permanecen parciales: este
documento y el fixture preparan pruebas, pero no demuestran su ejecución ni
completan tenant o auditoría general.
