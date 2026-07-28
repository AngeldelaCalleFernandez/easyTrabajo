# EasyParte — Protección de ramas

## 1. Propósito

Este documento define las protecciones recomendadas para `master` y `develop`
en GitHub. Es una propuesta operativa de REP-GIT-002-DOC: no acredita que las
reglas estén activas ni modifica la configuración remota.

## 2. Diagnóstico de partida

- `master` es la rama estable y predeterminada.
- `develop` es la rama de integración.
- Las ramas ordinarias se crean desde `develop` y sus Pull Requests apuntan a
  `develop`.
- El PR #4 siguió este flujo:
  `testing/automatizar-avisos-multiempresa` → `develop`, mediante el merge
  commit `9175e68`.
- No existe `.github/`; actualmente no hay workflows, plantilla de Pull
  Request ni `CODEOWNERS`.
- El PR #4 no registró revisiones ni status checks.
- No se ha podido confirmar que exista una protección remota activa.

Por tanto, todas las reglas descritas a continuación quedan como
recomendaciones pendientes de aplicación y comprobación manual.

## 3. Reglas recomendadas para master

El ruleset de `master` debería:

- bloquear la eliminación de la rama;
- bloquear los force push;
- exigir que los cambios entren mediante Pull Request;
- exigir la resolución de las conversaciones antes del merge;
- permitir y mantener merge commits;
- no exigir historial lineal;
- reservar los Pull Requests hacia `master` a ramas `release/*`, `hotfix/*` o
  a la propia rama `develop`.

`master` no debe recibir Pull Requests ordinarios de ramas `feature/*`,
`fix/*`, `refactor/*`, `security/*`, `docs/*` o `testing/*`.

## 4. Reglas recomendadas para develop

El ruleset de `develop` debería:

- bloquear la eliminación de la rama;
- bloquear los force push;
- exigir que los cambios entren mediante Pull Request;
- exigir la resolución de las conversaciones antes del merge;
- permitir y mantener merge commits;
- no exigir historial lineal.

Los Pull Requests ordinarios hacia `develop` pueden proceder de:

```txt
feature/*
fix/*
refactor/*
security/*
docs/*
testing/*
```

## 5. Aprobaciones obligatorias

La exigencia de una o más aprobaciones queda pendiente hasta confirmar que el
proyecto dispone de un segundo revisor real y disponible.

Activarla antes de esa confirmación podría bloquear todos los Pull Requests o
convertir el bypass en el flujo habitual. Mientras tanto, la revisión humana
continúa siendo recomendable, pero no debe documentarse como requisito remoto
activo.

Cuando exista un segundo revisor, se deberá decidir:

- el número mínimo de aprobaciones;
- si se descartan aprobaciones al añadir commits;
- quién puede aprobar cambios sensibles;
- si administradores y mantenedores pueden omitir la regla.

## 6. Status checks y quality-gate

Los status checks obligatorios quedan pendientes hasta crear y estabilizar un
`quality-gate` reproducible.

No debe activarse como obligatorio un check inexistente, renombrado, inestable
o que no se ejecute para todos los Pull Requests afectados. Antes de exigirlo
se debe:

1. crear el workflow;
2. validar su ejecución repetida en Pull Requests reales;
3. fijar el nombre exacto del check;
4. comprobar su comportamiento en `develop` y en promociones a `master`;
5. definir cómo se resuelven fallos o indisponibilidad de la infraestructura.

## 7. Merge y borrado de ramas

Se mantienen los merge commits como método de integración. No se recomienda
activar la exigencia de historial lineal, porque sería incompatible con ese
criterio.

La opción **Automatically delete head branches** es recomendable para retirar
ramas ordinarias ya fusionadas y reducir el riesgo de reutilizar ramas
antiguas. Como esta opción es global, antes de activarla debe quedar cerrado el
procedimiento de `release/*` y `hotfix/*`.

Excepciones:

- una `release/*` debe haber resuelto la coherencia con `develop` antes de
  completar su Pull Request a `master`, o quedar protegida temporalmente;
- un `hotfix/*` debe tener definido su backport inmediato a `develop` desde el
  commit integrado en `master`, aunque GitHub elimine después la rama de origen;
- una rama no debe eliminarse si conserva trabajo no integrado o evidencia
  necesaria para una incidencia abierta.

## 8. Bypass lists

Las bypass lists deben ser mínimas, nominativas y revisadas. No deben incluir a
todos los colaboradores ni utilizarse para el trabajo ordinario.

Una excepción administrativa debe quedar justificada por una operación
concreta, tener alcance temporal y conservar la trazabilidad del cambio.

## 9. Checklist manual en GitHub

1. Abrir **Settings → Rules → Rulesets**.
2. Inventariar rulesets y reglas de protección existentes antes de crear o
   modificar nada.
3. Comprobar qué reglas afectan realmente a `master`.
4. Comprobar qué reglas afectan realmente a `develop`.
5. Revisar actores, equipos y aplicaciones incluidos en bypass lists.
6. Comparar la configuración observada con las secciones 3 y 4.
7. No exigir aprobaciones hasta confirmar un segundo revisor real.
8. No exigir status checks hasta disponer de un `quality-gate` estable; nunca
   seleccionar un check inexistente.
9. Mantener habilitados los merge commits y deshabilitada la exigencia de
   historial lineal.
10. Definir primero el tratamiento de `release/*` y `hotfix/*` y después activar
    **Automatically delete head branches** en la configuración general del
    repositorio.
11. Revisar las ramas remotas antiguas y confirmar que están fusionadas y no
    conservan trabajo antes de plantear su eliminación.
12. Guardar evidencia de la configuración final y probarla con un Pull Request
    controlado.

## 10. Ramas antiguas candidatas a revisión

Las siguientes ramas remotas son candidatas a limpieza, pero este documento no
autoriza ni ejecuta su eliminación:

```txt
origin/feature/avisos-reasignacion-auditada
origin/security/clientes-role-check
origin/security/tenant-minimo
origin/testing/avisos-reasignacion-multiempresa
```

Antes de eliminar cualquiera se debe verificar de nuevo su estado remoto, su
integración en `develop`, la ausencia de commits útiles exclusivos y la falta
de referencias operativas pendientes.

## 11. Orden seguro de implantación

1. Inventariar y documentar la configuración remota actual.
2. Aplicar primero las reglas que bloquean eliminación y force push y exigen
   Pull Request y resolución de conversaciones.
3. Confirmar que los merge commits siguen permitidos y que no se exige
   historial lineal.
4. Crear y estabilizar el `quality-gate`.
5. Confirmar la disponibilidad de un segundo revisor real.
6. Activar después, y por separado, checks y aprobaciones obligatorias.
7. Probar ambos rulesets con Pull Requests controlados.
8. Revisar el borrado automático y las ramas antiguas sin eliminar nada hasta
   completar sus comprobaciones.

## 12. Decisiones pendientes

- Confirmar la protección remota que existe actualmente.
- Confirmar un segundo revisor real antes de exigir aprobaciones.
- Crear y estabilizar el `quality-gate` antes de exigir status checks.
- Definir los bypass estrictamente necesarios.
- Revisar individualmente las ramas antiguas antes de eliminarlas.
