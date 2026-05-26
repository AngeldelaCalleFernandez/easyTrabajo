# Roadmap técnico — EasyParte

## 1. Propósito del documento

Este documento sirve para ordenar el trabajo técnico de profesionalización de EasyParte dentro del repositorio `easyTrabajo`.

No es un listado cerrado de funcionalidades. Es una guía viva para que Codex o cualquier agente pueda:

- detectar tareas técnicas;
- clasificarlas por fase;
- dividirlas en cambios pequeños;
- evitar modificar el proyecto sin control;
- dejar constancia de lo que falta, lo que está bloqueado y lo que requiere revisión humana.

Este documento no sustituye a `pendientes.md`, `incidencias.md` ni a los documentos de arquitectura. Sirve como visión técnica general.

---

## 2. Reglas de uso

1. Antes de añadir una tarea, el agente debe revisar la documentación del proyecto.
2. No se debe marcar una tarea como completada si no se ha revisado el código y probado el cambio.
3. Las tareas deben ser pequeñas y revisables.
4. No se deben mezclar cambios de backend, frontend, base de datos y despliegue en una única tarea grande.
5. Si una tarea afecta a seguridad, permisos, empresas, base de datos, auditoría o pérdida de datos, debe requerir revisión humana.
6. Si falta una decisión de negocio, debe registrarse como duda o pendiente, no inventarse.
7. Cada tarea técnica debe indicar fase, estado, riesgo y siguiente paso.
8. Este documento debe actualizarse después de cada auditoría o bloque de trabajo relevante.

Estados permitidos:

```txt
pendiente
implementado
parcial
dudoso
bloqueado
```

---

## 3. Fases técnicas iniciales

## Fase 1 — Auditoría inicial

Objetivo: conocer el estado real del repositorio antes de modificar código.

Tareas esperadas:

- revisar estructura real de `backend/`, `frontend/` y `bbdd/`;
- detectar controladores, rutas, servicios, modelos y helpers existentes;
- detectar componentes, servicios, guards, interceptors y rutas Angular;
- comparar el código real con la documentación objetivo;
- identificar hardcodes, errores expuestos, permisos débiles y duplicidades;
- completar `implementado_backend.md`;
- completar `implementado_frontend.md`;
- registrar incidencias iniciales.

Estado inicial: pendiente.

---

## Fase 2 — Seguridad base

Objetivo: corregir los riesgos más importantes antes de ampliar funcionalidades.

Tareas posibles:

- revisar autenticación y validación de token;
- revisar almacenamiento y expiración de sesión;
- revisar si el backend valida roles realmente;
- revisar filtrado por empresa;
- evitar confiar en `id_empresa` enviado desde frontend;
- ocultar errores internos y registrar detalle en logs;
- preparar variables de entorno;
- revisar CORS para desarrollo y producción;
- detectar endpoints sin middleware.

Estado inicial: pendiente.

---

## Fase 3 — Refactor backend

Objetivo: ordenar el backend sin romper la funcionalidad existente.

Tareas posibles:

- separar controladores, servicios y repositorios;
- centralizar respuestas JSON;
- centralizar validaciones;
- reducir SQL directo en controladores;
- mejorar manejo de errores;
- ordenar rutas;
- revisar nombres inconsistentes;
- documentar endpoints reales.

Estado inicial: pendiente.

---

## Fase 4 — Refactor frontend

Objetivo: ordenar Angular y mejorar mantenibilidad sin cambiar el comportamiento principal.

Tareas posibles:

- revisar estructura `core`, `shared`, `layouts` y `features`;
- centralizar URL de API en environments;
- revisar guards e interceptors;
- reducir usos de `any`;
- mejorar manejo global de errores;
- unificar modales, alertas, botones y tablas;
- revisar responsive;
- mantener estilo visual EasyParte.

Estado inicial: pendiente.

---

## Fase 5 — Modelo SaaS

Objetivo: preparar planes, suscripciones y límites de uso.

Tareas posibles:

- definir tablas reales o migraciones objetivo;
- preparar modelo de plan;
- preparar modelo de suscripción;
- validar límites desde backend;
- mostrar uso del plan en frontend;
- bloquear acciones cuando se superen límites;
- auditar cambios de plan.

