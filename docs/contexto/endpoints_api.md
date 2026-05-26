# endpoints_api.md

# EasyParte — Endpoints API

## 1. Propósito

Este documento define una propuesta inicial de endpoints REST para EasyParte.

No es una especificación final tipo OpenAPI, pero sirve como guía para backend, frontend y Codex.

Regla principal:

> Todo endpoint protegido debe validar autenticación, empresa activa, suscripción y permisos en backend.

---

## 2. Formato estándar de respuesta

Respuesta correcta:

```json
{
  "success": true,
  "data": {},
  "message": "Operación realizada correctamente"
}
```

Respuesta de error:

```json
{
  "success": false,
  "error": {
    "code": "FORBIDDEN",
    "message": "No tienes permisos para realizar esta acción"
  }
}
```

---

## 3. Autenticación

| Método | Ruta | Descripción | Público |
|---|---|---|---|
| POST | /auth/login | Iniciar sesión | Sí |
| POST | /auth/logout | Cerrar sesión | No |
| GET | /auth/me | Obtener usuario actual | No |
| POST | /auth/refresh | Renovar sesión/token si aplica | No/Pendiente |

Notas:

- `/auth/me` debe devolver usuario, empresas disponibles, empresa activa y roles.
- No debe devolver password_hash.

---

## 4. Empresas

| Método | Ruta | Descripción | Roles |
|---|---|---|---|
| GET | /empresas | Listar empresas accesibles para el usuario | autenticado |
| GET | /empresas/{id} | Ver empresa | administrador_jefe, administrador |
| PATCH | /empresas/{id} | Actualizar empresa | administrador_jefe |
| PATCH | /empresas/{id}/activar | Activar empresa | pendiente |
| PATCH | /empresas/{id}/bloquear | Bloquear empresa | pendiente |

Notas:

- Un usuario solo ve empresas donde tenga vinculación.
- La administración global de empresas queda pendiente.

---

## 5. Planes y suscripciones

| Método | Ruta | Descripción | Roles |
|---|---|---|---|
| GET | /planes | Listar planes disponibles | autenticado |
| GET | /suscripciones/actual | Ver suscripción de empresa activa | administrador_jefe, administrador |
| POST | /suscripciones | Crear suscripción | pendiente |
| PATCH | /suscripciones/{id}/cambiar-plan | Cambiar plan | administrador_jefe |
| PATCH | /suscripciones/{id}/cancelar | Cancelar suscripción | administrador_jefe |
| GET | /suscripciones/uso | Ver uso del plan | administrador_jefe, administrador |

---

## 6. Usuarios y roles

| Método | Ruta | Descripción | Roles |
|---|---|---|---|
| GET | /usuarios | Listar usuarios de empresa | administrador_jefe, administrador |
| POST | /usuarios | Crear usuario | administrador_jefe, administrador con permiso |
| GET | /usuarios/{id} | Ver usuario | administrador_jefe, administrador |
| PATCH | /usuarios/{id} | Editar usuario | administrador_jefe, administrador |
| PATCH | /usuarios/{id}/bloquear | Bloquear usuario en empresa | administrador_jefe |
| PATCH | /usuarios/{id}/desbloquear | Desbloquear usuario | administrador_jefe |
| GET | /roles | Listar roles | administrador_jefe, administrador |
| POST | /usuarios/{id}/roles | Asignar rol en empresa | administrador_jefe |
| DELETE | /usuarios/{id}/roles/{rolId} | Quitar rol | administrador_jefe |

---

## 7. Empleados, departamentos y equipos

