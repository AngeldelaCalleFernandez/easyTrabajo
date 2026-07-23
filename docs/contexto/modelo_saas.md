# modelo_saas.md

# EasyParte — Modelo SaaS

## 1. Objetivo del modelo SaaS

EasyParte debe funcionar como un producto por suscripción. Una empresa contrata un plan y, en función de ese plan, puede usar más o menos recursos de la aplicación.

El sistema debe poder responder a estas preguntas:

- qué empresa usa EasyParte;
- qué plan tiene contratado;
- si la suscripción está activa;
- cuántos usuarios puede crear;
- cuántos avisos, partes y clientes puede gestionar;
- si ha superado los límites;
- si debe bloquearse o limitarse el acceso.

---

## 2. Empresa cliente

La tabla `empresa` representa a la empresa cliente que usa EasyParte.

Cada empresa tiene sus propios:

- usuarios;
- empleados;
- departamentos;
- equipos;
- clientes;
- avisos;
- presupuestos;
- partes;
- materiales;
- auditoría;
- exportaciones.

Regla principal:

> Una empresa nunca debe poder ver ni modificar datos de otra empresa.

---

## 3. Planes

Los planes se definen por tramos de usuarios.

Planes previstos:

| Plan | Usuarios | Límites principales |
|---|---:|---|
| Free / prueba | hasta 2 usuarios | 5 avisos, 5 partes, 15 clientes |
| Básico | 0-5 usuarios | límite según configuración comercial |
| Medio | 6-15 usuarios | límite según configuración comercial |
| Superior | 16-30 usuarios | límite según configuración comercial |
| 30+ / negociar | más de 30 usuarios | requiere contacto comercial |

Los campos recomendados para `plan` son:

- id_plan;
- nombre;
- tipo;
- min_usuarios;
- max_usuarios;
- max_avisos;
- max_partes;
- max_clientes;
- precio_mensual;
- requiere_contacto_comercial;
- activo;
- created_at;
- updated_at.

---

## 4. Suscripción

La tabla `suscripcion` vincula una empresa con un plan.

Una empresa puede tener varias suscripciones históricas, aunque normalmente solo tendrá una activa en cada momento.

Estados recomendados:

- prueba;
- activa;
- pendiente_pago;
- cancelada;
- caducada;
- bloqueada.

Campos recomendados:

- id_suscripcion;
- id_empresa;
- id_plan;
- estado;
- fecha_inicio;
- fecha_fin;
- renovacion_automatica;
- created_at;
- updated_at.

---

## 5. Control de límites

Antes de permitir determinadas acciones, el sistema debe comprobar los límites del plan.

Acciones que deben comprobar límites:

- crear usuario;
- crear cliente;
- crear aviso;
- crear parte;
- activar usuario;
- cambiar a un plan inferior;
- reactivar suscripción.

Ejemplo:

Si una empresa está en plan Free y ya tiene 2 usuarios activos, no debe poder crear un tercer usuario.

Si una empresa está en plan Free y ya tiene 5 avisos, no debe poder crear un sexto aviso salvo que actualice el plan.

---

## 6. Usuarios en varias empresas

Un usuario puede estar vinculado a varias empresas mediante `empresa_usuario`.

Esto permite casos como:

- un administrador que gestiona varias empresas;
- un administrador jefe vinculado a varias empresas;
- un consultor externo que trabaja con varias empresas.

La tabla `empresa_usuario` debe guardar:

- id_empresa_usuario;
- id_empresa;
- id_usuario;
- id_empleado;
- estado;
- bloqueado;
- bloqueado_por;
- bloqueado_at;
- created_at;
- updated_at;
- deleted_at.

El bloqueo debe ser por empresa, no necesariamente global.

Ejemplo:

Un usuario puede estar bloqueado en Empresa A pero seguir activo en Empresa B.

---

## 7. Roles por empresa

Los roles no deben asignarse de forma totalmente global al usuario, porque un mismo usuario puede tener roles distintos según la empresa.

Ejemplo:

- En Empresa A, Ángel es administrador_jefe.
- En Empresa B, Ángel es administrador.
- En Empresa C, Ángel solo tiene lectura.

Para eso se usa:

- `empresa_usuario`;
- `rol`;
- `empresa_usuario_rol`.

---

## 8. Acceso y suscripción

En cada inicio de sesión o acceso a módulo crítico, el backend debe comprobar:

1. Usuario autenticado.
2. Usuario activo globalmente.
3. Vinculación activa con empresa.
4. Usuario no bloqueado en esa empresa.
5. Empresa activa.
6. Suscripción activa o en prueba.
7. Plan no excedido.
8. Rol suficiente para la acción.

El frontend puede ocultar opciones visualmente, pero la validación real debe hacerla siempre el backend.

---

## 9. Plan Free / prueba

La suscripción Free o de prueba debe estar limitada.

Límites definidos:

- 2 usuarios.
- 15 clientes.
- 5 avisos.
- 5 partes.

Pendiente de decisión:

- duración temporal de la prueba;
- si se permite exportar a facturación en Free;
- si se permite firma de partes en Free;
- si el plan Free caduca o es gratuito permanente con límites.

---

## 10. Facturación de suscripciones

Pendiente de decisión.

La idea futura es que la aplicación pueda gestionar la facturación de suscripciones desde dentro y enviar registros a un programa externo de facturación o contabilidad.

Por ahora debe existir la base para registrar:

- qué se exporta;
- a qué sistema;
- cuándo;
- estado de la exportación;
- respuesta externa.

Esto se gestiona mediante `exportacion_facturacion`.

---

## 11. Reglas del modelo SaaS

1. Toda empresa debe tener una suscripción o registro equivalente.
2. Una empresa sin suscripción activa puede tener acceso limitado o bloqueado.
3. Los límites del plan se comprueban en backend.
4. La empresa no puede ver datos de otra empresa.
5. Un usuario puede trabajar en varias empresas.
6. Los roles se asignan por empresa.
7. Un usuario puede estar bloqueado en una empresa y activo en otra.
8. Los cambios de plan y suscripción deben auditarse.
9. Las acciones relacionadas con límites deben registrarse si son rechazadas.
10. La facturación completa queda como fase futura, pero la exportación debe estar prevista.

