# Pendientes - EasyParte

Auditoria inicial realizada el 2026-05-20 en modo solo lectura.

Tipos permitidos:

```txt
tarea
bug
mejora
documentacion
seguridad
testing
despliegue
decision
```

Estados permitidos:

```txt
pendiente
implementado
parcial
dudoso
bloqueado
```

## PEN-0001 - Realizar auditoria inicial con Codex

Tipo: tarea
Prioridad: alta
Estado: implementado
Relacionado con: `implementado_backend.md`, `implementado_frontend.md`, `incidencias.md`
Detectado por: preparacion inicial
Fecha: 2026-05-20

### Descripcion

Revisar el repositorio real `easyTrabajo` antes de modificar codigo.

### Motivo

No se debe profesionalizar ni refactorizar sin saber primero que esta implementado realmente.

### Archivos o zonas afectadas

- `backend/`
- `frontend/`
- `bbdd/`
- `AGENT/seguimiento/`

### Criterios de aceptacion

- [x] `implementado_backend.md` actualizado.
- [x] `implementado_frontend.md` actualizado.
- [x] Incidencias reales registradas.
- [x] Pendientes tecnicos detectados.
- [x] No se modifica codigo de aplicacion.

### Riesgos

Bajo. Solo documentacion de seguimiento.

### Siguiente paso

Revisar esta auditoria y priorizar tareas de seguridad.

---

## PEN-0002 - Confirmar estrategia de ramas

Tipo: decision
Prioridad: alta
Estado: parcial
Relacionado con: Git / repositorio
Detectado por: preparacion inicial
Fecha: 2026-05-20

### Descripcion

Confirmar flujo `main`, `develop` y ramas por tarea.

### Motivo

Evitar cambios directamente sobre `main`.

### Archivos o zonas afectadas

- Repositorio Git.
- `AGENT/repositorio/estrategia_ramas.md`

### Criterios de aceptacion

- [ ] Estrategia confirmada por la persona responsable.
- [ ] Ramas remotas revisadas.

### Riesgos

Trabajar sin ramas claras puede mezclar cambios.

### Siguiente paso

Confirmar si las siguientes tareas salen desde `develop`.

---

## PEN-0003 - Crear rama `develop` si no existe

Tipo: tarea
Prioridad: alta
Estado: implementado
Relacionado con: Git / repositorio
Detectado por: auditoria inicial
Fecha: 2026-05-20

### Descripcion

Comprobar si existe rama `develop`.

### Motivo

`develop` debe ser rama de integracion.

### Archivos o zonas afectadas

- Repositorio Git.

### Criterios de aceptacion

- [x] La rama actual detectada es `develop`.

### Riesgos

No se ha revisado estado remoto ni sincronizacion.

### Siguiente paso

Antes de cambios de codigo, revisar estado remoto y crear rama especifica.

---

## PEN-0004 - Corregir configuracion de seguridad basica de backend

Tipo: seguridad
Prioridad: critica
Estado: implementado
Relacionado con: INC-0001, INC-0002, INC-0003, INC-0004
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

Separar configuracion por entorno: CORS, credenciales DB, errores internos y secreto JWT.

### Motivo

El estado actual es local/demo y no apto para produccion.

### Archivos o zonas afectadas

- `backend/config/cors.php`
- `backend/config/database.php`
- `backend/helpers/jwt.php`

### Criterios de aceptacion

- [x] CORS restringible por entorno.
- [x] Credenciales configurables por entorno.
- [x] Sin usuario DB root en configuracion de produccion.
- [x] Sin exposicion de mensajes PDO al cliente.
- [x] `JWT_SECRET` obligatorio fuera de entorno local.
- [x] VAL-CONFIG-009 repetida tras loader `.env`.
- [x] Validacion manual completa en XAMPP documentada en `AGENT/testing/validacion_configuracion_seguridad.md`.

### Variables de entorno esperadas

- `APP_ENV=local|development|testing|staging|production`
- `DB_HOST`
- `DB_PORT`
- `DB_NAME` o `DB_DATABASE`
- `DB_USER` o `DB_USERNAME`
- `DB_PASSWORD`
- `DB_CHARSET`
- `JWT_SECRET`
- `CORS_ALLOWED_ORIGINS`
- `CORS_ALLOWED_METHODS`
- `CORS_ALLOWED_HEADERS`
- `CORS_ALLOW_CREDENTIALS`

### Carga de variables

