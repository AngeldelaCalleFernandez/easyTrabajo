# casos_prueba.md

# EasyParte — Casos de prueba

## 1. Propósito

Este documento define casos de prueba funcionales, técnicos y de seguridad para EasyParte.

Cada caso debe ejecutarse en local o staging antes de considerar una versión lista para producción.

---

## 2. Formato de caso de prueba

Formato recomendado:

```txt
ID:
Nombre:
Prioridad:
Rol:
Entorno:
Precondiciones:
Pasos:
Resultado esperado:
Resultado obtenido:
Estado:
Observaciones:
```

Estados posibles:

```txt
Pendiente
Correcta
Fallida
Bloqueada
No aplica
```

---

# 3. Autenticación

## CP-AUTH-001 — Login correcto

Prioridad: Crítica  
Rol: usuario válido

Precondiciones:

- Existe un usuario activo.
- La empresa está activa.
- La suscripción está activa o en prueba.

Pasos:

1. Abrir pantalla de login.
2. Introducir email correcto.
3. Introducir contraseña correcta.
4. Enviar formulario.

Resultado esperado:

- El backend valida credenciales.
- Se crea sesión o token.
- Se carga usuario actual.
- Se redirige al dashboard.

---

## CP-AUTH-002 — Login incorrecto

Prioridad: Crítica  
Rol: usuario no autenticado

Pasos:

1. Abrir login.
2. Introducir email correcto y contraseña incorrecta.
3. Enviar.

Resultado esperado:

- No se inicia sesión.
- Se muestra mensaje genérico.
- No se informa si el email existe.

---

## CP-AUTH-003 — Acceso sin autenticación

Prioridad: Crítica

Pasos:

1. Acceder directamente a una ruta protegida.
2. Acceder directamente a un endpoint protegido sin token/sesión.

Resultado esperado:

- Frontend redirige a login.
- Backend devuelve error de no autenticado.

---

## CP-AUTH-004 — Usuario bloqueado

Prioridad: Crítica

Precondiciones:

- Usuario bloqueado en una empresa.

Pasos:

1. Iniciar sesión.
2. Intentar acceder a la empresa bloqueada.

Resultado esperado:

- Acceso bloqueado.
- No puede ver datos de esa empresa.

---

# 4. Multiempresa y permisos

## CP-TENANT-001 — Empresa A no ve datos de Empresa B

Prioridad: Crítica

Precondiciones:

- Existen Empresa A y Empresa B.
- Ambas tienen clientes, avisos y partes.

Pasos:

1. Iniciar sesión como usuario de Empresa A.
2. Consultar clientes.
3. Consultar avisos.
4. Consultar partes.
5. Intentar acceder por URL o ID a un recurso de Empresa B.

Resultado esperado:

- Solo se ven datos de Empresa A.
- El backend bloquea acceso a datos de Empresa B.

---

## CP-TENANT-002 — Usuario en varias empresas

Prioridad: Alta

Precondiciones:

- Usuario vinculado a Empresa A y Empresa B.
- Tiene roles distintos en cada empresa.

Pasos:

1. Iniciar sesión.
2. Seleccionar Empresa A.
3. Comprobar permisos.
4. Cambiar a Empresa B.
5. Comprobar permisos.

Resultado esperado:

- Los permisos cambian según empresa activa.
- No se mezclan datos.

---

# 5. Roles

## CP-ROL-001 — Técnico solo ve sus avisos

Prioridad: Crítica

Precondiciones:

- Técnico 1 tiene aviso asignado.
- Técnico 2 tiene otro aviso asignado.

Pasos:

1. Iniciar sesión como Técnico 1.
2. Consultar avisos.
3. Intentar acceder al aviso de Técnico 2.

Resultado esperado:

- Técnico 1 solo ve sus avisos.
- Backend bloquea aviso de Técnico 2.

---

## CP-ROL-002 — Técnico solo ve sus partes

Prioridad: Crítica

Pasos:

1. Iniciar sesión como Técnico 1.
2. Consultar partes.
3. Intentar abrir un parte de Técnico 2.

Resultado esperado:

- Solo aparecen partes propias o donde participa.
- Acceso directo a parte ajeno queda bloqueado.

---

## CP-ROL-003 — Administrador ve todos los datos de su empresa

Prioridad: Alta

Pasos:

1. Iniciar sesión como administrador.
2. Consultar clientes.
3. Consultar avisos.
4. Consultar partes.

Resultado esperado:

