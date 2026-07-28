# estrategia_ramas.md

# EasyParte — Estrategia de ramas

## 1. Propósito

Este documento define cómo deben usarse las ramas de Git en EasyParte.

El objetivo es que la profesionalización del proyecto no se haga directamente sobre `master` y que cada cambio sea revisable, reversible y entendible.

---

## 2. Principio general

No trabajar directamente en `master`.

Toda modificación debe hacerse en una rama concreta según el tipo de tarea.
Las nuevas ramas de trabajo deben crearse desde `develop` actualizada.

---

## 3. Ramas principales

## master

Representa la versión estable del proyecto.

`master` es el nombre real de esta rama en el repositorio. Las referencias
históricas a `main` deben interpretarse y actualizarse como `master`; no existe
una rama principal distinta llamada `main`.

Reglas:

- debe estar siempre funcional;
- no se hacen pruebas directamente aquí;
- no se suben cambios sin revisar;
- se usa para versiones estables;
- debe poder desplegarse o servir como punto seguro.

## develop

Rama de integración.

Reglas:

- recibe cambios de ramas feature, fix, refactor, docs y security;
- es la base obligatoria para crear nuevas ramas de trabajo ordinarias;
- recibe como destino los Pull Requests ordinarios;
- puede tener trabajo en curso, pero no debe estar completamente rota;
- se prueba antes de preparar una release;
- sirve como base para nuevas ramas.

## Estado tras REP-GIT-001

El 2026-07-28 se corrigió la deuda entre las ramas principales:

- `develop` estaba en `19c6d19` y era ancestro de `master`;
- `master` contenía 19 commits exclusivos y `develop` no contenía ninguno;
- `develop` avanzó mediante fast-forward puro de `19c6d19` a `61ab382`;
- no se creó merge commit;
- no se usó `reset`, `rebase` ni force push;
- tras el push, `master`, `origin/master`, `develop` y `origin/develop`
  apuntaban a `61ab3828ee56c936dc4e657ee92bba30eb86e1c3`;
- la divergencia `origin/master...origin/develop` quedó en `0 0`.

Esta alineación no convierte `master` en rama de trabajo. Restablece
`develop` como rama de integración y base de las tareas siguientes.

---

## 4. Ramas de trabajo

## feature/*

Para nuevas funcionalidades.

Ejemplos:

```txt
feature/presupuestos-basicos
feature/usuarios-multiempresa
feature/asignacion-varios-tecnicos
feature/firma-parte
```

## fix/*

Para corregir errores.

Ejemplos:

```txt
fix/tecnico-ve-partes-ajenas
fix/error-login-json
fix/cors-duplicado
fix/dashboard-horas-tecnico
```

## refactor/*

Para mejorar código sin cambiar comportamiento funcional.

Ejemplos:

```txt
refactor/backend-services
refactor/nombres-avisos
refactor/repositories-pdo
refactor/frontend-core-shared
```

## security/*

Para cambios de seguridad.

Ejemplos:

```txt
security/validar-roles-backend
security/cors-produccion
security/ocultar-errores-sql
security/jwt-secret-env
```

## docs/*

Para documentación.

Ejemplos:

```txt
docs/agent-contexto-limpio
docs/endpoints-api
docs/despliegue-produccion
docs/testing
```

## release/*

Para preparar una versión estable.

Ejemplos:

```txt
release/1.0.0
release/1.1.0
```

## hotfix/*

Para corregir un error crítico sobre producción.

Ejemplos:

```txt
hotfix/login-produccion
hotfix/fallo-permisos-tecnico
hotfix/error-base-datos
```

---

## 5. Flujo normal de trabajo

Flujo recomendado:

```txt
master
  ↓
develop
  ↓
feature/nombre-tarea
  ↓
Pull Request / revisión
  ↓
develop
  ↓
release/x.y.z
  ↓
master
```

---

## 6. Flujo para correcciones

Para un bug normal:

```txt
develop
  ↓
fix/nombre-error
  ↓
develop
```

Para un bug crítico en producción:

```txt
master
  ↓
hotfix/nombre-error
  ↓
master
  ↓
develop
```

El hotfix debe fusionarse también a `develop` para que no se pierda.

---

## 7. Nombres de ramas

Formato:

```txt
tipo/descripcion-corta
```

