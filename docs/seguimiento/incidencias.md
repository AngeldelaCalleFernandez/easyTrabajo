# Incidencias - EasyParte

Auditoria inicial realizada el 2026-05-20 en modo solo lectura. No se ha modificado codigo de aplicacion, backend, frontend ni base de datos.

Estados permitidos:

```txt
abierta
en revision
en progreso
resuelta
descartada
bloqueada
```

## INC-0001 - CORS abierto a cualquier origen

ID: INC-0001
Titulo: CORS abierto a cualquier origen
Prioridad: critica
Estado: resuelta
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

El backend envia `Access-Control-Allow-Origin: *` en `backend/config/cors.php`.

### Pasos para reproducir

1. Revisar `backend/config/cors.php`.
2. Comprobar la cabecera configurada.

### Resultado esperado

En produccion solo deben permitirse origenes autorizados.

### Resultado actual

La API permite cualquier origen.

### Archivos o zonas afectadas

- `backend/config/cors.php`

### Riesgo

Riesgo de exposicion de API a origenes no controlados, especialmente si se anaden credenciales o cookies.

### Propuesta de solucion

Configurar CORS por entorno usando lista de origenes permitidos.

### Correccion aplicada

`backend/config/cors.php` permite configurar origenes, metodos, cabeceras y credenciales mediante variables de entorno. En entorno local/desarrollo mantiene compatibilidad con XAMPP usando `*` si no se define `CORS_ALLOWED_ORIGINS`; fuera de local no aplica wildcard por defecto y registra origenes rechazados con `error_log`.

Variables esperadas:

- `APP_ENV`
- `CORS_ALLOWED_ORIGINS`
- `CORS_ALLOWED_METHODS`
- `CORS_ALLOWED_HEADERS`
- `CORS_ALLOW_CREDENTIALS`

Nota 2026-05-22: se anade loader simple en `backend/config/env.php` para cargar `.env` desde la raiz del repositorio o `backend/.env` antes de CORS, BD y JWT. `.htaccess` queda solo para rewrite/configuracion minima.

### Pruebas necesarias

- [x] Prueba estatica de sintaxis PHP.
- [x] Prueba backend de cabeceras CORS.
- [x] Prueba frontend desde origen permitido.
- [x] Prueba negativa desde origen no permitido.

### Validacion de cierre

VAL-CONFIG-006 y VAL-CONFIG-007 quedan en estado `correcto`.

- En local, el origen `http://localhost:4200` funciona y no queda bloqueado por CORS.
- En entorno no local simulado, un origen no permitido no recibe `Access-Control-Allow-Origin`.
- El intento con origen no permitido queda registrado sin secretos.

---

## INC-0002 - Credenciales de base de datos y usuario root en codigo

ID: INC-0002
Titulo: Credenciales de base de datos y usuario root en codigo
Prioridad: critica
Estado: resuelta
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

`backend/config/database.php` contiene host, base de datos, usuario `root` y password vacio directamente en codigo.

### Pasos para reproducir

1. Abrir `backend/config/database.php`.
2. Revisar propiedades privadas de conexion.

### Resultado esperado

Credenciales fuera del repositorio, preferiblemente variables de entorno, y usuario DB sin privilegios excesivos.

### Resultado actual

Credenciales locales hardcodeadas y usuario `root`.

### Archivos o zonas afectadas

- `backend/config/database.php`

### Riesgo

Riesgo critico para produccion y mala separacion de entornos.

### Propuesta de solucion

Crear configuracion por entorno y usar variables de entorno para credenciales.

### Correccion aplicada

`backend/config/database.php` lee la configuracion desde variables de entorno y conserva valores locales solo para `APP_ENV=local/development/dev/testing` o cuando `APP_ENV` no esta definido. Fuera de local exige configuracion explicita y bloquea `DB_USER=root`.

Variables esperadas:

- `APP_ENV`
- `DB_HOST`
- `DB_PORT`
- `DB_NAME` o `DB_DATABASE`
- `DB_USER` o `DB_USERNAME`
- `DB_PASSWORD`
- `DB_CHARSET`

Nota 2026-05-22: la carga de variables se centraliza en `backend/config/env.php`. El archivo `.env` real debe quedar fuera de Git; `.env.example` documenta las variables esperadas sin secretos reales.