| Método | Ruta | Descripción | Roles |
|---|---|---|---|
| GET | /empleados | Listar empleados | administrador_jefe, administrador, jefe_departamento |
| POST | /empleados | Crear empleado | administrador_jefe, administrador |
| GET | /empleados/{id} | Ver empleado | según permisos |
| PATCH | /empleados/{id} | Editar empleado | administrador_jefe, administrador |
| DELETE | /empleados/{id} | Baja lógica | administrador_jefe, administrador |
| GET | /departamentos | Listar departamentos | administrador_jefe, administrador |
| POST | /departamentos | Crear departamento | administrador_jefe |
| PATCH | /departamentos/{id} | Editar departamento | administrador_jefe |
| GET | /equipos | Listar equipos | administrador_jefe, administrador, jefe_departamento |
| POST | /equipos | Crear equipo | administrador_jefe, administrador, jefe_departamento |
| PATCH | /equipos/{id} | Editar equipo | administrador_jefe, administrador, jefe_departamento |

---

## 8. Clientes

| Método | Ruta | Descripción | Roles |
|---|---|---|---|
| GET | /clientes | Listar clientes | según permisos |
| POST | /clientes | Crear cliente | administrador_jefe, administrador, atencion_cliente |
| GET | /clientes/{id} | Ver cliente | según permisos |
| PATCH | /clientes/{id} | Editar cliente | administrador_jefe, administrador, atencion_cliente |
| DELETE | /clientes/{id} | Baja lógica | administrador_jefe, administrador |
| PATCH | /clientes/{id}/reactivar | Reactivar cliente | administrador_jefe, administrador |

---

## 9. Avisos

| Método | Ruta | Descripción | Roles |
|---|---|---|---|
| GET | /avisos | Listar avisos | según permisos |
| POST | /avisos | Crear aviso | administrador_jefe, administrador, jefe_departamento, atencion_cliente |
| GET | /avisos/{id} | Ver aviso | según permisos |
| PATCH | /avisos/{id} | Editar aviso | según permisos |
| PATCH | /avisos/{id}/estado | Cambiar estado | según permisos |
| DELETE | /avisos/{id} | Cancelar/baja lógica | administrador_jefe, administrador |
| POST | /avisos/{id}/empleados | Asignar técnico | administrador_jefe, administrador, jefe_departamento, jefe_equipo |
| DELETE | /avisos/{id}/empleados/{empleadoId} | Quitar técnico | administrador_jefe, administrador, jefe_departamento |
| GET | /avisos/{id}/partes | Partes del aviso | según permisos |
| GET | /avisos/{id}/presupuestos | Presupuestos del aviso | según permisos |

---

## 10. Presupuestos

| Método | Ruta | Descripción | Roles |
|---|---|---|---|
| GET | /presupuestos | Listar presupuestos | según permisos |
| POST | /presupuestos | Crear presupuesto | administrador_jefe, administrador, atencion_cliente con permiso |
| GET | /presupuestos/{id} | Ver presupuesto | según permisos |
| PATCH | /presupuestos/{id} | Editar presupuesto | según permisos |
| DELETE | /presupuestos/{id} | Cancelar presupuesto | administrador_jefe, administrador |
| POST | /presupuestos/{id}/lineas | Añadir línea | según permisos |
| PATCH | /presupuestos/{id}/lineas/{lineaId} | Editar línea | según permisos |
| DELETE | /presupuestos/{id}/lineas/{lineaId} | Eliminar línea | según permisos |
| PATCH | /presupuestos/{id}/enviar | Marcar como enviado | según permisos |
| PATCH | /presupuestos/{id}/aceptar | Aceptar presupuesto | administrador_jefe, administrador |
| PATCH | /presupuestos/{id}/rechazar | Rechazar presupuesto | administrador_jefe, administrador |
| POST | /presupuestos/{id}/convertir | Convertir en aviso/parte | administrador_jefe, administrador |

---

## 11. Partes de trabajo

