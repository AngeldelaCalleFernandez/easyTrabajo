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