### Pruebas necesarias

- [x] Prueba estatica de sintaxis PHP.
- [x] Prueba local con variables de entorno.
- [x] Prueba de conexion con usuario DB limitado documentada como requisito para `APP_ENV=production`.

### Validacion de cierre

VAL-CONFIG-001, VAL-CONFIG-002, VAL-CONFIG-003 y VAL-CONFIG-009 quedan en estado `correcto`.

- Local/XAMPP sigue funcionando con la configuracion local.
- La configuracion se puede cargar desde `.env` en raiz o `backend/.env`.
- `.env` y `backend/.env` quedan ignorados por Git.
- En `APP_ENV=production`, `DB_USER=root` queda bloqueado de forma intencionada; una prueba completa de produccion simulada debe usar usuario MySQL no root.

---

## INC-0003 - Errores internos de base de datos expuestos

ID: INC-0003
Titulo: Errores internos de base de datos expuestos
Prioridad: critica
Estado: resuelta
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

En caso de fallo de conexion, `backend/config/database.php` devuelve al cliente `"Error de BD: " . $exception->getMessage()`.

### Pasos para reproducir

1. Revisar el bloque `catch(PDOException $exception)` en `database.php`.
2. Observar que se imprime el mensaje interno.

### Resultado esperado

Respuesta generica al cliente y detalle tecnico solo en logs.

### Resultado actual

El mensaje interno de PDO puede exponerse en JSON.

### Archivos o zonas afectadas

- `backend/config/database.php`

### Riesgo

Exposicion de rutas, host, base de datos o informacion SQL.

### Propuesta de solucion

Registrar con `error_log` y devolver error generico homogeneo.

### Correccion aplicada

`backend/config/database.php` ya no devuelve el mensaje interno de PDO al cliente. En errores de configuracion o conexion devuelve JSON generico con HTTP 500 y registra el detalle tecnico mediante `error_log` con prefijos `[EasyParte][DB_CONFIG]` o `[EasyParte][DB_CONNECTION]`.

### Pruebas necesarias

- [x] Prueba estatica de sintaxis PHP.
- [x] Simular fallo de conexion.
- [x] Verificar que el cliente no recibe detalle tecnico.

### Validacion de cierre

VAL-CONFIG-004 y VAL-CONFIG-005 quedan en estado `correcto`.

- El fallo de conexion devuelve error generico al cliente.
- No se exponen `SQLSTATE`, DSN, host, usuario, rutas internas ni trazas.
- El detalle tecnico queda reservado a logs mediante `error_log`.

---

## INC-0004 - Secreto JWT por defecto hardcodeado

ID: INC-0004
Titulo: Secreto JWT por defecto hardcodeado
Prioridad: critica
Estado: resuelta
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

`backend/helpers/jwt.php` usa `getenv('JWT_SECRET')` pero si no existe aplica una clave local fija.

### Pasos para reproducir

1. Revisar `JWT::getSecret()`.
2. Comprobar fallback `clave_local_para_desarrollo_easyparte_2026`.

### Resultado esperado

Sin secreto configurado, produccion no debe arrancar o debe fallar de forma controlada.

### Resultado actual

El sistema puede firmar tokens con una clave conocida del repositorio.

### Archivos o zonas afectadas

- `backend/helpers/jwt.php`

### Riesgo

Tokens falsificables si se despliega sin variable de entorno segura.

### Propuesta de solucion

Eliminar fallback para produccion y validar configuracion de entorno.

### Correccion aplicada

`backend/helpers/jwt.php` mantiene la clave local de compatibilidad solo para `APP_ENV=local/development/dev/testing`. Fuera de esos entornos, `JWT_SECRET` es obligatorio; si falta, se registra el error tecnico con `error_log` y el cliente recibe un error generico.

Variables esperadas:

- `APP_ENV`
- `JWT_SECRET`

Nota 2026-05-22: se corrige la carga de `JWT_SECRET` mediante loader `.env` propio y se retira `SetEnv APP_ENV local` de `.htaccess` para permitir simulacion estable de `APP_ENV=production`.

### Validacion de cierre

VAL-CONFIG-009 repetida mediante archivo `.env` temporal fuera del repositorio:

