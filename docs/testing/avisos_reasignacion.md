# Validación manual — reasignación auditada de avisos

Fecha de registro: 2026-07-27
Entorno: local/XAMPP
Fase: AVISOS-REASIGNACION-AUDITADA — Fase 4
Estado: resultados manuales registrados

## Objetivo

Registrar la validación manual del flujo que separa la edición general, la
asignación o reasignación, la toma de avisos libres y la cancelación. La
validación comprueba además el primer alcance persistente de auditoría aplicado
a las asignaciones de avisos.

Las pruebas AVISO-REA-001 a AVISO-REA-012 fueron ejecutadas manualmente por la
persona responsable del proyecto. En ese bloque, Codex únicamente registra los
resultados aportados y no ejecutó SQL ni repitió las operaciones contra la base
de datos. La ejecución posterior de TEST-AVISOS-FIN-001 se identifica y
documenta de forma separada.

## Contratos incluidos

```txt
GET /api/avisos/empleados-asignables
PUT /api/avisos/{id}/asignar
PUT /api/avisos/{id}/coger
PUT /api/avisos/{id}/cancelar
PUT /api/avisos/{id}
```

Reglas verificadas:

- `PUT /api/avisos/{id}` conserva la asignación existente aunque el cliente
  intente incluir `id_empleado`.
- `PUT /api/avisos/{id}/asignar` es el único contrato de esta fase para asignar
  o reasignar a un empleado concreto.
- `PUT /api/avisos/{id}/coger` obtiene el empleado desde el usuario autenticado;
  no acepta otro empleado como autoridad del cliente.
- La cancelación sigue usando su endpoint específico y no elimina físicamente
  el aviso.

## Matriz de pruebas manuales

| ID | Acción | Rol o condición | Resultado esperado | Resultado aportado | Estado |
|---|---|---|---|---|---|
| AVISO-REA-001 | Aplicar la migración `20260723_crear_auditoria_evento.sql` | Entorno local con copia de seguridad y prueba previa | La tabla queda disponible sin documentar secretos | Migración aplicada en local tras backup y prueba | Correcta |
| AVISO-REA-002 | Reasignar un aviso propio mediante `PUT /api/avisos/{id}/asignar` | Técnico propietario; empleado destino distinto | La reasignación se completa | La reasignación funciona | Correcta |
| AVISO-REA-003 | Consultar la auditoría generada por AVISO-REA-002 | Reasignación correcta | Existe un evento persistente asociado al aviso | El evento existe en `auditoria_evento` | Correcta |
| AVISO-REA-004 | Reasignar un aviso ajeno | Técnico no propietario | La API rechaza la operación y no cambia la asignación | El Técnico no puede reasignarlo | Correcta |
| AVISO-REA-005 | Reasignar un aviso cancelado | Técnico propietario | La API rechaza la operación | El Técnico no puede reasignarlo | Correcta |
| AVISO-REA-006 | Reasignar al mismo empleado autenticado | Técnico propietario y destino igual al origen | La API rechaza la operación | El Técnico no puede reasignarse a sí mismo | Correcta |
| AVISO-REA-007 | Asignar o reasignar un aviso de la empresa | Administrador | La operación se completa dentro de su empresa | Asignación y reasignación correctas | Correcta |
| AVISO-REA-008 | Asignar o reasignar un aviso de la empresa | Atención al Cliente | La operación se completa dentro de su empresa | Asignación y reasignación correctas | Correcta |
| AVISO-REA-009 | Enviar `id_empleado` al `PUT /api/avisos/{id}` general | Usuario autorizado para edición general | La asignación existente no cambia | `id_empleado` no cambia | Correcta |
| AVISO-REA-010 | Coger un aviso libre mediante `PUT /api/avisos/{id}/coger` | Técnico autenticado | El aviso queda asignado al Técnico autenticado | La operación funciona mediante el endpoint específico | Correcta |
| AVISO-REA-011 | Cancelar un aviso mediante `PUT /api/avisos/{id}/cancelar` | Rol autorizado | La cancelación sigue funcionando | Cancelación correcta | Correcta |
| AVISO-REA-012 | Revisar persistencia tras asignar, reasignar, coger o cancelar | Flujo completo | Ninguna de estas acciones borra físicamente el aviso | No hay borrado físico en el flujo | Correcta |

## Evidencia de auditoría acotada

La implementación registra eventos de entidad `aviso` para el alcance de
asignación:

- `aviso_asignado`;
- `aviso_reasignado`;
- `aviso_autoasignado`.

Los eventos pueden incluir `id_empleado` anterior y nuevo. Esta validación
manual confirma expresamente la persistencia del evento de reasignación; no
demuestra todavía cobertura de auditoría para todas las acciones críticas del
sistema.

## Seguridad de la evidencia

- No se incluyen tokens, contraseñas, secretos ni identificadores reales de
  usuarios o empresas.
- El usuario, la empresa y el empleado autenticado se resuelven en backend.
- Los rechazos no se presentan como controles exclusivamente visuales del
  frontend.

## TEST-AVISOS-FIN-001 — estados terminales

Fecha de ejecución: 2026-07-29
Entorno: local/XAMPP
Resultado: superado después de aplicar FIX-AVISOS-FIN-001

La API real confirmó que `Finalizada` y `Cancelada` bloquean asignación,
reasignación y toma para todos los roles implicados:

- FIN-01 a FIN-08 devolvieron HTTP 403;
- ningún rechazo cambió `id_empresa`, `estado`, `id_empleado` ni `fecha_fin`;
- cada rechazo produjo cero eventos posteriores a su baseline;
- REG-01 a REG-04 devolvieron HTTP 200, cambiaron únicamente `id_empleado` y
  generaron exactamente el evento esperado;
- ME-01 devolvió 403 y ME-02 devolvió 404 genérico, sin cambios ni eventos;
- ninguna respuesta expuso SQLSTATE, trazas PHP, rutas internas ni detalles SQL;
- el rollback dejó cero filas y eventos del fixture y conservó los dos roles
  base.

## TEST-AVISOS-FIN-002 — regresión automatizada

Fecha de ejecución: 2026-07-29
Entorno: local/XAMPP
Resultado: implementado y validado

El arnés `tests/integration/avisos_reasignacion_multiempresa/` incorpora la
matriz terminal completa como regresión automática:

- cinco autenticaciones correctas, incluida Atención al Cliente de Empresa A;
- FIN-01 a FIN-08 correctas con HTTP 403;
- comparación exacta antes/después de `id_tarea`, `id_empresa`, `estado`,
  `id_empleado` y `fecha_fin`;
- cero eventos posteriores al baseline individual de cada rechazo;
- respuestas sin SQLSTATE, trazas, rutas internas ni detalles SQL;
- las siete negativas y nueve positivas anteriores continúan correctas;
- las cuatro operaciones positivas continúan creando exactamente cuatro
  eventos y los controles multiempresa permanecen a cero;
- la matriz pasa de 20 a 29 resultados;
- rollback completo, comprobación independiente de cero residuos y
  `--rollback-only` correcto, con los tres roles base intactos.

El arnés continúa limitado a local/test y no forma parte del `quality-gate` de
GitHub.

## Pendientes

- Repetir periódicamente la matriz multiempresa ya validada como regresión.
- Verificar y documentar la cobertura de auditoría de las demás acciones
  críticas antes de cerrar INC-0013 o marcar PEN-0009 como implementado.
