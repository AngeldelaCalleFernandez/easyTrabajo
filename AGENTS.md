# AGENTS.md — EasyParte

## 1. Propósito

Este archivo define cómo debe trabajar Codex o cualquier agente de IA dentro del repositorio de EasyParte.

EasyParte es una aplicación web para empresas de servicios. Actualmente existe como proyecto funcional de DAW/TFC y se está profesionalizando hacia una aplicación SaaS multiempresa con mejor arquitectura, seguridad, trazabilidad, auditoría, suscripciones y preparación para despliegue.

Este archivo no sustituye a la documentación del proyecto. Su función es orientar al agente para saber qué debe leer, qué puede tocar y qué debe evitar.

---

## 2. Regla principal de trabajo

No leas toda la documentación en cada tarea.

Antes de actuar:

1. Identifica el tipo de tarea.
2. Lee solo los documentos relacionados.
3. Revisa el código afectado.
4. Propón o aplica cambios pequeños.
5. Explica archivos modificados, motivo, riesgos y pruebas.

Si una tarea afecta a seguridad, roles, base de datos, multiempresa, suscripción, auditoría o permisos, extrema la revisión y no hagas cambios grandes sin justificar el impacto.

---

## 3. Documentación del proyecto

La documentación base está en:

```txt
docs/
```

Estructura principal:

```txt
docs/agentes/
docs/arquitectura/
docs/checklists/
docs/contexto/
docs/despliegue/
docs/diagramas/
docs/repositorio/
docs/seguimiento/
docs/testing/
```

No muevas ni elimines documentación sin orden explícita.

---

## 4. Qué leer según la tarea

### Tareas generales u organización

Lee:

```txt
docs/contexto/objetivos.md
docs/contexto/alcance.md
docs/contexto/decisiones_tecnicas.md
docs/contexto/dudas_pendientes.md
docs/repositorio/repositorio.md
```

### Backend

Lee:

```txt
docs/agentes/agente_backend.md
docs/arquitectura/arquitectura.md
docs/contexto/reglas_de_negocio.md
docs/contexto/endpoints_api.md
docs/contexto/roles_y_permisos.md
```

Si afecta a seguridad, tenant, permisos o errores:

```txt
docs/despliegue/seguridad_produccion.md
docs/testing/validacion_configuracion_seguridad.md
```

### Frontend

Lee:

```txt
docs/agentes/agente_frontend.md
docs/arquitectura/estilo_visual.md
docs/arquitectura/arquitectura.md
docs/contexto/roles_y_permisos.md
```

Si afecta a API:

```txt
docs/contexto/endpoints_api.md
docs/testing/frontend_api_url.md
```

### Seguridad, roles, multiempresa o permisos

Lee:

```txt
docs/contexto/reglas_de_negocio.md
docs/contexto/roles_y_permisos.md
docs/contexto/modelo_saas.md
docs/arquitectura/arquitectura.md
docs/arquitectura/trazabilidad_y_auditoria.md
docs/checklists/checklist_auditoria.md
docs/checklists/checklist_pre_produccion.md
```

### Base de datos

Lee:

```txt
docs/contexto/entidades.md
docs/contexto/reglas_de_negocio.md
docs/contexto/modelo_saas.md
docs/arquitectura/trazabilidad_y_auditoria.md
bbdd/export_base_datos.sql
bbdd/seed_multiempresa_pruebas.sql
```

No modifiques estructura de base de datos sin explicar migración, impacto y datos existentes.

### Testing

Lee:

```txt
docs/testing/testing.md
docs/testing/casos_prueba.md
docs/testing/datos_prueba_multiempresa.md
docs/testing/tenant_minimo.md
docs/testing/validacion_configuracion_seguridad.md
docs/checklists/checklist_backend.md
docs/checklists/checklist_frontend.md
```

### Despliegue

Lee:

```txt
docs/despliegue/despliegue.md
docs/despliegue/entornos.md
docs/despliegue/variables_entorno.md
docs/despliegue/seguridad_produccion.md
docs/despliegue/backup_y_restauracion.md
```

### Repositorio, ramas y commits

Lee:

```txt
docs/repositorio/repositorio.md
docs/repositorio/estrategia_ramas.md
docs/repositorio/convenciones_commits.md
```

---