- `APP_ENV=production` sin `JWT_SECRET` falla de forma controlada con error generico.
- `APP_ENV=production` con `JWT_SECRET` definido permite emitir JWT.
- No se documentaron tokens completos ni secretos.
- Validacion adicional 2026-05-22: el loader se carga antes de CORS, base de datos y JWT; para login completo en produccion simulada debe usarse un usuario MySQL no root, porque `DB_USER=root` se bloquea intencionadamente fuera de local.

### Pruebas necesarias

- [x] Prueba estatica de sintaxis PHP.
- [x] Prueba de JWT con `JWT_SECRET` configurado en entorno no local.
- [x] Prueba de arranque/fallo seguro sin secreto en entorno no local.
- [x] Repetir VAL-CONFIG-009 tras loader `.env`.

---

## INC-0005 - Modelo de roles no coincide con la documentacion objetivo

ID: INC-0005
Titulo: Modelo de roles no coincide con la documentacion objetivo
Prioridad: alta
Estado: abierta
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

El SQL solo define `Administrador`, `Atencion al Cliente` y `Tecnico`, mientras la documentacion define roles por empresa: `administrador_jefe`, `administrador`, `jefe_departamento`, `jefe_equipo`, `atencion_cliente`, `tecnico`, `solo_lectura`.

### Pasos para reproducir

1. Revisar inserts de `rol` en `bbdd/export_base_datos.sql`.
2. Comparar con `AGENT/contexto/roles_y_permisos.md`.

### Resultado esperado

Roles por empresa alineados con el modelo objetivo.

### Resultado actual

Roles globales simplificados mediante `usuario_rol`.

### Archivos o zonas afectadas

- `bbdd/export_base_datos.sql`
- `backend/routes/api.php`
- `backend/models/Usuario.php`
- `frontend/src/app/core/guards/role.guard.ts`

### Riesgo

Permisos incompletos y reglas de negocio no aplicables.

### Propuesta de solucion

Disenar migracion controlada a `empresa_usuario` y `empresa_usuario_rol`.

### Pruebas necesarias

- [ ] Pruebas por rol.
- [ ] Pruebas multiempresa.

---

## INC-0006 - No existe middleware de tenant, rol detallado ni suscripcion

ID: INC-0006
Titulo: No existe middleware de tenant, rol detallado ni suscripcion
Prioridad: critica
Estado: abierta
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

Solo existe `AuthMiddleware`. No se detectan `RoleMiddleware`, `TenantMiddleware` ni `SubscriptionMiddleware`.

### Pasos para reproducir

1. Listar `backend/middleware/`.
2. Revisar `backend/routes/api.php`.

### Resultado esperado

Endpoints protegidos con validacion centralizada de usuario, empresa, rol y suscripcion.

### Resultado actual

Autenticacion centralizada, pero permisos y tenant se resuelven de forma parcial o manual.

### Archivos o zonas afectadas

- `backend/middleware/AuthMiddleware.php`
- `backend/routes/api.php`

### Riesgo

Accesos no autorizados por rol, empresa o plan.

### Propuesta de solucion

Crear middlewares/servicios centralizados antes de ampliar funcionalidades.

### Pruebas necesarias

- [ ] Pruebas de acceso sin token.
- [ ] Pruebas por rol.
- [ ] Pruebas por empresa.

---

## INC-0007 - `GET /api/empleados` no filtra por empresa

ID: INC-0007
Titulo: Listado de empleados no filtra por empresa
Prioridad: critica
Estado: resuelta
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

`EmpleadoController::getAll()` ejecuta `SELECT * FROM empleado WHERE activo = 1` sin `id_empresa`.

### Pasos para reproducir

1. Revisar `backend/controllers/EmpleadoController.php`.
2. Localizar metodo `getAll()`.

### Resultado esperado

El listado debe filtrar por empresa activa validada.

### Resultado actual

Devuelve empleados activos de todas las empresas existentes.

### Archivos o zonas afectadas

- `backend/controllers/EmpleadoController.php`
- Endpoint: `GET /api/empleados`

### Riesgo

Fuga de datos entre empresas.

### Propuesta de solucion

Filtrar por `id_empresa` del contexto autenticado y cubrir con prueba multiempresa.

### Correccion aplicada

`EmpleadoController::getAll()` filtra por `id_empresa` recibido desde el token/contexto autenticado. La ruta ya pasaba `$usuarioLogueado` al controlador.

