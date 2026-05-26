# arquitectura.md

# EasyParte — Arquitectura objetivo

## 1. Propósito del documento

Este documento define la arquitectura objetivo de EasyParte para pasar de un proyecto TFC funcional a una aplicación más profesional, mantenible y preparada para despliegue.

EasyParte parte de una arquitectura Full-Stack con:

- Frontend Angular.
- Backend PHP.
- Base de datos MariaDB/MySQL.
- Comunicación mediante API REST.
- Diseño preparado para múltiples empresas.
- Seguridad y trazabilidad como prioridades.

El objetivo no es reescribir todo sin criterio, sino profesionalizar la estructura actual manteniendo la lógica de negocio y el estilo visual.

---

## 2. Principios de arquitectura

La arquitectura debe seguir estos principios:

1. Separación clara entre frontend, backend y base de datos.
2. El frontend nunca accede directamente a la base de datos.
3. Toda validación crítica se realiza en backend.
4. El frontend puede mejorar la experiencia visual, pero no sustituye a la seguridad.
5. El backend debe proteger rutas, roles, empresa activa y suscripción.
6. Toda entidad de negocio debe estar asociada a empresa cuando proceda.
7. La base de datos debe permitir trazabilidad, auditoría y borrado lógico.
8. El sistema debe evitar IDs hardcodeados.
9. El usuario, la empresa activa y los permisos deben obtenerse desde token/sesión validada.
10. La arquitectura debe permitir crecer por módulos.

---

## 3. Stack tecnológico objetivo

## Frontend

- Angular 21+.
- Standalone Components.
- Signals.
- Reactive Forms.
- Lazy Loading.
- Guards.
- Interceptors.
- Tailwind CSS.
- Chart.js para gráficos del dashboard.

## Backend

- PHP vanilla orientado a objetos.
- Patrón Front Controller.
- API REST.
- PDO para acceso seguro a MariaDB/MySQL.
- Middleware de autenticación.
- Middleware de roles.
- Middleware de empresa/tenant.
- Middleware de suscripción.
- Servicios de dominio.
- Repositorios para acceso a datos.

## Base de datos

- MariaDB / MySQL.
- Tablas normalizadas.
- Claves foráneas.
- Borrado lógico en entidades principales.
- Auditoría de acciones importantes.
- Hash de integridad en partes cerrados.
- Modelo SaaS con planes y suscripciones.

---

## 4. Arquitectura general

La aplicación se divide en tres bloques principales:

```txt
Frontend Angular
      ↓ HTTP/JSON
API REST PHP
      ↓ PDO
MariaDB / MySQL
```

El frontend se encarga de la interfaz, formularios, navegación, validaciones visuales y consumo de API.

El backend se encarga de autenticación, autorización, reglas de negocio, validaciones reales, auditoría, hash, exportaciones y persistencia.

La base de datos guarda la información de empresas, usuarios, clientes, avisos, presupuestos, partes, materiales, suscripciones y auditoría.

---

## 5. Frontend objetivo

Estructura recomendada:

```txt
src/app/
  core/
    guards/
    interceptors/
    interfaces/
    services/
  shared/
    components/
    modals/
    layout/
  layouts/
    auth-layout/
    dashboard-layout/
  features/
    auth/
    dashboard/
    avisos/
    partes/
    presupuestos/
    clientes/
    administracion/
    suscripcion/
    materiales/
    auditoria/
```

## Responsabilidades del frontend

El frontend debe:

- mostrar la interfaz;
- validar formularios de forma visual;
- consumir endpoints del backend;
- gestionar estados de carga y errores;
- ocultar opciones según rol;
- proteger rutas mediante guards;
- mostrar dashboard y gráficos;
- mantener el estilo visual actual.

El frontend no debe:

- decidir permisos reales;
- confiar en `id_empresa` enviado por pantalla;
- permitir acciones sin validación backend;
- calcular reglas críticas de suscripción como única fuente de verdad;
- exponer secretos.

---

## 6. Servicios Angular recomendados

Servicios principales:

