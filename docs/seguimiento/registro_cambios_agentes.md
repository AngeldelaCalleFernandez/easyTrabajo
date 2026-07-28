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
- `docs/seguimiento/incidencias.md`
- `docs/seguimiento/pendientes.md`
- `docs/seguimiento/registro_cambios_agentes.md`

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
- `docs/testing/frontend_api_url.md`
- `AGENT/seguimiento/incidencias.md`
- `AGENT/seguimiento/pendientes.md`
- `AGENT/seguimiento/registro_cambios_agentes.md`

Motivo: Corregir INC-0012 y avanzar PEN-0012 sin cambiar contratos de servicios, endpoints, backend, base de datos, autenticacion, permisos, JWT ni CORS.
Pruebas realizadas: Revision estatica con `rg` confirmando que la URL hardcodeada solo aparece en `frontend/src/app/core/config/api.config.ts`; revision de servicios confirmando uso de `API_BASE_URL`; `npm run build` correcto.
Riesgos: En ese momento quedaban pendientes las pruebas manuales de login, dashboard, clientes, avisos, partes/albaranes y administracion; quedan registradas como correctas en CAMBIO-0014. La rama recomendada `refactor/frontend-api-url` no pudo crearse porque Git no permitio crear `refs/heads/refactor/frontend-api-url`; no se forzo ni se elimino ninguna referencia.
Estado: pendiente de revision
Siguiente paso: Pruebas manuales cerradas posteriormente en CAMBIO-0014.

---

## CAMBIO-0013 - Revision documental de URL base API frontend

Fecha: 2026-05-26
Agente: Codex / agente frontend EasyParte
Rama: security/tenant-minimo
Tipo de cambio: documentacion/testing
Resumen: Se revisa la centralizacion de la URL base de API en frontend, se confirma que los servicios siguen usando `API_BASE_URL` y se corrigen referencias documentales antiguas a `AGENT/testing/frontend_api_url.md`.
Archivos modificados:
- `docs/testing/frontend_api_url.md`
- `docs/seguimiento/incidencias.md`
- `docs/seguimiento/pendientes.md`
- `docs/seguimiento/registro_cambios_agentes.md`

Motivo: Dejar INC-0012 coherente con la estructura documental actual en `docs/` y con la verificacion tecnica previa al cierre manual.
Pruebas realizadas: `rg` confirmando que `http://localhost/easyTrabajo/backend/public/api` solo aparece en `frontend/src/app/core/config/api.config.ts`; revision de servicios confirmando imports de `API_BASE_URL`; `npm run build` correcto en `frontend`.
Riesgos: En ese momento quedaban pendientes pruebas manuales en XAMPP/navegador de login, dashboard, clientes, avisos, partes/albaranes y administracion; quedan registradas como correctas en CAMBIO-0014.
Estado: pendiente de revision
Siguiente paso: Pruebas manuales cerradas posteriormente en CAMBIO-0014.

---

## CAMBIO-0014 - Cierre documental de INC-0012

Fecha: 2026-05-26
Agente: Codex / agente frontend-auditor documental EasyParte
Rama: security/tenant-minimo
Tipo de cambio: documentacion/testing
Resumen: Se registran los resultados manuales aportados para login, dashboard, clientes, avisos, partes/albaranes y administracion, todos correctos tras la centralizacion de `API_BASE_URL`.
Archivos modificados:
- `docs/testing/frontend_api_url.md`
- `docs/seguimiento/incidencias.md`
- `docs/seguimiento/pendientes.md`
- `docs/seguimiento/registro_cambios_agentes.md`

Motivo: Cerrar la incoherencia documental y permitir que INC-0012 pase a `resuelta` y PEN-0012 a `implementado` sin modificar logica funcional.
Pruebas realizadas: Pruebas manuales ejecutadas por la persona responsable del proyecto; Codex solo registra los resultados aportados. No se documentaron tokens ni contrasenas.
Riesgos: No se ha definido todavia una configuracion frontend productiva por entorno; la URL local queda centralizada y preparada para esa evolucion.
Estado: pendiente de revision
Siguiente paso: Definir configuracion frontend productiva cuando se prepare despliegue fuera de XAMPP/local.

---

## CAMBIO-0015 - Matriz minima de permisos backend

