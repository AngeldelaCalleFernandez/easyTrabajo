# glosario.md

# EasyParte — Glosario

## 1. Propósito

Este documento define los términos principales usados en EasyParte.

Sirve para que la documentación, el código, la base de datos y los agentes usen el mismo lenguaje.

---

## Administrador

Usuario con permisos amplios dentro de una empresa, pero por debajo del administrador_jefe.

Puede gestionar clientes, avisos, presupuestos, partes, empleados y algunas configuraciones según permisos.

---

## Administrador jefe

Rol superior dentro de una empresa.

Puede gestionar usuarios, roles, bloqueos, configuración de empresa, suscripción y auditoría.

No equivale necesariamente a un superadmin global de EasyParte.

---

## Albarán

Nombre usado en el proyecto actual para referirse a partes de trabajo.

En la versión profesional se usará preferiblemente el término `parte_trabajo`.

---

## API REST

Interfaz de comunicación entre frontend Angular y backend PHP.

Usa rutas HTTP para crear, leer, actualizar y eliminar datos.

---

## Auditoría

Registro de acciones importantes realizadas dentro del sistema.

Permite saber quién hizo qué, cuándo, sobre qué entidad y qué datos cambiaron.

---

## Aviso

Incidencia, solicitud o trabajo pendiente.

En el proyecto anterior puede aparecer como `tarea`. En la versión profesional se recomienda usar `aviso`.

---

## Borrado lógico

Forma de desactivar un registro sin eliminarlo físicamente.

Normalmente se hace con campos como:

- activo;
- deleted_at;
- deleted_by.

---

## Cliente

Cliente final de la empresa que usa EasyParte.

Puede tener avisos, presupuestos y partes asociados.

---

## Codex

Agente o herramienta de ayuda al desarrollo que puede revisar, modificar o preparar código siguiendo instrucciones.

Debe trabajar con tareas pequeñas y documentación clara.

---

## Departamento

Área interna de una empresa.

Puede agrupar empleados, equipos y avisos.

---

## EasyParte Core

Núcleo principal de EasyParte:

- empresas;
- usuarios;
- roles;
- clientes;
- avisos;
- presupuestos;
- partes;
- auditoría;
- suscripciones.

---

## Empleado

Persona trabajadora de una empresa.

No todos los empleados tienen necesariamente usuario para iniciar sesión.

---

## Empresa

Empresa cliente que contrata y usa EasyParte.

Tiene sus propios usuarios, empleados, clientes, avisos, presupuestos y partes.

---

## Empresa usuario

Relación entre una empresa y un usuario.

Permite que un usuario pertenezca a varias empresas y tenga roles distintos en cada una.

---

## Equipo

Grupo de empleados dentro de un departamento.

Puede tener un jefe de equipo.

---

## Exportación a facturación

Proceso de enviar datos de EasyParte a un sistema externo de facturación o contabilidad.

Puede aplicarse a partes, presupuestos, clientes, materiales o suscripciones.

---

## Firma

Imagen o trazo asociado al cliente o firmante del parte.

Debe guardarse junto a un hash de firma.

---

## Hash

Huella digital generada a partir de un contenido.

Permite detectar si una firma o parte cerrado ha sido modificado.

---

## Integridad

Capacidad de comprobar que un documento o registro no ha sido alterado.

---

## Jefe de departamento

Rol que gestiona los avisos, técnicos y partes dentro de un departamento.

---

## Jefe de equipo

Rol que gestiona un grupo de técnicos o empleados.

---

## Material

Producto, pieza, recurso o concepto usado en un parte o presupuesto.

Puede ser manual o venir de catálogo.

---

## Middleware

Capa del backend que se ejecuta antes del controlador.

Sirve para validar autenticación, roles, empresa, suscripción o permisos.

---

## Multitenant

Modelo en el que una misma aplicación sirve a varias empresas separando sus datos.

En EasyParte, cada empresa solo debe acceder a su propia información.

---

## Parte de trabajo

Registro del trabajo realizado sobre un aviso.

Puede incluir empleados, horas, materiales, observaciones, firma y hash.

---

## Plan

Tipo de suscripción disponible en EasyParte.

Ejemplos:

- Free;
- Básico;
- Medio;
- Superior;
- 30+ negociar.

---

## Presupuesto

Propuesta económica enviada o preparada para un cliente.

Puede estar vinculada a un aviso y puede generar un parte de trabajo.

---

## Rectificación

Proceso de corrección de un parte cerrado.

Tiene reglas especiales, especialmente si han pasado 7 días o el parte ya está facturado.

---

## Rol

Permiso o conjunto de permisos que tiene un usuario dentro de una empresa.

---

## SaaS

Software as a Service.

Modelo donde las empresas usan EasyParte mediante suscripción.

---

## Suscripción

Relación entre empresa y plan.

Define si la empresa puede usar la aplicación y bajo qué límites.

---

## Técnico

Empleado o usuario encargado de realizar trabajos.

Normalmente ve solo avisos y partes asignados.

---

## Tenant

Empresa o cliente empresarial dentro de un sistema multitenant.

En EasyParte, normalmente equivale a `empresa`.

---

## Trazabilidad

Capacidad de seguir la vida de un registro o proceso.

Ejemplo:

Aviso creado → técnico asignado → parte cerrado → exportado a facturación.

