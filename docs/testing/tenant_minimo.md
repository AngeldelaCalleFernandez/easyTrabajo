# Validacion manual - tenant minimo

Fecha de creacion: 2026-05-22
Alcance: primera fase minima de PEN-0006.

Nota general: Estas pruebas fueron ejecutadas manualmente por la persona responsable del proyecto. Codex solo registra los resultados aportados.

Objetivo: comprobar que las fugas directas de empresa quedan mitigadas sin redisenar el modelo multiempresa ni crear TenantMiddleware.

Datos de prueba recomendados:

- Guia: `AGENT/testing/datos_prueba_multiempresa.md`.
- Seed opcional no destructivo: `bbdd/seed_multiempresa_pruebas.sql`.
- No ejecutar el seed en produccion.

## Pruebas tecnicas realizadas

- `php -l backend/controllers/EmpleadoController.php`: correcto.
- `php -l backend/controllers/AvisoController.php`: correcto.
- `php -l backend/controllers/ParteTrabajoController.php`: correcto.
- Busqueda en frontend: `frontend/src/app/features/avisos/avisos.ts` y `frontend/src/app/features/administracion/administracion.ts` ya no contienen `id_empresa`.
- `npm run build` en `frontend`: correcto tras ejecutar fuera del sandbox por fallo inicial `spawn EPERM`.

## Pruebas manuales recomendadas

### TENANT-MIN-001 - Listado de empleados por empresa

- Objetivo: confirmar que `GET /api/empleados` solo devuelve empleados de la empresa del token.
- Precondiciones: disponer de datos de dos empresas y un administrador de cada una. Puede usarse el seed `bbdd/seed_multiempresa_pruebas.sql`.
- Pasos:
  1. Iniciar sesion como administrador de Empresa A.
  2. Consultar `GET /api/empleados`.
  3. Verificar que no aparece ningun empleado de Empresa B.
  4. Repetir como administrador de Empresa B.
- Resultado esperado: cada empresa solo ve sus empleados.
- Resultado obtenido: Empresa A solo ve empleados de Empresa A; Empresa B solo ve empleados de Empresa B.
- Estado: correcto

### TENANT-MIN-002 - Crear aviso con cliente de otra empresa

- Objetivo: confirmar que no se puede crear un aviso con `id_cliente` ajeno.
- Precondiciones: token de Empresa A y `id_cliente` perteneciente a Empresa B. Con el seed opcional: Empresa A usa cliente `9101` y Empresa B usa cliente `9102`.
- Pasos:
  1. Enviar `POST /api/avisos` con descripcion valida y `id_cliente` de Empresa B.
  2. Revisar respuesta.
- Resultado esperado: HTTP 403 con mensaje generico, sin detalles internos.
- Resultado obtenido: HTTP 403 al intentar crear un aviso con cliente de otra empresa.
- Estado: correcto
- Evidencia resumida: `9102` pertenece a Empresa B.
- Validación:
  El backend impide crear un aviso usando un `id_cliente` ajeno a la empresa del usuario autenticado.

- Conclusión:
  Correcto. El aislamiento multiempresa se aplica en la creación de avisos.

### TENANT-MIN-003 - Crear aviso con empleado/departamento de otra empresa

- Objetivo: confirmar que no se puede asignar un aviso a empleado o departamento ajeno.
- Precondiciones: token de Empresa A, empleado/departamento de Empresa B y cliente valido de Empresa A. Con el seed opcional: empleado/departamento A `9101`, empleado/departamento B `9102`.
- Pasos:
  1. Enviar `POST /api/avisos` con `id_empleado` de Empresa B.
  2. Repetir con `id_departamento` de Empresa B si existen departamentos.
- Resultado esperado: HTTP 403 con mensaje generico, sin detalles internos.
- Resultado obtenido: HTTP 403 al crear un aviso con empleado o departamento de otra empresa.
- Estado: correcto
- Evidencia resumida: la validación se aplicó contra empleado y departamento ajenos.
- Validación:
  El backend impide crear un aviso usando un `id_empleado` o `id_departamento` ajeno a la empresa del usuario autenticado.

- Conclusión:
  Correcto. El aislamiento multiempresa se aplica en la creación de avisos.

### TENANT-MIN-004 - Crear parte con recurso relacionado de otra empresa

- Objetivo: confirmar que no se puede crear un parte con cliente, tarea o empleado ajeno.
- Precondiciones: token de Empresa A y recursos de Empresa B. Con el seed opcional: recursos A `9101`, recursos B `9102`.
- Pasos:
  1. Enviar `POST /api/partes` con `id_cliente` de Empresa B.
  2. Repetir con `id_tarea` de Empresa B.
  3. Repetir con `id_empleado` de Empresa B.
