# Matriz minima de permisos backend - PEN-0005

Fecha: 2026-05-26
Agente: Codex / agente backend-auditor EasyParte
Estado: propuesta documental actualizada tras primera implementacion minima

## Objetivo

Definir una matriz minima y realista de autorizacion backend usando los roles actuales del sistema antes de crear `RoleMiddleware`, modificar rutas o cambiar controladores.

Esta matriz prepara una fase posterior pequena para corregir especialmente INC-0008, sin migrar todavia al modelo objetivo de roles por empresa.

## Alcance revisado

Endpoints actuales revisados:

- `GET|POST|PUT|DELETE /api/clientes`
- `GET|POST|PUT|DELETE /api/avisos`
- `GET|POST|PUT /api/partes`
- `GET|POST|PUT|DELETE /api/empleados`
- `GET|POST|PUT|DELETE /api/usuarios`
- `GET /api/roles`
- `GET /api/dashboard`

Archivos de codigo revisados en modo solo lectura:

- `backend/routes/api.php`
- `backend/middleware/AuthMiddleware.php`
- `backend/controllers/ClienteController.php`
- `backend/controllers/AvisoController.php`
- `backend/controllers/ParteTrabajoController.php`
- `backend/controllers/EmpleadoController.php`
- `backend/controllers/UsuarioController.php`
- `backend/controllers/RolController.php`
- `backend/controllers/DashboardController.php`
- `backend/controllers/AuthController.php`
- `backend/models/Cliente.php`
- `backend/models/Usuario.php`
- `bbdd/export_base_datos.sql`

## Roles actuales reales

Los roles existentes actualmente en el dump SQL son:

| id_rol | Rol actual |
|---:|---|
| 1 | Administrador |
| 2 | Tecnico |
| 3 | Atencion al Cliente |

Notas:

- El modelo actual usa `usuario.id_empresa` y `usuario_rol`.
- El token incluye `id_usuario`, `id_empleado`, `id_empresa` y `rol_nombre`.
- No existe todavia `RoleMiddleware`.
- No existe todavia `TenantMiddleware`.
- No existe todavia modelo `empresa_usuario` / `empresa_usuario_rol`.
- La documentacion objetivo define roles futuros mas detallados, pero no deben inventarse ni implementarse en esta fase.

## Permisos actuales detectados

| Recurso | Metodo | Control actual | Observacion |
|---|---|---|---|
| `/api/clientes` | GET | Cualquier usuario autenticado | Filtra por `id_empresa`. No valida rol. |
| `/api/clientes` | POST | Cualquier usuario autenticado | Crea cliente en empresa del token. No valida rol. |
| `/api/clientes/{id}` | PUT | Cualquier usuario autenticado | Actualiza por `id_empresa`. No valida rol. |
| `/api/clientes/{id}` | DELETE | Cualquier usuario autenticado | Baja logica por `id_empresa`. No valida rol. |
| `/api/avisos` | GET | Cualquier usuario autenticado | Filtra por empresa; Tecnico ve asignados o sin asignar. |
| `/api/avisos` | POST | Cualquier usuario autenticado | Valida IDs relacionados por empresa, pero no rol. |
| `/api/avisos/{id}` | PUT | Cualquier usuario autenticado | Filtra por empresa. Tecnico solo edita avisos propios o toma avisos libres para si; no puede apropiarse de avisos ajenos ni asignarlos a otro empleado. |
| `/api/avisos/{id}` | DELETE | Cualquier usuario autenticado | Elimina fisicamente por empresa. No valida rol. |
| `/api/partes` | GET | Cualquier usuario autenticado | Filtra por empresa; Tecnico ve solo sus partes. |
| `/api/partes` | POST | Cualquier usuario autenticado | Tecnico queda forzado a su `id_empleado`; otros roles pueden indicar empleado. |
| `/api/partes/{id}` | PUT | Administrador o Tecnico autorizado | Tecnico solo actualiza partes propios; Atencion al Cliente recibe 403. |
| `/api/empleados` | GET/POST/PUT/DELETE | Solo `Administrador` | Control en `routes/api.php`. Filtra por empresa. |
| `/api/usuarios` | GET/POST/PUT/DELETE | Solo `Administrador` | Control en `routes/api.php`. Filtra por empresa. |
| `/api/roles` | GET | Solo `Administrador` | Control en `routes/api.php`. Lista roles globales. |
| `/api/dashboard` | GET | Cualquier usuario autenticado | Dashboard filtra por empresa; Tecnico obtiene vista parcial por empleado para avisos, partes y horas. |