Fecha: 2026-05-26
Agente: Codex / agente backend-auditor EasyParte
Rama: security/tenant-minimo
Tipo de cambio: seguridad/documentacion
Resumen: Se disena una matriz minima de autorizacion backend usando los roles actuales reales (`Administrador`, `Atencion al Cliente`, `Tecnico`) y se diferencia entre permisos actuales detectados, permisos recomendados para PEN-0005 y roles objetivo futuros.
Archivos modificados:
- `docs/contexto/matriz_permisos_backend.md`
- `docs/seguimiento/incidencias.md`
- `docs/seguimiento/pendientes.md`
- `docs/seguimiento/registro_cambios_agentes.md`

Motivo: Preparar PEN-0005 antes de implementar cambios, especialmente para corregir INC-0008 en una tarea pequena posterior.
Pruebas realizadas: Revision documental y estatica en modo solo lectura de `backend/routes/api.php`, `AuthMiddleware`, controladores de clientes, avisos, partes, empleados, usuarios, roles y dashboard, modelos de cliente/usuario y roles definidos en `bbdd/export_base_datos.sql`. No se modifico codigo, frontend ni base de datos.
Riesgos: La matriz es una propuesta previa; no aplica permisos por si misma. INC-0008 sigue sin corregir hasta implementar comprobaciones backend y ejecutar pruebas negativas por rol.
Estado: pendiente de revision
Siguiente paso: Implementar comprobacion minima de roles para `/api/clientes` y validar que Tecnico no puede crear, editar ni dar de baja clientes.

---

## CAMBIO-0016 - Control minimo de roles en clientes

Fecha: 2026-05-26
Agente: Codex / agente backend EasyParte
Rama: security/clientes-role-check
Tipo de cambio: seguridad/backend
Resumen: Se aplica una validacion minima de roles en `backend/routes/api.php` para `/api/clientes`, usando el rol del usuario autenticado y sin modificar controladores, frontend, base de datos, login, JWT ni CORS.
Archivos modificados:
- `backend/routes/api.php`
- `docs/testing/clientes_permisos.md`
- `docs/seguimiento/incidencias.md`
- `docs/seguimiento/pendientes.md`
- `docs/seguimiento/registro_cambios_agentes.md`

Motivo: Corregir la primera fase de INC-0008 evitando que cualquier usuario autenticado pueda crear, editar o dar de baja clientes.
Pruebas realizadas: Validacion estatica con `php -l backend/routes/api.php`. Se documentan pruebas manuales pendientes en `docs/testing/clientes_permisos.md`; no se documentan tokens ni contrasenas.
Riesgos: `Tecnico` queda bloqueado tambien en `GET /api/clientes` por decision de esta fase; si algun flujo tecnico dependia de listar clientes directamente, debera ajustarse con una decision funcional explicita. PEN-0005 sigue parcial porque no existe aun autorizacion centralizada completa.
Estado: pendiente de revision
Siguiente paso: Ejecutar pruebas manuales de permisos de clientes y regresion de login, dashboard, avisos y partes.

---

## CAMBIO-0017 - Decision funcional de baja logica de clientes

Fecha: 2026-05-26
Agente: Codex / agente backend-auditor documental EasyParte
Rama: security/clientes-role-check
Tipo de cambio: seguridad/backend/documentacion
Resumen: Se ajusta la validacion minima de `/api/clientes` para permitir a `Atencion al Cliente` ejecutar `DELETE /api/clientes/{id}` como baja logica, manteniendo bloqueado a `Tecnico` y conservando CRUD completo para `Administrador`.
Archivos modificados:
- `backend/routes/api.php`
- `docs/contexto/matriz_permisos_backend.md`
- `docs/testing/clientes_permisos.md`
- `docs/seguimiento/incidencias.md`
- `docs/seguimiento/pendientes.md`
- `docs/seguimiento/registro_cambios_agentes.md`

Motivo: Registrar la decision funcional revisada: la baja de cliente actual cambia `activo` de 1 a 0 y no realiza borrado fisico, por lo que `Atencion al Cliente` puede ejecutarla.
Pruebas realizadas: Validacion estatica con `php -l backend/routes/api.php`. Las pruebas manuales de permisos de clientes fueron ejecutadas por la persona responsable del proyecto y Codex solo registra los resultados aportados. No se documentaron tokens ni contrasenas.
Riesgos: PEN-0005 sigue parcial porque aun faltan permisos en avisos, partes, administracion o un middleware/servicio centralizado de autorizacion.
Estado: pendiente de revision
Siguiente paso: Continuar la autorizacion backend minima en los modulos pendientes sin migrar todavia al modelo objetivo de roles.

