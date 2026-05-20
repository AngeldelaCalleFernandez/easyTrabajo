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
```

---

## 7. Norma principal de trabajo

No trabajar directamente sobre `main`.

Toda tarea debe hacerse en una rama específica.

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
main
develop
feature/*
fix/*
refactor/*
docs/*
security/*
release/*
hotfix/*
```

Resumen:

- `main`: versión estable.
- `develop`: integración de cambios.
- `feature/*`: nuevas funcionalidades.
- `fix/*`: correcciones.
- `refactor/*`: mejoras internas sin cambiar comportamiento.
- `docs/*`: documentación.
- `security/*`: cambios de seguridad.
- `release/*`: preparación de versión.
- `hotfix/*`: correcciones urgentes sobre producción.

---

## 9. Reglas para Codex/agentes

Cualquier agente que trabaje sobre el repositorio debe cumplir:

1. Leer documentación antes de tocar código.
2. No modificar `main` directamente.
3. Trabajar por tareas pequeñas.
4. Explicar qué archivos va a tocar.
5. No cambiar la arquitectura sin justificarlo.
6. No inventar reglas de negocio.
7. No eliminar código o columnas sin revisión humana.
8. No introducir secretos.
9. No exponer errores internos.
10. Documentar cambios relevantes.

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
10. Preparar release hacia main.
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

---

## 12. Reglas de protección recomendadas

Cuando el proyecto avance, se recomienda proteger `main`.

Reglas:

- no permitir push directo a `main`;
- exigir Pull Request;
- exigir revisión humana;
- exigir que el proyecto compile;
- exigir checklist mínimo;
- exigir backup si hay cambios de base de datos en producción.

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

## 16. Decisión pendiente

Pendiente decidir:

- si se creará rama `develop`;
- si se protegerá `main`;
- si se trabajará con Pull Requests siempre;
- si Codex trabajará directamente sobre ramas;
- si habrá releases versionadas.

---

## 17. Conclusión

El repositorio debe tratarse como base técnica del producto, no como carpeta de pruebas.

La regla principal es:

```txt
main se protege, develop integra, las ramas pequeñas cambian una cosa cada vez.
```
