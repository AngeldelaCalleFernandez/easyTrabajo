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

Esta sección distingue el contrato local actual, validado hasta el 2026-07-29, del
modelo REST objetivo descrito en otras secciones. Los roles actuales reales son
`Administrador`, `Atencion al Cliente` y `Tecnico`.

| Método | Ruta | Descripción actual | Roles actuales | Auditoría |
|---|---|---|---|---|
| GET | /avisos | Lista avisos de la empresa; Tecnico recibe los propios y los libres | Administrador, Atencion al Cliente, Tecnico | No |
| GET | /avisos/empleados-asignables | Lista empleados activos de la empresa; para Tecnico excluye su propio empleado | Administrador, Atencion al Cliente, Tecnico | No |
| POST | /avisos | Crea un aviso; Tecnico solo puede dejarlo libre o asignárselo a sí mismo | Administrador, Atencion al Cliente, Tecnico condicionado | Si hay asignación, registra el evento correspondiente |
| PUT | /avisos/{id} | Edita datos generales sin cambiar `id_empleado` | Administrador, Atencion al Cliente, Tecnico dentro de su alcance | No registra reasignación |
| PUT | /avisos/{id}/asignar | Asigna o reasigna un aviso no terminal a un empleado activo de la misma empresa | Administrador, Atencion al Cliente; Tecnico condicionado | `aviso_asignado` o `aviso_reasignado` |
| PUT | /avisos/{id}/coger | Asigna un aviso libre no terminal al empleado del Tecnico autenticado | Solo Tecnico | `aviso_autoasignado` |
| PUT | /avisos/{id}/cancelar | Cambia el estado a `Cancelada` y conserva el aviso | Administrador, Atencion al Cliente; Tecnico propietario | Fuera de la validación de auditoría de esta fase |
| DELETE | /avisos/{id} | Bloqueado con 403; no forma parte del flujo de cancelación o reasignación | Ninguno | No |

Payload de asignación o reasignación:

```json
{
  "id_empleado": 123
}
```

El valor anterior es únicamente ilustrativo. La API valida que el empleado
destino esté activo y pertenezca a la empresa autenticada. No se deben
documentar identificadores reales de usuarios o empresas.

`PUT /avisos/{id}/coger` y `PUT /avisos/{id}/cancelar` no aceptan un
`id_empleado` como autoridad del cliente. El primero obtiene el empleado desde
el contexto autenticado y el segundo conserva la asignación existente.

Reglas específicas de `PUT /avisos/{id}/asignar`:

- Administrador y Atencion al Cliente pueden asignar o reasignar avisos de su
  empresa.
- Tecnico solo puede reasignar un aviso ya asignado a su propio empleado.
- Tecnico no puede usarlo sobre avisos libres o ajenos ni indicar su propio
  empleado como destino.
- `Finalizada` y `Cancelada` son estados terminales: Administrador, Atencion al
  Cliente y Tecnico reciben HTTP 403 al intentar asignar o reasignar. El mismo
  bloqueo se aplica a `PUT /avisos/{id}/coger`.
- El rechazo terminal ocurre después de bloquear el aviso y antes de validar
  relaciones de destino, actualizar datos o registrar auditoría.
- La operación correcta se ejecuta junto con el registro de auditoría.
- TEST-AVISOS-FIN-001 validó este contrato por API local el 2026-07-29.

El modelo futuro con varios técnicos por aviso y recursos
`/avisos/{id}/empleados` permanece como objetivo; no sustituye estos contratos
actuales ni debe marcarse como implementado.

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
4. Los técnicos ven los datos asignados a ellos y, en avisos, los avisos libres
   habilitados para la acción Coger.
5. Los administradores ven datos de su empresa.
6. Los jefes ven datos de su ámbito.
7. Las acciones críticas deben auditarse.
8. Los errores deben ser homogéneos.
9. Los endpoints deben documentar roles permitidos.
10. Los endpoints deben indicar si generan auditoría.

