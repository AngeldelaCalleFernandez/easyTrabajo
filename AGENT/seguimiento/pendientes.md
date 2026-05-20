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
Estado: pendiente
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

- [ ] CORS restringible por entorno.
- [ ] Credenciales fuera del codigo.
- [ ] Sin usuario DB root en configuracion de produccion.
- [ ] Sin exposicion de mensajes PDO al cliente.
- [ ] `JWT_SECRET` obligatorio fuera de entorno local.

### Riesgos

Afecta login, API y despliegue.

### Siguiente paso

Crear rama `security/configuracion-entornos`.

---

## PEN-0005 - Implementar autorizacion backend centralizada

Tipo: seguridad
Prioridad: critica
Estado: pendiente
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
- [ ] Endpoints documentan roles permitidos.
- [ ] Clientes, avisos, partes y administracion validan permisos en backend.
- [ ] Pruebas negativas por rol.

### Riesgos

Alto. Puede cambiar acceso a modulos existentes.

### Siguiente paso

Disenar matriz minima sobre roles actuales antes de migrar a roles objetivo.

---

## PEN-0006 - Implementar tenant/multiempresa real

Tipo: seguridad
Prioridad: critica
Estado: pendiente
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

- [ ] `GET /api/empleados` filtra por empresa.
- [ ] Crear/editar avisos valida cliente y empleado de empresa.
- [ ] Crear/editar partes valida cliente, tarea y empleado de empresa.
- [ ] Frontend no envia `id_empresa: 1`.
- [ ] Prueba multiempresa positiva y negativa.

### Riesgos

Critico por separacion de datos.

### Siguiente paso

Corregir primero fugas directas sin cambiar aun todo el modelo de datos.

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
Estado: pendiente
Relacionado con: INC-0013
Detectado por: Codex auditor tecnico
Fecha: 2026-05-20

### Descripcion

Crear `auditoria_evento` y un `AuditLogger` para acciones criticas.

### Motivo

No hay trazabilidad real de altas, bajas, cambios de rol, cierres ni cancelaciones.

### Archivos o zonas afectadas

- `bbdd/`
- `backend/services/` o equivalente
- `backend/controllers/`

### Criterios de aceptacion

- [ ] Eventos registran usuario, empresa, entidad, entidad_id, accion y fecha.
- [ ] Valores anteriores/nuevos cuando proceda.
- [ ] No se registran secretos ni passwords.
- [ ] Endpoints criticos generan eventos.

### Riesgos

Alto. Afecta seguridad y cumplimiento.

### Siguiente paso

Disenar tabla y servicio antes de conectar controladores.

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
Estado: pendiente
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

- [ ] API URL definida en un unico sitio.
- [ ] Servicios consumen configuracion comun.
- [ ] Build local sigue funcionando.

### Riesgos

Bajo-medio. Puede romper llamadas HTTP si se configura mal.

### Siguiente paso

Crear servicio/configuracion `environment` o constante central.

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
