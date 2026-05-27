# Validacion permisos clientes - INC-0008

Fecha: 2026-05-26
Agente: Codex / agente backend EasyParte
Incidencia relacionada: INC-0008
Pendiente relacionado: PEN-0005

## Objetivo

Validar que `/api/clientes` aplica una comprobacion minima de roles en backend usando el rol incluido en el token autenticado, sin confiar en datos enviados por frontend y sin modificar el modelo actual de usuarios o roles.

## Reglas implementadas

| Endpoint | Administrador | Atencion al Cliente | Tecnico |
|---|---:|---:|---:|
| `GET /api/clientes` | Permitido | Permitido | Permitido |
| `POST /api/clientes` | Permitido | Permitido | 403 |
| `PUT /api/clientes/{id}` | Permitido | Permitido | 403 |
| `DELETE /api/clientes/{id}` | Permitido | Permitido | 403 |

Notas:

- El rol se obtiene del usuario autenticado devuelto por `AuthMiddleware::checkToken()`.
- El filtrado por `id_empresa` se mantiene en `ClienteController` y `Cliente`.
- No se modifica login, JWT, CORS, base de datos ni estructura de roles.
- La baja de cliente es baja logica (`activo = 0`), no borrado fisico.
- Decision revisada 2026-05-27: `Tecnico` puede listar/ver clientes para su flujo operativo, pero no puede crear, editar ni dar de baja.
- No se documentan tokens completos ni contrasenas.

## Tabla de pruebas manuales

Estas pruebas fueron ejecutadas manualmente por la persona responsable del proyecto. Codex solo registra los resultados aportados.

| ID | Caso | Token usado | Payload usado | Resultado esperado | Resultado obtenido | Estado | Notas |
|---|---|---|---|---|---|---|---|
| CLI-PERM-001 | Token Tecnico creando cliente | Token de usuario con rol `Tecnico` | Cliente de prueba sin datos reales | HTTP 403 con mensaje generico | HTTP 403 confirmado | correcto | Verificado que no se crea cliente. |
| CLI-PERM-002 | Token Tecnico editando cliente | Token de usuario con rol `Tecnico` | Cambio sobre cliente de prueba de su empresa | HTTP 403 con mensaje generico | HTTP 403 confirmado | correcto | Tecnico no puede editar clientes. |
| CLI-PERM-003 | Token Tecnico dando de baja cliente | Token de usuario con rol `Tecnico` | N/A | HTTP 403 con mensaje generico | HTTP 403 confirmado | correcto | Verificado que `activo` no cambia. |
| CLI-PERM-004 | Token Atencion al Cliente dando de baja cliente | Token de usuario con rol `Atencion al Cliente` | N/A | Baja logica permitida | Baja logica correcta | correcto | Decision funcional revisada: puede cambiar `activo` de 1 a 0. |
| CLI-PERM-005 | Token Atencion al Cliente creando cliente | Token de usuario con rol `Atencion al Cliente` | Cliente de prueba sin datos reales | Cliente creado correctamente | Correcto | correcto | Sin errores derivados del control de roles. |
| CLI-PERM-006 | Token Atencion al Cliente editando cliente | Token de usuario con rol `Atencion al Cliente` | Cambio sobre cliente de prueba | Cliente actualizado correctamente | Correcto | correcto | Se mantiene `id_empresa` desde backend. |
| CLI-PERM-007 | Token Administrador mantiene CRUD completo | Token de usuario con rol `Administrador` | Cliente de prueba sin datos reales | Listar, crear, editar y dar de baja funcionan | Correcto | correcto | Administrador conserva CRUD completo. |
| CLI-PERM-008 | Regresion login | Usuario local valido | N/A | Login correcto | Correcto | correcto | No se documenta token completo. |
| CLI-PERM-009 | Regresion dashboard | Token autenticado | N/A | Dashboard carga correctamente | Correcto | correcto | Sin regresion detectada. |
| CLI-PERM-010 | Regresion avisos | Token autenticado | Datos locales de prueba | Avisos siguen funcionando | Correcto | correcto | No se han cambiado permisos de avisos. |
| CLI-PERM-011 | Regresion partes/albaranes | Token autenticado | Datos locales de prueba | Partes siguen funcionando | Correcto | correcto | No se han cambiado permisos de partes. |
| CLI-PERM-012 | Token Tecnico listando clientes | Token de usuario con rol `Tecnico` | N/A | HTTP 200. Lista clientes de su empresa. | Pendiente de ejecutar tras ajuste | pendiente | Nuevo ajuste funcional 2026-05-27. |

## Estado recomendado

INC-0008 queda pendiente de revalidar tras el ajuste que permite `GET /api/clientes` a `Tecnico`.

PEN-0005 debe permanecer `parcial`, porque esta fase solo cubre `/api/clientes` y todavia no existe autorizacion backend centralizada completa.