---

## CAMBIO-0018 - Decision funcional sobre cancelacion y borrado de avisos

Fecha: 2026-05-26
Agente: Codex / agente backend-auditor documental EasyParte
Rama: security/clientes-role-check
Tipo de cambio: seguridad/documentacion
Resumen: Se documenta la decision funcional de separar cancelacion de aviso y borrado fisico de aviso antes de implementar permisos de avisos.
Archivos modificados:
- `docs/contexto/matriz_permisos_backend.md`
- `docs/seguimiento/incidencias.md`
- `docs/seguimiento/pendientes.md`
- `docs/seguimiento/registro_cambios_agentes.md`
- `docs/contexto/dudas_pendientes.md`

Motivo: Evitar mezclar cancelar con borrar fisicamente. El borrado fisico solo debera permitirse si el aviso ya esta cancelado y nunca para `Tecnico`.
Pruebas realizadas: Revision documental. No se modifico codigo, backend, frontend, base de datos, rutas ni controladores.
Riesgos: Duda funcional resuelta posteriormente en CAMBIO-0019: el tecnico solo puede cancelar avisos asignados a el.
Estado: pendiente de revision
Siguiente paso: Ver CAMBIO-0019 antes de implementar permisos de avisos.

---

## CAMBIO-0019 - Decision cerrada sobre cancelacion de avisos por tecnico

Fecha: 2026-05-26
Agente: Codex / agente backend-auditor documental EasyParte
Rama: security/clientes-role-check
Tipo de cambio: seguridad/documentacion
Resumen: Se documenta la decision funcional cerrada: `Tecnico` solo puede cancelar avisos asignados a el, no avisos sin asignar ni asignados a otros tecnicos.
Archivos modificados:
- `docs/contexto/matriz_permisos_backend.md`
- `docs/seguimiento/incidencias.md`
- `docs/seguimiento/pendientes.md`
- `docs/seguimiento/registro_cambios_agentes.md`

Motivo: Dejar preparada la fase de permisos de avisos sin dudas sobre el alcance de cancelacion del rol `Tecnico`.
Pruebas realizadas: Revision documental. No se modifico codigo, backend, frontend, base de datos, rutas ni controladores.
Riesgos: En esta fase INC-0010 seguia abierta hasta implementar la separacion real entre cancelacion y borrado fisico; ver avance posterior en CAMBIO-0020.
Estado: pendiente de revision
Siguiente paso: Implementar permisos de avisos manteniendo `PEN-0005` como parcial hasta cubrir tambien partes, administracion o un servicio/middleware centralizado.

---

## CAMBIO-0020 - Cancelacion segura de avisos sin borrado fisico

Fecha: 2026-05-26
Agente: Codex / agente backend EasyParte
Rama: security/clientes-role-check
Tipo de cambio: seguridad/backend
Resumen: Se anade una primera fase de cancelacion segura de avisos mediante `PUT /api/avisos/{id}/cancelar`, cambiando el estado a `Cancelada` sin eliminar fisicamente el registro, y se evita el bypass por `PUT /api/avisos/{id}` con `estado = Cancelada`.
Archivos modificados:
- `backend/routes/api.php`
- `backend/controllers/AvisoController.php`
- `docs/testing/avisos_cancelacion.md`
- `docs/seguimiento/incidencias.md`
- `docs/seguimiento/pendientes.md`
- `docs/seguimiento/registro_cambios_agentes.md`

Motivo: Separar la cancelacion de avisos del borrado fisico actual y evitar que `DELETE /api/avisos/{id}` elimine registros durante esta fase.
Pruebas realizadas: Validacion estatica con `php -l backend/routes/api.php` y `php -l backend/controllers/AvisoController.php`. Se documentan pruebas manuales pendientes en `docs/testing/avisos_cancelacion.md`; no se documentan tokens ni contrasenas.
Riesgos: INC-0010 queda en revision, no resuelta, porque falta ejecutar pruebas manuales y queda pendiente decidir o implementar el borrado fisico condicionado de avisos ya cancelados.
Estado: pendiente de revision
Siguiente paso: Ejecutar pruebas manuales AVISO-CAN-001 a AVISO-CAN-008 y validar que no hay borrado fisico.

