# trazabilidad_y_auditoria.md

# EasyParte — Trazabilidad, auditoría e integridad

## 1. Objetivo

Este documento define cómo EasyParte debe registrar cambios, acciones importantes, firmas, hashes y estados críticos.

El objetivo es que el sistema pueda responder preguntas como:

- quién creó un aviso;
- quién asignó un técnico;
- quién modificó un cliente;
- quién cerró un parte;
- cuándo se firmó un parte;
- si un parte cerrado fue modificado;
- si un parte ya fue facturado;
- qué datos cambiaron antes y después;
- desde qué empresa y usuario se realizó una acción.

---

## 2. Diferencia entre trazabilidad, auditoría e integridad

## Trazabilidad

Permite seguir la vida de una entidad.

Ejemplo:

```txt
Aviso creado → técnico asignado → parte abierto → parte cerrado → exportado a facturación
```

## Auditoría

Registra quién hizo una acción, cuándo y qué cambió.

Ejemplo:

```txt
El usuario 14 cambió el estado del aviso 35 de pendiente a asignado.
```

## Integridad

Permite comprobar si un documento o registro ha sido alterado.

Ejemplo:

```txt
El parte cerrado genera un hash.
Si el contenido cambia después, el hash ya no coincide.
```

---

## 3. Entidades que requieren trazabilidad fuerte

Deben tener trazabilidad fuerte:

- usuario;
- empresa_usuario;
- roles;
- suscripción;
- cliente;
- aviso;
- presupuesto;
- parte_trabajo;
- parte_firma;
- parte_hash;
- parte_rectificacion;
- exportacion_facturacion;
- material;
- movimiento_almacen.

---

## 4. Campos estándar recomendados

En entidades principales:

```txt
created_at
updated_at
deleted_at
```

En entidades críticas también puede ser necesario:

```txt
created_by
updated_by
deleted_by
motivo_baja
```

No todas las tablas necesitan todos los campos, pero las entidades principales sí deben permitir saber cuándo se crearon, actualizaron o desactivaron.

---

## 5. Auditoría general

La tabla `auditoria_evento` debe registrar acciones importantes.

Campos recomendados:

```txt
id_evento
id_empresa
id_usuario
entidad
entidad_id
accion
descripcion
valores_anteriores
valores_nuevos
ip
user_agent
created_at
```

## Explicación de campos

`id_empresa`: empresa afectada por la acción.

`id_usuario`: usuario que realiza la acción.

`entidad`: nombre de la entidad afectada.

Ejemplos:

```txt
cliente
aviso
presupuesto
parte_trabajo
usuario
suscripcion
```

`entidad_id`: ID del registro afectado.

`accion`: acción realizada.

Ejemplos:

```txt
crear
actualizar
eliminar
asignar
cerrar
rectificar
bloquear
exportar
```

`descripcion`: texto fácil de leer.

`valores_anteriores`: JSON con valores antes del cambio.

`valores_nuevos`: JSON con valores después del cambio.

---

## 6. Auditoría con JSON explicada

Ejemplo: se cambia la prioridad de un aviso.

Antes:

```json
{
  "prioridad": "normal",
  "estado": "pendiente"
}
```

Después:

```json
{
  "prioridad": "alta",
  "estado": "asignado"
}
```

La auditoría debería guardar:

```txt
descripcion: "Se actualizó la prioridad y el estado del aviso"
valores_anteriores: {"prioridad":"normal","estado":"pendiente"}
valores_nuevos: {"prioridad":"alta","estado":"asignado"}
```

Esto permite saber exactamente qué cambió.

---

## 7. Acciones que deben auditarse

## Usuarios y permisos

- creación de usuario;
- cambio de contraseña;
- activación/desactivación;
- bloqueo por empresa;
- asignación de rol;
- retirada de rol;
- cambio de empresa activa;
- intento de acceso no autorizado relevante.

## Empresa y suscripción

- creación de empresa;
- cambio de plan;
- activación de suscripción;
- cancelación;
- bloqueo;
- superación de límites;
- intento de crear recurso fuera de límites.

## Clientes

- creación;
- actualización;
- baja lógica;
- reactivación.

## Avisos

- creación;
- cambio de estado;
- asignación de técnico;
- retirada de técnico;
- cambio de prioridad;
- cancelación;
- finalización.

## Presupuestos

- creación;
- actualización;
- envío;
- aceptación;
- rechazo;
- caducidad;
- conversión en trabajo;
- exportación.

## Partes

- creación;
- asignación de empleados;
- registro de horas;
- registro de materiales;
- firma;
- cierre;
- rectificación;
- bloqueo definitivo;
- facturación;
- exportación.

## Materiales

- creación;
- actualización;
- entrada de stock;
- salida de stock;
- ajuste;
- importación;
- exportación.

---

## 8. Hash de firma

La firma del cliente se guarda como imagen o ruta de archivo.

Además, se guarda un hash de esa firma.

Tabla recomendada:

```txt
parte_firma
```

Campos clave:

```txt
id_parte_firma
id_parte_trabajo
ruta_archivo
hash_firma
algoritmo_hash
nombre_firmante
documento_firmante
fecha_firma
ip
user_agent
```

