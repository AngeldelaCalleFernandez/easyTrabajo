# Validacion manual - cancelacion segura de avisos

Fecha: 2026-05-26
Agente: Codex / agente backend EasyParte
Fase: INC-0010 / PEN-0005 avisos
Estado: pendiente de ejecucion manual

## Alcance

Validar la primera fase de avisos: cancelacion segura sin borrado fisico.

Endpoint propuesto:

```txt
PUT /api/avisos/{id}/cancelar
```

La cancelacion debe cambiar el estado del aviso a `Cancelada` y no debe eliminar el registro de la tabla `tarea`.
Si el endpoint existente `PUT /api/avisos/{id}` recibe un cambio de estado a `Cancelada`, debe aplicar la misma validacion de cancelacion segura.

No se deben pegar tokens completos, contrasenas ni secretos en este documento.

## Matriz de pruebas

| ID | Endpoint o pantalla | Token usado | Payload usado | Resultado esperado | Resultado obtenido | Estado | Notas |
|---|---|---|---|---|---|---|---|
| AVISO-CAN-001 | `PUT /api/avisos/{id}/cancelar` | Administrador de la empresa del aviso | Sin body | HTTP 200. Aviso queda con estado `Cancelada`. El registro sigue existiendo. | Pendiente de ejecutar | pendiente | Usar aviso de la misma empresa. |
| AVISO-CAN-002 | `PUT /api/avisos/{id}/cancelar` | Atencion al Cliente de la empresa del aviso | Sin body | HTTP 200. Aviso queda con estado `Cancelada`. El registro sigue existiendo. | Pendiente de ejecutar | pendiente | Usar aviso de la misma empresa. |
| AVISO-CAN-003 | `PUT /api/avisos/{id}/cancelar` | Tecnico asignado al aviso | Sin body | HTTP 200. Aviso queda con estado `Cancelada`. El registro sigue existiendo. | Pendiente de ejecutar | pendiente | El aviso debe tener `id_empleado` igual al empleado del token. |
| AVISO-CAN-004 | `PUT /api/avisos/{id}/cancelar` | Tecnico de la misma empresa | Sin body | HTTP 403 generico. El aviso sin asignar no cambia de estado. | Pendiente de ejecutar | pendiente | El aviso debe tener `id_empleado` nulo o vacio. |
| AVISO-CAN-005 | `PUT /api/avisos/{id}/cancelar` | Tecnico distinto al asignado | Sin body | HTTP 403 generico. El aviso no cambia de estado. | Pendiente de ejecutar | pendiente | El aviso debe estar asignado a otro tecnico. |
| AVISO-CAN-006 | `DELETE /api/avisos/{id}` | Cualquier rol autenticado | Sin body | HTTP 403 generico. No se elimina fisicamente el aviso. | Pendiente de ejecutar | pendiente | Esta fase bloquea el borrado fisico. |
| AVISO-CAN-007 | `PUT /api/avisos/{id}` | Tecnico distinto al asignado | `{"estado":"Cancelada"}` | HTTP 403 generico. El aviso no cambia de estado. | Pendiente de ejecutar | pendiente | Prueba de regresion para evitar bypass por edicion normal. |
| AVISO-CAN-008 | `GET /api/avisos` | Atencion al Cliente | N/A | HTTP 200. Devuelve avisos de su empresa. | Pendiente de ejecutar | pendiente | Ajuste funcional 2026-05-27. |
| AVISO-CAN-009 | `POST /api/avisos` | Atencion al Cliente | Aviso con cliente valido | HTTP 201. Aviso creado dentro de su empresa. | Pendiente de ejecutar | pendiente | Rol permitido explicitamente en ruta. |
| AVISO-CAN-010 | `POST /api/avisos` | Tecnico | Aviso con cliente valido y sin `id_empleado` | HTTP 201. Aviso creado libre, sin trabajador asignado. | Pendiente de ejecutar | pendiente | El backend no debe autoasignar el aviso si el tecnico no envia `id_empleado`. |
| AVISO-CAN-011 | `POST /api/avisos` | Tecnico | Aviso con su propio `id_empleado` | HTTP 201. Aviso creado asignado al tecnico autenticado. | Pendiente de ejecutar | pendiente | No documentar IDs reales sensibles. |
| AVISO-CAN-012 | `POST /api/avisos` | Tecnico | Aviso con `id_empleado` de otro tecnico | HTTP 403 generico. Aviso no creado. | Pendiente de ejecutar | pendiente | No documentar IDs reales sensibles. |
| AVISO-CAN-022 | `PUT /api/avisos/{id}` | Tecnico | `{"id_empleado": <otro_tecnico>}` | HTTP 403 generico. No se reasigna a otro tecnico. | Pendiente de ejecutar | pendiente | No documentar IDs reales sensibles. |
| AVISO-CAN-013 | Pantalla de avisos | Atencion al Cliente | No aplica | Boton Cancelar visible en avisos no cancelados de su empresa. | Pendiente de ejecutar | pendiente | Validar tras aceptar variantes de rol en backend. |
| AVISO-CAN-014 | Pantalla de avisos | Tecnico | No aplica | Boton Cancelar visible solo en avisos asignados al tecnico y no cancelados. | Pendiente de ejecutar tras ajuste de template | pendiente | `avisos.html` ya usa `puedeCancelarAviso(tarea)` para decidir la visibilidad del boton. |
| AVISO-CAN-015 | Pantalla de avisos | Tecnico | No aplica | Boton Cancelar no visible en aviso sin asignar. | Pendiente de ejecutar tras ajuste de template | pendiente | `avisos.html` ya usa `puedeCancelarAviso(tarea)` para decidir la visibilidad del boton. |
| AVISO-CAN-016 | Pantalla de avisos | Tecnico | No aplica | Boton Cancelar no visible en aviso asignado a otro tecnico. | Pendiente de ejecutar tras ajuste de template | pendiente | Normalmente el tecnico no deberia ver avisos de otros tecnicos. |
| AVISO-CAN-017 | Pantallas principales | Usuario local valido | No aplica | Login, dashboard, clientes, avisos y partes siguen funcionando. | Pendiente de ejecutar | pendiente | Prueba de regresion funcional local. |
| AVISO-CAN-018 | `GET /api/empleados` | Atencion al Cliente | N/A | HTTP 200. Devuelve empleados activos de su empresa para poder asignar avisos. | Pendiente de ejecutar | pendiente | Ajuste 2026-05-27. No debe exponer empleados de otra empresa. |
| AVISO-CAN-019 | Pantalla crear/editar aviso | Atencion al Cliente | No aplica | El selector "Asignar trabajador" muestra empleados activos de la empresa. | Pendiente de ejecutar | pendiente | Depende de `GET /api/empleados` correcto. |
| AVISO-CAN-020 | `POST /api/empleados` / `PUT /api/empleados/{id}` / `DELETE /api/empleados/{id}` | Atencion al Cliente | Payload de gestion de empleado | HTTP 403 generico. Atencion al Cliente solo tiene lectura de empleados. | Pendiente de ejecutar | pendiente | No documentar datos personales reales. |
| AVISO-CAN-021 | `GET /api/empleados` | Tecnico | N/A | HTTP 403 generico. Tecnico no tiene gestion general de empleados. | Pendiente de ejecutar | pendiente | El tecnico opera sobre sus avisos/partes, no sobre el listado general de empleados. |

## Comprobaciones de base de datos local

Despues de cancelar un aviso:

- El registro debe seguir existiendo en `tarea`.
- `estado` debe quedar como `Cancelada`.
- `fecha_fin` debe quedar informada si estaba vacia.
- No debe ejecutarse borrado fisico durante la cancelacion.

## Estado documental

- INC-0010 no se marca como resuelta en esta fase.
- PEN-0005 sigue parcial.
- Queda pendiente definir o implementar el borrado fisico condicionado de avisos ya cancelados, si finalmente se mantiene esa accion.
- El 2026-05-27 se ajusta `frontend/src/app/features/avisos/avisos.html` para que el boton Cancelar se muestre mediante `puedeCancelarAviso(tarea)`. Queda pendiente ejecutar las pruebas manuales por rol en navegador.
