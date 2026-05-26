# negocio.md

# EasyParte — Lógica de negocio

## 1. Propósito del producto

EasyParte es una aplicación web para empresas de servicios que necesitan gestionar avisos, presupuestos, partes de trabajo, clientes, empleados, técnicos y trazabilidad de las intervenciones.

El objetivo no es solo registrar tareas, sino controlar todo el ciclo de trabajo de una empresa de mantenimiento, reparaciones, instalaciones o servicios técnicos:

1. Alta de clientes.
2. Creación de avisos o incidencias.
3. Asignación de técnicos o equipos.
4. Creación de presupuestos básicos.
5. Aceptación o rechazo del presupuesto.
6. Ejecución del trabajo.
7. Registro de horas, materiales, observaciones y firma.
8. Cierre del parte.
9. Posible exportación a facturación.
10. Auditoría de cambios importantes.

El proyecto parte de una versión TFC funcional y se quiere evolucionar hacia una versión profesional, desplegable y mantenible.

---

## 2. Tipo de aplicación

EasyParte debe entenderse como una aplicación SaaS multiempresa.

Esto significa que una misma instalación puede dar servicio a varias empresas cliente, pero cada empresa debe ver únicamente sus propios datos.

Ejemplo:

- Empresa A tiene sus usuarios, clientes, avisos y partes.
- Empresa B tiene sus usuarios, clientes, avisos y partes.
- Los datos de una empresa no deben mezclarse ni ser visibles para otra.

La separación por empresa es una regla central del sistema.

---

## 3. Usuarios objetivo

EasyParte está pensado para empresas que trabajan con avisos o partes de trabajo.

Ejemplos:

- empresas de mantenimiento;
- empresas de instalaciones;
- servicios técnicos;
- empresas de reparaciones;
- atención de incidencias;
- pequeñas empresas con técnicos en campo;
- negocios que necesitan albaranes o partes firmados por cliente.

---

## 4. Entidades principales

Las entidades principales del negocio son:

- Empresa.
- Plan.
- Suscripción.
- Usuario.
- Rol.
- Empleado.
- Departamento.
- Equipo.
- Cliente.
- Aviso.
- Presupuesto.
- Parte de trabajo.
- Horas de parte.
- Materiales de parte.
- Firma del parte.
- Hash de integridad.
- Auditoría.
- Exportación a facturación.

---

## 5. Conceptos de negocio

### Empresa

Representa a la empresa cliente que usa EasyParte. Cada empresa tiene sus propios usuarios, empleados, departamentos, clientes, avisos, presupuestos y partes.

Una empresa puede tener una suscripción activa, de prueba, cancelada o bloqueada.

### Usuario

Persona que puede iniciar sesión en EasyParte.

Un usuario no pertenece necesariamente a una sola empresa. Puede estar vinculado a varias empresas mediante la tabla `empresa_usuario`.

Ejemplo:

- Un administrador puede gestionar varias empresas.
- Un administrador jefe puede estar vinculado a varias empresas.
- Un técnico normalmente pertenecerá a una sola empresa, pero el modelo permite flexibilidad.

### Empleado

Persona trabajadora de una empresa. No todos los empleados tienen por qué tener acceso a la aplicación.

Un empleado puede estar vinculado a un usuario si necesita iniciar sesión.

### Cliente

Cliente final de la empresa que usa EasyParte.

Ejemplo:

- particular;
- comunidad de vecinos;
- local comercial;
- empresa externa.

### Aviso

Un aviso es una incidencia, solicitud o trabajo pendiente.

Puede venir de un cliente, estar asignado a uno o varios técnicos, tener prioridad y pasar por distintos estados.

Estados recomendados:

- pendiente;
- asignado;
- en_proceso;
- finalizado;
- cancelado.

### Presupuesto

Documento comercial básico asociado a una empresa y cliente. Puede estar vinculado o no a un aviso.

Un presupuesto puede tener varias líneas de concepto:

- mano de obra;
- material;
- desplazamiento;
- servicio;
- otro.

Estados recomendados:

- borrador;
- enviado;
- aceptado;
- rechazado;
- caducado;
- convertido;
- cancelado.