---

## CAMBIO-0021 - Ajustes funcionales de permisos de clientes y avisos

Fecha: 2026-05-27
Agente: Codex / agente backend-frontend EasyParte
Rama: security/clientes-role-check
Tipo de cambio: seguridad/backend/frontend
Resumen: Se ajustan permisos minimos tras pruebas manuales: `Tecnico` puede listar clientes, sigue bloqueado para crear/editar/baja; `Atencion al Cliente` puede ver avisos de su empresa; `Tecnico` puede crear avisos asignados a si mismo y no a otros tecnicos; tampoco puede reasignar avisos a otro tecnico por `PUT`; el servicio frontend de avisos usa `PUT /api/avisos/{id}/cancelar`.
Archivos modificados:
- `backend/routes/api.php`
- `backend/controllers/AvisoController.php`
- `frontend/src/app/features/clientes/clientes.ts`
- `frontend/src/app/features/avisos/avisos.ts`
- `frontend/src/app/core/services/avisos.service.ts`
- `docs/contexto/matriz_permisos_backend.md`
- `docs/testing/avisos_cancelacion.md`
- `docs/testing/clientes_permisos.md`
- `docs/seguimiento/incidencias.md`
- `docs/seguimiento/pendientes.md`
- `docs/seguimiento/registro_cambios_agentes.md`

Motivo: Corregir ajustes funcionales detectados en pruebas manuales sin modificar base de datos, login, JWT, CORS, modelo de roles ni crear `RoleMiddleware`.
Pruebas realizadas: `php -l backend/routes/api.php`, `php -l backend/controllers/AvisoController.php` y `npm run build` en `frontend`. Se documentan pruebas manuales pendientes; no se documentan tokens ni contrasenas.
Riesgos: Los templates HTML de clientes y avisos contienen condiciones visuales de rol; para ocultar/mostrar botones con precision por aviso puede requerirse una tarea permitiendo editar `frontend/src/app/features/clientes/clientes.html` y `frontend/src/app/features/avisos/avisos.html`. INC-0010 sigue en revision y PEN-0005 parcial.
Estado: pendiente de revision
Siguiente paso: Ejecutar pruebas manuales actualizadas y permitir ajuste de templates si la visibilidad del boton Cancelar no coincide con la regla por aviso asignado.

---

## CAMBIO-0022 - Correccion parcial de cancelacion de avisos por rol

Fecha: 2026-05-27
Agente: Codex / agente backend-frontend EasyParte
Rama: security/clientes-role-check
Tipo de cambio: seguridad/backend/frontend
Resumen: Se ajusta la cancelacion backend para que `Atencion al Cliente` pueda cancelar avisos de su empresa aunque el rol llegue con variante acentuada. Se mantiene la llamada frontend a `PUT /api/avisos/{id}/cancelar` y la validacion TS de permisos antes de cancelar.
Archivos modificados:
- `backend/controllers/AvisoController.php`
- `docs/testing/avisos_cancelacion.md`
- `docs/seguimiento/incidencias.md`
- `docs/seguimiento/pendientes.md`
- `docs/seguimiento/registro_cambios_agentes.md`

Motivo: Corregir el bloqueo de cancelacion para `Atencion al Cliente` detectado en pruebas manuales, sin modificar base de datos, login, JWT, CORS, modelo de roles ni implementar borrado fisico condicionado.
Pruebas realizadas: `php -l backend/controllers/AvisoController.php`, `php -l backend/routes/api.php` y `npm run build` en `frontend`. No se documentan tokens ni contrasenas.
Riesgos: El boton de cancelacion de `Tecnico` depende del template `frontend/src/app/features/avisos/avisos.html`, que no estaba permitido en esta tarea. La accion TS sigue protegida, pero la visibilidad exacta del boton requiere permitir ese archivo.
Estado: pendiente de revision
Siguiente paso: Permitir ajuste de `frontend/src/app/features/avisos/avisos.html` para mostrar el boton Cancelar solo cuando `puedeCancelarAviso(tarea)` sea verdadero.

---

## CAMBIO-0023 - Permisos explicitos de creacion de avisos

