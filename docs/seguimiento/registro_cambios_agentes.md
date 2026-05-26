# Registro de cambios de agentes - EasyParte

Este documento registra cambios realizados por agentes sobre el repositorio `easyTrabajo`.

Estados permitidos:

```txt
pendiente de revision
validado
parcial
revertido
bloqueado
```

## CAMBIO-0001 - Creacion de plantillas de seguimiento para agentes

Fecha: pendiente de completar
Agente: ChatGPT / preparacion inicial
Rama: pendiente de confirmar
Tipo de cambio: documentacion
Resumen: Se crean las plantillas iniciales dentro de `AGENT/seguimiento/` para organizar roadmap, cambios, implementacion detectada, incidencias y pendientes.
Archivos modificados:
- `AGENT/seguimiento/roadmap_tecnico.md`
- `AGENT/seguimiento/registro_cambios_agentes.md`
- `AGENT/seguimiento/implementado_backend.md`
- `AGENT/seguimiento/implementado_frontend.md`
- `AGENT/seguimiento/incidencias.md`
- `AGENT/seguimiento/pendientes.md`

Motivo: Preparar una zona de seguimiento para que Codex trabaje con limites claros y no modifique el proyecto sin control.
Pruebas realizadas: No aplica. Solo documentacion.
Riesgos: Bajo. No modifica codigo, base de datos ni configuracion.
Estado: pendiente de revision
Siguiente paso: Confirmar estrategia de ramas y pedir a Codex una auditoria inicial del repositorio antes de tocar codigo.

---

## CAMBIO-0002 - Auditoria inicial del repositorio en modo solo lectura

Fecha: 2026-05-20
Agente: Codex / agente auditor tecnico
Rama: develop
Tipo de cambio: documentacion
Resumen: Se ha revisado la documentacion base de `AGENT/`, la estructura real de `backend/`, `frontend/` y `bbdd/`, y se han completado los documentos de seguimiento con el estado real detectado.
Archivos modificados:
- `AGENT/seguimiento/implementado_backend.md`
- `AGENT/seguimiento/implementado_frontend.md`
- `AGENT/seguimiento/incidencias.md`
- `AGENT/seguimiento/pendientes.md`
- `AGENT/seguimiento/registro_cambios_agentes.md`

Motivo: Cumplir la primera tarea obligatoria de auditoria inicial antes de modificar codigo de aplicacion.
Pruebas realizadas: Revision estatica con lectura de documentacion, listado de archivos, inspeccion de rutas, controladores, servicios Angular, guards, interceptor y dump SQL. No se ejecutaron tests ni servidores.
Riesgos: No se ha validado comportamiento en ejecucion. La auditoria detecta riesgos criticos pendientes en seguridad, tenant, roles, CORS, configuracion y trazabilidad.
Estado: pendiente de revision
Siguiente paso: Revisar la auditoria y priorizar una primera tarea de seguridad, preferiblemente configuracion de entorno/CORS/JWT o tenant/backend.

---

## CAMBIO-0003 - Configuracion base de seguridad por entorno

Fecha: 2026-05-20
Agente: Codex / agente backend EasyParte
Rama: security/configuracion-entornos
Tipo de cambio: seguridad
Resumen: Se ajusta la configuracion base de backend para permitir variables de entorno en CORS, base de datos y JWT, manteniendo compatibilidad local con XAMPP y evitando exponer errores internos de PDO al cliente.
Archivos modificados:
- `backend/config/cors.php`
- `backend/config/database.php`
- `backend/helpers/jwt.php`
- `AGENT/seguimiento/incidencias.md`
- `AGENT/seguimiento/pendientes.md`
- `AGENT/seguimiento/registro_cambios_agentes.md`