### Parte de trabajo

Documento que registra el trabajo realizado.

Puede estar vinculado a un aviso, cliente y presupuesto. Puede incluir varios empleados, horas, materiales, observaciones, firma del cliente y hash de integridad.

Estados recomendados:

- abierto;
- en_curso;
- pausado;
- cerrado;
- anulado.

---

## 6. Ciclo principal de trabajo

El flujo principal de EasyParte debe ser:

1. Se crea un cliente.
2. Se crea un aviso asociado al cliente.
3. Se asignan uno o varios técnicos al aviso.
4. Si procede, se crea un presupuesto.
5. El presupuesto se envía al cliente.
6. El cliente acepta o rechaza el presupuesto.
7. Si se acepta, se realiza el trabajo.
8. Se crea uno o varios partes de trabajo.
9. Se registran empleados participantes, horas, materiales y observaciones.
10. El cliente firma el parte.
11. El sistema guarda la firma como imagen y almacena su hash.
12. Al cerrar el parte, se genera una huella de integridad.
13. El parte puede exportarse a un sistema de facturación.
14. Las acciones relevantes quedan guardadas en auditoría.

---

## 7. Presupuestos

Los presupuestos forman parte del núcleo básico del producto, no deben tratarse como una idea lejana.

Un presupuesto puede crearse:

- antes de crear un aviso;
- a partir de un aviso ya existente;
- como propuesta comercial previa a una intervención.

Un presupuesto aceptado puede terminar generando un aviso o un parte de trabajo.

Un presupuesto rechazado no debe eliminarse sin trazabilidad.

---

## 8. Partes y rectificaciones

Un parte cerrado no debe modificarse libremente.

Regla definida:

- El técnico dispone de 7 días para rectificar un parte cerrado.
- Pasados esos 7 días, solo un administrador, jefe de departamento o rol autorizado puede liberar la rectificación.
- Cuando un parte se factura, queda bloqueado definitivamente.
- Un parte facturado no se debe modificar directamente.
- Cualquier modificación debe quedar registrada en auditoría.

---

## 9. Firma y hash

La firma del cliente se guarda como imagen, pero debe almacenarse también un hash de la firma.

El sistema debe poder comprobar que la firma o el contenido firmado no han sido alterados.

Además, cuando el parte se cierra, debe generarse un hash de integridad del parte.

---

## 10. Facturación

EasyParte no tiene que ser inicialmente un programa de facturación completo.

Sí debe poder preparar y exportar datos hacia sistemas externos de facturación.

Elementos exportables:

- partes de trabajo;
- presupuestos;
- suscripciones;
- clientes;
- materiales.

La tabla `exportacion_facturacion` sirve para registrar qué se ha enviado, a qué sistema, cuándo y con qué resultado.

---

## 11. Materiales y almacén

En la primera versión, los materiales pueden registrarse manualmente en cada parte o presupuesto.

A futuro se quiere un módulo de almacén:

- alta de materiales;
- stock;
- movimientos de entrada y salida;
- importación/exportación;
- conexión con facturación o ERP externo.

Por eso el modelo debe permitir que una línea de material pueda estar vinculada a un material del catálogo o ser manual.

---

## 12. Módulos futuros

Módulos planteados para futuro:

- flota de vehículos;
- solicitud de materiales;
- almacén avanzado;
- proyectos tipo Trello/Notion;
- gestión de tableros, columnas y tarjetas;
- integraciones externas avanzadas.

Recomendación: no mezclar todo en la primera versión. EasyParte debe priorizar primero el núcleo:

- clientes;
- avisos;
- presupuestos;
- partes;
- usuarios;
- roles;
- suscripciones;
- auditoría;
- trazabilidad.

---

## 13. Lo que no debe inventarse

Si falta información sobre un proceso, no se debe asumir como cierto. Debe registrarse como duda pendiente.

Ejemplos:

- sistema exacto de pago;
- integración concreta con programa de facturación;
- funcionamiento final del módulo de flota;
- si el módulo de proyectos será interno o aplicación separada;
- política exacta de exportación/importación de materiales.

