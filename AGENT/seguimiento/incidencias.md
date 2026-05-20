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
Estado: abierta
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

### Pruebas necesarias

- [ ] Prueba backend de cabeceras CORS.
- [ ] Prueba frontend desde origen permitido.
- [ ] Prueba negativa desde origen no permitido.

---

## INC-0002 - Credenciales de base de datos y usuario root en codigo

ID: INC-0002
Titulo: Credenciales de base de datos y usuario root en codigo
Prioridad: critica
Estado: abierta
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

### Pruebas necesarias

- [ ] Prueba local con variables de entorno.
- [ ] Prueba de conexion con usuario DB limitado.

---

## INC-0003 - Errores internos de base de datos expuestos

ID: INC-0003
Titulo: Errores internos de base de datos expuestos
Prioridad: critica
Estado: abierta
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

### Pruebas necesarias

- [ ] Simular fallo de conexion.
- [ ] Verificar que el cliente no recibe detalle tecnico.

---

## INC-0004 - Secreto JWT por defecto hardcodeado

ID: INC-0004
Titulo: Secreto JWT por defecto hardcodeado
Prioridad: critica
Estado: abierta
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

### Pruebas necesarias

- [ ] Prueba de login con `JWT_SECRET` configurado.
- [ ] Prueba de arranque/fallo seguro sin secreto en entorno no local.

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
Estado: abierta
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

### Pruebas necesarias

- [ ] Crear datos de dos empresas.
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
Estado: abierta
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

### Pruebas necesarias

- [ ] Intentar crear aviso con cliente de otra empresa.
- [ ] Intentar crear parte con aviso de otra empresa.

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
Estado: abierta
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

### Pruebas necesarias

- [ ] Crear aviso sin `id_empresa` en payload.
- [ ] Crear empleado/usuario sin `id_empresa` en payload.

---

## INC-0012 - URL de API hardcodeada y duplicada en servicios Angular

ID: INC-0012
Titulo: URL de API hardcodeada y duplicada en servicios Angular
Prioridad: media
Estado: abierta
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

### Pruebas necesarias

- [ ] Build con configuracion local.
- [ ] Build con configuracion produccion.

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