Estado inicial: pendiente.

---

## Fase 6 — Usuarios multiempresa y roles

Objetivo: permitir que un usuario pueda pertenecer a varias empresas con roles distintos.

Tareas posibles:

- revisar modelo actual `usuario`, `empresa`, `rol`, `usuario_rol`;
- diseñar o implementar `empresa_usuario`;
- diseñar o implementar roles por empresa;
- revisar bloqueo por empresa;
- revisar permisos por endpoint;
- evitar permisos solo visuales.

Estado inicial: pendiente.

---

## Fase 7 — Presupuestos básicos

Objetivo: añadir presupuestos como módulo del núcleo de EasyParte.

Tareas posibles:

- definir entidad presupuesto;
- definir líneas de presupuesto;
- definir estados;
- vincular presupuesto con cliente y aviso;
- preparar aceptación/rechazo;
- preparar conversión futura en trabajo;
- preparar exportación futura.

Estado inicial: pendiente.

---

## Fase 8 — Partes, firma, hash y auditoría

Objetivo: profesionalizar partes/albaranes con trazabilidad e integridad.

Tareas posibles:

- revisar parte actual;
- permitir varios empleados por parte si procede;
- registrar horas por empleado;
- registrar materiales;
- registrar firma;
- guardar hash de firma;
- generar hash de integridad al cerrar;
- permitir rectificación dentro de reglas;
- auditar cierre, firma, rectificación y facturación.

Estado inicial: pendiente.

---

## Fase 9 — Testing y preproducción

Objetivo: comprobar que el sistema funciona antes de despliegue.

Tareas posibles:

- definir pruebas manuales por módulo;
- crear pruebas backend;
- crear pruebas frontend;
- revisar login, permisos y roles;
- revisar errores de consola;
- revisar build Angular;
- revisar endpoints principales;
- completar checklist preproducción.

Estado inicial: pendiente.

---

## Fase 10 — Despliegue

Objetivo: preparar EasyParte para entorno real.

Tareas posibles:

- definir hosting;
- definir dominio;
- configurar HTTPS;
- separar entorno desarrollo/producción;
- configurar variables de entorno;
- cerrar CORS;
- preparar backups;
- preparar logs;
- documentar despliegue;
- revisar estrategia CI/CD si procede.

Estado inicial: pendiente.

---

## 4. Formato obligatorio para nuevas tareas detectadas por Codex

Cada tarea nueva debe añadirse con este formato:

```md
## TAREA-XXXX — Título breve

Fase:
Tipo: seguridad / backend / frontend / base_datos / testing / despliegue / documentación
Estado: pendiente / implementado / parcial / dudoso / bloqueado
Prioridad: crítica / alta / media / baja

### Descripción

Explicar de forma concreta qué hay que hacer.

### Motivo

Indicar por qué es necesaria la tarea.

### Archivos o zonas afectadas

- `ruta/archivo.ext`
- `ruta/carpeta/`

### Riesgos

Indicar si puede afectar a login, permisos, datos, producción o compatibilidad.

### Criterios de aceptación

- [ ] El código compila.
- [ ] No se rompe funcionalidad existente.
- [ ] Se prueban los casos principales.
- [ ] Se actualiza documentación si procede.

### Siguiente paso

Indicar la acción concreta recomendada.
```

---

## 5. Tareas iniciales sin auditar

Estas tareas no están marcadas como implementadas. Deben servir como punto de partida para Codex.

```txt
TAREA-0001 — Auditar estructura real del repositorio.
TAREA-0002 — Revisar seguridad de autenticación y token.
TAREA-0003 — Revisar filtrado por empresa en endpoints.
TAREA-0004 — Revisar visibilidad de partes/albaranes para técnicos.
TAREA-0005 — Revisar dashboard por rol.
TAREA-0006 — Revisar configuración hardcodeada de API y base de datos.
TAREA-0007 — Comparar documentación objetivo con código real.
```

---

## 6. Nota para agentes

No conviertas este roadmap en una lista de cambios automáticos.

Primero audita. Después propone. Luego modifica en ramas pequeñas y con revisión humana.