- Ve todos los datos de su empresa.
- No ve datos de otras empresas.

---

## CP-ROL-004 — Solo lectura no modifica datos

Prioridad: Alta

Pasos:

1. Iniciar sesión como solo_lectura.
2. Intentar crear cliente.
3. Intentar editar aviso.
4. Intentar cerrar parte.

Resultado esperado:

- No puede modificar datos.
- Backend devuelve error de permisos.

---

# 6. Suscripciones y planes

## CP-SUS-001 — Plan Free dentro de límites

Prioridad: Alta

Precondiciones:

- Empresa con plan Free.
- Tiene menos de 2 usuarios, 15 clientes, 5 avisos y 5 partes.

Pasos:

1. Crear cliente.
2. Crear aviso.
3. Crear parte.

Resultado esperado:

- Se permite mientras no supere límites.

---

## CP-SUS-002 — Plan Free supera límite de usuarios

Prioridad: Crítica

Precondiciones:

- Empresa Free con 2 usuarios activos.

Pasos:

1. Intentar crear tercer usuario.

Resultado esperado:

- Backend bloquea acción.
- Se muestra mensaje claro.

---

## CP-SUS-003 — Empresa con suscripción bloqueada

Prioridad: Crítica

Pasos:

1. Iniciar sesión en empresa bloqueada.
2. Intentar crear aviso.

Resultado esperado:

- Se bloquea el acceso o la acción.
- No se crea el aviso.

---

# 7. Clientes

## CP-CLI-001 — Crear cliente

Prioridad: Alta

Pasos:

1. Iniciar sesión como rol permitido.
2. Abrir clientes.
3. Crear cliente con datos válidos.

Resultado esperado:

- Cliente creado.
- Aparece en listado.
- Se registra empresa correcta.
- Se genera auditoría.

---

## CP-CLI-002 — Baja lógica de cliente

Prioridad: Alta

Pasos:

1. Seleccionar cliente.
2. Dar de baja.

Resultado esperado:

- No se borra físicamente.
- Queda inactivo o con deleted_at.
- No aparece como activo.
- Se genera auditoría.

---

# 8. Avisos

## CP-AVI-001 — Crear aviso

Prioridad: Alta

Pasos:

1. Crear aviso asociado a cliente.
2. Indicar prioridad y descripción.

Resultado esperado:

- Aviso creado en estado pendiente.
- Asociado a empresa y cliente correctos.
- Auditoría registrada.

---

## CP-AVI-002 — Asignar varios técnicos

Prioridad: Alta

Pasos:

1. Abrir aviso.
2. Asignar Técnico 1.
3. Asignar Técnico 2.

Resultado esperado:

- Se crean registros en aviso_empleado.
- Ambos técnicos ven el aviso.
- Se genera auditoría.

---

## CP-AVI-003 — Cambiar estado de aviso

Prioridad: Media

Pasos:

1. Cambiar aviso de pendiente a asignado.
2. Cambiar a en_proceso.
3. Cambiar a finalizado.

Resultado esperado:

- Cambios válidos.
- Auditoría registrada.
- Estado visual correcto.

---

# 9. Presupuestos

## CP-PRE-001 — Crear presupuesto básico

Prioridad: Alta

Pasos:

1. Crear presupuesto para cliente.
2. Añadir línea de mano de obra.
3. Añadir línea de material.
4. Guardar.

Resultado esperado:

- Se calcula total.
- Estado inicial borrador.
- Se vincula a empresa y cliente.

---

## CP-PRE-002 — Aceptar presupuesto

Prioridad: Alta

Pasos:

1. Abrir presupuesto enviado.
2. Marcar como aceptado.

Resultado esperado:

- Estado aceptado.
- Se registra aceptado_at.
- Auditoría registrada.

---

## CP-PRE-003 — Convertir presupuesto aceptado

Prioridad: Alta

Pasos:

1. Presupuesto en estado aceptado.
2. Convertir en aviso o parte.

Resultado esperado:

- Se vincula correctamente.
- No se pierde el presupuesto.
- Se registra convertido_en_parte_at si procede.

---

# 10. Partes de trabajo

## CP-PAR-001 — Crear parte desde aviso

Prioridad: Alta

Pasos:

1. Abrir aviso.
2. Crear parte.
3. Añadir descripción.

Resultado esperado:

- Parte creado en estado abierto.
- Vinculado a aviso, cliente y empresa.

---

## CP-PAR-002 — Parte con varios empleados

Prioridad: Alta

