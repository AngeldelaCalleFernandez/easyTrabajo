# Matriz minima de permisos backend - PEN-0005

Fecha inicial: 2026-05-26
Última actualización: 2026-07-29
Agente: Codex / agente backend-auditor EasyParte
Estado: matriz actualizada; PEN-0005 permanece parcial

## Objetivo

Definir una matriz minima y realista de autorizacion backend usando los roles actuales del sistema antes de crear `RoleMiddleware`, modificar rutas o cambiar controladores.

Esta matriz prepara una fase posterior pequena para corregir especialmente INC-0008, sin migrar todavia al modelo objetivo de roles por empresa.

## Alcance revisado

Endpoints actuales revisados:

- `GET|POST|PUT|DELETE /api/clientes`
- `GET|POST|PUT|DELETE /api/avisos`
- `GET /api/avisos/empleados-asignables`
- `PUT /api/avisos/{id}/asignar`
- `PUT /api/avisos/{id}/coger`
- `PUT /api/avisos/{id}/cancelar`
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
| `/api/avisos` | POST | Administrador, Atencion al Cliente y Tecnico | Valida IDs relacionados por empresa; Tecnico solo crea el aviso libre o asignado a si mismo. |
| `/api/avisos/empleados-asignables` | GET | Administrador, Atencion al Cliente y Tecnico | Lista empleados activos de la empresa. Para Tecnico excluye su propio empleado. |
| `/api/avisos/{id}` | PUT | Usuario autenticado dentro de su alcance | Edita datos generales y conserva la asignacion actual; `id_empleado` ya no cambia por este contrato. |
| `/api/avisos/{id}/asignar` | PUT | Administrador y Atencion al Cliente; Tecnico condicionado | Asigna o reasigna dentro de la empresa. Ningún rol puede operar sobre avisos `Finalizada` o `Cancelada`; Tecnico solo puede mover un aviso propio a otro empleado, no uno libre, ajeno ni a si mismo. |
| `/api/avisos/{id}/coger` | PUT | Solo Tecnico | Toma un aviso libre no terminal usando el `id_empleado` del contexto autenticado. Los estados `Finalizada` y `Cancelada` devuelven 403. |
| `/api/avisos/{id}/cancelar` | PUT | Administrador y Atencion al Cliente; Tecnico condicionado | Tecnico solo cancela avisos propios. Conserva el registro. |
| `/api/avisos/{id}` | DELETE | Ningun rol en el flujo actual | Devuelve 403 para evitar borrado fisico. |
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
| Editar datos generales del aviso | Si | Si | Parcial | El `PUT` general no cambia `id_empleado`; Tecnico queda limitado por propiedad. |
| Listar empleados asignables para avisos | Si | Si | Parcial | Solo empleados activos de la empresa; para Tecnico se excluye su propio empleado. |
| Asignar aviso libre a un empleado concreto | Si | Si | No | Usa `PUT /api/avisos/{id}/asignar`; Tecnico debe usar la accion separada Coger. |
| Reasignar aviso ya asignado | Si | Si | Parcial | Solo sobre avisos no terminales. Tecnico además debe ser propietario, elegir otro empleado activo de su empresa y nunca asignarse a si mismo. |
| Coger aviso libre | No | No | Parcial | Solo Tecnico mediante `PUT /api/avisos/{id}/coger`; el empleado se obtiene del contexto autenticado y el aviso no puede estar `Finalizada` ni `Cancelada`. |
| Cancelar aviso | Si | Si | Parcial | Tecnico solo puede cancelar avisos asignados a el. No puede cancelar avisos sin asignar ni asignados a otros tecnicos. |
| Borrar aviso fisicamente | No | No | No | `DELETE /api/avisos/{id}` permanece bloqueado con 403; este flujo conserva el aviso. |
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

Reglas actuales de avisos validadas:

- Cancelar aviso y borrar aviso fisicamente son acciones distintas.
- Cancelar aviso debe cambiar el estado a `Cancelada` y conservar trazabilidad.
- `Administrador` y `Atencion al Cliente` pueden cancelar avisos.
- `Tecnico` solo puede cancelar avisos asignados a el.
- `Tecnico` no puede cancelar avisos sin asignar ni avisos asignados a otros tecnicos.
- `Tecnico` puede crear avisos dentro de su alcance operativo; puede dejarlos libres o asignarlos a su propio `id_empleado`, pero no puede asignarlos a otro tecnico.
- `Atencion al Cliente` puede listar empleados activos de su empresa como solo lectura para asignar avisos, pero no puede crear, editar ni dar de baja empleados.
- El `PUT` general conserva `id_empleado`; asignar, reasignar y coger usan
  endpoints especificos.
- `Administrador` y `Atencion al Cliente` pueden asignar o reasignar avisos
  dentro de su empresa.
- `Tecnico` solo puede reasignar un aviso propio a otro empleado activo de su
  empresa. No puede reasignar avisos ajenos o cancelados ni reasignarse a si
  mismo.
- `Tecnico` coge avisos libres mediante el endpoint especifico, sin indicar
  otro empleado.
- `Tecnico` no puede borrar fisicamente avisos.
- Ningun rol puede borrar fisicamente avisos mediante el endpoint actual.
- Las asignaciones correctas generan eventos de auditoria
  `aviso_asignado`, `aviso_reasignado` o `aviso_autoasignado`.

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

## Validacion de reasignacion auditada 2026-07-27

Resultados manuales registrados en
`docs/testing/avisos_reasignacion.md`:

- Tecnico reasigna correctamente un aviso propio.
- Los intentos sobre aviso ajeno, aviso cancelado o destino igual al propio
  empleado quedan bloqueados.
- Administrador y Atencion al Cliente asignan o reasignan dentro de su empresa.
- El `PUT` general no cambia `id_empleado`.
- Coger un aviso libre y cancelar un aviso siguen usando acciones separadas.
- La reasignacion correcta deja evento persistente en `auditoria_evento`.
- Ninguna accion del flujo realiza borrado fisico.

La prueba de reasignacion de aviso finalizado por API quedó superada el
2026-07-29 mediante TEST-AVISOS-FIN-001. Administrador, Atencion al Cliente y
Tecnico reciben 403 para estados `Finalizada` y `Cancelada`; coger un aviso
libre terminal también devuelve 403. Los rechazos no cambian datos ni crean
auditoria. PEN-0005 y PEN-0009 continúan parciales por el alcance restante.

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
