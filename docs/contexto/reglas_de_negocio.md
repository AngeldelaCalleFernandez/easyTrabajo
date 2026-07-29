# reglas_de_negocio.md

# EasyParte — Reglas de negocio

## 1. Reglas generales

1. EasyParte es una aplicación multiempresa.
2. Cada empresa solo puede acceder a sus propios datos.
3. Toda consulta sensible debe filtrar por `id_empresa`.
4. El frontend nunca debe ser la única capa de seguridad.
5. Toda validación crítica debe repetirse en backend.
6. Las acciones importantes deben quedar registradas en auditoría.
7. No se deben borrar datos críticos sin trazabilidad.
8. Los registros importantes deben usar `created_at`, `updated_at` y, cuando proceda, `deleted_at`.
9. El borrado lógico es preferible al borrado físico en entidades de negocio.
10. Si falta información de negocio, no debe inventarse: debe registrarse como duda pendiente.

---

## 2. Reglas de empresa y suscripción

1. Una empresa debe tener una suscripción para usar EasyParte.
2. La suscripción puede estar en prueba, activa, pendiente de pago, cancelada, caducada o bloqueada.
3. Una empresa con suscripción bloqueada no debe poder operar con normalidad.
4. Una empresa en plan Free tiene límites reducidos.
5. El backend debe comprobar límites antes de crear usuarios, clientes, avisos o partes.
6. Los cambios de plan deben quedar auditados.
7. Una empresa no debe poder superar su plan sin autorización o cambio de suscripción.
8. El plan 30+ requiere contacto o negociación comercial.
9. Las acciones bloqueadas por límites deben devolver un error claro.
10. La aplicación debe diferenciar entre empresa activa e inactiva.

---

## 3. Reglas de usuarios

1. Un usuario puede estar vinculado a una o varias empresas.
2. Un usuario puede tener roles diferentes en cada empresa.
3. El bloqueo de un usuario puede aplicarse por empresa.
4. Un usuario bloqueado en una empresa no debe acceder a los datos de esa empresa.
5. Un usuario globalmente inactivo no debe acceder a ninguna empresa.
6. Un usuario puede estar vinculado a un empleado, pero no es obligatorio.
7. No todos los empleados tienen por qué tener usuario.
8. El email de usuario debe ser único de forma controlada.
9. La contraseña debe guardarse siempre con hash seguro.
10. Nunca se debe almacenar una contraseña en texto plano.

---

## 4. Reglas de roles

Roles definidos:

- administrador_jefe;
- administrador;
- jefe_departamento;
- jefe_equipo;
- atencion_cliente;
- tecnico;
- solo_lectura.

Reglas:

1. El administrador_jefe es el rol superior dentro de una empresa.
2. Puede haber administradores vinculados a varias empresas.
3. Los administradores pueden ser bloqueados por el administrador_jefe dentro de una empresa.
4. El jefe de departamento tiene autoridad sobre su departamento.
5. El jefe de equipo tiene autoridad sobre su equipo.
6. El técnico solo debe ver sus avisos, partes o trabajos asignados.
7. Atención al cliente puede crear clientes y avisos, pero no debe tener permisos técnicos avanzados salvo autorización.
8. Solo lectura puede consultar información, pero no modificarla.
9. La asignación de roles debe ser por empresa, no solo global.
10. El backend debe comprobar permisos en cada endpoint.

---

## 5. Reglas de clientes

1. Un cliente pertenece siempre a una empresa.
2. Una empresa puede tener muchos clientes.
3. Un cliente puede tener muchos avisos.
4. Un cliente puede recibir presupuestos.
5. Un cliente puede estar activo o dado de baja.
6. Los clientes dados de baja no deben eliminarse físicamente si tienen avisos, presupuestos o partes asociados.
7. No se debe permitir acceder a clientes de otra empresa.
8. El NIF, email o teléfono pueden validarse, pero no deben impedir registrar un cliente si el negocio permite datos incompletos.
9. Los cambios importantes en un cliente deben auditarse.
10. Los clientes podrán exportarse a facturación en fases futuras.

---

## 6. Reglas de avisos

