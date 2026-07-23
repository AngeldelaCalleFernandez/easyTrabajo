# alcance.md

# EasyParte — Alcance del proyecto

## 1. Propósito del documento

Este documento define qué entra y qué no entra en la profesionalización de EasyParte.

Su objetivo es evitar que el proyecto crezca sin control. EasyParte debe evolucionar desde un TFC funcional hacia una aplicación profesional, pero sin intentar resolver todos los módulos futuros en la primera versión.

Este documento debe ser respetado por ChatGPT, Codex o cualquier agente que trabaje sobre el proyecto.

---

## 2. Alcance general

EasyParte será una aplicación web SaaS multiempresa para empresas de servicios que necesitan gestionar:

- clientes;
- avisos;
- presupuestos;
- partes de trabajo;
- empleados;
- técnicos;
- departamentos;
- equipos;
- roles;
- suscripciones;
- trazabilidad;
- auditoría;
- exportación futura a facturación.

La versión profesional debe mantener la lógica principal del proyecto original, pero mejorar la arquitectura, seguridad, base de datos, roles, permisos, trazabilidad y mantenibilidad.

---

## 3. Dentro del alcance inicial

Entran dentro del alcance inicial profesional:

## 3.1. Gestión de empresas

- alta de empresa;
- datos básicos de empresa;
- empresa activa/inactiva;
- separación de datos por empresa;
- relación con suscripción;
- usuarios vinculados a empresa.

## 3.2. Modelo SaaS

- planes por tramos;
- suscripción Free/prueba;
- plan Básico;
- plan Medio;
- plan Superior;
- plan 30+ a negociar;
- límites por usuarios, avisos, partes y clientes;
- bloqueo o limitación por suscripción.

## 3.3. Usuarios y roles

- usuarios multiempresa;
- vinculación usuario-empresa;
- roles por empresa;
- bloqueo de usuarios por empresa;
- administrador_jefe;
- administrador;
- jefe_departamento;
- jefe_equipo;
- atención_cliente;
- técnico;
- solo_lectura.

## 3.4. Clientes

- listado de clientes;
- creación;
- edición;
- baja lógica;
- reactivación si procede;
- filtrado por empresa;
- relación con avisos, presupuestos y partes.

## 3.5. Avisos

- creación de avisos;
- asignación a uno o varios técnicos;
- asignación a departamento;
- estados;
- prioridades;
- histórico básico;
- vinculación con clientes;
- generación de partes;
- vinculación con presupuestos.

## 3.6. Presupuestos básicos

- creación de presupuesto;
- líneas de presupuesto;
- cálculo de importes;
- estados de presupuesto;
- vinculación a cliente;
- vinculación opcional a aviso;
- aceptación o rechazo;
- preparación para exportación futura.

## 3.7. Partes de trabajo

- creación de parte;
- vinculación con aviso;
- vinculación opcional con presupuesto;
- varios empleados participantes;
- registro de horas;
- registro de materiales;
- observaciones;
- firma del cliente;
- hash de firma;
- cierre del parte;
- hash de integridad;
- rectificación durante 7 días;
- bloqueo definitivo al facturar.

## 3.8. Auditoría y trazabilidad

- auditoría de acciones críticas;
- valores anteriores y nuevos en JSON cuando proceda;
- registro de usuario, empresa, entidad y acción;
- trazabilidad de partes cerrados;
- trazabilidad de roles, bloqueos y suscripciones.

## 3.9. Arquitectura técnica

- frontend Angular;
- backend PHP vanilla orientado a objetos;
- API REST;
- base de datos MariaDB/MySQL;
- PDO;
- middlewares;
- services;
- repositories;
- helpers;
- respuestas API homogéneas.

## 3.10. Estilo visual

- mantener la identidad visual actual;
- verdes como color principal;
- Tailwind CSS;
- tarjetas blancas;
- modales;
- tablas;
- dashboard;
- Chart.js;
- diseño limpio y profesional.

---

## 4. Fuera del alcance inicial

No entran en la primera versión profesional, aunque pueden quedar preparados como módulos futuros:

## 4.1. Facturación completa

No se desarrollará inicialmente un programa completo de facturación.

Sí se dejará prevista la exportación de:

- partes;
- presupuestos;
- clientes;
- materiales;
- suscripciones.

## 4.2. Pasarela de pago

No se define aún una integración real con Stripe, Redsys, PayPal u otra pasarela.

El sistema debe estar preparado para gestionar estados de suscripción, pero la automatización de pagos queda pendiente.

## 4.3. Almacén avanzado

No es obligatorio implementar desde el inicio:

- stock avanzado;
- ubicaciones de almacén;
- pedidos a proveedor;
- inventario completo;
- múltiples almacenes;
- valoración de stock.

Sí debe quedar preparado el modelo para materiales y movimientos básicos.

## 4.4. Flota de vehículos

La flota queda como módulo futuro.

No entra en el núcleo inicial:

- vehículos;
- revisiones;
- ITV;
- seguros;
- kilometraje;
- asignación a técnicos.

## 4.5. Gestión de proyectos tipo Trello/Notion

No entra en el alcance inicial.

Queda pendiente decidir si será:

- módulo interno de EasyParte;
- aplicación paralela conectada;
- funcionalidad descartada.

## 4.6. Aplicación móvil nativa

No entra una app móvil nativa.

Sí debe cuidarse el responsive web para técnicos.

## 4.7. Firma digital legal avanzada

La primera versión contempla firma simple con imagen y hash.

No se define firma electrónica avanzada, certificado digital ni firma legal cualificada.

---

## 5. Alcance técnico inicial

La primera versión técnica debe centrarse en:

1. Seguridad real.
2. Separación por empresa.
3. Roles backend.
4. Modelo SaaS.
5. CRUD de clientes.
6. Avisos con varios técnicos.
7. Presupuestos básicos.
8. Partes con varios empleados.
9. Firma e integridad.
10. Auditoría.
11. Dashboard.
12. Preparación para despliegue.

---

## 6. Alcance funcional mínimo viable

La versión profesional mínima debe permitir:

- iniciar sesión;
- seleccionar o resolver empresa activa si el usuario pertenece a varias;
- consultar dashboard según rol;
- gestionar clientes;
- crear avisos;
- asignar técnicos a avisos;
- crear presupuestos;
- aceptar/rechazar presupuestos;
- crear partes;
- registrar horas;
- registrar materiales;
- firmar partes;
- cerrar partes;
- rectificar partes dentro de plazo;
- bloquear partes facturados;
- consultar trazabilidad según permisos.

---

## 7. Límites del alcance

EasyParte no debe convertirse en una aplicación genérica que haga de todo.

El núcleo debe seguir siendo:

```txt
clientes → avisos → presupuestos → partes → facturación/exportación
```

Los módulos futuros deben diseñarse como extensiones, no como dependencias obligatorias del núcleo.

---

## 8. Criterios para aceptar nuevas funcionalidades

Una nueva funcionalidad solo debe entrar en el alcance si cumple al menos una de estas condiciones:

1. Mejora directamente la gestión de avisos.
2. Mejora directamente la gestión de partes.
3. Mejora la trazabilidad.
4. Mejora la seguridad.
5. Mejora el modelo SaaS.
6. Mejora la exportación a facturación.
7. Es necesaria para roles o permisos.
8. Es necesaria para mantener el producto.

Si no cumple estas condiciones, debe pasar a `modulos_futuros.md`.

---

## 9. Riesgo principal de alcance

El mayor riesgo es intentar construir al mismo tiempo:

- SaaS;
- CRM;
- ERP;
- facturación;
- almacén;
- flota;
- Trello/Notion;
- app móvil.

Eso haría el proyecto demasiado grande.

La prioridad debe ser profesionalizar EasyParte Core.

---

## 10. Conclusión

El alcance inicial debe ser realista.

EasyParte Core debe quedar sólido antes de añadir módulos grandes.

Orden recomendado:

```txt
seguridad → empresas → roles → clientes → avisos → presupuestos → partes → auditoría → exportación
```