| Método | Ruta | Descripción | Roles |
|---|---|---|---|
| GET | /partes | Listar partes | según permisos |
| POST | /partes | Crear parte | administrador_jefe, administrador, jefe_departamento, jefe_equipo, tecnico asignado |
| GET | /partes/{id} | Ver parte | según permisos |
| PATCH | /partes/{id} | Editar parte abierto | según permisos |
| DELETE | /partes/{id} | Anular parte | administrador_jefe, administrador |
| POST | /partes/{id}/empleados | Añadir empleado | según permisos |
| DELETE | /partes/{id}/empleados/{empleadoId} | Quitar empleado | según permisos |
| POST | /partes/{id}/horas | Registrar horas | técnico asignado o superior |
| PATCH | /partes/{id}/horas/{horaId} | Editar horas | según permisos |
| POST | /partes/{id}/materiales | Añadir material | técnico asignado o superior |
| PATCH | /partes/{id}/materiales/{materialId} | Editar material | según permisos |
| POST | /partes/{id}/firma | Registrar firma | técnico asignado o superior |
| PATCH | /partes/{id}/cerrar | Cerrar parte | técnico asignado o superior |
| POST | /partes/{id}/rectificaciones | Solicitar rectificación | técnico asignado o superior |
| PATCH | /partes/{id}/rectificaciones/{rectificacionId}/autorizar | Autorizar rectificación | administrador_jefe, administrador, jefe_departamento |
| PATCH | /partes/{id}/facturar | Marcar como facturado | administrador_jefe, administrador |

---

## 12. Materiales y almacén

| Método | Ruta | Descripción | Roles |
|---|---|---|---|
| GET | /materiales | Listar materiales | según permisos |
| POST | /materiales | Crear material | administrador_jefe, administrador |
| GET | /materiales/{id} | Ver material | según permisos |
| PATCH | /materiales/{id} | Editar material | administrador_jefe, administrador |
| DELETE | /materiales/{id} | Baja lógica | administrador_jefe, administrador |
| GET | /movimientos-almacen | Listar movimientos | administrador_jefe, administrador |
| POST | /movimientos-almacen | Crear movimiento | administrador_jefe, administrador |

Nota:

El almacén avanzado puede quedar para fase posterior.

---

## 13. Exportación a facturación

| Método | Ruta | Descripción | Roles |
|---|---|---|---|
| GET | /exportaciones-facturacion | Listar exportaciones | administrador_jefe, administrador |
| POST | /exportaciones-facturacion | Crear exportación | administrador_jefe, administrador |
| GET | /exportaciones-facturacion/{id} | Ver exportación | administrador_jefe, administrador |
| POST | /exportaciones-facturacion/{id}/reintentar | Reintentar exportación | administrador_jefe, administrador |

---

## 14. Auditoría

| Método | Ruta | Descripción | Roles |
|---|---|---|---|
| GET | /auditoria | Listar auditoría | administrador_jefe, administrador limitado |
| GET | /auditoria/{id} | Ver evento | administrador_jefe |
| GET | /auditoria/entidad/{entidad}/{id} | Auditoría de entidad | administrador_jefe, administrador |

---

## 15. Dashboard

| Método | Ruta | Descripción | Roles |
|---|---|---|---|
| GET | /dashboard | Dashboard según rol | autenticado |
| GET | /dashboard/empresa | Métricas de empresa | administrador_jefe, administrador |
| GET | /dashboard/departamento | Métricas departamento | jefe_departamento |
| GET | /dashboard/equipo | Métricas equipo | jefe_equipo |
| GET | /dashboard/tecnico | Métricas técnico | tecnico |

---

## 16. Reglas globales de endpoints

1. Ningún endpoint protegido debe ejecutarse sin usuario autenticado.
2. Ningún endpoint debe confiar en `id_empresa` enviado por frontend como fuente de verdad.
3. Los endpoints deben filtrar por empresa.
4. Los técnicos solo ven datos asignados.
5. Los administradores ven datos de su empresa.
6. Los jefes ven datos de su ámbito.
7. Las acciones críticas deben auditarse.
8. Los errores deben ser homogéneos.
9. Los endpoints deben documentar roles permitidos.
10. Los endpoints deben indicar si generan auditoría.