1. Un aviso pertenece siempre a una empresa.
2. Un aviso normalmente pertenece a un cliente.
3. Un aviso puede estar vinculado a un departamento.
4. Un aviso lo crea un usuario.
5. En el modelo actual, un aviso está libre o tiene un único empleado en
   `tarea.id_empleado`.
6. La relación múltiple mediante `aviso_empleado` sigue siendo un objetivo
   futuro y no debe presentarse como implementada.
7. Un aviso puede estar inicialmente sin técnico asignado.
8. Un aviso puede generar uno o varios partes de trabajo.
9. Un aviso puede tener uno o varios presupuestos.
10. Un aviso cancelado debe conservarse; cancelar, asignar, reasignar o coger un
    aviso no implica borrado físico.

Reglas actuales de asignación:

1. La edición general mediante `PUT /api/avisos/{id}` no puede cambiar
   `id_empleado`.
2. Asignar o reasignar a un empleado concreto se realiza mediante
   `PUT /api/avisos/{id}/asignar`.
3. Coger un aviso libre es una acción distinta y usa
   `PUT /api/avisos/{id}/coger`; el empleado procede del contexto autenticado.
4. `Administrador` y `Atencion al Cliente` pueden asignar o reasignar avisos
   dentro de su empresa a empleados activos de esa empresa.
5. `Tecnico` solo puede reasignar un aviso que ya esté asignado a su propio
   `id_empleado`, y debe elegir otro empleado activo de la misma empresa.
6. `Tecnico` no puede reasignar avisos ajenos, libres ni asignarse el aviso a
   sí mismo.
7. `Finalizada` y `Cancelada` son estados terminales. Ningún rol puede asignar,
   reasignar ni coger un aviso en esos estados. El backend rechaza la operación
   con HTTP 403 antes de modificar datos o registrar auditoría.
8. La asignación, reasignación y toma de aviso correctas registran auditoría con
   la empresa, el usuario, el aviso y los valores anterior y nuevo de
   `id_empleado`.
9. Los rechazos de autorización deben producirse antes de modificar el aviso.
10. La cancelación sigue siendo una acción independiente mediante
    `PUT /api/avisos/{id}/cancelar`.
11. No existe un flujo de reapertura de avisos dentro de los contratos de
    asignación o toma.

Estados recomendados:

- pendiente;
- asignado;
- en_proceso;
- finalizado;
- cancelado.

Reglas de visibilidad:

- Administrador jefe y administrador pueden ver todos los avisos de su empresa.
- Jefe de departamento puede ver avisos de su departamento.
- Jefe de equipo puede ver avisos de su equipo.
- En el modelo actual, Técnico puede ver sus avisos y los avisos libres que
  puede coger; no debe ver avisos asignados a otros técnicos.
- Atención al cliente puede ver y crear avisos según permisos de empresa.

---

## 7. Reglas de presupuestos

1. Un presupuesto pertenece siempre a una empresa.
2. Un presupuesto pertenece siempre a un cliente.
3. Un presupuesto puede estar vinculado a un aviso, pero no es obligatorio.
4. Un aviso puede tener varios presupuestos.
5. Un presupuesto contiene una o varias líneas.
6. Las líneas pueden ser de mano de obra, material, desplazamiento, servicio u otros conceptos.
7. Una línea puede vincularse a un material del catálogo o ser manual.
8. Un presupuesto aceptado puede generar trabajo real.
9. Un presupuesto rechazado no debe eliminarse sin trazabilidad.
10. Todo cambio de estado debe auditarse.
11. Un presupuesto puede exportarse a facturación.

Estados recomendados:

- borrador;
- enviado;
- aceptado;
- rechazado;
- caducado;
- convertido;
- cancelado.

---

## 8. Reglas de partes de trabajo

1. Un parte de trabajo pertenece siempre a una empresa.
2. Un parte normalmente se vincula a un aviso.
3. Un parte puede estar vinculado a un presupuesto aceptado.
4. Un parte pertenece a un cliente.
5. Un parte puede tener varios empleados participantes.
6. La participación de empleados se registra mediante `parte_empleado`.
7. Las horas se registran mediante `parte_hora`.
8. Los materiales se registran mediante `parte_material`.
9. Un parte puede incluir observaciones.
10. Un parte puede ser firmado por el cliente.
11. Al cerrar un parte debe generarse una huella de integridad.
12. Un parte facturado queda bloqueado definitivamente.