Fecha: 2026-05-27
Agente: Codex / agente backend-frontend EasyParte
Rama: security/clientes-role-check
Tipo de cambio: seguridad/backend
Resumen: Se explicitan en `backend/routes/api.php` los roles permitidos para `POST /api/avisos`: `Administrador`, `Atencion al Cliente` y `Tecnico`. Se mantiene en `AvisoController` la restriccion de que `Tecnico` no puede asignar avisos a otro empleado. La regla de autoasignacion se revisa posteriormente en CAMBIO-0026.
Archivos modificados:
- `backend/routes/api.php`
- `docs/testing/avisos_cancelacion.md`
- `docs/seguimiento/incidencias.md`
- `docs/seguimiento/pendientes.md`
- `docs/seguimiento/registro_cambios_agentes.md`

Motivo: Corregir la validacion manual donde `Tecnico` y `Atencion al Cliente` no podian crear avisos segun la regla funcional vigente.
Pruebas realizadas: `php -l backend/routes/api.php`, `php -l backend/controllers/AvisoController.php` y `npm run build` en `frontend`. No se documentan tokens ni contrasenas.
Riesgos: La visibilidad exacta del boton Cancelar para `Tecnico` sigue requiriendo editar `frontend/src/app/features/avisos/avisos.html`, no incluido en archivos permitidos en esta tarea.
Estado: pendiente de revision
Siguiente paso: Ejecutar AVISO-CAN-009 a AVISO-CAN-011 y permitir el ajuste del template de avisos para cerrar la parte visual del boton Cancelar.

---

## CAMBIO-0024 - Lectura de empleados para asignacion de avisos

Fecha: 2026-05-27
Agente: Codex / agente backend-frontend EasyParte
Rama: security/clientes-role-check
Tipo de cambio: seguridad/backend/documentacion
Resumen: Se ajusta `GET /api/empleados` para permitir lectura a `Atencion al Cliente`, filtrada por empresa, con el objetivo de volver a poblar el selector de trabajadores al crear o editar avisos. `POST`, `PUT` y `DELETE` de empleados siguen reservados a `Administrador`.
Archivos modificados:
- `backend/routes/api.php`
- `docs/contexto/matriz_permisos_backend.md`
- `docs/testing/avisos_cancelacion.md`
- `docs/seguimiento/incidencias.md`
- `docs/seguimiento/pendientes.md`
- `docs/seguimiento/registro_cambios_agentes.md`

Motivo: Corregir el 403 detectado manualmente en `GET /api/empleados` para `Atencion al Cliente` sin abrir gestion general de empleados ni tocar base de datos, login, JWT, CORS, tenant o modelo de roles.
Pruebas realizadas: `php -l backend/routes/api.php`. Las pruebas manuales quedan documentadas como pendientes; no se documentaron tokens ni contrasenas.
Riesgos: La visibilidad exacta del boton Cancelar para `Tecnico` sigue dependiendo de `frontend/src/app/features/avisos/avisos.html`, archivo no permitido en esta tarea. La regla backend y la llamada segura `PUT /api/avisos/{id}/cancelar` se mantienen.
Estado: pendiente de revision
Siguiente paso: Ejecutar AVISO-CAN-018 a AVISO-CAN-021 y permitir el ajuste del template de avisos para mostrar el boton Cancelar a `Tecnico` solo en avisos asignados a el.

---

## CAMBIO-0025 - Visibilidad del boton Cancelar en avisos

Fecha: 2026-05-27
Agente: Codex / agente frontend EasyParte
Rama: security/clientes-role-check
Tipo de cambio: frontend/documentacion
Resumen: Se corrige la visibilidad del boton Cancelar en la pantalla de avisos para que dependa de `puedeCancelarAviso(tarea)`, manteniendo la llamada existente a `cancelarAviso(...)` y sin tocar backend, servicios, rutas ni reglas de negocio.
Archivos modificados:
- `frontend/src/app/features/avisos/avisos.html`
- `docs/testing/avisos_cancelacion.md`
- `docs/seguimiento/incidencias.md`
- `docs/seguimiento/pendientes.md`
- `docs/seguimiento/registro_cambios_agentes.md`

Motivo: Permitir que `Tecnico` vea el boton Cancelar solo en avisos asignados a su `id_empleado`, mientras `Administrador` y `Atencion al Cliente` lo ven en avisos cancelables de su empresa.
Pruebas realizadas: `npm run build` en `frontend` correcto. Las pruebas manuales por rol quedan documentadas como pendientes; no se documentaron tokens ni contrasenas.
Riesgos: El backend ya protege la accion, pero la visibilidad visual debe validarse manualmente en navegador con usuarios reales de cada rol.
Estado: pendiente de revision
Siguiente paso: Ejecutar AVISO-CAN-013 a AVISO-CAN-016 en navegador y confirmar que los avisos ya cancelados no muestran el boton.