Motivo: Corregir INC-0001, INC-0002, INC-0003 e INC-0004 sin modificar controladores, frontend, base de datos ni rutas.
Pruebas realizadas: Validacion de sintaxis PHP con `php -l` sobre `backend/config/cors.php`, `backend/config/database.php` y `backend/helpers/jwt.php`. Simulacion CLI de fallo de conexion DB en `APP_ENV=production`, verificando respuesta generica y detalle en `error_log`. Simulacion CLI de `JWT_SECRET` ausente en `APP_ENV=production`, verificando error generico y detalle en `error_log`.
Riesgos: Pendiente de prueba manual en XAMPP para confirmar login, conexion local y cabeceras CORS. En entornos no locales, faltas de `DB_*` o `JWT_SECRET` provocaran HTTP 500 generico y registro tecnico en logs.
Estado: pendiente de revision
Siguiente paso: Ejecutar pruebas manuales de conexion, login, fallo de BD y CORS permitido/denegado.

---

## CAMBIO-0004 - Loader .env para configuracion de seguridad

Fecha: 2026-05-22
Agente: Codex / agente backend EasyParte
Rama: security/configuracion-entornos
Tipo de cambio: seguridad
Resumen: Se crea un loader simple de `.env` sin dependencias externas, se carga al inicio del backend y se reutiliza en configuracion de base de datos y JWT. Se retira `SetEnv APP_ENV local` de `.htaccess` para que la simulacion de produccion pueda leer `APP_ENV` y `JWT_SECRET` desde `.env`.
Archivos modificados:
- `backend/config/env.php`
- `backend/public/index.php`
- `backend/config/database.php`
- `backend/helpers/jwt.php`
- `backend/public/.htaccess`
- `.gitignore`
- `.env.example`
- `AGENT/testing/validacion_configuracion_seguridad.md`
- `AGENT/seguimiento/incidencias.md`
- `AGENT/seguimiento/pendientes.md`
- `AGENT/seguimiento/registro_cambios_agentes.md`

Motivo: Corregir la carga inestable de variables en XAMPP/local y en simulacion de produccion, especialmente para `JWT_SECRET` en VAL-CONFIG-009.
Pruebas realizadas: Validacion de sintaxis PHP con `php -l` sobre `backend/config/env.php`, `backend/public/index.php`, `backend/config/database.php` y `backend/helpers/jwt.php`. Prueba CLI de lectura de variables desde `.env.example`. Prueba CLI de JWT en `APP_ENV=production` con `JWT_SECRET` presente y ausente. Pendiente validacion manual completa en XAMPP; deben repetirse login local, BD, CORS y VAL-CONFIG-009.
Riesgos: El loader `.env` es simple y no reemplaza una libreria completa. No registrar secretos ni tokens completos en logs ni documentacion. PEN-0004 sigue parcial e INC-0004 sigue en revision hasta validar manualmente.
Estado: pendiente de revision
Siguiente paso: Crear `.env` local temporal si hace falta, repetir VAL-CONFIG-009 y actualizar el documento de validacion solo con resultados reales.

---

## CAMBIO-0005 - Validacion de JWT_SECRET con loader .env

Fecha: 2026-05-22
Agente: Codex / agente backend-auditor EasyParte
Rama: security/configuracion-entornos
Tipo de cambio: seguridad/testing
Resumen: Se repite la validacion tecnica de `VAL-CONFIG-009` usando archivo `.env` temporal fuera del repositorio, confirmando fallo controlado sin `JWT_SECRET` en `APP_ENV=production` y emision de JWT cuando `JWT_SECRET` esta definido.
Archivos modificados:
- `backend/config/cors.php`
- `.env.example`
- `AGENT/testing/validacion_configuracion_seguridad.md`
- `AGENT/seguimiento/incidencias.md`
- `AGENT/seguimiento/pendientes.md`
- `AGENT/seguimiento/registro_cambios_agentes.md`

