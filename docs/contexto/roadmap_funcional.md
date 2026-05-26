# roadmap_funcional.md

# EasyParte — Roadmap funcional

## 1. Propósito

Este documento define el orden recomendado de evolución funcional de EasyParte.

No es un plan cerrado de fechas, sino una guía de fases para que el proyecto avance sin mezclar demasiadas cosas a la vez.

---

## Fase 0 — Organización documental

Objetivo:

Dejar preparada la documentación que servirá como fuente de verdad.

Tareas:

- negocio.md;
- modelo_saas.md;
- reglas_de_negocio.md;
- roles_y_permisos.md;
- arquitectura.md;
- trazabilidad_y_auditoria.md;
- estilo_visual.md;
- objetivos.md;
- alcance.md;
- decisiones_tecnicas.md;
- entidades.md;
- dudas_pendientes.md;
- glosario.md;
- estados_del_sistema.md;
- endpoints_api.md;
- roadmap_funcional.md;
- modulos_futuros.md.

Resultado esperado:

El proyecto tiene documentación clara antes de que Codex modifique código.

---

## Fase 1 — Seguridad base

Objetivo:

Cerrar los riesgos principales antes de añadir funcionalidad.

Tareas:

- autenticación real;
- middleware de autenticación;
- middleware de roles;
- middleware de empresa/tenant;
- middleware de suscripción;
- eliminar endpoints temporales;
- eliminar IDs hardcodeados;
- configurar errores API;
- revisar CORS;
- usar variables de entorno;
- asegurar password_hash.

Resultado esperado:

La aplicación deja de depender de controles solo visuales.

---

## Fase 2 — Modelo multiempresa y usuarios

Objetivo:

Implementar usuarios vinculados a una o varias empresas.

Tareas:

- empresa;
- usuario;
- empresa_usuario;
- rol;
- empresa_usuario_rol;
- bloqueo por empresa;
- selección de empresa activa;
- permisos por empresa.

Resultado esperado:

Un usuario puede trabajar en distintas empresas con roles diferentes.

---

## Fase 3 — Modelo SaaS

Objetivo:

Implementar planes y suscripciones.

Tareas:

- plan Free/prueba;
- plan Básico;
- plan Medio;
- plan Superior;
- plan 30+ negociar;
- límites de usuarios;
- límites de clientes;
- límites de avisos;
- límites de partes;
- pantalla de uso del plan;
- bloqueo por suscripción.

Resultado esperado:

EasyParte puede controlar si una empresa puede usar la aplicación y bajo qué límites.

---

## Fase 4 — Clientes

Objetivo:

Profesionalizar la gestión de clientes.

Tareas:

- listado;
- creación;
- edición;
- baja lógica;
- reactivación;
- validaciones;
- filtrado por empresa;
- auditoría.

Resultado esperado:

Clientes sólidos, filtrados por empresa y preparados para avisos, presupuestos y partes.

---

## Fase 5 — Avisos

Objetivo:

Profesionalizar el módulo de avisos.

Tareas:

- crear aviso;
- editar aviso;
- cambiar estado;
- asignar varios técnicos;
- vincular departamento;
- prioridad;
- filtros;
- permisos por rol;
- auditoría de asignaciones;
- dashboard de avisos.

Resultado esperado:

Los avisos reflejan el trabajo real y permiten varios técnicos.

---

## Fase 6 — Presupuestos básicos

Objetivo:

Añadir presupuestos al núcleo de EasyParte.

Tareas:

- crear presupuesto;
- líneas de presupuesto;
- cálculo de subtotal, descuento, impuestos y total;
- estados;
- aceptar;
- rechazar;
- convertir en trabajo;
- vincular a aviso;
- vincular a cliente;
- auditoría.

Resultado esperado:

La empresa puede preparar presupuestos básicos antes o después de crear un aviso.

---

## Fase 7 — Partes de trabajo

Objetivo:

Profesionalizar albaranes/partes.

Tareas:

- crear parte;
- vincular aviso;
- vincular presupuesto;
- añadir varios empleados;
- registrar horas;
- registrar materiales;
- observaciones;
- estados;
- permisos por técnico;
- filtros;
- dashboard de partes.

Resultado esperado:

Los partes reflejan el trabajo real y pueden incluir varias personas.

---

## Fase 8 — Firma, hash y rectificación

Objetivo:

Añadir integridad al cierre del parte.

Tareas:

- firma como imagen;
- hash de firma;
- cierre de parte;
- hash de integridad;
- rectificación 7 días;
- liberación por rol autorizado;
- bloqueo definitivo al facturar;
- auditoría de cambios.

Resultado esperado:

Los partes cerrados tienen trazabilidad e integridad.

---

## Fase 9 — Auditoría

Objetivo:

Centralizar auditoría de acciones críticas.

Tareas:

- AuditLogger;
- auditoria_evento;
- valores anteriores/nuevos;
- descripción legible;
- filtros;
- permisos de consulta;
- auditoría de roles, partes, presupuestos y suscripciones.

Resultado esperado:

Se puede saber quién hizo qué, cuándo y qué cambió.

---

## Fase 10 — Exportación a facturación

Objetivo:

Preparar salida de datos hacia sistemas externos.

Tareas:

- exportacion_facturacion;
- exportar parte;
- exportar presupuesto;
- registrar payload;
- registrar respuesta;
- estado de exportación;
- reintentos.

Resultado esperado:

EasyParte queda preparado para conectarse con programas de facturación.

---

## Fase 11 — Materiales básicos

Objetivo:

Preparar el camino hacia almacén.

Tareas:

- material;
- parte_material;
- presupuesto_linea vinculada a material;
- movimientos básicos;
- importación/exportación futura.

Resultado esperado:

Los materiales dejan de ser solo texto libre.

---

## Fase 12 — Dashboard avanzado

Objetivo:

Mejorar la visión de empresa y roles.

Tareas:

- dashboard empresa;
- dashboard departamento;
- dashboard equipo;
- dashboard técnico;
- uso de plan;
- métricas de avisos;
- métricas de partes;
- métricas de presupuestos;
- gráficos Chart.js.

Resultado esperado:

Cada rol ve información útil según su ámbito.

---

## Fase 13 — Despliegue y producción

Objetivo:

Preparar la aplicación para entorno real.

Tareas:

- variables de entorno;
- HTTPS;
- CORS producción;
- logs;
- backups;
- política de errores;
- documentación de despliegue;
- checklist preproducción.

Resultado esperado:

EasyParte puede desplegarse de forma más segura y mantenible.

---

## Fase 14 — Módulos futuros

Módulos que no deben entrar antes de estabilizar el núcleo:

- almacén avanzado;
- flota;
- solicitud de materiales;
- proyectos tipo Trello/Notion;
- integraciones avanzadas;
- pagos reales.

---

## Orden recomendado resumido

```txt
documentación
seguridad
usuarios multiempresa
SaaS
clientes
avisos
presupuestos
partes
firma/hash
auditoría
facturación/exportación
materiales
dashboard
despliegue
módulos futuros
```