- `backend/config/env.php` carga `.env` desde la raiz del repositorio y desde `backend/.env` si existen.
- `.env.example` documenta las variables esperadas sin secretos reales.
- `.env` y `backend/.env` quedan ignorados por Git.
- `backend/public/.htaccess` no debe usarse como fuente principal de secretos ni de entorno.
- En `APP_ENV=production`, una prueba de login completa debe usar `DB_USER` no root; root queda bloqueado intencionadamente fuera de local/desarrollo.

### Riesgos

Riesgo residual bajo. La fase de configuracion base queda implementada, pero despliegues reales deben crear `.env` con secretos propios y usuario MySQL no root.

### Siguiente paso

Mantener JWT Bearer por compatibilidad actual. Evaluar cookie HttpOnly en una tarea futura separada, sin mezclarla con el cierre de configuracion base.

---

## PEN-0005 - Implementar autorizacion backend centralizada

Tipo: seguridad
Prioridad: critica
Estado: parcial
Relacionado con: INC-0005, INC-0006, INC-0008
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

Crear validacion centralizada de rol y permisos por endpoint.

### Motivo

Actualmente solo hay `AuthMiddleware` y comprobaciones manuales parciales.

### Archivos o zonas afectadas

- `backend/middleware/`
- `backend/routes/api.php`
- `backend/controllers/`

### Criterios de aceptacion

- [ ] `RoleMiddleware` o servicio equivalente.
- [x] Matriz minima de permisos backend documentada sobre roles actuales.
- [x] Primera comprobacion minima de roles aplicada en `/api/clientes`.
- [x] Pruebas manuales de permisos de clientes documentadas.
- [x] Primera cancelacion segura de avisos implementada sin borrado fisico.
- [x] Ajuste backend para que `Tecnico` pueda listar clientes sin crear/editar/baja.
- [x] Ajuste backend para que `Tecnico` pueda crear avisos libres o asignados a si mismo, nunca a otro tecnico.
- [x] Ajuste backend para que `Atencion al Cliente` y `Tecnico` puedan entrar por `POST /api/avisos` segun la regla vigente.
- [x] Ajuste backend para que `Atencion al Cliente` pueda listar empleados activos de su empresa como solo lectura para asignar avisos.
- [ ] Pruebas manuales de cancelacion de avisos documentadas como correctas.
- [ ] Revalidar permisos de clientes tras decision de lectura para `Tecnico`.
- [ ] Revalidar visibilidad y creacion de avisos por rol.
- [x] Ajustar template de avisos para mostrar boton Cancelar segun `puedeCancelarAviso(tarea)`.
- [ ] Validar manualmente boton Cancelar por rol y aviso asignado.
- [ ] Revalidar selector de empleados en avisos para `Atencion al Cliente`.
- [ ] Revalidar que `Atencion al Cliente` no puede crear, editar ni dar de baja empleados.
- [x] Permisos minimos backend aplicados en `/api/partes` para bloquear escritura de `Atencion al Cliente` y limitar `Tecnico` a partes propios.
- [x] Pruebas manuales de permisos de partes/albaranes documentadas como correctas para propiedad, creacion y reasignacion de `Tecnico`.
- [x] Regresion manual de login, dashboard, avisos, clientes y partes registrada como correcta.
- [x] Corregida y validada la deteccion del rol real `Tecnico` en permisos de partes (`0e2fa38`).
- [x] Corregido y validado el bypass de apropiacion de avisos ajenos mediante `PUT` (`0e2fa38`).
- [ ] Endpoints restantes documentan roles permitidos en codigo/rutas.
- [ ] Clientes, avisos, partes y administracion validan permisos en backend.
- [ ] Pruebas negativas por rol en los modulos restantes.

### Riesgos

Alto. Puede cambiar acceso a modulos existentes.

### Siguiente paso

PEN-0005 sigue parcial porque falta autorizacion centralizada y validacion completa
de endpoints restantes. Continuan pendientes una prueba real con segunda empresa,
eliminar la normalizacion duplicada de roles, automatizar las pruebas, decidir si
un tecnico puede dejar libre un aviso propio y limpiar de forma controlada los
fixtures identificados de prueba. INC-0014 permanece abierta: el cierre formal
con firma, hash, bloqueo y rectificacion no forma parte de esta validacion.

---

## PEN-0006 - Implementar tenant/multiempresa real

Tipo: seguridad
Prioridad: critica
Estado: parcial
Relacionado con: INC-0007, INC-0009, INC-0011
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

