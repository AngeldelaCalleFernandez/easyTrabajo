# modulos_futuros.md

# EasyParte — Módulos futuros

## 1. Propósito

Este documento recoge ideas y módulos que pueden ampliar EasyParte en el futuro, pero que no deben mezclarse con el núcleo inicial.

El objetivo es evitar que estas ideas se pierdan sin convertirlas en obligación inmediata.

---

## 2. Regla principal

Un módulo futuro no debe implementarse hasta que EasyParte Core esté estable.

EasyParte Core incluye:

- empresas;
- usuarios;
- roles;
- suscripciones;
- clientes;
- avisos;
- presupuestos;
- partes;
- firma;
- hash;
- auditoría;
- exportación básica.

---

## 3. EasyParte Stock

Módulo futuro de almacén y materiales.

Funcionalidades posibles:

- catálogo de materiales;
- stock por empresa;
- entradas;
- salidas;
- ajustes;
- devoluciones;
- importación de materiales;
- exportación de materiales;
- conexión con facturación o ERP;
- solicitud de material por técnicos;
- aprobación por jefe o almacén.

Entidades posibles:

- material;
- almacen;
- ubicacion_almacen;
- movimiento_almacen;
- solicitud_material;
- solicitud_material_linea;
- proveedor.

Prioridad:

Media, después de estabilizar partes y presupuestos.

---

## 4. EasyParte Fleet

Módulo futuro de flota de vehículos.

Funcionalidades posibles:

- alta de vehículos;
- asignación a técnicos;
- vehículo usado en un parte;
- control de kilometraje;
- revisiones;
- ITV;
- seguros;
- mantenimiento;
- gastos del vehículo.

Entidades posibles:

- vehiculo;
- empleado_vehiculo;
- parte_vehiculo;
- mantenimiento_vehiculo;
- gasto_vehiculo.

Prioridad:

Baja/media, según necesidad real de empresas usuarias.

---

## 5. EasyParte Projects

Módulo futuro de gestión de proyectos tipo Trello/Notion.

Funcionalidades posibles:

- proyectos;
- tableros;
- columnas;
- tarjetas;
- asignaciones;
- etiquetas;
- comentarios;
- fechas límite;
- relación con avisos;
- relación con partes.

Decisión pendiente:

Definir si será:

- módulo interno de EasyParte;
- aplicación paralela conectada;
- producto separado;
- funcionalidad descartada.

Riesgo:

Puede hacer crecer demasiado el proyecto y desviarlo del núcleo.

Recomendación:

No implementarlo en primera versión profesional.

---

## 6. Portal de cliente

Módulo futuro para que el cliente final pueda acceder a cierta información.

Funcionalidades posibles:

- consultar presupuestos;
- aceptar/rechazar presupuesto;
- ver estado de avisos;
- descargar partes;
- firmar online;
- consultar histórico.

Riesgo:

Requiere permisos, acceso público o cuentas de cliente.

Prioridad:

Media/alta si se quiere mejorar la experiencia comercial.

---

## 7. Facturación avanzada

Módulo futuro para ir más allá de la exportación.

Funcionalidades posibles:

- generación de facturas;
- numeración;
- impuestos;
- vencimientos;
- pagos;
- integración con programa externo;
- facturas rectificativas;
- exportación contable.

Decisión pendiente:

Definir si EasyParte debe facturar directamente o solo integrarse con sistemas externos.

---

## 8. Pagos de suscripción

Módulo futuro para automatizar cobros del SaaS.

Funcionalidades posibles:

- Stripe;
- Redsys;
- PayPal;
- facturación recurrente;
- avisos de pago;
- bloqueo automático;
- actualización automática de estado de suscripción.

Prioridad:

Media, cuando el producto tenga clientes reales.

---

## 9. Notificaciones

Módulo futuro de avisos internos y externos.

Funcionalidades posibles:

- notificaciones en app;
- correo electrónico;
- WhatsApp/SMS;
- aviso de presupuesto aceptado;
- aviso de parte cerrado;
- aviso de suscripción pendiente;
- aviso de técnico asignado.

Entidades posibles:

- notificacion;
- plantilla_notificacion;
- canal_notificacion.

---

## 10. App móvil

Módulo o producto futuro.

Funcionalidades posibles:

- vista técnico;
- partes;
- firma;
- fotos;
- materiales;
- horas;
- offline parcial.

Recomendación:

Primero hacer responsive web. App móvil nativa después.

---

## 11. Adjuntos y evidencias

Módulo futuro para adjuntar archivos.

Funcionalidades posibles:

- fotos en aviso;
- fotos en parte;
- documentos;
- PDF de presupuesto;
- PDF de parte firmado;
- evidencias antes/después.

Entidades posibles:

- archivo_adjunto;
- entidad_archivo;
- tipo_archivo.

Prioridad:

Alta/media, porque encaja bien con partes de trabajo.

---

## 12. Informes avanzados

Módulo futuro para explotación de datos.

Funcionalidades posibles:

- horas por técnico;
- rentabilidad por cliente;
- presupuestos aceptados;
- avisos por estado;
- tiempos de resolución;
- materiales más usados;
- exportación Excel/PDF.

Prioridad:

Media.

---

## 13. IA y automatizaciones

Módulo futuro opcional.

Funcionalidades posibles:

- sugerir técnico según carga;
- resumir observaciones;
- detectar partes incompletos;
- generar borrador de presupuesto;
- alertar de anomalías;
- clasificar avisos.

Regla:

No debe sustituir decisiones críticas sin revisión humana.

---

## 14. Priorización recomendada

Orden recomendado de módulos futuros:

1. Adjuntos y evidencias.
2. Exportación/facturación más completa.
3. Materiales y almacén.
4. Notificaciones.
5. Portal de cliente.
6. Flota.
7. Informes avanzados.
8. Pagos de suscripción.
9. Proyectos tipo Trello/Notion.
10. App móvil.
11. IA y automatizaciones.

---

## 15. Conclusión

Los módulos futuros deben ampliar EasyParte, no convertirlo en un sistema caótico.

La prioridad siempre debe ser mantener estable el núcleo:

```txt
clientes → avisos → presupuestos → partes → auditoría → exportación
```
