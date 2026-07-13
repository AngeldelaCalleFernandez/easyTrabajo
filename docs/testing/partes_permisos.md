# Validacion manual - permisos minimos de partes/albaranes

Fecha: 2026-05-27
Agente: Codex / agente backend-auditor EasyParte
Fase: PEN-0005 partes/albaranes
Estado: pendiente de ejecucion manual

## Alcance

Validar permisos minimos backend sobre `/api/partes` sin modificar frontend, base de datos, login, JWT, CORS, tenant ni modelo de roles.

No se deben pegar tokens completos, contrasenas ni secretos en este documento.

## Matriz de pruebas

| ID | Endpoint | Token usado | Payload usado | Resultado esperado | Resultado obtenido | Estado | Notas |
|---|---|---|---|---|---|---|---|
| PARTE-PERM-001 | `GET /api/partes` | Administrador | N/A | HTTP 200. Devuelve partes activos de su empresa. | Pendiente de ejecutar | pendiente | No debe mostrar partes de otra empresa. |
| PARTE-PERM-002 | `GET /api/partes` | Atencion al Cliente | N/A | HTTP 200. Devuelve partes activos de su empresa en modo consulta. | Pendiente de ejecutar | pendiente | Solo lectura en esta fase. |
| PARTE-PERM-003 | `GET /api/partes` | Tecnico | N/A | HTTP 200. Devuelve solo partes donde `id_empleado` coincide con el tecnico autenticado. | Pendiente de ejecutar | pendiente | No debe mostrar partes de otros tecnicos. |
| PARTE-PERM-004 | `POST /api/partes` | Atencion al Cliente | Parte valido | HTTP 403 generico. No crea parte. | Pendiente de ejecutar | pendiente | No hay decision funcional para crear/cerrar partes desde Atencion al Cliente. |
| PARTE-PERM-005 | `PUT /api/partes/{id}` | Atencion al Cliente | Edicion o cierre de parte | HTTP 403 generico. No modifica parte. | Pendiente de ejecutar | pendiente | Incluye intento de `estado = Cerrado`. |
| PARTE-PERM-006 | `POST /api/partes` | Tecnico | Parte con `id_empleado` propio | HTTP 201. Parte creado para el tecnico autenticado. | Pendiente de ejecutar | pendiente | Si incluye aviso, el aviso debe estar asignado al tecnico. |
| PARTE-PERM-007 | `POST /api/partes` | Tecnico | Parte con `id_empleado` de otro empleado | HTTP 403 generico. No crea parte. | Pendiente de ejecutar | pendiente | No documentar IDs reales sensibles. |
| PARTE-PERM-008 | `POST /api/partes` | Tecnico | Parte con aviso asignado a otro tecnico | HTTP 403 generico. No crea parte. | Pendiente de ejecutar | pendiente | Valida alcance operativo del aviso. |
| PARTE-PERM-009 | `POST /api/partes` | Tecnico | Parte con aviso asignado al tecnico | HTTP 201. Parte creado. | Pendiente de ejecutar | pendiente | Flujo esperado desde aviso asignado. |
| PARTE-PERM-010 | `PUT /api/partes/{id}` | Tecnico propietario | Edicion de descripcion/horas/material/observaciones | HTTP 200. Parte propio actualizado. | Pendiente de ejecutar | pendiente | No valida firma/hash en esta fase. |
| PARTE-PERM-011 | `PUT /api/partes/{id}` | Tecnico no propietario | Edicion de parte de otro tecnico | HTTP 403 generico. No modifica parte. | Pendiente de ejecutar | pendiente | Parte debe pertenecer a la misma empresa para aislar la prueba de rol. |
| PARTE-PERM-012 | `PUT /api/partes/{id}` | Tecnico propietario | Payload con `id_empleado` de otro empleado | HTTP 403 generico. No reasigna parte. | Pendiente de ejecutar | pendiente | Evita reasignacion por payload. |
| PARTE-PERM-013 | `POST /api/partes` | Administrador | Parte valido de su empresa | HTTP 201. Parte creado. | Pendiente de ejecutar | pendiente | Puede asignar empleado de su empresa. |
| PARTE-PERM-014 | `PUT /api/partes/{id}` | Administrador | Edicion de parte de su empresa | HTTP 200. Parte actualizado. | Pendiente de ejecutar | pendiente | Cierre formal con firma/hash queda fuera de esta tarea. |
| PARTE-PERM-015 | Regresion local | Usuarios validos | N/A | Login, dashboard, avisos, clientes y partes siguen funcionando. | Pendiente de ejecutar | pendiente | Prueba funcional manual en XAMPP/local. |

## Tabla Permisos

Rol	Prueba	Esperado	Obtenido	BD  correcta	Estado
ADM-PAR-01	Administrador	GET partes	200	si	Sí	resuelta
ADM-PAR-02	Administrador	POST parte	201	si	Sí	resuelta
ADM-PAR-03	Administrador	PUT parte	200	si	Sí	resuelta
AC-PAR-01	Atención Cliente GET partes	200	si	Sí	resuelta
AC-PAR-02	Atención Cliente POST parte	403	si	Sí	resuelta
AC-PAR-03	Atención Cliente PUT parte	403	si	Sí	resuelta
TEC-PAR-01	Técnico GET solo propios	200	no	No	Pendiente
TEC-PAR-02	Técnico	POST propio	201	no	No	Pendiente
TEC-PAR-03	Técnico POST otro empleado	403	no	No	Pendiente
TEC-PAR-04	Técnico POST aviso ajeno	403	no	No	Pendiente
TEC-PAR-05	Técnico POST aviso propio	201	si	si	Pendiente
TEC-PAR-06	Técnico	PUT propio	200	si	Sí	resuelta
TEC-PAR-07	Técnico	PUT ajeno	403	no	No	Pendiente
TEC-PAR-08	Técnico Reasignar empleado	403	si	Sí	resuelta
TEC-PAR-09	Técnico	Cerrar propio	200	si	Sí	resuelta
REG-PAR-01	Todos	Regresión funcional	Correcta	—	—	Pendiente


## Estado documental

- PEN-0005 sigue `parcial`.
- INC-0014 sigue `abierta`: el cierre formal con firma, hash, bloqueo y rectificacion queda fuera de esta fase.
- No se crea `RoleMiddleware` completo en esta tarea.