Motivo: Cerrar INC-0004 sin modificar controladores, rutas, frontend, base de datos ni logica funcional de login.
Pruebas realizadas: `php -l` sobre `backend/config/env.php`, `backend/config/cors.php`, `backend/config/database.php`, `backend/helpers/jwt.php` y `backend/public/index.php`. `git check-ignore` para `.env` y `backend/.env`. Validacion CLI con `.env` temporal: `APP_ENV=production` sin `JWT_SECRET` devuelve error generico; `APP_ENV=production` con `JWT_SECRET` emite JWT; `APP_ENV=local` sigue emitiendo JWT sin secreto explicito.
Riesgos: La prueba de VAL-CONFIG-009 fue tecnica/conceptual y no un login completo desde navegador/XAMPP. No se documentaron tokens completos, passwords ni secretos reales.
Estado: pendiente de revision
Siguiente paso: Repetir las pruebas manuales pendientes de CORS, conexion BD y regresion funcional antes de cerrar PEN-0004.

---

## CAMBIO-0006 - Revision de integracion del loader .env

Fecha: 2026-05-22
Agente: Codex / agente backend-auditor EasyParte
Rama: security/configuracion-entornos
Tipo de cambio: seguridad/testing
Resumen: Se revisa que `backend/config/env.php` se carga antes de CORS, base de datos y JWT, que busca `.env` en raiz y en `backend/.env`, y que `.env` queda ignorado por Git. Se documenta que el login completo en `APP_ENV=production` requiere usuario MySQL no root.
Archivos modificados:
- `AGENT/testing/validacion_configuracion_seguridad.md`
- `AGENT/seguimiento/incidencias.md`
- `AGENT/seguimiento/pendientes.md`
- `AGENT/seguimiento/registro_cambios_agentes.md`

Motivo: Confirmar la integracion del loader `.env` sin modificar controladores, rutas, frontend, base de datos ni logica funcional de login.
Pruebas realizadas: Revision estatica de includes en `backend/public/index.php`, `backend/config/cors.php`, `backend/config/database.php` y `backend/helpers/jwt.php`; `git check-ignore` para `.env` y `backend/.env`; `php -l` sobre `env.php`, `cors.php` y `jwt.php`; validacion CLI de `APP_ENV=production` con y sin `JWT_SECRET`, y de `APP_ENV=local` sin secreto explicito.
Riesgos: La validacion de JWT fue tecnica y no un login completo desde navegador. Si se repite login completo con `APP_ENV=production`, `DB_USER=root` bloqueara la prueba de forma intencionada; usar usuario MySQL no root.
Estado: pendiente de revision
Siguiente paso: Completar pruebas manuales pendientes de BD, CORS y regresion funcional antes de cerrar PEN-0004.

---

## CAMBIO-0007 - Cierre de configuracion base de seguridad

Fecha: 2026-05-22
Agente: Codex / agente backend-auditor EasyParte
Rama: security/configuracion-entornos
Tipo de cambio: seguridad/documentacion
Resumen: Se revisa la validacion manual de configuracion base, se confirman las pruebas de CORS, base de datos, errores genericos y JWT_SECRET, y se cierran INC-0001, INC-0002, INC-0003 e INC-0004. PEN-0004 pasa a implementado.
Archivos modificados:
- `AGENT/testing/validacion_configuracion_seguridad.md`
- `AGENT/seguimiento/incidencias.md`
- `AGENT/seguimiento/pendientes.md`
- `AGENT/seguimiento/registro_cambios_agentes.md`
- `AGENT/despliegue/variables_entorno.md`
- `AGENT/despliegue/entornos.md`

Motivo: Cerrar la fase de configuracion base de seguridad sin modificar funcionalidades, controladores, frontend, base de datos ni logica de login.
Pruebas realizadas: Revision documental de VAL-CONFIG-001 a VAL-CONFIG-010, todas en estado `correcto`; confirmacion de ausencia de tokens completos y secretos reales en la validacion; revision de estados de INC-0001 a INC-0004 y PEN-0004.
Riesgos: JWT Bearer se mantiene por compatibilidad actual. Cookie HttpOnly queda como decision futura separada. En despliegues reales debe configurarse `.env` con secretos propios y usuario MySQL no root.
Estado: pendiente de revision
Siguiente paso: Continuar con las siguientes fases de seguridad, especialmente autorizacion centralizada y tenant/multiempresa.

---