- AuthService.
- UsuarioService.
- EmpresaService.
- SuscripcionService.
- DashboardService.
- ClientesService.
- AvisosService.
- PresupuestosService.
- PartesService.
- MaterialesService.
- AdminService.
- AuditoriaService.
- AlertService.

Guards recomendados:

- authGuard.
- roleGuard.
- tenantGuard.
- subscriptionGuard.

Interceptors recomendados:

- AuthInterceptor.
- ErrorInterceptor.
- LoadingInterceptor opcional.

---

## 7. Backend objetivo

Estructura recomendada:

```txt
backend/
  public/
    index.php
    .htaccess
  routes/
    api.php
  config/
    database.php
    env.php
    cors.php
  middleware/
    AuthMiddleware.php
    RoleMiddleware.php
    TenantMiddleware.php
    SubscriptionMiddleware.php
  controllers/
    AuthController.php
    ClienteController.php
    AvisoController.php
    PresupuestoController.php
    ParteController.php
    UsuarioController.php
    EmpleadoController.php
    EmpresaController.php
    SuscripcionController.php
    MaterialController.php
    DashboardController.php
    AuditoriaController.php
  services/
    AuthService.php
    ClienteService.php
    AvisoService.php
    PresupuestoService.php
    ParteService.php
    UsuarioService.php
    EmpresaService.php
    SuscripcionService.php
    MaterialService.php
    AuditLogger.php
    HashService.php
    ExportacionService.php
  repositories/
    UsuarioRepository.php
    ClienteRepository.php
    AvisoRepository.php
    PresupuestoRepository.php
    ParteRepository.php
    EmpresaRepository.php
    SuscripcionRepository.php
    MaterialRepository.php
    AuditoriaRepository.php
  helpers/
    JwtService.php
    ResponseFactory.php
    Validator.php
    PasswordHasher.php
```

---

## 8. Capas del backend

## public/index.php

Punto de entrada único de la API.

Responsabilidad:

- recibir la petición;
- cargar configuración;
- pasar la petición al router.

No debe contener lógica de negocio.

## routes/api.php

Responsabilidad:

- definir rutas;
- asociar rutas con controladores;
- aplicar middlewares;
- devolver respuestas homogéneas.

No debe contener consultas SQL directas.

## Middlewares

Responsabilidad:

- validar autenticación;
- comprobar roles;
- comprobar empresa activa;
- comprobar suscripción;
- bloquear accesos no permitidos antes de llegar al controlador.

Middlewares recomendados:

```txt
AuthMiddleware
RoleMiddleware
TenantMiddleware
SubscriptionMiddleware
```

## Controllers

Responsabilidad:

- recibir datos de la petición;
- llamar al servicio correspondiente;
- devolver respuesta JSON;
- no contener consultas SQL complejas;
- no decidir reglas de negocio profundas.

## Services

Responsabilidad:

- aplicar reglas de negocio;
- validar acciones importantes;
- calcular estados;
- llamar a repositorios;
- registrar auditoría;
- generar hash cuando proceda.

## Repositories

Responsabilidad:

- ejecutar consultas a base de datos;
- encapsular SQL;
- usar PDO y consultas preparadas;
- no decidir permisos.

---

## 9. Flujo de una petición

Ejemplo: cerrar un parte de trabajo.

```txt
Angular PartesPage
  ↓
PartesService.cerrarParte()
  ↓
HTTP PATCH /partes/{id}/cerrar
  ↓
index.php
  ↓
api.php
  ↓
AuthMiddleware
  ↓
TenantMiddleware
  ↓
SubscriptionMiddleware
  ↓
RoleMiddleware
  ↓
ParteController
  ↓
ParteService
  ↓
ParteRepository
  ↓
MariaDB
  ↓
AuditLogger
  ↓
HashService
  ↓
Respuesta JSON
```

---

## 10. Seguridad arquitectónica

La seguridad debe estar repartida por capas, pero la validación fuerte debe estar en backend.

## Frontend

- Guards para rutas privadas.
- Ocultar acciones según rol.
- Interceptor para token/sesión.
- Manejo homogéneo de errores.
- No guardar secretos.

