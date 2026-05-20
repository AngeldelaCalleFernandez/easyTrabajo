# estrategia_ramas.md

# EasyParte — Estrategia de ramas

## 1. Propósito

Este documento define cómo deben usarse las ramas de Git en EasyParte.

El objetivo es que la profesionalización del proyecto no se haga directamente sobre `main` y que cada cambio sea revisable, reversible y entendible.

---

## 2. Principio general

No trabajar directamente en `main`.

Toda modificación debe hacerse en una rama concreta según el tipo de tarea.

---

## 3. Ramas principales

## main

Representa la versión estable del proyecto.

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
- puede tener trabajo en curso, pero no debe estar completamente rota;
- se prueba antes de preparar una release;
- sirve como base para nuevas ramas.

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
main
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
main
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
main
  ↓
hotfix/nombre-error
  ↓
main
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
2. No debe trabajar en `main`.
3. No debe mezclar muchas tareas.
4. Debe explicar los cambios.
5. Debe dejar pasos de prueba.
6. Debe actualizar documentación si procede.
7. Debe pedir revisión para cambios críticos.

---

## 13. Estrategia recomendada inicial

Para empezar la profesionalización:

```bash
git checkout main
git pull
git checkout -b develop
git push -u origin develop
```

Luego crear ramas desde `develop`:

```bash
git checkout develop
git pull
git checkout -b docs/agent-contexto-limpio
```

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

## 15. Cuándo fusionar a main

Solo fusionar a `main` cuando:

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
Cambios pequeños, ramas claras, main estable.
```