## Matriz minima recomendada para fase PEN-0005

Convencion:

- `Si`: permitido en backend.
- `Parcial`: permitido con reglas adicionales ya existentes o recomendadas.
- `No`: debe devolver 403.

| Recurso / accion | Administrador | Atencion al Cliente | Tecnico | Regla minima recomendada |
|---|---:|---:|---:|---|
| Ver dashboard | Si | Si | Parcial | Todos autenticados; Tecnico con vista personal. |
| Listar clientes | Si | Si | Si | Tecnico puede consultar clientes para su flujo operativo, sin crear, editar ni dar de baja. |
| Crear cliente | Si | Si | No | Corrige INC-0008 sin romper flujo de atencion. |
| Editar cliente | Si | Si | No | Atencion al Cliente puede mantener datos basicos. |
| Dar de baja cliente | Si | Si | No | Baja logica permitida para Atencion al Cliente tras decision funcional revisada. |
| Listar avisos | Si | Si | Parcial | Atencion al Cliente ve todos los avisos de su empresa; Tecnico solo asignados o sin asignar, como comportamiento actual. |
| Crear aviso | Si | Si | Parcial | Tecnico puede crear avisos libres o asignados a su propio `id_empleado`; no puede asignarlos a otro tecnico. |
| Editar aviso | Si | Si | Parcial | Tecnico solo cambio operativo de aviso propio si se mantiene flujo actual; evitar reasignaciones libres. |
| Cancelar aviso | Si | Si | Parcial | Tecnico solo puede cancelar avisos asignados a el. No puede cancelar avisos sin asignar ni asignados a otros tecnicos. |
| Borrar aviso fisicamente | Parcial | Parcial | No | Solo permitido a Administrador y Atencion al Cliente si el aviso ya esta cancelado. No borrar avisos no cancelados. |
| Listar partes/albaranes | Si | Si | Parcial | Tecnico solo partes propios; Atencion al Cliente consulta operativa. |
| Crear parte | Si | No | Parcial | Tecnico solo para si mismo; si indica aviso, debe estar asignado al tecnico. Administrador puede crear/asignar dentro de su empresa. |
| Editar/cerrar parte | Si | No | Parcial | Tecnico solo parte propio; Atencion al Cliente queda solo lectura. Cierre formal con firma/hash fuera de esta fase. |
| Listar empleados | Si | Parcial | No | Atencion al Cliente puede listar empleados activos de su empresa solo para asignar avisos. |
| Crear/editar/baja empleados | Si | No | No | Mantener control actual. |
| Listar usuarios | Si | No | No | Mantener control actual. |
| Crear/editar/baja usuarios | Si | No | No | Mantener control actual. |
| Listar roles | Si | No | No | Mantener control actual. |

## Reglas recomendadas para implementar despues

La siguiente tarea de implementacion deberia ser pequena y centrada:

1. Crear un punto central de comprobacion de rol minimo, preferiblemente en rutas o helper dedicado antes de crear un middleware completo.
2. No cambiar contratos de respuesta salvo para devolver 403 generico y homogeneo.
3. Aplicar primero a `clientes` para cerrar INC-0008:
   - `GET /api/clientes`: `Administrador`, `Atencion al Cliente`; decidir si `Tecnico` puede consultar o queda bloqueado.
   - `POST /api/clientes`: `Administrador`, `Atencion al Cliente`.
   - `PUT /api/clientes/{id}`: `Administrador`, `Atencion al Cliente`.
   - `DELETE /api/clientes/{id}`: `Administrador`, `Atencion al Cliente`.
4. Mantener filtros por `id_empresa` existentes.
5. No confiar en rol enviado por frontend.
6. No aceptar `id_empresa` del body.
7. Registrar pruebas negativas por rol antes de marcar INC-0008 como resuelta.

Decision funcional revisada el 2026-05-26:

- `Atencion al Cliente` puede dar de baja clientes porque la accion actual es baja logica (`activo = 0`), no borrado fisico.
- `Tecnico` puede listar clientes, pero sigue sin poder crear, editar ni dar de baja clientes.
- `Administrador` conserva CRUD completo.

Decision funcional sobre avisos pendiente de implementacion:

- Cancelar aviso y borrar aviso fisicamente son acciones distintas.
- Cancelar aviso debe cambiar el estado a `Cancelada` y conservar trazabilidad.
- `Administrador` y `Atencion al Cliente` pueden cancelar avisos.
- `Tecnico` solo puede cancelar avisos asignados a el.
- `Tecnico` no puede cancelar avisos sin asignar ni avisos asignados a otros tecnicos.
- `Tecnico` puede crear avisos dentro de su alcance operativo; puede dejarlos libres o asignarlos a su propio `id_empleado`, pero no puede asignarlos a otro tecnico.
- `Atencion al Cliente` puede listar empleados activos de su empresa como solo lectura para asignar avisos, pero no puede crear, editar ni dar de baja empleados.
- El borrado fisico solo puede permitirse a `Administrador` y `Atencion al Cliente` cuando el aviso ya este cancelado.
- `Tecnico` no puede borrar fisicamente avisos.
- No se debe borrar fisicamente un aviso que no este en estado `Cancelada`.
- El estado actual del codigo no cumple esta separacion: `AvisoController::delete()` ejecuta `DELETE FROM tarea`.

## Validacion de seguridad 2026-07-13

Commit validado: `0e2fa38`.

- `ParteTrabajoController` reconoce el rol real `Tecnico` con o sin tilde y activa los controles de propiedad existentes.
- `AvisoController::update()` comprueba la asignacion actual antes de procesar el payload y bloquea la apropiacion horizontal.
- Partes: TEC-PAR-01, TEC-PAR-03, TEC-PAR-04, TEC-PAR-05, TEC-PAR-07 y TEC-PAR-08 correctas, con verificacion en base de datos.
- Avisos: AVI-TEC-01 a AVI-TEC-06 correctas, sin modificaciones parciales en respuestas 403.
- Atencion al Cliente conserva lectura de partes, recibe 403 en POST/PUT de partes y conserva edicion autorizada de avisos.
- Administrador conserva edicion autorizada de avisos.

Permanecen pendientes la prueba con segunda empresa, la centralizacion de roles,
la automatizacion de estas pruebas, la decision sobre liberar un aviso propio y
la limpieza controlada de fixtures.

## Permisos objetivo futuros

La documentacion objetivo define roles por empresa:

- `administrador_jefe`
- `administrador`
- `jefe_departamento`
- `jefe_equipo`
- `atencion_cliente`
- `tecnico`
- `solo_lectura`

Ese modelo requiere migracion separada:

- `empresa_usuario`
- `empresa_usuario_rol`
- bloqueo por empresa
- roles por empresa
- `RoleMiddleware`
- `TenantMiddleware`
- `SubscriptionMiddleware`

No forma parte de esta fase minima.

## Decisiones pendientes antes de implementar

1. Confirmar si `Atencion al Cliente` puede editar cualquier aviso de la empresa o solo avisos creados por ese rol.
2. Confirmar si `Atencion al Cliente` puede consultar partes/albaranes o debe quedar fuera del modulo.
3. Definir formato unico de error 403 antes de tocar rutas.

## Pruebas recomendadas para la fase de implementacion

Pruebas negativas:

- Token `Tecnico` creando cliente debe recibir 403.
- Token `Tecnico` editando cliente debe recibir 403.
- Token `Tecnico` dando de baja cliente debe recibir 403.
- Token `Atencion al Cliente` dando de baja cliente debe ejecutar baja logica correctamente.
- Token `Atencion al Cliente` accediendo a `/api/usuarios`, `/api/empleados` o `/api/roles` debe recibir 403.

Pruebas positivas:

- Token `Administrador` mantiene CRUD de clientes.
- Token `Atencion al Cliente` puede listar, crear y editar clientes.
- Token `Administrador` mantiene administracion de empleados, usuarios y roles.
- Login, dashboard, avisos y partes siguen funcionando en local.