Estados recomendados:

- abierto;
- en_curso;
- pausado;
- cerrado;
- anulado.

---

## 9. Reglas de rectificación de partes

1. Un técnico puede rectificar un parte cerrado durante los 7 días posteriores al cierre.
2. Pasados los 7 días, solo un administrador, administrador_jefe o jefe_departamento autorizado puede liberar una rectificación.
3. Una rectificación debe registrar motivo.
4. Una rectificación debe indicar quién la solicita.
5. Una rectificación autorizada debe indicar quién la autoriza.
6. Una rectificación rechazada debe quedar registrada.
7. Un parte facturado no debe rectificarse directamente.
8. Si un parte facturado requiere corrección, debe hacerse mediante documento correctivo o proceso futuro definido.
9. La rectificación debe generar auditoría.
10. El hash de integridad debe recalcularse o versionarse si se modifica contenido relevante.

---

## 10. Reglas de firma y hash

1. La firma del cliente se guarda como imagen o referencia de archivo.
2. Debe guardarse el hash de la firma.
3. Debe registrarse el algoritmo usado para el hash.
4. Debe registrarse la fecha de firma.
5. Si es posible, debe registrarse IP y user agent.
6. Al cerrar un parte debe generarse hash de integridad del contenido relevante.
7. El hash anterior puede guardarse para permitir cadena de integridad.
8. Una modificación posterior debe generar nueva versión o evento auditable.
9. No se debe sobrescribir una firma sin trazabilidad.
10. La firma y el hash deben formar parte del cierre del parte.

---

## 11. Reglas de materiales

1. En primera fase, un material puede escribirse manualmente en un parte o presupuesto.
2. A futuro, los materiales deben poder venir de un catálogo.
3. Un material pertenece a una empresa.
4. El stock debe controlarse por empresa.
5. Los movimientos de almacén deben registrar entradas, salidas, ajustes y devoluciones.
6. Una línea de parte puede referenciar un material o ser manual.
7. Una línea de presupuesto puede referenciar un material o ser manual.
8. Los materiales deben poder importarse y exportarse en el futuro.
9. Los materiales deben poder comunicarse con sistemas externos de facturación o ERP.
10. El módulo de almacén avanzado puede dejarse para una fase posterior.

---

## 12. Reglas de facturación/exportación

1. EasyParte debe poder exportar partes a sistemas de facturación.
2. EasyParte debe poder exportar presupuestos.
3. A futuro puede exportar suscripciones, clientes y materiales.
4. La exportación debe registrar sistema destino.
5. La exportación debe guardar estado.
6. Debe guardarse el payload enviado.
7. Debe guardarse la respuesta externa si existe.
8. Un fallo de exportación no debe borrar datos.
9. Un parte facturado queda bloqueado definitivamente.
10. La facturación completa queda pendiente de decisión.

---

## 13. Reglas de auditoría

1. Toda acción crítica debe generar auditoría.
2. La auditoría debe guardar usuario, empresa, entidad, entidad_id, acción y fecha.
3. Debe guardar una descripción legible.
4. Cuando proceda, debe guardar valores anteriores y nuevos en JSON.
5. Los valores JSON permiten reconstruir qué cambió exactamente.
6. Los datos de auditoría no deben editarse desde la interfaz normal.
7. Los datos de auditoría no deben borrarse salvo política legal definida.
8. El acceso a auditoría debe estar restringido.
9. Los cambios de roles, permisos y suscripciones deben auditarse.
10. Los cierres, rectificaciones y facturaciones de partes deben auditarse.

---

## 14. Reglas de seguridad

1. La autenticación debe ser real, no simulada.
2. El backend debe validar token o sesión en cada endpoint protegido.
3. La autorización debe comprobarse en backend.
4. El frontend puede ocultar botones, pero no sustituye a la seguridad backend.
5. No debe haber IDs hardcodeados como usuario actual o empleado actual.
6. El backend debe extraer usuario, empresa y roles desde token/sesión validada.
7. Los errores internos no deben exponerse al cliente.
8. CORS debe configurarse de forma restrictiva en producción.
9. Los secretos deben estar en variables de entorno.
10. Las consultas a base de datos deben usar PDO preparado o mecanismo seguro equivalente.

