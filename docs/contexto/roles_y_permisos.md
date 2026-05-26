# roles_y_permisos.md

# EasyParte — Roles y permisos

## 1. Objetivo

Este documento define los roles de usuario y los permisos principales de EasyParte.

La regla más importante es que los permisos deben validarse siempre en backend. El frontend puede ocultar botones o rutas, pero nunca debe ser la única barrera de seguridad.

---

## 2. Modelo de roles

Los roles se asignan por empresa.

Un mismo usuario puede tener un rol en una empresa y otro rol distinto en otra empresa.

Ejemplo:

- Usuario A es administrador_jefe en Empresa 1.
- Usuario A es administrador en Empresa 2.
- Usuario B es técnico en Empresa 1.
- Usuario C es jefe_departamento en Empresa 1.

Esto se modela con:

- `usuario`;
- `empresa`;
- `empresa_usuario`;
- `rol`;
- `empresa_usuario_rol`.

---

## 3. Roles definidos

Roles principales:

1. administrador_jefe
2. administrador
3. jefe_departamento
4. jefe_equipo
5. atencion_cliente
6. tecnico
7. solo_lectura

No se define inicialmente un superadmin global de EasyParte. El rol más alto dentro de una empresa es `administrador_jefe`.

---

## 4. Administrador jefe

El `administrador_jefe` es el responsable principal de una empresa dentro de EasyParte.

Puede haber casos donde un mismo administrador jefe esté vinculado a varias empresas.

Permisos recomendados:

- gestionar configuración de la empresa;
- gestionar usuarios de la empresa;
- gestionar empleados;
- asignar y quitar roles;
- bloquear administradores dentro de su empresa;
- gestionar departamentos;
- gestionar equipos;
- ver todos los clientes;
- ver todos los avisos;
- ver todos los presupuestos;
- ver todos los partes;
- autorizar rectificaciones fuera de plazo;
- consultar auditoría de la empresa;
- ver estado de suscripción;
- solicitar cambio de plan;
- exportar datos a facturación, si el plan lo permite.

Restricciones:

- No debe ver datos de otras empresas salvo que también esté vinculado a ellas.
- No debe modificar datos internos de EasyParte como plataforma si no existe rol específico futuro.
- No debe eliminar datos críticos sin trazabilidad.

---

## 5. Administrador

El `administrador` gestiona la operativa de la empresa, pero está por debajo del administrador jefe.

Permisos recomendados:

- crear y editar clientes;
- crear y editar avisos;
- asignar técnicos;
- crear presupuestos;
- gestionar partes;
- ver todos los avisos y partes de la empresa;
- gestionar empleados, si el administrador jefe lo permite;
- consultar dashboard general;
- exportar partes o presupuestos si tiene permiso;
- autorizar algunas rectificaciones según configuración.

Restricciones:

- Puede ser bloqueado por el administrador_jefe dentro de una empresa.
- No debe poder bloquear al administrador_jefe.
- No debe cambiar la suscripción salvo permiso explícito.
- No debe ver datos de otras empresas.

---

## 6. Jefe de departamento

El `jefe_departamento` gestiona los avisos, técnicos y partes de su departamento.

Permisos recomendados:

- ver avisos de su departamento;
- asignar técnicos de su departamento;
- ver partes de su departamento;
- revisar trabajo de técnicos;
- autorizar rectificaciones fuera de plazo de su departamento;
- consultar métricas de su departamento;
- crear o revisar presupuestos si la empresa lo permite.

Restricciones:

- No debe gestionar departamentos ajenos salvo permiso.
- No debe cambiar roles globales.
- No debe gestionar suscripción.
- No debe acceder a datos de otra empresa.

---

## 7. Jefe de equipo

El `jefe_equipo` gestiona un grupo más pequeño de técnicos.

Permisos recomendados:

- ver avisos asignados a su equipo;
- ver partes de su equipo;
- repartir trabajo dentro del equipo;
- consultar estado de avisos del equipo;
- revisar partes de técnicos del equipo;
- solicitar rectificaciones.

Restricciones:

- No debe gestionar toda la empresa.
- No debe modificar usuarios o roles.
- No debe gestionar suscripciones.
- No debe ver partes de otros equipos salvo permiso.

---

## 8. Atención al cliente

El rol `atencion_cliente` está orientado a registrar solicitudes y gestionar la relación inicial con clientes.

Permisos recomendados:

- crear clientes;
- editar datos básicos de clientes;
- crear avisos;
- consultar estado de avisos;
- añadir observaciones iniciales;
- crear presupuestos básicos si la empresa lo permite;
- consultar presupuestos enviados al cliente.

Restricciones:

- No debe cerrar partes técnicos.
- No debe modificar horas de técnicos.
- No debe autorizar rectificaciones.
- No debe gestionar roles.
- No debe acceder a auditoría completa.
- No debe modificar suscripciones.

---

## 9. Técnico

El `tecnico` ejecuta trabajos asignados.

Permisos recomendados:

- ver avisos asignados a él;
- ver partes donde participa;
- crear o completar partes de trabajo;
- registrar horas;
- registrar materiales usados;
- añadir observaciones;
- capturar firma del cliente;
- cerrar parte si tiene permiso;
- rectificar parte cerrado dentro del plazo de 7 días.

Restricciones:

- No debe ver avisos de otros técnicos salvo que estén en el mismo equipo y tenga permiso.
- No debe ver todos los partes de la empresa.
- No debe modificar clientes salvo datos mínimos autorizados.
- No debe gestionar usuarios.
- No debe gestionar roles.
- No debe modificar suscripción.
- No debe rectificar partes fuera de plazo sin autorización.
- No debe modificar partes facturados.