## Backend

- Validar token/sesión.
- Obtener usuario real desde token/sesión.
- Obtener empresa activa desde contexto validado.
- Comprobar roles en backend.
- Comprobar límites de plan.
- Filtrar siempre por `id_empresa`.
- No exponer errores internos.
- Usar consultas preparadas.
- Registrar acciones críticas.

## Base de datos

- Claves foráneas.
- Restricciones.
- Índices.
- Borrado lógico.
- Auditoría.
- Hash de integridad.

---

## 11. Modelo multitenant

EasyParte será multiempresa.

Regla central:

> Toda entidad de negocio debe pertenecer a una empresa o estar vinculada a una entidad que pertenezca a una empresa.

Tablas que deben estar claramente asociadas a empresa:

- usuario mediante empresa_usuario;
- empleado;
- departamento;
- equipo;
- cliente;
- aviso;
- presupuesto;
- parte_trabajo;
- material;
- suscripcion;
- auditoria_evento;
- exportacion_facturacion.

El backend nunca debe confiar en un `id_empresa` recibido libremente desde frontend.

---

## 12. Modelo SaaS en arquitectura

El backend debe consultar la suscripción de la empresa antes de permitir acciones limitadas.

Acciones que deben revisar plan:

- crear usuario;
- crear cliente;
- crear aviso;
- crear parte;
- activar usuario;
- exportar a facturación, si el plan lo limita;
- acceder a funciones avanzadas.

Esto debe resolverse en `SubscriptionMiddleware` o en un servicio de permisos/límites.

---

## 13. Auditoría e integridad

El backend debe registrar auditoría en acciones críticas.

Ejemplos:

- login fallido relevante;
- cambio de rol;
- bloqueo de usuario;
- creación de aviso;
- asignación de técnico;
- aceptación de presupuesto;
- cierre de parte;
- rectificación de parte;
- facturación/exportación;
- cambio de suscripción.

El hash de integridad debe generarse especialmente al cerrar partes y al guardar firma.

---

## 14. Endpoints principales

Propuesta inicial de endpoints:

```txt
/auth/login
/auth/logout
/auth/me

/empresas
/suscripciones
/planes

/usuarios
/roles
/empleados
/departamentos
/equipos

/clientes
/avisos
/avisos/{id}/asignaciones

/presupuestos
/presupuestos/{id}/lineas
/presupuestos/{id}/estado

/partes
/partes/{id}/empleados
/partes/{id}/horas
/partes/{id}/materiales
/partes/{id}/firma
/partes/{id}/cerrar
/partes/{id}/rectificaciones

/materiales
/movimientos-almacen

/exportaciones-facturacion
/auditoria
/dashboard
```

---

## 15. Respuestas API

Las respuestas deben ser homogéneas.

Ejemplo correcto:

```json
{
  "success": true,
  "data": {},
  "message": "Operación realizada correctamente"
}
```

Ejemplo de error:

```json
{
  "success": false,
  "error": {
    "code": "FORBIDDEN",
    "message": "No tienes permisos para realizar esta acción"
  }
}
```

No se deben devolver trazas internas, errores SQL completos ni rutas del servidor.

---

## 16. Decisiones pendientes

- Si la sesión final se mantendrá con JWT en cliente o cookie HttpOnly.
- Integración concreta con sistemas de facturación.
- Política exacta de retención de auditoría.
- Diseño final del módulo de almacén.
- Si proyectos tipo Trello/Notion será módulo interno o aplicación conectada.
- Pasarela de pago o gestión manual de suscripciones.

---

## 17. Regla para Codex/agentes

Antes de modificar código, cualquier agente debe leer:

1. negocio.md.
2. modelo_saas.md.
3. reglas_de_negocio.md.
4. roles_y_permisos.md.
5. arquitectura.md.
6. trazabilidad_y_auditoria.md.
7. estilo_visual.md.

Si existe contradicción, prevalece la lógica de negocio y las reglas de seguridad.

Si falta información, no se debe inventar: se debe añadir a dudas_pendientes.md.
