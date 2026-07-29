# repositorio.md

# EasyParte — Repositorio

## 1. Propósito del documento

Este documento define la información básica del repositorio de EasyParte y las normas de trabajo con Git/GitHub.

Su objetivo es evitar cambios desordenados, proteger la rama principal y facilitar que Codex, ChatGPT o cualquier agente trabajen de forma controlada.

---

## 2. Repositorio principal

Repositorio actual:

```txt
https://github.com/AngeldelaCalleFernandez/easyTrabajo
```

Nombre del proyecto/repo:

```txt
easyTrabajo
```

Nombre del producto:

```txt
EasyParte
```

La diferencia entre ambos nombres debe tenerse clara:

- `easyTrabajo` es el nombre técnico actual del repositorio/proyecto.
- `EasyParte` es el nombre del producto/aplicación.

---

## 3. Estado del repositorio

Estado actual:

```txt
Proyecto TFC funcional en proceso de profesionalización.
```

Objetivo:

```txt
Convertir EasyParte en una aplicación más profesional, mantenible, segura y preparada para despliegue.
```

El repositorio actual sirve como base, pero no debe tratarse como producto final listo para producción.

---

## 4. Stack del proyecto

Stack actual/propuesto:

```txt
Frontend: Angular
Backend: PHP vanilla orientado a objetos
Base de datos: MariaDB/MySQL
Estilos: Tailwind CSS
Gráficos: Chart.js
API: REST
```

---

## 5. Estructura esperada del repositorio

Estructura orientativa:

```txt
easyTrabajo/
├── .github/
│   ├── workflows/
│   ├── scripts/
│   └── pull_request_template.md
├── frontend/
├── backend/
├── bbdd/
├── README.md
├── README-FRONTEND.md
├── README-BACKEND.md
└── AGENT/
```

La carpeta `AGENT/` será la documentación de gobierno del proyecto cuando se traslade al repositorio.

---

## 6. Documentación principal

Antes de modificar código, se debe leer la documentación limpia del proyecto.

Documentos principales:

```txt
negocio.md
modelo_saas.md
reglas_de_negocio.md
roles_y_permisos.md
arquitectura.md
trazabilidad_y_auditoria.md
estilo_visual.md
objetivos.md
alcance.md
decisiones_tecnicas.md
entidades.md
dudas_pendientes.md
glosario.md
estados_del_sistema.md
endpoints_api.md
roadmap_funcional.md
modulos_futuros.md
```

Documentos de despliegue:

```txt
despliegue.md
entornos.md
variables_entorno.md
seguridad_produccion.md
backup_y_restauracion.md
```

Documentos de testing:

```txt
testing.md
casos_prueba.md
checklist_pre_produccion.md
```

Documentos de Git:

```txt
repositorio.md
estrategia_ramas.md
convenciones_commits.md
proteccion_ramas.md
```

---

## 7. Norma principal de trabajo

No trabajar directamente sobre `master`.

Toda tarea debe hacerse en una rama específica creada desde `develop`
actualizada.

Ejemplos:

```txt
feature/presupuestos-basicos
fix/tecnico-ve-partes-ajenas
refactor/backend-services
docs/actualizar-agent
security/cors-produccion
```

---

## 8. Ramas principales

Ramas recomendadas:

```txt
master
develop
feature/*
fix/*
refactor/*
docs/*
security/*
testing/*
release/*
hotfix/*
```

Resumen:

- `master`: versión estable y nombre real de la rama principal del repositorio.
- `develop`: integración de cambios.
- `feature/*`: nuevas funcionalidades.
- `fix/*`: correcciones.
- `refactor/*`: mejoras internas sin cambiar comportamiento.
- `docs/*`: documentación.
- `security/*`: cambios de seguridad.
- `testing/*`: pruebas, fixtures y automatización de validaciones.
- `release/*`: preparación de versión.
- `hotfix/*`: correcciones urgentes sobre producción.

---

## 9. Reglas para Codex/agentes

Cualquier agente que trabaje sobre el repositorio debe cumplir:

1. Leer documentación antes de tocar código.
2. Crear las ramas de trabajo ordinarias desde `develop` actualizada.
3. No modificar directamente `master` ni `develop`.
4. Dirigir los Pull Requests ordinarios a `develop`.
5. Trabajar por tareas pequeñas.
6. Explicar qué archivos va a tocar.
7. No cambiar la arquitectura sin justificarlo.
8. No inventar reglas de negocio.
9. No eliminar código o columnas sin revisión humana.
10. No introducir secretos.
11. No exponer errores internos.
12. Documentar cambios relevantes.

---

## 10. Flujo recomendado de trabajo

Flujo básico:

```txt
1. Crear rama desde develop.
2. Aplicar cambios pequeños.
3. Probar localmente.
4. Actualizar documentación si procede.
5. Hacer commit claro.
6. Abrir Pull Request o revisión.
7. Revisar diff.
8. Fusionar a develop.
9. Pasar pruebas.
10. Preparar release hacia master.
```