---

## 10. Solo lectura

El rol `solo_lectura` sirve para usuarios que deben consultar información sin modificarla.

Permisos recomendados:

- ver clientes autorizados;
- ver avisos autorizados;
- ver partes autorizados;
- ver presupuestos autorizados;
- consultar dashboard limitado.

Restricciones:

- No puede crear.
- No puede editar.
- No puede eliminar.
- No puede asignar.
- No puede cerrar partes.
- No puede firmar partes.
- No puede exportar a facturación salvo permiso específico.

---

## 11. Matriz inicial de permisos

| Acción | administrador_jefe | administrador | jefe_departamento | jefe_equipo | atencion_cliente | tecnico | solo_lectura |
|---|---:|---:|---:|---:|---:|---:|---:|
| Ver dashboard empresa | Sí | Sí | Parcial | Parcial | Parcial | Parcial | Parcial |
| Gestionar empresa | Sí | No/Parcial | No | No | No | No | No |
| Ver suscripción | Sí | Parcial | No | No | No | No | No |
| Cambiar plan | Sí | No/Con permiso | No | No | No | No | No |
| Crear usuarios | Sí | Sí/Con permiso | No | No | No | No | No |
| Bloquear usuarios | Sí | Parcial | No | No | No | No | No |
| Asignar roles | Sí | Parcial | No | No | No | No | No |
| Crear empleados | Sí | Sí | No/Parcial | No | No | No | No |
| Crear departamentos | Sí | Sí/Con permiso | No | No | No | No | No |
| Crear equipos | Sí | Sí | Sí/Parcial | No | No | No | No |
| Crear clientes | Sí | Sí | Sí/Parcial | No/Parcial | Sí | No | No |
| Editar clientes | Sí | Sí | Sí/Parcial | No/Parcial | Sí/Parcial | No | No |
| Crear avisos | Sí | Sí | Sí | Sí/Parcial | Sí | No/Parcial | No |
| Ver todos los avisos | Sí | Sí | Solo departamento | Solo equipo | Según permiso | Solo asignados | Según permiso |
| Asignar técnicos | Sí | Sí | Sí departamento | Sí equipo | No | No | No |
| Crear presupuesto | Sí | Sí | Sí/Con permiso | No/Parcial | Sí/Con permiso | No | No |
| Aprobar presupuesto internamente | Sí | Sí | Sí/Parcial | No | No | No | No |
| Crear parte | Sí | Sí | Sí | Sí | No | Sí asignado | No |
| Editar parte abierto | Sí | Sí | Sí departamento | Sí equipo | No | Sí asignado | No |
| Cerrar parte | Sí | Sí | Sí departamento | Sí equipo | No | Sí asignado | No |
| Rectificar parte dentro de 7 días | Sí | Sí | Sí | Sí equipo | No | Sí asignado | No |
| Liberar rectificación fuera de plazo | Sí | Sí/Con permiso | Sí departamento | No/Con permiso | No | No | No |
| Modificar parte facturado | No directo | No directo | No directo | No directo | No | No | No |
| Exportar a facturación | Sí | Sí/Con permiso | No/Parcial | No | No/Parcial | No | No |
| Ver auditoría | Sí | Sí/Limitado | Limitado | No | No | No | No |

---

## 12. Reglas técnicas de autorización

1. Cada endpoint debe declarar qué roles pueden ejecutarlo.
2. El backend debe resolver el usuario autenticado desde token o sesión.
3. El backend debe resolver la empresa activa.
4. El backend debe comprobar que el usuario está vinculado a esa empresa.
5. El backend debe comprobar que el usuario no está bloqueado.
6. El backend debe comprobar la suscripción de la empresa.
7. El backend debe comprobar el rol en esa empresa.
8. El backend debe aplicar filtros por `id_empresa`.
9. El backend debe aplicar filtros por departamento, equipo o empleado cuando proceda.
10. El frontend nunca debe enviar `id_empresa` como fuente de verdad.

---

## 13. Reglas específicas de visibilidad

### Técnico

Debe ver:

- avisos asignados directamente;
- partes donde participa;
- partes de avisos donde está asignado;
- información mínima del cliente necesaria para hacer el trabajo.

No debe ver:

- avisos de otros técnicos;
- dashboard global de horas;
- partes de toda la empresa;
- datos administrativos.

### Jefe de equipo

Debe ver:

- técnicos de su equipo;
- avisos del equipo;
- partes del equipo;
- métricas del equipo.

### Jefe de departamento

Debe ver:

- empleados de su departamento;
- equipos del departamento;
- avisos del departamento;
- partes del departamento;
- métricas del departamento.

### Administrador y administrador_jefe

Pueden ver toda la información de la empresa, respetando siempre el límite de empresa.

---

## 14. Bloqueo de usuarios

Un usuario puede estar:

- activo globalmente;
- inactivo globalmente;
- activo en una empresa;
- bloqueado en una empresa.

El bloqueo por empresa se guarda en `empresa_usuario`.

Ejemplo:

Un administrador puede estar bloqueado en Empresa A por el administrador_jefe, pero seguir trabajando en Empresa B.

Todo bloqueo debe registrar:

- quién bloqueó;
- cuándo;
- motivo si se añade en fase posterior;
- empresa afectada.

---

## 15. Recomendación para implementación

Crear un middleware o servicio de autorización que centralice las comprobaciones.

Ejemplo conceptual:

```txt
AuthMiddleware
RoleMiddleware
TenantMiddleware
SubscriptionMiddleware
PermissionService
```

Ningún controlador debería decidir permisos de forma improvisada.