### Validacion de cierre

TENANT-MIN-001 queda documentada como `correcto`: Empresa A solo ve empleados de Empresa A y Empresa B solo ve empleados de Empresa B.

### Pruebas necesarias

- [x] Validacion estatica de sintaxis PHP.
- [x] Preparar datos de prueba de dos empresas.
- [ ] Verificar que un administrador solo ve empleados de su empresa.

---

## INC-0008 - Endpoints de clientes no validan rol en backend

ID: INC-0008
Titulo: Endpoints de clientes no validan rol en backend
Prioridad: alta
Estado: abierta
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

`/api/clientes` solo exige token. No se detecta validacion de roles para crear, editar o dar de baja clientes.

### Pasos para reproducir

1. Revisar caso `clientes` en `backend/routes/api.php`.
2. Revisar `ClienteController`.

### Resultado esperado

Backend debe autorizar acciones segun rol.

### Resultado actual

Cualquier usuario autenticado puede llegar al controlador.

### Archivos o zonas afectadas

- `backend/routes/api.php`
- `backend/controllers/ClienteController.php`

### Riesgo

Tecnicos o usuarios no autorizados podrian modificar clientes.

### Propuesta de solucion

Aplicar middleware/servicio de permisos por endpoint.

### Pruebas necesarias

- [ ] Crear/editar cliente como tecnico.
- [ ] Validar respuesta 403.

---

## INC-0009 - Creacion de avisos y partes no valida pertenencia de IDs relacionados

ID: INC-0009
Titulo: Creacion de avisos y partes no valida pertenencia de cliente/empleado/tarea a empresa
Prioridad: critica
Estado: resuelta
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

`AvisoController::create()` y `ParteTrabajoController::create()` usan `id_cliente`, `id_empleado` o `id_tarea` recibidos sin comprobar que pertenezcan a la empresa activa.

### Pasos para reproducir

1. Revisar metodos `create()` en `AvisoController` y `ParteTrabajoController`.
2. Observar que solo se asigna `id_empresa` desde token.

### Resultado esperado

Todo ID relacionado debe validarse contra empresa activa antes de insertar.

### Resultado actual

La base de datos podria aceptar relaciones cruzadas si existen IDs validos.

### Archivos o zonas afectadas

- `backend/controllers/AvisoController.php`
- `backend/controllers/ParteTrabajoController.php`

### Riesgo

Mezcla de datos entre empresas y fuga indirecta de informacion.

### Propuesta de solucion

Validar existencia y pertenencia de cada recurso relacionado en backend.

### Correccion parcial aplicada

Fase minima de PEN-0006:

- `AvisoController` valida que `id_cliente`, `id_empleado` e `id_departamento`, si se reciben, pertenezcan a la empresa del token antes de crear o actualizar.
- `ParteTrabajoController` valida que `id_cliente`, `id_tarea` e `id_empleado`, si se reciben, pertenezcan a la empresa del token antes de crear o actualizar.
- Los JOINs de avisos y partes limitan nombres relacionados a la misma empresa.
- No se ha creado todavia `TenantMiddleware`; queda pendiente la fase completa de tenant/multiempresa.

### Validacion de cierre

TENANT-MIN-003 y TENANT-MIN-004 quedan documentadas como `correcto`: las pruebas cruzadas de avisos y partes devolvieron HTTP 403 al usar recursos de otra empresa.

### Pruebas necesarias

- [x] Validacion estatica de sintaxis PHP.
- [x] Preparar datos de prueba multiempresa.
- [ ] Intentar crear aviso con cliente de otra empresa.
- [ ] Intentar crear parte con aviso de otra empresa.
- [ ] Repetir con empleado/departamento/tarea de otra empresa.

---

## INC-0010 - Borrado fisico de avisos

ID: INC-0010
Titulo: Borrado fisico de avisos
Prioridad: alta
Estado: abierta
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

`AvisoController::delete()` ejecuta `DELETE FROM tarea`, contrario a la recomendacion de baja logica/trazabilidad.

### Pasos para reproducir

1. Revisar `backend/controllers/AvisoController.php`.
2. Localizar metodo `delete()`.

### Resultado esperado

Cancelar o baja logica con trazabilidad.

### Resultado actual

El aviso se elimina fisicamente.

### Archivos o zonas afectadas