---

## CAMBIO-0026 - Selector de trabajadores y creacion de avisos por tecnico

Fecha: 2026-05-27
Agente: Codex / agente backend-frontend EasyParte
Rama: security/clientes-role-check
Tipo de cambio: seguridad/backend/frontend/documentacion
Resumen: Se corrige la carga del selector de trabajadores para `Atencion al Cliente` endureciendo la comprobacion de rol de `GET /api/empleados` frente a variantes con acentos/codificacion, sin abrir `POST`, `PUT` ni `DELETE` de empleados. Tambien se ajusta la creacion de avisos por `Tecnico` para permitir avisos libres o asignados a si mismo, pero nunca a otro trabajador.
Archivos modificados:
- `backend/routes/api.php`
- `backend/controllers/AvisoController.php`
- `frontend/src/app/features/avisos/avisos.ts`
- `frontend/src/app/features/avisos/avisos.html`
- `docs/testing/avisos_cancelacion.md`
- `docs/seguimiento/incidencias.md`
- `docs/seguimiento/pendientes.md`
- `docs/seguimiento/registro_cambios_agentes.md`
- `docs/contexto/matriz_permisos_backend.md`

Motivo: Resolver dos fallos concretos del modulo de avisos: el desplegable vacio para `Atencion al Cliente` y la autoasignacion forzada de avisos creados por `Tecnico`.
Pruebas realizadas: `php -l backend/routes/api.php`, `php -l backend/controllers/AvisoController.php` y `npm run build` en `frontend` correctos. Las pruebas manuales por rol quedan documentadas como pendientes; no se documentaron tokens ni contrasenas.
Riesgos: La validacion real de permisos requiere repetir las pruebas manuales con usuarios de `Administrador`, `Atencion al Cliente` y `Tecnico` en XAMPP/local.
Estado: pendiente de revision
Siguiente paso: Ejecutar AVISO-CAN-009 a AVISO-CAN-012, AVISO-CAN-018 a AVISO-CAN-021 y AVISO-CAN-022.

---

## CAMBIO-0027 - Permisos minimos backend en partes/albaranes

Fecha: 2026-05-27
Agente: Codex / agente backend-auditor EasyParte
Rama: security/clientes-role-check
Tipo de cambio: seguridad/backend/documentacion
Resumen: Se aplican permisos minimos backend en `/api/partes`: `Atencion al Cliente` queda solo lectura, `Tecnico` solo puede ver/editar partes propios y crear partes para si mismo; si crea desde aviso, el aviso debe estar asignado al tecnico. `Administrador` conserva operaciones sobre partes de su empresa.
Archivos modificados:
- `backend/routes/api.php`
- `backend/controllers/ParteTrabajoController.php`
- `docs/testing/partes_permisos.md`
- `docs/contexto/matriz_permisos_backend.md`
- `docs/seguimiento/incidencias.md`
- `docs/seguimiento/pendientes.md`
- `docs/seguimiento/registro_cambios_agentes.md`

Motivo: Continuar PEN-0005 con permisos minimos de partes/albaranes sin modificar frontend, base de datos, login, JWT, CORS, tenant ni crear `RoleMiddleware` completo.
Pruebas realizadas: `php -l backend/routes/api.php` y `php -l backend/controllers/ParteTrabajoController.php` correctos. Las pruebas manuales quedan documentadas como pendientes; no se documentaron tokens ni contrasenas.
Riesgos: INC-0014 sigue abierta porque el cierre formal con firma, hash, bloqueo y rectificacion no forma parte de esta fase.
Estado: pendiente de revision
Siguiente paso: Ejecutar `docs/testing/partes_permisos.md` con usuarios de Administrador, Atencion al Cliente y Tecnico.

---

## CAMBIO-0028 - Correcciones de acceso horizontal en avisos y partes

