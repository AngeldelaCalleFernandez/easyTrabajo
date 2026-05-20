# estados_del_sistema.md

# EasyParte — Estados del sistema

## 1. Propósito

Este documento define los estados principales usados en EasyParte.

Los estados deben usarse de forma coherente en base de datos, backend, frontend, filtros, badges visuales y auditoría.

---

## 2. Estados de suscripción

Entidad:

```txt
suscripcion
```

Estados recomendados:

| Estado | Descripción |
|---|---|
| prueba | Suscripción gratuita o temporal con límites. |
| activa | La empresa puede usar la aplicación normalmente. |
| pendiente_pago | La suscripción requiere regularizar pago. |
| cancelada | La empresa ha cancelado la suscripción. |
| caducada | La suscripción ha terminado por fecha. |
| bloqueada | Acceso bloqueado por impago, abuso o decisión administrativa. |

Reglas:

- Solo `prueba` y `activa` permiten uso normal.
- `pendiente_pago` puede permitir acceso limitado.
- `bloqueada` debe impedir acciones principales.
- Todo cambio debe auditarse.

---

## 3. Estados de empresa_usuario

Entidad:

```txt
empresa_usuario
```

Estados recomendados:

| Estado | Descripción |
|---|---|
| activo | El usuario puede trabajar en la empresa. |
| invitado | Usuario invitado, pendiente de activación. |
| pendiente | Vinculación creada pero no confirmada. |
| bloqueado | Usuario bloqueado en esa empresa. |
| baja | Usuario dado de baja en esa empresa. |

Reglas:

- Un usuario puede estar activo en una empresa y bloqueado en otra.
- El bloqueo debe registrar `bloqueado_por` y `bloqueado_at`.

---

## 4. Estados de cliente

Entidad:

```txt
cliente
```

Estados simples:

```txt
activo
inactivo
baja
```

Reglas:

- Un cliente con partes, avisos o presupuestos no debe eliminarse físicamente.
- La baja debe ser lógica.

---

## 5. Estados de aviso

Entidad:

```txt
aviso
```

Estados recomendados:

| Estado | Descripción |
|---|---|
| pendiente | Aviso creado pero sin trabajo iniciado. |
| asignado | Tiene técnico o equipo asignado. |
| en_proceso | El trabajo está en curso. |
| finalizado | El aviso ha sido resuelto. |
| cancelado | El aviso se cancela y no continuará. |

Reglas:

- Un aviso puede estar pendiente sin técnico.
- Un aviso puede tener varios técnicos.
- Un aviso finalizado no debería modificarse salvo autorización.
- Un aviso cancelado no debe eliminarse sin trazabilidad.

---

## 6. Estados de presupuesto

Entidad:

```txt
presupuesto
```

Estados recomendados:

| Estado | Descripción |
|---|---|
| borrador | Presupuesto en preparación. |
| enviado | Presupuesto enviado o presentado al cliente. |
| aceptado | El cliente acepta el presupuesto. |
| rechazado | El cliente rechaza el presupuesto. |
| caducado | Ha superado la fecha de validez. |
| convertido | Ya se ha convertido en trabajo, aviso o parte. |
| cancelado | Presupuesto cancelado internamente. |

Reglas:

- Solo un presupuesto aceptado debería convertirse en trabajo.
- Un presupuesto rechazado no debe borrarse sin trazabilidad.
- Los cambios de estado deben auditarse.

---

## 7. Estados de parte de trabajo

Entidad:

```txt
parte_trabajo
```

Estados recomendados:

| Estado | Descripción |
|---|---|
| abierto | Parte creado pero no finalizado. |
| en_curso | Trabajo registrándose activamente. |
| pausado | Trabajo detenido temporalmente. |
| cerrado | Parte terminado y cerrado. |
| anulado | Parte anulado por error o decisión autorizada. |

Campos complementarios:

- facturado;
- bloqueado_definitivo;
- rectificable_hasta;
- cerrado_at;
- cerrado_por.

Reglas:

- Un parte cerrado puede rectificarse durante 7 días.
- Un parte facturado queda bloqueado definitivamente.
- Cerrar un parte debe generar hash de integridad.

---

## 8. Estados de rectificación

Entidad:

```txt
parte_rectificacion
```

Estados recomendados:

| Estado | Descripción |
|---|---|
| pendiente | Solicitud creada, pendiente de resolver. |
| autorizada | Se permite modificar o rectificar. |
| rechazada | No se permite la rectificación. |
| aplicada | La rectificación ya se aplicó. |
| caducada | La solicitud ya no es válida. |

Reglas:

- Debe registrar motivo.
- Debe registrar solicitante.
- Debe registrar autorizador cuando proceda.
- Debe generar auditoría.

---

## 9. Estados de exportación a facturación

Entidad:

```txt
exportacion_facturacion
```

Estados recomendados:

| Estado | Descripción |
|---|---|
| pendiente | Preparada pero no enviada. |
| exportado | Enviada correctamente. |
| error | Falló la exportación. |
| reintentado | Se ha intentado reenviar. |
| cancelado | Exportación cancelada. |

Reglas:

- Un error de exportación no debe borrar los datos.
- Debe guardarse payload y respuesta externa si existe.

---

## 10. Estados de material

Entidad:

```txt
material
```

Estados simples:

```txt
activo
inactivo
baja
```

Para movimientos de almacén:

```txt
entrada
salida
ajuste
devolucion
```

---

## 11. Estados visuales recomendados

## Avisos

- pendiente: gris o amarillo;
- asignado: azul;
- en_proceso: naranja;
- finalizado: verde;
- cancelado: rojo o gris.

## Presupuestos

- borrador: gris;
- enviado: azul;
- aceptado: verde;
- rechazado: rojo;
- caducado: amarillo/gris;
- convertido: verde oscuro;
- cancelado: gris/rojo.

## Partes

- abierto: azul;
- en_curso: naranja;
- pausado: amarillo;
- cerrado: verde;
- anulado: rojo/gris;
- facturado: verde oscuro o morado.

## Suscripciones

- prueba: azul;
- activa: verde;
- pendiente_pago: amarillo;
- cancelada: gris;
- caducada: naranja;
- bloqueada: rojo.

---

## 12. Regla general

Los estados deben ser consistentes.

No se deben crear estados nuevos en código sin actualizar este documento.