- `backend/controllers/AvisoController.php`
- Endpoint: `DELETE /api/avisos/{id}`

### Riesgo

Perdida de trazabilidad y posible ruptura de referencias.

### Propuesta de solucion

Sustituir por estado `Cancelada` o campo `activo/deleted_at`, con auditoria.

### Pruebas necesarias

- [ ] Cancelar aviso con parte asociado.
- [ ] Verificar trazabilidad y ausencia de borrado fisico.

---

## INC-0011 - Frontend envia `id_empresa: 1` en formularios

ID: INC-0011
Titulo: Frontend envia `id_empresa: 1` en formularios
Prioridad: critica
Estado: resuelta
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

Formularios de avisos y administracion incluyen `id_empresa: 1`.

### Pasos para reproducir

1. Revisar `frontend/src/app/features/avisos/avisos.ts`.
2. Revisar `frontend/src/app/features/administracion/administracion.ts`.

### Resultado esperado

El frontend no debe enviar `id_empresa` como autoridad de negocio.

### Resultado actual

El formulario contiene un ID de empresa fijo, aunque parte del backend usa el token.

### Archivos o zonas afectadas

- `frontend/src/app/features/avisos/avisos.ts`
- `frontend/src/app/features/administracion/administracion.ts`

### Riesgo

Confusion de seguridad, datos hardcodeados y riesgo futuro si algun endpoint confia en el body.

### Propuesta de solucion

Eliminar `id_empresa` de formularios y resolver empresa solo desde backend/contexto validado.

### Correccion aplicada

Se elimina `id_empresa` de los formularios de avisos y administracion. El frontend deja de enviar `id_empresa: 1` como autoridad de negocio; backend conserva la resolucion desde el token.

### Validacion de cierre

TENANT-MIN-005 queda documentada como `correcto`: login, dashboard, clientes, avisos, partes y administracion siguen funcionando sin enviar `id_empresa` desde frontend.

### Pruebas necesarias

- [x] Busqueda estatica sin `id_empresa` en los formularios afectados.
- [x] Build frontend correcto.
- [x] Preparar datos de prueba multiempresa.
- [ ] Crear aviso sin `id_empresa` en payload.
- [ ] Crear empleado/usuario sin `id_empresa` en payload.

---

## INC-0012 - URL de API hardcodeada y duplicada en servicios Angular

ID: INC-0012
Titulo: URL de API hardcodeada y duplicada en servicios Angular
Prioridad: media
Estado: en revision
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

Varios servicios definen `http://localhost/easyTrabajo/backend/public/api` directamente.

### Pasos para reproducir

1. Buscar `localhost/easyTrabajo/backend/public/api` en `frontend/src/app`.
2. Revisar servicios afectados.

### Resultado esperado

Configuracion centralizada por entorno.

### Resultado actual

URL local duplicada en varios servicios.

### Archivos o zonas afectadas

- `frontend/src/app/core/services/auth.service.ts`
- `frontend/src/app/core/services/admin.service.ts`
- `frontend/src/app/core/services/clientes.service.ts`
- `frontend/src/app/core/services/avisos.service.ts`
- `frontend/src/app/core/services/partes.service.ts`
- `frontend/src/app/core/services/dashboard.service.ts`

### Riesgo

Dificulta despliegue y aumenta probabilidad de errores por entorno.

### Propuesta de solucion

Centralizar API URL en configuracion/environment.

### Correccion aplicada

Se crea `frontend/src/app/core/config/api.config.ts` como punto unico de configuracion frontend para la URL base local:

```txt
http://localhost/easyTrabajo/backend/public/api
```

Los servicios `auth`, `admin`, `clientes`, `avisos`, `partes` y `dashboard` importan `API_BASE_URL` desde esa configuracion. No se modifican endpoints, nombres de metodos, payloads, autenticacion, permisos, CORS, backend ni base de datos.

Validacion documentada en:

- `AGENT/testing/frontend_api_url.md`

### Pruebas necesarias

- [x] Revision estatica de servicios sin URL hardcodeada duplicada.
- [x] Build con configuracion local.
- [ ] Prueba manual de login.
- [ ] Prueba manual de dashboard.
- [ ] Prueba manual de clientes.
- [ ] Prueba manual de avisos.
- [ ] Prueba manual de partes/albaranes.
- [ ] Prueba manual de administracion.
- [ ] Build con configuracion produccion cuando exista configuracion de entorno productiva.