Fecha: 2026-07-13
Agente: Codex / agente backend-auditor EasyParte
Rama: security/clientes-role-check
Tipo de cambio: seguridad/backend/documentacion
Resumen: Se corrigen y validan dos fallos de autorizacion horizontal. `ParteTrabajoController::isTecnico()` reconoce el rol real `Tecnico` y reactiva los controles de partes propios. `AvisoController::update()` comprueba la asignacion actual antes de procesar el payload y bloquea la apropiacion de avisos ajenos.
Archivos de codigo incluidos en el commit `0e2fa38`:
- `backend/controllers/ParteTrabajoController.php`
- `backend/controllers/AvisoController.php`

Pruebas realizadas:
- Partes: TEC-PAR-01, TEC-PAR-03, TEC-PAR-04, TEC-PAR-05, TEC-PAR-07 y TEC-PAR-08 correctas con login/JWT reales y comprobacion de base de datos.
- Avisos: AVI-TEC-01 a AVI-TEC-06 correctas; los rechazos 403 no producen cambios parciales.
- Regresion: Administrador y Atencion al Cliente conservan la edicion autorizada de avisos; Atencion al Cliente recibe 403 al crear o editar partes.
- Sintaxis PHP y `git diff --check` correctos.

Incidencias: INC-0016 e INC-0017 quedan resueltas y validadas.
Riesgos: Falta prueba real con segunda empresa; normalizacion de roles duplicada; pruebas de permisos manuales; decision pendiente sobre liberar un aviso propio; fixtures pendientes de limpieza controlada.
Estado: validado
Siguiente paso: Automatizar la regresion de permisos o preparar una segunda empresa de pruebas segura en una tarea separada.

---

## CAMBIO-0029 - Registro final de pruebas manuales de partes/albaranes

Fecha: 2026-07-23
Agente: Codex / agente backend-auditor documental EasyParte
Rama: security/clientes-role-check
Tipo de cambio: documentacion/testing
Resumen: Se registran como correctas las pruebas manuales de permisos minimos de partes/albaranes ejecutadas por la persona responsable del proyecto para `Administrador`, `Atencion al Cliente` y `Tecnico`, junto con la regresion funcional de login, dashboard, avisos, clientes y partes.
Archivos modificados:
- `docs/testing/partes_permisos.md`
- `docs/seguimiento/pendientes.md`
- `docs/seguimiento/registro_cambios_agentes.md`

Resultados registrados:
- `Administrador`: GET, POST y PUT de partes correctos.
- `Atencion al Cliente`: GET correcto; POST y PUT bloqueados con 403.
- `Tecnico`: GET limitado a partes propios; POST propio correcto; POST con otro empleado o aviso ajeno bloqueado con 403; POST sobre aviso propio correcto; PUT propio correcto; PUT ajeno y reasignacion bloqueados con 403.
- Regresion: login, dashboard, avisos, clientes y partes siguen funcionando.

Motivo: Cerrar el desfase entre las pruebas ya ejecutadas y las filas que todavia figuraban como pendientes en la matriz documental.
Pruebas realizadas por Codex: revision de consistencia documental y validacion del diff; Codex no repite las pruebas manuales ni registra tokens o contrasenas.
Riesgos: PEN-0005 permanece parcial porque no existe todavia autorizacion centralizada completa. INC-0014 permanece abierta porque el cierre formal con firma, hash, bloqueo y rectificacion queda fuera de este alcance.
Estado: documentacion validada
Siguiente paso: Continuar PEN-0005 en una tarea separada sin cerrar INC-0014 hasta implementar y validar el cierre formal.

---

## CAMBIO-0030 - Documentación y pruebas de reasignación auditada de avisos

Fecha: 2026-07-27
Agente: Codex / agente documentación-auditor EasyParte
Rama: security/clientes-role-check
Tipo de cambio: documentación/testing
Resumen: Se registra la validación manual del flujo separado de edición,
asignación, reasignación, toma y cancelación de avisos. Se documenta el primer
alcance persistente de auditoría para asignaciones sin presentar PEN-0009 como
implementado.

Archivos modificados:

- `docs/contexto/reglas_de_negocio.md`
- `docs/contexto/matriz_permisos_backend.md`
- `docs/contexto/endpoints_api.md`
- `docs/arquitectura/trazabilidad_y_auditoria.md`
- `docs/testing/avisos_reasignacion.md`
- `docs/seguimiento/incidencias.md`
- `docs/seguimiento/pendientes.md`
- `docs/seguimiento/registro_cambios_agentes.md`

Resultados manuales aportados:

- La migración de `auditoria_evento` fue aplicada en local tras backup y
  prueba.