Pasos:

1. Abrir parte.
2. Añadir Técnico 1.
3. Añadir Técnico 2.

Resultado esperado:

- Ambos quedan vinculados mediante parte_empleado.

---

## CP-PAR-003 — Registrar horas

Prioridad: Alta

Pasos:

1. Abrir parte.
2. Añadir tramo de horas.
3. Guardar.

Resultado esperado:

- Se registra parte_hora.
- Se recalcula horas_total si procede.

---

## CP-PAR-004 — Registrar materiales

Prioridad: Alta

Pasos:

1. Abrir parte.
2. Añadir material manual.
3. Guardar.

Resultado esperado:

- Se registra parte_material.
- Se calcula total si hay precio.

---

## CP-PAR-005 — Firmar parte

Prioridad: Crítica

Pasos:

1. Abrir parte.
2. Registrar firma.
3. Guardar.

Resultado esperado:

- Se guarda imagen/ruta.
- Se genera hash_firma.
- Se registra fecha_firma.
- Auditoría registrada.

---

## CP-PAR-006 — Cerrar parte

Prioridad: Crítica

Pasos:

1. Abrir parte con datos completos.
2. Cerrar parte.

Resultado esperado:

- Estado cerrado.
- Se registra cerrado_at.
- Se registra cerrado_por.
- Se genera parte_hash.
- Se genera auditoría.

---

## CP-PAR-007 — Rectificar dentro de 7 días

Prioridad: Alta

Pasos:

1. Parte cerrado recientemente.
2. Técnico asignado intenta rectificar.

Resultado esperado:

- Se permite según reglas.
- Se registra rectificación.
- Se actualiza hash si cambia contenido relevante.

---

## CP-PAR-008 — Rectificar fuera de 7 días

Prioridad: Alta

Pasos:

1. Parte cerrado hace más de 7 días.
2. Técnico intenta rectificar.

Resultado esperado:

- No se permite directamente.
- Requiere autorización.

---

## CP-PAR-009 — Parte facturado bloqueado

Prioridad: Crítica

Pasos:

1. Marcar parte como facturado.
2. Intentar editarlo.

Resultado esperado:

- No se permite edición directa.
- bloqueado_definitivo activo.
- Auditoría registrada.

---

# 11. Auditoría

## CP-AUD-001 — Auditoría al crear aviso

Prioridad: Alta

Pasos:

1. Crear aviso.
2. Consultar auditoría.

Resultado esperado:

- Existe evento con usuario, empresa, entidad aviso y acción crear.

---

## CP-AUD-002 — Auditoría con valores anteriores y nuevos

Prioridad: Alta

Pasos:

1. Cambiar prioridad de aviso.
2. Consultar auditoría.

Resultado esperado:

- valores_anteriores contiene prioridad anterior.
- valores_nuevos contiene prioridad nueva.

---

# 12. Exportación a facturación

## CP-EXP-001 — Exportar parte

Prioridad: Media

Pasos:

1. Seleccionar parte cerrado.
2. Crear exportación.

Resultado esperado:

- Se crea registro en exportacion_facturacion.
- Estado pendiente/exportado/error.
- Se guarda payload.

---

# 13. Dashboard

## CP-DASH-001 — Dashboard administrador

Prioridad: Media

Pasos:

1. Iniciar sesión como administrador.
2. Abrir dashboard.

Resultado esperado:

- Ve métricas de empresa.
- No ve datos de otras empresas.

---

## CP-DASH-002 — Dashboard técnico

Prioridad: Media

Pasos:

1. Iniciar sesión como técnico.
2. Abrir dashboard.

Resultado esperado:

- Ve solo sus avisos, partes y horas.

---

# 14. Producción

## CP-PROD-001 — Errores internos ocultos

Prioridad: Crítica

Pasos:

1. Provocar error controlado.
2. Revisar respuesta.

Resultado esperado:

- Usuario ve mensaje genérico.
- Detalle técnico va a logs.

---

## CP-PROD-002 — CORS restringido

Prioridad: Crítica

Pasos:

1. Hacer petición desde origen no permitido.
2. Revisar respuesta.

Resultado esperado:

- Origen no permitido queda bloqueado.

---

## CP-PROD-003 — Backup antes de despliegue

Prioridad: Crítica

Pasos:

1. Ejecutar procedimiento de backup.
2. Confirmar archivo generado.
3. Confirmar restauración en staging si procede.

Resultado esperado:

- Backup válido.
- Restauración posible.