Centralizar resolucion de empresa activa y validar que todos los IDs relacionados pertenecen a esa empresa.

### Motivo

Hay filtrado parcial por `id_empresa`, pero no hay `TenantMiddleware` ni modelo multiempresa objetivo.

### Archivos o zonas afectadas

- `backend/controllers/EmpleadoController.php`
- `backend/controllers/AvisoController.php`
- `backend/controllers/ParteTrabajoController.php`
- `frontend/src/app/features/avisos/avisos.ts`
- `frontend/src/app/features/administracion/administracion.ts`
- `bbdd/export_base_datos.sql`

### Criterios de aceptacion

- [x] `GET /api/empleados` filtra por empresa.
- [x] Crear/editar avisos valida cliente, empleado y departamento de empresa.
- [x] Crear/editar partes valida cliente, tarea y empleado de empresa.
- [x] Frontend no envia `id_empresa: 1` en avisos ni administracion.
- [x] Datos de prueba multiempresa preparados.
- [ ] Prueba multiempresa positiva y negativa.
- [ ] TenantMiddleware centralizado.
- [ ] Modelo multiempresa completo.

### Avance aplicado

Fase minima `security/tenant-minimo`:

- Se filtra `GET /api/empleados` por `id_empresa` del token.
- Se validan IDs relacionados en creacion/edicion de avisos y partes.
- Se eliminan `id_empresa: 1` de formularios frontend de avisos y administracion.
- Se documentan pruebas manuales en `AGENT/testing/tenant_minimo.md`.
- Se prepara guia de datos en `AGENT/testing/datos_prueba_multiempresa.md`.
- Se crea seed opcional no destructivo en `bbdd/seed_multiempresa_pruebas.sql`.
- Las pruebas manuales TENANT-MIN-001 a TENANT-MIN-005 quedan registradas como `correcto`.

### Riesgos

Critico por separacion de datos.

### Siguiente paso

Ejecutar pruebas multiempresa manuales y disenar `TenantMiddleware`/contexto centralizado en una fase posterior.

---

## PEN-0007 - Migrar modelo de usuarios a roles por empresa

Tipo: tarea
Prioridad: alta
Estado: pendiente
Relacionado con: INC-0005
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

Evolucionar de `usuario.id_empresa` y `usuario_rol` a `empresa_usuario` y `empresa_usuario_rol`.

### Motivo

La documentacion objetivo exige usuarios multiempresa y roles por empresa.

### Archivos o zonas afectadas

- `bbdd/export_base_datos.sql`
- `backend/models/Usuario.php`
- `backend/controllers/AuthController.php`
- `backend/controllers/UsuarioController.php`
- `frontend/src/app/core/interfaces/auth.interfaces.ts`

### Criterios de aceptacion

- [ ] Tablas objetivo creadas mediante migracion revisada.
- [ ] Login devuelve empresas disponibles/empresa activa.
- [ ] Roles se resuelven por empresa.
- [ ] Bloqueo por empresa previsto.

### Riesgos

Alto. Requiere migracion de datos y revision humana.

### Siguiente paso

Preparar propuesta tecnica y migracion no destructiva.

---

## PEN-0008 - Implementar modelo SaaS de planes y suscripciones

Tipo: tarea
Prioridad: alta
Estado: pendiente
Relacionado con: modelo SaaS
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

Crear soporte real para planes, suscripciones, estados y limites.

### Motivo

No existen tablas `plan` ni `suscripcion`, ni middleware de limites.

### Archivos o zonas afectadas

- `bbdd/`
- `backend/`
- `frontend/src/app/`

### Criterios de aceptacion

- [ ] Tablas `plan` y `suscripcion`.
- [ ] Endpoints de plan/suscripcion.
- [ ] Limites de usuarios, clientes, avisos y partes en backend.
- [ ] Vista frontend de suscripcion si entra en alcance.

### Riesgos

Alto. Afecta flujo de creacion de recursos.

### Siguiente paso

Cerrar decisiones de plan Free/precios antes de implementar.

---

## PEN-0009 - Implementar auditoria y trazabilidad

Tipo: seguridad
Prioridad: critica
Estado: parcial
Relacionado con: INC-0013
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

Extender `auditoria_evento` y `AuditLogger` desde el primer alcance aplicado a
avisos hasta cubrir todas las acciones criticas.

### Motivo

La asignacion de avisos ya dispone de trazabilidad persistente, pero siguen sin
cobertura completa las altas, bajas, cambios de rol, cierres, cancelaciones y
otras acciones criticas.

