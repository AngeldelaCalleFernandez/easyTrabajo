# decisiones_tecnicas.md

# EasyParte — Decisiones técnicas

## 1. Propósito del documento

Este documento recoge las decisiones técnicas tomadas para la profesionalización de EasyParte.

Su objetivo es evitar que cada agente, desarrollador o sesión de trabajo tome decisiones distintas sobre arquitectura, nombres, seguridad, base de datos o estilo.

Cuando una decisión no esté cerrada, debe marcarse como pendiente y no inventarse.

---

## 2. Stack decidido

## Frontend

Decisión:

- Angular 21+.
- Standalone Components.
- Signals.
- Reactive Forms.
- Tailwind CSS.
- Chart.js.

Motivo:

El proyecto actual ya está construido en Angular y el objetivo es profesionalizarlo, no cambiar de tecnología sin necesidad.

## Backend

Decisión:

- PHP vanilla orientado a objetos.
- API REST.
- Front Controller.
- PDO.
- Estructura por controllers, services, repositories, middleware y helpers.

Motivo:

El proyecto actual usa PHP vanilla. Se mantiene para no convertir la profesionalización en una migración completa a otro framework.

## Base de datos

Decisión:

- MariaDB/MySQL.
- Modelo relacional.
- Claves foráneas.
- Borrado lógico.
- Auditoría.
- Hash de integridad.

Motivo:

Encaja con XAMPP, el proyecto actual y el tipo de aplicación de gestión.

---

## 3. Decisión sobre arquitectura backend

Decisión:

Separar el backend en capas:

```txt
index.php
api.php
middlewares
controllers
services
repositories
helpers
database
```

Los controladores no deben contener consultas SQL complejas ni reglas de negocio extensas.

Motivo:

Facilita mantenimiento, pruebas, seguridad y crecimiento.

---

## 4. Decisión sobre autenticación

Estado: pendiente de decisión final.

Opciones:

## Opción A — JWT gestionado en cliente

Ventajas:

- más simple;
- encaja con la versión actual;
- fácil de probar con API REST.

Riesgos:

- exposición si se guarda en localStorage/sessionStorage;
- requiere cuidar expiración y renovación.

## Opción B — Cookie HttpOnly

Ventajas:

- más segura frente a acceso por JavaScript;
- más recomendable para producción.

Riesgos:

- más compleja con CORS;
- requiere configuración correcta de SameSite, Secure y dominio.

Decisión provisional:

Documentar ambas opciones. Para producción se recomienda estudiar cookie HttpOnly, pero no cambiar sin plan técnico claro.

---

## 5. Decisión sobre roles

Decisión:

Los roles se asignan por empresa, no de forma global al usuario.

Modelo:

```txt
usuario
empresa
empresa_usuario
rol
empresa_usuario_rol
```

Motivo:

Un usuario puede estar en varias empresas y tener roles distintos en cada una.

---

## 6. Decisión sobre administrador superior

Decisión:

No se define inicialmente un `superadmin_easyparte`.

El rol superior será:

```txt
administrador_jefe
```

Este rol está vinculado a una o varias empresas.

Motivo:

El usuario ha definido que no quiere un superadmin global de EasyParte en esta fase, sino un administrador jefe de empresa.

Pendiente:

Definir en el futuro cómo se administrará internamente la plataforma EasyParte si hay muchas empresas cliente.

---

## 7. Decisión sobre usuarios multiempresa

Decisión:

Un usuario puede estar vinculado a varias empresas.

Motivo:

Se necesita permitir que un administrador_jefe o administrador pueda gestionar varias empresas.

Consecuencia:

No debe usarse `usuario.id_empresa` como única relación principal.

Debe usarse `empresa_usuario`.

---

## 8. Decisión sobre planes

Decisión:

Los planes se cobran por tramos de usuarios.

Planes definidos:

- Free / prueba.
- Básico: 0-5 usuarios.
- Medio: 6-15 usuarios.
- Superior: 16-30 usuarios.
- 30+ negociar.

Plan Free definido:

- 2 usuarios.
- 15 clientes.
- 5 avisos.
- 5 partes.

Pendiente:

- duración del plan Free;
- precio de cada plan;
- si se limita exportación o firma en plan Free.

---

## 9. Decisión sobre avisos

Decisión:

Un aviso puede tener varios técnicos asignados.

Modelo:

```txt
aviso
aviso_empleado
empleado
```

Motivo:

El trabajo real puede requerir varios técnicos.

Consecuencia:

No usar un único `id_empleado_asignado` como solución final.

---

## 10. Decisión sobre partes

Decisión:

Un parte puede tener varios empleados.

Modelo:

```txt
parte_trabajo
parte_empleado
parte_hora
```

Motivo:

Un parte puede registrar trabajo de más de un técnico y varias franjas de horas.

---

## 11. Decisión sobre presupuestos

Decisión:

Los presupuestos básicos forman parte del núcleo de EasyParte.

Modelo:

```txt
presupuesto
presupuesto_linea
presupuesto_historial_estado
```

Motivo:

El usuario ha indicado que realizar presupuestos de trabajos es importante.

Consecuencia:

El E/R y la arquitectura deben contemplarlo desde el inicio, aunque la implementación pueda ser básica.

---

## 12. Decisión sobre materiales

Decisión:

Los materiales podrán registrarse manualmente en partes y presupuestos.

A futuro podrán vincularse a catálogo y almacén.

Modelo:

```txt
material
parte_material
presupuesto_linea
movimiento_almacen
```

Motivo:

Se quiere preparar el sistema para almacén e importación/exportación con facturación.

---

## 13. Decisión sobre firma

Decisión:

La firma del cliente se guarda como imagen o referencia de archivo y debe tener hash.

Modelo:

```txt
parte_firma
```

Motivo:

Se quiere preservar evidencia de firma e integridad.

Pendiente:

Definir si la firma tendrá validez legal avanzada o solo conformidad operativa.

---

## 14. Decisión sobre hash de parte

Decisión:

Al cerrar un parte, se genera hash de integridad.

Modelo:

```txt
parte_hash
```

Motivo:

Permite detectar modificaciones posteriores sobre partes cerrados.

---

## 15. Decisión sobre rectificación

Decisión:

Los partes cerrados pueden rectificarse durante 7 días por el técnico.

Pasado el plazo, debe liberar la rectificación un rol autorizado.

Si el parte está facturado, queda bloqueado definitivamente.

Motivo:

Equilibra operativa real y control de integridad.

---

## 16. Decisión sobre facturación

Estado: parcialmente decidido.

Decisión actual:

EasyParte debe poder exportar datos a facturación, pero no será inicialmente un programa completo de facturación.

Modelo:

```txt
exportacion_facturacion
```

Pendiente:

- programa destino;
- formato;
- API o CSV;
- gestión real de pagos de suscripciones.

---

## 17. Decisión sobre auditoría

Decisión:

La auditoría debe guardar descripción legible y valores anteriores/nuevos en JSON cuando proceda.

Modelo:

```txt
auditoria_evento
```

Motivo:

Permite leer el cambio fácilmente y reconstruir datos técnicos.

---

## 18. Decisión sobre errores API

Decisión:

Las respuestas API deben ser homogéneas.

Formato recomendado:

```json
{
  "success": true,
  "data": {},
  "message": "Operación realizada correctamente"
}
```

Errores:

```json
{
  "success": false,
  "error": {
    "code": "FORBIDDEN",
    "message": "No tienes permisos para realizar esta acción"
  }
}
```

No se deben devolver trazas internas ni errores SQL completos.

---

## 19. Decisión sobre CORS

Decisión:

En desarrollo puede ser más abierto, pero en producción debe restringirse.

Pendiente:

Definir dominios finales:

- FRONTEND_URL;
- API_URL;
- CORS_ALLOWED_ORIGINS.

---

## 20. Decisión sobre despliegue

Estado: pendiente.

Decisiones por cerrar:

- hosting;
- dominio;
- HTTPS;
- base de datos gestionada o servidor propio;
- backups;
- logs;
- variables de entorno;
- CI/CD;
- ramas Git.

---

## 21. Decisión sobre módulos futuros

Decisión:

Flota, almacén avanzado, solicitudes de materiales y proyectos tipo Trello/Notion no entran en el núcleo inicial.

Se documentan en `modulos_futuros.md`.

---

## 22. Regla general

Si una decisión afecta a seguridad, base de datos, roles, facturación o pérdida de datos, no debe aplicarse sin revisión humana.