Reglas:

- usar minúsculas;
- usar guiones;
- no usar espacios;
- no usar tildes;
- no usar nombres genéricos como `prueba`, `cambios`, `final`.

Buenos ejemplos:

```txt
feature/presupuestos-basicos
fix/albaranes-tecnico
refactor/auth-middleware
security/tenant-backend
docs/roles-permisos
```

Malos ejemplos:

```txt
cambios
final
arreglo
prueba2
cosas-nuevas
```

---

## 8. Tamaño de ramas

Las ramas deben ser pequeñas.

Una rama debe intentar resolver una sola cosa.

Correcto:

```txt
fix/tecnico-ve-partes-ajenas
```

Incorrecto:

```txt
feature/refactor-completo-backend-frontend-bbdd-y-dashboard
```

---

## 9. Ramas y base de datos

Si una rama cambia base de datos, debe indicarlo claramente.

Ejemplo:

```txt
feature/modelo-presupuestos
refactor/empresa-usuario-roles
```

Debe incluir:

- script SQL;
- migración si se usa;
- explicación;
- impacto;
- datos afectados;
- si requiere backup.

Regla:

No aplicar cambios destructivos en base de datos sin aprobación.

---

## 10. Ramas y documentación

Si una rama cambia lógica de negocio, roles, endpoints, base de datos o despliegue, debe actualizar documentación.

Ejemplos:

- si cambia endpoints, actualizar `endpoints_api.md`;
- si cambia roles, actualizar `roles_y_permisos.md`;
- si cambia entidades, actualizar `entidades.md`;
- si cambia despliegue, actualizar `despliegue.md`.

---

## 11. Pull Request recomendado

Cada rama importante debería terminar en Pull Request.

Los Pull Requests ordinarios de ramas `feature/*`, `fix/*`, `refactor/*`,
`security/*`, `docs/*` y equivalentes deben apuntar a `develop`. Los Pull
Requests hacia `master` se reservan para releases o hotfixes revisados.

Plantilla recomendada:

```txt
## Resumen
Qué cambia.

## Motivo
Por qué se hace.

## Archivos modificados
Lista de zonas importantes.

## Pruebas realizadas
Qué se ha probado.

## Riesgos
Qué puede romperse.

## Documentación
Qué documentos se han actualizado.
```

---

## 12. Reglas para Codex

Cuando Codex trabaje:

1. Debe crear o usar una rama concreta.
2. Debe crear las ramas ordinarias desde `develop` actualizada.
3. No debe trabajar directamente en `master` ni en `develop`.
4. No debe mezclar muchas tareas.
5. Debe explicar los cambios.
6. Debe dejar pasos de prueba.
7. Debe actualizar documentación si procede.
8. Debe pedir revisión para cambios críticos.

---

## 13. Inicio de una tarea nueva

Antes de crear una rama de trabajo:

```bash
git switch develop
git pull --ff-only origin develop
git switch -c docs/agent-contexto-limpio
```

La rama creada debe abrir su Pull Request ordinario contra `develop`.

---

## 14. Primeras ramas recomendadas

Orden recomendado:

```txt
docs/agent-contexto-limpio
docs/despliegue-testing
refactor/estructura-backend
security/auth-middleware
security/tenant-empresa
feature/modelo-saas
feature/usuarios-multiempresa
feature/presupuestos-basicos
feature/partes-firma-hash
```

---

## 15. Cuándo fusionar a master

Solo fusionar a `master` cuando:

- la app compila;
- backend responde;
- pruebas críticas pasan;
- documentación actualizada;
- no hay errores críticos;
- se ha revisado el diff;
- se ha hecho backup si hay cambios de base de datos;
- se ha preparado release.

---

## 16. Versionado recomendado

Usar versionado semántico cuando el proyecto madure:

```txt
MAJOR.MINOR.PATCH
```

Ejemplos:

```txt
1.0.0
1.1.0
1.1.1
```

Significado:

- MAJOR: cambios grandes o incompatibles.
- MINOR: nuevas funcionalidades.
- PATCH: correcciones.

---

## 17. Conclusión

La estrategia de ramas debe proteger el proyecto.

Regla clave:

```txt
Cambios pequeños, ramas claras, `develop` integra y `master` permanece estable.
```