Reglas:

1. No se debe sobrescribir una firma sin registrar auditoría.
2. La firma debe asociarse a un parte concreto.
3. El hash debe calcularse sobre el contenido real o representación estable de la firma.
4. Debe guardarse el algoritmo usado.
5. Si se cambia la firma, debe quedar trazabilidad.

---

## 9. Hash de integridad del parte

Cuando un parte se cierra, el sistema debe generar un hash de integridad.

Tabla recomendada:

```txt
parte_hash
```

Campos clave:

```txt
id_parte_hash
id_parte_trabajo
hash_actual
hash_anterior
algoritmo
payload_firmado
version
created_at
```

## Qué debe incluir el payload firmado

El hash debería calcularse con datos relevantes del parte:

- id_parte_trabajo;
- id_empresa;
- id_cliente;
- id_aviso;
- empleados participantes;
- horas;
- materiales;
- observaciones;
- fecha_inicio;
- fecha_fin;
- firma_cliente/hash_firma;
- cerrado_at;
- cerrado_por;
- facturado si aplica.

El payload debe ser estable: mismo contenido, mismo hash.

---

## 10. Cadena de hashes

Para mejorar integridad, puede guardarse `hash_anterior`.

Esto permite crear una cadena:

```txt
hash versión 1 → hash versión 2 → hash versión 3
```

Si se altera una versión intermedia, la cadena deja de cuadrar.

No es necesario convertir esto en blockchain. Basta con una cadena de integridad interna bien registrada.

---

## 11. Rectificación de partes

Regla definida:

- El técnico puede rectificar un parte cerrado durante 7 días.
- Pasados 7 días, debe liberarlo un administrador, administrador_jefe o jefe_departamento autorizado.
- Si el parte está facturado, queda bloqueado definitivamente.

Tabla recomendada:

```txt
parte_rectificacion
```

Estados recomendados:

```txt
pendiente
autorizada
rechazada
aplicada
caducada
```

Toda rectificación debe registrar:

- quién la solicita;
- quién la autoriza;
- motivo;
- fecha de solicitud;
- fecha de autorización;
- fecha límite;
- auditoría;
- nuevo hash si afecta al contenido firmado.

---

## 12. Facturación y bloqueo definitivo

Cuando un parte se marca como facturado:

1. Se registra `facturado = true`.
2. Se guarda `facturado_at`.
3. Se activa `bloqueado_definitivo`.
4. Se registra auditoría.
5. Se guarda exportación si procede.
6. No se permite modificación directa del parte.

Si existe un error en un parte ya facturado, debe resolverse con un proceso correctivo futuro, no editando directamente el registro original.

---

## 13. Exportación a facturación

La tabla `exportacion_facturacion` registra envíos a sistemas externos.

Campos clave:

```txt
id_exportacion
id_empresa
tipo_origen
id_origen
sistema_destino
estado
payload_exportado
respuesta_externa
fecha_exportacion
created_at
```

Tipos de origen:

- parte_trabajo;
- presupuesto;
- suscripcion;
- cliente;
- material.

Estados recomendados:

- pendiente;
- exportado;
- error;
- reintentado;
- cancelado.

---

## 14. Niveles de auditoría

## Auditoría mínima

Registra:

- usuario;
- empresa;
- entidad;
- acción;
- fecha;
- descripción.

## Auditoría recomendable

Registra además:

- valores anteriores;
- valores nuevos;
- IP;
- user agent.

## Auditoría avanzada

Registra además:

- contexto de petición;
- correlación de eventos;
- hash de evento;
- cadena de auditoría.

Para la primera versión profesional, se recomienda al menos el nivel recomendable.

---

## 15. Reglas de protección de auditoría

1. La auditoría no debe editarse desde la interfaz normal.
2. Solo roles autorizados deben consultarla.
3. El técnico no debe ver auditoría global.
4. El jefe de departamento puede ver auditoría de su ámbito si se permite.
5. El administrador y administrador_jefe pueden ver auditoría de su empresa.
6. No se debe borrar auditoría sin política definida.
7. Los errores internos no deben exponerse al usuario final.
8. Las acciones fallidas relevantes también pueden registrarse.

---

## 16. Recomendación de implementación

Crear un servicio central:

```txt
AuditLogger
```

Uso conceptual:

```txt
AuditLogger::registrar(
  empresaId,
  usuarioId,
  entidad,
  entidadId,
  accion,
  descripcion,
  valoresAnteriores,
  valoresNuevos
)
```

Crear también:

```txt
HashService
```

Responsabilidades:

- calcular hash de firma;
- calcular hash de parte;
- normalizar payload;
- guardar versión;
- comparar integridad.

---

## 17. Qué no debe hacerse

1. No modificar partes facturados directamente.
2. No sobrescribir firmas sin auditoría.
3. No borrar auditoría manualmente.
4. No guardar contraseñas en auditoría.
5. No registrar datos sensibles innecesarios.
6. No confiar en datos enviados por frontend para auditoría crítica.
7. No usar IDs hardcodeados.
8. No devolver errores SQL al usuario final.
9. No dejar endpoints críticos sin middleware.
10. No mezclar datos de empresas.