### Archivos o zonas afectadas

- `bbdd/`
- `backend/services/` o equivalente
- `backend/controllers/`

### Criterios de aceptacion

- [x] Migracion minima de `auditoria_evento` aplicada en local tras backup y prueba.
- [x] Eventos de asignacion de avisos registran usuario, empresa, entidad, entidad_id, accion y fecha.
- [x] Asignacion y reasignacion guardan valores anteriores/nuevos de `id_empleado`.
- [x] No se registraron ni documentaron secretos o passwords en esta validacion.
- [x] Reasignacion de aviso validada manualmente con evento persistente.
- [ ] Endpoints criticos generan eventos.
- [ ] Auditoria aislada por empresa validada con una segunda empresa.
- [ ] Pruebas de auditoria automatizadas.

### Avance aplicado

Fase `AVISOS-REASIGNACION-AUDITADA`:

- La migracion `bbdd/migrations/20260723_crear_auditoria_evento.sql` fue
  aplicada en local por la persona responsable después de backup y prueba.
- `AuditLogger` registra la asignacion, reasignacion y toma de avisos.
- Los eventos del alcance son `aviso_asignado`, `aviso_reasignado` y
  `aviso_autoasignado`.
- La reasignacion auditada fue validada manualmente y queda registrada en
  `docs/testing/avisos_reasignacion.md`.
- INC-0013 permanece abierta porque la infraestructura todavía no cubre todas
  las acciones críticas.

### Riesgos

Alto. La existencia de eventos para avisos no debe interpretarse como una
auditoria completa del sistema.

### Siguiente paso

Extender `AuditLogger` de forma controlada a las demás acciones críticas,
definir consulta/protección de eventos, ejecutar la prueba con una segunda
empresa y automatizar la regresión. PEN-0009 no debe pasar a `implementado`
hasta completar y validar esa cobertura.

---

## PEN-0010 - Profesionalizar partes: empleados multiples, horas, firma, hash y rectificacion

Tipo: tarea
Prioridad: alta
Estado: pendiente
Relacionado con: INC-0014
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

Evolucionar partes desde campos directos a modelo objetivo con `parte_empleado`, `parte_hora`, `parte_material`, `parte_firma`, `parte_hash` y `parte_rectificacion`.

### Motivo

El modulo actual no cumple integridad, firma ni trazabilidad.

### Archivos o zonas afectadas

- `bbdd/export_base_datos.sql`
- `backend/controllers/ParteTrabajoController.php`
- `frontend/src/app/features/albaranes/`
- `frontend/src/app/core/services/partes.service.ts`

### Criterios de aceptacion

- [ ] Parte puede tener varios empleados.
- [ ] Horas normalizadas.
- [ ] Firma guardada con hash.
- [ ] Cierre genera hash de integridad.
- [ ] Rectificacion de 7 dias prevista.
- [ ] Parte facturado bloqueado.

### Riesgos

Alto. Cambio de modelo de datos y flujo critico.

### Siguiente paso

Dividir en subtareas: modelo, API, frontend, pruebas.

---

## PEN-0011 - Implementar presupuestos basicos

Tipo: tarea
Prioridad: alta
Estado: pendiente
Relacionado con: alcance inicial
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

Crear modulo de presupuestos con lineas, estados y vinculacion a cliente/aviso.

### Motivo

Presupuestos esta en alcance inicial, pero no existe en SQL, backend ni frontend.

### Archivos o zonas afectadas

- `bbdd/`
- `backend/`
- `frontend/src/app/features/`

### Criterios de aceptacion

- [ ] Tablas de presupuesto.
- [ ] Endpoints CRUD basicos.
- [ ] Vista frontend.
- [ ] Estados definidos.
- [ ] Preparacion para conversion/exportacion.

### Riesgos

Medio-alto. Nuevo modulo conectado con clientes, avisos y partes.

### Siguiente paso

Implementar despues de seguridad/tenant.

---

## PEN-0012 - Centralizar configuracion de API en frontend

Tipo: mejora
Prioridad: media
Estado: implementado
Relacionado con: INC-0012
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

Eliminar URLs hardcodeadas y duplicadas en servicios Angular.

### Motivo

Facilitar despliegue y evitar inconsistencias.

### Archivos o zonas afectadas

- `frontend/src/app/core/services/*.ts`
- Configuracion Angular de entornos.

### Criterios de aceptacion