## CAMBIO-0008 - Tenant minimo para fugas directas de empresa

Fecha: 2026-05-22
Agente: Codex / agente backend-frontend EasyParte
Rama: security/tenant-minimo
Tipo de cambio: seguridad
Resumen: Se aplica una primera fase minima de PEN-0006 para evitar fugas directas por empresa sin redisenar el modelo multiempresa ni crear aun TenantMiddleware.
Archivos modificados:
- `backend/controllers/EmpleadoController.php`
- `backend/controllers/AvisoController.php`
- `backend/controllers/ParteTrabajoController.php`
- `frontend/src/app/features/avisos/avisos.ts`
- `frontend/src/app/features/administracion/administracion.ts`
- `AGENT/testing/tenant_minimo.md`
- `AGENT/seguimiento/incidencias.md`
- `AGENT/seguimiento/pendientes.md`
- `AGENT/seguimiento/registro_cambios_agentes.md`

Motivo: Corregir INC-0007, avanzar parcialmente INC-0009 y retirar `id_empresa: 1` del frontend relacionado con INC-0011.
Pruebas realizadas: `php -l` sobre `EmpleadoController.php`, `AvisoController.php` y `ParteTrabajoController.php`; busqueda estatica confirmando ausencia de `id_empresa` en los formularios frontend afectados; `npm run build` en `frontend` correcto tras repetir fuera del sandbox por `spawn EPERM` inicial.
Riesgos: Pendientes pruebas manuales multiempresa con datos de dos empresas. No existe todavia TenantMiddleware centralizado ni rediseño completo del modelo multiempresa. La validacion se limita a los controladores y formularios incluidos en esta fase.
Estado: pendiente de revision
Siguiente paso: Ejecutar `AGENT/testing/tenant_minimo.md` y planificar TenantMiddleware/contexto multiempresa real.

---

## CAMBIO-0009 - Preparacion de datos de prueba multiempresa

Fecha: 2026-05-22
Agente: Codex / agente backend-auditor EasyParte
Rama: security/tenant-minimo
Tipo de cambio: testing/documentacion
Resumen: Se preparan datos y guia para ejecutar pruebas manuales multiempresa de tenant minimo sin modificar codigo, frontend, base de datos real ni dump principal.
Archivos modificados:
- `AGENT/testing/tenant_minimo.md`
- `AGENT/testing/datos_prueba_multiempresa.md`
- `bbdd/seed_multiempresa_pruebas.sql`
- `AGENT/seguimiento/incidencias.md`
- `AGENT/seguimiento/pendientes.md`
- `AGENT/seguimiento/registro_cambios_agentes.md`

Motivo: Facilitar la validacion manual de INC-0007, INC-0011 y la parte minima de INC-0009 con datos de Empresa A y Empresa B.
Pruebas realizadas: Revision de estructura real en `bbdd/export_base_datos.sql`; preparacion de seed separado, no destructivo sobre datos reales y reversible para IDs reservados `9101` y `9102`. No se ejecuto el seed ni se modifico la base de datos.
Riesgos: El hash de password del seed es demo/reutilizado del dump local para facilitar pruebas; si la clave no es conocida, debe sustituirse por un hash local antes de importar. No usar este seed en produccion.
Estado: pendiente de revision
Siguiente paso: Importar el seed opcional en XAMPP/local y ejecutar las pruebas manuales descritas en `AGENT/testing/datos_prueba_multiempresa.md`.

---

## CAMBIO-0010 - Tabla de ejecucion manual tenant minimo

Fecha: 2026-05-22
Agente: Codex / agente auditor EasyParte
Rama: security/tenant-minimo
Tipo de cambio: testing/documentacion
Resumen: Se anade una tabla de ejecucion manual para TENANT-MIN-001 a TENANT-MIN-005 con endpoint/pantalla, token referenciado, payload, resultado esperado, resultado obtenido, estado y notas.
Archivos modificados:
- `AGENT/testing/tenant_minimo.md`
- `AGENT/seguimiento/registro_cambios_agentes.md`