---

## INC-0013 - No existe auditoria real de acciones criticas

ID: INC-0013
Titulo: No existe auditoria real de acciones criticas
Prioridad: critica
Estado: abierta
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

No se detecta tabla `auditoria_evento`, endpoint `/auditoria` ni servicio `AuditLogger`.

### Pasos para reproducir

1. Revisar `bbdd/export_base_datos.sql`.
2. Revisar `backend/`.

### Resultado esperado

Acciones criticas auditadas con usuario, empresa, entidad, accion y fecha.

### Resultado actual

No hay auditoria persistente.

### Archivos o zonas afectadas

- `bbdd/export_base_datos.sql`
- `backend/`

### Riesgo

No hay trazabilidad de cambios, cierres, roles, bajas ni accesos relevantes.

### Propuesta de solucion

Disenar e implementar auditoria en tarea separada de seguridad/trazabilidad.

### Pruebas necesarias

- [ ] Crear cliente y verificar evento.
- [ ] Cerrar parte y verificar evento.
- [ ] Cambiar rol y verificar evento.

---

## INC-0014 - Cierre de parte sin firma, hash ni bloqueo definitivo

ID: INC-0014
Titulo: Cierre de parte sin firma, hash ni bloqueo definitivo
Prioridad: alta
Estado: abierta
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

El cierre de parte se realiza cambiando `estado` a `Cerrado`; no se genera hash de integridad, firma, rectificacion ni bloqueo.

### Pasos para reproducir

1. Revisar `ParteTrabajoController::update()`.
2. Revisar tabla `parte_trabajo`.

### Resultado esperado

Cierre con firma/hash/auditoria y reglas de rectificacion.

### Resultado actual

Solo se actualiza estado y fecha de fin.

### Archivos o zonas afectadas

- `backend/controllers/ParteTrabajoController.php`
- `bbdd/export_base_datos.sql`
- `frontend/src/app/features/albaranes/albaranes.ts`

### Riesgo

Partes cerrados sin integridad ni trazabilidad.

### Propuesta de solucion

Implementar cierre formal en endpoint especifico con auditoria y hash.

### Pruebas necesarias

- [ ] Cerrar parte y verificar hash.
- [ ] Intentar editar parte cerrado/facturado.

---

## INC-0015 - Datos demo y usuarios demo incluidos en el dump SQL

ID: INC-0015
Titulo: Datos demo y usuarios demo incluidos en el dump SQL
Prioridad: media
Estado: abierta
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

`bbdd/export_base_datos.sql` incluye empresa demo, clientes, empleados, avisos, partes y usuarios demo con emails conocidos.

### Pasos para reproducir

1. Revisar `INSERT INTO` del dump SQL.

### Resultado esperado

Datos demo separados de estructura/migracion y nunca usados en produccion.

### Resultado actual

Estructura y datos demo estan mezclados en el dump.

### Archivos o zonas afectadas

- `bbdd/export_base_datos.sql`

### Riesgo

Despliegues accidentales con datos o accesos de demo.

### Propuesta de solucion

Separar schema, seed demo y datos reales; documentar que seeds no son produccion.

### Pruebas necesarias

- [ ] Importar schema limpio.
- [ ] Importar seed demo solo en local.

---

## Candidatas revisadas

- Tecnicos podrian ver partes/albaranes de otros tecnicos: no confirmada en backend actual para `GET /api/partes`, porque filtra por `id_empleado` cuando el rol es `Tecnico`. Queda riesgo residual por modelo de rol/token y por falta de pruebas multiempresa.
- Dashboard de tecnico podria sumar horas de otros usuarios: no confirmada en backend actual para horas/avisos/partes, porque filtra por `id_empleado` cuando el rol es `Tecnico`. Si cuenta todos los clientes de empresa incluso para tecnico.
- Endpoint confiando en `id_empresa` frontend: confirmado como riesgo frontend hardcodeado en INC-0011; backend revisado usa token en los endpoints principales, pero debe eliminarse del payload para evitar regresiones.
- Controlador podria exponer errores internos: confirmado para conexion de BD en INC-0003.
- Configuracion local o CORS abierto: confirmado en INC-0001, INC-0002 e INC-0004.