- Resultado esperado: HTTP 403 con mensaje generico, sin detalles internos.
- Resultado obtenido: HTTP 403 al crear un parte con cliente, tarea o empleado de otra empresa.
- Estado: correcto
- Evidencia resumida: la validación se aplicó contra cliente, tarea y empleado ajenos.
- Validación:
  El backend impide crear un parte usando un `id_cliente`, `id_tarea` o `id_empleado` ajeno a la empresa del usuario autenticado.

- Conclusión:
  Correcto. El aislamiento multiempresa se aplica en la creación de partes.

### TENANT-MIN-005 - Regresion local

- Objetivo: confirmar que login, dashboard, clientes, avisos, partes y administracion siguen funcionando.
- Precondiciones: XAMPP local y datos actuales.
- Pasos:
  1. Iniciar sesion.
  2. Abrir dashboard.
  3. Abrir clientes.
  4. Crear/editar aviso con cliente y empleado de la misma empresa.
  5. Crear/editar parte con recursos de la misma empresa.
  6. Abrir administracion y crear/editar empleado.
- Resultado esperado: flujos locales siguen funcionando sin enviar `id_empresa` desde frontend.
- Resultado obtenido: login, dashboard, clientes, avisos, partes y administracion siguen funcionando sin enviar `id_empresa` desde frontend.
- Estado: correcto
- Evidencia resumida: confirmación en red/navegador de que el frontend no envía `id_empresa`.

## Tabla de ejecucion manual

No pegar tokens completos ni contrasenas reales. En `token usado`, indicar solo `Token Empresa A`, `Token Empresa B` o una referencia interna no sensible.

| ID | Endpoint o pantalla | Token usado | Payload usado | Resultado esperado | Resultado obtenido | Estado | Notas |
| --- | --- | --- | --- | --- | --- | --- | --- |
| TENANT-MIN-001 | `GET /api/empleados` | Token Empresa A y Token Empresa B | No aplica | Empresa A solo ve `Empleado Tenant A`; Empresa B solo ve `Empleado Tenant B`. | Empresa A solo ve empleados de Empresa A; Empresa B solo ve empleados de Empresa B. | correcto | Usar seed `bbdd/seed_multiempresa_pruebas.sql` si se necesitan datos de dos empresas. |
| TENANT-MIN-002 | `POST /api/avisos` | Token Empresa A | `{"descripcion":"Prueba tenant: cliente ajeno","id_cliente":9102}` | HTTP 403 con mensaje generico, sin detalles internos. | HTTP 403 al intentar crear un aviso con cliente de otra empresa. | correcto | `9102` pertenece a Empresa B. |
| TENANT-MIN-003 | `POST /api/avisos` | Token Empresa A | `{"descripcion":"Prueba tenant: empleado ajeno","id_cliente":9101,"id_empleado":9102}` y `{"descripcion":"Prueba tenant: departamento ajeno","id_cliente":9101,"id_departamento":9102}` | HTTP 403 con mensaje generico, sin detalles internos. | HTTP 403 al crear un aviso con empleado o departamento de otra empresa. | correcto | Probar empleado y departamento por separado. |
| TENANT-MIN-004 | `POST /api/partes` | Token Empresa A | `{"descripcion":"Prueba tenant: parte cliente ajeno","id_cliente":9102}`, `{"descripcion":"Prueba tenant: parte tarea ajena","id_cliente":9101,"id_tarea":9102,"id_empleado":9101}` y `{"descripcion":"Prueba tenant: parte empleado ajeno","id_cliente":9101,"id_empleado":9102}` | HTTP 403 con mensaje generico, sin detalles internos. | HTTP 403 al crear un parte con cliente, tarea o empleado de otra empresa. | correcto | Probar cliente, tarea y empleado ajenos por separado. |
| TENANT-MIN-005 | Login, dashboard, clientes, avisos, partes y administracion | Token/sesion local valida | Aviso valido: `{"descripcion":"Prueba tenant: aviso valido","id_cliente":9101,"id_empleado":9101,"id_departamento":9101}`. Parte valido: `{"descripcion":"Prueba tenant: parte valido","id_cliente":9101,"id_tarea":9101,"id_empleado":9101,"horas":1}` | Flujos locales funcionan y los payloads no incluyen `id_empresa`. | Login, dashboard, clientes, avisos, partes y administracion siguen funcionando sin enviar `id_empresa` desde frontend. | correcto | Confirmado en red/navegador que el frontend no envia `id_empresa`. |