---

## 11. Pull Requests

Cada Pull Request debería incluir:

```txt
Resumen del cambio
Motivo
Archivos principales modificados
Pruebas realizadas
Riesgos
Capturas si afecta a frontend
Migraciones si afecta a base de datos
Documentación actualizada
```

Destino:

- los Pull Requests ordinarios deben apuntar a `develop`;
- los Pull Requests hacia `master` quedan reservados para releases y hotfixes
  revisados;
- una tarea nueva no debe usar `master` como base mientras `develop` esté
  alineada y operativa.

Desde REP-GIT-003-CI existe una plantilla común en
`.github/pull_request_template.md`. Todavía no existe `CODEOWNERS`, porque no
se ha confirmado un segundo revisor real.

---

## 12. Reglas de protección recomendadas

REP-GIT-002-DOC define las recomendaciones completas en
[`proteccion_ramas.md`](proteccion_ramas.md). No se ha confirmado ni modificado
la protección remota.

Para ambas ramas se recomienda bloquear eliminación y force push, exigir Pull
Request y resolución de conversaciones, mantener merge commits y no exigir
historial lineal.

Además:

- `master` se reserva para Pull Requests desde `release/*`, `hotfix/*` o
  `develop`;
- `develop` recibe los Pull Requests ordinarios desde `feature/*`, `fix/*`,
  `refactor/*`, `security/*`, `docs/*` y `testing/*`;
- las aprobaciones obligatorias quedan pendientes hasta confirmar un segundo
  revisor real;
- existe un `quality-gate` informativo integrado en `develop`, pero los status
  checks obligatorios quedan pendientes hasta completar su estabilización;
- nunca debe activarse como obligatorio un check inexistente;
- se recomienda borrar automáticamente las ramas fusionadas, con comprobaciones
  adicionales para `release/*` y `hotfix/*`.

---

## 13. Qué no debe subirse al repositorio

No subir:

```txt
.env
contraseñas
claves API
tokens
backups
archivos privados
node_modules
vendor si no procede
logs
firmas reales
datos personales reales
exports reales
```

Debe existir `.gitignore`.

---

## 14. Archivos recomendados

Archivos útiles para añadir al repositorio:

```txt
.env.example
.gitignore
README.md
README-FRONTEND.md
README-BACKEND.md
AGENT/
```

---

## 15. Relación con Drive

Drive se usa como biblioteca y fuente documental.

GitHub se usa como fuente de código.

Regla:

```txt
Drive contiene contexto, documentación, diagramas y referencias.
GitHub contiene código, configuración versionable y documentación integrada.
```

Los documentos estables de Drive pueden copiarse a `/AGENT` dentro del repositorio cuando se empiece la profesionalización con Codex.

---

## 16. Estado de las ramas principales

REP-GIT-001 se completó el 2026-07-28:

- `develop` avanzó de `19c6d19` a `61ab382` mediante fast-forward puro;
- `develop` no tenía commits exclusivos que rescatar;
- no se creó merge commit;
- no se usó `reset`, `rebase` ni force push;
- `master`, `origin/master`, `develop` y `origin/develop` quedaron en
  `61ab3828ee56c936dc4e657ee92bba30eb86e1c3`;
- la divergencia remota quedó en `0 0`.

La decisión operativa queda cerrada:

- la rama estable real se llama `master`, no `main`;
- `develop` es la rama de integración;
- las ramas nuevas salen de `develop`;
- los Pull Requests ordinarios apuntan a `develop`;
- `master` se reserva para el estado estable.

REP-GIT-002 ha definido la protección recomendada, pero continúa pendiente
inventariar y confirmar la configuración remota, aplicar los rulesets de forma
manual y disponer de un segundo revisor real.

REP-GIT-003-CI incorporó la infraestructura inicial de GitHub Actions:

- el commit funcional `e59c317` añadió el workflow informativo `quality-gate`,
  dos scripts auxiliares y la plantilla de Pull Request;
- el PR #5, desde `ci/quality-gate-informativo` hacia `develop`, se integró
  mediante el merge commit `74c3769`;
- `repository-and-php`, `frontend-build` y `quality-gate` finalizaron con
  estado `success`;
- la rama remota de trabajo se eliminó tras el merge;
- `develop` quedó limpio y sincronizado con `origin/develop`.

El workflow todavía no es obligatorio y necesita validarse en más Pull
Requests antes de incorporarlo a un ruleset. No existe `CODEOWNERS` y tampoco
se ha confirmado que las protecciones remotas estén activas. También continúa
pendiente el proceso formal de releases versionadas.

---

## 17. Conclusión

El repositorio debe tratarse como base técnica del producto, no como carpeta de pruebas.

La regla principal es:

```txt
master se protege, develop integra, las ramas pequeñas cambian una cosa cada vez.
```
