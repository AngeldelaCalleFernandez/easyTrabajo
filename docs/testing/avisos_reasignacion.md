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

Las pruebas fueron ejecutadas manualmente por la persona responsable del
proyecto. Codex únicamente registra los resultados aportados y no ha ejecutado
SQL ni ha repetido las operaciones contra la base de datos.

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

## Pendientes

- Probar explícitamente por API el intento de reasignar un aviso finalizado.
- Repetir la matriz con una segunda empresa para verificar aislamiento
  multiempresa real.
- Automatizar las pruebas positivas y negativas de permisos.
- Verificar y documentar la cobertura de auditoría de las demás acciones
  críticas antes de cerrar INC-0013 o marcar PEN-0009 como implementado.