- Técnico puede reasignar un aviso propio y la operación deja evento
  persistente.
- Técnico no puede reasignar avisos ajenos o cancelados ni reasignarse a sí
  mismo.
- Administrador y Atención al Cliente pueden asignar o reasignar avisos de su
  empresa.
- El `PUT` general conserva `id_empleado`.
- Coger y cancelar siguen funcionando mediante endpoints específicos.
- El flujo no realiza borrado físico.

Motivo: Alinear reglas, permisos, contratos y seguimiento con el estado
validado de AVISOS-REASIGNACION-AUDITADA, manteniendo claramente separado el
avance parcial de auditoría de la cobertura general todavía pendiente.

Pruebas realizadas: Las pruebas funcionales y de base de datos fueron
ejecutadas manualmente por la persona responsable; Codex solo registra los
resultados aportados. Codex realizó revisión de coherencia documental,
comprobación de rutas y nombres de eventos en modo solo lectura y validación del
diff. No se ejecutó SQL y no se documentaron tokens, contraseñas ni secretos.

Estados conservados o actualizados:

- INC-0010 permanece `resuelta`.
- INC-0013 permanece `abierta`.
- INC-0017 permanece `resuelta y validada`.
- PEN-0009 pasa de `pendiente` a `parcial`; no se marca como `implementado`.

Riesgos: Falta validar por API la reasignación de avisos finalizados, repetir la
matriz con una segunda empresa, automatizar pruebas y extender auditoría al
resto de acciones críticas.

Estado: validado
Siguiente paso: Abordar la cobertura restante de PEN-0009 en una tarea separada
sin cerrar INC-0013 hasta disponer de auditoría general validada.

---

## CAMBIO-0031 - Preparación de fixtures para avisos multiempresa

Fecha: 2026-07-28
Agente: Codex / agente testing-base de datos-documentación EasyParte
Rama: master
Tipo de cambio: testing/documentación/fixture local
Resumen: Se prepara un seed dedicado y una matriz manual para validar el
aislamiento de avisos, empleados asignables y eventos de reasignación auditada
entre Empresa A y Empresa B. La tarea solo prepara artefactos; no ejecuta SQL ni
registra resultados como superados.

Archivos creados:

- `bbdd/seed_avisos_reasignacion_multiempresa_pruebas.sql`
- `docs/testing/avisos_reasignacion_multiempresa.md`

Archivos actualizados:

- `docs/testing/datos_prueba_multiempresa.md`
- `docs/seguimiento/registro_cambios_agentes.md`

Contenido preparado:

- IDs de filas de fixture reservados entre `9201` y `9272`.
- Dos empresas y departamentos.
- Dos empleados activos por empresa.
- Administrador y Técnico por empresa.
- Técnico A vinculado a `9211` y Técnico B vinculado a `9221`.
- Clientes y avisos específicos para pruebas positivas y negativas.
- Preflight de colisiones y dependencias que evita inserciones parciales.
- Hash bcrypt técnico/local sin contraseña en claro.
- Rollback transaccional en orden de claves foráneas, empezando por
  `auditoria_evento`.
- Baseline de auditoría, matriz por rol, consultas de cruce y criterios de
  éxito.

Motivo: Permitir una prueba multiempresa reproducible de
AVISOS-REASIGNACION-AUDITADA sin modificar backend, frontend, endpoints,
migraciones, dump principal ni el seed multiempresa anterior.

Pruebas realizadas: Revisión estática del esquema y las claves foráneas,
revisión estática del SQL del fixture y `git diff --check`. No se ejecutó SQL,
no se aplicó el seed y no se documentaron tokens, contraseñas ni secretos.

Estados conservados:

- INC-0013 permanece `abierta`.
- PEN-0006 permanece `parcial`.
- PEN-0009 permanece `parcial`.

Riesgos: La preparación no acredita aislamiento real hasta ejecutar la matriz
en local. Si existen colisiones, faltan los roles base o no está aplicada
`auditoria_evento`, el fixture no debe importarse. La consulta de auditoría
continúa siendo SQL local hasta disponer de un endpoint protegido y filtrado
por empresa.

Estado: pendiente de ejecución manual
Siguiente paso: Realizar backup local, aplicar el seed manualmente, ejecutar
primero las pruebas negativas y después las positivas, registrar resultados y
usar el rollback exacto al finalizar.
