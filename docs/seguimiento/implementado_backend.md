# Implementado backend - EasyParte

Auditoria inicial realizada en modo solo lectura el 2026-05-20 sobre `backend/` y `bbdd/export_base_datos.sql`.

Estado general del backend: parcial.

Estructura real detectada:

- Punto de entrada: `backend/public/index.php`.
- Router manual: `backend/routes/api.php`.
- Configuracion: `backend/config/database.php`, `backend/config/cors.php`.
- Middleware: `backend/middleware/AuthMiddleware.php`.
- Helpers: `backend/helpers/jwt.php`, `backend/helpers/response.php`.
- Controladores: `AuthController`, `ClienteController`, `AvisoController`, `ParteTrabajoController`, `DashboardController`, `EmpleadoController`, `UsuarioController`, `RolController`.
- Modelos: `Usuario`, `Cliente`.
- No se detectan carpetas `services/` ni `repositories/`.

Endpoints reales detectados:

- `POST /api/login`
- `GET /api/dashboard`
- `GET|POST /api/clientes`
- `PUT|DELETE /api/clientes/{id}`
- `GET|POST /api/avisos`
- `PUT|DELETE /api/avisos/{id}`
- `GET|POST /api/partes`
- `PUT /api/partes/{id}`
- `GET|POST /api/empleados`
- `PUT|DELETE /api/empleados/{id}`
- `GET|POST /api/usuarios`
- `PUT|DELETE /api/usuarios/{id}`
- `GET /api/roles`

## Modulo: Autenticacion

Estado: parcial

### Archivos detectados

- `backend/controllers/AuthController.php`
- `backend/models/Usuario.php`
- `backend/helpers/jwt.php`
- `backend/middleware/AuthMiddleware.php`
- `backend/routes/api.php`

### Endpoints detectados

- `POST /api/login`

### Dependencias

- Middleware: `AuthMiddleware::checkToken()` en rutas privadas.
- Helpers: `JWT`.
- Tablas: `usuario`, `usuario_rol`, `rol`, `empleado`.

### Problemas

- El JWT incluye `id_empresa` y `rol_nombre` desde tablas de usuario/rol globales, no desde modelo multiempresa `empresa_usuario`.
- `JWT_SECRET` tiene fallback hardcodeado en `backend/helpers/jwt.php`.
- No hay `/auth/me`, `/auth/logout` ni refresh.
- No se comprueba empresa activa, bloqueo por empresa ni suscripcion.
- Respuestas no siguen de forma uniforme el formato `success/data/error`.

### Pruebas realizadas

- Revision estatica de archivos y rutas. No se ejecutaron endpoints ni se modifico codigo.

### Notas

- Usa `password_verify` y token con expiracion de 8 horas.

## Modulo: Usuarios

Estado: parcial

### Archivos detectados

- `backend/controllers/UsuarioController.php`
- `backend/models/Usuario.php`
- `backend/routes/api.php`

### Endpoints detectados

- `GET /api/usuarios`
- `POST /api/usuarios`
- `PUT /api/usuarios/{id}`
- `DELETE /api/usuarios/{id}`

### Dependencias

- Middleware: `AuthMiddleware`; comprobacion manual de rol `Administrador` en `api.php`.
- Tablas: `usuario`, `usuario_rol`, `rol`, `empleado`.

### Problemas

- No existe modelo objetivo `empresa_usuario` ni roles por empresa.
- La baja es `activo = 0`, sin bloqueo por empresa ni auditoria.
- No hay limites de plan al crear usuarios.
- En `Usuario::create()` el rol por defecto es `id_rol = 2`.
- La actualizacion de `usuario_rol` no filtra por empresa.

### Pruebas realizadas

- Revision estatica.

### Notas

- Las contrasenas se guardan con `password_hash`.

## Modulo: Roles

Estado: parcial

### Archivos detectados

- `backend/controllers/RolController.php`
- `backend/routes/api.php`
- `backend/models/Usuario.php`

### Endpoints detectados

- `GET /api/roles`

### Dependencias

- Tablas: `rol`, `usuario_rol`.

### Problemas