Motivo: Facilitar que una persona ejecute y rellene las pruebas multiempresa sin pegar tokens completos ni contrasenas reales.
Pruebas realizadas: Revision documental. No se ejecutaron pruebas manuales ni se modifico codigo, frontend, backend funcional o base de datos.
Riesgos: Todas las filas quedan en estado `pendiente`; INC-0007, INC-0009 e INC-0011 no se cierran hasta ejecutar y validar las pruebas.
Estado: pendiente de revision
Siguiente paso: Ejecutar la tabla manual con datos multiempresa y actualizar resultados sin documentar secretos.

---

## CAMBIO-0011 - Cierre documental de tenant minimo manual

Fecha: 2026-05-25
Agente: Codex / agente auditor documental EasyParte
Rama: security/tenant-minimo
Tipo de cambio: documentacion/testing
Resumen: Se actualiza `AGENT/testing/tenant_minimo.md` para reflejar los resultados manuales ya ejecutados por la persona responsable, se corrigen textos copiados en TENANT-MIN-003 y TENANT-MIN-004, y se alinean incidencias y registro de seguimiento con estados `correcto` y `resuelta`.
Archivos modificados:
- `AGENT/testing/tenant_minimo.md`
- `AGENT/seguimiento/incidencias.md`
- `AGENT/seguimiento/registro_cambios_agentes.md`

Motivo: Eliminar la contradiccion entre los bloques de pruebas y la tabla final, dejando constancia de que Codex solo registra y ordena resultados aportados manualmente.
Pruebas realizadas: Revision documental y actualizacion de seguimiento. No se modifico backend, frontend ni base de datos.
Riesgos: PEN-0006 permanece `parcial` porque siguen pendientes TenantMiddleware, modelo multiempresa completo y roles por empresa.
Estado: pendiente de revision
Siguiente paso: Mantener la trazabilidad de esta fase y continuar con la planificacion de tenant real cuando toque.

---

## CAMBIO-0012 - Centralizacion de URL base API en frontend

Fecha: 2026-05-25
Agente: Codex / agente frontend EasyParte
Rama: security/tenant-minimo
Tipo de cambio: refactor/frontend
Resumen: Se centraliza la URL base local de la API Angular en una constante compartida y se sustituyen las URLs hardcodeadas duplicadas de los servicios principales.
Archivos modificados:
- `frontend/src/app/core/config/api.config.ts`
- `frontend/src/app/core/services/auth.service.ts`
- `frontend/src/app/core/services/admin.service.ts`
- `frontend/src/app/core/services/clientes.service.ts`
- `frontend/src/app/core/services/avisos.service.ts`
- `frontend/src/app/core/services/partes.service.ts`
- `frontend/src/app/core/services/dashboard.service.ts`
- `AGENT/testing/frontend_api_url.md`
- `AGENT/seguimiento/incidencias.md`
- `AGENT/seguimiento/pendientes.md`
- `AGENT/seguimiento/registro_cambios_agentes.md`

Motivo: Corregir INC-0012 y avanzar PEN-0012 sin cambiar contratos de servicios, endpoints, backend, base de datos, autenticacion, permisos, JWT ni CORS.
Pruebas realizadas: Revision estatica con `rg` confirmando que la URL hardcodeada solo aparece en `frontend/src/app/core/config/api.config.ts`; revision de servicios confirmando uso de `API_BASE_URL`; `npm run build` correcto tras repetir fuera del sandbox porque el primer intento fallo con `spawn EPERM`.
Riesgos: Las pruebas manuales de login, dashboard, clientes, avisos, partes/albaranes y administracion siguen pendientes en XAMPP/navegador. La rama recomendada `refactor/frontend-api-url` no pudo crearse porque Git no permitio crear `refs/heads/refactor/frontend-api-url`; no se forzo ni se elimino ninguna referencia.
Estado: pendiente de revision
Siguiente paso: Ejecutar las pruebas manuales documentadas en `AGENT/testing/frontend_api_url.md` y cerrar INC-0012 solo si todas son correctas.