- [x] API URL definida en un unico sitio.
- [x] Servicios consumen configuracion comun.
- [x] Build local sigue funcionando.
- [x] Pruebas manuales de login, dashboard, clientes, avisos, partes/albaranes y administracion documentadas.

### Avance aplicado

Fase `INC-0012`:

- Se crea `frontend/src/app/core/config/api.config.ts` con `API_BASE_URL`.
- Se actualizan los servicios Angular principales para consumir la configuracion comun.
- La URL local XAMPP se mantiene sin cambios.
- `npm run build` finaliza correctamente tras repetir fuera del sandbox por `spawn EPERM`.
- Las pruebas manuales de login, dashboard, clientes, avisos, partes/albaranes y administracion fueron ejecutadas por la persona responsable del proyecto y registradas como correctas en `docs/testing/frontend_api_url.md`.

### Riesgos

Bajo. La URL sigue centralizada en un unico punto; futuros despliegues deberan ajustar la configuracion de API cuando exista una configuracion productiva formal.

### Siguiente paso

Mantener `API_BASE_URL` como punto unico de configuracion hasta definir una estrategia formal de entornos frontend para produccion.

---

## PEN-0013 - Reducir `any` y mejorar tipado de respuestas frontend

Tipo: mejora
Prioridad: media
Estado: pendiente
Relacionado con: frontend
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

Sustituir `any` en servicios/componentes por interfaces de respuesta.

### Motivo

Mejorar mantenibilidad y detectar errores de contrato API.

### Archivos o zonas afectadas

- `frontend/src/app/core/services/`
- `frontend/src/app/features/`
- `frontend/src/app/core/interfaces/`

### Criterios de aceptacion

- [ ] Respuestas de login, cliente, usuario, aviso y parte tipadas.
- [ ] Componentes principales sin `any` innecesario.
- [ ] Build TypeScript correcto.

### Riesgos

Bajo.

### Siguiente paso

Abordar tras estabilizar contratos API.

---

## PEN-0014 - Crear ErrorInterceptor y homogeneizar errores frontend

Tipo: mejora
Prioridad: media
Estado: pendiente
Relacionado con: alertas/modales
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

Gestionar 401/403/500 y errores de API en un interceptor o servicio comun.

### Motivo

Ahora hay `console.error`, mensajes genericos y `alert()` en albaranes.

### Archivos o zonas afectadas

- `frontend/src/app/core/interceptors/`
- `frontend/src/app/core/services/alert.service.ts`
- `frontend/src/app/features/albaranes/albaranes.ts`

### Criterios de aceptacion

- [ ] 401 redirige o limpia sesion.
- [ ] 403 muestra mensaje claro.
- [ ] 500 no expone detalle tecnico.
- [ ] Sin `alert()` nativos en flujos principales.

### Riesgos

Medio. Puede afectar UX global.

### Siguiente paso

Crear tarea frontend separada.

---

## PEN-0015 - Separar schema SQL y datos demo

Tipo: despliegue
Prioridad: media
Estado: pendiente
Relacionado con: INC-0015
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

Separar estructura de base de datos, seeds demo y datos reales.

### Motivo

El dump actual contiene usuarios y datos demo.

### Archivos o zonas afectadas

- `bbdd/export_base_datos.sql`

### Criterios de aceptacion

- [ ] Script de schema limpio.
- [ ] Script seed local/demo separado.
- [ ] Documentacion de importacion local.
- [ ] Advertencia de no usar seeds en produccion.

### Riesgos

Medio. Requiere cuidado para no perder datos.

### Siguiente paso

Hacer backup y proponer estructura de scripts antes de modificar SQL.

---

## PEN-0016 - Crear pruebas minimas backend y frontend

Tipo: testing
Prioridad: alta
Estado: pendiente
Relacionado con: auditoria inicial
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

Definir pruebas minimas de login, permisos, tenant, clientes, avisos, partes y dashboard.

### Motivo

No se detectaron pruebas automatizadas especificas para flujos criticos.

### Archivos o zonas afectadas

- `backend/`
- `frontend/src/app/`
- `AGENT/testing/`

### Criterios de aceptacion

- [ ] Casos manuales por rol.
- [ ] Casos multiempresa.
- [ ] Smoke test frontend.
- [ ] Pruebas de endpoints criticos.

### Riesgos

Medio. Sin pruebas, las correcciones de seguridad pueden romper flujos.

### Siguiente paso

Crear checklist de pruebas ejecutable antes de refactors.