- Roles reales del SQL: `Administrador`, `Atencion al Cliente`, `Tecnico`; no coinciden con los siete roles objetivo.
- No hay `RoleMiddleware`.
- La proteccion de administracion exige solo `Administrador`.
- No hay permisos por accion ni por ambito departamento/equipo/tecnico salvo casos puntuales.

### Pruebas realizadas

- Revision estatica.

### Notas

- El control real de permisos esta incompleto.

## Modulo: Empresas

Estado: parcial

### Archivos detectados

- `backend/config/database.php`
- Uso de `id_empresa` en controladores y modelos.

### Endpoints detectados

- No hay endpoints `/empresas`.

### Dependencias

- Tablas: `empresa`; `usuario.id_empresa` en lugar de `empresa_usuario`.

### Problemas

- No hay seleccion/resolucion de empresa activa multiempresa.
- No existe `TenantMiddleware`.
- La empresa se toma del JWT generado desde `usuario.id_empresa`.
- No hay CRUD ni gestion de empresa.

### Pruebas realizadas

- Revision estatica de backend y SQL.

### Notas

- Varias consultas filtran por `id_empresa`, pero el modelo no es el objetivo SaaS multiempresa.

## Modulo: Empleados

Estado: parcial

### Archivos detectados

- `backend/controllers/EmpleadoController.php`
- `backend/routes/api.php`

### Endpoints detectados

- `GET /api/empleados`
- `POST /api/empleados`
- `PUT /api/empleados/{id}`
- `DELETE /api/empleados/{id}`

### Dependencias

- Middleware: `AuthMiddleware`; rol `Administrador` en `api.php`.
- Tablas: `empleado`, `departamento`, `empresa`.

### Problemas

- `getAll()` no filtra por `id_empresa`, aunque la ruta exige administrador.
- No hay modelo/repository.
- No hay auditoria ni validacion de departamento perteneciente a la empresa.
- No hay equipos.

### Pruebas realizadas

- Revision estatica.

### Notas

- Crear, editar y baja logica existen parcialmente.

## Modulo: Departamentos

Estado: pendiente

### Archivos detectados

- Tabla `departamento` en SQL.

### Endpoints detectados

- No detectados.

### Dependencias

- Tabla: `departamento`.

### Problemas

- No hay controlador, modelo, servicio, rutas ni frontend especifico.

### Pruebas realizadas

- Revision estatica.

### Notas

- Existe solo como soporte en base de datos.

## Modulo: Clientes

Estado: parcial

### Archivos detectados

- `backend/controllers/ClienteController.php`
- `backend/models/Cliente.php`
- `backend/routes/api.php`

### Endpoints detectados

- `GET /api/clientes`
- `POST /api/clientes`
- `PUT /api/clientes/{id}`
- `DELETE /api/clientes/{id}`

### Dependencias

- Middleware: `AuthMiddleware`.
- Helper: `Response` en parte del controlador.
- Tabla: `cliente`.

### Problemas

- No hay control de rol por accion: cualquier usuario autenticado puede crear, editar o dar de baja clientes.
- No hay comprobacion de limites de plan.
- Validacion backend basica.
- Respuestas mezclan `Response` con `echo json_encode`.
- No hay auditoria.

### Pruebas realizadas

- Revision estatica.

### Notas

- Listado, alta, edicion y baja logica filtran por `id_empresa`.

## Modulo: Avisos/tareas

Estado: parcial

### Archivos detectados

- `backend/controllers/AvisoController.php`
- `backend/routes/api.php`

### Endpoints detectados

- `GET /api/avisos`
- `POST /api/avisos`
- `PUT /api/avisos/{id}`
- `DELETE /api/avisos/{id}`

### Dependencias

- Middleware: `AuthMiddleware`.
- Tablas: `tarea`, `cliente`, `empleado`, `departamento`, `empresa`, `usuario`.

### Problemas

- El modulo usa tabla `tarea` como aviso.
- Solo permite un tecnico por `id_empleado`; no existe `aviso_empleado`.
- Tecnico puede ver avisos sin asignar ademas de los suyos.
- Crear/editar/cancelar no valida rol en backend.
- Al crear/editar no se comprueba que `id_cliente` o `id_empleado` pertenezcan a la empresa.
- `DELETE` elimina fisicamente el aviso.
- No hay auditoria ni historial.

### Pruebas realizadas

- Revision estatica.

### Notas