## 5. Reglas obligatorias

### Seguridad

- No expongas secretos.
- No inventes claves.
- No escribas tokens reales.
- No devuelvas errores SQL ni trazas internas al cliente.
- Usa mensajes de error genéricos para el usuario y logs internos para detalle técnico.
- No confíes en `id_empresa` enviado por frontend.
- Obtén empresa, usuario y rol desde token, sesión o contexto validado.
- Toda acción protegida debe validarse en backend.

### Multiempresa

- Toda consulta sensible debe filtrar por empresa.
- Un usuario no puede ver ni modificar datos de otra empresa.
- Los técnicos solo ven sus avisos, partes y horas, salvo permiso explícito.
- Administrador y atención al cliente pueden ver datos globales solo dentro de su empresa.

### Backend

- Mantén PHP vanilla orientado a objetos.
- Usa PDO y consultas preparadas.
- Mantén respuestas JSON homogéneas.
- Evita lógica de negocio grande dentro de controladores.
- Si refactorizas, separa progresivamente en servicios y repositorios.
- No rompas endpoints existentes sin justificarlo.

### Frontend

- Mantén Angular con standalone components.
- Usa servicios para comunicación HTTP.
- Usa guards e interceptors.
- El frontend puede ocultar opciones visualmente, pero la seguridad real va en backend.
- Mantén el estilo visual verde/lima, tarjetas blancas, modales limpios y dashboard útil.
- No hardcodees nuevas URLs si se está trabajando en configuración por entorno.

### Base de datos

- Prefiere borrado lógico en entidades de negocio.
- Mantén integridad referencial.
- No elimines columnas o tablas sin plan.
- No cambies datos demo sin explicar impacto.
- Si una FK impide borrar, analiza relaciones antes de forzar cambios.

### Documentación

- Si detectas una decisión no cerrada, no la inventes.
- Registra la duda en `docs/contexto/dudas_pendientes.md` o indícala en tu respuesta.
- Actualiza `docs/seguimiento/` solo si la tarea lo pide.
- No conviertas ideas futuras en implementación actual sin autorización.

---

## 6. Flujo recomendado para cada tarea

Antes de modificar:

```txt
1. Resumir objetivo de la tarea.
2. Identificar archivos implicados.
3. Leer documentación mínima necesaria.
4. Revisar el código relacionado.
5. Proponer plan pequeño.
```

Durante la modificación:

```txt
1. Cambiar el mínimo código necesario.
2. Mantener compatibilidad.
3. Evitar refactors grandes mezclados con bugs pequeños.
4. No tocar frontend y backend a la vez salvo que la tarea lo requiera.
```

Después de modificar:

```txt
1. Listar archivos tocados.
2. Explicar qué cambió.
3. Indicar riesgos.
4. Indicar pruebas realizadas o pendientes.
5. Proponer siguiente paso.
```

---

## 7. Agentes disponibles

Los agentes especializados están en:

```txt
docs/agentes/
```

Agentes principales:

```txt
docs/agentes/agente_orquestador.md
docs/agentes/agente_backend.md
docs/agentes/agente_frontend.md
docs/agentes/agente_auditor.md
docs/agentes/agente_testing.md
docs/agentes/agente_documentacion.md
docs/agentes/agente_despliegue.md
docs/agentes/agente_base_datos.md
```

Si una tarea encaja con un agente especializado, sigue sus reglas además de este archivo.

---

## 8. Límites

No hagas estas acciones sin orden explícita:

- Cambiar stack tecnológico.
- Migrar de PHP vanilla a Laravel.
- Cambiar Angular por otro framework.
- Rehacer toda la base de datos.
- Crear pasarela de pago real.
- Añadir funcionalidades fuera de alcance.
- Crear un superadmin global si sigue como pendiente.
- Subir `node_modules`, `dist`, `.angular/cache` o archivos generados.
- Modificar `.env` real o secretos.

---

## 9. Prioridad de instrucciones

Orden de prioridad:

1. Instrucción directa del usuario.
2. Este `AGENTS.md`.
3. `backend/AGENTS.md` o `frontend/AGENTS.md` si existen.
4. Agente especializado de `docs/agentes/`.
5. Documentación de `docs/`.
6. Código actual del proyecto.

Si hay conflicto, avisa antes de actuar.