- Estados reales: `Pendiente`, `En proceso`, `Finalizada`, `Cancelada`.

## Modulo: Partes/albaranes

Estado: parcial

### Archivos detectados

- `backend/controllers/ParteTrabajoController.php`
- `backend/routes/api.php`

### Endpoints detectados

- `GET /api/partes`
- `POST /api/partes`
- `PUT /api/partes/{id}`

### Dependencias

- Middleware: `AuthMiddleware`.
- Tablas: `parte_trabajo`, `cliente`, `tarea`, `empleado`, `empresa`.

### Problemas

- Solo un empleado por parte (`id_empleado`); no existen `parte_empleado` ni `parte_hora`.
- Horas y material son campos directos, no tablas normalizadas.
- No hay firma real, hash de firma ni hash de integridad.
- Cerrar parte no genera auditoria ni bloqueo de rectificacion.
- No hay endpoint especifico de firma, cierre, horas, materiales, rectificaciones o facturacion.
- Crear parte no valida que cliente/tarea/empleado pertenezcan a la empresa.

### Pruebas realizadas

- Revision estatica.

### Notas

- Para rol `Tecnico`, listado y update filtran por `id_empleado`.

## Modulo: Dashboard

Estado: parcial

### Archivos detectados

- `backend/controllers/DashboardController.php`
- `backend/routes/api.php`

### Endpoints detectados

- `GET /api/dashboard`

### Dependencias

- Middleware: `AuthMiddleware`.
- Tablas: `cliente`, `tarea`, `parte_trabajo`.

### Problemas

- Solo distingue rol `Tecnico`; no hay vistas por jefe, departamento, equipo ni atencion cliente.
- Cuenta todos los clientes de empresa incluso para tecnico.
- Depende de rol en token.
- No hay endpoints separados `/dashboard/empresa`, `/dashboard/tecnico`, etc.

### Pruebas realizadas

- Revision estatica.

### Notas

- Horas, avisos y partes si filtran por tecnico cuando `rol_nombre === 'Tecnico'`.

## Modulo: Presupuestos

Estado: pendiente

### Archivos detectados

- No detectados en backend ni SQL.

### Endpoints detectados

- No detectados.

### Dependencias

- No existen tablas `presupuesto`, `presupuesto_linea` ni historial.

### Problemas

- Modulo objetivo no implementado.

### Pruebas realizadas

- Revision estatica.

### Notas

- Solo aparece en documentacion AGENT.

## Modulo: Suscripciones

Estado: pendiente

### Archivos detectados

- No detectados.

### Endpoints detectados

- No detectados.

### Dependencias

- No existen tablas `plan` ni `suscripcion`.

### Problemas

- No hay `SubscriptionMiddleware` ni control de limites SaaS.

### Pruebas realizadas

- Revision estatica.

### Notas

- Modelo SaaS existe solo en documentacion objetivo.

## Modulo: Auditoria

Estado: pendiente

### Archivos detectados

- No detectados.

### Endpoints detectados

- No detectados.

### Dependencias

- No existe tabla `auditoria_evento`.

### Problemas

- Ninguna accion critica registrada en auditoria.
- No hay `AuditLogger`.

### Pruebas realizadas

- Revision estatica.

### Notas

- Requisito clave de profesionalizacion pendiente.

## Modulo: Materiales

Estado: parcial

### Archivos detectados

- `backend/controllers/ParteTrabajoController.php`
- Campo `parte_trabajo.material` en SQL.

### Endpoints detectados

- No hay endpoints `/materiales`.

### Dependencias

- Tabla: `parte_trabajo`.

### Problemas

- Material existe solo como texto libre en parte.
- No hay catalogo, stock, movimientos ni relacion normalizada.

### Pruebas realizadas

- Revision estatica.

### Notas

- Puede cubrir uso minimo manual, no el modulo objetivo.

## Modulo: Exportacion a facturacion

Estado: pendiente

### Archivos detectados

- No detectados.

### Endpoints detectados

- No detectados.

### Dependencias

- No existe tabla `exportacion_facturacion`.

### Problemas

- No hay preparacion tecnica real para exportaciones.

### Pruebas realizadas

- Revision estatica.

### Notas

- Facturacion completa queda fuera de alcance inicial, pero la exportacion prevista no esta implementada.
